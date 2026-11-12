<?php
namespace hardness;
require_once("old/lib/funcoes.php");
definirClasse('FIS006');
class FIS006_
{

    /**
     * exportarSintegra
     *
     * @param Form $form
     * @param File $arquivo
     * @return bool
     */    
    public function exportarSintegra($form, $arquivo = null)
    { 
        global $g;
        
        $Data_Inicio     = $form->campoValorEnviado('Data_Inicio');
        $Data_Fim        = $form->campoValorEnviado('Data_Fim');
        $Finalidade      = $form->campoValorEnviado('Finalidade');
        $Registro        = $form->campoValorEnviado('Registro');
        $Inventario      = $form->campoValorEnviado('Inventario');
        $Data_Inventario = $form->campoValorEnviado('Data_Inventario');
        
        set_time_limit(0);
        
        if (empty($Finalidade)) {
            return 'Selecione a Finalidade do arquivo';
        }
        
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Criando o Arquivo
        
        //-- Definindo Variaveis e Constantes
        define(SALTAR_LINHA, chr(13) . chr(10));
        
        $cAgrega_Dados = '';
        if (!validar_data($Data_Inicio)) {
            return "Data Inicial Inválida: " . $Data_Inicio;
        }
        if (!validar_data($Data_Fim)) {
            return "Data Final Inválida: " . $Data_Fim;
        }
        if (!empty($Data_Inventario) AND !validar_data($Data_Inventario)) {
            return "Data Inventário Inválida: " . $Data_Inventario;
        }
        
        //troca a data para ano mes e dia
        $Data_Inicio       = Troca_Data_amd($Data_Inicio);
        $Data_Fim          = Troca_Data_amd($Data_Fim);
        $Data_Inventario   = Troca_Data_amd($Data_Inventario);
        $C004_Data_Final   = $Data_Fim;
        $C004_Data_Inicial = $Data_Inicio;
        
        //-- Pesquisando a Empresa usuaria
        $cC004 = mysql_query("SELECT *
	                             FROM C004
	                             LEFT JOIN D020 ON D020_Id=C004_D020_Id
	                             LEFT JOIN D018 ON D018_Id=D020_D018_Id
	                            WHERE C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($cC004);
        
        $mC004['C004_Nome_Abreviado'] = str_replace('.', '_', $mC004['C004_Nome_Abreviado']);
        $mC004['C004_Nome_Abreviado'] = str_replace(' - ', '_', $mC004['C004_Nome_Abreviado']);
        $mC004['C004_Nome_Abreviado'] = str_replace('-', '', $mC004['C004_Nome_Abreviado']);
        $mC004['C004_Nome_Abreviado'] = str_replace(' ', '_', $mC004['C004_Nome_Abreviado']);
        
        $Cnpj = str_replace('-', '', $mC004['C004_Cnpj']);
        $Cnpj = str_replace('.', '', $Cnpj);
        $Cnpj = str_replace('/', '', $Cnpj);
        
        $nRandom = rand(99999, 9999999999);
        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/SINTEGRA_" . $Cnpj . "_" . substr(str_replace('-', '_', $Data_Fim), 0, 7) . ".txt";
        //$cArquivo=str_replace("/empresas/$Pasta_Empresa","",$cArquivo);
        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }
        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }
        CHMOD($cArquivo, 0777);
        
        //variavel contadoras de linhas
        $conta_linha = 1;
        
        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());
        
        //-- Prepara o campo numero da remessa
        $Numero_Remessa = $mT035['T035_Id'];
        $Numero_Remessa = STR_REPEAT('0', 7 - STRLEN($Numero_Remessa)) . $Numero_Remessa;
        
        ////////////////////////////////////////////////////////////
        // tabela 10
        /////////////////////////////////////////////////////////////
        
        //-- Preparando o campo Cnpj
        $C004_Cnpj = STR_REPLACE(".", "", $mC004['C004_Cnpj']);
        $C004_Cnpj = STR_REPLACE("/", "", $C004_Cnpj);
        $C004_Cnpj = STR_REPLACE("-", "", $C004_Cnpj);
        $C004_Cnpj = STR_REPEAT('0', 14 - STRLEN($C004_Cnpj)) . $C004_Cnpj;
        
        // inscricao_estadual
        $C004_Inscricao_Estadual = STR_REPLACE(".", "", $mC004['C004_Inscricao_Estadual']);
        $C004_Inscricao_Estadual = STR_REPLACE("/", "", $C004_Inscricao_Estadual);
        $C004_Inscricao_Estadual = STR_REPLACE("-", "", $C004_Inscricao_Estadual);
        if (empty($C004_Inscricao_Estadual)) {
            $C004_Inscricao_Estadual = "ISENTO";
        }
        $C004_Inscricao_Estadual .= STR_REPEAT(" ", 14 - STRLEN($C004_Inscricao_Estadual));
        
        //-- Prepara o campo "Nome da Empresa"
        $C004_Nome_Empresa = SUBSTR($mC004['C004_Nome_Empresa'], 0, 35);
        $C004_Nome_Empresa = $C004_Nome_Empresa . STR_REPEAT(' ', 35 - STRLEN($C004_Nome_Empresa));
        
        // municipio
        $C004_Cidade = SUBSTR($mC004['D020_Nome_Cidade'], 0, 30);
        $C004_Cidade .= STR_REPEAT(' ', 30 - STRLEN($C004_Cidade));
        
        // uf
        $C004_Uf = SUBSTR($mC004['D018_UF'], 0, 2);
        $C004_Uf .= STR_REPEAT(' ', 2 - STRLEN($C004_Uf));
        
        // fax
        $C004_Fax = SUBSTR($mC004['C004_Fax'], 0, 10);
        $C004_Fax = str_replace("(", "", ($C004_Fax));
        $C004_Fax = str_replace(")", "", ($C004_Fax));
        $C004_Fax = str_replace(" ", "", ($C004_Fax));
        $C004_Fax = str_replace("-", "", ($C004_Fax));
        $C004_Fax = STR_REPEAT(0, 10 - STRLEN($C004_Fax)) . $C004_Fax;
        
        //Identificacao_Convenio
        $C004_Identificacao_Convenio  = "3";
        //Identificacao_Operacao
        $C004_Identificacao_Operacao  = "3";
        //C004_Identificacao_Magnetico
        $C004_Identificacao_Magnetico = "$Finalidade";
        
        //data inicial
        $C004_Data_Inicial = str_replace("-", "", ($C004_Data_Inicial));
        
        //data final
        $C004_Data_Final = str_replace("-", "", ($C004_Data_Final));
        
        //-- tabela 10                                               //   Tam  posicao  descricao
        $cAgrega_Dados = "10"; //-- 2    1-2      -destino
        $cAgrega_Dados .= $C004_Cnpj; //-- 14   3-16     -cnpj
        $cAgrega_Dados .= $C004_Inscricao_Estadual; //-- 14   17-30    -Inscricao_Estadual
        $cAgrega_Dados .= $C004_Nome_Empresa; //-- 35   31-65    - razao social da empresa
        $cAgrega_Dados .= $C004_Cidade; //-- 30   66-95    - cidade
        $cAgrega_Dados .= $C004_Uf; //-- 2    96-97    - Uf
        $cAgrega_Dados .= $C004_Fax; //-- 10   98-107   - fax
        $cAgrega_Dados .= $C004_Data_Inicial; //-- 8    108-115  - Data inicial AAAA/MM/DD
        $cAgrega_Dados .= $C004_Data_Final; //-- 8    116-123  - Data final AAAA/MM/DD
        $cAgrega_Dados .= $C004_Identificacao_Convenio; //-- 1    124-124  - Codigo de indentificacao do convenio
        $cAgrega_Dados .= $C004_Identificacao_Operacao; //-- 1    125-125  - Codigo de indentificacao da natureza da operacao
        $cAgrega_Dados .= $C004_Identificacao_Magnetico; //-- 1    126-126  - Codigo de indentificacao da finalidade utiliza no arquivo magnetico confome tabela
        $cAgrega_Dados .= SALTAR_LINHA;
        
        gravar_linha($cArquivo, $cAgrega_Dados);
        $cAgrega_Dados = "";
        $conta_linha++;
        
        ///////////////////////////////////////////////////////////
        // tabela 11
        //////////////////////////////////////////////////////////
        
        // Logradouro
        $C004_Logradouro = SUBSTR($mC004['C004_Logradouro'], 0, 34);
        $C004_Logradouro = $C004_Logradouro . STR_REPEAT(' ', 34 - STRLEN($C004_Logradouro));
        
        // Numero
        $C004_Numero = SUBSTR($mC004['C004_Numero'], 0, 5);
        $C004_Numero = $C004_Numero . STR_REPEAT(0, 5 - STRLEN($C004_Numero));
        
        // Complento
        $C004_Complemento = SUBSTR($mC004['C004_Complemento'], 0, 22);
        $C004_Complemento .= STR_REPEAT(' ', 22 - STRLEN($C004_Complemento));
        
        // Bairro
        $C004_Bairro = SUBSTR($mC004['C004_Bairro'], 0, 15);
        $C004_Bairro .= STR_REPEAT(' ', 15 - STRLEN($C004_Bairro));
        
        // Cep
        $C004_Cep = SUBSTR($mC004['C004_Cep'], 0, 10);
        $C004_Cep = STR_REPLACE(".", "", $C004_Cep);
        $C004_Cep = STR_REPLACE("-", "", $C004_Cep);
        $C004_Cep = $C004_Cep . STR_REPEAT('0', 8 - STRLEN($C004_Cep));
        
        // Nome do Contato
        $C004_Responsavel_Contato .= SUBSTR($mC004['C004_Responsavel_Contato'], 0, 28);
        $C004_Responsavel_Contato .= STR_REPEAT(' ', 28 - STRLEN($C004_Responsavel_Contato));
        
        // telefone
        $C004_Telefone = SUBSTR($mC004['C004_Telefone'], 0, 12);
        $C004_Telefone = STR_REPLACE("(", "", $C004_Telefone);
        $C004_Telefone = STR_REPLACE(")", "", $C004_Telefone);
        $C004_Telefone = STR_REPLACE(" ", "", $C004_Telefone);
        $C004_Telefone = STR_REPLACE("-", "", $C004_Telefone);
        $C004_Telefone = STR_REPEAT('0', 12 - STRLEN($C004_Telefone)) . $C004_Telefone;
        
        // table 11                                                  //   Tam  posicao  descricao
        $cAgrega_Dados .= "11"; //-- 2    1-2     - tipo
        $cAgrega_Dados .= $C004_Logradouro; //-- 34   3-36    - logradouro
        $cAgrega_Dados .= $C004_Numero; //-- 5    37-41   - Numero
        $cAgrega_Dados .= $C004_Complemento; //-- 22   42-63   - Complemento
        $cAgrega_Dados .= $C004_Bairro; //-- 15   64-78   - Bairro
        $cAgrega_Dados .= $C004_Cep; //-- 8    79-86   - Cep
        $cAgrega_Dados .= $C004_Responsavel_Contato; //-- 28   87-114  - C004_Responsavel_Contato
        $cAgrega_Dados .= $C004_Telefone; //-- 12   115-126 - C004_Telefone
        $cAgrega_Dados .= SALTAR_LINHA;
        
        gravar_linha($cArquivo, $cAgrega_Dados);
        
        $cAgrega_Dados = "";
        $conta_linha++;
        
        if ($Registro == 1 OR $Registro == 0) {
            
            /////////////////////////////////////////////////////////////////////
            // TIPO 50 - ICMS
            // ENTIDADE: T007 NOTA EMITIDA
            /////////////////////////////////////////////////////////////////////
            $cClausula_T007 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Inscricao_Estadual,
		                                  D018_UF,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
		                                  T007_Cliente_CNPJ_CPF,
		                                  T007_Cliente_Inscricao_Estadual,
		                                  T007_Cliente_Estado,
		                                  T007_Flag_ACP,
		                                  D006_Codigo_CFOP,
		                                  T007_Data_Emissao,
		                                  round(sum(T008_Valor_Total_Preco+T008_Valor_Total_Frete+T008_Valor_Total_Despesas_Acessorias+T008_Valor_IPI-T008_Valor_Total_Desconto),2) T007_Valor_Total,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(if(T007_Flag_Finalidade_NFe=1,sum(T008_Valor_ICMS),T007_Valor_Total_ICMS),2)) T007_Valor_Total_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(if(T007_Flag_Finalidade_NFe=1,sum(T008_Valor_Base_ICMS),T007_Valor_Total_Base_ICMS),2)) T007_Valor_Total_Base_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T008_Aliquota_ICMS) T007_Aliquota_ICMS,
		                                  T007_Numero_Nota_Fiscal,
		                                  T007_Flag_Cancelada,
		                                  round(sum(T008_Valor_Total_Frete),2) T007_Valor_Frete,
		                                  round(sum(T008_Valor_Total_Despesas_Acessorias),2) T007_Valor_Despesas_Acessorias
		                             FROM T008
		                        LEFT JOIN T007 ON T007_Id=T008_T007_Id
		                        LEFT JOIN D024 ON D024_Id=T007_D024_Id
		                        LEFT JOIN D006 ON D006_Id=T008_D006_Id
		                        LEFT JOIN D018 ON D018_Id=D024_D018_Id
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND (T007_Flag_ACP='1' OR T007_Flag_ACP='6')
		                              AND T007_Numero_Nota_Fiscal!=''
		                              AND T007_Flag_Inutilizado!='S'
		                         GROUP BY concat(T007_Numero_Nota_Fiscal,D024_Id,D006_Codigo_CFOP),T008_Aliquota_ICMS
		                         ORDER BY T007_Data_Emissao,T007_Numero_Nota_Fiscal ";
            
            /*                                  round(T008_Valor_Total_Preco,2) as T008_Valor_Total_Preco,
            as T008_Valor_Base_ICMS,
            round(T008_Valor_IPI,2) as T008_Valor_IPI,
            round(T008_Aliquota_ICMS,2) as T008_Aliquota_ICMS */
            
            $cT007        = mysql_query($cClausula_T007);
            $rows_50_T007 = mysql_num_rows($cT007);
            $erro         = mysql_error();
            //echo "$erro";
            
            while ($mT007 = mysql_fetch_array($cT007)) {
                
                if (!empty($mT007['T007_Cliente_CNPJ_CPF'])) {
                    $T007_Cpf_Cnpj = $mT007['T007_Cliente_CNPJ_CPF'];
                } else {
                    // verifica em que campo cpf ou cnpj
                    if ($mT007['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                        //-- Preparando o campo Cnpj
                        $T007_Cpf_Cnpj = $mT007['D024_Cnpj'];
                    } else {
                        //--  Preparando o campo Cpf
                        $T007_Cpf_Cnpj = $mT007['D024_Cpf'];
                    }
                }
                $T007_Cpf_Cnpj = STR_REPLACE(".", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE(" ", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("/", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("-", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_PAD($T007_Cpf_Cnpj, 14, "0", STR_PAD_LEFT);
                
                /*
                if (!empty($mT007['T007_Cliente_Inscricao_Estadual'])) {
                $T007_Inscricao_Estadual=$mT007['T007_Cliente_Inscricao_Estadual'];            	
                } else {
                // inscricao_estadual
                $T007_Inscricao_Estadual = $mT007['D024_Inscricao_Estadual'];
                }
                */
                
                $T007_Inscricao_Estadual = $mT007['D024_Inscricao_Estadual'];
                
                if (empty($T007_Inscricao_Estadual) OR strtoupper(trim($T007_Inscricao_Estadual)) == 'ISENTA') {
                    $T007_Inscricao_Estadual = "ISENTO";
                }
                $T007_Inscricao_Estadual = STR_REPLACE(".", "", $T007_Inscricao_Estadual);
                $T007_Inscricao_Estadual = STR_REPLACE(" ", "", $T007_Inscricao_Estadual);
                $T007_Inscricao_Estadual = STR_REPLACE("/", "", $T007_Inscricao_Estadual);
                $T007_Inscricao_Estadual = STR_REPLACE("-", "", $T007_Inscricao_Estadual);
                $T007_Inscricao_Estadual .= STR_REPEAT(' ', 14 - STRLEN($T007_Inscricao_Estadual));
                $T007_Inscricao_Estadual = SUBSTR($T007_Inscricao_Estadual, 0, 14);
                
                // data  de emissao
                $T007_Data_Emissao = STR_REPLACE("-", "", $mT007['T007_Data_Emissao']);
                
                // uf
                $T007_D024_Estado = $mT007['D018_UF'];
                $T007_D024_Estado = SUBSTR($T007_D024_Estado, 0, 2);
                $T007_D024_Estado = STR_PAD($T007_D024_Estado, 2, " ", STR_PAD_RIGHT);
                
                // numero da nota fiscal
                $T007_Numero_Nota_Fiscal = SUBSTR($mT007['T007_Numero_Nota_Fiscal'], 0, 6);
                $T007_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T007_Numero_Nota_Fiscal)) . $T007_Numero_Nota_Fiscal;
                
                // numero da nota fiscal
                $T007_CFOP = STR_REPLACE(".", "", $mT007['D006_Codigo_CFOP']);
                $T007_CFOP = SUBSTR($T007_CFOP, 0, 4);
                $T007_CFOP = STR_REPEAT('0', 4 - STRLEN($T007_CFOP)) . $T007_CFOP;
                
                // valor total da nota fiscal
                $T007_Valor_Total = STR_REPLACE(".", "", $mT007['T007_Valor_Total']);
                $T007_Valor_Total = STR_REPLACE(",", "", $T007_Valor_Total);
                $T007_Valor_Total = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total)) . $T007_Valor_Total;
                
                // Base ICMS
                $T007_Valor_Total_Base_ICMS = STR_REPLACE(".", "", $mT007['T007_Valor_Total_Base_ICMS']);
                $T007_Valor_Total_Base_ICMS = STR_REPLACE(",", "", $T007_Valor_Total_Base_ICMS);
                $T007_Valor_Total_Base_ICMS = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total_Base_ICMS)) . $T007_Valor_Total_Base_ICMS;
                
                // valor ICMS
                $T007_Valor_Total_ICMS = STR_REPLACE(".", "", $mT007['T007_Valor_Total_ICMS']);
                $T007_Valor_Total_ICMS = STR_REPLACE(",", "", $T007_Valor_Total_ICMS);
                $T007_Valor_Total_ICMS = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total_ICMS)) . $T007_Valor_Total_ICMS;
                
                // isenta nao nao tributada
                $T007_Nao_Tributada = number_format($mT007['T007_Valor_Despesas_Acessorias'] + $mT007['T007_Valor_Frete'], 2);
                $T007_Nao_Tributada = STR_REPLACE(".", "", $T007_Nao_Tributada);
                $T007_Nao_Tributada = STR_REPLACE(",", "", $T007_Nao_Tributada);
                $T007_Nao_Tributada = STR_REPEAT('0', 13 - STRLEN($T007_Nao_Tributada)) . $T007_Nao_Tributada;
                
                // outra
                $T007_Outra = STR_REPLACE(".", "", $T007_Outra);
                $T007_Outra = STR_REPLACE(",", "", $T007_Outra);
                $T007_Outra = STR_REPEAT('0', 13 - STRLEN($T007_Outra)) . $T007_Outra;
                
                // Percentual icms
                $T007_Aliquota_ICMS = STR_REPLACE(".", "", $mT007['T007_Aliquota_ICMS']);
                $T007_Aliquota_ICMS = STR_REPLACE(",", "", $T007_Aliquota_ICMS);
                $T007_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T007_Aliquota_ICMS)) . $T007_Aliquota_ICMS;
                
                //T007_Flag_Cancelada
                $T007_Flag_Cancelada = $mT007['T007_Flag_Cancelada'];
                if (empty($T007_Flag_Cancelada)) {
                    $T007_Flag_Cancelada = "N";
                }
                
                $cAgrega_Dados .= "50"; //-- 2    1-2     - tipo
                $cAgrega_Dados .= $T007_Cpf_Cnpj; //-- 14   3-16    - T007_Cpf_Cnpj
                $cAgrega_Dados .= $T007_Inscricao_Estadual; //-- 14   17-30   - T007_Inscricao_estadual
                $cAgrega_Dados .= $T007_Data_Emissao; //-- 8    31-38   - data de emissao
                $cAgrega_Dados .= $T007_D024_Estado; //-- 2    39-40   - unidade federativa
                if ($mT007['T007_Flag_ACP'] == '6') {
                    $cAgrega_Dados .= '55'; //-- 2    41-42   - Codigo do modelo fiscal
                } else {
                    $cAgrega_Dados .= '01'; //-- 2    41-42   - Codigo do modelo fiscal
                }
                $cAgrega_Dados .= '001'; //-- 3    43-45   - Serie nota fiscal
                $cAgrega_Dados .= $T007_Numero_Nota_Fiscal; //-- 6    46-51   - Numero da Nota Fiscal
                $cAgrega_Dados .= $T007_CFOP; //-- 4    52-55   - Numero da Nota Fiscal
                $cAgrega_Dados .= 'P'; //-- 1    56      - Emitente
                $cAgrega_Dados .= $T007_Valor_Total; //-- 13   57-69   - Valor Total
                $cAgrega_Dados .= $T007_Valor_Total_Base_ICMS; //-- 13   70-82   - Valor Base_ICmS
                $cAgrega_Dados .= $T007_Valor_Total_ICMS; //-- 13   83-95   - Valor Base_ICmS
                $cAgrega_Dados .= $T007_Nao_Tributada; //-- 13   96-108  - Valor Isenta ou nao tributada
                $cAgrega_Dados .= $T007_Outra; //-- 13   109-121 - nao confirmada ou nao creditada
                $cAgrega_Dados .= $T007_Aliquota_ICMS; //-- 4    122-125 - nao confirmada ou nao creditada
                $cAgrega_Dados .= $T007_Flag_Cancelada; //-- 4    122-125 - nao confirmada ou nao creditada
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////
            // TIPO 50 - ICMS
            // ENTIDADE: T013 NOTA RECEBIDA
            /////////////////////////////////////////////////////////////////////
            $cClausula_T013 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Inscricao_Estadual,
		                                  D018_UF,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
		                                  D006_Codigo_CFOP,
		                                  T013_Data_Emissao,
		                                  T013_Data_Entrada,
		                                  round(sum(T014_Valor_Total_Custo+if(T013_Flag_Frete='1',T014_Valor_Frete,0)+T014_Valor_Despesas_Acessorias+T014_Valor_IPI-T014_Valor_Total_Desconto),2) T013_Valor_Total,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(sum(T014_Valor_Base_ICMS),2)) as T013_Valor_Base_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(sum(T014_Valor_ICMS),2)) as T013_Valor_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T014_Aliquota_ICMS) T014_Aliquota_ICMS,
		                                  T013_Numero_Nota_Fiscal,
		                                  T013_Valor_Frete,
		                                  T013_Valor_Seguro,
		                                  T013_Valor_Despesas_Acessorias,
		                                  T013_Flag_Cancelada
		                             FROM T014
		                        LEFT JOIN T013 ON T013_Id=T014_T013_Id
		                        LEFT JOIN D024 ON D024_Id=T013_D024_Id
		                        LEFT JOIN D020 ON D020_Id=D024_D020_Id                        
		                        LEFT JOIN D018 ON D018_Id=D020_D018_Id
		                        LEFT JOIN D006 ON D006_Id=T014_D006_Id or D006_Id=T013_D006_Id
		                            WHERE T013_C004_Id='{$g['empresaAtual']}'
		                              AND T013_Data_Entrada >= '$Data_Inicio'
		                              AND T013_Data_Entrada <= '$Data_Fim'
		                              AND T013_Numero_Nota_Fiscal not LIKE '%A%'
		                              AND FLOOR(T013_Numero_Nota_Fiscal)>0
		                              AND ifnull(T013_Flag_Cancelada,'N')!='S'
		                              AND ifnull(T013_Flag_Travar_Nota,'N')='S'
		                         GROUP BY concat(T013_Id,D024_Id),
		                                  D006_Codigo_CFOP,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T014_Aliquota_ICMS)
		                         ORDER BY T013_Data_Entrada,
		                                  T013_Numero_Nota_Fiscal,
		                                  T013_Id";
            
            $cT013        = mysql_query($cClausula_T013);
            $rows_50_T013 = mysql_num_rows($cT013);
            $erro         = mysql_error();
            echo "$erro";
            
            while ($mT013 = mysql_fetch_array($cT013)) {
                
                // verifica em que campo cpf ou cnpj
                if ($mT013['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                    //-- Preparando o campo Cnpj
                    $T013_Cpf_Cnpj = $mT013['D024_Cnpj'];
                } else {
                    //--  Preparando o campo Cpf
                    $T013_Cpf_Cnpj = $mT013['D024_Cpf'];
                }
                $T013_Cpf_Cnpj = STR_REPLACE(".", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE("/", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE(" ", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE("-", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPEAT('0', 14 - STRLEN($T013_Cpf_Cnpj)) . $T013_Cpf_Cnpj;
                $T013_Cpf_Cnpj = SUBSTR($T013_Cpf_Cnpj, 0, 14);
                
                // inscricao_estadual
                if (empty($mT013['D024_Inscricao_Estadual']) OR trim(strtoupper($mT013['D024_Inscricao_Estadual'])) == 'ISENTA') {
                    $T013_Inscricao_Estadual = "ISENTO";
                } else {
                    $T013_Inscricao_Estadual = STR_REPLACE(".", "", $mT013['D024_Inscricao_Estadual']);
                    $T013_Inscricao_Estadual = STR_REPLACE("/", "", $T013_Inscricao_Estadual);
                    $T013_Inscricao_Estadual = STR_REPLACE("-", "", $T013_Inscricao_Estadual);
                    $T013_Inscricao_Estadual = STR_REPLACE(" ", "", $T013_Inscricao_Estadual);
                }
                $T013_Inscricao_Estadual .= STR_REPEAT(' ', 14 - STRLEN($T013_Inscricao_Estadual));
                $T013_Inscricao_Estadual = SUBSTR($T013_Inscricao_Estadual, 0, 14);
                
                // data  de emissao
                $T013_Data_Emissao = STR_REPLACE("-", "", $mT013['T013_Data_Entrada']);
                
                // uf
                $T013_D024_Estado = SUBSTR($mT013['D018_UF'], 0, 2);
                $T013_D024_Estado . STR_REPEAT(' ', 2 - STRLEN($T013_D024_Estado));
                
                // numero da nota fiscal
                $T013_Numero_Nota_Fiscal = SUBSTR($mT013['T013_Numero_Nota_Fiscal'], 0, 6);
                $T013_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T013_Numero_Nota_Fiscal)) . $T013_Numero_Nota_Fiscal;
                
                // numero da nota fiscal
                $T013_CFOP = STR_REPLACE(".", "", $mT013['D006_Codigo_CFOP']);
                $T013_CFOP = SUBSTR($T013_CFOP, 0, 4);
                $T013_CFOP = STR_REPEAT('0', 4 - STRLEN($T013_CFOP)) . $T013_CFOP;
                
                // valor total da nota fiscal
                $T013_Valor_Total = STR_REPLACE(".", "", $mT013['T013_Valor_Total']);
                $T013_Valor_Total = STR_REPLACE(",", "", $T013_Valor_Total);
                $T013_Valor_Total = STR_REPEAT('0', 13 - STRLEN($T013_Valor_Total)) . $T013_Valor_Total;
                
                // Base ICMS
                $T013_Valor_Base_ICMS = STR_REPLACE(".", "", $mT013['T013_Valor_Base_ICMS']);
                $T013_Valor_Base_ICMS = STR_REPLACE(",", "", $T013_Valor_Base_ICMS);
                $T013_Valor_Base_ICMS = STR_REPEAT('0', 13 - STRLEN($T013_Valor_Base_ICMS)) . $T013_Valor_Base_ICMS;
                
                // valor ICMS
                $T013_Valor_ICMS = STR_REPLACE(".", "", $mT013['T013_Valor_ICMS']);
                $T013_Valor_ICMS = STR_REPLACE(",", "", $T013_Valor_ICMS);
                $T013_Valor_ICMS = STR_REPEAT('0', 13 - STRLEN($T013_Valor_ICMS)) . $T013_Valor_ICMS;
                
                // isenta nao tributada
                //$T013_Nao_Tributada = number_format($mT013['T013_Valor_Frete'] + $mT013['T013_Valor_Seguro'] + $mT013['T013_Valor_Despesas_Acessorias'],2);
                $T013_Nao_Tributada = STR_REPLACE(".", "", $T013_Nao_Tributada);
                $T013_Nao_Tributada = STR_REPLACE(",", "", $T013_Nao_Tributada);
                $T013_Nao_Tributada = STR_REPEAT('0', 13 - STRLEN($T013_Nao_Tributada)) . $T013_Nao_Tributada;
                
                // outra
                $T013_Outra = STR_REPLACE(".", "", $T013_Outra);
                $T013_Outra = STR_REPLACE(",", "", $T013_Outra);
                $T013_Outra = STR_REPEAT('0', 13 - STRLEN($T013_Outra)) . $T013_Outra;
                
                // Percentual icms
                $T013_Aliquota_ICMS = STR_REPLACE(".", "", $mT013['T014_Aliquota_ICMS']);
                $T013_Aliquota_ICMS = STR_REPLACE(",", "", $T013_Aliquota_ICMS);
                $T013_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T013_Aliquota_ICMS)) . $T013_Aliquota_ICMS;
                
                //NOTA CANCELADA
                $T013_Flag_Cancelada = $mT013['T013_Flag_Cancelada'];
                if (empty($T013_Flag_Cancelada)) {
                    $T013_Flag_Cancelada = "N";
                }
                
                //  table 50
                $cAgrega_Dados .= "50"; //-- 2    1-2     - tipo
                $cAgrega_Dados .= $T013_Cpf_Cnpj; //-- 14   3-16    - T013_Cpf_Cnpj
                $cAgrega_Dados .= $T013_Inscricao_Estadual; //-- 14   17-30   - T013_Inscricao_estadual
                $cAgrega_Dados .= $T013_Data_Emissao; //-- 8    31-38   - data de emissao
                $cAgrega_Dados .= $T013_D024_Estado; //-- 2    39-40   - unidade federativa
                $cAgrega_Dados .= '01'; //-- 2    41-42   - Codigo do modelo fiscal
                $cAgrega_Dados .= '   '; //-- 3    43-45   - Serie nota fiscal
                $cAgrega_Dados .= $T013_Numero_Nota_Fiscal; //-- 6    46-51   - Numero da Nota Fiscal
                $cAgrega_Dados .= $T013_CFOP; //-- 4    52-55   - Numero da Nota Fiscal
                $cAgrega_Dados .= 'T'; //-- 1    56      - Emitente
                $cAgrega_Dados .= $T013_Valor_Total; //-- 13   57-69   - Valor Total
                $cAgrega_Dados .= $T013_Valor_Base_ICMS; //-- 13   70-82   - Valor Base_ICmS
                $cAgrega_Dados .= $T013_Valor_ICMS; //-- 13   83-95   - Valor Base_ICmS
                $cAgrega_Dados .= $T013_Nao_Tributada; //-- 13   96-108  - Valor Isenta ou nao tributada
                $cAgrega_Dados .= $T013_Outra; //-- 13   109-121 - nao confirmada ou nao creditada
                $cAgrega_Dados .= $T013_Aliquota_ICMS; //-- 4    122-125 - nao confirmada ou nao creditada
                $cAgrega_Dados .= $T013_Flag_Cancelada; //-- 4    122-125 - nao confirmada ou nao creditada
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////
            // TIPO 51 - IPI
            // ENTIDADE: T007 NOTA EMITIDA
            /////////////////////////////////////////////////////////////////////
            $cClausula_T007 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Inscricao_Estadual,
		                                  D018_UF,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
		                                  T007_Codigo_CFOP,
		                                  T007_Data_Emissao,
		                                  T007_Valor_Total,
		                                  round(T007_Valor_Total_IPI,2) T007_Valor_Total_IPI,
		                                  T007_Numero_Nota_Fiscal,
		                                  T007_Flag_Cancelada
		                             FROM T007
		                        LEFT JOIN D024 ON D024_Id=T007_D024_Id
		                        LEFT JOIN D018 ON D018_Id=D024_D018_Id
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND (T007_Flag_ACP='1' OR T007_Flag_ACP='6')
		                              AND T007_Flag_Cancelada!='S'
		                              AND T007_Numero_Nota_Fiscal!=''
		                              AND T007_Flag_Inutilizado!='S'
		                              AND T007_Valor_Total_IPI>0
		                         ORDER BY T007_Data_Emissao,T007_Numero_Nota_Fiscal ";
            
            $cT007        = mysql_query($cClausula_T007);
            $rows_51_T007 = mysql_num_rows($cT007);
            while ($mT007 = mysql_fetch_array($cT007)) {
                
                // verifica em que campo cpf ou cnpj
                if ($mT007['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                    //-- Preparando o campo Cnpj
                    $T007_Cpf_Cnpj = $mT007['D024_Cnpj'];
                } else {
                    //--  Preparando o campo Cpf
                    $T007_Cpf_Cnpj = $mT007['D024_Cpf'];
                }
                $T007_Cpf_Cnpj = STR_REPLACE(".", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("/", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE(" ", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("-", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPEAT('0', 14 - STRLEN($T007_Cpf_Cnpj)) . $T007_Cpf_Cnpj;
                $T007_Cpf_Cnpj = SUBSTR($T007_Cpf_Cnpj, 0, 14);
                
                
                // inscricao_estadual
                if (empty($mT007['D024_Inscricao_Estadual'])) {
                    $T007_Inscricao_Estadual = "ISENTO";
                } else {
                    $T007_Inscricao_Estadual = STR_REPLACE(".", "", $mT007['D024_Inscricao_Estadual']);
                    $T007_Inscricao_Estadual = STR_REPLACE("/", "", $T007_Inscricao_Estadual);
                    $T007_Inscricao_Estadual = STR_REPLACE(" ", "", $T007_Inscricao_Estadual);
                    $T007_Inscricao_Estadual = STR_REPLACE("-", "", $T007_Inscricao_Estadual);
                }
                $T007_Inscricao_Estadual .= STR_REPEAT(' ', 14 - STRLEN($T007_Inscricao_Estadual));
                $T007_Inscricao_Estadual = SUBSTR($T007_Inscricao_Estadual, 0, 14);
                
                
                // data  de emissao
                $T007_Data_Emissao = STR_REPLACE("-", "", $mT007['T007_Data_Emissao']);
                
                // uf
                $T007_D024_Estado = SUBSTR($mT007['D018_UF'], 0, 2);
                $T007_D024_Estado .= STR_REPEAT(' ', 2 - STRLEN($T007_D024_Estado));
                
                // numero da nota fiscal
                $T007_Numero_Nota_Fiscal = SUBSTR($mT007['T007_Numero_Nota_Fiscal'], 0, 6);
                $T007_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T007_Numero_Nota_Fiscal)) . $T007_Numero_Nota_Fiscal;
                
                // numero da nota fiscal
                $T007_CFOP = STR_REPLACE(".", "", $mT007['T007_Codigo_CFOP']);
                $T007_CFOP = SUBSTR($T007_CFOP, 0, 4);
                $T007_CFOP = STR_REPEAT('0', 4 - STRLEN($T007_CFOP)) . $T007_CFOP;
                
                // valor total da nota fiscal
                $T007_Valor_Total = STR_REPLACE(".", "", $mT007['T007_Valor_Total']);
                $T007_Valor_Total = STR_REPLACE(",", "", $T007_Valor_Total);
                $T007_Valor_Total = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total)) . $T007_Valor_Total;
                
                // valor ipi
                $T007_Valor_IPI = STR_REPLACE(".", "", $mT007['T007_Valor_Total_IPI']);
                $T007_Valor_IPI = STR_REPLACE(",", "", $T007_Valor_IPI);
                $T007_Valor_IPI = STR_REPEAT('0', 13 - STRLEN($T007_Valor_IPI)) . $T007_Valor_IPI;
                
                // isento ipi   (VERIFICAR)
                $T007_Isento_IPI = STR_REPLACE(".", "", $mT007['T007_Isento_IPI']);
                $T007_Isento_IPI = STR_REPLACE(",", "", $T007_Isento_IPI);
                $T007_Isento_IPI = STR_REPEAT('0', 13 - STRLEN($T007_Isento_IPI)) . $T007_Isento_IPI;
                
                // OUTRO ipi   (VERIFICAR)
                $T007_Outro_IPI = STR_REPLACE(".", "", $mT007['T007_Outro_IPI']);
                $T007_Outro_IPI = STR_REPLACE(",", "", $T007_Outro_IPI);
                $T007_Outro_IPI = STR_REPEAT('0', 13 - STRLEN($T007_Outro_IPI)) . $T007_Outro_IPI;
                
                // BRancos   (VERIFICAR)
                $T007_Brancos = SUBSTR($mT007['T007_Brancos'], 0, 20);
                $T007_Brancos .= STR_REPEAT(' ', 20 - STRLEN($T007_Brancos));
                
                //T007_Flag_Cancelada
                $T007_Flag_Cancelada = $mT007['T007_Flag_Cancelada'];
                if (empty($T007_Flag_Cancelada)) {
                    $T007_Flag_Cancelada = "N";
                }
                
                // table 51
                $cAgrega_Dados .= "51"; //-- 2    1-2
                $cAgrega_Dados .= $T007_Cpf_Cnpj; //-- 14   3-16
                $cAgrega_Dados .= $T007_Inscricao_Estadual; //-- 14   17-30
                $cAgrega_Dados .= $T007_Data_Emissao; //-- 8    31-38
                $cAgrega_Dados .= $T007_D024_Estado; //-- 2    39-40
                $cAgrega_Dados .= '   '; //-- 3    41-43
                $cAgrega_Dados .= $T007_Numero_Nota_Fiscal; //-- 6    44-49
                $cAgrega_Dados .= $T007_CFOP; //-- 4    50-53
                $cAgrega_Dados .= $T007_Valor_Total; //-- 13   54-66
                $cAgrega_Dados .= $T007_Valor_IPI; //-- 13   67-79
                $cAgrega_Dados .= $T007_Isento_IPI; //-- 13   80-92
                $cAgrega_Dados .= $T007_Outro_IPI; //-- 13   93-105
                $cAgrega_Dados .= $T007_Brancos; //-- 20   106-125
                $cAgrega_Dados .= $T007_Flag_Cancelada; //-- 1    126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////
            // TIPO 51 - IPI
            // ENTIDADE: T013 NOTA RECEBIDA
            /////////////////////////////////////////////////////////////////////
            $cClausula_T013 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Inscricao_Estadual,
		                                  D018_UF,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
		                                  D006_Codigo_CFOP,
		                                  T013_Data_Emissao,
		                                  T013_Data_Entrada,
		                                  round(sum(T014_Valor_Total_Custo+if(T013_Flag_Frete='1',T014_Valor_Frete,0)+T014_Valor_Despesas_Acessorias+T014_Valor_IPI-T014_Valor_Total_Desconto),2) T013_Valor_Total,
		                                  sum(T014_Valor_IPI) T013_Valor_Total_IPI,
		                                  T013_Numero_Nota_Fiscal,
		                                  T013_Flag_Cancelada
		                             FROM T013
		                        LEFT JOIN D024 ON D024_Id=T013_D024_Id
		                        LEFT JOIN T014 ON T014_T013_Id=T013_Id
		                        LEFT JOIN D006 ON D006_Id=T014_D006_Id or D006_Id=T013_D006_Id
		                        LEFT JOIN D018 ON D018_Id=D024_D018_Id
		                            WHERE T013_C004_Id='{$g['empresaAtual']}'
		                              AND T013_Data_Entrada >= '$Data_Inicio'
		                              AND T013_Data_Entrada <= '$Data_Fim'
		                              AND T013_Numero_Nota_Fiscal not LIKE '%A%'
		                              AND FLOOR(T013_Numero_Nota_Fiscal)>0
		                              AND T013_Valor_Total_IPI>0
		                              AND ifnull(T013_Flag_Cancelada,'N')!='S'
		                              AND ifnull(T013_Flag_Travar_Nota,'N')='S'
		                         GROUP BY T013_Id,D006_Codigo_CFOP
		                         ORDER BY T013_Data_Entrada,
		                                  T013_Numero_Nota_Fiscal,
		                                  T013_Id";
            
            $cT013        = mysql_query($cClausula_T013);
            $rows_51_T013 = mysql_num_rows($cT013);
            $erro         = mysql_error();
            //echo "$erro";
            
            while ($mT013 = mysql_fetch_array($cT013)) {
                
                // verifica em que campo cpf ou cnpj
                if ($mT013['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                    //-- Preparando o campo Cnpj
                    $T013_Cpf_Cnpj = $mT013['D024_Cnpj'];
                } else {
                    //--  Preparando o campo Cpf
                    $T013_Cpf_Cnpj = $mT013['D024_Cpf'];
                }
                $T013_Cpf_Cnpj = STR_REPLACE(".", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE("/", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE(" ", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE("-", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPEAT('0', 14 - STRLEN($T013_Cpf_Cnpj)) . $T013_Cpf_Cnpj;
                $T013_Cpf_Cnpj = SUBSTR($T013_Cpf_Cnpj, 0, 14);
                
                // inscricao_estadual
                if (empty($mT013['D024_Inscricao_Estadual'])) {
                    $T013_Inscricao_Estadual = "ISENTO";
                } else {
                    $T013_Inscricao_Estadual = STR_REPLACE(".", "", $mT013['D024_Inscricao_Estadual']);
                    $T013_Inscricao_Estadual = STR_REPLACE("/", "", $T013_Inscricao_Estadual);
                    $T013_Inscricao_Estadual = STR_REPLACE(" ", "", $T013_Inscricao_Estadual);
                    $T013_Inscricao_Estadual = STR_REPLACE("-", "", $T013_Inscricao_Estadual);
                }
                $T013_Inscricao_Estadual .= STR_REPEAT(' ', 14 - STRLEN($T013_Inscricao_Estadual));
                $T013_Inscricao_Estadual = SUBSTR($T013_Inscricao_Estadual, 0, 14);
                
                // data  de emissao
                $T013_Data_Emissao = STR_REPLACE("-", "", $mT013['T013_Data_Entrada']);
                
                // uf
                $T013_D024_Estado = SUBSTR($mT013['D018_UF'], 0, 2);
                $T013_D024_Estado .= STR_REPEAT(' ', 2 - STRLEN($T013_D024_Estado));
                
                // numero da nota fiscal
                $T013_Numero_Nota_Fiscal = SUBSTR($mT013['T013_Numero_Nota_Fiscal'], 0, 6);
                $T013_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T013_Numero_Nota_Fiscal)) . $T013_Numero_Nota_Fiscal;
                
                // numero da nota fiscal
                $T013_CFOP = STR_REPLACE(".", "", $mT013['D006_Codigo_CFOP']);
                $T013_CFOP = SUBSTR($T013_CFOP, 0, 4);
                $T013_CFOP = STR_REPEAT('0', 4 - STRLEN($T013_CFOP)) . $T013_CFOP;
                
                // valor total da nota fiscal
                $T013_Valor_Total = STR_REPLACE(".", "", $mT013['T013_Valor_Total']);
                $T013_Valor_Total = STR_REPLACE(",", "", $T013_Valor_Total);
                $T013_Valor_Total = STR_REPEAT('0', 13 - STRLEN($T013_Valor_Total)) . $T013_Valor_Total;
                
                // valor ipi
                $T013_Valor_Total_IPI = STR_REPLACE(".", "", $mT013['T013_Valor_Total_IPI']);
                $T013_Valor_Total_IPI = STR_REPLACE(",", "", $T013_Valor_Total_IPI);
                $T013_Valor_Total_IPI = STR_REPEAT('0', 13 - STRLEN($T013_Valor_Total_IPI)) . $T013_Valor_Total_IPI;
                
                // isento ipi   (VERIFICAR)
                $T013_Isento_IPI = STR_REPLACE(".", "", $mT013['T013_Isento_IPI']);
                $T013_Isento_IPI = STR_REPLACE(",", "", $T013_Isento_IPI);
                $T013_Isento_IPI = STR_REPEAT('0', 13 - STRLEN($T013_Isento_IPI)) . $T013_Isento_IPI;
                
                // OUTRO ipi   (VERIFICAR)
                $T013_Outro_IPI = STR_REPLACE(".", "", $mT013['T013_Outro_IPI']);
                $T013_Outro_IPI = STR_REPLACE(",", "", $T013_Outro_IPI);
                $T013_Outro_IPI = STR_REPEAT('0', 13 - STRLEN($T013_Outro_IPI)) . $T013_Outro_IPI;
                
                // BRancos   (VERIFICAR)
                $T013_Brancos = SUBSTR($mT013['T013_Brancos'], 0, 20);
                $T013_Brancos .= STR_REPEAT(' ', 20 - STRLEN($T013_Brancos));
                
                //NOTA CANCELADA
                $T013_Flag_Cancelada = $mT013['T013_Flag_Cancelada'];
                if (empty($T013_Flag_Cancelada)) {
                    $T013_Flag_Cancelada = "N";
                }
                
                // tabela 51
                $cAgrega_Dados .= "51"; //-- 2    1-2
                $cAgrega_Dados .= $T013_Cpf_Cnpj; //-- 14   3-16
                $cAgrega_Dados .= $T013_Inscricao_Estadual; //-- 14   17-30
                $cAgrega_Dados .= $T013_Data_Emissao; //-- 8    31-38
                $cAgrega_Dados .= $T013_D024_Estado; //-- 2    39-40
                $cAgrega_Dados .= '   '; //-- 3    41-43
                $cAgrega_Dados .= $T013_Numero_Nota_Fiscal; //-- 6    44-49
                $cAgrega_Dados .= $T013_CFOP; //-- 4    50-53
                $cAgrega_Dados .= $T013_Valor_Total; //-- 13   54-66
                $cAgrega_Dados .= $T013_Valor_Total_IPI; //-- 13   67-79
                $cAgrega_Dados .= $T013_Isento_IPI; //-- 13   80-92
                $cAgrega_Dados .= $T013_Outro_IPI; //-- 13   93-105
                $cAgrega_Dados .= $T013_Brancos; //-- 20   106-125
                $cAgrega_Dados .= $T013_Flag_Cancelada; //-- 1    126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////
            // TIPO 53 - SUBSTITUICAO TRIBUTARIA
            // ENTIDADE: T007 NOTA EMITIDA
            /////////////////////////////////////////////////////////////////////
            $cSql_T007 = mysql_query("SELECT T007_Id
		                                  FROM T007
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND (T007_Flag_ACP='1' OR T007_Flag_ACP='6')
		                              AND T007_Flag_Cancelada!='S'
		                              AND T007_Valor_ICMS_Substituicao > 0
		                              AND T007_Valor_Total_Produtos=0
		                              AND T007_Numero_Nota_Fiscal!=''
                                      AND T007_Flag_Finalidade_NFe != '2'
		                         ORDER BY T007_Data_Emissao,T007_Numero_Nota_Fiscal ");
            if (mysql_num_rows($cSql_T007) > 0) {
                return "Existem notas sem totalização dos produtos e com valor de ICMS de substituição";
            }
            $cClausula_T007 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Inscricao_Estadual,
		                                  D018_UF,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
                                          T005_Flag_ACP,
		                                  T007_Cliente_CNPJ_CPF,
		                                  T007_Codigo_CFOP,
		                                  T007_Data_Emissao,
		                                  T007_Valor_Total_Produtos,
		                                  T007_Valor_ICMS_Substituicao,
		                                  T007_Valor_Frete,
		                                  T007_Valor_Despesas_Acessorias,
		                                  T007_Numero_Nota_Fiscal,
		                                  T007_Flag_Cancelada
		                             FROM T007
		                        LEFT JOIN D024 ON D024_Id=T007_D024_Id
		                        LEFT JOIN D018 ON D018_Id=D024_D018_Id
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND (T007_Flag_ACP='1' OR T007_Flag_ACP='6')
		                              AND T007_Valor_ICMS_Substituicao > 0
		                              AND T007_Flag_Cancelada!='S'
		                              AND T007_Flag_Inutilizado!='S'
		                              AND T007_Numero_Nota_Fiscal!=''
                                      AND T007_Flag_Finalidade_NFe != '2'
		                         ORDER BY T007_Data_Emissao,T007_Numero_Nota_Fiscal limit 1 ";
            
            //                              AND T007_Valor_ICMS_Substituicao > 0
            
            $cT007        = mysql_query($cClausula_T007);
            $rows_53_T007 = mysql_num_rows($cT007);
            while ($mT007 = mysql_fetch_array($cT007)) {
                
                if (!empty($mT007['T007_Cliente_CNPJ_CPF'])) {
                    $T007_Cpf_Cnpj = $mT007['T007_Cliente_CNPJ_CPF'];
                } else {
                    // verifica em que campo cpf ou cnpj
                    if ($mT007['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                        //-- Preparando o campo Cnpj
                        $T007_Cpf_Cnpj = $mT007['D024_Cnpj'];
                    } else {
                        //--  Preparando o campo Cpf
                        $T007_Cpf_Cnpj = $mT007['D024_Cpf'];
                    }
                }
                $T007_Cpf_Cnpj = STR_REPLACE(".", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("/", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE(" ", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("-", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_PAD($T007_Cpf_Cnpj, 14, "0", STR_PAD_LEFT);
                
                // inscricao_estadual
                if (empty($mT007['D024_Inscricao_Estadual'])) {
                    $T007_Inscricao_Estadual = "ISENTO";
                } else {
                    $T007_Inscricao_Estadual = STR_REPLACE(".", "", $mT007['D024_Inscricao_Estadual']);
                    $T007_Inscricao_Estadual = STR_REPLACE("/", "", $T007_Inscricao_Estadual);
                    $T007_Inscricao_Estadual = STR_REPLACE(" ", "", $T007_Inscricao_Estadual);
                    $T007_Inscricao_Estadual = STR_REPLACE("-", "", $T007_Inscricao_Estadual);
                }
                $T007_Inscricao_Estadual .= STR_REPEAT(' ', 14 - STRLEN($T007_Inscricao_Estadual));
                
                // data  de emissao
                $T007_Data_Emissao = STR_REPLACE("-", "", $mT007['T007_Data_Emissao']);
                
                // uf
                $T007_D024_Estado = SUBSTR($mT007['D018_UF'], 0, 2);
                $T007_D024_Estado .= STR_REPEAT(' ', 2 - STRLEN($T007_D024_Estado));
                
                // numero da nota fiscal
                $T007_Numero_Nota_Fiscal = SUBSTR($mT007['T007_Numero_Nota_Fiscal'], 0, 6);
                $T007_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T007_Numero_Nota_Fiscal)) . $T007_Numero_Nota_Fiscal;
                
                // numero CFOP
                $T007_CFOP = STR_REPLACE(".", "", $mT007['T007_Codigo_CFOP']);
                $T007_CFOP = SUBSTR($T007_CFOP, 0, 4);
                $T007_CFOP = STR_REPEAT('0', 4 - STRLEN($T007_CFOP)) . $T007_CFOP;
                
                // valor total da nota fiscal
                $T007_Valor_Total = STR_REPLACE(".", "", $mT007['T007_Valor_Total']);
                $T007_Valor_Total = STR_REPLACE(",", "", $T007_Valor_Total);
                $T007_Valor_Total = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total)) . $T007_Valor_Total;
                
                // T007_Valor_Total_Produtos
                $T007_Valor_Total_Produtos = STR_REPLACE(".", "", $mT007['T007_Valor_Total_Produtos']);
                $T007_Valor_Total_Produtos = STR_REPLACE(",", "", $T007_Valor_Total_Produtos);
                $T007_Valor_Total_Produtos = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total_Produtos)) . $T007_Valor_Total_Produtos;
                
                // ICMS
                $T007_Valor_ICMS_Substituicao = STR_REPLACE(".", "", $mT007['T007_Valor_ICMS_Substituicao']);
                $T007_Valor_ICMS_Substituicao = STR_REPLACE(",", "", $T007_Valor_ICMS_Substituicao);
                $T007_Valor_ICMS_Substituicao = STR_REPEAT('0', 13 - STRLEN($T007_Valor_ICMS_Substituicao)) . $T007_Valor_ICMS_Substituicao;
                
                // Despesas Acessorias
                $T007_Despesas_Acessorias = number_format($mT007['T007_Valor_Frete'] + $mT007['T007_Valor_Despesas_Acessorios'], 2);
                $T007_Despesas_Acessorias = STR_REPLACE(".", "", $T007_Despesas_Acessorias);
                $T007_Despesas_Acessorias = STR_REPLACE(",", "", $T007_Despesas_Acessorias);
                $T007_Despesas_Acessorias = STR_REPEAT('0', 13 - STRLEN($T007_Despesas_Acessorias)) . $T007_Despesas_Acessorias;
                
                //T007_Flag_Cancelada
                $T007_Flag_Cancelada = $mT007['T007_Flag_Cancelada'];
                if (empty($T007_Flag_Cancelada)) {
                    $T007_Flag_Cancelada = "N";
                }
                
                // brancos
                $T007_Brancos = SUBSTR($T007_Brancos, 0, 2);
                $T007_Brancos .= STR_REPEAT(' ', 29 - STRLEN($T007_Brancos));
                
                //table 53
                $cAgrega_Dados .= "53"; //-- 2    1-2
                $cAgrega_Dados .= $T007_Cpf_Cnpj; //-- 14   3-16
                $cAgrega_Dados .= $T007_Inscricao_Estadual; //-- 14   17-30
                $cAgrega_Dados .= $T007_Data_Emissao; //-- 8    31-38
                $cAgrega_Dados .= $T007_D024_Estado; //-- 2    39-40
                if ($mT007['T007_Flag_ACP'] == '6') {
                    $cAgrega_Dados .= '55'; //-- 2    41-42   - Codigo do modelo fiscal
                } else {
                    $cAgrega_Dados .= '01'; //-- 2    41-42   - Codigo do modelo fiscal
                }
                $cAgrega_Dados .= '001'; //-- 3    43-45   - Serie nota fiscal
                $cAgrega_Dados .= $T007_Numero_Nota_Fiscal; //-- 6    46-51
                $cAgrega_Dados .= $T007_CFOP; //-- 4    52-55
                $cAgrega_Dados .= 'P'; //-- 1    56-56
                $cAgrega_Dados .= $T007_Valor_Total_Produtos; //-- 13   57-69
                $cAgrega_Dados .= $T007_Valor_ICMS_Substituicao; //"0000000000001";             //-- 13   70-82
                $cAgrega_Dados .= $T007_Despesas_Acessorias; //-- 13   82-95
                $cAgrega_Dados .= $T007_Flag_Cancelada; //-- 1    96
                $cAgrega_Dados .= ' '; //-- 1    97
                $cAgrega_Dados .= $T007_Brancos; //-- 29 98-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////
            // TIPO 53 - SUBSTITUICAO TRIBUTARIA
            // ENTIDADE: T013 NOTA DE COMPRA
            /////////////////////////////////////////////////////////////////////
            
            $cClausula_T013 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Inscricao_Estadual,
		                                  D018_UF,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
		                                  D006_Codigo_CFOP,
		                                  T013_Data_Entrada,
		                                  T013_Valor_Total_Produtos,
		                                  T013_Valor_Total,
		                                  T013_Valor_ICMS_Substituicao,
		                                  T013_Valor_Frete,
		                                  T013_Valor_Seguro,
		                                  T013_Numero_Nota_Fiscal,
		                                  T013_Valor_Despesas_Acessorias,
		                                  T013_Flag_Cancelada
		                             FROM T013
		                        LEFT JOIN D006 ON D006_Id=T013_D006_Id
		                        LEFT JOIN D024 ON D024_Id=T013_D024_Id
		                        LEFT JOIN D020 ON D020_Id=D024_D020_Id
		                        LEFT JOIN D018 ON D018_Id=D020_D018_Id
		                            WHERE T013_C004_Id='{$g['empresaAtual']}'
		                              AND T013_Data_Entrada >= '$Data_Inicio'
		                              AND T013_Data_Entrada <= '$Data_Fim'
		                              AND T013_Numero_Nota_Fiscal not LIKE '%A%'
		                              AND FLOOR(T013_Numero_Nota_Fiscal)>0
		                              AND T013_Valor_ICMS_Substituicao>0
		                              AND ifnull(T013_Flag_Cancelada,'N')!='S'
		                              AND ifnull(T013_Flag_Travar_Nota,'N')='S'
		                         ORDER BY T013_Data_Entrada,
		                                  T013_Numero_Nota_Fiscal,
		                                  T013_Id";
            
            $cT013        = mysql_query($cClausula_T013);
            $rows_53_T013 = mysql_num_rows($cT013);
            while ($mT013 = mysql_fetch_array($cT013)) {
                
                // verifica em que campo cpf ou cnpj
                if ($mT013['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                    //-- Preparando o campo Cnpj
                    $T013_Cpf_Cnpj = STR_REPLACE(".", "", $mT013['D024_Cnpj']);
                } else {
                    //--  Preparando o campo Cpf
                    $T013_Cpf_Cnpj = STR_REPLACE(".", "", $mT013['D024_Cpf']);
                }
                $T013_Cpf_Cnpj = STR_REPLACE("/", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE(" ", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE("-", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPEAT('0', 14 - STRLEN($T013_Cpf_Cnpj)) . $T013_Cpf_Cnpj;
                
                // inscricao_estadual
                if (empty($mT013['D024_Inscricao_Estadual'])) {
                    $T013_Inscricao_Estadual = "ISENTO";
                } else {
                    $T013_Inscricao_Estadual = STR_REPLACE(".", "", $mT013['D024_Inscricao_Estadual']);
                    $T013_Inscricao_Estadual = STR_REPLACE("/", "", $T013_Inscricao_Estadual);
                    $T013_Inscricao_Estadual = STR_REPLACE(" ", "", $T013_Inscricao_Estadual);
                    $T013_Inscricao_Estadual = STR_REPLACE("-", "", $T013_Inscricao_Estadual);
                }
                $T013_Inscricao_Estadual .= STR_REPEAT(' ', 14 - STRLEN($T013_Inscricao_Estadual));
                
                // T013_Data_Entrada
                $T013_Data_Entrada = STR_REPLACE("-", "", $mT013['T013_Data_Entrada']);
                
                // uf
                $T013_D024_Estado = SUBSTR($mT013['D018_UF'], 0, 2);
                $T013_D024_Estado .= STR_REPEAT(' ', 2 - STRLEN($T013_D024_Estado));
                
                // numero da nota fiscal
                $T013_Numero_Nota_Fiscal = SUBSTR($mT013['T013_Numero_Nota_Fiscal'], 0, 6);
                $T013_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T013_Numero_Nota_Fiscal)) . $T013_Numero_Nota_Fiscal;
                
                // numero CFOP
                $T013_CFOP = STR_REPLACE(".", "", $mT013['D006_Codigo_CFOP']);
                $T013_CFOP = SUBSTR($T013_CFOP, 0, 4);
                $T013_CFOP = STR_REPEAT('0', 4 - STRLEN($T013_CFOP)) . $T013_CFOP;
                
                // valor total da nota fiscal
                $T013_Valor_Total = STR_REPLACE(".", "", $mT013['T013_Valor_Total']);
                $T013_Valor_Total = STR_REPLACE(",", "", $T013_Valor_Total);
                $T013_Valor_Total = STR_REPEAT('0', 13 - STRLEN($T013_Valor_Total)) . $T013_Valor_Total;
                
                // T007_Valor_Total_Produtos
                $T013_Valor_Total_Produtos = STR_REPLACE(".", "", $mT013['T013_Valor_Total_Produtos']);
                $T013_Valor_Total_Produtos = STR_REPLACE(",", "", $T013_Valor_Total_Produtos);
                $T013_Valor_Total_Produtos = STR_REPEAT('0', 13 - STRLEN($T013_Valor_Total_Produtos)) . $T013_Valor_Total_Produtos;
                
                // ICMS
                $T013_Valor_ICMS_Substituicao = STR_REPLACE(".", "", $mT013['T013_Valor_ICMS_Substituicao']);
                $T013_Valor_ICMS_Substituicao = STR_REPLACE(",", "", $T013_Valor_ICMS_Substituicao);
                $T013_Valor_ICMS_Substituicao = STR_REPEAT('0', 13 - STRLEN($T013_Valor_ICMS_Substituicao)) . $T013_Valor_ICMS_Substituicao;
                
                // Despesas Acessorias
                $T013_Despesas_Acessorias = number_format($mT013['T013_Valor_Frete'] + $mT013['T013_Valor_Despesas_Acessorias'], 2);
                $T013_Despesas_Acessorias = STR_REPLACE(".", "", $T013_Despesas_Acessorias);
                $T013_Despesas_Acessorias = STR_REPLACE(",", "", $T013_Despesas_Acessorias);
                $T013_Despesas_Acessorias = STR_REPEAT('0', 13 - STRLEN($T013_Despesas_Acessorias)) . $T013_Despesas_Acessorias;
                
                //NOTA CANCELADA
                $T013_Flag_Cancelada = $mT013['T013_Flag_Cancelada'];
                if (empty($T013_Flag_Cancelada)) {
                    $T013_Flag_Cancelada = "N";
                }
                
                // brancos
                $T013_Brancos = SUBSTR($T013_Brancos, 0, 2);
                $T013_Brancos .= STR_REPEAT(' ', 29 - STRLEN($T013_Brancos));
                
                //tabela 53
                $cAgrega_Dados .= "53"; //-- 2    1-2
                $cAgrega_Dados .= $T013_Cpf_Cnpj; //-- 14   3-16
                $cAgrega_Dados .= $T013_Inscricao_Estadual; //-- 14   17-30
                $cAgrega_Dados .= $T013_Data_Entrada; //-- 8    31-38
                $cAgrega_Dados .= $T013_D024_Estado; //-- 2    39-40
                $cAgrega_Dados .= '01'; //-- 2    41-42
                $cAgrega_Dados .= '   '; //-- 3    43-45
                $cAgrega_Dados .= $T013_Numero_Nota_Fiscal; //-- 6    46-51
                $cAgrega_Dados .= $T013_CFOP; //-- 4    52-55
                $cAgrega_Dados .= 'T'; //-- 1    56-56
                $cAgrega_Dados .= $T013_Valor_Total_Produtos; //-- 13   57-69
                $cAgrega_Dados .= $T013_Valor_ICMS_Substituicao; //-- 13   70-82
                $cAgrega_Dados .= $T013_Despesas_Acessorias; //-- 13   82-95
                $cAgrega_Dados .= 'N'; //-- 1    96
                $cAgrega_Dados .= ' '; //-- 1    97
                $cAgrega_Dados .= $T013_Brancos; //-- 29 98-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 54 REGISTRO DO PRODUTO (CLASSIFICACAO FISCAL)
            // ENTIDADE T007 NOTA EMITIDA
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula_T007 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
		                                  T007_Cliente_CNPJ_CPF,
		                                  D006_Codigo_CFOP,
		                                  T007_Data_Emissao,
		                                  T007_Flag_ACP,
		                                  T007_Numero_Nota_Fiscal,
		                                  T008_Codigo_Produto,
		                                  T008_Quantidade,
		                                  round(T008_Valor_Total_Preco-T008_Valor_Total_Desconto,2) as T008_Valor_Total_Preco,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(T008_Valor_Base_ICMS,2)) as T008_Valor_Base_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(T008_Valor_IPI,2)) as T008_Valor_IPI,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(T008_Aliquota_ICMS,2)) as T008_Aliquota_ICMS
		                             FROM T007
		                        LEFT JOIN D024 ON D024_Id=T007_D024_Id
		                        LEFT JOIN T008 ON T008_T007_Id=T007_Id
		                        LEFT JOIN D006 ON D006_Id=T008_D006_Id
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND (T007_Flag_ACP='1' OR T007_Flag_ACP='6')
		                              AND T007_Numero_Nota_Fiscal!=''
		                              AND T007_Flag_Cancelada!='S'
		                              AND T008_Codigo_Produto!=''
		                              AND T007_Flag_Inutilizado!='S'
		                              AND T008_Descricao_Produto!=''
		                         ORDER BY T007_Data_Emissao,T007_Numero_Nota_Fiscal ";
            
            
            $cT007        = mysql_query($cClausula_T007);
            $rows_54_T007 = mysql_num_rows($cT007);
            $erro         = mysql_error();
            //echo "$erro";
            
            
            while ($mT007 = mysql_fetch_array($cT007)) {
                
                
                if (!empty($mT007['T007_Cliente_CNPJ_CPF'])) {
                    $T007_Cpf_Cnpj = $mT007['T007_Cliente_CNPJ_CPF'];
                } else {
                    // verifica em que campo cpf ou cnpj
                    if ($mT007['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                        //-- Preparando o campo Cnpj
                        $T007_Cpf_Cnpj = $mT007['D024_Cnpj'];
                    } else {
                        //--  Preparando o campo Cpf
                        $T007_Cpf_Cnpj = $mT007['D024_Cpf'];
                    }
                }
                $T007_Cpf_Cnpj = STR_REPLACE(".", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("/", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE(" ", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_REPLACE("-", "", $T007_Cpf_Cnpj);
                $T007_Cpf_Cnpj = STR_PAD($T007_Cpf_Cnpj, 14, "0", STR_PAD_LEFT);
                
                // numero da nota fiscal
                $T007_Numero_Nota_Fiscal = SUBSTR($mT007['T007_Numero_Nota_Fiscal'], 0, 6);
                $T007_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T007_Numero_Nota_Fiscal)) . $T007_Numero_Nota_Fiscal;
                
                // numero CFOP
                $T007_CFOP = STR_REPLACE(".", "", $mT007['D006_Codigo_CFOP']);
                $T007_CFOP = SUBSTR($T007_CFOP, 0, 4);
                $T007_CFOP = STR_REPEAT('0', 4 - STRLEN($T007_CFOP)) . $T007_CFOP;
                
                //contador de itens da nota
                if (empty($mT007['T007_Numero_Nota_Fiscal'])) {
                    $nota = $mT007['T007_Numero_Nota_Fiscal'];
                }
                if ($nota == $mT007['T007_Numero_Nota_Fiscal']) {
                    $cont++;
                } else {
                    $nota = $mT007['T007_Numero_Nota_Fiscal'];
                    $cont = 1;
                }
                $linha = $cont;
                $linha = STR_REPEAT('0', 3 - STRLEN($linha)) . $linha;
                
                // Codigo do Produto
                $T007_Codigo_Produto = SUBSTR($mT007['T008_Codigo_Produto'], 0, 14);
                $T007_Codigo_Produto = STR_PAD($T007_Codigo_Produto, 14, "0", STR_PAD_LEFT);
                $T007_Codigo_Produto = $T007_Codigo_Produto;
                
                // Quantidade do produto
                $T007_Quantidade_Produto = STR_REPLACE(".", "", $mT007['T008_Quantidade']);
                $T007_Quantidade_Produto = STR_REPEAT('0', 11 - STRLEN($T007_Quantidade_Produto)) . $T007_Quantidade_Produto;
                
                // Valor do Produto
                $T007_Valor_Produto = STR_REPLACE(".", "", $mT007['T008_Valor_Total_Preco']);
                $T007_Valor_Produto = STR_REPLACE(",", "", $T007_Valor_Produto);
                $T007_Valor_Produto = STR_REPEAT('0', 12 - STRLEN($T007_Valor_Produto)) . $T007_Valor_Produto;
                
                // Valor do Desconto
                $T007_Valor_Desconto = STR_REPLACE(".", "", $T007_Valor_Desconto);
                $T007_Valor_Desconto = STR_REPEAT('0', 12 - STRLEN($T007_Valor_Desconto)) . $T007_Valor_Desconto;
                
                // Valor T008_Valor_Base_ICMS
                $T007_Valor_Total_Base_ICMS = STR_REPLACE(".", "", $mT007['T008_Valor_Base_ICMS']);
                $T007_Valor_Total_Base_ICMS = STR_REPEAT('0', 12 - STRLEN($T007_Valor_Total_Base_ICMS)) . $T007_Valor_Total_Base_ICMS;
                
                // Calculo do ICMS
                $T007_Calculo_ICMS = STR_REPLACE(".", "", $T007_Calculo_ICMS);
                $T007_Calculo_ICMS = STR_REPEAT('0', 12 - STRLEN($T007_Calculo_ICMS)) . $T007_Calculo_ICMS;
                
                //T008_Valor_ICMS
                $T007_Valor_IPI = STR_REPLACE(".", "", $mT007['T008_Valor_IPI']);
                $T007_Valor_IPI = STR_REPEAT('0', 12 - STRLEN($T007_Valor_IPI)) . $T007_Valor_IPI;
                
                //T008_Aliquota_ICMS
                $T007_Aliquota_ICMS = STR_REPLACE(".", "", $mT007['T008_Aliquota_ICMS']);
                $T007_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T007_Aliquota_ICMS)) . $T007_Aliquota_ICMS;
                
                //table 54
                $cAgrega_Dados .= "54"; //-- 2    1-2
                $cAgrega_Dados .= $T007_Cpf_Cnpj; //-- 14   3-16
                if ($mT007['T007_Flag_ACP'] == '6') {
                    $cAgrega_Dados .= '55'; //-- 2    17-18
                } else {
                    $cAgrega_Dados .= '01'; //-- 2    17-18
                }
                $cAgrega_Dados .= '001'; //-- 3    19-21
                $cAgrega_Dados .= $T007_Numero_Nota_Fiscal; //-- 6    22-27
                $cAgrega_Dados .= $T007_CFOP; //-- 4    28-31
                $cAgrega_Dados .= '000'; //-- 3    32-34
                $cAgrega_Dados .= $linha; //-- 3    35-37
                $cAgrega_Dados .= $T007_Codigo_Produto; //-- 4    38-51
                $cAgrega_Dados .= $T007_Quantidade_Produto; //-- 11   52-62
                $cAgrega_Dados .= $T007_Valor_Produto; //-- 12   63-74
                $cAgrega_Dados .= $T007_Valor_Desconto; //-- 12   75-86
                $cAgrega_Dados .= $T007_Valor_Total_Base_ICMS; //-- 12   87-98
                $cAgrega_Dados .= $T007_Calculo_ICMS; //-- 12   99-110
                $cAgrega_Dados .= $T007_Valor_IPI; //-- 12   111-122
                $cAgrega_Dados .= $T007_Aliquota_ICMS; //-- 4  123-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 54 REGISTRO DO PRODUTO (CLASSIFICACAO FISCAL)
            // ENTIDADE T013 NOTA RECEBIDA
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula_T013 = "SELECT D024_Cnpj,
		                                  D024_Cpf,
		                                  D024_Flag_Pessoa_Fisica_Juridica,
		                                  D006_Codigo_CFOP,
		                                  T013_Data_Emissao,
		                                  T013_Numero_Nota_Fiscal,
		                                  T014_Codigo_Produto,
		                                  T014_Quantidade,
		                                  T014_Valor_Total_Desconto,
		                                  T014_Valor_Total_Custo,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(T014_Valor_Base_ICMS,2)) as T014_Valor_Base_ICMS,
		                                  T014_Valor_IPI,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(T014_Aliquota_ICMS,2)) as T014_Aliquota_ICMS
		                             FROM T013
		                        LEFT JOIN D024 ON D024_Id=T013_D024_Id
		                        LEFT JOIN T014 ON T014_T013_Id=T013_Id
		                        LEFT JOIN D006 ON D006_Id=T014_D006_Id or D006_Id=T013_D006_Id
		                            WHERE T013_C004_Id='{$g['empresaAtual']}'
		                              AND T013_Data_Entrada >= '$Data_Inicio'
		                              AND T013_Data_Entrada <= '$Data_Fim'
		                              AND T013_Numero_Nota_Fiscal not LIKE '%A%'
		                              AND FLOOR(T013_Numero_Nota_Fiscal)>0
		                              AND T014_Codigo_Produto!=''
		                              AND T014_Descricao_Produto!=''
		                              AND ifnull(T013_Flag_Cancelada,'N')!='S'
		                              AND ifnull(T013_Flag_Travar_Nota,'N')='S'
		                         ORDER BY T013_Numero_Nota_Fiscal";
            
            
            $cT013        = mysql_query($cClausula_T013);
            $rows_54_T013 = mysql_num_rows($cT013);
            $erro         = mysql_error();
            //echo "$erro";
            
            
            while ($mT013 = mysql_fetch_array($cT013)) {
                
                // verifica em que campo cpf ou cnpj
                if ($mT013['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                    //-- Preparando o campo Cnpj
                    $T013_Cpf_Cnpj = $mT013['D024_Cnpj'];
                } else {
                    //--  Preparando o campo Cpf
                    $T013_Cpf_Cnpj = $mT013['D024_Cpf'];
                }
                $T013_Cpf_Cnpj = STR_REPLACE(".", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE("/", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE(" ", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPLACE("-", "", $T013_Cpf_Cnpj);
                $T013_Cpf_Cnpj = STR_REPEAT('0', 14 - STRLEN($T013_Cpf_Cnpj)) . $T013_Cpf_Cnpj;
                
                // numero da nota fiscal
                $T013_Numero_Nota_Fiscal = SUBSTR($mT013['T013_Numero_Nota_Fiscal'], 0, 6);
                $T013_Numero_Nota_Fiscal = STR_REPEAT('0', 6 - STRLEN($T013_Numero_Nota_Fiscal)) . $T013_Numero_Nota_Fiscal;
                
                // numero CFOP
                $T013_CFOP = STR_REPLACE(".", "", $mT013['D006_Codigo_CFOP']);
                $T013_CFOP = SUBSTR($T013_CFOP, 0, 4);
                $T013_CFOP = STR_REPEAT('0', 4 - STRLEN($T013_CFOP)) . $T013_CFOP;
                
                //contador de itens da nota
                if (empty($mT013['T013_Numero_Nota_Fiscal'])) {
                    $nota = $mT013['T013_Numero_Nota_Fiscal'];
                }
                if ($nota == $mT013['T013_Numero_Nota_Fiscal']) {
                    $cont++;
                } else {
                    $nota = $mT013['T013_Numero_Nota_Fiscal'];
                    $cont = 1;
                }
                $linha = $cont;
                $linha = STR_REPEAT('0', 3 - STRLEN($linha)) . $linha;
                
                // Codigo do Produto
                $T013_Codigo_Produto = SUBSTR($mT013['T014_Codigo_Produto'], 0, 14);
                $T013_Codigo_Produto = STR_PAD($T013_Codigo_Produto, 14, "0", STR_PAD_LEFT);
                $T013_Codigo_Produto = $T013_Codigo_Produto;
                
                // Quantidade do produto
                $T013_Quantidade_Produto = STR_REPLACE(".", "", $mT013['T014_Quantidade']);
                $T013_Quantidade_Produto = STR_REPEAT('0', 11 - STRLEN($T013_Quantidade_Produto)) . $T013_Quantidade_Produto;
                
                // Valor do Produto
                $T013_Valor_Produto = number_format($mT013['T014_Valor_Total_Custo'], 2);
                $T013_Valor_Produto = STR_REPLACE(".", "", $T013_Valor_Produto);
                $T013_Valor_Produto = STR_REPLACE(",", "", $T013_Valor_Produto);
                $T013_Valor_Produto = STR_REPEAT('0', 12 - STRLEN($T013_Valor_Produto)) . $T013_Valor_Produto;
                
                // Valor do Desconto
                $T013_Valor_Desconto = STR_REPLACE(".", "", $mT013['T014_Valor_Total_Desconto']);
                $T013_Valor_Desconto = STR_REPEAT('0', 12 - STRLEN($T013_Valor_Desconto)) . $T013_Valor_Desconto;
                
                // Valor T014_Valor_Base_ICMS
                $T013_Valor_Base_ICMS = STR_REPLACE(".", "", $mT013['T014_Valor_Base_ICMS']);
                $T013_Valor_Base_ICMS = STR_REPEAT('0', 12 - STRLEN($T013_Valor_Base_ICMS)) . $T013_Valor_Base_ICMS;
                
                // Base de Calculo do ICMS para Substituicao tributaria
                $T013_Valor_Base_ICMS_Substituicao = STR_REPLACE(".", "", 0);
                $T013_Valor_Base_ICMS_Substituicao = STR_REPEAT('0', 12 - STRLEN($T013_Valor_Base_ICMS_Substituicao)) . $T013_Valor_Base_ICMS_Substituicao;
                
                //T014_Valor_ICMS
                $T013_Valor_Total_IPI = STR_REPLACE(".", "", $mT013['T014_Valor_IPI']);
                $T013_Valor_Total_IPI = STR_REPEAT('0', 12 - STRLEN($T013_Valor_Total_IPI)) . $T013_Valor_Total_IPI;
                
                //T014_Aliquota_ICMS
                $T013_Aliquota_ICMS = STR_REPLACE(".", "", $mT013['T014_Aliquota_ICMS']);
                $T013_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T013_Aliquota_ICMS)) . $T013_Aliquota_ICMS;
                
                //table 54
                $cAgrega_Dados .= "54"; //-- 2    1-2
                $cAgrega_Dados .= $T013_Cpf_Cnpj; //-- 14   3-16
                $cAgrega_Dados .= '01'; //-- 2    17-18
                $cAgrega_Dados .= '   '; //-- 3    19-21
                $cAgrega_Dados .= $T013_Numero_Nota_Fiscal; //-- 6    22-27
                $cAgrega_Dados .= $T013_CFOP; //-- 4    28-31
                $cAgrega_Dados .= '000'; //-- 3    32-34
                $cAgrega_Dados .= $linha; //-- 3    35-37
                $cAgrega_Dados .= $T013_Codigo_Produto; //-- 4    38-51
                $cAgrega_Dados .= $T013_Quantidade_Produto; //-- 11   52-62
                $cAgrega_Dados .= $T013_Valor_Produto; //-- 12   63-74
                $cAgrega_Dados .= $T013_Valor_Desconto; //-- 12   75-86
                $cAgrega_Dados .= $T013_Valor_Base_ICMS; //-- 12   87-98
                $cAgrega_Dados .= $T013_Valor_Base_ICMS_Substituicao; //-- 12   99-110
                $cAgrega_Dados .= $T013_Valor_Total_IPI; //-- 12   111-122
                $cAgrega_Dados .= $T013_Aliquota_ICMS; //-- 4  123-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
        }
        
        if (!empty($arquivo['name'])) {
            $conteudo = file($arquivo['tmp_name']);
            $rows_60M = $rows_60A = $rows_60R = 0;
            foreach ($conteudo as $nr => $linha) {
                if (substr($linha, 0, 2) == "60") {
                    $cAgrega_Dados .= $linha;
                    
                    switch (substr($linha, 0, 3)) {
                        case '60M':
                            $rows_60M++;
                            break;
                        case '60A':
                            $rows_60A++;
                            break;
                        case '60R':
                            $rows_60R++;
                            break;
                    }
                }
            }
        }
        
        if (($Registro == 2 OR $Registro == 0) && empty($arquivo['name'])) {
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 60 - MESTRE (CUPOM FISCAL)
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula_T007 = "SELECT t1.T007_Data_Emissao,
		                                  ( SELECT t2.T007_Numero_Nota_Fiscal FROM T007 as t2 WHERE t2.T007_Data_Emissao=t1.T007_Data_Emissao AND t2.T007_C004_Id=t1.T007_C004_Id AND t2.T007_Flag_ACP='4' AND t2.T007_Numero_Nota_Fiscal!='' ORDER BY t2.T007_Id asc limit 1 ) as Primeiro_Cupom_Dia,
		                                  ( SELECT t2.T007_Numero_Nota_Fiscal FROM T007 as t2 WHERE t2.T007_Data_Emissao=t1.T007_Data_Emissao AND t2.T007_C004_Id=t1.T007_C004_Id AND t2.T007_Flag_ACP='4' AND t2.T007_Numero_Nota_Fiscal!='' ORDER BY t2.T007_Id desc limit 1) as Ultimo_Cupom_Dia
		                                  FROM T007 as t1
		                            WHERE t1.T007_C004_Id='{$g['empresaAtual']}'
		                              AND t1.T007_Data_Emissao >= '$Data_Inicio'
		                              AND t1.T007_Data_Emissao <= '$Data_Fim'
		                              AND t1.T007_Flag_ACP='4'
		                              AND t1.T007_Numero_Nota_Fiscal!=''
		                              AND T007_Flag_Inutilizado!='S'
		                              AND t1.T007_Flag_Cancelada!='S'
		                         GROUP BY t1.T007_Data_Emissao
		                         ORDER BY t1.T007_Data_Emissao,t1.T007_Numero_Nota_Fiscal ";
            
            
            $cT007    = mysql_query($cClausula_T007);
            $rows_60M = mysql_num_rows($cT007);
            echo mysql_error();
            
            //$Numero_Serie_Impressora_Fiscal='4708000149374';
            
            while ($mT007 = mysql_fetch_array($cT007)) {
                // Data  de emissao
                $T007_Data_Emissao = STR_REPLACE("-", "", $mT007['T007_Data_Emissao']);
                $T007_Data_Emissao = STR_PAD($T007_Data_Emissao, 8, "0", STR_PAD_LEFT);
                
                $Primeiro_Cupom_Dia = STR_PAD($mT007['Primeiro_Cupom_Dia'], 6, "0", STR_PAD_LEFT);
                $Ultimo_Cupom_Dia   = STR_PAD($mT007['Ultimo_Cupom_Dia'], 6, "0", STR_PAD_LEFT);
                
                $Numero_Serie_Impressora_Fiscal = STR_PAD($Numero_Serie_Impressora_Fiscal, 20, "0", STR_PAD_LEFT);
                
                $cAgrega_Dados .= "60"; //-- 2    1-2
                $cAgrega_Dados .= "M"; //-- 1    3
                $cAgrega_Dados .= $T007_Data_Emissao; //-- 8    4-11 Data de emissao dos documentos fiscais
                $cAgrega_Dados .= $Numero_Serie_Impressora_Fiscal; //-- 20   12-31 Numero de s�rie da impressora fiscal
                $cAgrega_Dados .= "001"; //-- 3    32-34 Codigo do equipamento na empresa
                $cAgrega_Dados .= "2B"; //-- 2    35-36 Codigo do modelo do documento fiscal Preencher com "2B", quando se tratar de Cupom Fiscal emitido por m�quina registradora (n�o ECF), com "2C", quando se tratar de Cupom Fiscal PDV, ou "2D", quando se tratar de Cupom Fiscal (emitido por ECF). J� para os demais Documentos Fiscais deve ser preenchido conforme c�digos da tabela de modelos, do subitem 3.2.1;
                $cAgrega_Dados .= $Primeiro_Cupom_Dia; //-- 6    37-42 Numero do primeiro documento fiscal do dia
                $cAgrega_Dados .= $Ultimo_Cupom_Dia; //-- 6    43-48 Numero do ultimo documento fiscal do dia
                $cAgrega_Dados .= "000000"; //-- 6    49-54 Numero do contador de reducao Z (CRZ)
                $cAgrega_Dados .= "000"; //-- 3    55-57 Valor acumulado no contador de reinicio de operacao (CRO)
                $cAgrega_Dados .= "0000000000000000"; //-- 16   58-73 Valor acumulado no totalizador de Venda Bruta
                $cAgrega_Dados .= "0000000000000000"; //-- 16   74-89 Valor acumulado no totalizador Geral
                $cAgrega_Dados .= str_repeat(" ", 37); //-- 37   90-126 Brancos
                
                $cAgrega_Dados .= SALTAR_LINHA;
            }
            
            
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 60 - ANALITICO (CUPOM FISCAL)
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula_T007 = "SELECT T007_Data_Emissao,
		                                  T008_Aliquota_ICMS,
		                                  round(sum(T008_Valor_ICMS),2) as T008_Valor_ICMS,
		                                  round(sum((T008_Valor_Preco_Unitario*T008_Quantidade)-T008_Valor_Total_Desconto),2) as T008_Valor_Preco_Unitario
		                                  FROM T007
		                           LEFT JOIN T008 ON T008_T007_Id=T007_Id
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND T007_Flag_ACP='4'
		                              AND T007_Numero_Nota_Fiscal!=''
		                              AND T007_Flag_Cancelada!='S'
		                         GROUP BY T007_Data_Emissao,T008_Aliquota_ICMS
		                         ORDER BY T007_Data_Emissao,T008_Aliquota_ICMS ";
            
            
            $cT007    = mysql_query($cClausula_T007);
            $rows_60A = mysql_num_rows($cT007);
            echo mysql_error();
            
            
            while ($mT007 = mysql_fetch_array($cT007)) {
                // Data  de emissao
                $T007_Data_Emissao = STR_REPLACE("-", "", $mT007['T007_Data_Emissao']);
                $T007_Data_Emissao = STR_PAD($T007_Data_Emissao, 8, "0", STR_PAD_LEFT);
                
                $Numero_Serie_Impressora_Fiscal = substr(STR_PAD($Numero_Serie_Impressora_Fiscal, 20, "0", STR_PAD_LEFT), 0, 20);
                
                $T008_Aliquota_ICMS = STR_REPLACE(array(
                    ",",
                    "."
                ), "", $mT007['T008_Aliquota_ICMS']);
                $T008_Aliquota_ICMS = STR_PAD($T008_Aliquota_ICMS, 4, "0", STR_PAD_LEFT);
                
                $T008_Valor_ICMS = STR_REPLACE(array(
                    ",",
                    "."
                ), "", $mT007['T008_Valor_ICMS']);
                $T008_Valor_ICMS = STR_PAD($T008_Valor_ICMS, 12, "0", STR_PAD_LEFT);
                
                $T008_Valor_Preco_Unitario = $mT007['T008_Valor_Preco_Unitario'];
                $T008_Valor_Preco_Unitario = number_format($T008_Valor_Preco_Unitario, 2, ',', '.');
                $T008_Valor_Preco_Unitario = STR_REPLACE(ARRAY(
                    ".",
                    ","
                ), "", $T008_Valor_Preco_Unitario);
                $T008_Valor_Preco_Unitario = substr($T008_Valor_Preco_Unitario, 0, 12);
                $T008_Valor_Preco_Unitario = STR_PAD($T008_Valor_Preco_Unitario, 12, "0", STR_PAD_LEFT);
                
                $cAgrega_Dados .= "60"; //-- 2    1-2 Tipo
                $cAgrega_Dados .= "A"; //-- 1    3 SubTipo
                $cAgrega_Dados .= $T007_Data_Emissao; //-- 8    4-11 Data de emissao dos documentos fiscais
                $cAgrega_Dados .= $Numero_Serie_Impressora_Fiscal; //-- 20   12-31 Numero de s�rie da impressora fiscal
                $cAgrega_Dados .= $T008_Aliquota_ICMS; //-- 4    32-35 Identificador da Situacao tributaria aliquota
                $cAgrega_Dados .= $T008_Valor_Preco_Unitario; //-- 12   36-47 Valor acumulado acumulado no final do dia no totalizador parcial da situacao tributaria/aliquota indicada no campo 05 (com 2 decimais)
                $cAgrega_Dados .= str_repeat(" ", 79); //-- 37   48-126
                
                $cAgrega_Dados .= SALTAR_LINHA;
                
            }
            
            
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 60 - RESUMO (CUPOM FISCAL)
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula_T007 = "SELECT substring(T007_Data_Emissao,1,7) as Ano_Mes,
		                                  T008_Codigo_Produto,
		                                  sum(T008_Quantidade) as T008_Quantidade,
		                                  T008_Aliquota_ICMS,
		                                  round(sum((T008_Valor_Preco_Unitario*T008_Quantidade)-T008_Valor_Total_Desconto),2) as T008_Valor_Preco_Unitario,
		                                  round(sum(T008_Valor_Base_ICMS_Impresso),2) as T008_Valor_Base_ICMS_Impresso,
		                                  round(sum(T008_Valor_ICMS),2) as T008_Valor_ICMS
		                                  FROM T007
		                           LEFT JOIN T008 ON T008_T007_Id=T007_Id
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND T007_Flag_ACP='4'
		                              AND T007_Numero_Nota_Fiscal!=''
		                              AND T007_Flag_Inutilizado!='S'
		                              AND T007_Flag_Cancelada!='S'
		                         GROUP BY substring(T007_Data_Emissao,1,7),T008_Codigo_Produto,T008_Aliquota_ICMS
		                         ORDER BY T008_Codigo_Produto,T007_Data_Emissao ";
            
            
            $cT007    = mysql_query($cClausula_T007);
            $rows_60R = mysql_num_rows($cT007);
            echo mysql_error();
            while ($mT007 = mysql_fetch_array($cT007)) {
                list($Ano, $Mes) = explode("-", $mT007['Ano_Mes']);
                $T007_Data_Emissao = STR_PAD($Mes . $Ano, 6, "0", STR_PAD_LEFT);
                
                $T008_Codigo_Produto = substr($mT007['T008_Codigo_Produto'], 0, 14);
                $T008_Codigo_Produto = STR_PAD($T008_Codigo_Produto, 14, "0", STR_PAD_LEFT);
                
                $T008_Quantidade = $mT007['T008_Quantidade'];
                $T008_Quantidade = number_format($T008_Quantidade, 3, ',', '.');
                $T008_Quantidade = STR_REPLACE(ARRAY(
                    ".",
                    ","
                ), "", $T008_Quantidade);
                $T008_Quantidade = substr($T008_Quantidade, 0, 13);
                $T008_Quantidade = STR_PAD($T008_Quantidade, 13, "0", STR_PAD_LEFT);
                
                $T008_Valor_Preco_Unitario = $mT007['T008_Valor_Preco_Unitario'];
                $T008_Valor_Preco_Unitario = number_format($T008_Valor_Preco_Unitario, 2, ',', '.');
                $T008_Valor_Preco_Unitario = STR_REPLACE(ARRAY(
                    ".",
                    ","
                ), "", $T008_Valor_Preco_Unitario);
                $T008_Valor_Preco_Unitario = substr($T008_Valor_Preco_Unitario, 0, 16);
                $T008_Valor_Preco_Unitario = STR_PAD($T008_Valor_Preco_Unitario, 16, "0", STR_PAD_LEFT);
                
                $T008_Valor_Base_ICMS_Impresso = $mT007['T008_Valor_Base_ICMS_Impresso'];
                $T008_Valor_Base_ICMS_Impresso = number_format($T008_Valor_Base_ICMS_Impresso, 3, ',', '.');
                $T008_Valor_Base_ICMS_Impresso = STR_REPLACE(ARRAY(
                    ".",
                    ","
                ), "", $T008_Valor_Base_ICMS_Impresso);
                $T008_Valor_Base_ICMS_Impresso = substr($T008_Valor_Base_ICMS_Impresso, 0, 16);
                $T008_Valor_Base_ICMS_Impresso = STR_PAD($T008_Valor_Base_ICMS_Impresso, 16, "0", STR_PAD_LEFT);
                
                $T008_Aliquota_ICMS = STR_REPLACE(array(
                    ",",
                    "."
                ), "", $mT007['T008_Aliquota_ICMS']);
                $T008_Aliquota_ICMS = STR_PAD($T008_Aliquota_ICMS, 4, "0", STR_PAD_LEFT);
                
                $cAgrega_Dados .= "60"; //-- 2    1-2
                $cAgrega_Dados .= "R"; //-- 1    3
                $cAgrega_Dados .= $T007_Data_Emissao; //-- 6    4-9 Mes e Ano de emissao dos documentos fiscais
                $cAgrega_Dados .= $T008_Codigo_Produto; //-- 14   10-23 Codigo do produto ou servico do informante
                $cAgrega_Dados .= $T008_Quantidade; //-- 13   24-36 Quantidade do produto no mes (3 decimais)
                $cAgrega_Dados .= $T008_Valor_Preco_Unitario; //-- 16   37-52 Valor bruto - desconto do produto ou servico acumulado no mes (2 decimais)
                $cAgrega_Dados .= $T008_Valor_Base_ICMS_Impresso; //-- 16   53-68 Base de calculo do ICMS, valor acumulado no mes
                $cAgrega_Dados .= $T008_Aliquota_ICMS; //-- 4    69-72 Identificador da situacao tributaria - Aliquota ICMS (2 casas decimais)
                $cAgrega_Dados .= str_repeat(" ", 54); //-- 54   73-126 Brancos
                
                $cAgrega_Dados .= SALTAR_LINHA;
            }
        }
        
        if ($Registro == 3 OR $Registro == 0) {
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 70 - CONHECIMENTO DE TRANSPORTE
            // ENTIDADE: T084
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula_T084 = "SELECT D022_Cnpj,
		                                  D022_Cpf,
		                                  D022_Flag_Pessoa_Fisica_Juridica,
		                                  D022_Inscricao_Estadual,
		                                  D018_UF,
		                                  T084_Data_Emissao,
		                                  T084_Numero_Conhecimento,
		                                  T084_Valor_Conhecimento,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T084_Valor_Base_ICMS) T084_Valor_Base_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T084_Valor_ICMS) T084_Valor_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T084_Aliquota_ICMS) T084_Aliquota_ICMS,
		                                  D006_Codigo_CFOP
		                             FROM T084
		                        LEFT JOIN D022 ON D022_Id=T084_D022_Id
		                        LEFT JOIN D006 ON D006_Id=T084_D006_Id
		                        LEFT JOIN D020 ON D020_Id=D022_D020_Id
		                        LEFT JOIN D018 ON D018_Id=D020_D018_Id
		                            WHERE T084_C004_Id='{$g['empresaAtual']}'
		                              AND T084_Data_Emissao >= '$Data_Inicio'
		                              AND T084_Data_Emissao <= '$Data_Fim'
		                              AND T084_Flag_Cancelado = 'N'
		                         ORDER BY T084_Numero_Conhecimento";
            
            $cT084        = mysql_query($cClausula_T084);
            $rows_70_T084 = mysql_num_rows($cT084);
            
            $erro = mysql_error();
            //echo "$erro";
            
            while ($mT084 = mysql_fetch_array($cT084)) {
                
                // verifica em que campo cpf ou cnpj
                if ($mT084['D022_Flag_Pessoa_Fisica_Juridica'] == 'J') {
                    //-- Preparando o campo Cnpj
                    $T084_Cpf_Cnpj = STR_REPLACE(".", "", $mT084['D022_Cnpj']);
                    $T084_Cpf_Cnpj = STR_REPLACE("/", "", $T084_Cpf_Cnpj);
                    $T084_Cpf_Cnpj = STR_REPLACE("-", "", $T084_Cpf_Cnpj);
                    $T084_Cpf_Cnpj = STR_REPEAT('0', 14 - STRLEN($T084_Cpf_Cnpj)) . $T084_Cpf_Cnpj;
                } else {
                    //--  Preparando o campo Cpf
                    $T084_Cpf_Cnpj = STR_REPLACE(".", "", $mT084['D022_Cpf']);
                    $T084_Cpf_Cnpj = STR_REPLACE("/", "", $T084_Cpf_Cnpj);
                    $T084_Cpf_Cnpj = STR_REPLACE("-", "", $T084_Cpf_Cnpj);
                    $T084_Cpf_Cnpj = STR_REPEAT('0', 14 - STRLEN($T084_Cpf_Cnpj)) . $T084_Cpf_Cnpj;
                }
                
                // Inscricao Estadual
                $T084_Inscricao_Estadual = STR_REPLACE(".", "", $mT084['D022_Inscricao_Estadual']);
                $T084_Inscricao_Estadual = STR_REPLACE("/", "", $T084_Inscricao_Estadual);
                $T084_Inscricao_Estadual = STR_REPLACE("-", "", $T084_Inscricao_Estadual);
                $T084_Inscricao_Estadual = SUBSTR($T084_Inscricao_Estadual, 0, 14);
                $T084_Inscricao_Estadual .= STR_REPEAT(' ', 14 - STRLEN($T084_Inscricao_Estadual));
                
                // data
                $T084_Data_Entrada = STR_REPLACE("-", "", $mT084['T084_Data_Emissao']);
                
                // Estado
                $T084_Estado = SUBSTR($mT084['D018_UF'], 0, 2);
                $T084_Estado .= STR_REPEAT(' ', 2 - STRLEN($T084_Estado));
                
                // Tipo Transporte
                //if($mT084['T084_Tipo_Transporte'] == 'RODOVIARIO')
                //{
                $T084_Tipo_Transporte = "08";
                //}
                //else if($mT084['T084_Tipo_Transporte'] == 'AEREO')
                //{
                //    $T084_Tipo_Transporte = "10";
                //}else{
                
                //                $T084_Tipo_Transporte = "00";
                //}
                //else
                //{
                //    $T084_Tipo_Transporte = "00";
                //}
                
                //Numero do documento
                $T084_Numero_Conhecimento = STR_REPLACE(".", "", $mT084['T084_Numero_Conhecimento']);
                $T084_Numero_Conhecimento = STR_PAD(SUBSTR($T084_Numero_Conhecimento, 0, 6), 6, '0', STR_PAD_LEFT);
                
                // numero CFOP
                $T084_Codigo_CFOP = STR_REPLACE(".", "", $mT084['D006_Codigo_CFOP']);
                $T084_Codigo_CFOP = STR_REPEAT('0', 4 - STRLEN($T084_Codigo_CFOP)) . $T084_Codigo_CFOP;
                
                // Valor Frete
                $T084_Valor_Frete = STR_REPLACE(".", "", $mT084['T084_Valor_Conhecimento']);
                $T084_Valor_Frete = STR_REPEAT('0', 13 - STRLEN($T084_Valor_Frete)) . $T084_Valor_Frete;
                
                // T084_Calculo_Valor_ICMS
                $T084_Calculo_Valor_ICMS = STR_REPLACE(".", "", $mT084['T084_Valor_Base_ICMS']);
                $T084_Calculo_Valor_ICMS = STR_REPEAT('0', 14 - STRLEN($T084_Calculo_Valor_ICMS)) . $T084_Calculo_Valor_ICMS;
                
                // T084_Valor_ICMS
                $T084_Valor_ICMS = STR_REPLACE(".", "", $mT084['T084_Valor_ICMS']);
                $T084_Valor_ICMS = STR_REPEAT('0', 14 - STRLEN($T084_Valor_ICMS)) . $T084_Valor_ICMS;
                
                //$T084_Isento
                $T084_Isento = STR_REPLACE(".", "", 0);
                $T084_Isento = STR_REPEAT('0', 14 - STRLEN($T084_Isento)) . $T084_Isento;
                
                //$T084_Outro
                $T084_Outro = STR_REPLACE(".", "", 0);
                $T084_Outro = STR_REPEAT('0', 14 - STRLEN($T084_Outro)) . $T084_Outro;
                
                //$T084_CIF_FOB
                $T084_CIF_FOB = STR_REPLACE(".", "", 0);
                $T084_CIF_FOB = STR_REPEAT('0', 1 - STRLEN($T084_CIF_FOB)) . $T084_CIF_FOB;
                
                //table 70
                $cAgrega_Dados .= "70"; //-- 2    1-2
                $cAgrega_Dados .= $T084_Cpf_Cnpj; //-- 14   3-16
                $cAgrega_Dados .= $T084_Inscricao_Estadual; //-- 14   17-30
                $cAgrega_Dados .= $T084_Data_Entrada; //-- 8    31-38
                $cAgrega_Dados .= $T084_Estado; //-- 2    39-40
                $cAgrega_Dados .= $T084_Tipo_Transporte; //-- 2    41-42
                $cAgrega_Dados .= ' '; //-- 1    43
                $cAgrega_Dados .= '  '; //-- 2    44-45
                $cAgrega_Dados .= $T084_Numero_Conhecimento; //-- 6    46-51
                $cAgrega_Dados .= $T084_Codigo_CFOP; //-- 4    52-55
                $cAgrega_Dados .= $T084_Valor_Frete; //-- 13   56-68
                $cAgrega_Dados .= $T084_Calculo_Valor_ICMS; //-- 14   69-82
                $cAgrega_Dados .= $T084_Valor_ICMS; //-- 14   83-96
                $cAgrega_Dados .= $T084_Isento; //-- 14   97-110
                $cAgrega_Dados .= $T084_Outro; //-- 14   111-124
                $cAgrega_Dados .= $T084_CIF_FOB; //-- 1    125
                $cAgrega_Dados .= 'N'; //-- 1    126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
        }
        
        if ($Inventario == 'S' AND $Registro == 0) {
            
            /////////////////////////////////////////////////////////////////////////////////
            // TIPO 74 - INVENTARIO
            /////////////////////////////////////////////////////////////////////////////////
            $sql = <<<EOT
                SELECT T001_D009_Id, concat(T001_Quantidade_Saldo,',,',T001_Valor_Custo_Unitario) as Saldo_Custo
                    FROM T001
                    WHERE T001_Data_Lancamento <= '$Data_Inventario'
                    ORDER BY T001_Data_Lancamento desc
EOT;
            $res = mysql_query($sql);
            $cacheSaldoCusto = array();
            while ($row = mysql_fetch_assoc($res)) {
                if (isset($cacheSaldoCusto[$row['T001_D009_Id']])) { continue; }
                $cacheSaldoCusto[$row['T001_D009_Id']] = $row['Saldo_Custo'];
            }

            $cClausula_D001 = "SELECT D001_Codigo_Produto,
	                                      D001_Id,
	                                      D001_Descricao_Produto,
	                                      D005_Classificacao_Fiscal,
	                                      D037_Unidade,
                                          D009_Id
	                                 FROM D001
	                            LEFT JOIN D049 ON D049_D001_Id=D001_Id
	                            LEFT JOIN D009 ON D049_Id=D009_D049_Id
	                            LEFT JOIN D005 ON D005_Id=D001_D005_Id
	                            LEFT JOIN D037 ON D001_D037_Id=D037_Id
	                                WHERE D009_C004_Id='{$g['empresaAtual']}'
	                             ORDER BY D001_Codigo_Produto";
            
            $cD001 = mysql_query($cClausula_D001);
            
            while ($mD001 = mysql_fetch_array($cD001)) {
                list($Saldo, $Custo) = explode(",,", $cacheSaldoCusto[$mD001['D009_Id']]);
                
                if ($Saldo <= 0 OR $Custo <= 0) {
                    continue;
                }
                $rows_74_D001++;
                
                $Data_Inventario = str_replace("-", "", $Data_Inventario);
                
                // Codigo do Produto
                $Codigo_Produto = SUBSTR($mD001['D001_Codigo_Produto'], 0, 14);
                $Codigo_Produto = STR_PAD($Codigo_Produto, 14, "0", STR_PAD_LEFT);
                $Codigo_Produto = $Codigo_Produto ;
                
                $Quantidade = $Saldo;
                $Quantidade = number_format($Quantidade, 3, '', '');
                $Quantidade = STR_REPLACE(ARRAY(
                    ".",
                    ","
                ), "", $Quantidade);
                $Quantidade = substr($Quantidade, 0, 13);
                $Quantidade = STR_PAD($Quantidade, 13, "0", STR_PAD_LEFT);
                
                $Valor_Produto = $Custo * $Saldo;
                $Valor_Produto = number_format($Valor_Produto, 2, '', '');
                $Valor_Produto = STR_REPLACE(ARRAY(
                    ".",
                    ","
                ), "", $Valor_Produto);
                $Valor_Produto = substr($Valor_Produto, 0, 13);
                $Valor_Produto = STR_PAD($Valor_Produto, 13, "0", STR_PAD_LEFT);
                
                
                // Para o Registro 75
                $m75_Codigo_Produto[]       = $mD001['D001_Codigo_Produto'];
                $m75_Descricao_Produto[]    = $mD001['D001_Descricao_Produto'];
                $m75_Classificacao_Fiscal[] = $mD001['D005_Classificacao_Fiscal'];
                $m75_Unidade[]              = $mD001['D037_Unidade'];
                
                
                $cAgrega_Dados .= "74"; //-- 2    1-2
                $cAgrega_Dados .= $Data_Inventario; //-- 8    3-10 Data Inventario
                $cAgrega_Dados .= $Codigo_Produto; //-- 14   11-24 Codigo do Produto
                $cAgrega_Dados .= $Quantidade; //-- 13   25-37 Quantidade
                $cAgrega_Dados .= $Valor_Produto; //-- 13   38-50 Valor do Produto
                $cAgrega_Dados .= "1"; //-- 51   51 Codigo de Posse das Mercadorias (1 = Propriedade do informante)
                $cAgrega_Dados .= "00000000000000"; //-- 14   52-65 CNPJ do proprietario da mercadoria
                $cAgrega_Dados .= "              "; //-- 14   66-79 Inscricao estadual
                $cAgrega_Dados .= "  "; //-- 2    80-81 UF
                $cAgrega_Dados .= str_repeat(" ", 45); //-- 45   82-126 Brancos
                $cAgrega_Dados .= SALTAR_LINHA;
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
                
            }
        }
        
        if ($Registro == 1 OR $Registro == 0) {
            $arrayProdutosNota = array();
            /////////////////////////////////////////////////////////////////////////////////
            // TIPO 75 - CODIGO DE PRODUTO OU SERVICO
            // ENTIDADE T007 NOTA EMITIDA
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula         = "(";
            $cClausula .= "SELECT T008_Codigo_Produto,
		                                  D005_Classificacao_Fiscal as T008_Classificacao_Fiscal,
		                                  T008_Descricao_Produto,
		                                  T008_Aliquota_IPI,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T008_Aliquota_ICMS) T008_Aliquota_ICMS,
		                                  T007_Data_Emissao,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T008_Percentual_Reducao_ICMS) T008_Percentual_Reducao_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(T008_Valor_Base_ICMS,2)) as T008_Valor_Base_ICMS,
		                                  D037_Unidade as T008_Unidade
		                             FROM T007
		                        LEFT JOIN T008 ON T007_Id=T008_T007_Id
		                        LEFT JOIN D037 ON D037_Id=T008_D037_Id
		                        LEFT JOIN D005 ON D005_Id=T008_D005_Id
		                        LEFT JOIN D006 ON D006_Id=T008_D006_Id
		                            WHERE T007_C004_Id='{$g['empresaAtual']}'
		                              AND T007_Data_Emissao >= '$Data_Inicio'
		                              AND T007_Data_Emissao <= '$Data_Fim'
		                              AND (T007_Flag_ACP='1' OR T007_Flag_ACP='6' OR T007_Flag_ACP='4')
		                              AND T007_Flag_Cancelada!='S'
		                              AND T007_Numero_Nota_Fiscal!=''
		                              AND T007_Flag_Inutilizado!='S'
		                              AND T008_Codigo_Produto!=''
		                              AND T008_Descricao_Produto!=''
		                         GROUP BY T008_Codigo_Produto
		                         ORDER BY T008_Codigo_Produto";
            $cClausula .= " ) union (";
            $cClausula .= "SELECT T014_Codigo_Produto as T008_Codigo_Produto,
		                                  T014_Classificacao_Fiscal as T008_Classificacao_Fiscal,
		                                  T014_Descricao_Produto T008_Descricao_Produto,
		                                  T014_Aliquota_IPI as T008_Aliquota_IPI,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T014_Aliquota_ICMS) as T008_Aliquota_ICMS,
		                                  T013_Data_Entrada as T007_Data_Emissao,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,T014_Percentual_Reducao_ICMS) as T008_Percentual_Reducao_ICMS,
		                                  if(D006_Flag_Deb_Crd_ICMS='N',0,round(T014_Valor_Base_ICMS,2)) as T008_Valor_Base_ICMS,
		                                  D037_Unidade as T008_Unidade
		                             FROM T013
		                        LEFT JOIN T014 ON T014_T013_Id=T013_Id
		                        LEFT JOIN D009 ON D009_Id=T014_D009_Id
		                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
		                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
		                        LEFT JOIN D037 ON D037_Id=T014_D037_Id OR D001_D037_Id = D037_Id
		                        LEFT JOIN D005 ON D005_Id=T014_D005_Id
		                        LEFT JOIN D006 ON D006_Id=T014_D006_Id or D006_Id=T013_D006_Id
		                            WHERE T013_C004_Id='{$g['empresaAtual']}'
		                              AND T013_Data_Entrada >= '$Data_Inicio'
		                              AND T013_Data_Entrada <= '$Data_Fim'
		                              AND T013_Numero_Nota_Fiscal not LIKE '%A%'
		                              AND FLOOR(T013_Numero_Nota_Fiscal)>0
		                              AND T014_Codigo_Produto!=''
		                              AND T014_Descricao_Produto!=''
		                              AND ifnull(T013_Flag_Cancelada,'N')!='S'
		                              AND ifnull(T013_Flag_Travar_Nota,'N')='S'
		                         GROUP BY T014_Codigo_Produto
		                         ORDER BY T014_Codigo_Produto";
            $cClausula .= ")";
            
            $cT007 = mysql_query($cClausula);
            $erro  = mysql_error();
            
            $rows_75_T007  = mysql_num_rows($cT007);
            $TotalMatriz75 = count($m75_Codigo_Produto);
            while ($mT007 = mysql_fetch_array($cT007)) {
                
                for ($y = 0; $y < $TotalMatriz75; $y++) {
                    if ($m75_Codigo_Produto['$y'] == $mT007['T008_Codigo_Produto']) {
                        echo "eliminados: " . $m75_Codigo_Produto['$y'] . "<BR><BR>";
                        unset($m75_Codigo_Produto['$y']);
                        unset($m75_Descricao_Produto['$y']);
                        unset($m75_Classificacao_Fiscal['$y']);
                        unset($m75_Unidade['$y']);
                    }
                }
                
                //data inicial
                $T007_Data_Inicial = str_replace("-", "", $Data_Inicio);
                
                //data final
                $T007_Data_Final = str_replace("-", "", $Data_Fim);
                
                // Codigo do produto
                $T007_Codigo_Produto = SUBSTR($mT007['T008_Codigo_Produto'], 0, 14);
                $T007_Codigo_Produto = STR_PAD($T007_Codigo_Produto, 14, "0", STR_PAD_LEFT);
                $T007_Codigo_Produto = $T007_Codigo_Produto ;
                $arrayProdutosNota[] = $T007_Codigo_Produto;
                
                // Classificacao fiscal
                $T007_Classificacao_Fiscal = str_replace(".", "", $mT007['T008_Classificacao_Fiscal']);
                $T007_Classificacao_Fiscal = SUBSTR($T007_Classificacao_Fiscal, 0, 8);
                $T007_Classificacao_Fiscal .= STR_REPEAT(' ', 8 - STRLEN($T007_Classificacao_Fiscal));
                
                // Descricao_Produto
                $T007_Descricao_Produto = SUBSTR($mT007['T008_Descricao_Produto'], 0, 53);
                $T007_Descricao_Produto .= STR_REPEAT(' ', 53 - STRLEN($T007_Descricao_Produto));
                
                //T008_Aliquota_IPI
                $T007_Aliquota_IPI = STR_REPLACE(".", "", $mT007['T008_Aliquota_IPI']);
                $T007_Aliquota_IPI = STR_REPEAT('0', 5 - STRLEN($T007_Aliquota_IPI)) . $T007_Aliquota_IPI;
                
                //T008_Aliquota_ICMS
                $T007_Aliquota_ICMS = STR_REPLACE(".", "", $mT007['T008_Aliquota_ICMS']);
                $T007_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T007_Aliquota_ICMS)) . $T007_Aliquota_ICMS;
                
                //Reducacao_Base_ICMS
                $T007_Reducacao_Base_ICMS = STR_REPLACE(".", "", $mT007['T008_Percentual_Reducao_ICMS']);
                $T007_Reducacao_Base_ICMS = STR_REPEAT('0', 5 - STRLEN($T007_Reducacao_Base_ICMS)) . $T007_Reducacao_Base_ICMS;
                
                //Base_ICMS
                $T007_Valor_Total_Base_ICMS = STR_REPLACE(".", "", $mT007['T008_Valor_Base_ICMS']);
                $T007_Valor_Total_Base_ICMS = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total_Base_ICMS)) . $T007_Valor_Total_Base_ICMS;
                
                //UNIDADE
                $T008_Unidade = $mT007['T008_Unidade'];
                $T008_Unidade = $T008_Unidade . STR_REPEAT(' ', 6 - STRLEN($T008_Unidade));
                
                //table 75
                $cAgrega_Dados .= "75"; //-- 2    1-2
                $cAgrega_Dados .= $T007_Data_Inicial; //-- 8    3-10
                $cAgrega_Dados .= $T007_Data_Final; //-- 8    11-18
                $cAgrega_Dados .= $T007_Codigo_Produto; //-- 14   19-31
                $cAgrega_Dados .= $T007_Classificacao_Fiscal; //-- 8    33-40
                $cAgrega_Dados .= $T007_Descricao_Produto; //-- 53   41-93
                $cAgrega_Dados .= $T008_Unidade; //-- 6    94-99
                $cAgrega_Dados .= $T007_Aliquota_IPI; //-- 5    100-104
                $cAgrega_Dados .= $T007_Aliquota_ICMS; //-- 4    105-108
                $cAgrega_Dados .= $T007_Reducacao_Base_ICMS; //-- 5    109-113
                $cAgrega_Dados .= $T007_Valor_Total_Base_ICMS; //-- 13   114-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            for ($y = 0; $y < $TotalMatriz75; $y++) {
                if (empty($m75_Codigo_Produto['$y'])) {
                    continue;
                }
                $rows_75_T007++;
                
                //data inicial
                $T007_Data_Inicial = str_replace("-", "", $Data_Inicio);
                
                //data final
                $T007_Data_Final = str_replace("-", "", $Data_Fim);
                
                // Codigo do produto
                $T007_Codigo_Produto = SUBSTR($m75_Codigo_Produto['$y'], 0, 14);
                $T007_Codigo_Produto = STR_PAD($T007_Codigo_Produto, 14, "0", STR_PAD_LEFT);
                $T007_Codigo_Produto = $T007_Codigo_Produto ;
                
                // Classificacao fiscal
                $T007_Classificacao_Fiscal = str_replace(".", "", $m75_Classificacao_Fiscal['$y']);
                $T007_Classificacao_Fiscal = SUBSTR($T007_Classificacao_Fiscal, 0, 8);
                $T007_Classificacao_Fiscal .= STR_REPEAT(' ', 8 - STRLEN($T007_Classificacao_Fiscal));
                
                // Descricao_Produto
                $T007_Descricao_Produto = SUBSTR($m75_Descricao_Produto['$y'], 0, 53);
                $T007_Descricao_Produto .= STR_REPEAT(' ', 53 - STRLEN($T007_Descricao_Produto));
                
                //T008_Aliquota_IPI
                $T007_Aliquota_IPI = STR_REPLACE(".", "", 0);
                $T007_Aliquota_IPI = STR_REPEAT('0', 5 - STRLEN($T007_Aliquota_IPI)) . $T007_Aliquota_IPI;
                
                //T008_Aliquota_ICMS
                $T007_Aliquota_ICMS = STR_REPLACE(".", "", 0);
                $T007_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T007_Aliquota_ICMS)) . $T007_Aliquota_ICMS;
                
                //Reducacao_Base_ICMS
                $T007_Reducacao_Base_ICMS = STR_REPLACE(".", "", 0);
                $T007_Reducacao_Base_ICMS = STR_REPEAT('0', 5 - STRLEN($T007_Reducacao_Base_ICMS)) . $T007_Reducacao_Base_ICMS;
                
                //Base_ICMS
                $T007_Valor_Total_Base_ICMS = STR_REPLACE(".", "", 0);
                $T007_Valor_Total_Base_ICMS = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total_Base_ICMS)) . $T007_Valor_Total_Base_ICMS;
                
                //UNIDADE
                $T008_Unidade = $m75_Unidade['$y'];
                $T008_Unidade = $T008_Unidade . STR_REPEAT(' ', 6 - STRLEN($T008_Unidade));
                
                //table 75
                $cAgrega_Dados .= "75"; //-- 2    1-2
                $cAgrega_Dados .= $T007_Data_Inicial; //-- 8    3-10
                $cAgrega_Dados .= $T007_Data_Final; //-- 8    11-18
                $cAgrega_Dados .= $T007_Codigo_Produto; //-- 14   19-31
                $cAgrega_Dados .= $T007_Classificacao_Fiscal; //-- 8    33-40
                $cAgrega_Dados .= $T007_Descricao_Produto; //-- 53   41-93
                $cAgrega_Dados .= $T008_Unidade; //-- 6    94-99
                $cAgrega_Dados .= $T007_Aliquota_IPI; //-- 5    100-104
                $cAgrega_Dados .= $T007_Aliquota_ICMS; //-- 4    105-108
                $cAgrega_Dados .= $T007_Reducacao_Base_ICMS; //-- 5    109-113
                $cAgrega_Dados .= $T007_Valor_Total_Base_ICMS; //-- 13   114-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 75 - CODIGO DE PRODUTO EMITIDO PELO EQUIPAMENTO ECF
            // O CODIGO DOS PRODUTOS NO EQUIPAMENTO ECF ESTA COM ZEROS A ESQUERDA (EX: 223 > 00000000223)
            /////////////////////////////////////////////////////////////////////////////////////
            if (!empty($arquivo['name'])) {
                $conteudo = file($arquivo['tmp_name']);
                foreach ($conteudo as $nr => $linha) {
                    if (substr($linha, 0, 2) == "75") {
                        if (!in_array(substr($linha, 18, 14), $arrayProdutosNota)) {
                            $cAgrega_Dados .= $linha;
                            $rows_75_T007++;
                        }
                    }
                }
            }
            
            /*
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 75 - CODIGO DE PRODUTO OU SERVICO
            // ENTIDADE T013 NOTA RECEBIDA
            /////////////////////////////////////////////////////////////////////////////////////
            $cClausula_T013 = "SELECT T014_Codigo_Produto,
            T014_Classificacao_Fiscal,
            T014_Descricao_Produto,
            T014_Aliquota_IPI,
            T014_Aliquota_ICMS,
            T013_Data_Entrada,
            T014_Percentual_Reducao_ICMS,
            T014_Valor_Base_ICMS
            FROM T013
            LEFT JOIN T014 ON T014_T013_Id=T013_Id
            WHERE T013_C004_Id='$Empresa_Atual'
            AND T013_Data_Entrada >= '$Data_Inicio'
            AND T013_Data_Entrada <= '$Data_Fim'
            AND T013_Numero_Nota_Fiscal not LIKE '%A%'
            AND FLOOR(T013_Numero_Nota_Fiscal)>0
            AND T014_Codigo_Produto!=''
            AND T014_Descricao_Produto!=''
            AND T013_Flag_Travar_Nota='S'
            AND T013_Flag_Cancelada!='S'
            GROUP BY T014_Codigo_Produto
            ORDER BY T014_Codigo_Produto";
            
            $cT013=mysql_query($cClausula_T013);
            $rows_75_T013 = mysql_num_rows($cT013);
            
            $erro = mysql_error();
            echo "$erro";
            
            while($mT013 = mysql_fetch_array($cT013)){
            
            //data inicial
            $T013_Data_Inicial = str_replace("-","",$Data_Inicio);
            
            //data final
            $T013_Data_Final = str_replace("-","",$Data_Fim);
            
            // Codigo do produto
            $T013_Codigo_Produto=SUBSTR($mT013['T014_Codigo_Produto'],0,14);
            $T013_Codigo_Produto = STR_PAD($T013_Codigo_Produto,14," ",STR_PAD_RIGHT);
            
            // Classificacao fiscal
            $T013_Classificacao_Fiscal = str_replace(".","",$mT013['T014_Classificacao_Fiscal']);
            $T013_Classificacao_Fiscal=SUBSTR($T013_Classificacao_Fiscal,0,8);
            $T013_Classificacao_Fiscal.=STR_REPEAT(' ',8-STRLEN($T013_Classificacao_Fiscal));
            
            // Descricao_Produto
            $T013_Descricao_Produto=SUBSTR($mT013['T014_Descricao_Produto'],0,53);
            $T013_Descricao_Produto.=STR_REPEAT(' ',53-STRLEN($T013_Descricao_Produto));
            
            //T008_Aliquota_IPI
            $T013_Percentual_IPI=STR_REPLACE(".","",$mT013['T014_Aliquota_IPI']);
            $T013_Percentual_IPI=STR_REPEAT('0',5-STRLEN($T013_Percentual_IPI)).$T013_Percentual_IPI;
            
            //T008_Aliquota_ICMS
            $T013_Aliquota_ICMS=STR_REPLACE(".","",$mT013['T014_Aliquota_ICMS']);
            $T013_Aliquota_ICMS=STR_REPEAT('0',4-STRLEN($T013_Aliquota_ICMS)).$T013_Aliquota_ICMS;
            
            //Reducacao_Base_ICMS
            $T013_Reducacao_Base_ICMS=STR_REPLACE(".","",$mT013['T014_Percentual_Reducao_ICMS']);
            $T013_Reducacao_Base_ICMS=STR_REPEAT('0',5-STRLEN($T013_Reducacao_Base_ICMS)).$T013_Reducacao_Base_ICMS;
            
            //Base_ICMS
            $T013_Base_ICMS=STR_REPLACE(".","",$mT013['T014_Valor_Base_ICMS']);
            $T013_Base_ICMS=STR_REPEAT('0',13-STRLEN($T013_Base_ICMS)).$T013_Base_ICMS;
            
            //UNIDADE
            $T014_Unidade='PC';
            $T014_Unidade=$T014_Unidade.STR_REPEAT(' ',6-STRLEN($T014_Unidade));
            
            //table 75
            $cAgrega_Dados .= "75";                                      //-- 2    1-2
            $cAgrega_Dados .= $T013_Data_Inicial;                        //-- 8    3-10
            $cAgrega_Dados .= $T013_Data_Final;                          //-- 8    11-18
            $cAgrega_Dados .= $T013_Codigo_Produto;                      //-- 14   19-31
            $cAgrega_Dados .= $T013_Classificacao_Fiscal;                //-- 8    33-40
            $cAgrega_Dados .= $T013_Descricao_Produto;                   //-- 53   41-93
            $cAgrega_Dados .= $T014_Unidade;                             //-- 6    94-99
            $cAgrega_Dados .= $T013_Percentual_IPI;                      //-- 5    100-104
            $cAgrega_Dados .= $T013_Aliquota_ICMS;                       //-- 4    105-108
            $cAgrega_Dados .= $T013_Reducacao_Base_ICMS;                 //-- 5    109-113
            $cAgrega_Dados .= $T013_Base_ICMS;                           //-- 13   114-126
            $cAgrega_Dados .= SALTAR_LINHA;
            
            gravar_linha($cArquivo,$cAgrega_Dados);
            $cAgrega_Dados="";
            $conta_linha++;
            }
            */
        }

        // REGISTRO TIPO 2 = CUPOM FISCAL.
        // PARA CADA REGISTRO TIPO 60R É NECESSÁRIO UM REGISTRO TIPO 75 ABAIXO
        if ($Registro == 2) {
            $arrayProdutosNota = array();
            /////////////////////////////////////////////////////////////////////////////////
            // TIPO 75 - CODIGO DE PRODUTO OU SERVICO
            // ENTIDADE T007 NOTA EMITIDA - CUPOM FISCAL
            /////////////////////////////////////////////////////////////////////////////////
            $cClausula = "SELECT T008_Codigo_Produto,
                                          D005_Classificacao_Fiscal as T008_Classificacao_Fiscal,
                                          T008_Descricao_Produto,
                                          T008_Aliquota_IPI,
                                          if(D006_Flag_Deb_Crd_ICMS='N',0,T008_Aliquota_ICMS) T008_Aliquota_ICMS,
                                          T007_Data_Emissao,
                                          if(D006_Flag_Deb_Crd_ICMS='N',0,T008_Percentual_Reducao_ICMS) T008_Percentual_Reducao_ICMS,
                                          if(D006_Flag_Deb_Crd_ICMS='N',0,round(T008_Valor_Base_ICMS,2)) as T008_Valor_Base_ICMS,
                                          D037_Unidade as T008_Unidade
                                     FROM T007
                                LEFT JOIN T008 ON T007_Id=T008_T007_Id
                                LEFT JOIN D037 ON D037_Id=T008_D037_Id
                                LEFT JOIN D005 ON D005_Id=T008_D005_Id
                                LEFT JOIN D006 ON D006_Id=T008_D006_Id
                                    WHERE T007_C004_Id='{$g['empresaAtual']}'
                                      AND T007_Data_Emissao >= '$Data_Inicio'
                                      AND T007_Data_Emissao <= '$Data_Fim'
                                      AND (T007_Flag_ACP='4')
                                      AND T007_Flag_Cancelada!='S'
                                      AND T007_Numero_Nota_Fiscal!=''
                                      AND T007_Flag_Inutilizado!='S'
                                      AND T008_Codigo_Produto!=''
                                      AND T008_Descricao_Produto!=''
                                 GROUP BY T008_Codigo_Produto
                                 ORDER BY T008_Codigo_Produto;";

            
            $cT007 = mysql_query($cClausula);
            $erro  = mysql_error();
            
            $rows_75_T007  = mysql_num_rows($cT007);
            $TotalMatriz75 = count($m75_Codigo_Produto);
            while ($mT007 = mysql_fetch_array($cT007)) {
                
                for ($y = 0; $y < $TotalMatriz75; $y++) {
                    if ($m75_Codigo_Produto['$y'] == $mT007['T008_Codigo_Produto']) {
                        echo "eliminados: " . $m75_Codigo_Produto['$y'] . "<BR><BR>";
                        unset($m75_Codigo_Produto['$y']);
                        unset($m75_Descricao_Produto['$y']);
                        unset($m75_Classificacao_Fiscal['$y']);
                        unset($m75_Unidade['$y']);
                    }
                }
                
                //data inicial
                $T007_Data_Inicial = str_replace("-", "", $Data_Inicio);
                
                //data final
                $T007_Data_Final = str_replace("-", "", $Data_Fim);
                
                // Codigo do produto
                // $T007_Codigo_Produto = SUBSTR($mT007['T008_Codigo_Produto'], 0, 13);
                // $T007_Codigo_Produto = STR_PAD($T007_Codigo_Produto, 13, "0", STR_PAD_LEFT);
                

                $T007_Codigo_Produto = SUBSTR($mT007['T008_Codigo_Produto'],0,14);
                $T007_Codigo_Produto = STR_PAD($T007_Codigo_Produto,14," ",STR_PAD_RIGHT);
                
                // Classificacao fiscal
                $T007_Classificacao_Fiscal = str_replace(".", "", $mT007['T008_Classificacao_Fiscal']);
                $T007_Classificacao_Fiscal = SUBSTR($T007_Classificacao_Fiscal, 0, 8);
                $T007_Classificacao_Fiscal .= STR_REPEAT(' ', 8 - STRLEN($T007_Classificacao_Fiscal));
                
                // Descricao_Produto
                $T007_Descricao_Produto = SUBSTR($mT007['T008_Descricao_Produto'], 0, 53);
                $T007_Descricao_Produto .= STR_REPEAT(' ', 53 - STRLEN($T007_Descricao_Produto));
                
                //T008_Aliquota_IPI
                $T007_Aliquota_IPI = STR_REPLACE(".", "", $mT007['T008_Aliquota_IPI']);
                $T007_Aliquota_IPI = STR_REPEAT('0', 5 - STRLEN($T007_Aliquota_IPI)) . $T007_Aliquota_IPI;
                
                //T008_Aliquota_ICMS
                $T007_Aliquota_ICMS = STR_REPLACE(".", "", $mT007['T008_Aliquota_ICMS']);
                $T007_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T007_Aliquota_ICMS)) . $T007_Aliquota_ICMS;
                
                //Reducacao_Base_ICMS
                $T007_Reducacao_Base_ICMS = STR_REPLACE(".", "", $mT007['T008_Percentual_Reducao_ICMS']);
                $T007_Reducacao_Base_ICMS = STR_REPEAT('0', 5 - STRLEN($T007_Reducacao_Base_ICMS)) . $T007_Reducacao_Base_ICMS;
                
                //Base_ICMS
                $T007_Valor_Total_Base_ICMS = STR_REPLACE(".", "", $mT007['T008_Valor_Base_ICMS']);
                $T007_Valor_Total_Base_ICMS = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total_Base_ICMS)) . $T007_Valor_Total_Base_ICMS;
                
                //UNIDADE
                $T008_Unidade = $mT007['T008_Unidade'];
                $T008_Unidade = $T008_Unidade . STR_REPEAT(' ', 6 - STRLEN($T008_Unidade));
                
                //table 75
                $cAgrega_Dados .= "75"; //-- 2    1-2
                $cAgrega_Dados .= $T007_Data_Inicial; //-- 8    3-10
                $cAgrega_Dados .= $T007_Data_Final; //-- 8    11-18
                $cAgrega_Dados .= $T007_Codigo_Produto; //-- 14   19-31
                $cAgrega_Dados .= $T007_Classificacao_Fiscal; //-- 8    33-40
                $cAgrega_Dados .= $T007_Descricao_Produto; //-- 53   41-93
                $cAgrega_Dados .= $T008_Unidade; //-- 6    94-99
                $cAgrega_Dados .= $T007_Aliquota_IPI; //-- 5    100-104
                $cAgrega_Dados .= $T007_Aliquota_ICMS; //-- 4    105-108
                $cAgrega_Dados .= $T007_Reducacao_Base_ICMS; //-- 5    109-113
                $cAgrega_Dados .= $T007_Valor_Total_Base_ICMS; //-- 13   114-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            for ($y = 0; $y < $TotalMatriz75; $y++) {
                if (empty($m75_Codigo_Produto['$y'])) {
                    continue;
                }
                $rows_75_T007++;
                
                //data inicial
                $T007_Data_Inicial = str_replace("-", "", $Data_Inicio);
                
                //data final
                $T007_Data_Final = str_replace("-", "", $Data_Fim);
                
                // Codigo do produto
                $T007_Codigo_Produto = SUBSTR($m75_Codigo_Produto['$y'], 0, 14);
                $T007_Codigo_Produto = STR_PAD($T007_Codigo_Produto, 14, "0", STR_PAD_LEFT);
                $T007_Codigo_Produto = $T007_Codigo_Produto ;
                
                // Classificacao fiscal
                $T007_Classificacao_Fiscal = str_replace(".", "", $m75_Classificacao_Fiscal['$y']);
                $T007_Classificacao_Fiscal = SUBSTR($T007_Classificacao_Fiscal, 0, 8);
                $T007_Classificacao_Fiscal .= STR_REPEAT(' ', 8 - STRLEN($T007_Classificacao_Fiscal));
                
                // Descricao_Produto
                $T007_Descricao_Produto = SUBSTR($m75_Descricao_Produto['$y'], 0, 53);
                $T007_Descricao_Produto .= STR_REPEAT(' ', 53 - STRLEN($T007_Descricao_Produto));
                
                //T008_Aliquota_IPI
                $T007_Aliquota_IPI = STR_REPLACE(".", "", 0);
                $T007_Aliquota_IPI = STR_REPEAT('0', 5 - STRLEN($T007_Aliquota_IPI)) . $T007_Aliquota_IPI;
                
                //T008_Aliquota_ICMS
                $T007_Aliquota_ICMS = STR_REPLACE(".", "", 0);
                $T007_Aliquota_ICMS = STR_REPEAT('0', 4 - STRLEN($T007_Aliquota_ICMS)) . $T007_Aliquota_ICMS;
                
                //Reducacao_Base_ICMS
                $T007_Reducacao_Base_ICMS = STR_REPLACE(".", "", 0);
                $T007_Reducacao_Base_ICMS = STR_REPEAT('0', 5 - STRLEN($T007_Reducacao_Base_ICMS)) . $T007_Reducacao_Base_ICMS;
                
                //Base_ICMS
                $T007_Valor_Total_Base_ICMS = STR_REPLACE(".", "", 0);
                $T007_Valor_Total_Base_ICMS = STR_REPEAT('0', 13 - STRLEN($T007_Valor_Total_Base_ICMS)) . $T007_Valor_Total_Base_ICMS;
                
                //UNIDADE
                $T008_Unidade = $m75_Unidade['$y'];
                $T008_Unidade = $T008_Unidade . STR_REPEAT(' ', 6 - STRLEN($T008_Unidade));
                
                //table 75
                $cAgrega_Dados .= "75"; //-- 2    1-2
                $cAgrega_Dados .= $T007_Data_Inicial; //-- 8    3-10
                $cAgrega_Dados .= $T007_Data_Final; //-- 8    11-18
                $cAgrega_Dados .= $T007_Codigo_Produto; //-- 14   19-31
                $cAgrega_Dados .= $T007_Classificacao_Fiscal; //-- 8    33-40
                $cAgrega_Dados .= $T007_Descricao_Produto; //-- 53   41-93
                $cAgrega_Dados .= $T008_Unidade; //-- 6    94-99
                $cAgrega_Dados .= $T007_Aliquota_IPI; //-- 5    100-104
                $cAgrega_Dados .= $T007_Aliquota_ICMS; //-- 4    105-108
                $cAgrega_Dados .= $T007_Reducacao_Base_ICMS; //-- 5    109-113
                $cAgrega_Dados .= $T007_Valor_Total_Base_ICMS; //-- 13   114-126
                $cAgrega_Dados .= SALTAR_LINHA;
                
                gravar_linha($cArquivo, $cAgrega_Dados);
                $cAgrega_Dados = "";
                $conta_linha++;
            }
            
            /////////////////////////////////////////////////////////////////////////////////////
            // TIPO 75 - CODIGO DE PRODUTO EMITIDO PELO EQUIPAMENTO ECF
            // O CODIGO DOS PRODUTOS NO EQUIPAMENTO ECF ESTA COM ZEROS A ESQUERDA (EX: 223 > 00000000223)
            /////////////////////////////////////////////////////////////////////////////////////
            if (!empty($arquivo['name'])) {
                $conteudo = file($arquivo['tmp_name']);
                foreach ($conteudo as $nr => $linha) {
                    if (substr($linha, 0, 2) == "75") {
                        if (!in_array(substr($linha, 18, 14), $arrayProdutosNota)) {
                            $cAgrega_Dados .= $linha;
                            $rows_75_T007++;
                        }
                    }
                }
            }
        }
        
        /*
        /////////////////////////////////////////////////////////////////////////////////////
        // TIPO 85 - INFORMA��ES DE EXPORTACAO
        //
        /////////////////////////////////////////////////////////////////////////////////
        
        $cAgrega_Dados .= "85";                                      //-- 02   1-2
        $cAgrega_Dados .= "00000000000";                             //-- 11   3-13 N. da declaracao da exportacao
        $cAgrega_Dados .= "20081101";                                //-- 08   14-21 Data declaracao de exportacao
        $cAgrega_Dados .= "1";                                       //-- 01   22 Exportacao Direta (conforme tabela)
        $cAgrega_Dados .= "000000000000";                            //-- 12   23-34 N. Registro de Exportacao
        $cAgrega_Dados .= "20081101";                                //-- 08   35-42 Data registro exportacao
        $cAgrega_Dados .= "0000000000000000";                        //-- 16   43-58 N. Conhecimento
        $cAgrega_Dados .= "20081101";                                //-- 08   59-66 Data conhecimento de Embarque
        $cAgrega_Dados .= "01";                                      //-- 2    67-68 Tipo conhecimento transporte
        $cAgrega_Dados .= "0097";                                    //-- 4    69-72 Codigo pais destino (conf.tabela Siscomex)
        $cAgrega_Dados .= "00000000";                                //-- 8    73-80 Reservado
        $cAgrega_Dados .= "20081101";                                //-- 8    81-88 Data averbacao da Declaracao de Exportacao
        $cAgrega_Dados .= "000000";                                  //-- 6    89-94 Numero nota fiscal exportacao
        $cAgrega_Dados .= "20081101";                                //-- 8    95-102 Data emissao NF exportacao
        $cAgrega_Dados .= "01";                                      //-- 2    103-104 Modelo nota fiscal
        $cAgrega_Dados .= "001";                                     //-- 3    105-107 S�rie Nota Fiscal
        $cAgrega_Dados .= str_repeat(" ",19);                        //-- 19   108-126 Brancos
        $cAgrega_Dados .= SALTAR_LINHA;
        
        
        /////////////////////////////////////////////////////////////////////////////////////
        // TIPO 86 - INFORMA��ES COMPLEMENTARES DE EXPORTACAO
        //
        /////////////////////////////////////////////////////////////////////////////////
        
        $cAgrega_Dados .= "86";                                      //-- 02   1-2
        $cAgrega_Dados .= "000000000000";                            //-- 12   3-14 N. Registro de Exportacao
        $cAgrega_Dados .= "20081101";                                //-- 08   15-22 Data registro exportacao
        $cAgrega_Dados .= "00000000000000";                          //-- 14   23-36 CNPJ Contribuinte
        $cAgrega_Dados .= "1013282281    ";                          //-- 14   37-50 Inscricao Estadual Contribuinte
        $cAgrega_Dados .= "PR";                                      //-- 2    51-52 UF
        $cAgrega_Dados .= "000000";                                  //-- 6    53-58 Numero nota fiscal exportacao
        $cAgrega_Dados .= "20081101";                                //-- 8    59-66 Data emissao NF exportacao
        $cAgrega_Dados .= "01";                                      //-- 2    67-68 Modelo nota fiscal
        $cAgrega_Dados .= "001";                                     //-- 3    69-71 S�rie Nota Fiscal
        $cAgrega_Dados .= "00000000001459";                          //-- 14   72-85 Codigo do Produto
        $cAgrega_Dados .= "00000000001";                             //-- 11   86-96 Quantidade
        $cAgrega_Dados .= "000000000001";                            //-- 12   97-108 Valor do Produto
        $cAgrega_Dados .= "000000000001";                            //-- 12   109-120 Valor TOTAL do Produto
        $cAgrega_Dados .= "0";                                       //-- 1    121 Conforme tabela
        $cAgrega_Dados .= str_repeat(" ",5);                         //-- 5    122-126 Brancos
        $cAgrega_Dados .= SALTAR_LINHA;
        
        
        /////////////////////////////////////////////////////////////////
        // table 90
        ////////////////////////////////////////////////////////////////
        
        $rows_85=1;
        $rows_86=1;
        $rows_74=1;
        */
        
        $rows_60 = $rows_60M + $rows_60A + $rows_60R;
        
        $total_registros = $rows_50_T013 + $rows_50_T007 + $rows_51_T013 + $rows_51_T007 + $rows_53_T013 + $rows_53_T007 + $rows_54_T013 + $rows_54_T007 + $rows_70_T084 + $rows_70_T007 + $rows_75_T013 + $rows_75_T007 + $rows_74_D001 + $rows_85 + $rows_86 + $rows_60 + 4;
        
        //Base_ICMS
        $T013_Base_ICMS = STR_REPLACE(".", "", $T013_Base_ICMS);
        $T013_Base_ICMS = STR_REPEAT('0', 13 - STRLEN($T013_Base_ICMS)) . $T013_Base_ICMS;
        
        $cAgrega_Dados .= "90"; //-- 2    1-2
        $cAgrega_Dados .= $C004_Cnpj; //-- 14   3-16
        $cAgrega_Dados .= $C004_Inscricao_Estadual; //-- 14   17-30
        
        //tabela 50 do T007 e T013
        $T50_Total     = '50';
        $Total_Rows_50 = $rows_50_T013 + $rows_50_T007;
        $Total_Rows_50 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_50)) . $Total_Rows_50;
        $T50_Total .= $Total_Rows_50;
        $Total_Geral .= $T50_Total;
        
        //tabela 51 do T007 e T013
        $T51_Total     = '51';
        $Total_Rows_51 = $rows_51_T013 + $rows_51_T007;
        $Total_Rows_51 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_51)) . $Total_Rows_51;
        $T51_Total .= $Total_Rows_51;
        $Total_Geral .= $T51_Total;
        
        //tabela 53 do T007 e T013
        $T53_Total     = '53';
        $Total_Rows_53 = $rows_53_T013 + $rows_53_T007;
        $Total_Rows_53 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_53)) . $Total_Rows_53;
        $T53_Total .= $Total_Rows_53;
        $Total_Geral .= $T53_Total;
        
        //tabela 54 do T007 e T013
        $T54_Total     = '54';
        $Total_Rows_54 = $rows_54_T013 + $rows_54_T007;
        $Total_Rows_54 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_54)) . $Total_Rows_54;
        $T54_Total .= $Total_Rows_54;
        $Total_Geral .= $T54_Total;
        
        //tabela 70 do T007 e T013
        $T70_Total     = '70';
        $Total_Rows_70 = $rows_70_T084 + $rows_70_T007;
        $Total_Rows_70 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_70)) . $Total_Rows_70;
        $T70_Total .= $Total_Rows_70;
        $Total_Geral .= $T70_Total;
        
        //tabela 74
        $T74_Total     = '74';
        $Total_Rows_74 = $rows_74_D001;
        $Total_Rows_74 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_74)) . $Total_Rows_74;
        $T74_Total .= $Total_Rows_74;
        $Total_Geral .= $T74_Total;
        
        $Total_Geral .= STR_REPEAT(' ', 95 - STRLEN($Total_Geral));
        
        $cAgrega_Dados .= $Total_Geral; //-- 8   33-40
        $cAgrega_Dados .= '2'; //-- 1   126-126
        $cAgrega_Dados .= SALTAR_LINHA;
        
        gravar_linha($cArquivo, $cAgrega_Dados);
        $cAgrega_Dados = "";
        $conta_linha++;
        
        $Total_Geral = '';
        
        $cAgrega_Dados .= "90"; //-- 2    1-2
        $cAgrega_Dados .= $C004_Cnpj; //-- 14   3-16
        $cAgrega_Dados .= $C004_Inscricao_Estadual; //-- 14   17-30
        
        //tabela 85
        $T85_Total     = '85';
        $Total_Rows_85 = $rows_85;
        $Total_Rows_85 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_85)) . $Total_Rows_85;
        $T85_Total .= $Total_Rows_85;
        $Total_Geral .= $T85_Total;
        
        //tabela 86
        $T86_Total     = '86';
        $Total_Rows_86 = $rows_86;
        $Total_Rows_86 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_86)) . $Total_Rows_86;
        $T86_Total .= $Total_Rows_86;
        $Total_Geral .= $T86_Total;
        
        //tabela 75 do T007 e T013
        $T75_Total     = '75';
        $Total_Rows_75 = $rows_75_T013 + $rows_75_T007;
        $Total_Rows_75 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_75)) . $Total_Rows_75;
        $T75_Total .= $Total_Rows_75;
        $Total_Geral .= $T75_Total;
        
        //tabela 75 do T007 e T013
        $T60_Total     = '60';
        $Total_Rows_60 = $rows_60;
        $Total_Rows_60 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_60)) . $Total_Rows_60;
        $T60_Total .= $Total_Rows_60;
        $Total_Geral .= $T60_Total;
        
        //tabela 75 do T007 e T013
        $T99_Total     = '99';
        $Total_Rows_99 = $total_registros;
        $Total_Rows_99 = STR_REPEAT('0', 8 - STRLEN($Total_Rows_99)) . $Total_Rows_99;
        $T99_Total .= $Total_Rows_99;
        $Total_Geral .= $T99_Total;
        
        $Total_Geral .= STR_REPEAT(' ', 95 - STRLEN($Total_Geral));
        
        $cAgrega_Dados .= $Total_Geral; //-- 8   33-40
        $cAgrega_Dados .= '2'; //-- 1   126-126
        gravar_linha($cArquivo, $cAgrega_Dados);
        $cAgrega_Dados = "";
        
        return array(
            "$cArquivo"
        );
    }
}
