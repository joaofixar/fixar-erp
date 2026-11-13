<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

/** HTTPS-only destination validation and DNS pinning input for cURL. */
class DestinationValidator
{
    public function validate(string $url): ValidatedDestination
    {
        if (strlen($url) === 0 || strlen($url) > 2048 || preg_match('/[\x00-\x1F\x7F\s]/', $url)) {
            throw new DestinationValidationException('Webhook destination URL is invalid.');
        }
        $parts = parse_url($url);
        if (!is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || !isset($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment'])) {
            throw new DestinationValidationException('Webhook destination must be an HTTPS URL without credentials or fragment.');
        }
        $host = strtolower((string) $parts['host']);
        $port = isset($parts['port']) ? (int) $parts['port'] : 443;
        if ($port !== 443 || $host === '' || (!$this->isIp($host) && !$this->isValidDnsName($host))) {
            throw new DestinationValidationException('Webhook destination host or port is not allowed.');
        }
        $addresses = $this->resolve($host);
        if ($addresses === array()) { throw new DestinationValidationException('Webhook destination DNS resolution returned no public address.'); }
        foreach ($addresses as $address) { if (!self::isPublicAddress($address)) { throw new DestinationValidationException('Webhook destination resolves to a prohibited address.'); } }
        return new ValidatedDestination($url, $host, $port, $addresses);
    }

    /** @return array<int,string> */
    protected function resolve(string $host): array
    {
        if ($this->isIp($host)) { return array($host); }
        $records = dns_get_record($host, DNS_A + DNS_AAAA);
        if (!is_array($records)) { return array(); }
        $addresses = array();
        foreach ($records as $record) {
            $address = isset($record['ip']) ? $record['ip'] : (isset($record['ipv6']) ? $record['ipv6'] : null);
            if (is_string($address) && $this->isIp($address)) { $addresses[$address] = $address; }
        }
        return array_values($addresses);
    }

    public static function isPublicAddress(string $address): bool
    {
        if (filter_var($address, FILTER_VALIDATE_IP) === false) { return false; }
        if (strpos($address, ':') === false) {
            foreach (array('0.0.0.0/8', '10.0.0.0/8', '100.64.0.0/10', '127.0.0.0/8', '169.254.0.0/16', '172.16.0.0/12', '192.0.0.0/24', '192.0.2.0/24', '192.88.99.0/24', '192.168.0.0/16', '198.18.0.0/15', '198.51.100.0/24', '203.0.113.0/24', '224.0.0.0/4', '240.0.0.0/4') as $network) { if (self::isInNetwork($address, $network)) { return false; } }
            return true;
        }
        foreach (array('::/128', '::1/128', '::ffff:0:0/96', '64:ff9b:1::/48', '100::/64', '2001:db8::/32', '2002::/16', 'fc00::/7', 'fe80::/10', 'ff00::/8') as $network) { if (self::isInNetwork($address, $network)) { return false; } }
        return true;
    }

    private static function isInNetwork(string $address, string $network): bool
    {
        list($base, $bits) = explode('/', $network, 2); $addressBytes = inet_pton($address); $baseBytes = inet_pton($base);
        if ($addressBytes === false || $baseBytes === false || strlen($addressBytes) !== strlen($baseBytes)) { return false; }
        $remaining = (int) $bits;
        for ($index = 0; $index < strlen($addressBytes); ++$index) { $mask = $remaining >= 8 ? 255 : ($remaining <= 0 ? 0 : (256 - (1 << (8 - $remaining)))); if ((ord($addressBytes[$index]) & $mask) !== (ord($baseBytes[$index]) & $mask)) { return false; } $remaining -= 8; }
        return true;
    }

    private function isIp(string $host): bool { return filter_var($host, FILTER_VALIDATE_IP) !== false; }
    private function isValidDnsName(string $host): bool
    {
        if (strlen($host) > 253 || strpos($host, '.') === false || !preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)+$/', $host)) { return false; }
        return true;
    }
}
