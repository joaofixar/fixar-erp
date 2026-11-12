<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class FormaPagamento extends BaseIntegracao {
	public function integrar() {
    $sql =  "SELECT d027.D027_Id                        AS ID_INTEGRACAO,
                    d027.D027_Portador                  AS DESCRICAO,
                    d027.D027_Tipo_Forma_Pagamento_NFCE AS CODIGO_SEFAZ,
                    CAST(1 AS UNSIGNED)                 AS STATUS
               FROM d027
              WHERE d027.D027_Flag_Cupom = 'S'";

    return $this->sql2Json($sql);
	}
}