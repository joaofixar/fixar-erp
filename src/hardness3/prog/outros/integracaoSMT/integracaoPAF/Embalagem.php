<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Embalagem extends BaseIntegracao {
  public function integrar() {
      $param = array("empresa");
      $validate = $this->validateParams($param);

      if ($validate) {
        $empresa = $this->paramFromGet("empresa");

        $sql =  "SELECT d001.D001_Id                       AS ID_INTEGRACAO,
                        d001.D001_Id                       AS ID_INTEGRACAO_PRODUTO,
                        d001.D001_Codigo_Produto           AS CODIGO,
                        d001.D001_Codigo_Barras_Tributavel AS GTIN,
                        CAST(1 AS DECIMAL)                 AS MULTIPLICADOR,
                        CAST(1 AS UNSIGNED)                AS STATUS
                   FROM d001, d001a, d005, d009, d037, d149, d059, d006

                  WHERE d001a.D001A_D001_Id                 = d001.D001_Id
                    AND d001.D001_D005_Id                   = d005.D005_Id
                    AND d149.D149_D005_Id                   = d005.D005_Id
                    AND d009.D009_D001_Id                   = d001.D001_Id
                    AND d001.D001_D037_Id                   = d037.D037_Id
                    AND d001a.D001A_D059_Id_PAF_CST_ICMS    = d059.D059_Id
                    AND d001a.D001A_D006_Id_PAF_CFOP        = d006.D006_Id
                    AND d009.D009_C004_Id                   = '{$empresa}'
                    AND d149.D149_C004_Id                   = '{$empresa}'
                    AND d001.D001_Flag_Ativo                = 'S'";

      return $this->sql2Json($sql);
    }
	}
}