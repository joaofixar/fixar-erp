<?php
declare(strict_types=1);
namespace Hardness\Observability\Auth;

final class ClientAccessPolicy
{
    private const ALLOWED = array('view', 'full_sql', 'connection_identity');
    private $enabled;
    private $users;
    private $tenantUsers;
    private function __construct(bool $enabled, array $users, array $tenantUsers) { $this->enabled = $enabled; $this->users = $users; $this->tenantUsers = $tenantUsers; }
    public static function fromJson(string $json): self
    {
        if (preg_match('//u', $json) !== 1) { throw new \InvalidArgumentException('utf8'); }
        $data = json_decode($json);
        if (!is_object($data) || json_last_error() !== JSON_ERROR_NONE || count(get_object_vars($data)) !== 2 || !property_exists($data, 'enabled') || (!property_exists($data, 'allowed_users') && !property_exists($data, 'tenants'))) { throw new \InvalidArgumentException('schema'); }
        if (!is_bool($data->enabled)) { throw new \InvalidArgumentException('schema'); }
        if (property_exists($data, 'allowed_users')) {
            if (!is_object($data->allowed_users)) { throw new \InvalidArgumentException('schema'); }
            return new self($data->enabled, self::parseUsers($data->allowed_users), array());
        }
        if (!is_object($data->tenants)) { throw new \InvalidArgumentException('schema'); }
        $tenantUsers = array();
        foreach (get_object_vars($data->tenants) as $tenantKey => $tenant) {
            if (!is_string($tenantKey) || preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/D', $tenantKey) !== 1 || !is_object($tenant) || count(get_object_vars($tenant)) !== 1 || !property_exists($tenant, 'allowed_users') || !is_object($tenant->allowed_users)) { throw new \InvalidArgumentException('tenant'); }
            $tenantUsers[$tenantKey] = self::parseUsers($tenant->allowed_users);
        }
        return new self($data->enabled, array(), $tenantUsers);
    }

    private static function parseUsers(object $allowedUsers): array
    {
        $users = array();
        foreach (get_object_vars($allowedUsers) as $id => $permissions) {
            $id = (string) $id;
            if (preg_match('/^[1-9][0-9]*$/D', $id) !== 1 || (int) $id === 63) { throw new \InvalidArgumentException('id'); }
            if (!is_array($permissions) || $permissions === array()) { throw new \InvalidArgumentException('permissions'); }
            $seen = array();
            foreach ($permissions as $permission) {
                if (!is_string($permission) || $permission === '' || !in_array($permission, self::ALLOWED, true) || isset($seen[$permission])) { throw new \InvalidArgumentException('permissions'); }
                $seen[$permission] = true;
            }
            if (!isset($seen['view'])) { throw new \InvalidArgumentException('view'); }
            $users[(int) $id] = $seen;
        }
        return $users;
    }
    public function capabilitiesFor(string $tenantKey, int $id, bool $allowLegacyGlobal = false): ?array
    {
        if (!$this->enabled) { return null; }
        if ($this->tenantUsers !== array()) {
            if (!isset($this->tenantUsers[$tenantKey][$id])) { return null; }
            $permissions = $this->tenantUsers[$tenantKey][$id];
        } else {
            if (!$allowLegacyGlobal || !isset($this->users[$id])) { return null; }
            $permissions = $this->users[$id];
        }
        return array('view' => true, 'full_sql' => isset($permissions['full_sql']), 'connection_identity' => isset($permissions['connection_identity']));
    }
}
