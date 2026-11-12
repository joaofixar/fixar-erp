<?php
namespace hardness;
definirClasse('CRM001');
class CRM001_
{
	/**
     * prepararImportarCSV
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSV($arquivo, $acaoId)
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

        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        if (!$ponteiro){
            return "ERRO - Não foi possível ler o arquivo $cArquivo ({$mArquivo['tmp_name']})";
        }

        //variavel criada para retornar os erros
        $return = "";

        // Array criado para a comparação dos nomes das colunas com o nome do documento
        // $array_cols=array('Código Cliente', 'Código do Produto', 'Preço');
        $array_cols=array('CODIGO CLIENTE', 'CODIGO PRODUTO', 'PRECO');

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
            
            $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";

            if ($linha==1){
                // Validar número de colunas e se o nome das colunas bate com o padrão  
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

            }else {      

            	//VALIDANDO OS CAMPOS OBRIGATÓRIOS      
                if(empty($array_linha[0])){
                    $return .=  $msgLinha . "A coluna Codigo Cliente está em branco.\\n";
                }
             
                if(empty($array_linha[1])){
                    $return .=  $msgLinha . "A coluna Codigo Produto está em branco.\\n".$array_linha[1];
                }

                if(empty($array_linha[2])){
                    $return .=  $msgLinha . "A coluna Preco está em branco.\\n".$array_linha[2];
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

                    // corrigindo a formatação do valor
                    $array_linha[2] = gCorrigeNumeroInverte($array_linha[2]);
                   
                    // procura o cliente selecionado
			        $sqlD161D024    = "SELECT D161_D024_Id FROM D161 WHERE D161_Id='{$acaoId}'";
			        $resultD161D024 = mysql_query($sqlD161D024);
			        $mD161D024      = mysql_fetch_array($resultD161D024);
			        
			        if($mD161D024['D161_D024_Id'] != $array_linha[0]){
			        	return "ERRO - Código do cliente informado ". $array_linha[0] . " Difere com o código do cliente selecionado ". $mD161D024['D161_D024_Id'];
			        }

                    // verifica se o produto existe 
                    $sqlD001 = "SELECT D001_id FROM D001 WHERE D001_Codigo_Produto = '{$array_linha[1]}'";
                    $resultD001 = mysql_query($sqlD001);
                    $mD001 = mysql_fetch_array($resultD001);
                    if(mysql_num_rows($resultD001) <= 0){
                        return "ERRO - Código Produto informado ". $array_linha[1] . " não cadastrado!";
                    }

                    // pegando o D009_id através do código do produto  para cadastrar
                    $sqlD009 = "SELECT D009_id FROM D009 WHERE D009_D001_id='{$mD001['D001_id']}' AND D009_C004_Id='{$g['empresaAtual']}'";
                    $resultD009 = mysql_query($sqlD009);
                    $mD009 = mysql_fetch_array($resultD009);
                    
                    // verifica se o cliente já tem produto cadastrado, se tiver será update, se não insert
			        $sqlD160    = "SELECT D160_Id FROM D160 WHERE D160_D024_id='{$array_linha[0]}' AND D160_D009_id='{$mD009['D009_id']}'";
			        $resultD160 = mysql_query($sqlD160);
                    $mResultD160 = mysql_fetch_array($resultD160);

			        if(mysql_num_rows($resultD160) > 0){
			        	$updateD160 = mysql_query("UPDATE D160 SET D160_Valor_Preco = '{$array_linha[2]}', 
			        								D160_Data_Hora = CURRENT_TIMESTAMP(), 
			        								D160_C007_Id = '{$g['usuarioAtual']}' 
			        								WHERE D160_Id='{$mResultD160['D160_Id']}'");
	                    $cErro = mysql_error();
	                    if (!empty($cErro)) {
                             mysql_query("ROLLBACK");
	                         return "Erro: " . $cErro . "\\n" . $updateD160;
	                    }
			        }else{
				        $cClausula = <<<EOT
				        			INSERT INTO D160(
				        				D160_D024_Id,
				        				D160_D009_Id,
										D160_Valor_Preco,
										D160_C007_Id,
										D160_Data_Hora,
										D160_D161_Id
				        			)VALUES
									(
										'{$array_linha[0]}',
	                            		'{$mD009['D009_id']}',
	                            		'{$array_linha[2]}',
	                            		'{$g['usuarioAtual']}',
	                            		CURRENT_TIMESTAMP(),
	                            		'{$acaoId}'
									)

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
            
            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram atualizados ".($linha-2)." preços tabela!";
        
        }else {
            return $return;
        } 
    }
}
