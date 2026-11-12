<?php
namespace hardness;

/** Consulta as permissões necessárias para acessar o painel de integrações. */
class iset_permissoes_
{
    public function usuarioTemPermissao($permissaoId)
    {
        global $g;

        $permissaoId = (int) $permissaoId;
        $perfilUsuario = isset($g['perfilUsuario']) ? (int) $g['perfilUsuario'] : (isset($_SESSION['Perfil_Usuario']) ? (int) $_SESSION['Perfil_Usuario'] : 0);
        if ($permissaoId <= 0 || $perfilUsuario <= 0) {
            return false;
        }

        if (isset($g['c029Ids'])) {
            return !empty($g['c029Ids'][$permissaoId]);
        }

        $sql = <<<SQL
            SELECT C030_Id
              FROM C030
             WHERE C030_C021_Id = '{$perfilUsuario}'
               AND C030_C029_Id = '{$permissaoId}'
               AND C030_Acesso_Liberado = 'S'
             LIMIT 1
SQL;
        $consulta = mysql_query($sql);

        return $consulta && mysql_num_rows($consulta) > 0;
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_PERMISSOES');
}

if (!class_exists(__NAMESPACE__ . '\\iset_permissoes', false)) {
    class_alias(__NAMESPACE__ . '\\iset_permissoes_', __NAMESPACE__ . '\\iset_permissoes');
}
