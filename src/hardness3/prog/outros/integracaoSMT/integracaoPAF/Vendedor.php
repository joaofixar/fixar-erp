<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Vendedor extends BaseIntegracao {
	public function integrar() {
    $sql =  "SELECT c007.C007_Id         AS ID_INTEGRACAO,
                    c007.C007_Nome       AS DESCRICAO,
                    CAST(1 AS UNSIGNED)  AS STATUS
               FROM c007
               WHERE C007_Flag_Vendedor = 'V'
                 AND C007_Ativo         = 'S'";

    return $this->sql2Json($sql);
	}
}
