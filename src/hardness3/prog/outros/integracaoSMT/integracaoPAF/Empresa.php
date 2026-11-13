<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Empresa extends BaseIntegracao {
	public function integrar() {
    $param = array("empresa");
    $validate = $this->validateParams($param);

    if ($validate) {
      $empresa = $this->paramFromGet("empresa");

      $sql =  "SELECT c004.C004_Id                                               AS ID_INTEGRACAO,
                      c004.C004_Nome_Empresa                                     AS RAZAO_SOCIAL,
                      c004.C004_Nome_Abreviado                                   AS NOME_FANTASIA,
                      c004.C004_Cnpj                                             AS CNPJ,
                      c004.C004_Inscricao_Estadual                               AS IE,
                      CONCAT(c004.C004_DDD_Telefone, \" \", c004.C004_Telefone)  AS FONE,
                      c004.C004_Logradouro                                       AS ENDERECO,
                      c004.C004_Numero                                           AS NUMERO,
                      c004.C004_Complemento                                      AS COMPLEMENTO,
                      CAST(\"\" AS CHAR(60))                                     AS REFERENCIA,
                      c004.C004_Bairro                                           AS BAIRRO,
                      d020.D020_Nome_Cidade                                      AS CIDADE,
                      d018.D018_UF                                               AS UF,
                      CAST(d020.D020_Codigo_IBGE AS CHAR(7))                     AS CODIGO_IBGE,
                      c004.C004_Cep                                              AS CEP,
                      c004.C004_Certificado_Digital                              AS STR_CERTIFICADO,
                      c004.C004_Senha_Certificados                               AS PWD_CERTIFICADO,
                      c004.C004_Codigo_CSC                                       AS CSC,
                      c004.C004_Identificacao_CSC                                AS ID_CSC,
                      c004.C004_Codigo_Regime_Tributario                         AS CRT,
                      CAST(1 AS UNSIGNED)                                        AS STATUS
                 FROM c004, d020, d018
                WHERE c004.C004_Id         = '{$empresa}'
                  AND c004.C004_D020_Id    = d020.D020_Id
                  AND c004.C004_D018_Id    = d018.D018_Id
                  AND c004.C004_Flag_Ativo = 'S'";

      return $this->sql2Json($sql);
    }
	}
}
