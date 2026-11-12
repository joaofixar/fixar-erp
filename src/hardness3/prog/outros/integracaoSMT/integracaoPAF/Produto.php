<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Produto extends BaseIntegracao {
  public function integrar() {
    $param = array("empresa");
    $validate = $this->validateParams($param);

    if ($validate) {
      $empresa = $this->paramFromGet("empresa");

      $sql = "SELECT d001.D001_Id                                                             AS ID_INTEGRACAO,
                     d001.D001_Codigo_Produto                                                 AS CODIGO,
                     d001.D001_Descricao_Produto                                              AS DESCRICAO,
                     d005.D005_Classificacao_Fiscal                                           AS NCM,
                     d149.D149_Situacao_Tributaria_PIS                                        AS CST_PIS,
                     d149.D149_Situacao_Tributaria_COFINS                                     AS CST_COFINS,
                     d059.D059_Codigo_ST                                                      AS CST_ICMS,

                     CASE d006.D006_Flag_Isento_IPI
                       WHEN \"N\" THEN (SELECT d088.D088_Codigo_ST
                                          FROM d088
                                         WHERE d088.D088_Id = d006.D006_D088_Id_Sem_Aliquota)
                       ELSE (SELECT d088.D088_Codigo_ST
                                        FROM d088
                                       WHERE d088.D088_Id = d006.D006_D088_Id)
                     END                                                                      AS CST_IPI,

                     d149.D149_Aliquota_PIS                                                   AS P_PIS,
                     d149.D149_Aliquota_COFINS                                                AS P_COFINS,
                     d001a.D001A_PAF_P_ICMS                                                   AS P_ICMS,

                     CASE d006.D006_Flag_Isento_IPI
                       WHEN \"N\" THEN d005.D005_Aliquota_IPI
                       ELSE 0
                     END                                                                      AS P_IPI,

                     d006.D006_Codigo_CFOP                                                    AS CFOP,
                     d149.D149_Codigo_CEST                                                    AS CEST,
                     CSOSN.D059_Codigo_ST                                                     AS CSOSN,
                     IF(COALESCE(d149.D149_Flag_ST, 'N') = 'S', 'true', 'false')              AS ST,
                     d037.D037_Unidade                                                        AS UM,
                     d009.D009_Preco_Loja                                                     AS PRECO_VENDA,
                     CAST(1 AS UNSIGNED)                                                      AS STATUS

                FROM d001, d001a, d005, d009, d037, d149, d059, d059 CSOSN, d006

               WHERE d001a.D001A_D001_Id              = d001.D001_Id
                 AND d001.D001_D005_Id                = d005.D005_Id
                 AND d149.D149_D005_Id                = d005.D005_Id
                 AND d009.D009_D001_Id                = d001.D001_Id
                 AND d001.D001_D037_Id                = d037.D037_Id
                 AND d001a.D001A_D059_Id_PAF_CST_ICMS = d059.D059_Id
                 AND d001a.D001A_D006_Id_PAF_CFOP     = d006.D006_Id
                 AND d006.D006_D059_Id                = CSOSN.D059_Id
                 AND d009.D009_C004_Id                = '{$empresa}'
                 AND d149.D149_C004_Id                = '{$empresa}'
                 AND d001.D001_Flag_Ativo             = 'S'";

      return $this->sql2Json($sql);
    }
  }
}
