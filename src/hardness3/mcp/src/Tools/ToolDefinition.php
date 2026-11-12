<?php

declare(strict_types=1);

namespace HardnessMcp\Tools;

final class ToolDefinition
{
    private string $name;
    private string $description;
    /** @var array<string, mixed> */
    private array $inputSchema;
    /** @var string[] */
    private array $requiredScopes;
    private bool $requiresErpActor;
    private ?\HardnessMcp\Tools\Validation\ToolArgumentsValidator $argumentsValidator;

    /** @param array<string, mixed> $inputSchema @param string[] $requiredScopes */
    public function __construct(string $name, string $description, array $inputSchema, array $requiredScopes, bool $requiresErpActor = false, ?\HardnessMcp\Tools\Validation\ToolArgumentsValidator $argumentsValidator = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->inputSchema = $inputSchema;
        $this->requiredScopes = array_values($requiredScopes);
        $this->requiresErpActor = $requiresErpActor;
        $this->argumentsValidator = $argumentsValidator;
    }

    public function getArgumentsValidator(): ?\HardnessMcp\Tools\Validation\ToolArgumentsValidator { return $this->argumentsValidator; }

    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    /** @return array<string, mixed> */
    public function getInputSchema(): array { return $this->inputSchema; }
    /** @return string[] */
    public function getRequiredScopes(): array { return $this->requiredScopes; }
    public function requiresErpActor(): bool { return $this->requiresErpActor; }
}
