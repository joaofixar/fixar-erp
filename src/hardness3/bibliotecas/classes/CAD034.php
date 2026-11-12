<?php
namespace hardness;
definirClasse('CAD034');
class CAD034_
{

    /**
     * cad034Reprocessar
     *
     * @param String $D014_Id
     * @param String $campo_Editado
     * @return String/bool
     */ 	
    public function cad034Reprocessar($D014_Id, $campo_Editado)
    {
        if (empty($Campo_Editado)) {
            return "Campo_Editado nao existe!";
        }
        
        $Conteudo_Campo_Editado = formata_valor_gravar($Conteudo_Campo_Editado, 3);
        
        $Mes = str_pad("$Mes", 2, "0", STR_PAD_LEFT);
        
        $T018  = mysql_query("SELECT T018_Id FROM T018 WHERE T018_Mes='$Mes' AND T018_Ano='$Ano' AND T018_D014_Id='$D014_Id' AND T018_C004_Id='$Empresa_Atual'");
        $mT018 = mysql_fetch_array($T018);
        
        if ($mT018[T018_Id] > 0) {
            mysql_query("UPDATE T018 SET T018_Flag_Confirmado='S',T018_Valor='$Conteudo_Campo_Editado' WHERE T018_Id='$mT018[T018_Id]'");
        } else {
            mysql_query("INSERT INTO T018 (T018_Valor,T018_Mes,T018_Ano,T018_D014_Id,T018_C004_Id,T018_Flag_Confirmado) VALUES ('$Conteudo_Campo_Editado','$Mes','$Ano','$D014_Id','$Empresa_Atual','S')");
        }
        return true;
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



        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
       
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        if (!$ponteiro){
            return "ERRO - Não foi possível ler o arquivo $cArquivo ({$mArquivo['tmp_name']})";
        }

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('NOME DA CONTA', 'DIA VENCIMENTO', 'TIPO', 'GRUPO', 'SUB-GRUPO', 'CENTRO DE CUSTOS', 'GRUPO DRE', 'PORTADOR', 'DIA SEMANA VENCIMENTO', 'EMPRESA', 'OBSERVACAO', 'ATIVO', 'VERIFICAR SALDO', 'REGISTRAR CONTA', 'PAGAMENTO UNICO');
         //posicao do array     <0>              <1>           <2>      <3>      <4>          <5>                  <6>         <7>           <8>                    <9>        <10>         <11>        <12>              <13>                 <14>        
 

        $linha = 1;        
        $incrementaResult=0;
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
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
                $array_linha[13] = trim($array_linha[13]);
                $array_linha[14] = trim($array_linha[14]);

                 log("teste: ".$array_linha[3]);

                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                    // Validar número de colunas e o se o nome das colunas bate com o padrão  
                    if (count($array_cols)!=count($array_linha)) {
                        $return .= "O número de colunas é diferente de " . count($array_cols) . "\\n";
                    }
                    // Validação dos titulos das colunas
                    for ($count=0;$count<count($array_linha);$count++){
                        $array_linha[$count] = strtoupper($array_linha[$count]);
                        if (trim($array_cols[0]) != trim($array_linha[0])) {     
                            $return .="A coluna ".trim($array_linha[$count])." do arquivo não confere com a coluna ".$array_cols[$count]." esperada.\\n \\n";
                        }
                    }                   

                } else {
                    $array_linha[0]=mb_strtoupper($array_linha[0]);
                    $array_linha[2]=mb_strtoupper($array_linha[2]);
                    $array_linha[3]=mb_strtoupper($array_linha[3]);                  
                    $array_linha[4]=mb_strtoupper($array_linha[4]);
                    $array_linha[5]=mb_strtoupper($array_linha[5]);
                    $array_linha[7]=mb_strtoupper($array_linha[7]);
                    $array_linha[9]=mb_strtoupper($array_linha[9]);
                    $array_linha[11]=mb_strtoupper($array_linha[11]);
                    $array_linha[12]=mb_strtoupper($array_linha[12]);
                    $array_linha[13]=mb_strtoupper($array_linha[13]);
                    $array_linha[14]=mb_strtoupper($array_linha[14]);


                    // Valida a coluna 1 - Nome da conta
                    if(empty($array_linha[0])){
                        $return .=  $msgLinha . "A coluna Nome da conta está em branco.\\n";
                    }else{
                            // Validar se código está duplicado
                            $cClausula = "SELECT D014_Id from D014 WHERE D014_SubConta='$array_linha[0]'";
                            $cSQL_D014 = mysql_query($cClausula);
                            $mD014     = mysql_fetch_array($cSQL_D014);
                            $cErro     = mysql_error();
                            if(!empty($cErro)){
                                $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                            }     
                            if(mysql_num_rows($cSQL_D014) != 0){
                                $return .= $msgLinha . "A conta ". $array_linha[0] ." já está cadastrada!\\n";
                            }    
                        }                        
                    
                    // Valida se a coluna 2 - Dia vencimento está em branco                   
                    if(empty($array_linha[1])){
                        $return .=  $msgLinha . "A coluna Dia vencimento está em branco.\\n";
                    }else if(($array_linha[1]<1)||($array_linha[1]>31)){
                        $return .= $msgLinha . "O dia vencimento ". $array_linha[1] ." não é válido! Informe uma data entre 1 e 31.\\n";
                    }

                    // Valida se a coluna 3 tipo- Tipo está em branco
                    if(empty($array_linha[2])){
                        $return .=  $msgLinha . "A coluna Tipo está em branco.\\n";
                    }else if(($array_linha[2]!="PG") && (($array_linha[2]!="RC"))){
                        $return .= $msgLinha . "O tipo ". $array_linha[2] ." não é válido! O tipo deve ser PG ou RC.\\n";    
                    }

                    //Valida se o GRUPO existe
                    if(!empty($array_linha[3])){
                        $cSelectGrupo = "SELECT D032_Id FROM D032 where D032_Descricao='$array_linha[3]' ";
                        $cSQL_D032 = mysql_query($cSelectGrupo);
                        $resultGrupo[$incrementaResult] = mysql_result($cSQL_D032, 0);
                        $mD032 = mysql_fetch_array($cSQL_D032);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectGrupo; 
                        }
                        if(mysql_num_rows($cSQL_D032) == 0){
                            $return .= $msgLinha . "O grupo ". $array_linha[3] ." não está cadastrado!\\n";
                        } 
                    }
                    
                    //Valida se o Sub-Grupo exite 
                    if(!empty($array_linha[4])){
                        $cSelectSGrupo = "SELECT D154_Id FROM D154  where D154_Descricao='$array_linha[4]' ";
                        $cSQL_D154 = mysql_query($cSelectSGrupo);
                        $resultSubGrupo[$incrementaResult] = mysql_result($cSQL_D154, 0);
                        $mD154 = mysql_fetch_array($cSQL_D154);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectGrupo; 
                        }
                        if((mysql_num_rows($cSQL_D154) == 0)) {
                            $return .= $msgLinha . "O Sub-Grupo ". $array_linha[4] ." não está cadastrado!\\n";
                        } 
                    }

                    //Valida Centro de custos 
                    if(!empty($array_linha[5])){
                        $cSelectCusto = "SELECT D073_Id FROM D073  where D073_Nome='$array_linha[5]' ";
                        $cSQL_D073 = mysql_query($cSelectCusto);
                        $resultCentroDeCustos[$incrementaResult] = mysql_result($cSQL_D073, 0);
                        $mD073 = mysql_fetch_array($cSQL_D073);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectGrupo; 
                        }
                        if(mysql_num_rows($cSQL_D073) == 0){
                            $return .= $msgLinha . "O Centro de Custos ". $array_linha[5] ." não está cadastrado!\\n";
                        } 
                    }


                    //Valida Grupo DRE .
                    if(!empty($array_linha[6])){
                        if(($array_linha[6] < 1) || ($array_linha[6] > 8)){
                            $return .= $msgLinha . "O grupo DRE ". $array_linha[6] ." não é válido! Digite um valor entre 1 e 8.\\n";
                        }
                    }

                    //Valida Portador
                    if(!empty($array_linha[7])){
                        $cSelectPortador = "SELECT D031_Id FROM D031 where D031_Portador='$array_linha[7]' ";
                        $cSQL_D031 = mysql_query($cSelectPortador);
                        $resultPortador[$incrementaResult] = mysql_result($cSQL_D031, 0);
                        $mD031 = mysql_fetch_array($cSQL_D031);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectGrupo; 
                        }
                        if(mysql_num_rows($cSQL_D031) == 0){
                            $return .= $msgLinha . "O Portador ". $array_linha[7] ." não está cadastrado!\\n";
                        } 
                    }
                    

                    //Valida dia da semana do Vencimento
                    if(!empty($array_linha[8])){
                        if(($array_linha[8] < 1) || ($array_linha[8] > 7)){

                            $return .= $msgLinha . "O dia da semana ". $array_linha[8] ." não é válido! Digite um valor entre 1 e 7.\\n";    
                        }
                    }

                    //Valida Empresa
                    if(!empty($array_linha[9])){
                        $cSelectEmpresa = "SELECT D024_Id FROM D024 where D024_Id='$array_linha[9]'";
                        $cSQL_D014_D024 = mysql_query($cSelectEmpresa);
                        $resultEmpresa[$incrementaResult] = mysql_result($cSQL_D014_D024, 0);
                        $mD014_D024 = mysql_fetch_array($cSQL_D014_D024);

                        log("Id empresa : $mD014_D024");

                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectEmpresa; 
                        }
                        if(mysql_num_rows($cSQL_D014_D024) == 0){
                            $return .= $msgLinha . "A Empresa ". $array_linha[9] ." não está cadastrada!\\n";
                        } 
                    }

                    //Valida Ativo
                    if(!empty($array_linha[11])){
                        $array_linha[11] = strtoupper($array_linha[11]);
                        if(($array_linha[11]!="S") && (($array_linha[11]!="N"))){
                            $return .= $msgLinha . "Ativo ". $array_linha[11] ." não é válido! Preencha com S ou N. \\n";    
                        }
                    }

                    //Valida Verificar Saldo
                    if(!empty($array_linha[12])){
                        if(($array_linha[12]!="S") && (($array_linha[12]!="N"))){
                            $return .= $msgLinha . "Verificar Saldo ". $array_linha[12] ." não é válido! Preencha com S ou N. \\n";    
                        }
                    }

                    // Valida Registrar Conta
                    if(!empty($array_linha[13])){
                        if(($array_linha[13]!="S") && (($array_linha[13]!="N"))){
                            $return .= $msgLinha . "Registrar Conta ". $array_linha[13] ." não é válido! Preencha com S ou N. \\n";    
                        }
                    }

                    //Valida Pagamento único
                    if(!empty($array_linha[14])){
                        if(($array_linha[14]!="S") && (($array_linha[14]!="N"))){
                            $return .= $msgLinha . "Pagamento Único ". $array_linha[14] ." não é válido! Preencha com S ou N. \\n";    
                        }
                    }
                    $incrementaResult++;
               }

                $linha++;
        }

        //SE NÃO OCORREU NENHUM ERRO NAS VALIDAÇÕES
        if(empty($return)){
            $incrementaResult=0;
            //-- ABRE O ARQUIVO CSV
            $ponteiro      = fopen($mArquivo["tmp_name"], "r");

            //INICIA A TRANSAÇÃO DO BANCO
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $linha=1;

            //PERCORRER AS LINHAS NOVAMENTE PARA REALIZAR O INSERT
            while (($array_linha = fgets($ponteiro))) {            
                    if($linha==1){
                        $linha++;
                        continue;
                    }                                    
                    
                    $array_linha = explode(';',$array_linha);
                    for ($count=0;$count<count($array_linha);$count++){
                         if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                             $array_linha[$count] = utf8_encode($array_linha[$count]);
                         }
                    }

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
                    $array_linha[13] = trim($array_linha[13]);
                    $array_linha[14] = trim($array_linha[14]);

                    $array_linha[0]=mb_strtoupper($array_linha[0]);
                    $array_linha[2]=strtoupper($array_linha[2]);
                    $array_linha[3]=mb_strtoupper($array_linha[3]);
                    $array_linha[4]=mb_strtoupper($array_linha[4]);
                    $array_linha[5]=mb_strtoupper($array_linha[5]);
                    $array_linha[7]=mb_strtoupper($array_linha[7]);
                    $array_linha[9]=mb_strtoupper($array_linha[9]);
                    $array_linha[11]=strtoupper($array_linha[11]);
                    $array_linha[12]=strtoupper($array_linha[12]);
                    $array_linha[13]=strtoupper($array_linha[13]);
                    $array_linha[14]=strtoupper($array_linha[14]);

                    $cInsert = "INSERT INTO D014 (D014_SubConta, D014_Dia_Vencimento, D014_Flag_Tipo, D014_Flag_Ativo, D014_D032_Id, D014_D154_Id, D014_D073_Id, 
                                                  D014_Grupo_DRE, D014_D031_Id, D014_Dia_Semana_Vencimento, D014_D024_Id, D014_Observacao, D014_Flag_Verificar_Saldo, D014_Flag_Registrar_Conta, D014_Flag_Pagamento_Unico) 
                                VALUES ('$array_linha[0]', '$array_linha[1]', '$array_linha[2]', '$array_linha[11]', '$resultGrupo[$incrementaResult]', '$resultSubGrupo[$incrementaResult]', 
                                        '$resultCentroDeCustos[$incrementaResult]', '$array_linha[6]', '$resultPortador[$incrementaResult]', '$array_linha[8]', '$resultEmpresa[$incrementaResult]', 
                                        '$array_linha[10]', '$array_linha[12]', '$array_linha[13]', '$array_linha[14]')";

                    $cSQL_D014 = mysql_query($cInsert);
                    $incrementaResult++;
                    $linha++;


            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram inseridos ".($linha-2)." Planos de contas!";

      
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

        log("nome do arquivo recebido $cArquivo / {$mArquivo['tmp_name']}");
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        if (!$ponteiro){
            return "ERRO - Não foi possível ler o arquivo $cArquivo ({$mArquivo['tmp_name']})";
        }

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('GRUPO', 'ATIVO', 'TIPO');
        //posicao do array  <0>      <1>      <2>      

        $linha = 1;        
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {

                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);
                
                $array_linha = explode(';',$array_linha);
                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                        $array_linha[$count] = utf8_encode($array_linha[$count]);
                     }
                }

                $array_linha[0] = trim($array_linha[0]);
                $array_linha[1] = trim($array_linha[1]);
                $array_linha[2] = trim($array_linha[2]);

                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                    // Validar número de colunas e o se o nome das colunas bate com o padrão  
                    if (count($array_cols)!=count($array_linha)) {
                        $return .= "O número de colunas é diferente de " . count($array_cols) . "\\n";
                    }

                    // Validação dos titulos das colunas
                    for ($count=0;$count<count($array_linha);$count++){
                        $array_linha[$count] = strtoupper($array_linha[$count]);
                        if (trim($array_cols[0]) != trim($array_linha[0])) {     
                            $return .="A coluna ".trim($array_linha[$count])." do arquivo não confere com a coluna ".$array_cols[$count]." esperada.\\n \\n";
                        }
                    }                   

                } else { 
                    $array_linha[1]=strtoupper($array_linha[1]);
                    $array_linha[0]=strtoupper($array_linha[0]);                                   
                    log("mostrar grupo: $array_linha[0]");

                    // Valida a coluna 0 - Grupo
                    if(empty($array_linha[0])){
                        $return .=  $msgLinha . "A coluna Grupo da conta está em branco.\\n";
                    }
                    else {
                        $cSelectG = "SELECT D032_Id FROM D032 where D032_Descricao='$array_linha[0]'";
                        $cSQL_D032 = mysql_query($cSelectG);
                        $mD154 = mysql_fetch_array($cSQL_D032);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro consuta grupo: ".mb_detect_encoding($array_linha[0]).' '.$cErro."\\nClausula: ".$cSelectSG; 
                        }
                        if(mysql_num_rows($cSQL_D032) != 0){
                            $return .= $msgLinha . "O Grupo ". $array_linha[0] ." já está cadastrado!\\n";
                        } 
                    }

                    //Valida Ativo
                    if(!empty($array_linha[1])){
                        $array_linha[1] = strtoupper($array_linha[1]);
                        if(($array_linha[1]!="S") && (($array_linha[1]!="N"))){
                            $return .= $msgLinha . "Ativo ". $array_linha[1] ." não é válido! Preencha com S ou N \\n";    
                        }
                    }

                    //log("Exibe tipo $array_linha[2]");
                    //Valida Tipo
                    if(empty($array_linha[2])){
                        $return .= $msgLinha . "O Tipo não é válido! Preencha com 01, 10, 20, 25, 30, 40 ou 50.\\n"; 
                    }else if(!empty($array_linha[2])){
                        if(($array_linha[2]==0)){
                            $return .= $msgLinha . "O Tipo ". $array_linha[2] ." não é válido! Preencha com 01, 10, 20, 25, 30, 40 ou 50. \\n";
                        }else if(($array_linha[2] != 1) && ($array_linha[2] != 10) && ($array_linha[2] != 20) && ($array_linha[2] != 25) && ($array_linha[2] != 30) && ($array_linha[2] != 40) && ($array_linha[2] != 50)){
                            $return .= $msgLinha . "O Tipo ". $array_linha[2] ." não é válido! Preencha com 01, 10, 20, 25, 30, 40 ou 50. \\n";
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

            log("inicio");
            $data = date('Y-m-d');

            log("exibe data $data");

            //PERCORRER AS LINHAS NOVAMENTE PARA REALIZAR O INSERT
            while (($array_linha = fgets($ponteiro))) {            
                    if($linha==1){
                        $linha++;
                        continue;
                    }

                    $array_linha = explode(';',$array_linha);
                    for ($count=0;$count<count($array_linha);$count++){
                         if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                            $array_linha[$count] = utf8_encode($array_linha[$count]);
                            log("teste: ".$array_linha[0]);


                         }
                    }

                    $array_linha[0] = trim($array_linha[0]);
                    $array_linha[1] = trim($array_linha[1]);
                    $array_linha[2] = trim($array_linha[2]);

                    $array_linha[1] = mb_strtoupper($array_linha[1]);
                    $array_linha[0] = mb_strtoupper($array_linha[0]);
                    
                    $cInsert = "INSERT INTO D032 (D032_Descricao, D032_Flag_Ativo, D032_Tipo, D032_Data_Inclusao) 
                                VALUES ('$array_linha[0]', '$array_linha[1]', '$array_linha[2]', '$data')";

                    $cSQL_D014 = mysql_query($cInsert);

                    $linha++;


            }
  
            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram inseridos ".($linha-2)." Grupos!";

      
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
        $array_cols=array('SUB-GRUPO');
        //posicao do array     <0>        
 

        $linha = 1;        
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {

                // if(($array_linha == fgetcsv($ponteiro,1000,',')) ==True){
                //     $return .= "Documento não está com o formato correto. Favor verificar!\\n";
                // }     

                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                if ($tipoArquivo!='UTF-8'){
                    $array_linha = explode(';',utf8_encode($array_linha));
                } else {
                    $array_linha = explode(';',$array_linha);
                } 
              
                $array_linha[0] = trim($array_linha[0]);
                
                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                    // Validar número de colunas e o se o nome das colunas bate com o padrão  
                    if (count($array_cols)!=count($array_linha)) {
                        $return .= "O número de colunas é diferente de " . count($array_cols) . "\\n";
                    }
                    // Validação dos titulos das colunas
                    for ($count=0;$count<count($array_linha);$count++){
                        $array_linha[$count] = strtoupper($array_linha[$count]);
                        if (trim($array_cols[0]) != trim($array_linha[0])) {     
                            $return .="A coluna ".trim($array_linha[$count])." do arquivo não confere com a coluna ".$array_cols[$count]." esperada.\\n \\n";
                        }
                    }                   

                } else {                                                       
                   
                    // Valida a coluna 1 - Sub-Grupo
                    if(empty($array_linha[0])){
                        $return .=  $msgLinha . "A coluna Sub-Grupo está em branco.\\n";
                    }
                    else {
                        $array_linha[0] = strtoupper($array_linha[0]);
                        $cSelectSG = "SELECT D154_Id FROM D154 where D154_Descricao='$array_linha[0]'";
                        $cSQL_D154 = mysql_query($cSelectSG);
                        $mD154 = mysql_fetch_array($cSQL_D154);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectSG; 
                        }
                        if(mysql_num_rows($cSQL_D154) != 0){
                            $return .= $msgLinha . "O sub-Grupo ". $array_linha[0] ." já está cadastrado!\\n";
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

            //PERCORRER AS LINHAS NOVAMENTE PARA REALIZAR O INSERT
            while (($array_linha = fgets($ponteiro))) {            
                    if($linha==1){
                        $linha++;
                        continue;
                    }

                    $array_linha = explode(';',$array_linha);
                    for ($count=0;$count<count($array_linha);$count++){
                         if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                             $array_linha[$count] = utf8_encode($array_linha[$count]);
                         }
                    }

                    $array_linha[0]=trim($array_linha[0]);
                    $array_linha[0]=mb_strtoupper($array_linha[0]);
                    
                    $cInsert = "INSERT INTO D154 (D154_Descricao)
                                VALUES ('$array_linha[0]')";

                    $cSQL_D014 = mysql_query($cInsert);

                    $linha++;

            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram inseridos ".($linha-2)." Sub-Grupos!";
      
        } else {

            return $return;

        }        
    }    
}
