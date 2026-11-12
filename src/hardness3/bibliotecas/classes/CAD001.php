<?php
namespace hardness;
definirClasse('CAD001');
class CAD001_
{
    /**
     * montarEmail
     * 
     * @param string $D024_Id
     * @return array
     */
    public function montarEmail($D024_Id)
    {
        $D024_Id = mysql_real_escape_string($D024_Id);
        $sql     = "SELECT * FROM D024 WHERE D024_Id='{$D024_Id}'";
        $D024    = mysql_query($sql);
        $D024    = mysql_fetch_array($D024);
        
        $array['Para']    = $D024['D024_E_Mail'];
        $array['Assunto'] = "";
        $array['Corpo']   = <<<EOT
            {$D024['D024_Nome_Empresa']},<br />
            <br /><br />
EOT;
        
        return $array;
    }
    
    /**
     * insereRegistrosT165
     * 
     * @param string $D116_Id
     */
    public function insereRegistrosT165($D116_Id)
    {
        $sql = "SELECT D131_Id FROM D131";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T165_Id = mysql_query("SELECT T165_Id FROM T165 WHERE T165_D131_Id='{$row['D131_Id']}' AND T165_D116_Id='$D116_Id'");
            $mT165   = mysql_fetch_array($T165_Id);
            
            if (mysql_num_rows($T165_Id) <= 0) {
                mysql_query("INSERT INTO T165 (T165_D131_Id,T165_D116_Id)
                                       VALUES ('{$row['D131_Id']}','$D116_Id')");
            }
            
        }
    }

    /**
     * imprimirEtiquetas
     * 
     * @param Form $form
     * @return string
     */  
    public function imprimirEtiquetas($form)
    {
        //$D024_Id_Inicial = $form->campoValorEnviado('D024_Id_Inicial');
        //$D024_Id_Final = $form->campoValorEnviado('D024_Id_Final');
        $D024_Classes_Ativos   = $form->campoValorEnviado('D024_Classes_Ativos');
        $D024_Classes_Inativos = $form->campoValorEnviado('D024_Classes_Inativos');
        $quantidade            = $form->campoValorEnviado('Quantidade');
        
        if (trim($D024_Classes_Ativos) != "" && strtoupper((trim($D024_Classes_Ativos))) != "N") {
            $classesAtivos = explode(",", $D024_Classes_Ativos);
            $strAtivos     = "(D024_Flag_Comercial='A' AND (";
            foreach ($classesAtivos as $ativo) {
                $ativo = strtoupper($ativo);
                $strAtivos .= " D024_Classe='$ativo' OR ";
            }
            $strAtivos .= " 1=2 ) ) ";
        } else {
            $strAtivos = " (1=2) ";
        }
        if (strtoupper(trim($D024_Classes_Ativos)) == "T") {
            $strAtivos = "(D024_Flag_Comercial='A')";
        }
        
        if (trim($D024_Classes_Inativos) != "" && strtoupper(trim($D024_Classes_Inativos)) != "N") {
            $classesInativos = explode(",", $D024_Classes_Inativos);
            $strInativos     = "(D024_Flag_Comercial='I' AND (";
            foreach ($classesInativos as $inativo) {
                $inativo = strtoupper($inativo);
                $strInativos .= " D024_Classe='$inativo' OR ";
            }
            $strInativos .= " 1=2 ) )";
        } else {
            $strInativos = " (1=2) ";
        }
        if (strtoupper(trim($D024_Classes_Inativos)) == "T") {
            $strInativos = "(D024_Flag_Comercial='I')";
        }
        
        $stringAI = $strAtivos . ' OR ' . $strInativos;
        
        /*
        if(empty($D024_Id_Inicial) && empty($D024_Id_Final)){
        return 'O campos não podem ser vazios';
        }
        if(empty($D024_Id_Inicial)){
        return 'O campo "Id Cliente Inicial" não pode ser vazio';
        }
        if(empty($D024_Id_Final)){
        return 'O campo "Id Cliente Final" não pode ser vazio';
        }
        if($D024_Id_Inicial > $D024_Id_Final){
        return 'O Código do cliente inicial não pode ser maior que o Código do cliente final';
        }
        
        
        $sql="SELECT D024_Id,
        D024_Nome_Empresa,
        D024_Nome_Contato,
        D024_Endereco,
        D024_Numero,
        D024_Cep,
        D024_Bairro,
        D020_Nome_Cidade,
        D018_UF 
        FROM D024 
        LEFT JOIN D020 ON D020_Id=D024_D020_Id 
        LEFT JOIN D018 ON D018_Id=D020_D018_Id 
        WHERE D024_Id >= '$D024_Id_Inicial}' 
        AND D024_Id <= '$D024_Id_Final'
        AND D024_Flag_Cliente='S'
        AND D024_Endereco!=''
        AND D024_Flag_Ativo='S'
        AND D020_Id not is null
        AND D018_Id not is null
        AND D024_Cep!=''";
        */
        $sql = <<<EOT
            SELECT 
                D024_Id,
                D024_Nome_Empresa,
                D024_Nome_Contato,
                D024_Endereco,
                D024_Numero,
                D024_Cep,
                D024_Bairro,
                D020_Nome_Cidade,
                D018_UF 
            FROM
                D024 
                LEFT JOIN D020 ON D020_Id = D024_D020_Id 
                LEFT JOIN D018 ON D018_Id = D020_D018_Id 
            WHERE ('{$stringAI}') 
                AND D024_Flag_Cliente = 'S' 
                AND D024_Endereco != '' 
                AND D024_Flag_Ativo = 'S' 
                AND D020_Id IS NOT NULL 
                AND D018_Id IS NOT NULL 
                AND D024_Cep != '' 
EOT;
        
        // CRV Only
        $sql .= " AND D024_C007_Vendedor_Interno!=64";
        
        if (trim($quantidade) != "")
            $sql .= " LIMIT $quantidade";
        
        $resultado = mysql_query($sql);
        $total     = mysql_num_rows($resultado);
        
        $txtEtiquetaCliente = array();
        
        while ($row = mysql_fetch_assoc($resultado)) {
            $row['D024_Nome_Empresa'] = gLimpaAcentos($row['D024_Nome_Empresa']);
            $row['D024_Nome_Contato'] = gLimpaAcentos($row['D024_Nome_Contato']);
            $row['D024_Endereco']     = gLimpaAcentos($row['D024_Endereco']);
            $row['D024_Numero']       = gLimpaAcentos($row['D024_Numero']);
            $row['D020_Nome_Cidade']  = gLimpaAcentos($row['D020_Nome_Cidade']);
            $row['D018_UF']           = gLimpaAcentos($row['D018_UF']);
            $row['D024_Bairro']       = gLimpaAcentos($row['D024_Bairro']);
            $txtEtiquetaCliente[]     = <<<EOT
^xa
^LH10,10
^FO50,50^A0N30,30,^FD{$row['D024_Nome_Contato']}^FS
^FO50,100^A0N30,30,^FD{$row['D024_Nome_Empresa']}^FS
^FO50,150^A0N30,30,^FD{$row['D024_Endereco']}, {$row['D024_Numero']}^FS
^FO50,200^A0N30,30,^FD{$row['D020_Nome_Cidade']} - {$row['D018_UF']}^FS
^FO50,250^A0N30,30,^FD{$row['D024_Bairro']} - {$row['D024_Cep']}^FS
^PQ1,0,1,Y^XZ
EOT;
        }
        
        $cArquivo = "/tmp/etiqueta.txt";
        
        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }
        
        if (!file_put_contents($cArquivo, implode("\n", $txtEtiquetaCliente))) {
            return "Não foi possivel gravar o arquivo $cArquivo";
        }
        
        exec("echo `lpr -Petiqueta $cArquivo`;");
        
        return $total;
    }
    
    /**
     * CAD001montarPdfGridClientes
     *
     * @param String (query) $sqlD024
     * @return array
     */
    public function CAD001montarPdfGridClientes($sqlD024)
    {
        global $g, $confUsuario;
        
        $resultD024 = mysql_query($sqlD024);
        
        $sqlC004    = "SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $C004       = mysql_fetch_array($resultC004);
        
        $sqlC007    = "SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
        $resultC007 = mysql_query($sqlC007);
        $C007       = mysql_fetch_array($resultC007);
        
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        
        // Array de retorno para criação do e-mail
        $array            = array();
        $array['Para']    = "";
        $array['Assunto'] = "Clientes";
        
        /**
         * PDF
         */
        require_once('bibliotecas/tcpdf/config/lang/eng.php');
        require_once('bibliotecas/tcpdf/tcpdf.php');
        
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hardness Sistemas');
        // remove default header/footer
        $pdf->setPrintHeader(false);
        //$pdf->setPrintFooter(true);
        $pdf->SetMargins(8, 8, 8, 8);
        // set font
        $pdf->SetFont('helvetica', '', 7);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Orientação da página
        $pdf->setPageOrientation('P');
        // add a page
        $pdf->AddPage();
        
        $html = '';
        /**
         * PDF - STYLE
         */
        $html .= <<<EOT
                <style>
                    .header .text {
                        font-size: 25px;
                    }
                    .header .text2 {
                        font-size: 35px;
                        font-weight: bold;
                    }
                    .titulo {
                        background-color: #E1ECF1;
                    }
                    .titulo_superior {
                        font-size: 30px;
                    }
                    .itens td {
                        border: 1px solid black;
                    }
                    .textIncluso {
                        font-size: 18px;
                        color: red;
                        background-color: #E1ECF1;
                    }
                    
                </style>
EOT;
        
        /**
         * PDF - HEADER
         */
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        $html .= <<<EOT
        <table style='height:10px;border:1px solid black;'>
            <tr>
                <td align="left">{$g['c004']['C004_Nome_Empresa']}</td>
                <td align="right" style="font-size: 230%;"><b>Clientes</b></td>
            </tr>
        </table>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        $html .= <<<EOT
            <div style="height:20px; width:100%;"></div>
                    <table class="itens" cellspacing="0" cellpadding="2">
                        <tr class="titulo">
                            <td style="width: 8%"; align="center">Código</td>
                            <td style="width: 35%"; align="left">Cliente</td>
                            <td style="width: 13%"; align="left">Telefone</td>
                            <td style="width: 19%"; align="left">Cidade</td>
                            <td style="width: 7%"; align="center">UF</td>
                            <td style="width: 18%"; align="center">CPF/CNPJ</td>
                        </tr>
                    </table>
EOT;
        
        $html .= <<<EOT
        <table class="itens" cellspacing="0" cellpadding="2">
EOT;
        while ($rowD024 = mysql_fetch_array($resultD024)) {
            if (empty($rowD024['D024_CNPJ'])) {
                $documentoCliente = $rowD024['D024_CPF'];
            } else {
                $documentoCliente = $rowD024['D024_CNPJ'];
            }
            $rowD024['D024_Nome_Empresa'] = substr($rowD024['D024_Nome_Empresa'], 0, 30);
            $rowD024['D020_Nome_Cidade']  = substr($rowD024['D020_Nome_Cidade'], 0, 20);
            $D013_DDD_Telefone_1          = $rowD024['D013_DDD_Telefone_1'];
            $D013_Telefone_1              = $rowD024['D013_Telefone_1'];
            if (!empty($D013_DDD_Telefone_1)) {
                $telefoneCliente = "({$D013_DDD_Telefone_1}) {$D013_Telefone_1}";
            } else {
                $telefoneCliente = "{$D013_Telefone_1}";
            }
            $html .= <<<EOT
            
                <tr>
                    <td style="width: 8%"; align="center">{$rowD024['D024_Id']}</td>
                    <td style="width: 35%"; align="left">{$rowD024['D024_Nome_Empresa']}</td>
                    <td style="width: 13%"; align="left">{$telefoneCliente}</td>
                    <td style="width: 19%"; align="left">{$rowD024['D020_Nome_Cidade']}</td>
                    <td style="width: 7%"; align="center">{$rowD024['D018_UF']}</td>
                    <td style="width: 18%"; align="center">{$documentoCliente}</td>
                </tr>
                
EOT;
            
        }
        $html .= '</table>';
        
        /**
         * PDF - GERA!
         */
        $pdf->writeHTML($html, true, false, true, false, 'C');
        // reset pointer to the last page
        $pdf->lastPage();
        // get data
        $data = $pdf->getPDFData();
        
        // Gravar arquivo no disco
        $anexoDir = $g['pathDados'] . 'tmp/';
        is_dir($anexoDir) or mkdir($anexoDir, 0777, true);
        $anexoNome = "{$anexoDir}clientesLista.pdf";
        @unlink($anexoNome);
        $h = fopen($anexoNome, 'w');
        fwrite($h, $data);
        fclose($h);
        // Adiciona como anexo
        $array['Anexos']    = array(
            $anexoNome
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $anexoNome
        );
        // Logo         
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem  = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem  = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        // Variaveis enviadas para o modulo de e-mails (que serão gravadas na tabela T139)
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Lista de Clientes em anexo.</font><br /><br /><br />";
        
        return $array;
    }
    
    /**
     * updateCheckboxImprimir
     *
     * @param String $D024_Id
     * @param String $selected
     * @return bool
     */
    public function updateCheckboxImprimir($D024_Id, $selected)
    {
        global $g;
        $D024_Id  = mysql_real_escape_string($D024_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("UPDATE D024 SET D024_Flag_Imprimir_Etiqueta='$selected' WHERE D024_Id='$D024_Id'");
        return true;
    }
    
    /**
     * alterarClientePreCadastro
     *
     * @param String $D024_Id
     * @return bool
     */    
    public function alterarClientePreCadastro($D024_Id)
    {
        $sqlD024 = mysql_query("SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
        $D024    = mysql_fetch_array($sqlD024);
        if (!empty($D024['D024_Nome_Empresa']) && !empty($D024['D024_Nome_Fantasia']) && !empty($D024['D024_Flag_Contribuinte']) && !empty($D024['D024_Flag_Pessoa_Fisica_Juridica']) && ($D024['D024_D020_Id'] > 0) && ($D024['D024_D018_Id'] > 0) && !empty($D024['D024_Bairro']) && !empty($D024['D024_DDD_Telefone_1']) && !empty($D024['D024_Telefone_1']) && !empty($D024['D024_Endereco']) && !empty($D024['D024_Numero'])) {
            if ($D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F' && !empty($D024['D024_Cpf'])) {
                mysql_query("UPDATE D024 SET D024_Flag_Pre_Cadastro = 'N' WHERE D024_Id = '{$D024_Id}'");
                return true;
            } else if ($D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J' && !empty($D024['D024_Cnpj'])) {
                mysql_query("UPDATE D024 SET D024_Flag_Pre_Cadastro = 'N' WHERE D024_Id = '{$D024_Id}'");
                return true;
            }
        }
        return false;
    }

    public function removerAcentos($x) {
          //-- ELIMINA CARACTERES ESPECIAIS
          define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŒØŠÙÚÛÜÝŽÞàáâãäåæçèéêëìíîïðñòóôõö&œøšùúûüýžþºª&.$" );
          define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii noooooE   uuuuy  ..E.$" );
          $x=strtr($x, ACENTOS, NORMAL);
          
          $novoX = '';
          for ($i = 0; $i < strlen($x); $i++) {
                $code = ord($x[$i]);
                if ($code == 192) { $novoX .= 'A'; continue; }
                if ($code == 193) { $novoX .= 'A'; continue; }
                if ($code == 194) { $novoX .= 'A'; continue; }
                if ($code == 195) { $novoX .= 'A'; continue; }
                if ($code == 196) { $novoX .= 'A'; continue; }
                if ($code == 197) { $novoX .= 'A'; continue; }
                if ($code == 198) { $novoX .= 'A'; continue; }
                if ($code == 199) { $novoX .= 'C'; continue; }
                if ($code == 200) { $novoX .= 'E'; continue; }
                if ($code == 201) { $novoX .= 'E'; continue; }
                if ($code == 202) { $novoX .= 'E'; continue; }
                if ($code == 203) { $novoX .= 'E'; continue; }
                if ($code == 204) { $novoX .= 'I'; continue; }
                if ($code == 205) { $novoX .= 'I'; continue; }
                if ($code == 206) { $novoX .= 'I'; continue; }
                if ($code == 207) { $novoX .= 'I'; continue; }
                if ($code == 210) { $novoX .= 'O'; continue; }
                if ($code == 211) { $novoX .= 'O'; continue; }
                if ($code == 212) { $novoX .= 'O'; continue; }
                if ($code == 213) { $novoX .= 'O'; continue; }
                if ($code == 214) { $novoX .= 'O'; continue; }
                if ($code == 217) { $novoX .= 'U'; continue; }
                if ($code == 218) { $novoX .= 'U'; continue; }
                if ($code == 219) { $novoX .= 'U'; continue; }
                if ($code == 220) { $novoX .= 'U'; continue; }
                if ($code == 224) { $novoX .= 'A'; continue; }
                if ($code == 225) { $novoX .= 'A'; continue; }
                if ($code == 226) { $novoX .= 'A'; continue; }
                if ($code == 227) { $novoX .= 'A'; continue; }
                if ($code == 228) { $novoX .= 'A'; continue; }
                if ($code == 229) { $novoX .= 'A'; continue; }
                if ($code == 231) { $novoX .= 'C'; continue; }
                if ($code == 232) { $novoX .= 'E'; continue; }
                if ($code == 233) { $novoX .= 'E'; continue; }
                if ($code == 234) { $novoX .= 'E'; continue; }
                if ($code == 235) { $novoX .= 'E'; continue; }
                if ($code == 236) { $novoX .= 'I'; continue; }
                if ($code == 237) { $novoX .= 'I'; continue; }
                if ($code == 238) { $novoX .= 'I'; continue; }
                if ($code == 239) { $novoX .= 'I'; continue; }
                if ($code == 242) { $novoX .= 'O'; continue; }
                if ($code == 243) { $novoX .= 'O'; continue; }
                if ($code == 244) { $novoX .= 'O'; continue; }
                if ($code == 245) { $novoX .= 'O'; continue; }
                if ($code == 246) { $novoX .= 'O'; continue; }
                if ($code == 249) { $novoX .= 'U'; continue; }
                if ($code == 250) { $novoX .= 'U'; continue; }
                if ($code == 251) { $novoX .= 'U'; continue; }
                if ($code == 252) { $novoX .= 'U'; continue; }
                $novoX .= $x[$i];
          }
          $x = $novoX;
          return $x;

    }

    public function pesquisaTerceirosByjg($tCep)
    {
        /**
        * WebService ByJG
        * http://www.byjg.com.br/
        * Consulta sem limites.
        * Deve retornar False caso nao tenha encontrado o CEP
        */
        header('Content-Type: text/html; charset=UTF-8');
        $isRetorno = false;

        $username  = "hardness";
        $password  = "hard3871";

        $url = "http://www.byjg.com.br/site/webservice.php/ws/cep?httpmethod=obterlogradouroauth";
        $url .= "&cep=" . urlencode($tCep);
        $url .= "&usuario=" . urlencode($username);
        $url .= "&senha=" . urlencode($password);

        if ($result = file_get_contents($url))
        {
            $result = utf8_decode($result);
            if (strpos($result,"não encontrado")!==FALSE OR strpos($result,"senha inválidos")!==FALSE)
            {
                return false;
            } 
          
            $aux = explode("|", $result);
            $mByjg = explode(",", $aux[1]);
                    
            for ($i=count($mByjg);$i<5;$i++) $mByjg[] = "";

                $mByjg[0] = trim($mByjg[0]);
                $mByjg[1] = trim($mByjg[1]);             
                $mByjg[2] = trim($mByjg[2]);
                $mByjg[3] = trim($mByjg[3]);
                $mByjg[4] = trim($mByjg[4]);            

                $mDados['Cep']             = $this->removerAcentos($tCep);
                $mDados['Logradouro']      = $this->removerAcentos($mByjg[0]);
                $mDados['Bairro']          = $this->removerAcentos($mByjg[1]);
                $mDados['Cidade']          = $this->removerAcentos($mByjg[2]);
                $mDados['UF']              = $this->removerAcentos($mByjg[3]);
                $mDados['IBGE_Cidade']     = $this->removerAcentos($mByjg[4]);
                $mDados['IBGE_UF']         = substr($mByjg[4],0,2);
                            
            return $mDados;
        }
        else
        {            
            return $isRetorno;
        }            

        return false;
    }

    public function consultaCep($cep) {
        $cep = str_replace('-', '', $cep);
        if (strlen($cep) != 8 ) {
            return [false, "Cep com numero de digitos errados. Cep utilizado: {$cep}"];
        }
        
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $result = file_get_contents($url);
        $result = json_decode($result, true);

        if (empty($result)) {
            return [false, "Erro ao realizar consulta do CEP.<br><br>Url utilizada: <a href='{$url}' target='_blank'>{$url}</a>"];
        }

        return [true, $result];
    }


   /**
     * prepararImportarCSV
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSV($arquivo)
    {
   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);


        log("inicio importarCSV");

        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }

        log("nome do arquivo recebido $cArquivo / {$mArquivo['tmp_name']}");
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        $tipoArquivo='UTF-8';
        while ($array_linha = fgets($ponteiro)) {

              // Limpa characteres especiais do arquivo texto
              $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

              if (mb_detect_encoding($array_linha)!='UTF-8'){
                 $tipoArquivo=mb_detect_encoding($array_linha);
                 break;
              }

        }

        log("Tipo arquivo: ".$tipoArquivo);

        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        if (!$ponteiro){
            return "ERRO - Não foi possível ler o arquivo $cArquivo ({$mArquivo['tmp_name']})";
        }

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
       // $array_cols=array('CPF', 'CNPJ', 'Contato', 'E-mail', 'E-mail para NF', 'E-mail para boleto');
        $array_cols=array('Nome', 'Fantasia', 'CNPJ', 'CPF', 'IE', 'RG', 'Pessoa','Tipo','DDD Telefone 1','Telefone 1','DDD Telefone 2', 'Telefone 2','Contato','DDD Celular','Celular','DDD Fax','Fax','Email','Email NF','Email Boleto','Obs','CEP', 'Endereco','Complemento','Numero','Bairro','UF','Cidade','Pais','CRT','Website','Cliente','Fornecedor','Pre-Cadastro','Fundacao/nascimento','Lista Preco','Cod.Vendedor Principal','Comissao','Cod.Vendedor Externo','Comissao','Cod.Representante','Comissao','Data Ativo','Data Inativo','Sit. Comercial','Permite Emitir ACP','Cod.Transportadora','Obs Comercial','IPV Venda','Cod.Matriz','Dia visita','Cliente ativo?','Credito Liberado','Obs Financeira','Instrucao pagamento','Valor Limite/Fat.Minimo','Protestar?','Agrupamento Auto.','Empresa Grupo','1 Vencimento','Dia fixo/Intervalo','Num. Parcelas','Portador');
       //                   '0'      '1'       '2'     '3'    '4'  '5'     '6'      '7'       '8'             '9'            '10'         '11'           '12'        '13'       '14'      '15'    '16'   '17'      '18'        '19'        '20'  21'     '22'          '23'      '24'      '25'   '26'   '27'    '28'  '29'    '30'      '31'        '32'         '33'                '34'               '35'            '36'              '37'                '38'            '39'          '40'             '41'        '42'          '43'            '44'            '45'                  '46'              '47'            '48'          '49'      '50'           '51'                 '52'            '53'                '54'                   '55'                '56'                '57'         '58'               '59'             '60'               '61'           62                                                                                                                                                                                                                                                                                                                                   


        $linha = 1;        
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {
            // Limpa characteres especiais do arquivo texto
            $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

            if ($tipoArquivo!='UTF-8'){
                $array_linha = explode(';',utf8_encode($array_linha));
            } else {
                $array_linha = explode(';',$array_linha);
            } 

           
            //retirando os espaços em branco dos campos
            $array_linha[0] = trim($array_linha[0]);
            $array_linha[1] = trim($array_linha[1]);
            $array_linha[2] = trim($array_linha[2]);
            $array_linha[3] = trim($array_linha[3]);
            $array_linha[4] = trim($array_linha[4]);
            $array_linha[5] = trim($array_linha[5]);

            $array_linha[6] = trim($array_linha[6]);
            $array_linha[7] = trim($array_linha[7]);
            $array_linha[8] = trim($array_linha[8]);
            $array_linha[9] = trim($array_linha[9]);
            $array_linha[10] = trim($array_linha[10]);
            $array_linha[11] = trim($array_linha[11]);

            $array_linha[12] = trim($array_linha[12]);
            $array_linha[12] = trim($array_linha[12]);
            $array_linha[13] = trim($array_linha[13]);
            $array_linha[14] = trim($array_linha[14]);
            $array_linha[15] = trim($array_linha[15]);
            $array_linha[16] = trim($array_linha[16]);

            $array_linha[17] = trim($array_linha[17]);
            $array_linha[18] = trim($array_linha[18]);
            $array_linha[19] = trim($array_linha[19]);
            $array_linha[20] = trim($array_linha[20]);
            $array_linha[21] = trim($array_linha[21]);
            $array_linha[22] = trim($array_linha[22]);

            $array_linha[23] = trim($array_linha[23]);
            $array_linha[24] = trim($array_linha[24]);
            $array_linha[25] = trim($array_linha[25]);
            $array_linha[26] = trim($array_linha[26]);
            $array_linha[27] = trim($array_linha[27]);
            $array_linha[28] = trim($array_linha[28]);

            $array_linha[29] = trim($array_linha[29]);
            $array_linha[30] = trim($array_linha[30]);

            $array_linha[31] = trim($array_linha[31]);
            $array_linha[32] = trim($array_linha[32]);
            $array_linha[33] = trim($array_linha[33]);
            $array_linha[34] = trim($array_linha[34]);
            $array_linha[35] = trim($array_linha[35]);
            $array_linha[36] = trim($array_linha[36]);

            $array_linha[37] = trim($array_linha[37]);
            $array_linha[38] = trim($array_linha[38]);
            $array_linha[39] = trim($array_linha[39]);
            $array_linha[40] = trim($array_linha[40]);
            $array_linha[41] = trim($array_linha[41]);
            $array_linha[42] = trim($array_linha[42]);

            $array_linha[43] = trim($array_linha[43]);
            $array_linha[44] = trim($array_linha[44]);
            $array_linha[45] = trim($array_linha[45]);
            $array_linha[46] = trim($array_linha[46]);
            $array_linha[47] = trim($array_linha[47]);
            $array_linha[48] = trim($array_linha[48]);

            $array_linha[49] = trim($array_linha[49]);
            $array_linha[50] = trim($array_linha[50]);
            $array_linha[51] = trim($array_linha[51]);
            $array_linha[52] = trim($array_linha[52]);
            $array_linha[53] = trim($array_linha[53]);
            $array_linha[54] = trim($array_linha[54]);

            $array_linha[55] = trim($array_linha[55]);
            $array_linha[56] = trim($array_linha[56]);
            $array_linha[57] = trim($array_linha[57]);
            $array_linha[58] = trim($array_linha[58]);
            $array_linha[59] = trim($array_linha[59]);
            $array_linha[60] = trim($array_linha[60]);
            $array_linha[61] = trim($array_linha[61]);
            $array_linha[62] = trim($array_linha[62]);
                     
            $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";


            if ($linha==1){
                // Validar número de colunas e o se o nome das colunas bate com o padrão  
                if (count($array_cols)!=count($array_linha)) {
                    $return .= "O número de colunas é diferente de " . count($array_cols) . "\\n";
                }
                // Validação dos titulos das colunas
                for ($count=0;$count<count($array_linha);$count++){
                    if (trim($array_cols[$count]) != trim(str_replace('*','', $array_linha[$count]))) {     
                        $return .="A coluna ".trim($array_linha[$count])." do arquivo não confere com a coluna ".$array_cols[$count]." esperada.\\n \\n";
                    }
                }                   

            } else {      

                //VALIDANDO OS CAMPOS OBRIGATÓRIOS OU QUE TEM ALGUMA VALIDAÇÃO ESPECIFICA      
                if(empty($array_linha[0])){
                    $return .=  $msgLinha . "A coluna Nome está em branco.\\n";
                }
             
                if(empty($array_linha[1])){
                    $return .=  $msgLinha . "A coluna Fantasia está em branco.\\n";
                }

                //verificando Pessoa juridica ou fisica e conforme for verificar se o cpf ou cnpj estao preenchidos
                //A pessoa não pode estar em branco
                if(empty($array_linha[6])){
                    $return .=  $msgLinha . "A coluna Pessoa está em branco.\\n";
                }else{
                //Se a pessoa for J a coluna cnpj não pode estar em branco, se nao estiver em branco irá verificar pelo cnpj do cliente se ele ja esta cadastrado
                    if($array_linha[6]=='J'){
                        if(empty($array_linha[2])){
                            $return .=  $msgLinha . "A coluna CNPJ está em branco.\\n";
                        }else{
                            // Validar se código está duplicado
                            $cClausula = "SELECT D024_Id from D024 WHERE D024_Cnpj='$array_linha[2]'";
                            $cSQL_D024 = mysql_query($cClausula);
                            $mD024     = mysql_fetch_array($cSQL_D024);
                            $cErro     = mysql_error();
                            if(!empty($cErro)){
                                $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                            }     
                            if(mysql_num_rows($cSQL_D024) > 0){
                                $return .= $msgLinha . "O CNPJ ". $array_linha[2] ." está cadastrado!\\n";
                            } 
                        }
                    }else if($array_linha[6]=='F'){
                        if(empty($array_linha[3])){
                            $return .=  $msgLinha . "A coluna CPF está em branco.\\n";
                        }else{
                            // Validar se código está duplicado
                            $cClausula = "SELECT D024_Id from D024 WHERE D024_Cpf='$array_linha[3]'";
                            $cSQL_D024 = mysql_query($cClausula);
                            $mD024     = mysql_fetch_array($cSQL_D024);
                            $cErro     = mysql_error();
                            if(!empty($cErro)){
                                $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                            }     
                            if(mysql_num_rows($cSQL_D024) > 0){
                                $return .= $msgLinha . "O CPF ". $array_linha[3] ." está cadastrado!\\n";
                            }    
                        }
                    }
                }         

                // Valida se a tipo esta em branco ou se tem um valor invalido    
                if(empty($array_linha[7]) && $array_linha[7] != '0'){
                    $return .=  $msgLinha . "A coluna Tipo está em branco.\\n";
                }else{
                    if($array_linha[7]>5){
                        $return .=  $msgLinha . "A coluna Tipo deve conter um valor entre 0 e 5.\\n";
                    }
                }

                //Verifica se o e-mail está inválido
                $conta = "^[a-zA-Z0-9\._-]+@";
                $domino = "[a-zA-Z0-9\._-]+.";
                $extensao = "([a-zA-Z]{2,4})$";
                $pattern = $conta.$domino.$extensao;


                // Valida se E-mail está não esta branco e se esta no formato correto
               if(!empty($array_linha[17])){
                    if ((preg_match($pattern, $array_linha[17]) == 1) || (strpos($array_linha[17], '.') === false)){
                        $return .= $msgLinha . "O e-mail ".$array_linha[17] ." não é válido. \\n" ;
                    }
                }

                // Valida se a coluna 4 - E-mail para NF está em branco
                if(!empty($array_linha[18])){
                    if ((preg_match($pattern, $array_linha[18]) == 1  || (strpos($array_linha[18],'.') === false)))
                        $return .= $msgLinha . "O e-mail para NF " .$array_linha[18]. " não é válido  \\n";    
                }

                // Valida se a coluna 5 - E-mail para boleto está em branco
                if(!empty($array_linha[19])){
                    if ((preg_match($pattern, ($array_linha[19])) == 1) || (strpos($array_linha[19],'.') === false))
                        $return .= $msgLinha . "O e-mail para Boleto".$array_linha[19]."  não é válido. \\n";    
                }

                //validar coluna CRT
                if(empty($array_linha[29])){
                    $return .= $msgLinha . "A coluna CRT está em branco. \\n";    
                }else{
                    if($array_linha[29]==0 && $array_linha[29]>3){
                        $return .= $msgLinha . "A coluna CRT deve conter um valor entre 1 e 3. \\n"; 
                    }
                }   

                //validando colune cliente
                if(empty($array_linha[31])){
                    $return .= $msgLinha . "A coluna Cliente está em branco. \\n";    
                }else{
                    if($array_linha[31]!='S' && $array_linha[31]!='N'){
                        $return .= $msgLinha . "A coluna Cliente deve ser preenchida com S ou N. \\n"; 
                    }
                }  


                //validando colune fornecedor
                if(empty($array_linha[32])){
                    $return .= $msgLinha . "A coluna Fornecedor está em branco. \\n";    
                }else{
                    if($array_linha[32]!='S' && $array_linha[32]!='N'){
                        $return .= $msgLinha . "A coluna Fornecedor deve ser preenchida com S ou N. \\n"; 
                    }
                }   

                //validando colune Pre-cadastro
                if(empty($array_linha[33])){
                    $return .= $msgLinha . "A coluna Pre-Cadastro está em branco. \\n";    
                }else{
                    if($array_linha[33]!='S' && $array_linha[33]!='N'){
                        $return .= $msgLinha . "A coluna Pre-Cadastro deve ser preenchida com S ou N. \\n"; 
                    }
                }  

                //validando coluna UF
                if(empty($array_linha[26])){
                    $return .= $msgLinha . "A coluna UF está em branco. \\n";    
                }else{
                    $cClausula = "SELECT D018_Id from D018 WHERE D018_UF='$array_linha[26]'";
                    $cSQL_D018 = mysql_query($cClausula);
                    $mD018     = mysql_fetch_array($cSQL_D018);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D018) <= 0){
                        $return .= $msgLinha . "A UF ". $array_linha[26] ." não está cadastrada!\\n";
                    }
                }

                //validando coluna Cidade
                if(empty($array_linha[27])){
                    $return .= $msgLinha . "A coluna Cidade está em branco. \\n";    
                }else{
                    $cClausula = "SELECT D020_Id from D020 WHERE D020_Nome_Cidade='$array_linha[27]'";
                    $cSQL_D020 = mysql_query($cClausula);
                    $mD020     = mysql_fetch_array($cSQL_D020);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D020) <= 0){
                        $return .= $msgLinha . "A Cidade ". $array_linha[27] ." não está cadastrada!\\n";
                    }
                }

                //validando coluna Pais
                if(empty($array_linha[28])){
                    $return .= $msgLinha . "A coluna País está em branco. \\n";    
                }else{
                    $cClausula = "SELECT D030_Id from D030 WHERE D030_Nome_Pais='$array_linha[28]'";
                    $cSQL_D030 = mysql_query($cClausula);
                    $mD030     = mysql_fetch_array($cSQL_D030);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D030) <= 0){
                        $return .= $msgLinha . "O País ". $array_linha[28] ." não está cadastrado!\\n";
                    }
                }

                //VALIDAÇÕES DE CAMPOS DE TELEFONE
                if(empty($array_linha[9])){
                    $return .= $msgLinha . "A coluna Telefone 1 está em branco. \\n";    
                }
                if(empty($array_linha[8])){
                    $return .= $msgLinha . "A coluna DDD Telefone 1 está em branco. \\n"; 
                }

                //validando lista de preço
                if(empty($array_linha[35])){
                    $return .= $msgLinha . "A coluna Lista Preco está em branco. \\n";   
                }else{
                    if($array_linha[35]<1 && $array_linha[35]>4){
                        $return .= $msgLinha . "A coluna Lista Preco deve constar um valor entre 1 e 4. \\n";   
                    }
                }

                //matriz
                if(!empty($array_linha[49])){
                    $cClausula = "SELECT D024_Id,D024_Nome_Empresa from D024 WHERE D024_Id='$array_linha[49]'";
                    $cSQL_D024 = mysql_query($cClausula);
                    $mD024     = mysql_fetch_array($cSQL_D024);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D024) <= 0){
                        $return .= $msgLinha . "A Matriz de código ". $array_linha[49] ." não está cadastrada!\\n";
                    }  
                }

                //Vendedor Principal
                if(!empty($array_linha[36])){
                    $cClausula = "SELECT C007_Id,C007_Nome from C007 WHERE C007_Id='$array_linha[36]'";
                    $cSQL_C007 = mysql_query($cClausula);
                    $mC007     = mysql_fetch_array($cSQL_C007);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_C007) <= 0){
                        $return .= $msgLinha . "O Vendedor de código ". $array_linha[36] ." não está cadastrado!\\n";
                    } 
                }

                //Vendedor Externo
                if(!empty($array_linha[38])){
                    $cClausula = "SELECT C007_Id from C007 WHERE C007_Id='$array_linha[38]'";
                    $cSQL_C007 = mysql_query($cClausula);
                    $mC007     = mysql_fetch_array($cSQL_C007);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_C007) <= 0){
                        $return .= $msgLinha . "O Vendedor de código ". $array_linha[38] ." não está cadastrado!\\n";
                    }  
                }

                //Representante
                if(!empty($array_linha[40])){
                    $cClausula = "SELECT D021_Id from D021 WHERE D021_Id='$array_linha[40]'";
                    $cSQL_D021 = mysql_query($cClausula);
                    $mD021     = mysql_fetch_array($cSQL_D021);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D021) <= 0){
                        $return .= $msgLinha . "O Representante de código ". $array_linha[40] ." não está cadastrado!\\n";
                    }  
                }

                //transportadora
                if(!empty($array_linha[46])){
                    $cClausula = "SELECT D022_Id,D022_Nome_Empresa from D022 WHERE D022_Id='$array_linha[46]'";
                    $cSQL_D022 = mysql_query($cClausula);
                    $mD022     = mysql_fetch_array($cSQL_D022);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D022) <= 0){
                        $return .= $msgLinha . "A Transportadora de código ". $array_linha[46] ." não está cadastrada!\\n";
                    }
                }

                //portador
                if(!empty($array_linha[62])){
                    $cClausula = "SELECT D027_Id from D027 WHERE D027_Portador='$array_linha[62]'";
                    $cSQL_D027 = mysql_query($cClausula);
                    $mD027     = mysql_fetch_array($cSQL_D027);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D027) <= 0){
                        $return .= $msgLinha . "O Portador ". $array_linha[62] ." não está cadastrado!\\n";
                    }  
                }


                //Campo situacao comercial
                if(!empty($array_linha[44])){
                    if($array_linha[44]!='A' && $array_linha[44]!='I'){
                        $return .= $msgLinha . "A coluna Sit. Comercial deve ser preenchida com A ou I. \\n";
                    }                   
                }  

                //Campo ACP
                if(!empty($array_linha[45])){
                    if($array_linha[45]!='S' && $array_linha[45]!='N'){
                        $return .= $msgLinha . "A coluna Permite Emitir ACP deve ser preenchida com S ou N. \\n";
                    }                   
                }       

                //cliente ativo?
                if(empty($array_linha[51])){
                    $return .= $msgLinha . "A coluna Cliente ativo? está em branco. \\n";
                }else{
                    if($array_linha[51]!='S' && $array_linha[51]!='N'){
                        $return .= $msgLinha . "A coluna Cliente ativo? deve ser preenchida com S ou N. \\n";
                    }      
                }

                //credito liberado
                if(!empty($array_linha[52])){
                    if($array_linha[52]!='S' && $array_linha[52]!='N'){
                        $return .= $msgLinha . "A coluna Credito Liberado deve ser preenchida com S ou N. \\n";
                    }                   
                } 

                //protestar
                if(!empty($array_linha[56])){
                    if($array_linha[56]!='S' && $array_linha[56]!='N'){
                        $return .= $msgLinha . "A coluna Protestar? deve ser preenchida com S ou N. \\n";
                    }                   
                } 

                //agrupamento automatico
                if(!empty($array_linha[57])){
                    if($array_linha[57]!='S' && $array_linha[57]!='N'){
                        $return .= $msgLinha . "A coluna Agrupamento auto. deve ser preenchida com S ou N. \\n";
                    }                   
                } 

                //empresa grupo
                if(!empty($array_linha[58])){
                    if($array_linha[58]!='S' && $array_linha[58]!='N'){
                        $return .= $msgLinha . "A coluna Empresa Grupo deve ser preenchida com S ou N. \\n";
                    }                   
                } 

            }
            $linha++;
        }

        //SE NÃO OCORREU NENHUM ERRO NAS VALIDAÇÕES
        if(empty($return)){

            //-- ABRE O ARQUIVO CSV
            $ponteiro      = fopen($mArquivo["tmp_name"], "r");

            //INICIA A TRANSAÇÃO DO BANCO
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $linha=1;
            //PERCORRER AS LINHAS NOVAMENTE 
            while (($array_linha = fgetcsv($ponteiro,1000,';')) !== FALSE) {
                    if($linha==1){
                        $linha++;
                        continue;
                    } 
                    $linha++;

                    //buscando novamente os campos que precisam de Id do banco de dados

                    $cClausula = "SELECT D018_Id from D018 WHERE D018_UF='$array_linha[26]'";
                    $cSQL_D018 = mysql_query($cClausula);
                    $mD018     = mysql_fetch_array($cSQL_D018);

                    $cClausula = "SELECT D020_Id from D020 WHERE D020_Nome_Cidade='$array_linha[27]'";
                    $cSQL_D020 = mysql_query($cClausula);
                    $mD020     = mysql_fetch_array($cSQL_D020);

                    $cClausula = "SELECT D030_Id from D030 WHERE D030_Nome_Pais='$array_linha[28]'";
                    $cSQL_D030 = mysql_query($cClausula);
                    $mD030     = mysql_fetch_array($cSQL_D030);

                    $cClausula = "SELECT C007_Id from C007 WHERE C007_Id='$array_linha[36]'";
                    $cSQL_C007 = mysql_query($cClausula);
                    $mC007Principal     = mysql_fetch_array($cSQL_C007);

                    $cClausula = "SELECT C007_Id from C007 WHERE C007_Id='$array_linha[38]'";
                    $cSQL_C007 = mysql_query($cClausula);
                    $mC007Externo     = mysql_fetch_array($cSQL_C007);

                    $cClausula = "SELECT D021_Id from D021 WHERE D021_Id='$array_linha[40]'";
                    $cSQL_D021 = mysql_query($cClausula);
                    $mD021     = mysql_fetch_array($cSQL_D021);

                    $cClausula = "SELECT D022_Id from D022 WHERE D022_Id='$array_linha[46]'";
                    $cSQL_D022 = mysql_query($cClausula);
                    $mD022     = mysql_fetch_array($cSQL_D022);

                    $cClausula = "SELECT D027_Id from D027 WHERE D027_Portador='$array_linha[62]'";
                    $cSQL_D027 = mysql_query($cClausula);
                    $mD027     = mysql_fetch_array($cSQL_D027);

                    $cClausula = "SELECT D024_Id from D024 WHERE D024_Id='$array_linha[49]'";
                    $cSQL_D024 = mysql_query($cClausula);
                    $mD024     = mysql_fetch_array($cSQL_D024);


                    $array_linha[34]=gcorrigeDataInverte($array_linha[34]);
                    $array_linha[42]=gCorrigeDataInverte($array_linha[42]);
                    $array_linha[43]=gCorrigeDataInverte($array_linha[43]);


                    $array_linha[37]=gCorrigeNumeroInverte($array_linha[37]);
                    $array_linha[39]=gCorrigeNumeroInverte($array_linha[39]);
                    $array_linha[41]=gCorrigeNumeroInverte($array_linha[41]);
                    $array_linha[48]=gCorrigeNumeroInverte($array_linha[48]);
                    $array_linha[55]=gCorrigeNumeroInverte($array_linha[55]);
                   
                    $cClausula = "INSERT INTO D024 (
                        D024_D018_Id,
                        D024_D020_Id,
                        D024_D030_Id,
                        D024_D021_Id,
                        D024_D022_Id,
                        D024_D027_Id,
                        D024_Id_Matriz,
                        D024_Nome_Empresa,
                        D024_Nome_Fantasia,
                        D024_Cnpj,
                        D024_Cpf,
                        D024_Inscricao_Estadual,
                        D024_Rg,
                        D024_Flag_Pessoa_Fisica_Juridica,
                        D024_Flag_Contribuinte,
                        D024_DDD_Telefone_1,
                        D024_Telefone_1,
                        D024_DDD_Telefone_2,
                        D024_Telefone_2,
                        D024_Nome_Contato,
                        D024_DDD_Celular,
                        D024_Celular,
                        D024_DDD_Fax,
                        D024_Fax,
                        D024_E_Mail,
                        D024_E_Mail_Nota,
                        D024_E_Mail_Boleto,
                        D024_Observacoes,
                        D024_Cep,
                        D024_Endereco,
                        D024_Complemento,
                        D024_Numero,
                        D024_Bairro,
                        D024_Codigo_Regime_Tributario,
                        D024_Web_Site,
                        D024_Flag_Cliente,
                        D024_Flag_Fornecedor,
                        D024_Flag_Pre_Cadastro,
                        D024_Data_Fundacao,
                        D024_Data_Cadastro,
                        D024_Flag_Lista_Preco,
                        D024_C007_Vendedor_Interno,
                        D024_Percentual_Comissao_Interno,
                        D024_C007_Vendedor_Externo,
                        D024_Percentual_Comissao_Externo,
                        D024_Percentual_Comissao,
                        D024_Data_Ativo,
                        D024_Data_Inativo,
                        D024_Flag_comercial,
                        D024_Flag_Permite_ACP,
                        D024_Observacao_Comercial,
                        D024_IPV_1,
                        D024_Dia_Visita,
                        D024_Flag_Ativo,
                        D024_Flag_Liberado,
                        D024_Observacao_Financeiro,
                        D024_Instrucao_Pagamento,
                        D024_Valor_Limite,
                        D024_Flag_Protestar,
                        D024_Flag_Permite_Agrupamentos,
                        D024_Flag_Empresas_Grupo,
                        D024_Prazo_Parcela,
                        D024_Dias_Vencimento,
                        D024_Quantidade_Parcelas
                    ) VALUES (
                        '" . mysql_real_escape_string($mD018['D018_Id']) . "',
                        '" . mysql_real_escape_string($mD020['D020_Id']) . "',
                        '" . mysql_real_escape_string($mD030['D030_Id']) . "',
                        '" . mysql_real_escape_string($mD021['D021_Id']) . "',
                        '" . mysql_real_escape_string($mD022['D022_Id']) . "',
                        '" . mysql_real_escape_string($mD027['D027_Id']) . "',
                        '" . mysql_real_escape_string($mD024['D024_Id']) . "',
                        '" . mysql_real_escape_string($array_linha[0]) . "',
                        '" . mysql_real_escape_string($array_linha[1]) . "',
                        '" . mysql_real_escape_string($array_linha[2]) . "',
                        '" . mysql_real_escape_string($array_linha[3]) . "',
                        '" . mysql_real_escape_string($array_linha[4]) . "',
                        '" . mysql_real_escape_string($array_linha[5]) . "',
                        '" . mysql_real_escape_string($array_linha[6]) . "',
                        '" . mysql_real_escape_string($array_linha[7]) . "',
                        '" . mysql_real_escape_string($array_linha[8]) . "',
                        '" . mysql_real_escape_string($array_linha[9]) . "',
                        '" . mysql_real_escape_string($array_linha[10]) . "',
                        '" . mysql_real_escape_string($array_linha[11]) . "',
                        '" . mysql_real_escape_string($array_linha[12]) . "',
                        '" . mysql_real_escape_string($array_linha[13]) . "',
                        '" . mysql_real_escape_string($array_linha[14]) . "',
                        '" . mysql_real_escape_string($array_linha[15]) . "',
                        '" . mysql_real_escape_string($array_linha[16]) . "',
                        '" . mysql_real_escape_string($array_linha[17]) . "',
                        '" . mysql_real_escape_string($array_linha[18]) . "',
                        '" . mysql_real_escape_string($array_linha[19]) . "',
                        '" . mysql_real_escape_string($array_linha[20]) . "',
                        '" . mysql_real_escape_string($array_linha[21]) . "',
                        '" . mysql_real_escape_string($array_linha[22]) . "',
                        '" . mysql_real_escape_string($array_linha[23]) . "',
                        '" . mysql_real_escape_string($array_linha[24]) . "',
                        '" . mysql_real_escape_string($array_linha[25]) . "',
                        '" . mysql_real_escape_string($array_linha[29]) . "',
                        '" . mysql_real_escape_string($array_linha[30]) . "',
                        '" . mysql_real_escape_string($array_linha[31]) . "',
                        '" . mysql_real_escape_string($array_linha[32]) . "',
                        '" . mysql_real_escape_string($array_linha[33]) . "',
                        '" . mysql_real_escape_string($array_linha[34]) . "',
                        CURDATE(),
                        '" . mysql_real_escape_string($array_linha[35]) . "',
                        '" . mysql_real_escape_string($mC007Principal['C007_Id']) . "',
                        '" . mysql_real_escape_string($array_linha[37]) . "',
                        '" . mysql_real_escape_string($mC007Externo['C007_Id']) . "',
                        '" . mysql_real_escape_string($array_linha[39]) . "',
                        '" . mysql_real_escape_string($array_linha[41]) . "',
                        '" . mysql_real_escape_string($array_linha[42]) . "',
                        '" . mysql_real_escape_string($array_linha[43]) . "',
                        '" . mysql_real_escape_string($array_linha[44]) . "',
                        '" . mysql_real_escape_string($array_linha[45]) . "',
                        '" . mysql_real_escape_string($array_linha[47]) . "',
                        '" . mysql_real_escape_string($array_linha[48]) . "',
                        '" . mysql_real_escape_string($array_linha[50]) . "',
                        '" . mysql_real_escape_string($array_linha[51]) . "',
                        '" . mysql_real_escape_string($array_linha[52]) . "',
                        '" . mysql_real_escape_string($array_linha[53]) . "',
                        '" . mysql_real_escape_string($array_linha[54]) . "',
                        '" . mysql_real_escape_string($array_linha[55]) . "',
                        '" . mysql_real_escape_string($array_linha[56]) . "',
                        '" . mysql_real_escape_string($array_linha[57]) . "',
                        '" . mysql_real_escape_string($array_linha[58]) . "',
                        '" . mysql_real_escape_string($array_linha[59]) . "',
                        '" . mysql_real_escape_string($array_linha[60]) . "',
                        '" . mysql_real_escape_string($array_linha[61]) . "'
                        );";
                            
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                         mysql_query("ROLLBACK");
                         return "Erro: " . $cErro . "\\n" . $cClausula;
                    } 

            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram incluidos ".($linha-2)." clientes/fornecedores!";

      
        } else {

            return $return;

        }        
    }

    /**
     * Calcular limite
     * 
     * @return bool/String
     */  
    public function calcularLimite()
    {
        // Personalização criada para a GP 
        /*mysql_query("SET autocommit=0");
        mysql_query("start transaction");
        
        // faz a soma do valor do lançamento dos último 3 meses mais o valor do lançamento dos últimnos 6 meses
        $cClausula = "SELECT SUM(valor) valor, T019_D024_Id
                            FROM(
                                SELECT T019_D024_ID, SUM(T019_VALOR_LANCAMENTO) AS VALOR  
                                FROM T019 
                                WHERE T019_FLAG_DEB_CRD=1 AND
                                T019_DATA_LANCAMENTO BETWEEN CURDATE() - INTERVAL 3 MONTH AND CURDATE()
                                GROUP BY T019_D024_ID 
                            UNION ALL
                                SELECT T019_D024_ID, SUM(T019_VALOR_LANCAMENTO) AS VALOR  
                                FROM T019 
                                WHERE T019_FLAG_DEB_CRD=1 AND
                                T019_DATA_LANCAMENTO BETWEEN CURDATE() - INTERVAL 6 MONTH AND CURDATE()
                                GROUP BY T019_D024_ID
                            )T019 GROUP BY T019_D024_Id";
        
        $T019 = mysql_query($cClausula);
        $cErro = mysql_error();
        if(!empty($cErro)){
            mysql_query("rollback");
            return "ERRO: ".$cErro."<br>Cláusula: ".$cClausula; 
        }
        
        // Calcula o valor limite do cliente de acordo com a fórmula fornecida ((valorRecebido3meses+valorRecebido6meses)/6)*3 pela empresa e altera o valor limite no cliente
        while($rowT019 = mysql_fetch_array($T019)){
            $total = (($rowT019['valor'] /6)*3) ."<br>"; 
            mysql_query("UPDATE D024 SET D024_Valor_Limite = '$total' WHERE D024_Id='{$rowT019[T019_D024_Id]}' AND D024_Flag_Ativo='S'");
            
            // verifica se ocorreu algum erro 
            $cErro = mysql_error();
            if(!empty($cErro)){
                mysql_query("rollback");
                return "ERRO: ".$cErro."<br>Cláusula: ".$cClausula; 
            }
        }
        
        mysql_query("COMMIT");
        
        return true;*/
        return false;
    }

    public function processarCarteiraCliente($D024_Id, $configuracoes = '') {
        global $g;

        $ativarCliente     = true;
        $observacaoInativo = '';
        if($configuracoes != ''){
            // Processo manual pela tela CRM->Clientes
            $diasAgenda        = $configuracoes['diasAgenda'];
            $diasOrcamento     = $configuracoes['diasOrcamento'];
            $diasPedido        = $configuracoes['diasPedido'];
            $diasNotaFiscal    = $configuracoes['diasNotaFiscal'];
        } else{
            // Gerado pelo processo automático do crontab, dados da empresa C004_Id = 1
            $diasAgenda        = $g['C031']['carteiraClienteDiasMaxContato'];
            $diasOrcamento     = $g['C031']['carteiraClienteDiasMaxOrcamento'];
            $diasPedido        = $g['C031']['carteiraClienteDiasMaxPedido'];
            $diasNotaFiscal    = $g['C031']['carteiraClienteDiasMaxNotaFiscal'];
        }

        $D024 = mysql_query("SELECT D024_Id,
                                    IFNULL(D024_Data_Cadastro, '0000-00-00') as D024_Data_Cadastro, 
                                    IFNULL(D024_Data_Ativo, '0000-00-00') as D024_Data_Ativo 
                               FROM D024 
                              WHERE D024_Id = '{$D024_Id}'");

        $mD024 = mysql_fetch_array($D024);

                
        if($mD024['D024_Data_Cadastro'] == '0000-00-00'){
            $diasCadastro = 999;
        } else {
            $diasCadastro = round(abs(strtotime(date("Y-m-d"))-strtotime($mD024['D024_Data_Cadastro']))/86400);
        }

        if($mD024['D024_Data_Ativo'] == '0000-00-00'){
            $diasAtivo = 120;
        } else {
            $diasAtivo = round(abs(strtotime(date("Y-m-d"))-strtotime($mD024['D024_Data_Ativo']))/86400);
        }

        if(!empty($diasNotaFiscal) && $diasNotaFiscal > 0){
            $sqlNotaFiscal = mysql_query("SELECT T007_Id 
                                            FROM T007 
                                       LEFT JOIN D024 ON D024_Id = T007_D024_Id 
                                           WHERE T007_D024_Id = '{$mD024['D024_Id']}' 
                                             AND IFNULL(T007_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasNotaFiscal} DAY LIMIT 1");

            if(mysql_num_rows($sqlNotaFiscal) <= 0 && $diasCadastro >= $diasNotaFiscal && $diasAtivo >= $diasNotaFiscal){
                $ativarCliente = false;
                $observacaoInativo .= "Nota em $diasNotaFiscal dia(s)<br>";
            }
        }

        if(!empty($diasPedido) && $diasPedido > 0){
            $sqlPedido = mysql_query("SELECT T005_Id 
                                        FROM T005 
                                   LEFT JOIN D024 ON D024_Id = T005_D024_Id 
                                       WHERE T005_D024_Id = '{$mD024['D024_Id']}' 
                                         AND IFNULL(T005_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasPedido} DAY LIMIT 1");
            if(mysql_num_rows($sqlPedido) <= 0 && $diasCadastro >= $diasPedido && $diasAtivo >= $diasPedido){
                $ativarCliente = false;
                $observacaoInativo .= "Pedido em $diasPedido dia(s)<br>";
            }
        }

        if(!empty($diasOrcamento) && $diasOrcamento > 0){
            $sqlOrcamento = mysql_query("SELECT T003_Id 
                                           FROM T003 
                                      LEFT JOIN D024 ON D024_Id = T003_D024_Id 
                                          WHERE T003_D024_Id = '{$mD024['D024_Id']}' 
                                            AND IFNULL(T003_Data_Emissao, '0000-00-00') > CURDATE() - INTERVAL {$diasOrcamento} DAY LIMIT 1");
            if(mysql_num_rows($sqlOrcamento) <= 0 && $diasCadastro >= $diasOrcamento && $diasAtivo >= $diasOrcamento){
                $ativarCliente = false;
                $observacaoInativo .= "Orcamento em $diasOrcamento dia(s)<br>";
            }
        }

        if(!empty($diasAgenda) && $diasAgenda > 0){
            $sqlAgenda = mysql_query("SELECT D085_Id 
                                        FROM D085 
                                   LEFT JOIN D024 ON D024_Id = D085_D024_Id 
                                       WHERE D085_D024_Id = '{$mD024['D024_Id']}' 
                                         AND IFNULL(D085_Data, '0000-00-00') > CURDATE() - INTERVAL {$diasAgenda} DAY LIMIT 1");
            if(mysql_num_rows($sqlAgenda) <= 0 && $diasCadastro >= $diasAgenda && $diasAtivo >= $diasAgenda){
                $ativarCliente = false;
                $observacaoInativo .= "Tarefa em $diasAgenda dia(s)";
            }
        }

        $sql = "SELECT D024_Flag_Comercial FROM D024 WHERE D024_Id = '{$mD024['D024_Id']}'";
        $comercialAntes = mysql_fetch_assoc(mysql_query($sql));

        if($ativarCliente){
            mysqli_query("UPDATE D024 SET D024_Flag_Comercial     = 'A', 
                                          D024_Data_Inativo       = '0000-00-00', 
                                          D024_Observacao_Inativo = '' 
                                    WHERE D024_Id = '{$mD024['D024_Id']}' 
                                      AND D024_Flag_Comercial != 'A'");
            $erroMysql = mysql_error();
            if (!empty($erroMysql)){
                return "Erro no Update ativo: {$erroMysql}";
            }
        } else {
            mysqli_query("UPDATE D024 SET D024_Flag_Comercial     = 'I', 
                                          D024_Data_Inativo       = CURDATE(), 
                                          D024_Observacao_Inativo = '{$observacaoInativo}' 
                                    WHERE D024_Id = '{$mD024['D024_Id']}' 
                                      AND D024_Flag_Comercial != 'I'");
            $erroMysql = mysql_error();
            if (!empty($erroMysql)){
                return "Erro no Update ativo: {$erroMysql}";
            }
        }       
        
        $sql = "SELECT D024_Flag_Comercial FROM D024 WHERE D024_Id = '{$mD024['D024_Id']}'";
        $comercialDepois = mysql_fetch_assoc(mysql_query($sql));
        
        if ($comercialAntes['D024_Flag_Comercial'] != $comercialDepois['D024_Flag_Comercial']) {
            // Grava na agenda a alteração de status do cliente
        // $status = ($ativarCliente) ? 'ATIVADO' : 'INATIVADO';
        // $mensagem = "CLIENTE {$status} POR {$g['C007']['C007_Primeiro_Nome']}";        
        // $ins = "INSERT INTO D085 (D085_D024_Id, D085_Data, D085_Mensagem) VALUES ('{$mD024['D024_Id']}', CURDATE(), '{$mensagem}')";
        // mysqli_query($ins);

        // $erroMysql = mysql_error();
        // if (!empty($erroMysql)){
        //     return "Erro inserir: {$erroMysql}";
        // }
        }

        return true;
    }

    /**
     * Trigger para fazer uma alteracao no consultaApi
     * 
     * @return bool
     */  
    function triggerAlteracaoConsultaApi(&$array,$r_CNPJs){
      
    }
}
