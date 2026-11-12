<?php
namespace hardness;

require_once('bibliotecas/classes/MYTCPDF.PHP');
require_once "old/lib/funcoes.php";

definirClasse('validadorNFe');

class validadorNFe_
{

   /**
     * validador
     *
     * @param String $T003_Id
     * @param String $T005_Id
     * @param String $T007_Id
     * @return String/Bool
     */

    public function validadorNFe($T003_Id,$T005_Id,$T007_Id) {

        // Manual de Orientação do Contribuinte - Padrões Técnicos de Comunicação.
        // Versão 6.0
        // Data Setembro 2015

        $c = array();

        if ($T003_Id>0){
            $cabecalho = mysql_query("SELECT D024Cidade.D020_Nome_Cidade AS cidadeCliente,
                                        D024Estado.D018_Codigo_IBGE AS codigoEstadoCliente,
                                        D024Estado.D018_UF AS UFCliente,
                                        D024Cidade.D020_Codigo_IBGE AS codCidadeCliente,
                                        C004Cidade.D020_Nome_Cidade AS cidadeEmpresaUsuaria,
                                        C004Estado.D018_Codigo_IBGE AS empresaCodigoUF,
                                        C004Cidade.D020_Codigo_IBGE AS codMunicipioEmpresa,
                                        C004Estado.D018_UF AS UFEmpresaUsuaria,
                                        D024_Bairro AS bairroCliente,
                                        D024_Endereco AS enderecoCliente,
                                        D024_Numero AS numeroEnderecoCliente,
                                        D024_Cep AS CEPCliente, 
                                        T003_Flag_Acp AS modeloACP,
                                        D024_Flag_Contribuinte AS flagContribuinte,
                                        C004_Cnpj AS CNPJEmpresaUsuaria,
                                        C004_Nome_Empresa as nomeEmpresaUsuaria,
                                        C004_Logradouro AS enderecoEmpresaUsuaria,
                                        C004_Numero AS numeroEmpresaUsuaria, 
                                        C004_Bairro AS bairroEmpresaUsuaria,
                                        C004_cep AS CEPEmpresaUsuaria,
                                        C004_Inscricao_Estadual AS IEEmpresaUsuaria,
                                        C004_Inscricao_Municipal AS IMEmpresaUsuaria,
                                        C004_Codigo_Regime_Tributario AS CRTEmpresaUsuaria,
                                        D024_Flag_Pessoa_Fisica_Juridica,
                                        D024_Cpf ,
                                        D024_Cnpj,
                                        D024_Nome_Empresa AS nomeCliente,
                                        T003_Valor_Total_Base_ICMS AS valorTotalBCICMS,
                                        T003_Valor_Total_ICMS AS valorTotalICMS,
                                        T003_Valor_Total_ICMS_Desonerado AS totalICMSDesonerado,
                                        T003_Valor_Total_Produtos AS totalProdutos,
                                        T003_Valor_Frete AS valorFrete,
                                        T003_Valor_Desconto AS valorDesconto,
                                        T003_Valor_Total_IPI AS totalIPI,
                                        T003_Valor_Total_PIS AS totalPIS, 
                                        T003_Valor_Total_COFINS AS totalCofins,
                                        T003_Valor_Despesas_Acessorias AS despesasAcessorias, 
                                        T003_Valor_Total AS valorTotal,
                                        T003_Flag_Frete AS freteTotal

	                                 FROM T003
	                                 LEFT JOIN C004 ON C004_Id=T003_C004_Id
	                                 LEFT JOIN D024 ON D024_Id=T003_D024_Id
	                                 LEFT JOIN D020 AS C004Cidade ON C004Cidade.D020_Id=C004_D020_Id 
	                                 LEFT JOIN D018 AS C004Estado ON C004Estado.D018_Id=C004_D018_Id 
	                                 LEFT JOIN D020 AS D024Cidade ON D024Cidade.D020_Id=D024_D020_Id 
	                                 LEFT JOIN D018 AS D024Estado ON D024Estado.D018_Id=D024_D018_Id
                                  
                                 WHERE T003_Id='{$T003_Id}'");
            $mCabecalho = mysql_fetch_array($cabecalho);

            $produto = "SELECT T004_Codigo_Produto AS codigoProduto,
                                 T004_Descricao_Produto AS descricaoProduto,
                                 T004_Classificacao_Fiscal AS NCM,
                                 D006_Codigo_CFOP AS CFOP,
                                 D037_Unidade AS unidade,
                                 T004_Quantidade AS quantidade,
                                 T004_Valor_Preco_Sem_Desconto_Unitario AS valorUnitarioComercializacao,
                                 T004_Valor_Total_Preco_Sem_Desconto AS valorTotalBruto, 
                                 T004_Numero_Pedido_Compra AS numeroPedidoCompra,
                                 T004_Valor_Trib_Transparencia AS valorAproximadoTributos,
                                 T004_Flag_Situacao_Tributaria AS situacaoTributaria,
                                 T004_Modalidade_ICMS AS modalidadeICMS,
                                 T004_Aliquota_ICMS AS aliquotaICMS,
                                 T004_Valor_ICMS AS valorICMS,
                                 D059_Codigo_ST AS codigoST,
                                 T004_Modalidade_ICMS_Substituicao AS modalidadeBCICMS,
                                 T004_Valor_Base_ICMS AS valorBaseICMS,
                                 T004_Valor_ICMS_Substituicao_Retido AS valorICMSSTRetido,
                                 T004_Percentual_Reducao_ICMS AS percentualReducaoICMS,
                                 T004_Valor_ICMS_Desonerado AS valorICMSDesonerado,
                                 T004_Motivo_Desoneracao_ICMS AS motivoDesoneracaoICMS,
                                 T004_Item_Pedido_Compra AS itemPedidoCompra

                          FROM T004 
                          LEFT JOIN D006 ON D006_Id=T004_D006_Id 
                          LEFT JOIN D037 ON D037_Id=T006_D037_Id
                          LEFT JOIN D059 ON D059_Id=T004_D059_Id
                          WHERE T004_T003_Id = '{$T003_Id}'";

            // Seleciona a natureza da operação de todos os produtos e converte o array gerado em uma única string.
            $descricao_CFOP  = " select ";
            $descricao_CFOP .= " D006_Flag_Estado, ";
            $descricao_CFOP .= " D006_Descricao ";
            $descricao_CFOP .= " from T004 ";
            $descricao_CFOP .= " left join D006 on D006_Id=T004_D006_Id ";
            $descricao_CFOP .= " where T004_D006_Id='{$T004_Id}'";
            $descricao_CFOP .= " group by T004_D006_Id ";
            $cSql_T006 = mysql_query($descricao_CFOP);
            echo mysql_error();
            
            unset($mDescricao_CFOP);
            while ($mT006 = mysql_fetch_array($cSql_T006))
            {
                   $mDescricao_CFOP[] = $mT006[D006_Descricao];
                   $cD006_Flag_Estado = $mT006[D006_Flag_Estado];
            }
            $cDescricao_CFOP=implode(" - ",$mDescricao_CFOP);
            $cDescricao_CFOP=substr($cDescricao_CFOP,0,60);

        } else if ($T005_Id>0){

            $cabecalho = mysql_query("SELECT D024Cidade.D020_Nome_Cidade AS cidadeCliente,
                                        D024Estado.D018_Codigo_IBGE AS codigoEstadoCliente,
                                        D024Estado.D018_UF AS UFCliente,
                                        D024Cidade.D020_Codigo_IBGE AS codCidadeCliente,
                                        C004Estado.D018_Codigo_IBGE AS empresaCodigoUF,
                                        C004Cidade.D020_Nome_Cidade AS cidadeEmpresaUsuaria,
                                        C004Cidade.D020_Codigo_IBGE AS codMunicipioEmpresa,
                                        C004Estado.D018_UF AS UFEmpresaUsuaria,
                                        D024_Bairro AS bairroCliente,
                                        D024_Endereco AS enderecoCliente,
                                        D024_Numero AS numeroEnderecoCliente,
                                        D024_Cep AS CEPCliente,
                                        T005_Flag_ACP AS modeloACP,
                                        D024_Flag_Contribuinte AS flagContribuinte,
                                        C004_Cnpj AS CNPJEmpresaUsuaria,
                                        C004_Nome_Empresa AS nomeEmpresaUsuaria,
                                        C004_Logradouro AS enderecoEmpresaUsuaria,
                                        C004_Numero AS numeroEmpresaUsuaria,
                                        C004_Bairro AS bairroEmpresaUsuaria,
                                        C004_cep AS CEPEmpresaUsuaria,
                                        C004_Inscricao_Estadual AS IEEmpresaUsuaria,
                                        C004_Inscricao_Municipal AS IMEmpresaUsuaria,
                                        C004_Codigo_Regime_Tributario AS CRTEmpresaUsuaria,
                                        D024_Flag_Pessoa_Fisica_Juridica,
                                        D024_Cpf,
                                        D024_Cnpj,
                                        D024_Nome_Empresa AS nomeCliente,
                                        T005_Valor_Total_ICMS_Desonerado AS totalICMSDesonerado,
                                        T005_Valor_Total_Produtos AS totalProdutos, 
                                        T005_Valor_Frete AS valorFrete, 
                                        T005_Valor_Desconto AS valorDesconto,
                                        T005_Valor_Total_IPI AS totalIPI, 
                                        T005_Valor_Total_PIS AS totalPIS, 
                                        T005_Valor_Total_COFINS AS totalCofins, 
                                        T005_Valor_Despesas_Acessorias AS despesasAcessorias, 
                                        T005_Valor_Total AS valorTotal,
                                        T005_Flag_Frete AS freteTotal

	                                 FROM T005
	                                 LEFT JOIN C004 ON C004_Id=T005_C004_Id
	                                 LEFT JOIN D024 ON D024_Id=T005_D024_Id
	                                 LEFT JOIN D020 as C004Cidade ON C004Cidade.D020_Id=C004_D020_Id 
	                                 LEFT JOIN D018 as C004Estado ON C004Estado.D018_Id=C004_D018_Id 
	                                 LEFT JOIN D020 as D024Cidade ON D024Cidade.D020_Id=D024_D020_Id 
	                                 LEFT JOIN D018 as D024Estado ON D024Estado.D018_Id=D024_D018_Id
	                                 WHERE T005_Id='{$T005_Id}'");
            $mCabecalho = mysql_fetch_array($cabecalho);
            
            $produto = "SELECT T006_Codigo_Produto AS codigoProduto,
                                 T006_Descricao_Produto AS descricaoProduto,
                                 T006_Classificacao_Fiscal AS NCM, 
                                 D006_Codigo_CFOP AS CFOP,
                                 D037_Unidade AS unidade,
                                 T006_Quantidade AS quantidade,
                                 T006_Valor_Preco_Sem_Desconto_Unitario AS valorUnitarioComercializacao,
                                 T006_Valor_Total_Preco_Sem_Desconto AS valorTotalBruto,
                                 T006_Numero_Pedido_Compra AS numeroPedidoCompra,
                                 T006_Valor_Trib_Transparencia AS valorAproximadoTributos,
                                 T006_Flag_Situacao_Tributaria AS situacaoTributaria,
                                 T006_Modalidade_ICMS AS modalidadeICMS,
                                 T006_Aliquota_ICMS AS aliquotaICMS,
                                 T006_Valor_ICMS AS valorICMS, 
                                 D059_Codigo_ST AS codigoST,
                                 T006_Modalidade_ICMS_Substituicao AS modalidadeBCICMS,
                                 T006_Valor_Base_ICMS AS valorBaseICMS,
                                 T006_Valor_ICMS_Substituicao_Retido AS valorICMSSTRetido,
                                 T006_Percentual_Reducao_ICMS AS percentualReducaoICMS,
                                 T006_Valor_ICMS_Desonerado AS valorICMSDesonerado,
                                 T006_Motivo_Desoneracao_ICMS AS motivoDesoneracaoICMS,
                                 T006_Item_Pedido_Compra AS itemPedidoCompra
                          FROM T006
                          LEFT JOIN D006 ON D006_Id=T006_D006_Id
                          LEFT JOIN D037 ON D037_Id=T006_D037_Id 
                          LEFT JOIN D059 ON D059_Id=T006_D059_Id
                          WHERE T006_T005_Id = '{$T005_Id}'";


            // Seleciona a natureza da operação de todos os produtos e converte o array gerado em uma única string.
            $descricao_CFOP  = " select ";
            $descricao_CFOP .= " D006_Flag_Estado, ";
            $descricao_CFOP .= " D006_Descricao ";
            $descricao_CFOP .= " from T006 ";
            $descricao_CFOP .= " left join D006 on D006_Id=T006_D006_Id ";
            $descricao_CFOP .= " where T006_T005_Id='{$T005_Id}'";
            $descricao_CFOP .= " group by T006_D006_Id ";
            $cSql_T006 = mysql_query($descricao_CFOP);
            echo mysql_error();
            
            unset($mDescricao_CFOP);
            while ($mT006 = mysql_fetch_array($cSql_T006))
            {
                   $mDescricao_CFOP[] = $mT006[D006_Descricao];
                   $cD006_Flag_Estado = $mT006[D006_Flag_Estado];
            }
            $cDescricao_CFOP=implode(" - ",$mDescricao_CFOP);
            $cDescricao_CFOP=substr($cDescricao_CFOP,0,60);

        } else if ($T007_Id>0){

            $cabecalho = mysql_query("SELECT T007_Cliente_Bairro AS bairroCliente,
                                        T007_Endereco AS enderecoCliente,
                                        T007_Cliente_Endereco_Numero AS numeroEnderecoCliente,
                                        T007_Cliente_Cidade AS cidadeCliente,
                                        T007_Cliente_CEP AS CEPCliente, 
                                        T007_Empresa_Usuaria_Codigo_Estado_IBGE AS empresaCodigoUF,
                                        T007_Chave_Acesso_Nfe,
                                        T007_Serie,
                                        T007_Numero_Nota_Fiscal,
                                        T007_Data_Emissao,
                                        T007_Data_Saida,
                                        T007_Hora_Saida,
                                        T007_Flag_ACP AS modeloACP,
                                        T007_Flag_Entrada_Saida,
                                        T007_Flag_Destino_Operacao,
                                        T007_Empresa_Usuaria_Codigo_Cidade_IBGE AS codMunicipioEmpresa,
                                        T007_Flag_Finalidade_NFe,
                                        D024_Flag_Contribuinte AS flagContribuinte,
                                        T007_Fuso_Horario_Emissao,
                                        T007_Empresa_Usuaria_Cnpj AS CNPJEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Nome AS nomeEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Endereco AS enderecoEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Numero AS numeroEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Bairro AS bairroEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Cidade AS cidadeEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Uf AS UFEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Cep AS CEPEmpresaUsuaria,
                                        T007_Empresa_Usuaria_Inscricao_Estadual AS IEEmpresaUsuaria,
                                        T007_Inscricao_Municipal AS IMEmpresaUsuaria,
                                        T007_Empresa_Usuaria_CRT AS CRTEmpresaUsuaria,
                                        T007_Cliente_CNPJ_CPF,
                                        T007_Cliente_Razao AS nomeCliente,
                                        T007_Cliente_Codigo_Cidade_IBGE AS CodCidadeCliente,
                                        T007_Cliente_Estado AS UFCliente,
                                        T007_Valor_Total_Base_ICMS_Impresso AS valorTotalBCICMS,
                                        T007_Valor_Total_ICMS AS valorTotalICMS,  
                                        T007_Valor_Total_ICMS_Desonerado AS totalICMSDesonerado, 
                                        T007_Valor_Total_Produtos AS totalProdutos,
                                        T007_Valor_Frete AS valorFrete,
                                        T007_Valor_Desconto AS valorDesconto,
                                        T007_Valor_Total_IPI AS totalIPI,
                                        T007_Valor_Total_PIS AS totalPIS,
                                        T007_Valor_Total_COFINS AS totalCofins,
                                        T007_Valor_Despesas_Acessorias AS despesasAcessorias,
                                        T007_Valor_Total AS valorTotal,
                                        T007_Flag_Frete AS freteTotal


                                 FROM T007
                                 LEFT JOIN D024 ON D024_Id=T007_D024_Id
                                 WHERE T007_Id='{$T007_Id}'");
            $mCabecalho = mysql_fetch_array($cabecalho);

            // pesquisa de produtos
            $produto = "SELECT T008_Codigo_Produto AS codigoProduto,
                                 T008_Descricao_Produto AS descricaoProduto,
                                 T008_Classificacao_Fiscal AS NCM,
                                 T008_Unidade AS unidade,
                                 T008_Quantidade AS quantidade,
                                 T008_Valor_Preco_Sem_Desconto_Unitario AS valorUnitarioComercializacao,
                                 T008_Valor_Total_Preco_Sem_Desconto AS valorTotalBruto, 
                                 T008_Numero_Pedido_Compra AS numeroPedidoCompra,
                                 T008_Valor_Trib_Transparencia AS valorAproximadoTributos,
                                 T008_Flag_Situacao_Tributaria AS situacaoTributaria,
                                 T008_Modalidade_ICMS AS modalidadeICMS,
                                 T008_Valor_Base_ICMS_Impresso AS valorBaseICMS,
                                 T008_Aliquota_ICMS AS aliquotaICMS, 
                                 T008_Valor_ICMS AS valorICMS,
                                 D059_Codigo_ST AS codigoST,
                                 T008_Modalidade_ICMS_Substituicao AS modalidadeBCICMS,
                                 T008_Valor_ICMS_Substituicao_Retencao AS valorICMSSTRetido,
                                 T008_Percentual_Reducao_ICMS AS percentualReducaoICMS,
                                 T008_Valor_ICMS_Desonerado AS valorICMSDesonerado,
                                 T008_Motivo_Desoneracao_ICMS AS motivoDesoneracaoICMS,
                                 T008_Item_Pedido_Compra AS itemPedidoCompra

                          FROM T008 
                          LEFT JOIN D059 ON D059_Id=T008_D059_Id
                          WHERE T008_T007_Id = '{$T007_Id}'";

            // Seleciona a natureza da operação de todos os produtos e converte o array gerado em uma única string.
            $descricao_CFOP  = " select ";
            $descricao_CFOP .= " D006_Flag_Estado, ";
            $descricao_CFOP .= " D006_Descricao ";
            $descricao_CFOP .= " from T008 ";
            $descricao_CFOP .= " left join D006 on D006_Id=T008_D006_Id ";
            $descricao_CFOP .= " where T008_D006_Id='{$T008_Id}'";
            $descricao_CFOP .= " group by T008_D006_Id ";
            $cSql_T006 = mysql_query($descricao_CFOP);
            
            unset($mDescricao_CFOP);
            while ($mT006 = mysql_fetch_array($cSql_T006))
            {
                   $mDescricao_CFOP[] = $mT006[D006_Descricao];
                   $cD006_Flag_Estado = $mT006[D006_Flag_Estado];
            }
            $cDescricao_CFOP=implode(" - ",$mDescricao_CFOP);
            $cDescricao_CFOP=substr($cDescricao_CFOP,0,60);

            // Select das Notas fiscais referenciadas - NFR
            $T132 = mysql_query("SELECT T132_T007_Id AS T132_T007_Id,
                                  T132_Chave_Acesso AS chaveAcessoNFR,
                                  T132_UF_IBGE AS UFNFR,
                                  T132_Ano_Mes AS anoMesNFR, 
                                  T132_Cnpj AS CNPJNFR, 
                                  T132_Modelo AS modeloNFR, 
                                  T132_Serie AS serieNFR,
                                  T132_Numero_Nota_Fiscal AS numeroNFR, 
                                  T132_Cupom_Numero_EFC AS sequencialECFNFR,
                                  T132_Cupom_Numero_COO AS numeroCOONFR,
                                  T132_tipo AS tipoNFR
                      FROM T132
                      WHERE T132_T007_Id = '{$T007_Id}'");  

            $mT132 = mysql_fetch_array($T132);

        } else {
            return "Tabela não informada";
        }

         // B-10-B06
        // Não validar caso não seja NF-e ou NFC-e
        if(($mCabecalho['modeloACP'] != 6) && ($mCabecalho['modeloACP'] != 8)){
            return true;
        }

        // Validação do cabeçalho 

        require_once('layoutE.php');

		// Loop para validação dos produtos

        /*

        $produtos = mysql_query($produto);

        $erros = '';

        if(mysql_num_rows($produtos) > 0) {
            $erros .= "\n";
            while($mProduto = mysql_fetch_array($produtos)){

            }
        }

        */

        if(empty($erros)){
            return true;
        }else{
            return $erros;
        }
    }

    /*
     * Retirar caracteres 
     * @param String $dado
     * @return String
    */
    public function retirarCaracteres($dado){
        $dado=str_replace(".","",$dado);
        $dado=str_replace("-","",$dado);
        $dado=str_replace("/","",$dado);
        $dado=str_replace(" ","",$dado);
        $dado=trim($dado);
        
        return $dado; 
    }

    /*
     * Conta casas decimais 
     * @param String $valor
     * @return int
    */
    public function contaCasasDecimais($valor){
        $pos = strpos($valor, ',');
        $extrai = strlen(substr($valor, $pos+1));
        return $extrai;
    }

    /*
     * Conta calcula dígito verificador município 
     * @param int $valor
     * @return int
    */
    function calcula_dv_municipio($codigo_municipio){
        $peso = "1212120";
        $soma = 0;
        for($i = 0; $i < 7; $i++){ 
            $valor = substr($codigo_municipio,$i,1) * substr($peso,$i,1); if($valor>9)
            $soma = $soma + substr($valor,0,1) + substr($valor,1,1);
            else
            $soma = $soma + $valor;
        }
        $dv = (10 - ($soma % 10));
        if(($soma % 10)==0)
            $dv = 0;
        return $dv;
    }

}


