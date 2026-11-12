<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Deposito extends BaseIntegracao {
	public function integrar() {
    $sql =  "SELECT d146.D146_Id         AS ID_INTEGRACAO,
                    d146.D146_Descricao  AS DESCRICAO,
                    CAST(1 AS UNSIGNED)  AS STATUS
               FROM d146";

    return $this->sql2Json($sql);
	}
}
