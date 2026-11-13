<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Usuario extends BaseIntegracao {
	public function integrar() {
    $sql =  "SELECT c007.C007_Id         AS ID_INTEGRACAO,
                    c007.C007_Login      AS LOGIN,
                    c007.C007_Senha      AS SENHA,
                    c007.C007_Nome       AS NOME,
                    c007.C007_Email      AS EMAIL,
                    CAST(1 AS UNSIGNED)  AS STATUS
               FROM c007
              WHERE c007.C007_Ativo = 'S'";

    return $this->sql2Json($sql);
	}
}
