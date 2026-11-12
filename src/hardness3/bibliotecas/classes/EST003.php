<?php
namespace hardness;

definirClasse('EST003');
class EST003_
{

    /**
     * Cria o histórico oficial de balanço para uma locação.
     *
     * O T214 é o identificador textual usado em T001_Descricao_Operacao pelo
     * balanço cíclico. Esta rotina é compartilhada pelo lote legado e pela
     * efetivação unitária.
     */
    public function est003InserirLancamentoBalanco($mD009, $T214_Id, $quantidade = null)
    {
        global $g;

        $C007 = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$mD009['T066_C007_Id_Marcou_Balanco']}'");
        $mC007 = mysql_fetch_array($C007);
        $usuarioMarcacao = substr((string) $mC007['C007_Primeiro_Nome'], 0, 10);
        $usuarioProcessamento = substr((string) $g['C007']['C007_Primeiro_Nome'], 0, 10);
        $quantidade = $quantidade === null ? $mD009['T066_Quantidade_Balanco'] : $quantidade;

        $sql = <<<EOT
            INSERT INTO T001 (
                T001_D024_Id_Cliente,
                T001_D024_Id,
                T001_D009_Id,
                T001_C004_Id,
                T001_Codigo_Produto,
                T001_Data_Lancamento,
                T001_Descricao_Operacao,
                T001_Quantidade,
                T001_Flag_Complemento,
                T001_Flag_Operacao,
                T001_Valor_Preco_Unitario,
                T001_Observacao,
                T001_Valor_Custo_Unitario,
                T001_Flag_Atualiza_Custo,
                T001_Quantidade_Saldo,
                T001_T066_Id,
                T001_D004_Id
            ) VALUES (
                0,
                '{$mD009['D049_D024_Id']}',
                '{$mD009['D009_Id']}',
                '{$g['empresaAtual']}',
                '{$mD009['D001_Codigo_Produto']}',
                CURRENT_DATE(),
                'BL-{$T214_Id} ({$usuarioMarcacao}/{$usuarioProcessamento})',
                '{$quantidade}',
                '',
                'B',
                '0.00000',
                '',
                '0.00000',
                'N',
                0,
                '{$mD009['T066_Id']}',
                '{$mD009['D004_Id']}'
            )
EOT;
        mysql_query($sql);
        $erro = mysql_error();
        return empty($erro) ? true : 'Erro: ' . $erro;
    }

    /**
     * Efetiva, em uma transação, um único T066 já reservado pelo operador.
     */
    public function est003EfetivarBalancoT066($T066_Id, $quantidade)
    {
        global $g;

        $T066_Id = (int) $T066_Id;
        $empresaAtual = (int) $g['empresaAtual'];
        $usuarioAtual = (int) $g['usuarioAtual'];
        if ($T066_Id <= 0 || $empresaAtual <= 0 || $usuarioAtual <= 0) {
            return 'Dados inválidos para efetivar o balanço.';
        }

        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        mysql_query('SET autocommit=0');
        mysql_query('START TRANSACTION');

        $rollback = function ($mensagem) {
            mysql_query('ROLLBACK');
            return $mensagem;
        };

        $sql = <<<EOT
            SELECT
                T066.T066_Id,
                T066.T066_D009_Id,
                T066.T066_D004_Id AS D004_Id,
                T066.T066_Flag_Balanco,
                T066.T066_C007_Id_Marcou_Balanco,
                D009.D009_Id,
                D009.D009_C004_Id,
                D049.D049_D024_Id,
                D001.D001_Codigo_Produto
            FROM T066
            INNER JOIN D009 ON D009.D009_Id=T066.T066_D009_Id
            INNER JOIN D004 ON D004.D004_Id=T066.T066_D004_Id
            INNER JOIN D049 ON D049.D049_Id=D009.D009_D049_Id
            INNER JOIN D001 ON D001.D001_Id=D049.D049_D001_Id
            WHERE T066.T066_Id='{$T066_Id}'
              AND D009.D009_C004_Id='{$empresaAtual}'
              AND D004.D004_C004_Id='{$empresaAtual}'
            LIMIT 1
            FOR UPDATE
EOT;
        $resultado = mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) return $rollback('Erro ao validar a locação: ' . $erro);
        $mD009 = mysql_fetch_array($resultado);
        if (!$mD009) return $rollback('Registro de produto por locação não encontrado na empresa ativa.');
        if ($mD009['T066_Flag_Balanco'] !== 'S' || (int) $mD009['T066_C007_Id_Marcou_Balanco'] !== $usuarioAtual) {
            return $rollback('A marcação de balanço não pertence a este usuário.');
        }

        $sql = "INSERT INTO T214 (T214_C007_Id, T214_C004_Id, T214_Data_Hora_Balanco) VALUES ('{$usuarioAtual}', '{$empresaAtual}', CURRENT_TIMESTAMP())";
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) return $rollback('Erro ao criar a identificação do balanço: ' . $erro);
        $T214_Id = $g['mysqlLastId'];

        $retorno = $this->est003InserirLancamentoBalanco($mD009, $T214_Id, $quantidade);
        if ($retorno !== true) return $rollback($retorno);

        $retorno = $CAD002->D001_reprocessa_historico(false, $mD009['D009_Id'], 100);
        if ($retorno !== true) return $rollback('Erro no reprocessamento do estoque: ' . $retorno);

        $sql = <<<EOT
            UPDATE T066
               SET T066_Quantidade_Balanco='0.00',
                   T066_Data_Balanco=CURDATE(),
                   T066_Frequencia_Apos_Balanco=0,
                   T066_Flag_Balanco='N',
                   T066_C007_Id_Marcou_Balanco=''
             WHERE T066_Id='{$mD009['T066_Id']}'
               AND T066_D009_Id='{$mD009['D009_Id']}'
EOT;
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) return $rollback('Erro ao finalizar o balanço: ' . $erro);

        mysql_query('COMMIT');
        return true;
    }

	/**
	 * est003faturarPedido
	 *
	 * @param int $T005_Id
	 * @return true/string
	 */
    public function est003faturarPedido($T005_Id)
    {
        global $g;
        if (empty($T005_Id)) {
            return 'Não encontrado o Id do pedido (T005_ID)';
        }

        $sql = <<<EOT
			UPDATE 
				T005
			SET
				T005_Flag_Status='10',
				T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1)
			WHERE
				T005_Id = $T005_Id
EOT;
        mysql_query($sql);
        
        // Insere registros no histórico do pedido (T178)
        mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aguardando Conferência','')");
        
        return true;
    }

    /**
	 * est003BalancoCiclico
	 *
	 * @return true/string
	 */
    public function est003BalancoCiclico($sql)
    {
        global $g;

        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

    	$sql = base64_decode($sql);
    	$sql = str_replace('LIMIT 0,100', '', $sql);
		$sql = gInsertExtraWhere($sql, "T066_Flag_Balanco = 'S'");

        mysql_query("set autocommit=0");
        mysql_query("START TRANSACTION");

        /*
        $sql = <<<EOT
			SELECT 
				*
			FROM D009
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			left join T066 on T066_D009_Id=D009_Id
			LEFT JOIN D004 ON D004_Id=T066_D004_Id
			WHERE
				T066_Flag_Balanco = 'S'
				AND D009_C004_Id = '{$g['empresaAtual']}'
EOT;
*/
        $D009 = mysql_query($sql);
        $erro = mysql_error();
        if($erro){
        	return "1. Erro: ".$erro;
        }
        if (mysql_num_rows($D009) <= 0) {
            return "Nenhum produto selecionado";
        }

        $sql = <<<EOT
			INSERT INTO T214 (
				T214_C007_Id,
				T214_C004_Id,
				T214_Data_Hora_Balanco
			) VALUES (
				'{$g['usuarioAtual']}',
				'{$g['empresaAtual']}',
				CURRENT_TIMESTAMP()
			)
EOT;
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro: " . $erro;
        }
        $T214_Id = $g['mysqlLastId'];

        while ($mD009 = mysql_fetch_array($D009)) {
            $retorno = $this->est003InserirLancamentoBalanco($mD009, $T214_Id);
            if ($retorno !== true) return $retorno;

            $CAD002->D001_reprocessa_historico(false, $mD009['D009_Id'], 10);
			$sql = <<<EOT
				UPDATE
					T066
				SET
					T066_Quantidade_Balanco = '0.00',
					T066_Data_Balanco = CURDATE(),
					T066_Frequencia_Apos_Balanco = 0,
					T066_Flag_Balanco = 'N',
					T066_C007_Id_Marcou_Balanco=''
				WHERE T066_Id = '{$mD009['T066_Id']}'
EOT;
	        mysql_query($sql);
	        $erro = mysql_error();
	        if (!empty($erro)) {
	            return "Erro: " . $erro;
	        }
        }
        /*$sql = <<<EOT
			UPDATE
				D009
			SET
				D009_Quantidade_Balanco = '0.00',
				D009_Data_Balanco = CURDATE(),
				D009_Frequencia_Apos_Balanco = 0,
				D009_Flag_Balanco = 'N'
			WHERE
				D009_C004_Id = '{$g['empresaAtual']}'
EOT;
		*/

        mysql_query("COMMIT");
        return true;
    }

    /**
	 * mudarStatusColeta
	 *
	 * @param int $T224_Id
	 * @param int $Flag_Status
	 * @return true/string
	 */
    public function mudarStatusColeta($T224_Id, $Flag_Status)
    {
        global $g;
        $sqlT224 = mysql_query("SELECT * FROM T224 WHERE T224_Id = '{$T224_Id}'");
        $T224 = mysql_fetch_array($sqlT224);

        if($Flag_Status == 6 || $Flag_Status == 7){
        	if(!isset($T224['T224_D109_Id']) || $T224['T224_D109_Id'] <= 0 || !isset($T224['T224_Flag_Transporte_Coleta']) || $T224['T224_Flag_Transporte_Coleta'] <= 0){
        		return "O motorista e o tipo de transporte devem ser informados para prosseguir.";
        	}
        }
        $dataHoraColeta = '0000-00-00 00:00:00';
        if($Flag_Status == 7){
        	$dataHoraColeta = date('Y-m-d H:i:s');
        } else if($Flag_Status == 8){
        	$dataHoraColeta = $T224['T224_Data_Hora_Coleta'];
        }
        mysql_query("UPDATE T224 SET T224_Flag_Status = '{$Flag_Status}', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id), T224_Data_Hora_Coleta = '{$dataHoraColeta}' WHERE T224_Id = '{$T224_Id}'");
        $erro = mysql_error();
        if(!empty($erro)){
        	return "Erro: " . $erro;
        }
        return true;
    }

    public function gerarPedidoProdutosDefeitos($T005_Id)
    {
      	global $g;
        if (empty($T005_Id)) {
            return 'Não encontrado o Id do pedido (T005_ID)';
        }
        Log("T005_Id = ".$T005_Id);

        mysql_query("set autocommit=0");
        mysql_query("START TRANSACTION");

        $T006 = mysql_query("SELECT T206_Id, 
                                    T206_D009_Id, 
                                    D001_Codigo_Produto, 
                                    D001_Descricao_Produto, 
                                    D082_Marca, 
                                    D009_Quantidade_OE(T206_D009_Id, T206_Id, 0, T206_D169_Id) as saldo,
                                    D001_D037_Id 
        					   FROM T206 
        			      LEFT JOIN T006 ON T206_Id=T006_T206_Id 
        			      LEFT JOIN T005 ON T005_Id=T006_T005_Id 
        			      LEFT JOIN D169 ON D169_Id=T206_D169_Id 
        			      LEFT JOIN D009 ON D009_Id=T206_D009_Id 
        			      LEFT JOIN D049 ON D049_Id=D009_D049_Id 
        			      LEFT JOIN D082 ON D082_Id=D049_D082_Id 
        			      LEFT JOIN D001 ON D049_D001_Id=D001_Id 
        			          WHERE T206_Flag_Marcado='S'
        			       GROUP BY T206_Id");

        while($mT006 = mysql_fetch_array($T006)){
        	$sql  = "INSERT INTO T006 (   T006_T005_Id,
        								  T006_T206_Id,
        								  T006_D009_Id, 
                                          T006_Codigo_Produto, 
                                          T006_Marca,
                                          T006_Descricao_Produto, 
                                          T006_Quantidade, 
                                          T006_Valor_Preco_Sem_Desconto_Unitario, 
                                          T006_Valor_Preco_Original,
        								  T006_Valor_Preco_Original_Bruto,
        								  T006_Valor_Preco_Bruto,
                                          T006_D037_Id
                                          )
                                      VALUES
                                         ('{$T005_Id}',
                                          '{$mT006['T206_Id']}',
                                          '{$mT006['T206_D009_Id']}',
                                          '{$mT006['D001_Codigo_Produto']}',
                                          '{$mT006['D082_Marca']}',
                                          '{$mT006['D001_Descricao_Produto']}',
                                          '{$mT006['saldo']}',
                                          '0.01',
                                          '0.01',
                                          '0.01',
                                          '0.01',
                                          '{$mT006['D001_D037_Id']}'
                                          )";
                $ret  = mysql_query($sql, false);
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . $erro;
                }

            mysqli_query("UPDATE T206 SET T206_Flag_Finalizado = 'F' WHERE T206_Id = '{$mT006['T206_Id']}'");
        }        

        // VERIFICANDO SE GEROU CORRETAMENTE O T005 e T006
        $T005_T006=mysql_query("SELECT T005_Id,T006_Id from T005 LEFT JOIN T006 ON T006_T005_Id=T005_Id WHERE T005_Id='{$T005_Id}'");
        $mT005_T006=mysql_fetch_array($T005_T006);
        if($mT005_T006['T005_Id'] <=0 OR $mT005_T006['T006_Id'] <=0 ){
            mysql_query("ROLLBACK");
            return "Houve um problema na gravação dos dados. Por favor, refaça a operação";
        }

        mysql_query("COMMIT");
        return true;
	}

    /**
     * importarCSVBalanco
     *
     * @param string $arquivo
     * @return true/string
    */
     
    public function importarCSVBalanco($arquivo)
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
        $array_cols = array('Codigo_Produto','Locacao','Quantidade_Balanco','Alterar_Locacao');
        //posicao do array      <0>             <1>           <2>              <3>

        $linha = 1;        
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {
   
            $array_linha = explode(';',utf8_encode($array_linha));
            for ($count=0;$count<count($array_linha);$count++){
                if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                    $array_linha[$count] = utf8_encode($array_linha[$count]); 
                    $array_linha[$count] = trim($array_linha[$count]);                     
                }
            }

            $array_linha[0] = trim($array_linha[0]);
            $array_linha[1] = trim($array_linha[1]);
            $array_linha[2] = trim($array_linha[2]);
            $array_linha[3] = trim($array_linha[3]);

            $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
           
            if ($linha==1){
                // Validar número de colunas e o se o nome das colunas bate com o padrão  
                $total_cols = count($array_cols);
                if ($total_cols!=count($array_cols)) {
                    $return .= "O número de colunas é diferente de {$total_cols} \\n";
                }
                $array_linha[0] = str_replace(array(chr(239),chr(197),chr(201)),'',$array_linha[0]);
                for ($count=0;$count<count($array_linha);$count++){
                    if ($count!=0){
                        if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .="O nome da coluna ".trim($array_linha[$count])." no arquivo não confere com a coluna ".$array_cols[$count]." esperada.\\n \\n";
                        }
                    }
                }                   
            } else {

                $array_linha[3] = strtoupper($array_linha[3]);

                //Verifica se código do produto está cadastrado
                if(empty(trim($array_linha[0]))){
                    $return .=  $msgLinha . "O código do produto está em branco. \\n";
                }else{
                    $cClausula = "SELECT D009_Id FROM D001 
					           LEFT JOIN D049 ON D049_D001_Id=D001_Id 
							   LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id='{$g['empresaAtual']}' 
							       WHERE D001_Codigo_Produto='".trim($array_linha[0])."'";
                    $cSQL_D001 = mysql_query($cClausula);
                    $mD001     = mysql_fetch_array($cSQL_D001);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D001) <= 0){
                        $return .= $msgLinha . "O código do produto ". $array_linha[0] ." não está cadastrado!\\n";

                    }else{
                        $D009_Id=$mD001['D009_Id'];
                    }
				}
				
                //Verifica se a locação está cadastrada
                if(empty(trim($array_linha[1]))){
                    $return .=  $msgLinha . "A locação do produto está em branco. \\n";
                }else{
                    if ($array_linha[3]=='SIM'){
                        $cClausula = "SELECT D004_Id 
                                        FROM D004
                                       WHERE D004_Local='{$array_linha[1]}'
                                         AND D004_C004_Id='{$g['empresaAtual']}'";
                        $cSQL_D004 = mysql_query($cClausula);
                        $mD004     = mysql_fetch_array($cSQL_D004);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }
                        if(mysql_num_rows($cSQL_D004) <= 0){
                            $return .= $msgLinha . "A locação ". $array_linha[1] ." não existe \\n";
                        }
                        // CADASTRO DA LOCAÇÃO PRODUTO
                        $cClausula = "SELECT T066_Id 
                                        FROM T066
                                       WHERE T066_D009_Id='$D009_Id'";
                        $cSQL_T066 = mysql_query($cClausula);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }
                        if(mysql_num_rows($cSQL_T066) > 1){
                            $return .= $msgLinha . " " . $array_linha[0] . " possui mais de uma local, não é possível alterar a locação por balanço\\n";
                        }
                    } else {
                        $cClausula = "SELECT T066_Id 
                                        FROM T066
                                   LEFT JOIN D004 ON D004_Id=T066_D004_Id
                                       WHERE T066_D009_Id='$D009_Id'
                                         AND D004_Local='".trim($array_linha[1])."'";
                        $cSQL_T066 = mysql_query($cClausula);
                        $mT066     = mysql_fetch_array($cSQL_T066);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }
                        if(mysql_num_rows($cSQL_T066) <= 0){
                            $return .= $msgLinha . "A locação ". $array_linha[1] ." não está cadastrada para o produto " . $array_linha[0] . "\\n";
                        }
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
             
                $array_linha[0] = trim($array_linha[0]);
                $array_linha[1] = trim($array_linha[1]);
                $array_linha[2] = trim($array_linha[2]);
                $array_linha[3] = trim($array_linha[3]);

                //REALIZA OS SELECT´S NO BANCO PARA ALIMENTAR O UPDATE
                //CADASTRO DE PRODUTO
				$cClausula = "SELECT D009_Id 
							    FROM D001 
						   LEFT JOIN D049 ON D049_D001_Id=D001_Id 
						   LEFT JOIN D009 ON D009_D049_Id=D049_Id  
							   WHERE D001_Codigo_Produto='{$array_linha[0]}'
                                 AND D009_C004_Id='{$g['empresaAtual']}'";
				$cSQL_D001 = mysql_query($cClausula);
				$mD001     = mysql_fetch_array($cSQL_D001);
                $D009_Id=$mD001['D009_Id'];
                
                if ($array_linha[3]=='SIM'){
                    // CADASTRO DA LOCAÇÃO
                    $cClausula = "SELECT D004_Id 
                                    FROM D004
                                   WHERE D004_Local='{$array_linha[1]}'
                                     AND D004_C004_Id='{$g['empresaAtual']}'";
                    $cSQL_D004 = mysql_query($cClausula);
                    $mD004     = mysql_fetch_array($cSQL_D004);
                    $updateD004 = ", T066_D004_Id = '{$mD004['D004_Id']}' ";
                    // CADASTRO DA LOCAÇÃO PRODUTO
                    $cClausula = "SELECT T066_Id 
                                    FROM T066
                                   WHERE T066_D009_Id='$D009_Id'";
                } else {
                    // CADASTRO DA LOCAÇÃO PRODUTO
                    $cClausula = "SELECT T066_Id 
                                    FROM T066
                               LEFT JOIN D004 ON D004_Id=T066_D004_Id
                                   WHERE T066_D009_Id='$D009_Id'
                                     AND D004_Local='{$array_linha[1]}'";
                }
				$cSQL_T066 = mysql_query($cClausula);
				$mT066     = mysql_fetch_array($cSQL_T066);
				$T066_Id=$mT066['T066_Id'];
 
				//TROCA VIRGULA POR PONTO DECIMAL
				$array_linha[2]=gCorrigeNumeroInverte($array_linha[2]);

				// Altera quantidade no balanço
				$cClausula = <<<EOT
					UPDATE T066 SET
                        T066_Quantidade_Balanco='{$array_linha[2]}',
                        T066_Flag_Balanco = 'S',
                        T066_C007_Id_Marcou_Balanco = '{$g['usuarioAtual']}' 
                        $updateD004
					    WHERE T066_Id='$T066_Id'
EOT;
				mysql_query($cClausula);
				$cErro = mysql_error();
				if (!empty($cErro)) {
					mysql_query("ROLLBACK");
					return "Erro update: " . $cErro . "\\n" . $cClausula;
				} else {
					$linha++;
				}
            }

			mysql_query("COMMIT");
			mysql_query("SET AUTOCOMMIT=1");

			return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foi importado a quantidade de ".($linha-2)." produtos para a planilha!!\\n";

        } else {
            return "Importação cancelada, verifique os erros abaixo:\\n******************************************************** \\n\\n".$return;
        }        
    }

        /**
     * importarCSVBalanco
     *
     * @param string $sql
     * @return true/string
    */
     
    public function reabastecimentoLoja($sql = false)
    {
        global $g;

        mysql_query("START TRANSACTION");
		        
        if($sql){
		    $res = mysql_query($sql);
        } else {

            $res = mysql_query("SELECT D009_Id, 
                                       D001_Codigo_Produto, 
                                       D001_Descricao_Produto, 
                                       D009_Qtde_Reabastecimento_Loja, 
                                       T066_Quantidade_Estoque,
                                       D009_Quantidade_Estoque_Liquido,
                                       D009_Quantidade_OC,
                                       T066_Minimo,
                                       T066_Maximo, 
                                       T066_Id, 
                                       D001_Id 
                                  FROM D009 
                             LEFT JOIN D049 ON D049_Id=D009_D049_Id
                             LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                             LEFT JOIN T066 ON D009_Id=T066_D009_Id 
                             LEFT JOIN D004 ON D004_Id=T066_D004_Id
                             LEFT JOIN T001 ON D009_Id=T001_D009_Id 
                                 WHERE D009_Qtde_Reabastecimento_Loja > 0
                                   AND D009_C004_Id = '{$g['empresaAtual']}'
                                   AND D004_Local = 'LOJA'
                                   AND T066_Maximo > 0 
                              GROUP BY D009_Id
                              ORDER BY D001_Codigo_Produto");
        }

        $error = mysql_error();
        if(!empty($error)){
            mysql_query("ROLLBACK");
            return 'Erro <br>'.$error;
        }

        if(mysql_num_rows($res) <= 0){
            mysql_query("ROLLBACK");
            return "Não tem itens para incluir na DE da empresa {$g['empresaAtual']}";
        }

        // Cria D.E.
		$T029 = <<<EOT
					INSERT INTO T029 (`T029_C004_Id`, 
									  `T029_C007_Id`, 
									  `T029_Numero`, 
									  `T029_Data`,
									  `T029_Descricao`, 
									  `T029_Flag_Ok`, 
									  `T029_Valor_Debitos`, 
									  `T029_Valor_Creditos`, 
									  `T029_Flag_Coletor`)
								VALUES 
									 ('{$g['empresaAtual']}', 
									  '{$g['usuarioAtual']}', 
									  '0',
									   CURDATE(),
									  'REABASTECIMENTO LOJA', 
									  'N', 
									  '0.00', 
									  '0.00', 
									  'N');
EOT;
		mysql_query($T029);
        $error = mysql_error();
        if(!empty($error)){
            mysql_query("ROLLBACK");
            return 'Erro <br>'.$error;
        }

		$T029_Id = $g['mysqlLastId'];

        // Cria Itens da D.E.
		$total     = 0;
        $totalErro = 0;
        $totalOk   = 0;

        $semLocacaoLoja    = array();
        $semLocacaoEstoque = array();
		while($row = mysql_fetch_assoc($res)) {
			
            if ($row['D009_Qtde_Reabastecimento_Loja'] == 0) { 
                continue; 
            }

            $erroLocacao = false;
			$total++;

			// Baixa locação LOJA
			$D004 = <<<EOT
						SELECT T066_Id,
						       D004_Id, 
							   D004_Local
						  FROM T066 
			      	 LEFT JOIN D009 ON T066_D009_Id=D009_Id 
				  	 LEFT JOIN D048 ON D048_Id=T066_D048_Id 
					 LEFT JOIN D004 ON D004_Id=T066_D004_Id	 
						 WHERE D009_Id='{$row['D009_Id']}'
						   AND D009_C004_Id='{$g['empresaAtual']}'
						   AND D004_Local = 'LOJA'
EOT;
			$resD004 = mysql_query($D004);

			if (mysql_num_rows($resD004) == 0) {
                //mysql_query("ROLLBACK");
				//return "Locação 'LOJA' não encontrada para o produto '{$row['D001_Codigo_Produto']}'.";
                $erroLocacao = true;
                $semLocacaoLoja[] = $row['D001_Codigo_Produto'];
			} else {
                $rowD004Loja = mysql_fetch_assoc($resD004);
            }
   

			// Baixa locação ESTOQUE
			$T066 = <<<EOT
						SELECT T066_Id,
							   D004_Id, 
							   D004_Local
						  FROM T066 
					 LEFT JOIN D009 ON T066_D009_Id=D009_Id 
					 LEFT JOIN D048 ON D048_Id=T066_D048_Id 
					 LEFT JOIN D004 ON D004_Id=T066_D004_Id 
				         WHERE D009_Id='{$row['D009_Id']}'
						   AND D009_C004_Id='{$g['empresaAtual']}'
						   AND D004_Local != 'LOJA'
						   AND T066_Quantidade_Estoque>=D009_Qtde_Reabastecimento_Loja
						   AND IFNULL(D004_Flag_Somar_Estoque_Fisico,'S') != 'N'
					  GROUP BY T066_Id
EOT;
			$resD004 = mysql_query($T066);
			if (mysql_num_rows($resD004) == 0) {
                //mysql_query("ROLLBACK");
				//return "Locação 'ESTOQUE' não encontrada para o produto '{$row['D001_Codigo_Produto']}'.";
                $erroLocacao = true;
                $semLocacaoEstoque[] = $row['D001_Codigo_Produto'];
			} else {
                $rowD004Estoque = mysql_fetch_assoc($resD004);
            }

            if($erroLocacao === false){
                $totalOk++;

                // Credito locação LOJA
                $insertT030 = <<<EOT
                            INSERT INTO T030 (`T030_D009_Id`, 
                                              `T030_T029_Id`,
                                              `T030_C004_Id`,
                                              `T030_C007_Id`,
                                              `T030_Quantidade`,
                                              `T030_Flag_Entrada_Saida`,
                                              `T030_Valor_Custo_Unitario`,
                                              `T030_Flag_Alterar_Custo`,
                                              `T030_Numero`,
                                              `T030_Codigo_Produto`,
                                              `T030_Codigo_Fornecedor`,
                                              `T030_Valor_Custo_Unitario_Origem`,
                                              `T030_Flag_Cancelada`,
                                              `T030_Observacao`,
                                              `T030_Data_Inclusao`,
                                              `T030_Locacao`,
                                              `T030_D004_Id`,
                                              `T030_Quantidade_Estoque_Anterior_Produto`,
                                              `T030_Quantidade_Estoque_Anterior_Local`,
                                              `T030_D144_Id`,
                                              `T030_T066_Id`) 
                                        VALUES
                                             ('{$row['D009_Id']}', 
                                              '{$T029_Id}',
                                              '{$g['empresaAtual']}',
                                              '{$g['usuarioAtual']}',
                                              '{$row['D009_Qtde_Reabastecimento_Loja']}',
                                              'E',
                                              '0.00000',
                                              'N',
                                              '0',
                                              '{$row['D001_Codigo_Produto']}',
                                              NULL,
                                              '0.00',
                                              'N',
                                              'REABASTECIMENTO LOJA',
                                              NOW(),
                                              NULL,
                                              '{$rowD004Loja['D004_Id']}',
                                              '0.00',
                                              '0.00',
                                              NULL,
                                              '{$rowD004Loja['T066_Id']}');
EOT;
                mysql_query($insertT030);
    
                $error = mysql_error();
                if(!empty($error)){
                    mysql_query("ROLLBACK");
                    return 'Erro <br>'.$error;
                }

                // Baixa locação ESTOQUE
                $insertT030 = <<<EOT
                            INSERT INTO T030 (`T030_D009_Id`, 
                                              `T030_T029_Id`,
                                              `T030_C004_Id`,
                                              `T030_C007_Id`,
                                              `T030_Quantidade`,
                                              `T030_Flag_Entrada_Saida`,
                                              `T030_Valor_Custo_Unitario`,
                                              `T030_Flag_Alterar_Custo`,
                                              `T030_Numero`,
                                              `T030_Codigo_Produto`,
                                              `T030_Codigo_Fornecedor`,
                                              `T030_Valor_Custo_Unitario_Origem`,
                                              `T030_Flag_Cancelada`,
                                              `T030_Observacao`,
                                              `T030_Data_Inclusao`,
                                              `T030_Locacao`,
                                              `T030_D004_Id`,
                                              `T030_Quantidade_Estoque_Anterior_Produto`,
                                              `T030_Quantidade_Estoque_Anterior_Local`,
                                              `T030_D144_Id`,
                                              `T030_T066_Id`) 
                                      VALUES
                                             ('{$row['D009_Id']}', 
                                              '{$T029_Id}',
                                              '{$g['empresaAtual']}',
                                              '{$g['usuarioAtual']}',
                                              '{$row['D009_Qtde_Reabastecimento_Loja']}',
                                              'S',
                                              '0.00000',
                                              'N',
                                              '0',
                                              '{$row['D001_Codigo_Produto']}',
                                              NULL,
                                              '0.00',
                                              'N',
                                              'REABASTECIMENTO LOJA',
                                              NOW(),
                                              NULL,
                                              '{$rowD004Estoque['D004_Id']}',
                                              '0.00',
                                              '0.00',
                                              NULL,
                                              '{$rowD004Estoque['T066_Id']}');
    EOT;
                mysql_query($insertT030);
                $error = mysql_error();
                if(!empty($error)){
                    mysql_query("ROLLBACK");
                    return 'Erro <br>'.$error;
                }       

                mysqli_query("UPDATE D009 SET D009_Qtde_Reabastecimento_Loja = 0 where D009_Id='{$row['D009_Id']}'");
                $error = mysql_error();
                if(!empty($error)){
                    mysql_query("ROLLBACK");
                    return 'Erro <br>'.$error;
                }
            } else {
                $totalErro++;
            }
		}

        if(!empty($semLocacaoEstoque)){
            $msgSemLocacaoEstoque = "Item sem locação 'ESTOQUE', verifique se existe uma locação diferente de loja, que some o estoque e tenha estoque disponível para o reabastecimento dos produtos:<br> - ".implode(", ",$semLocacaoEstoque);
        }

        if(!empty($semLocacaoLoja)){
            $msgSemLocacaoLoja = "Locação 'LOJA' não encontrada para os produtos:<br> - ".implode(", ",$semLocacaoLoja);
        }

        // Aqui se todos os itens deram erro por conta da locação
        if($total == $totalErro){
            if(!empty($msgSemLocacaoEstoque) && !empty($msgSemLocacaoLoja)){
                $retornoErro = $msgSemLocacaoLoja . "<br>" . $msgSemLocacaoEstoque;
            } elseif(!empty($msgSemLocacaoEstoque)) {
                $retornoErro = $msgSemLocacaoEstoque;
            } else {
                $retornoErro = $msgSemLocacaoLoja;
            }
            mysql_query("ROLLBACK");
            return $retornoErro;

        }

        mysql_query("COMMIT");


        return array('T029_Id' => $T029_Id, 'total' => $totalOk, 'totalErro' => $totalErro ,'erroLocacaoEstoque' => $msgSemLocacaoEstoque, 'erroLocacaoLoja' => $msgSemLocacaoLoja);
                
    }

}
