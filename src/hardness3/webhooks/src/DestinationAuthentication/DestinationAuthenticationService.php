<?php

declare(strict_types=1);

namespace HardnessWebhooks\DestinationAuthentication;

use HardnessWebhooks\Exception\DestinationAuthenticationUnavailableException;
use HardnessWebhooks\Security\MasterKeyProvider;
use HardnessWebhooks\Security\SecretCipher;

/** Keeps receiver credentials separate from the versioned WHK007 HMAC secret. */
final class DestinationAuthenticationService
{
    public const TYPE_NONE = 'N';
    public const TYPE_BEARER = 'B';
    public const MAX_TOKEN_LENGTH = 4096;

    /** @var SecretCipher|null */
    private $cipher;

    public function __construct(?SecretCipher $cipher = null)
    {
        $this->cipher = $cipher;
    }

    /** @return array{type:string,ciphertext:?string} */
    public function forCreate($type, $token): array
    {
        return $this->forSave($type, $token, null, null);
    }

    /** @return array{type:string,ciphertext:?string} */
    public function forUpdate($type, $token, ?string $currentType, ?string $currentCiphertext): array
    {
        return $this->forSave($type, $token, $currentType, $currentCiphertext);
    }

    public function assertConfigured(string $type, ?string $ciphertext): void
    {
        if ($type === self::TYPE_NONE && $ciphertext === null) {
            return;
        }
        if ($type !== self::TYPE_BEARER || !is_string($ciphertext) || $ciphertext === '') {
            throw new DestinationAuthenticationUnavailableException('Webhook destination authentication is unavailable.');
        }
        try { $this->validateToken($this->cipher()->decrypt($ciphertext)); } catch (\Throwable $exception) { throw new DestinationAuthenticationUnavailableException('Webhook destination authentication is unavailable.'); }
    }

    /** @return array{type:string,token:?string} */
    public function loadForDelivery(\mysqli $connection, int $deliveryId): array
    {
        $sql = 'SELECT e.WHK001_Destination_Auth_Type,e.WHK001_Destination_Auth_Criptografada FROM WHK004 d INNER JOIN WHK001 e ON e.WHK001_Id=d.WHK004_WHK001_Id AND e.WHK001_C004_Id=d.WHK004_C004_Id WHERE d.WHK004_Id=? LIMIT 1';
        $statement = $connection->prepare($sql);
        if ($statement === false) { throw new \RuntimeException('Could not prepare destination authentication lookup.'); }
        try {
            $statement->bind_param('i', $deliveryId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute destination authentication lookup.'); }
            $statement->bind_result($type, $ciphertext);
            if (!$statement->fetch()) { throw new DestinationAuthenticationUnavailableException('Webhook destination authentication is unavailable.'); }
            if ($type === self::TYPE_NONE && $ciphertext === null) { return array('type' => self::TYPE_NONE, 'token' => null); }
            if ($type !== self::TYPE_BEARER || !is_string($ciphertext) || $ciphertext === '') { throw new DestinationAuthenticationUnavailableException('Webhook destination authentication is unavailable.'); }
            try { $token = $this->cipher()->decrypt($ciphertext); $this->validateToken($token); } catch (\Throwable $exception) { throw new DestinationAuthenticationUnavailableException('Webhook destination authentication is unavailable.'); }
            return array('type' => self::TYPE_BEARER, 'token' => $token);
        } finally { $statement->close(); }
    }

    /** @return array{type:string,ciphertext:?string} */
    private function forSave($type, $token, ?string $currentType, ?string $currentCiphertext): array
    {
        $type = is_string($type) ? $type : '';
        $token = is_string($token) ? $token : '';
        if ($type === self::TYPE_NONE) { return array('type' => self::TYPE_NONE, 'ciphertext' => null); }
        if ($type !== self::TYPE_BEARER) { throw new \InvalidArgumentException('Autenticação do destino é inválida.'); }
        if ($token === '') {
            if ($currentType === self::TYPE_BEARER && is_string($currentCiphertext) && $currentCiphertext !== '') {
                return array('type' => self::TYPE_BEARER, 'ciphertext' => $currentCiphertext);
            }
            throw new \InvalidArgumentException('Token Bearer é obrigatório.');
        }
        $this->validateToken($token);
        return array('type' => self::TYPE_BEARER, 'ciphertext' => $this->cipher()->encrypt($token));
    }

    private function validateToken(string $token): void
    {
        if ($token === '' || strlen($token) > self::MAX_TOKEN_LENGTH || preg_match('/[\x00-\x1F\x7F]/', $token)) {
            throw new \InvalidArgumentException('Token Bearer é inválido.');
        }
    }
    private function cipher(): SecretCipher
    {
        if (!$this->cipher instanceof SecretCipher) { $this->cipher = new SecretCipher(MasterKeyProvider::fromEnvironment()); }
        return $this->cipher;
    }
}
