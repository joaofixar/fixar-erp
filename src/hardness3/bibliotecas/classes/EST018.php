<?php
namespace hardness;
require_once("old/lib/funcoes.php");
definirClasse('EST018');
class EST018_
{

    /**
     * exportarXMLNfe
     *
     * @param Form $form
     * @return String/bool
     */ 	
    public function exportarXMLConhecimentos($form)
    {
        global $g;
        $Diretorio_Usuario = $g['pathDadosUsuario'];
        $Campo_Data        = ($form->campoValorEnviado('Campo_Data') == '1') ? 'T084_Data_Emissao' : 'T084_Data_Lancamento' ;
        $Data_Inicio       = $form->campoValorEnviado('Data_Inicio');
        $Data_Fim          = $form->campoValorEnviado('Data_Fim');
        $C004_Id           = $form->campoValorEnviado('T084_C004_Id');//Empresa
        $D022_Id           = $form->campoValorEnviado('T084_D022_Id');//Transportadora
        
        if (!extension_loaded('zip')) {
            return 'Nao esta habilitado php_zip, edite seu php.ini e descomente essa linha, se nao existir basta cria-la: extension=php_zip';
        }
        
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

        $cClausula = " SELECT T084_Numero_Protocolo_Cte, T084_Chave_Acesso_Cte, T084_Numero_Conhecimento ";
        $cClausula .= " FROM T084 ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id = T084_D024_Id ";
        $cClausula .= " WHERE T084_C004_Id='{$C004_Id}' ";
        $cClausula .= " AND T084_Flag_Entrada_Saida != 'S' "; 
        $cClausula .= " AND {$Campo_Data} >= '{$dData_Inicio}' ";
        $cClausula .= " AND {$Campo_Data} <= '{$dData_Fim}' ";
        $cClausula .= " AND ifnull(T084_Numero_Protocolo_Cte,'') != '' ";

        if($D022_Id>0){
            $cClausula .= " and T084_D022_Id='{$D022_Id}' ";
        }

        $cClausula .= " order by T084_Id,T084_Data_Emissao";
        
        $cT084 = mysql_query($cClausula);
        $erro  = mysql_error();
        
        if (mysql_num_rows($cT084) == 0) {
            return "Não encontrado conhecimentos entre as datas selecionadas";
        } 

        
        $Nome_Pasta = $Diretorio_Usuario . "arq_temp/";
        if (file_exists($Nome_Pasta)) {
            $this->recursiveDelete($Nome_Pasta);
        }
        
        if (!is_dir("$Nome_Pasta")) {
            //-- CRIA A PASTA CASO ELA AINDA NAO EXISTA
            if (!mkdir("$Nome_Pasta", 0777)) {
                return "Erro ao criar a pasta: <b>$Nome_Pasta</b>";
            }
            chmod("$Nome_Pasta", 0777);
        }
        
        $Nome_Pasta = $Diretorio_Usuario . "/arq_temp/xml_recebidos";
        if (@!is_dir("$Nome_Pasta")) {
            //-- CRIA A PASTA CASO ELA AINDA NAO EXISTA
            if (!@mkdir("$Nome_Pasta", 0777)) {
                return "Erro ao criar a pasta: <b>$Nome_Pasta</b>";
            }
            chmod("$Nome_Pasta", 0777);
        }
        
        $cArquivo_ZIP = "$cData_Inicio" . "_a_$cData_Fim" . "_Conhecimentos_Empresa_$C004_Id" . ".zip";
        if (file_exists("$Nome_Pasta/$cArquivo_ZIP")) {
            unlink("$Nome_Pasta/$cArquivo_ZIP");
        }
        
        $Diretorio_Conhecimentos = $g['pathDadosAntigo'] . 'cte';
        
        $zip = new \ZipArchive();
        if ($zip->open("$Nome_Pasta/$cArquivo_ZIP", \ZIPARCHIVE::CREATE) !== TRUE) {
            return "Impossivel abrir <$Diretorio_Conhecimentos/xml/recebido/$cArquivo_ZIP>";
        }
        
        while ($mT084 = mysql_fetch_array($cT084)) {
            $Chave_Acesso = $mT084['T084_Chave_Acesso_Cte'];
            $Protocolo = $mT084['T084_Numero_Protocolo_Cte'];
            $Diretorio_CTe = $g['pathDadosAntigo'] . "cte/xml/recebido/" . $Chave_Acesso . "-" . $Protocolo . ".xml";
            if (file_exists($Diretorio_CTe)) {
                $Novo_Arq     = "{$mT084['T084_Numero_Conhecimento']}.xml";
                $Novo_Arquivo = "$Nome_Pasta/$Novo_Arq";
                
                if (!@copy($Diretorio_CTe, $Novo_Arquivo)) {
                    $mErro[] = "FALHA AO COPIAR O ARQUIVO! ORIDEM: $Diretorio_CTe DESTINO: $Novo_Arquivo";
                } else {
                    $nCopiados++;
                }
            } else {
                $mErro[] = "XML NAO EXISTE! Conhecimento:{$mT084['T084_Numero_Conhecimento']} PROTOCOLO:{$mT084['T084_Numero_Protocolo_Cte']}";
            }
            $zip->addFile($Novo_Arquivo, "$Novo_Arq");
        }
        
        $zip->close();
        
        if (count($mErro) > 0) {
            return "Ocorreram os sequintes erros: <br />" . str_replace("/p/p", "<br />", implode("/p/p", $mErro));
        }
        
        if ($nCopiados > 0) {
            return array(
                "/hardness3/$Nome_Pasta/$cArquivo_ZIP"
            );
            echo "<iframe src='../xml_conhecimentos/zipar_xml.php?cArquivo_ZIP=$Nome_Pasta/$cArquivo_ZIP' style='display:inline;'></iframe>";
        }
        return "Erro! Por favor, contato o suporte técnico";
    }

        /**
     * exportarXMLNfe
     *
     * @param Form $form
     * @return String/bool
     */ 	
    public function exportarXMLConhecimentosEmitidos($form)
    {
        global $g;
        $Diretorio_Usuario = $g['pathDadosUsuario'];
        $Campo_Data        = ($form->campoValorEnviado('Campo_Data') == '1') ? 'T084_Data_Emissao' : 'T084_Data_Lancamento' ;
        $Data_Inicio       = $form->campoValorEnviado('Data_Inicio');
        $Data_Fim          = $form->campoValorEnviado('Data_Fim');
        $C004_Id           = $form->campoValorEnviado('T084_C004_Id');//Empresa
        $D022_Id           = $form->campoValorEnviado('T084_D022_Id');//Transportadora
        
        if (!extension_loaded('zip')) {
            return 'Nao esta habilitado php_zip, edite seu php.ini e descomente essa linha, se nao existir basta cria-la: extension=php_zip';
        }
        
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

        $cClausula = " SELECT T084_Numero_Protocolo_Cte, T084_Chave_Acesso_Cte, T084_Numero_Conhecimento,T084_Data_Emissao ";
        $cClausula .= " FROM T084 ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id = T084_D024_Id ";
        $cClausula .= " where T084_C004_Id='$C004_Id' ";
        $cClausula .= " AND T084_Flag_Entrada_Saida = 'S' "; 
        $cClausula .= " AND {$Campo_Data} >= '{$dData_Inicio}' ";
        $cClausula .= " AND {$Campo_Data} <= '{$dData_Fim}' ";
        $cClausula .= " AND ifnull(T084_Numero_Protocolo_Cte,'') != '' ";

        if($D022_Id>0){
            $cClausula .= " and T084_D022_Id='{$D022_Id}' ";
        }

        $cClausula .= " order by T084_Id,T084_Data_Emissao";
        
        $cT084 = mysql_query($cClausula);
        $erro  = mysql_error();
        
        if (mysql_num_rows($cT084) == 0) {
            return "Não encontrado conhecimentos entre as datas selecionadas";
        }
        
        
        $Nome_Pasta = $Diretorio_Usuario . "arq_temp/";
        if (file_exists($Nome_Pasta)) {
            error_log("vai pagar antes recursive  : ".$Nome_Pasta);
            $this->recursiveDelete($Nome_Pasta);
        }
        
        if (!is_dir("$Nome_Pasta")) {
            //-- CRIA A PASTA CASO ELA AINDA NAO EXISTA
            if (!mkdir("$Nome_Pasta", 0777)) {
                return "Erro ao criar a pasta: <b>$Nome_Pasta</b>";
            }
            chmod("$Nome_Pasta", 0777);
        }
        
        $Nome_Pasta = $Diretorio_Usuario . "/arq_temp/xml_cte_emitidos";
        if (@!is_dir("$Nome_Pasta")) {
            //-- CRIA A PASTA CASO ELA AINDA NAO EXISTA
            if (!@mkdir("$Nome_Pasta", 0777)) {
                return "Erro ao criar a pasta: <b>$Nome_Pasta</b>";
            }
            chmod("$Nome_Pasta", 0777);
        }
        
        $cArquivo_ZIP = "$cData_Inicio" . "_a_$cData_Fim" . "_Conhecimentos_Empresa_$C004_Id" . ".zip";
        if (file_exists("$Nome_Pasta/$cArquivo_ZIP")) {
            unlink("$Nome_Pasta/$cArquivo_ZIP");
        }
        
        $Diretorio_Conhecimentos = $g['pathDadosAntigo'] . 'cte';
        
        $zip = new \ZipArchive();
        if ($zip->open("$Nome_Pasta/$cArquivo_ZIP", \ZIPARCHIVE::CREATE) !== TRUE) {
            return "Impossivel abrir <$Diretorio_Conhecimentos/xml/recebido/$cArquivo_ZIP>";
        }
        
        while ($mT084 = mysql_fetch_array($cT084)) {
            $cAno_Mes_NFe = substr($mT084['T084_Data_Emissao'], 0, 7);

            $Diretorio_CTe = "{$g['pathDadosAntigo']}cte/xml/autorizado/{$cAno_Mes_NFe}/{$mT084['T084_Numero_Protocolo_Cte']}.xml";

            if (file_exists($Diretorio_CTe)) {
                $Novo_Arq     = "{$mT084['T084_Numero_Conhecimento']}.xml";
                $Novo_Arquivo = "$Nome_Pasta/$Novo_Arq";
                
                if (!@copy($Diretorio_CTe, $Novo_Arquivo)) {
                    $mErro[] = "FALHA AO COPIAR O ARQUIVO! ORIDEM: $Diretorio_CTe DESTINO: $Novo_Arquivo";
                } else {
                    $nCopiados++;
                }
            } else {
                $mErro[] = "XML NAO EXISTE! Conhecimento:{$mT084['T084_Numero_Conhecimento']} PROTOCOLO:{$mT084['T084_Numero_Protocolo_Cte']}";
            }
            $zip->addFile($Novo_Arquivo, "$Novo_Arq");
        }
        
        $zip->close();
        
        if (count($mErro) > 0) {
            return "Ocorreram os sequintes erros: <br />" . str_replace("/p/p", "<br />", implode("/p/p", $mErro));
        }
        
        if ($nCopiados > 0) {
            return array(
                "/hardness3/$Nome_Pasta/$cArquivo_ZIP"
            );
            echo "<iframe src='../xml_conhecimentos/zipar_xml.php?cArquivo_ZIP=$Nome_Pasta/$cArquivo_ZIP' style='display:inline;'></iframe>";
        }
        return "Erro! Por favor, contato o suporte técnico";
    }
    
    
    /**
     * recursiveDelete
     *
     * @param String $str
     * @return bool
     */     
    public function recursiveDelete($str)
    {
        if (is_file($str)) {
            //return "isfile";
            return @unlink($str);
            
        } elseif (is_dir($str)) {
            $scan = glob(rtrim($str, '/') . '/*');
            foreach ($scan as $index => $path) {
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }    
}
