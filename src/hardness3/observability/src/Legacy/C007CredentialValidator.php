<?php
declare(strict_types=1);
namespace Hardness\Observability\Legacy;

require_once dirname(__DIR__, 3) . '/bibliotecas/segurancaSenha.php';

final class C007CredentialValidator implements LegacyCredentialValidator
{
    public function authenticate(array $tenant, string $login, string $password): ?array
    {
        $db = $tenant['database'];
        if (!isset($db['host'], $db['database'], $db['username'], $db['password'])) { return null; }
        $connection = @new \mysqli($db['host'], $db['username'], $db['password'], $db['database'], isset($db['port']) ? (int) $db['port'] : 3306);
        if ($connection->connect_errno) { return null; }
        $statement = $connection->prepare('SELECT C007_Id, C007_Senha, C007_Ativo FROM C007 WHERE C007_Login = ? LIMIT 1');
        if (!$statement) { $connection->close(); return null; }
        $statement->bind_param('s', $login); $statement->execute(); $result = $statement->get_result(); $row = $result ? $result->fetch_assoc() : null; $statement->close(); $connection->close();
        if (!is_array($row) || $row['C007_Ativo'] !== 'S' || (int) $row['C007_Id'] === 63 || !\hardness\SegurancaSenha::validarSenhaArmazenada($password, (string) $row['C007_Senha'])) { return null; }
        return array('id' => (int) $row['C007_Id']);
    }
}
