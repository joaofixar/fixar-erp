<?php
namespace hardness;
definirClasse('CAD023');
class CAD023_
{
    /**
     * prepararImportarCSVDepartamentos
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSVDepartamentos($arquivo)
    {
   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
    	//-- ABRE O ARQUIVO CSV
        $ponteiro  = fopen($mArquivo["tmp_name"], "r");

        //Variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols = array('Departamento'); 


        $linha = 1; 

        //O array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha
        while ($array_linha = fgets($ponteiro)) {
 
                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',$array_linha);

                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                         $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);

                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                    // Validar número de colunas e o se o nome das colunas bate com o padrão  
                    $total_cols = count($array_cols);
                    if ($total_cols!=count($array_linha)) {
                        $return .= "Deve haver apenas {$total_cols} coluna(s) \\n \\n";
                    }
                    //Valida os titulos das colunas
                    for ($count = 0;$count<count($array_linha);$count++){
                    	if (($count+1)>count($array_cols)) {     
                            $return .='A coluna "'.trim($array_linha[$count]).'" extrapola o número de colunas esperado.\\n \\n';  
                        } else if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .='O titulo "'.trim($array_linha[$count]).'" não confere com o titulo "'.$array_cols[$count].'" esperado.\\n \\n';                         
                        }
                    }
                        	    

                } else {                                                        
                    
                    //DEPARTAMENTO 
                    if(!empty($array_linha[0])){

                        $string = '';
                        for ($char = 0;$char<strlen($array_linha[0]);$char++){
                            $string .= ord(substr($array_linha[0],$char,1)).','; 
                        }
                        log('Códigos ASC '.$string);

                        $array_linha[0] = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha[0]);
                        $cClausula = "SELECT C008_Id FROM C008 WHERE C008_Tipo='".$array_linha[0]."'";
                        $cSQL_C008 = mysql_query($cClausula);
                        $mC008     = mysql_fetch_array($cSQL_C008);
                        $cErro     = mysql_error();
                        log($cClausula);
                        if(!empty($cErro)){
                            $return .=  $msgLinha."Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_C008) > 0) {          
                             $return .=  $msgLinha . "Departamento " .$array_linha[0]. " já está cadastrado.\\n";
                        }
                    }
                                      
                }
                $linha++;
        }

        //SE NÃO OCORREU NENHUM ERRO NAS VALIDAÇÕES
        if(empty($return)){

            //-- ABRE O ARQUIVO CSV
            $ponteiro = fopen($mArquivo["tmp_name"], "r");

            //INICIA A TRANSAÇÃO DO BANCO
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $linha=1;
            //PERCORRER AS LINHAS NOVAMENTE 
            while ($array_linha = fgets($ponteiro)) {
 
                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',$array_linha);

                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                         $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);

                if($linha==1){
                    $linha++;
                    continue;
                } 
                $linha++;
                if(!empty($array_linha[0])){
                    $cClausula = <<<EOT
                    insert into C008(
                            C008_Tipo
                            )
                            values
                            (
                            '{$array_linha[0]}')

EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "Erro: " . $cErro . "\\n" . $cClausula;
                    } 
                }           
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." departamentos!!";      
        } else {
            return $return;
        }        
    }


    /**
     * prepararImportarCSVSetores
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSVSetores($arquivo)
    {
   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('Setor'); 


        $linha = 1;        
        //Array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {
                // Limpa caracteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                    if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                         $array_linha[$count] = utf8_encode($array_linha[$count]); 
                         $array_linha[$count] = trim($array_linha[$count]);                     
                    }
                }
                //Limpa os espaços em brancos da direita e esquerda da string
                $array_linha[0] = trim($array_linha[0]);
                //Adiciona zeros à esquerda 
                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                // Validar número de colunas e o se o nome das colunas bate com o padrão  
                  	$total_cols = count($array_cols);
                    if ($total_cols!=count($array_linha)) {
                        $return .= "Deve haver apenas {$total_cols} coluna(s)\\n \\n";
                    }

                    for ($count=0;$count<count($array_linha);$count++){
                    	if (($count+1)>count($array_cols)) {     
                            $return .='A coluna "'.trim($array_linha[$count]).'" extrapola o número de colunas esperado.\\n \\n';  
                        } else if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .='O titulo "'.trim($array_linha[$count]).'" não confere com o titulo "'.$array_cols[$count].'" esperado.\\n \\n';                         
                        }
                    }      	    

                } else {                                                        
                    //SETOR
                    if(!empty($array_linha[0])){
                        $cClausula = "SELECT D060_Id FROM D060 WHERE D060_Descricao_Grupo='".trim($array_linha[0])."'";
                        $cSQL_D060 = mysql_query($cClausula);
                        $mD060     = mysql_fetch_array($cSQL_D060);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .=  $msgLinha."Erro: ".$cErro."\\nClausula: ".$cClausula;
                        }   
                        if (mysql_num_rows($cSQL_D060) > 0) {          
                             $return .=  $msgLinha ."Setor ".$array_linha[0]." já está cadastrado.\\n";
                        }
                    }    
                                      
                }
                $linha++;
        }

        //SE NÃO OCORREU NENHUM ERRO NAS VALIDAÇÕES
        if(empty($return)){

            //-- ABRE O ARQUIVO CSV
            $ponteiro = fopen($mArquivo["tmp_name"], "r");

            //INICIA A TRANSAÇÃO DO BANCO
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $linha=1;
            //PERCORRER AS LINHAS NOVAMENTE 
           while ($array_linha = fgets($ponteiro)) { 

            //Limpa caracteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                     $array_linha = explode(';',$array_linha);

                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                         $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);

                if($linha==1){
                    $linha++;
                    continue;
                } 
                $linha++;
                
                $cClausula = <<<EOT
                insert into D060(
                        D060_Descricao_Grupo
                        )
                        values
                        (
                        '{$array_linha[0]}')

EOT;
                $cSql  = mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . $cErro . "\\n" . $cClausula;
                }          
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." setores!!";      
        } else {
            return $return;
        }        
    }


    /**
     * prepararImportarCSVGrupos
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSVGrupos($arquivo)
    {   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('Grupo'); 


        $linha = 1;        
        //O array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {

             //Limpa characteres especiais do arquivo texto
            $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);
            //Separa as colunas
            $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                    if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                         $array_linha[$count] = utf8_encode($array_linha[$count]); 
                         $array_linha[$count] = trim($array_linha[$count]);                     
                    }
                }

                //Limpa os espaços em brancos da direita e esquerda da string
                $array_linha[0] = trim($array_linha[0]);
                //Adiciona zeros à esquerda
                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                //Validar número de colunas e o se o nome das colunas bate com o padrão  
                  	$total_cols = count($array_cols);
                    if ($total_cols!=count($array_linha)) {
                        $return .= "Deve haver apenas {$total_cols} coluna(s) \\n \\n";
                    }
                    //Valida os titulos das colunas
                    for ($count=0;$count<count($array_linha);$count++){
                    	if (($count+1)>count($array_cols)) {     
                            $return .='A coluna "'.trim($array_linha[$count]).'" extrapola o número de colunas esperado.\\n \\n';  
                        } else if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .='O titulo "'.trim($array_linha[$count]).'" não confere com o titulo "'.$array_cols[$count].'" esperado.\\n \\n';                         
                        }
                    }      	    

                } else {                                                        
                    
                   //GRUPO
                    if(!empty($array_linha[0])){
                        $cClausula = "SELECT D003_Id FROM D003 WHERE D003_Nome_Linha='".trim($array_linha[0])."'";
                        $cSQL_D003 = mysql_query($cClausula);
                        $mD003     = mysql_fetch_array($cSQL_D003);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .=  $msgLinha."Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D003) > 0) {          
                            $return .=  $msgLinha ."Grupo ".$array_linha[0]." já está cadastrado.\\n";
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
           while ($array_linha = fgets($ponteiro)) { 

                //Limpa caracteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                    if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                    $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);

                if($linha==1){
                    $linha++;
                    continue;
                } 
                $linha++;
                
                if(!empty($array_linha[0])){
                $cClausula = <<<EOT
                insert into D003(
                        D003_Nome_Linha
                        )
                        values
                        (
                        '{$array_linha[0]}')

EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . $cErro . "\\n" . $cClausula;
                    } 
                }         
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." grupos!!";      
        } else {
            return $return;
        }        
    }

    /**
     * prepararImportarCSVSubGrupos
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSVSubGrupos($arquivo)
    {   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro = fopen($mArquivo["tmp_name"], "r");

        //Variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('Sub-Grupo'); 


        $linha = 1;        
        //O array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {

                //Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);
                //Adiciona zeros à esquerda 
                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                    if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                         $array_linha[$count] = utf8_encode($array_linha[$count]); 
                         $array_linha[$count] = trim($array_linha[$count]);                     
                    }
                }

                //limpa os espaços em brancos da direita e esquerda da string
                $array_linha[0] = trim($array_linha[0]);

                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                // Validar número de colunas e o se o nome das colunas bate com o padrão  
                  	$total_cols = count($array_cols);
                    if ($total_cols!=count($array_linha)) {
                        $return .= "Deve haver apenas {$total_cols} coluna(s) \\n \\n";
                    }
                    //Valida os titulos das colunas
                    for ($count=0;$count<count($array_linha);$count++){
                        if (($count+1)>count($array_cols)) {     
                            $return .='A coluna "'.trim($array_linha[$count]).'" extrapola o número de colunas esperado.\\n \\n';                         
                        } else if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .='O titulo "'.trim($array_linha[$count]).'" não confere com o titulo "'.$array_cols[$count].'" esperado.\\n \\n';                         
                        }
                    }           	    

                } else {                                                        
                    
                   //SUB_GRUPO
                    if(!empty($array_linha[0])){    
                        $cClausula = "SELECT D002_Id FROM D002 WHERE D002_Descricao_Produto='".trim($array_linha[0])."'";
                        $cSQL_D002 = mysql_query($cClausula);
                        $mD002     = mysql_fetch_array($cSQL_D002);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .=  $msgLinha."Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D002) > 0) {          
                            $return .=  $msgLinha . "Sub-Grupo ".$array_linha[0]." já está cadastrado.\\n";
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
           while ($array_linha = fgets($ponteiro)) { 

                //Limpa caracteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                         $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);

                if($linha==1){
                    $linha++;
                    continue;
                } 
                $linha++;
                
                if(!empty($array_linha[0])){
                $cClausula = <<<EOT
                insert into D002(
                        D002_Descricao_Produto
                        )
                        values
                        (
                        '{$array_linha[0]}')

EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . $cErro . "\\n" . $cClausula;
                    }
                }          
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." sub-grupos!!";      
        } else {
            return $return;
        }        
    }


    /**
     *importarCSVMarcas($arquivo)
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSVMarcas($arquivo)
    {   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        //Variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('Marca'); 


        $linha = 1;        
        //O array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {

                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);
 
                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                    if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                         $array_linha[$count] = utf8_encode($array_linha[$count]); 
                         $array_linha[$count] = trim($array_linha[$count]);                     
                    }
                }

                //limpa os espaços em brancos da direita e esquerda da string
                $array_linha[0] = trim($array_linha[0]);
                //adiciona zeros à esquerda 
                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                // Validar número de colunas e o se o nome das colunas bate com o padrão  
                  $total_cols = count($array_cols);
                    if ($total_cols!=count($array_linha)) {
                        $return .= "Deve haver apenas {$total_cols} coluna(s) \\n \\n";
                    }
                    for ($count=0;$count<count($array_linha);$count++){
                    	if (($count+1)>count($array_cols)) {     
                            $return .='A coluna "'.trim($array_linha[$count]).'" extrapola o número de colunas esperado.\\n \\n';  
                        } else if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .='O titulo "'.trim($array_linha[$count]).'" não confere com o titulo "'.$array_cols[$count].'" esperado.\\n \\n';                         
                        }
                    }      	    

                } else {                                                        
                    
                   //MARCA
                    if(!empty($array_linha[0])){
                        $cClausula = "SELECT D082_Id FROM D082 WHERE D082_Marca='".trim($array_linha[0])."'";
                        $cSQL_D082 = mysql_query($cClausula);
                        $mD082     = mysql_fetch_array($cSQL_D082);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .=  $msgLinha."Erro: ".$cErro."\\nClausula: ".$cClausula;
                        }   
                        if (mysql_num_rows($cSQL_D082) > 0) {          
                            $return .=  $msgLinha . "A Marca ".$array_linha[0]." já está cadastrada.\\n";
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
           while ($array_linha = fgets($ponteiro)) {

                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha); 

                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                         $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);

                if($linha==1){
                    $linha++;
                    continue;
                } 
                $linha++;

                if(!empty($array_linha[0])){                
                $cClausula = <<<EOT
                insert into D082(
                        D082_Marca
                        )
                        values
                        (
                        '{$array_linha[0]}')

EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . $cErro . "\\n" . $cClausula;
                    }
                }          
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." marcas!!";      
        } else {
            return $return;
        }        
    }


    /**
     * prepararImportarCSVLocacoes
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSVLocacoes($arquivo)
    {   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro = fopen($mArquivo["tmp_name"], "r");

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols = array('Locacao'); 

        $linha = 1;        
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {

                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                    if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                         $array_linha[$count] = utf8_encode($array_linha[$count]); 
                         $array_linha[$count] = trim($array_linha[$count]);                     
                    }
                }

                //limpa os espaços em brancos da direita e esquerda da string
                $array_linha[0] = trim($array_linha[0]);

                //adiciona zeros à esquerda 
                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                    // Validar número de colunas e o se o nome das colunas bate com o padrão  
                    $total_cols = count($array_cols);
                    if ($total_cols!=count($array_linha)) {
                        $return .= "Deve haver apenas {$total_cols} coluna(s) \\n \\n";
                    }
                    
                    for ($count=0;$count<count($array_linha);$count++){
                    	if (($count+1)>count($array_cols)) {     
                            $return .='A coluna "'.trim($array_linha[$count]).'" extrapola o número de colunas esperado.\\n \\n';  
                        } else if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .='O titulo "'.trim($array_linha[$count]).'" não confere com o titulo "'.$array_cols[$count].'" esperado.\\n \\n';                         
                        }
                    }         	    

                } else {                                                        
                    
                    //LOCACOES
                    if(!empty($array_linha[0])){
                        $cClausula = "SELECT D004_Id FROM D004 WHERE D004_Local='{$array_linha[0]}' AND D004_C004_Id = '{$g['empresaAtual']}'";
                        $teste = 'marcelo';
                        $cSQL_D004 = mysql_query($cClausula);
                        $mD004     = mysql_fetch_array($cSQL_D004);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .=  $msgLinha."Erro: ".$cErro."\\nClausula: ".$cClausula;
                        } 
                        if (mysql_num_rows($cSQL_D004) > 0) {          
                            $return .=  $msgLinha . "A Locação ".$array_linha[0]." já está cadastrada.\\n";
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
           while ($array_linha = fgets($ponteiro)) { 

                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',utf8_encode($array_linha));

                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                         $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);

                if($linha==1){
                    $linha++;
                    continue;
                } 
                $linha++;
                

                if(!empty($array_linha[0])){
                $cClausula = <<<EOT
                insert into D004(
                        D004_Local,
                        D004_C004_Id
                        )
                        values
                        (
                        '{$array_linha[0]}',
                        '{$g['empresaAtual']}')

EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . $cErro . "\\n" . $cClausula;
                    }
                }          
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." locações!!";      
        } else {
            return $return;
        }        
    }

}
