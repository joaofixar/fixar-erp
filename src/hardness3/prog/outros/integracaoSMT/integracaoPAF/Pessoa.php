<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Pessoa extends BaseIntegracao {
	public function integrar() {
    $sql =  "SELECT d024.D024_Id                                                   AS ID_INTEGRACAO,
                    d024.D024_Nome_Empresa                                         AS NOME_RAZAO,
                    CASE d024.D024_Flag_Pessoa_Fisica_Juridica
                      WHEN \"F\" THEN d024.D024_Cpf
                      WHEN \"J\" THEN d024.D024_Cnpj
                    END                                                            AS CPF_CNPJ,
                    CASE d024.D024_Flag_Pessoa_Fisica_Juridica
                      WHEN \"F\" THEN d024.D024_Rg
                      WHEN \"J\" THEN d024.D024_Inscricao_Estadual
                    END                                                            AS RG_IE,
                    CONCAT(d024.D024_DDD_Telefone_1, \" \", d024.D024_Telefone_1)  AS FONE,
                    d024.D024_Endereco                                             AS ENDERECO,
                    d024.D024_Numero                                               AS NUMERO,
                    d024.D024_Complemento                                          AS COMPLEMENTO,
                    CAST(\"\" AS CHAR(60))                                         AS REFERENCIA,
                    d024.D024_Bairro                                               AS BAIRRO,
                    d020.D020_Nome_Cidade                                          AS CIDADE,
                    d018.D018_UF                                                   AS ESTADO,
                    CAST(d020.D020_Codigo_IBGE AS CHAR(7))                         AS CODIGO_IBGE,
                    d024.D024_Cep                                                  AS CEP,
                    CAST(1 AS UNSIGNED)                                            AS STATUS
               FROM d024, d020, d018
              WHERE d024.D024_D020_Id    = d020.D020_Id
                AND d024.D024_D018_Id    = d018.D018_Id
                AND d024.D024_Flag_Ativo = 'S'";

    return $this->sql2Json($sql);
	}
}
