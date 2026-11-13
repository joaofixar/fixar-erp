<?php
namespace hardness;
require_once("old/lib/funcoes.php");
definirClasse('FIS008');
class FIS008_
{

    /**
     * exportarTXTNfe
     *
     * @param Form $form
     * @return String/array
     */ 	
    public function exportarTXTNfe($form)
    {
        global $g;
        
        $Diretorio_Usuario = $g['pathDadosUsuario'];
        
        $Data_Inicio = $form->campoValorEnviado('Data_Inicio');
        $Data_Fim    = $form->campoValorEnviado('Data_Fim');
        $C004_Id     = $form->campoValorEnviado('C004_Id');
        
        $Pular_Script = 'S';
        
        DEFINE(_SALTO_LINHA, CHR(13) . CHR(10));
        
        $cData_Inicio = str_replace("/", "_", $Data_Inicio);
        $cData_Fim    = str_replace("/", "_", $Data_Fim);
        
        $dData_Inicio = troca_data_amd($Data_Inicio);
        $dData_Fim    = troca_data_amd($Data_Fim);
        
        $xData_Inicio = str_replace("-", "", $Data_Inicio);
        $xData_Fim    = str_replace("-", "", $Data_Fim);
        
        if (empty($xData_Inicio)) {
            return "Digite a Data Inicial";
        }
        
        if (empty($xData_Fim)) {
            return "Digite a Data Final";
        }
        
        if ($xData_Fim < $xData_Inicio) {
            return "Data final é menor que a data inicial";
        }
        
        if (empty($C004_Id)) {
            return "Selecione uma Empresa";
        }
        
        // $_SESSION[sData_Inicio]=$Data_Inicio;
        // $_SESSION[sData_Fim]=$Data_Fim;
        
        
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T007 ";
        $cClausula .= " where T007_C004_Id='$C004_Id' ";
        $cClausula .= " and T007_Data_Emissao >= '$dData_Inicio' ";
        $cClausula .= " and T007_Data_Emissao <= '$dData_Fim' ";
        $cClausula .= " and T007_Flag_ACP='6' ";
        $cClausula .= " and (T007_Numero_Protocolo_Nfe is not null or T007_Numero_Protocolo_Nfe!='') ";
        $cClausula .= " order by T007_Numero_Nota_Fiscal ";
        $cT007 = mysql_query($cClausula);
        $erro  = mysql_error();
        
        $nTotalNotas = mysql_num_rows($cT007);
        if ($nTotalNotas == 0) {
            return "Não encontrada notas entre as datas selecionadas";
        }
        
        $cArquivo = $Diretorio_Usuario . '/arq_temp/txt_padrao_nfe.txt';
        if (file_exists($cArquivo)) {
            unlink("$cArquivo");
        }
        
        $cArquivo_Aberto = fopen($cArquivo, 'x');
        
        
        //-- Abre a tela de download do arquivo
        // header('Content-type: application/x-msdownload');
        // header('Content-Disposition: attachment; filename='.basename($cArquivo));
        // header('Pragma: no-cache');
        // header('Expires: 0');
        
        $String = 'NOTAFISCAL';
        $String .= '|';
        $String .= $nTotalNotas;
        fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
        
        while ($mT007 = mysql_fetch_array($cT007)) {
            //////////////////////////////////////////////////
            //-- Atributos da NF-e
            //////////////////////////////////////////////////
            $String = 'A';
            $String .= '|';
            
            //-- Vers�o do leiaute
            $String .= '1.10';
            $String .= '|';
            
            //-- Para a importa��o: informar apenas o literal �NFe�.
            //-- Para a exporta��o: ser� preenchido com a chave de acesso da NF-e precedida do literal �NFe�
            //-- Se informado na importa��o, a chave de acesso ser� desconsiderada.
            $String .= 'NFe' . $mT007['T007_Chave_Acesso_Nfe'];
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- Identificadores da NF-e
            //////////////////////////////////////////////////
            $String = 'B';
            $String .= '|';
            
            //-- C�digo da UF do emitente do Documento Fiscal. Utilizar a Tabela do IBGE de c�digo de unidades da federa��o.
            $String .= $mT007['T007_Empresa_Usuaria_Codigo_Estado_IBGE'];
            $String .= '|';
            
            //-- C�digo num�rico que comp�e a Chave de Acesso. N�o informar para a importa��o. Caso seja informado, ser� desconsiderado.
            $String .= substr($mT007['T007_Chave_Acesso_Nfe'], 34, 9);
            $String .= '|';
            
            $cClausula = " select ";
            $cClausula .= " D006_Descricao ";
            $cClausula .= " from T008 ";
            $cClausula .= " left join D006 on D006_Id=T008_D006_Id ";
            $cClausula .= " where T008_T007_Id='{$mT007['T007_Id']}' ";
            $cClausula .= " group by D006_Descricao ";
            $cSql_D006 = mysql_query($cClausula);
            echo mysql_error();
            
            unset($mDescricao_CFOP);
            while ($mD006 = mysql_fetch_array($cSql_D006)) {
                $mDescricao_CFOP[] = $mD006['D006_Descricao'];
            }
            $cDescricao_CFOP = implode(" - ", $mDescricao_CFOP);
            
            //-- Informar a natureza da opera��o de que decorrer a sa�da ou a entrada, tais
            //-- como: venda, compra, transfer�ncia, devolu��o, importa��o, consigna��o,
            //-- remessa (para fins de demonstra��o, de industrializa��o ou outra), conforme
            //-- previsto na al�nea 'i', inciso I, art. 19 do CONV�NIO S/N�, de 15 de dezembro de 1970.
            $String .= substr(TrocarCaracterEspecial($cDescricao_CFOP), 0, 60);
            $String .= '|';
            
            //-- 0 � pagamento � vista;
            //-- 1 � pagamento � prazo;
            //-- 2 - outros.
            $String .= '1';
            $String .= '|';
            
            //-- Utilizar o c�digo 55 para identifica��o da NF-e, emitida em substitui��o ao modelo 1 ou 1A.
            $String .= '55';
            $String .= '|';
            
            //-- S�rie do Documento Fiscal, informar 0 (zero) para s�rie �nica.
            $String .= '0';
            $String .= '|';
            
            //-- N�mero do Documento Fiscal.
            $String .= $mT007['T007_Numero_Nota_Fiscal'];
            $String .= '|';
            
            //-- Data de emiss�o do Documento Fiscal. Formato �AAAA-MM-DD�
            $String .= $mT007['T007_Data_Emissao'];
            $String .= '|';
            
            //-- Data de Sa�da ou da Entrada da Mercadoria/Produto
            $String .= $mT007['T007_Data_Saida'];
            $String .= '|';
            
            //-- Tipo do Documento Fiscal: 0 a entrada / 1 a sa�da
            if ($mT007['T007_Flag_Entrada_Saida'] == 'E') {
                $String .= '0';
            } else {
                $String .= '1';
            }
            $String .= '|';
            
            //-- Informar o munic�pio de ocorr�ncia do fato gerador do ICMS. Utilizar a Tabela do IBGE
            $String .= $mT007['T007_Empresa_Usuaria_Codigo_Cidade_IBGE'];
            $String .= '|';
            
            //-- Formato de Impress�o do DANFE: 1 a Retrato/ 2-Paisagem
            $String .= '1';
            $String .= '|';
            
            //-- Forma de Emiss�o da NF-e: 1 a Normal/ 2-Conting�ncia
            if ($mT007['T007_Flag_Contingencia'] == 'N') {
                $String .= '1';
            } else {
                $String .= '2';
            }
            $String .= '|';
            
            //-- Informar o DV da Chave de Acesso da NF-e, o DV ser� calculado com a aplica��o do algoritmo m�dulo 11 (base 2,9) da Chave de Acesso.
            //-- (vide item 5 do Manual de Integra��o). N�o informar para a importa��o. Caso seja informado, ser� desconsiderado.
            $String .= substr($mT007['T007_Chave_Acesso_Nfe'], -1);
            $String .= '|';
            
            //-- Identifica��o do Ambiente: Produ��o/ 2 - Homologa��o
            $String .= $mT007['T007_Ambiente_NFe'];
            $String .= '|';
            
            //-- Finalidade de emiss�o da NF-e: 1 a NF-e normal/ 2-NF-e complementar / 3 � NF-e de ajuste
            $String .= $mT007['T007_Flag_Finalidade_NFe'];
            $String .= '|';
            
            //-- Identificador do processo de emiss�o da NF-e:
            //-- 0 - emiss�o de NF-e com aplicativo do contribuinte;
            //-- 1 - emiss�o de NF-e avulsa pelo Fisco;
            //-- 2 - emiss�o de NF-e avulsa, pelo contribuinte com seu certificado digital, atrav�s do site do Fisco;
            //-- 3- emiss�o NF-e pelo contribuinte com aplicativo fornecido pelo Fisco.
            //-- Dever� SEMPRE ser preenchido com 3 se o Software Emissor for utilizado para a emiss�o
            $String .= '0';
            $String .= '|';
            
            //-- Identificador da vers�o do processo de emiss�o (informar a vers�o do aplicativo emissor de NF-e).
            //-- Caso informado vers�o diferente, o aplicativo, no momento da importa��o, realizar� a atualiza��o do campo automaticamente.
            $String .= '1.0';
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- Informa��o das NF-e referenciadas
            //////////////////////////////////////////////////
            if (!empty($mT007['T007_Origem_Chave_Acesso'])) {
                $String = 'B13';
                $String .= '|';
                
                //-- Chave de acesso das NF-e referenciadas
                $String .= $mT007['T007_Origem_Chave_Acesso'];
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            }
            
            //////////////////////////////////////////////////
            //-- Informa��o das NF referenciadas - Idem ao anterior,
            //-- referenciando uma Nota Fiscal modelo 1/1A normal (a NF referenciada n�o � uma NF-e).
            //////////////////////////////////////////////////
            if (!empty($mT007['T007_Origem_UF'])) {
                $String = 'B14';
                $String .= '|';
                
                //-- C�digo da UF do emitente do Documento Fiscal
                $String .= $mT007['T007_Origem_UF'];
                $String .= '|';
                
                //-- Ano e M�s de emiss�o da NF-e
                $String .= $mT007['T007_Origem_Ano_Mes'];
                $String .= '|';
                
                //-- CNPJ do emitente
                $String .= $mT007['T007_Origem_Cnpj'];
                $String .= '|';
                
                //-- Modelo do Documento Fiscal
                $String .= $mT007['T007_Origem_Modelo'];
                $String .= '|';
                
                //-- S�rie do Documento Fiscal
                $String .= $mT007['T007_Origem_Serie'];
                $String .= '|';
                
                //-- N�mero do Documento Fiscal
                $String .= $mT007['T007_Numero_Nota_Devolvida'];
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            }
            
            //////////////////////////////////////////////////
            //-- Emitente
            //////////////////////////////////////////////////
            $String = 'C';
            $String .= '|';
            
            //-- Raz�o Social ou Nome do emitente
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Nome']);
            $String .= '|';
            
            //-- Nome fantasia
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Nome']);
            $String .= '|';
            
            //-- Inscri��o Estadual: Campo de informa��o obrigat�ria nos casos de emiss�o pr�pria (procEmi = 0, 2 ou 3).
            $mT007['T007_Empresa_Usuaria_Inscricao_Estadual'] = str_replace(array(
                ".",
                "/",
                "-",
                " "
            ), "", $mT007['T007_Empresa_Usuaria_Inscricao_Estadual']);
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Inscricao_Estadual']);
            $String .= '|';
            
            //-- IE do Substituto Tribut�rio: Informar a IE do ST da UF de destino da mercadoria, quando houver a reten��o do ICMS ST para a UF de destino
            $String .= "";
            $String .= '|';
            
            //-- Inscri��o Municipal: Este campo deve ser informado, quando ocorrer a emiss�o de NF-e conjugada, com presta��o de servi�os
            //-- sujeitos ao ISSQN e fornecimento de pe�as sujeitos ao ICMS.
            $mT007['T007_Inscricao_Municipal'] = str_replace(array(
                ".",
                "/",
                "-",
                " "
            ), "", $mT007['T007_Inscricao_Municipal']);
            $String .= TrocarCaracterEspecial($mT007['T007_Inscricao_Municipal']);
            $String .= '|';
            
            //-- CNAE fiscal: Este campo deve ser informado quando o campo IM for informado
            $String .= $mT007['T007_Empresa_Usuaria_CNAE_Fiscal'];
            $String .= '|';
            
            //-- CNAE fiscal: Este campo deve ser informado quando o campo IM for informado
            $String .= $mT007['T007_Empresa_Usuaria_CNAE_Fiscal'];
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            
            //////////////////////////////////////////////////
            //-- dados do emitente
            //////////////////////////////////////////////////
            $String = 'C02';
            $String .= '|';
            
            //-- CNPJ do emitente
            $mT007['T007_Empresa_Usuaria_Cnpj'] = str_replace(array(
                ".",
                "/",
                "-",
                " "
            ), "", $mT007['T007_Empresa_Usuaria_Cnpj']);
            $String .= $mT007['T007_Empresa_Usuaria_Cnpj'];
            $String .= '|';
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- dados do emitente
            //////////////////////////////////////////////////
            $String = 'C05';
            $String .= '|';
            
            //-- Logradouro
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Endereco']);
            $String .= '|';
            
            //-- N�mero
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Numero']);
            $String .= '|';
            
            //-- Complemento
            $String .= "";
            $String .= '|';
            
            //-- Bairro
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Bairro']);
            $String .= '|';
            
            //-- C�digo do munic�pio:
            //-- Utilizar Tabela do IBGE. Informar �9999999 �para opera��es com o exterior.
            $String .= $mT007['T007_Empresa_Usuaria_Codigo_Cidade_IBGE'];
            $String .= '|';
            
            //-- Nome do munic�pio
            //-- Informar �EXTERIOR �para opera��es com o exterior.
            //-- Para importa��o, ser� considerado o c�digo do munic�pio para apresenta��o do nome do munic�pio.
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Cidade']);
            $String .= '|';
            
            //-- Sigla da UF: Informar �EX �para opera��es com o exterior.
            $String .= TrocarCaracterEspecial($mT007['T007_Empresa_Usuaria_Uf']);
            $String .= '|';
            
            //-- C�digo do CEP: Informar os zeros n�o significativos.
            $String .= TrocarCaracterEspecial(str_replace(array(
                "-",
                " ",
                "(",
                ")",
                "/"
            ), "", $mT007['T007_Empresa_Usuaria_Cep']));
            $String .= '|';
            
            //-- C�digo do Pa�s
            $String .= "1058";
            $String .= '|';
            
            //-- Nome do Pa�s
            $String .= "BRASIL";
            $String .= '|';
            
            //-- Telefone: Preencher com C�digo DDD + n�mero do telefone.
            $String .= str_replace(array(
                "-",
                " ",
                "(",
                ")",
                "/"
            ), "", $mT007['T007_Empresa_Usuaria_Telefone']);
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            
            //////////////////////////////////////////////////
            //-- Destinat�rio
            //////////////////////////////////////////////////
            $String = 'E';
            $String .= '|';
            
            //-- Raz�o Social ou nome do destinat�rio
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Razao']);
            $String .= '|';
            
            //-- Inscri��o Estadual
            //-- Informar a IE quando o destinat�rio for contribuinte do ICMS.
            //-- Informar ISENTO quando o destinat�rio for contribuinte do ICMS, mas n�o estiver obrigado � inscri��o no cadastro de contribuintes do ICMS.
            //-- Vers�o 1.1.1 - (29/10/2008) P�gina 17 de 32
            // N�o informar se o destinat�rio n�o for contribuinte do ICMS.
            $mT007['T007_Cliente_Inscricao_Estadual'] = str_replace(array(
                ".",
                "/",
                "-",
                " "
            ), "", $mT007['T007_Cliente_Inscricao_Estadual']);
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Inscricao_Estadual']);
            $String .= '|';
            
            //-- Inscri��o na SUFRAMA
            //-- Obrigat�rio, nas opera��es que se beneficiam de incentivos fiscais existentes nas �reas sob controle da SUFRAMA.
            $mT007['T007_Cliente_Inscricao_Suframa'] = str_replace(array(
                ".",
                "/",
                "-",
                " "
            ), "", $mT007['T007_Cliente_Inscricao_Suframa']);
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Inscricao_Suframa']);
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            
            //-- CNPJ do destinat�rio
            //-- Informar o CNPJ ou o CPF do destinat�rio, preenchendo os zeros n�o significativos.
            $T007_Cliente_CNPJ_CPF = str_replace(array(
                "-",
                ".",
                "/",
                " "
            ), "", $mT007['T007_Cliente_CNPJ_CPF']);
            
            if (strlen($T007_Cliente_CNPJ_CPF) == '14') {
                $String = 'E02';
                $String .= '|';
                $String .= $T007_Cliente_CNPJ_CPF;
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            }
            
            if (strlen($T007_Cliente_CNPJ_CPF) == '11') {
                $String = 'E03';
                $String .= '|';
                $String .= $T007_Cliente_CNPJ_CPF;
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            }
            
            //////////////////////////////////////////////////
            //-- Endere�o Destinat�rio
            //////////////////////////////////////////////////
            
            $String = 'E05';
            $String .= '|';
            
            // Logradouro
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Endereco']);
            $String .= '|';
            
            // N�mero
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Endereco_Numero']);
            $String .= '|';
            
            // Complemento
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Complemento']);
            $String .= '|';
            
            // Bairro
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Bairro']);
            $String .= '|';
            
            // C�digo do munic�pio
            $String .= $mT007['T007_Cliente_Codigo_Cidade_IBGE'];
            $String .= '|';
            
            // Nome do munic�pio
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Cidade']);
            $String .= '|';
            
            // Sigla da UF
            $String .= TrocarCaracterEspecial($mT007['T007_Cliente_Estado']);
            $String .= '|';
            
            // CEP
            $mT007['T007_Cliente_CEP'] = str_replace(array(
                ".",
                "/",
                "-",
                " "
            ), "", $mT007['T007_Cliente_CEP']);
            $String .= $mT007['T007_Cliente_CEP'];
            $String .= '|';
            
            //-- C�digo do Pa�s
            $String .= "1058";
            $String .= '|';
            
            //-- Nome do Pa�s
            $String .= "BRASIL";
            $String .= '|';
            
            //-- Telefone: Preencher com C�digo DDD + n�mero do telefone.
            $String .= str_replace(array(
                "-",
                " ",
                "(",
                ")",
                "/"
            ), "", $mT007['T007_Cliente_Telefone']);
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            
            $cClausula = " select ";
            $cClausula .= " * ";
            $cClausula .= " from T008 ";
            $cClausula .= " where T008_T007_Id='{$mT007['T007_Id']}' ";
            $cClausula .= " order by T008_Id ";
            $cT008 = mysql_query($cClausula);
            $erro  = mysql_error();
            
            $Sequencia = 0;
            while ($mT008 = mysql_fetch_array($cT008)) {
                
                //////////////////////////////////////////////////
                //-- Detalhamento de produtos e servi�os - m�ximo = 990
                //////////////////////////////////////////////////
                
                $String = 'H';
                $String .= '|';
                
                $Sequencia++;
                $String .= $Sequencia;
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- Produto e servi�o
                //////////////////////////////////////////////////
                
                $String = 'I';
                $String .= '|';
                
                // C�digo do produto ou servi�o
                $String .= $mT008['T008_Codigo_Produto'];
                $String .= '|';
                
                // c�digo de barras
                $String .= "";
                $String .= '|';
                
                // Descri��o do produto ou servi�o
                $String .= TrocarCaracterEspecial($mT008['T008_Descricao_Produto']);
                $String .= '|';
                
                // C�digo NCM
                $mT008['T008_Classificacao_Fiscal'] = str_replace(array(
                    ".",
                    "/",
                    "-",
                    " "
                ), "", $mT008['T008_Classificacao_Fiscal']);
                $String .= $mT008['T008_Classificacao_Fiscal'];
                $String .= '|';
                
                // EX_TIPI
                $String .= "";
                $String .= '|';
                
                // G�nero do Produto ou Servi�o
                $String .= "";
                $String .= '|';
                
                // C�digo Fiscal de Opera��es e Presta��es
                $String .= str_replace(".", "", $mT008['T008_Codigo_CFOP']);
                $String .= '|';
                
                // Unidade Comercial
                $String .= $mT008['T008_Unidade'];
                $String .= '|';
                
                // Quantidade Comercial
                $String .= number_format($mT008['T008_Quantidade'], 4, '.', '');
                $String .= '|';
                
                // Valor Unit�rio de comercializa��o
                $String .= number_format($mT008['T008_Valor_Preco_Unitario'], 4, '.', '');
                $String .= '|';
                
                // Valor Total Bruto dos Produtos ou Servi�os
                $String .= number_format($mT008['T008_Valor_Total_Preco'], 2, '.', '');
                $String .= '|';
                
                // GTIN (Global Trade Item Number) da unidade tribut�vel, antigo c�digo EAN ou c�digo de barras
                $String .= "";
                $String .= '|';
                
                // Unidade Tribut�vel
                $String .= $mT008['T008_Unidade'];
                $String .= '|';
                
                // Quantidade Tribut�vel
                $String .= number_format($mT008['T008_Quantidade'], 4, '.', '');
                $String .= '|';
                
                // Valor Unit�rio de tributa��o
                $String .= number_format($mT008['T008_Valor_Preco_Unitario'], 4, '.', '');
                $String .= '|';
                
                // Valor Total do Frete
                $String .= "";
                $String .= '|';
                
                // Valor Total do Seguro
                $String .= "";
                $String .= '|';
                
                // Valor do Desconto
                $String .= "";
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- Grupo de Tributos incidentes no Produto ou Servi�o
                //////////////////////////////////////////////////
                
                $String = 'M';
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- ICMS
                //////////////////////////////////////////////////
                
                $String = 'N';
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                $Origem_Mercadoria   = substr($mT008['T008_Flag_Situacao_Tributaria'], 0, 1);
                $Situacao_Tributaria = substr($mT008['T008_Flag_Situacao_Tributaria'], 1, 2);
                
                if ($Situacao_Tributaria == "00") {
                    $String = 'N02';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS
                    $String .= "0";
                    $String .= '|';
                    
                    // Valor da BC do ICMS
                    $String .= number_format($mT008['T008_Valor_Base_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto
                    $String .= number_format($mT008['T008_Aliquota_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS
                    $String .= number_format($mT008['T008_Valor_ICMS'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                    
                    
                } elseif ($Situacao_Tributaria == "10") {
                    $String = 'N03';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS
                    $String .= "0";
                    $String .= '|';
                    
                    // Valor da BC do ICMS
                    $String .= number_format($mT008['T008_Valor_Base_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto
                    $String .= number_format($mT008['T008_Aliquota_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS
                    $String .= number_format($mT008['T008_Valor_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS ST
                    $String .= "4";
                    $String .= '|';
                    
                    // Percentual da margem de valor Adicionado do ICMS ST
                    $String .= "";
                    $String .= '|';
                    
                    // Valor da BC do ICMS ST
                    $String .= number_format($mT008['T008_Valor_Base_ICMS_Substituicao'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto do ICMS ST
                    $String .= number_format($mT008['T008_ICMS_Inter_Estadual'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS ST
                    $String .= number_format($mT008['T008_Valor_ICMS_Substituicao_Retencao'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                } elseif ($Situacao_Tributaria == "20") {
                    $String = 'N04';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS
                    $String .= "0";
                    $String .= '|';
                    
                    // Percentual da Redu��o de BC
                    $String .= number_format($mT008['T008_Percentual_Reducao_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor da BC do ICMS
                    $String .= number_format($mT008['T008_Valor_Base_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto
                    $String .= number_format($mT008['T008_Aliquota_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS
                    $String .= number_format($mT008['T008_Valor_ICMS'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                } elseif ($Situacao_Tributaria == "30") {
                    $String = 'N05';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS ST
                    $String .= "4";
                    $String .= '|';
                    
                    // Percentual da margem de valor Adicionadodo ICMS ST
                    $String .= "";
                    $String .= '|';
                    
                    // Percentual da Redu��o de BC do ICMS ST
                    $String .= "";
                    $String .= '|';
                    
                    // Valor da BC do ICMS ST
                    $String .= number_format($mT008['T008_Valor_Base_ICMS_Substituicao'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto do ICMS ST
                    $String .= number_format($mT008['T008_ICMS_Inter_Estadual'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS ST
                    $String .= number_format($mT008['T008_Valor_ICMS_Substituicao_Retencao'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                } elseif ($Situacao_Tributaria == "40" or $Situacao_Tributaria == "41" or $Situacao_Tributaria == "50") {
                    $String = 'N06';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                } elseif ($Situacao_Tributaria == "51") {
                    $String = 'N07';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS
                    $String .= "0";
                    $String .= '|';
                    
                    // Percentual da Redu��o de BC
                    $String .= number_format($mT008['T008_Percentual_Reducao_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor da BC do ICMS
                    $String .= number_format($mT008['T008_Valor_Base_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto
                    $String .= number_format($mT008['T008_Aliquota_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS
                    $String .= number_format($mT008['T008_Valor_ICMS'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                } elseif ($Situacao_Tributaria == "60") {
                    $String = 'N08';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Valor da BC do ICMS ST
                    $String .= number_format($mT008['T008_Valor_Base_ICMS_Substituicao'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS ST
                    $String .= number_format($mT008['T008_Valor_ICMS_Substituicao_Retencao'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                } elseif ($Situacao_Tributaria == "70") {
                    $String = 'N09';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS
                    $String .= "0";
                    $String .= '|';
                    
                    // Percentual da Redu��o de BC
                    $String .= number_format($mT008['T008_Percentual_Reducao_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor da BC do ICMS
                    $String .= number_format($mT008['T008_Valor_Base_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto
                    $String .= number_format($mT008['T008_Aliquota_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS
                    $String .= number_format($mT008['T008_Valor_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS ST
                    $String .= "4";
                    $String .= '|';
                    
                    // Percentual da margem de valor Adicionadodo ICMS ST
                    $String .= "";
                    $String .= '|';
                    
                    // Percentual da Redu��o de BC do ICMS ST
                    $String .= "";
                    $String .= '|';
                    
                    // Valor da BC do ICMS ST
                    $String .= number_format($mT008['T008_Valor_Base_ICMS_Substituicao'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto do ICMS ST
                    $String .= number_format($mT008['T008_ICMS_Inter_Estadual'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS ST
                    $String .= number_format($mT008['T008_Valor_ICMS_Substituicao_Retencao'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                } elseif ($Situacao_Tributaria == "90") {
                    $String = 'N10';
                    $String .= '|';
                    
                    // Origem_Mercadoria
                    $String .= $Origem_Mercadoria;
                    $String .= '|';
                    
                    // Tributa��o do ICMS
                    $String .= $Situacao_Tributaria;
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS
                    $String .= "0";
                    $String .= '|';
                    
                    // Percentual da Redu��o de BC
                    $String .= number_format($mT008['T008_Percentual_Reducao_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor da BC do ICMS
                    $String .= number_format($mT008['T008_Valor_Base_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto
                    $String .= number_format($mT008['T008_Aliquota_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS
                    $String .= number_format($mT008['T008_Valor_ICMS'], 2, '.', '');
                    $String .= '|';
                    
                    // Modalidade de determina��o da BC do ICMS ST
                    $String .= "4";
                    $String .= '|';
                    
                    // Percentual da margem de valor Adicionadodo ICMS ST
                    $String .= "";
                    $String .= '|';
                    
                    // Percentual da Redu��o de BC do ICMS ST
                    $String .= "";
                    $String .= '|';
                    
                    // Valor da BC do ICMS ST
                    $String .= number_format($mT008['T008_Valor_Base_ICMS_Substituicao'], 2, '.', '');
                    $String .= '|';
                    
                    // Al�quota do imposto do ICMS ST
                    $String .= number_format($mT008['T008_ICMS_Inter_Estadual'], 2, '.', '');
                    $String .= '|';
                    
                    // Valor do ICMS ST
                    $String .= number_format($mT008['T008_Valor_ICMS_Substituicao_Retencao'], 2, '.', '');
                    
                    fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                    
                }
                
                //////////////////////////////////////////////////
                //-- IPI - Informar apenas quando o item for sujeito ao IPI
                //////////////////////////////////////////////////
                
                $String = 'O';
                $String .= '|';
                
                // Classe de enquadramento do IPI para Cigarros e Bebidas
                $String .= "";
                $String .= '|';
                
                // CNPJ do produtor da mercadoria, quando diferente do emitente. Somente para os casos de exporta��o direta ou indireta.
                $String .= "";
                $String .= '|';
                
                // C�digo do selo de controle IPI
                $String .= "";
                $String .= '|';
                
                // Quantidade de selo de controle
                $String .= "";
                $String .= '|';
                
                // C�digo de Enquadramento Legal do IPI
                $String .= "999";
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- IPI Tribut�vel
                //////////////////////////////////////////////////
                $String = 'O07';
                $String .= '|';
                
                // C�digo da situa��o tribut�ria do IPI
                //-- 00 a Entrada com recupera��o de cr�dito
                //-- 49-Outras entradas
                //-- 50 a Sa�da tributada
                //-- 99-Outras sa�das
                $String .= '00';
                $String .= '|';
                
                // Valor do IPI
                $String .= number_format($mT008['T008_Valor_IPI'], 2, '.', '');
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- IPI Tribut�vel
                //////////////////////////////////////////////////
                
                $String = 'O10';
                $String .= '|';
                
                // Valor da BC do IPI
                $String .= number_format($mT008['T008_Valor_Base_IPI'], 2, '.', '');
                $String .= '|';
                
                // Al�quota do IPI
                $String .= number_format($mT008['T008_Aliquota_IPI'], 2, '.', '');
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- PIS
                //////////////////////////////////////////////////
                
                $String = 'Q';
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- PIS - grupo de PIS Outras Opera��es
                //////////////////////////////////////////////////
                
                $String = 'Q05';
                $String .= '|';
                
                // C�digo de Situa��o Tribut�ria do PIS
                $String .= "99";
                $String .= '|';
                
                // Valor do PIS
                $String .= number_format($mT008['T008_Valor_PIS'], 2, '.', '');
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- PIS - grupo de PIS Outras Opera��es
                //////////////////////////////////////////////////
                
                $String = 'Q07';
                $String .= '|';
                
                // Valor da Base de C�lculo do PIS
                $String .= number_format($mT008['T008_Valor_Base_PIS'], 2, '.', '');
                $String .= '|';
                
                // Al�quota do PIS (em percentual)
                $String .= number_format($mT008['T008_Aliquota_PIS'], 2, '.', '');
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- COFINS
                //////////////////////////////////////////////////
                
                $String = 'S';
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- COFINS - grupo de COFINS Outras Opera��es
                //////////////////////////////////////////////////
                
                $String = 'S05';
                $String .= '|';
                
                // C�digo de Situa��o Tribut�ria do COFINS
                $String .= "99";
                $String .= '|';
                
                // Valor do COFINS
                $String .= number_format($mT008['T008_Valor_COFINS'], 2, '.', '');
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                //////////////////////////////////////////////////
                //-- COFINS - grupo de COFINS Outras Opera��es
                //////////////////////////////////////////////////
                
                $String = 'S07';
                $String .= '|';
                
                // Valor da Base de C�lculo da COFINS
                $String .= number_format($mT008['T008_Valor_Base_COFINS'], 2, '.', '');
                $String .= '|';
                
                // Al�quota da COFINS (em percentual)
                $String .= number_format($mT008['T008_Aliquota_COFINS'], 2, '.', '');
                
                fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
                
                
                
            }
            
            //////////////////////////////////////////////////
            //-- Totais
            //////////////////////////////////////////////////
            
            $String = 'W';
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- Grupo de Valores Totais referentes ao ICMS
            //////////////////////////////////////////////////
            
            $String = 'W02';
            $String .= '|';
            
            // Base de C�lculo do ICMS
            $String .= number_format($mT007['T007_Valor_Total_Base_ICMS_Impresso'], 2, '.', '');
            $String .= '|';
            
            // Valor Total do ICMS
            $String .= number_format($mT007['T007_Valor_Total_ICMS'], 2, '.', '');
            $String .= '|';
            
            // Base de C�lculo do ICMS ST
            $String .= number_format($mT007['T007_Valor_Base_ICMS_Substituicao'], 2, '.', '');
            $String .= '|';
            
            // Valor Total do ICMS ST
            $String .= number_format($mT007['T007_Valor_ICMS_Substituicao_Retencao'], 2, '.', '');
            $String .= '|';
            
            // Valor Total dos produtos e servi�os
            $String .= number_format($mT007['T007_Valor_Total_Produtos'], 2, '.', '');
            $String .= '|';
            
            // Valor Total do Frete
            $String .= number_format($mT007['T007_Valor_Frete'], 2, '.', '');
            $String .= '|';
            
            // Valor Total do Seguro
            $String .= number_format(0, 2, '.', '');
            $String .= '|';
            
            
            // Valor Total do Desconto
            $String .= number_format($mT007['T007_Valor_Desconto'], 2, '.', '');
            $String .= '|';
            
            // Valor Total do II
            $String .= "0.00";
            $String .= '|';
            
            
            // Valor Total do IPI
            $String .= number_format($mT007['T007_Valor_Total_IPI'], 2, '.', '');
            $String .= '|';
            
            // Valor do PIS
            $String .= number_format($mT007['T007_Valor_Total_PIS'], 2, '.', '');
            $String .= '|';
            
            // Valor do COFINS
            $String .= number_format($mT007['T007_Valor_Total_COFINS'], 2, '.', '');
            $String .= '|';
            
            // Outras Despesas acess�rias
            $String .= number_format($mT007['T007_Valor_Despesas_Acessorias'], 2, '.', '');
            $String .= '|';
            
            // Valor Total da NF-e
            $String .= number_format($mT007['T007_Valor_Total'], 2, '.', '');
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- Transporte
            //////////////////////////////////////////////////
            
            $String = 'X';
            $String .= '|';
            
            // Modalidade do frete
            $String .= "0";
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- Cobran�a
            //////////////////////////////////////////////////
            
            $String = 'Y';
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- Duplicata
            //////////////////////////////////////////////////
            
            $String = 'Y07';
            $String .= '|';
            
            // N�mero da Duplicata
            $String .= "";
            $String .= '|';
            
            // Data de vencimento
            $String .= "";
            $String .= '|';
            
            // Valor da duplicata
            $String .= "";
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
            
            //////////////////////////////////////////////////
            //-- Informa��es Adicionais
            //////////////////////////////////////////////////
            
            $String = 'Z';
            $String .= '|';
            
            // Informa��es Adicionais de Interesse do Fisco
            $String .= "";
            $String .= '|';
            
            // Informa��es Complementares de interesse do Contribuinte
            $String .= "";
            
            fwrite($cArquivo_Aberto, $String . _SALTO_LINHA);
        }
        
        fclose($cArquivo_Aberto);
        // $Ponteiro=fopen($cArquivo, "r");
        // $Conteudo=fread($Ponteiro, filesize($cArquivo));
        return array(
            "$cArquivo"
        );
    }
}
