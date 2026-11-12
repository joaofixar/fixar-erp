<?php
declare(strict_types=1);

namespace Hardness\Observability\Context;

final class ObservationContext
{
    private $role;
    private $scopeLabel;
    private $capabilities;
    private $tenantLabels;

    public function __construct(string $role, string $scopeLabel, array $capabilities, array $tenantLabels)
    {
        $this->role = $role;
        $this->scopeLabel = $scopeLabel;
        $this->capabilities = $capabilities;
        $this->tenantLabels = $tenantLabels;
    }

    public function toArray(): array
    {
        return array(
            'role' => $this->role,
            'scopeLabel' => $this->scopeLabel,
            'capabilities' => $this->capabilities,
            'tenantLabels' => $this->tenantLabels,
        );
    }
}
