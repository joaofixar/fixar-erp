<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Scopes\ScopeCatalog;
use HardnessMcp\Tools\ToolRegistry;
use RuntimeException;

/** Verifies the published Tool and administrative scope catalogues stay aligned. */
final class ToolCatalogConsistencyValidator
{
    public function assertConsistent(ToolRegistry $registry, ScopeCatalog $scopeCatalog): void
    {
        /** @var array<string, array<string, true>> $requiredScopes */
        $requiredScopes = [];
        /** @var array<string, true> $toolNames */
        $toolNames = [];

        foreach ($registry->all() as $tool) {
            $definition = $tool->getDefinition();
            $name = $definition->getName();
            if (!preg_match('/\Ahardness\.[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)?\.[a-z][a-z0-9_]*\z/', $name)) {
                throw new RuntimeException('Invalid MCP tool name.');
            }
            if (isset($toolNames[$name])) {
                throw new RuntimeException('Duplicate MCP tool registration.');
            }
            $toolNames[$name] = true;
            foreach ($definition->getRequiredScopes() as $scope) {
                if (!preg_match('/\A[a-z][a-z0-9_]*(?::[a-z][a-z0-9_-]*){1,3}\z/', $scope)) {
                    throw new RuntimeException('Invalid MCP scope name.');
                }
                if (!isset($requiredScopes[$scope])) {
                    $requiredScopes[$scope] = [];
                }
                $requiredScopes[$scope][$name] = true;
            }
        }

        /** @var array<string, array<string, true>> $activeScopes */
        $activeScopes = [];
        foreach ($scopeCatalog->all() as $scope) {
            $id = $scope['id'];
            if (!preg_match('/\A[a-z][a-z0-9_]*(?::[a-z][a-z0-9_-]*){1,3}\z/', $id)) {
                throw new RuntimeException('Invalid MCP scope name.');
            }
            foreach ($scope['tools'] as $toolName) {
                if (!isset($toolNames[$toolName])) {
                    throw new RuntimeException('MCP scope references an unregistered tool.');
                }
            }
            if (($scope['active'] ?? false) === true) {
                $activeScopes[$id] = [];
                foreach ($scope['tools'] as $toolName) {
                    $activeScopes[$id][$toolName] = true;
                }
                if (($scope['tools'] ?? []) === []) {
                    throw new RuntimeException('Active MCP scope has no registered tool.');
                }
            }
        }

        foreach ($requiredScopes as $scope => $tools) {
            if (!isset($activeScopes[$scope])) {
                throw new RuntimeException('Tool requires an inactive or unknown MCP scope.');
            }
            if ($activeScopes[$scope] !== $tools) {
                throw new RuntimeException('MCP scope tool relationship is inconsistent.');
            }
        }
        foreach ($activeScopes as $scope => $_) {
            if (!isset($requiredScopes[$scope])) {
                throw new RuntimeException('Active MCP scope is not required by a registered tool.');
            }
        }
    }
}
