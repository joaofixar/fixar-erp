<?php
namespace hardness;

	require_once("bibliotecas/classes/VEN001.php");
	
	
	if(!empty($g['c004']['C004_Logo_Marca'])){
		$pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
		$caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
	}else{
		$caminhoImagem="http://tudo.hardness.com.br//hardness3/dados_usuarios/e215/imagens/logo/1.png";
	}
	$logo ='<img src='.$caminhoImagem.' style="margin-left:25px;margin-top:0px;">';
	
	
	/**
	* Valida um CPF
	* @param string $cpf
	* @return true/string
	*/
	
	function ValidarCpf($cpf) {
		$erro = "O CPF é inválido.";
		$cpf=str_replace(".","",$cpf);
		$cpf=str_replace("-","",$cpf);
		$cpf=trim($cpf);
		$nulos = array("12345678909","11111111111","22222222222","33333333333",
		               "44444444444","55555555555","66666666666","77777777777",
		               "88888888888","99999999999","00000000000");
		/* Retira todos os caracteres que nao sejam 0-9 */
		$aux="";
		for ($i=1; $i<=strlen($cpf); $i++){
		  $ch=substr($cpf,$i-1,1);
		  if (ord($ch)>=48 && ord($ch)<=57){
		    $aux=$aux.$ch;
		  }
		}
		$cpf = $aux;
		/* Retorna falso se o cpf for nulo */
		foreach ( $nulos as $nulo ) {
		  if($cpf == $nulo){
		    return $erro;
		  }
		}
		/*Calcula o penúltimo dígito verificador*/
		$acum=0;
		for($i=0; $i<9; $i++) {
		  $acum+= @$cpf[$i]*(10-$i);
		}

		$x=$acum % 11;
		$acum = ($x>1) ? (11 - $x) : 0;
		/* Retorna falso se o digito calculado eh diferente do passado na string */
		if ($acum != @$cpf[9]){
		  return $erro;
		}
		/*Calcula o último dígito verificador*/
		$acum=0;
		for ($i=0; $i<10; $i++){
		  $acum+= @$cpf[$i]*(11-$i);
		}

		$x=$acum % 11;
		$acum = ($x > 1) ? (11-$x) : 0;
		/* Retorna falso se o digito calculado eh diferente do passado na string */
		if ( $acum != @$cpf[10]){
		  return $erro;
		}
		if (empty($cpf)) { return $erro; }
		/* Retorna verdadeiro se o cpf eh valido */
		return true;
	}

	/**
	* FUNÇÃO PARA INSERIR OS DADOS NO BANCO
	* 
	* @param array $form
	* @param array $D009_Id
	* @param int $count
	* @param array $T006_Quantidade
	* @return boolean
	*/
	function gravarFormulario($form, $D009_Id, $count, $T006_Quantidade){
		global $g;
		
		$selectD024 = mysql_query("SELECT * FROM D024 WHERE D024_CPF='{$form['D024_CPF']}'");
		
		if(mysql_num_rows($selectD024) > 0){
			
			$mD024 = mysql_fetch_array($selectD024);
			$sqlT005 = mysql_query("SELECT * FROM T005 LEFT JOIN T007 ON T007_T005_Id = T005_Id WHERE T005_D024_Id = '{$mD024['D024_Id']}' AND T005_Flag_Status != 8 AND T007_Id IS NULL");
			if(mysql_num_rows($sqlT005) > 0){
				return "Ja existe um pedido em andamento";
			}
		}
		
        $sqlPais = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais LIKE '%BRASIL%'");
        $rowPais = mysql_fetch_array($sqlPais);
        $idPais = $rowPais['D030_Id'];
        
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		if(mysql_num_rows($selectD024) == 0){
			$D024 = <<<EOT
			INSERT INTO D024 (
									D024_Nome_Empresa,
									D024_CPF,
									D024_DDD_Telefone_1,
									D024_Telefone_1,
									D024_Endereco,
									D024_Numero,
									D024_Complemento,
									D024_D018_Id,
									D024_D020_Id,
									D024_Bairro,
									D024_Flag_Pessoa_Fisica_Juridica,
									D024_Flag_Ativo,
									D024_Data_Cadastro,
									D024_Cep,
									D024_Flag_Cliente,
									D024_Flag_Fornecedor,
									D024_D030_Id,
									D024_Nome_Fantasia,
									D024_E_Mail,
									D024_Apelido,
                                    D024_Flag_Contribuinte,
                                    D024_Flag_Lista_Preco,
                                    D024_Flag_Restricao,
                                    D024_Flag_Liberado
                                    
			                  ) VALUES (
		                  			'{$form['D024_Nome_Empresa']}',
		                  			'{$form['D024_CPF']}',
		                  			'{$form['D024_DDD_Telefone_1']}',
		                  			'{$form['D024_Telefone_1']}',
		                  			'{$form['D024_Endereco']}',
		                  			'{$form['D024_Numero']}',
		                  			'{$form['D024_Complemento']}',
		                  			'{$form['D024_D018_Id']}',
		                  			'{$form['D024_D020_Id']}',
		                  			'{$form['D024_Bairro']}',
		                  			'F',
		                  			'S',
		                  			CURRENT_TIMESTAMP(),
		                  			'{$form['D024_Cep']}',
		                  			'S',
		                  			'N',
		                  			'{$idPais}',
		                  			'{$form['D024_Nome_Empresa']}',
		                  			'{$form['D024_E_Mail']}',
		                  			'{$form['T006_Observacao_Apelido']}',
                                    '1',
                                    '1',
                                    'N',
                                    'S'
			                  );
EOT;
			$resultD024 = mysql_query($D024);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return "Houve algum erro no cadastro. Por favor, contate o suporte técnico";
			}
			$D024_Id = $g['mysqlLastId'];
		}else{
			$upD024 = <<<EOT
			UPDATE D024  SET
				D024_Nome_Empresa='{$form['D024_Nome_Empresa']}',
				D024_DDD_Telefone_1='{$form['D024_DDD_Telefone_1']}',
				D024_Telefone_1='{$form['D024_Telefone_1']}',
				D024_Endereco='{$form['D024_Endereco']}',
				D024_Numero='{$form['D024_Numero']}',
				D024_Complemento='{$form['D024_Complemento']}',
				D024_D018_Id='{$form['D024_D018_Id']}',
				D024_D020_Id='{$form['D024_D020_Id']}',
				D024_Bairro='{$form['D024_Bairro']}',
				D024_Flag_Ativo='S',
				D024_Cep='{$form['D024_Cep']}',
				D024_Nome_Fantasia='{$form['D024_Nome_Empresa']}',
				D024_E_Mail='{$form['D024_E_Mail']}',
				D024_Apelido='{$form['T006_Observacao_Apelido']}'
				WHERE D024_Id='{$mD024['D024_Id']}'
EOT;
			mysql_query($upD024);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return "Houve algum erro no cadastro. Por favor, contate o suporte técnico";
			}
			$D024_Id = $mD024['D024_Id'];
		}
		
		$T005_Observacao = <<<EOT
Apelido: {$form['T006_Observacao_Apelido']}
		
EOT;
		$T005_Observacao_Comercial = <<<EOT
Modificações no pedido: {$form['T005_Observacao']}
		
Pagamento: {$form['formaPagamento']}
		
Forma envio: {$form['formaEnvio']}
		
EOT;
		$VEN001 = new VEN001();
		
		$D006_Id_Cabecalho = $VEN001->retornaCFOP($D024_Id);
        $D006Observacao = mysql_query("SELECT D006_Observacao FROM D006 WHERE D006_Id='{$D006_Id_Cabecalho}'");
        $D006Observacao = mysql_fetch_array($D006Observacao);
        $D006Observacao = $D006Observacao['D006_Observacao'];
		
		$T005 = <<<EOT
			INSERT INTO T005 (
				T005_D024_Id,
				T005_Data_Emissao,
				T005_C004_Id,
				T005_D006_Id,
				T005_Observacao,
				T005_C007_Id_Vendedor_Interno,
				T005_Observacao_Comercial,
                T005_Flag_ACP,
                T005_Flag_Lista_Preco,
                T005_Observacao_Nota_Fiscal
			) VALUES (
				'{$D024_Id}',
				CURDATE(),
				'{$g['empresaAtual']}',
				'{$D006_Id_Cabecalho}',
				'{$T005_Observacao}',
				'80',
				'{$T005_Observacao_Comercial}',
                '6',
                '1',
                '{$D006Observacao}'
			);
EOT;
		$resultT005 = mysql_query($T005);
		if(mysql_error()){
			mysql_query("ROLLBACK");
			return "Houve algum erro no cadastro. Por favor, contate o suporte técnico";
		}
		
		$T005_Id = $g['mysqlLastId'];
        
        //cria histórico do pedido
        mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$T005_Id}','80',CURRENT_TIMESTAMP(),'Pedido Mercado Livre','')");
        
		$contNovo=1;
		$countProd=-1;
		foreach($D009_Id as $D009_Id_Final){
			$countProd++;
			$D009 = <<<EOT
			SELECT * FROM D009 
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D041 on D041_D049_Id=D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			LEFT JOIN D136 ON D136_D001_Id=D001_Id
			WHERE D009_Id='{$D009_Id_Final}'
EOT;
			$D009 = mysql_query($D009);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return "Houve algum erro no cadastro. Por favor, contate o suporte técnico";
			}
			$mD009 = mysql_fetch_array($D009);
			
			if($mD009['D049_Flag_Tipo'] == 'MT'){
				$sqlKit = mysql_query("SELECT * FROM D042
							LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens
    						LEFT JOIN D082 ON D049_D082_Id=D082_Id
							LEFT JOIN D001 ON D001_Id=D049_D001_Id
    						LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens
    						LEFT JOIN D041 ON D041_D049_Id=D049_Id
							WHERE D042_D049_Id = '{$mD009['D049_Id']}'
							AND D009_C004_Id= '{$g['empresaAtual']}'
							AND D042_C004_Id= '{$g['empresaAtual']}'
    						AND D009_C004_Id= '{$g['empresaAtual']}'");
				while($rowKit = mysql_fetch_array($sqlKit)){
					$D006_Id_Item = $VEN001->retornaCFOP($D024_Id,$rowKit['D009_Id']);
					$quantidade = ($T006_Quantidade[$countProd]*$rowKit['D042_Quantidade']);
					//T006_Valor_Custo_Unitario
                    $T006 = <<<EOT
					INSERT INTO T006 (
                        T006_C004_Id,
						T006_T005_Id,
						T006_Descricao_Produto,
						T006_Codigo_Produto,
						T006_Quantidade,
						T006_D006_Id,
						T006_Valor_Preco_Original,
						T006_Valor_Preco_Sem_Desconto_Unitario,
                        T006_Valor_Preco_Unitario,
						T006_Item,
						T006_D009_Id,
                        T006_D037_Id
					) VALUES (
                        '{$g['empresaAtual']}',
						'{$T005_Id}',
						'{$rowKit['D001_Descricao_Produto']}',
						'{$rowKit['D001_Codigo_Produto']}',
						'{$quantidade}',
						'{$D006_Id_Item}',
						'{$rowKit['D009_Preco_1']}',
						'{$rowKit['D009_Preco_1']}',
                        '{$rowKit['D009_Preco_1']}',
						'{$contNovo}',
						'{$rowKit['D009_Id']}',
                        '{$rowKit['D001_D037_Id']}'
					);
EOT;
					$T006 = mysql_query($T006);
					if(mysql_error()){
						mysql_query("ROLLBACK");
						return "Houve algum erro no cadastro. Por favor, contate o suporte técnico";
					}
					$contNovo++;
				}
			} else {
				$D006_Id_Item = $VEN001->retornaCFOP($D024_Id,$D009_Id_Final);
				$T006 = <<<EOT
				INSERT INTO T006 (
                    T006_C004_Id,
					T006_T005_Id,
					T006_Descricao_Produto,
					T006_Codigo_Produto,
					T006_Quantidade,
					T006_D006_Id,
					T006_Valor_Preco_Original,
					T006_Valor_Preco_Sem_Desconto_Unitario,
                    T006_Valor_Preco_Unitario,
					T006_Item,
					T006_D009_Id,
                    T006_D037_Id
				) VALUES (
                    '{$g['empresaAtual']}',
					'{$T005_Id}',
					'{$mD009['D001_Descricao_Produto']}',
					'{$mD009['D001_Codigo_Produto']}',
					'{$T006_Quantidade[$countProd]}',
					'{$D006_Id_Item}',
					'{$mD009['D009_Preco_1']}',
					'{$mD009['D009_Preco_1']}',
                    '{$mD009['D009_Preco_1']}',
					'{$contNovo}',
					'{$D009_Id_Final}',
                    '{$mD009['D001_D037_Id']}'
				);
EOT;
				$T006 = mysql_query($T006);
				if(mysql_error()){
					mysql_query("ROLLBACK");
					return "Houve algum erro no cadastro. Por favor, contate o suporte técnico";
				}
				$contNovo++;
			}
		}
		mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        
        //Atualiza alíquotas, grava totalizações
        //Estas operações só devem ser feitas aqui no final.Pois só aqui temos todos os produtos do pedido para atualizar alíquotas e posteriormente gravar totalizações.
        
        require_once("bibliotecas/classes/VEN002.php");
        $VEN002 = new VEN002();
        $retorno = $VEN002->atualizarAliquotasPedidos($T005_Id);
        
        $sqlTotalizador = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
        while($rowTotalizador = mysql_fetch_array($sqlTotalizador)){
            mysql_query("call T006_Gravar_Totalizacao_4('{$rowTotalizador['T006_Id']}')");
        }
        mysql_query("call T005_Gravar_Totalizacao_4('$T005_Id')");
        
		return true;
		
	}

	switch ($g['r'][3]) {
		case 'teste':
			global $g;
			$msgIns = "";
			$disabled = "";
			$erros = array(
				'D024_Nome_Empresa' => '',
				'D024_CPF' =>'',
				'D024_DDD_Telefone_1'=>'',
				'D024_Telefone_1'=>'',
				'D024_Endereco'=>'',
				'D024_Numero'=>'',
				'D024_Complemento'=>'',
				'D024_D018_Id'=>'',
				'D024_D020_Id'=>'',
				'D024_Bairro'=>'',
				'D009_Id'=>'',
				'D024_Cep'=>'',
				'T005_Observacao'=>'',
				'T006_Quantidade'=>'',
				'T006_Observacao_Apelido',
				'D024_E_Mail'
			);
			
			$values = array(
				'D024_Nome_Empresa' => '',
				'D024_CPF' =>'',
				'D024_DDD_Telefone_1'=>'',
				'D024_Telefone_1'=>'',
				'D024_Endereco'=>'',
				'D024_Numero'=>'',
				'D024_Complemento'=>'',
				'D024_D018_Id'=>'',
				'D024_D020_Id'=>'',
				'D024_Bairro'=>'',
				'D009_Id'=>'',
				'D024_Cep'=>'',
				'T005_Observacao'=>'',
				'T006_Quantidade'=>'',
				'T006_Observacao_Apelido',
				'D024_E_Mail'
			);
			$html="";
			$error=0;
			$msg="";
			$qnt="";
			$checked="";
			/**
			* FORMULÁRIO ENVIADO
			*/
			
			if(isset($_POST['D024_Nome_Empresa'])){
				$D024_Nome_Empresa 				= strtoupper($_POST['D024_Nome_Empresa']);
				$D024_CPF 						= $_POST['D024_CPF'];
				$D024_DDD_Telefone_1 			= $_POST['D024_DDD_Telefone_1'];
				$D024_Telefone_1 				= $_POST['D024_Telefone_1'];
				$D024_DDD_Telefone_2 			= $_POST['D024_DDD_Telefone_2'];
				$D024_Telefone_2 				= $_POST['D024_Telefone_2'];
				$D024_Endereco 					= strtoupper($_POST['D024_Endereco']);
				$D024_Numero 					= strtoupper($_POST['D024_Numero']);
				$D024_Complemento 				= strtoupper($_POST['D024_Complemento']);
				$D024_D018_Id 					= $_POST['D024_D018_Id'];
				$D024_D020_Id 					= $_POST['D024_D020_Id'];
				$D024_Bairro 					= strtoupper($_POST['D024_Bairro']);
				$D009_Id 						= array();
				$D024_Cep 						= $_POST['D024_Cep'];
				$T005_Observacao				= strtoupper($_POST['T005_Observacao']);
				$count 							= $_POST['count'];
				$T006_Quantidade 				= $_POST['T006_Quantidade'];
				$T006_Observacao_Apelido 		= $_POST['T006_Observacao_Apelido'];
				$D024_E_Mail					= $_POST['D024_E_Mail'];
				$formaPagamento					= $_POST['formaPagamento'];
				$formaEnvio						= $_POST['formaEnvio'];
							  
				$values = array(
					'D024_Nome_Empresa' => $D024_Nome_Empresa,
					'D024_CPF' =>$D024_CPF,
					'D024_DDD_Telefone_1'=>$D024_DDD_Telefone_1,
					'D024_Telefone_1'=>$D024_Telefone_1,
					'D024_Endereco'=>$D024_Endereco,
					'D024_Numero'=>$D024_Numero,
					'D024_Complemento'=>$D024_Complemento,
					'D024_D018_Id'=>$D024_D018_Id,
					'D024_D020_Id'=>$D024_D020_Id,
					'D024_Bairro'=>$D024_Bairro,
					'D024_Cep'=>$D024_Cep,
					'T005_Observacao'=>$T005_Observacao,
					'T006_Quantidade'=>$T006_Quantidade,
					'T006_Observacao_Apelido'=>$T006_Observacao_Apelido,
					'D024_E_Mail'=>$D024_E_Mail,
					'formaPagamento'=>$formaPagamento,
					'formaEnvio'=>$formaEnvio
				);

				/**
				* VALIDAÇÕES
				*/
				for($i=1;$i<=$count;$i++){
					if(isset($_POST['D009_Id-'.$i])){
						$D009_Id[] = $_POST['D009_Id-'.$i];
						if(empty($_POST['T006_Quantidade-'.$i]) || $_POST['T006_Quantidade-'.$i] == 0){
							echo $_POST['T006_Quantidade-'.$i];
							$msg = "<span style='color:red;'>Insira uma quantidade *</span>";
							$error++;
						} else {
							$T006_Quantidade_Final[] = $_POST['T006_Quantidade-'.$i];
						}
					}
				}
				
				if(empty($D024_Nome_Empresa)){
					$erros['D024_Nome_Empresa'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($T006_Observacao_Apelido)){
					$erros['T006_Observacao_Apelido'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_E_Mail)){
					$erros['D024_E_Mail'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				
				if(empty($D024_CPF)){
					$erros['D024_CPF'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}else{
					$retorno = ValidarCpf($D024_CPF);
					if($retorno !== true){
						$erros['D024_CPF'] = 'border-color:red;background-color:#FFC1C1;';
						$error++;
					}
				}
				if(empty($D009_Id) && empty($T005_Observacao)){
					$msg .= "<span style='color:red;'>Selecione um produto ou preencha o campo de observações*</span>";
                    $erros['T005_Observacao'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($formaPagamento)){
					$msgPagamento .= "<span style='color:red;'>Selecione uma opção abaixo *</span>";
					$error++;
				}
				if(empty($formaEnvio)){
					$msgEnvio .= "<span style='color:red;'>Selecione uma opção abaixo *</span>";
					$error++;
				}
				if(empty($D024_DDD_Telefone_1)){
					$erros['D024_DDD_Telefone_1'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_Telefone_1)){
					$erros['D024_Telefone_1'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_Endereco)){
					$erros['D024_Endereco'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_E_Mail)){
					$erros['D024_E_Mail'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_Bairro)){
					$erros['D024_Bairro'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_D018_Id)){
					$erros['D024_D018_Id'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_D020_Id)){
					$erros['D024_D020_Id'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_Cep)){
					$erros['D024_Cep'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				if(empty($D024_Numero)){
					$erros['D024_Numero'] = 'border-color:red;background-color:#FFC1C1;';
					$error++;
				}
				
				if($error == 0){
					$retorno = gravarFormulario($values, $D009_Id, $count, $T006_Quantidade_Final);
					if($retorno === true){						
						echo <<<EOT
						<script type="text/javascript">							
							window.location = '/hardness3/prog/outros/pedidoMercadoLivre/pedidoConcluidoMercadoLivre.php';
						</script>
EOT;
						$disabled = "disabled='disabled'";
					} else {
						echo <<<EOT
						<script type="text/javascript">
							alert('{$retorno}');
							document.location.reload();
						</script>
EOT;
					}
				}
			}
				if(isset($_GET['apelido'])){
					$D024 = "SELECT * FROM D024 WHERE D024_Apelido = '{$_GET['apelido']}'";
					$sqlD024 = mysql_query($D024);
					if(mysql_num_rows($sqlD024)){
						$resD024 = mysql_fetch_array($sqlD024);
						$values = array(
							'D024_Nome_Empresa' => $resD024['D024_Nome_Empresa'],
							'D024_CPF' => $resD024['D024_Cpf'],
							'D024_DDD_Telefone_1'=> $resD024['D024_DDD_Telefone_1'],
							'D024_Telefone_1'=> $resD024['D024_Telefone_1'],
							'D024_DDD_Telefone_2'=> $resD024['D024_DDD_Telefone_2'],
							'D024_Telefone_2'=> $resD024['D024_Telefone_2'],
							'D024_Endereco'=> $resD024['D024_Endereco'],
							'D024_Numero'=> $resD024['D024_Numero'],
							'D024_Complemento'=> $resD024['D024_Complemento'],
							'D024_D018_Id'=> $resD024['D024_D018_Id'],
							'D024_D020_Id'=> $resD024['D024_D020_Id'],
							'D024_Bairro'=> $resD024['D024_Bairro'],
							'D024_Cep'=> $resD024['D024_Cep'],
							'T005_Observacao'=> $resD024['T005_Observacao'],
							'T006_Quantidade'=> $resD024['T006_Quantidade'],
							'T006_Observacao_Apelido'=> $resD024['D024_Apelido'],
							'D024_E_Mail'=> $resD024['D024_E_Mail'],
						);
					}else{
                        $values = array(
                            'T006_Observacao_Apelido'=> $_GET['apelido'],
                        );
                    }
				}
				$D018_Id = isset($resD024['D024_D018_Id']) ? $resD024['D024_D018_Id'] : '';

				$D018 = mysql_query("SELECT * FROM D018 ORDER BY D018_Uf");
				$D020 = mysql_query("SELECT * FROM D020 ORDER BY D020_Nome_Cidade where D020_Id='9999999999'");
				$D009 = <<<EOT
				SELECT * FROM D009 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				LEFT JOIN D136 ON D136_D001_Id=D001_Id
				LEFT JOIN T172 ON T172_D001_Id=D001_Id 
				WHERE D136_Flag_Mercado_Livre='S'
				AND D009_C004_Id='{$g['empresaAtual']}'
                ORDER BY IFNULL(D049_Flag_Tipo,'CP') DESC,D001_Descricao_Produto 
EOT;
				$D009 = mysql_query($D009);
				
				$html .= <<<EOT
				<html>
					<head>
						<title>Confirmação pedido</title>
						<script src="/hardness3/static/js/jquery-1.6.2.min.js"></script>
						<script src="/hardness3/static/js/jquery-ui-1.8.16.custom.min.js"></script>
						<script src="/hardness3/static/js/mask.js"></script>
						<style type="text/css">
							*{
								margin:0;
								padding:0;
							}
							body{
								font-family: Arial, Helvetica,sans-serif; 
								background: #F4F4F4;
								size: 5px;
							}
							input {
								text-transform: uppercase;
							}
							textarea {
								text-transform: uppercase;
							}
							.generalContent{
								width:100%;
							}
							.generalContent .centerContent{
								width:850px;
								margin:0 auto;
								border:5px solid #CFCFCF;
							}
							.generalContent .centerContent .header{
								width:100%;
							}
							.generalContent .centerContent .header table{
								width:98%;
								margin:0px auto;
								height:50px;
							}
							.generalContent .centerContent .form{
								width:100%;
							}
							.generalContent .centerContent .form table{
								width:100%;
							}
							.generalContent .centerContent .form table th{
								text-align:left;
								height:50px;
								font-size:20px;
								border-top:1px solid silver;
								border-bottom:1px solid silver;
							}
							.generalContent .centerContent .form table tr td{
								padding:5px;
							}
							.contentProduto{
								width:100%;
								float:left;
							}
							
						</style>
						<script type="text/javascript">
						  function Formatar_Cpf(campo,o,e,nome_form)  {

							var vr = o.value;
							var tam = vr.length;
							var strCheck = '0123456789';
							var cTecla = '';
							var nCodTecla = (window.Event) ? e.which : e.keyCode;
							var cValor= vr;

							// Se for um enter, pula para o proximo campo
							//Skip_Campo(campo,e,nome_form);

							if ((nCodTecla==8) || (nCodTecla==0) || (nCodTecla == 13)) {
							   return true;
							}

							// Verifica se é um Valor ou Numero
							cTecla = String.fromCharCode(nCodTecla);

							// Não é um Número ou Valor Válido
							if (strCheck.indexOf(cTecla) == -1) return false;

							    if (tam == 3)
							        cValor = vr.substr( 0, 3 ) + '.';

							    if (tam == 7)
							        cValor =  vr.substr( 0, 7 ) + '.';

							    if  (tam == 11)
							        cValor = vr.substr( 0, 11) + '-';

      							o.value=cValor;
						  }
							function formatar(src, mask, e){
								var nCodTecla = (window.Event) ? e.which : e.keyCode;
								//alert(nCodTecla);
							  	if((nCodTecla < 48 || nCodTecla > 57) && nCodTecla != 0 && nCodTecla != 8 && nCodTecla != 13){
							  		return false;
							  	}

								var i = src.value.length;
							  	var saida = mask.substring(0,1);
							  	var texto = mask.substring(i)

								if (texto.substring(0,1) != saida)
								{
								    src.value += texto.substring(0,1);
								}
								return true;
							}
							function alteraEstado(elem) {
							    $.getJSON('/cad/cad001/form_func-ajax/alteraEstado/?ajax=true&D024_D018_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
									if (request.code) {
										$('#D024_D020_Id').html(request.data['D024_D020_Id']);
										//reloadUI('#' . D024_D020_Id);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    									dialogConfirm("Erro!", request.data, \$buttons)
									}
								});
							}

						</script>
					</head>
					<body style="background: url('http://tudo.hardness.com.br/hardness3/static/css/temas/padrao/images/desktop.png');">
						<div class="generalContent">
							<div class="centerContent" style="background: #F4F4F4;">
								<div class="header">
									<table cellpadding=0 cellspacing=0>
										<tr>
											<td>{$logo}</td>
											<td align="center" style="font-size:28px;"><b>Agilize seu pedido!</b></td>
										</tr>
									</table>
								</div>
								<div class="form">	
									<form action="./?ajax=true&p=1" method="POST" name="formCadastro">
										<table>
											<tr>
												<th colspan="4">Informe seus dados</th>
											</tr>
EOT;
							if($error > 0){
									$html .=<<<EOT
											<tr>
												<td colspan="4"><span style="color:red;">Campos obrigatórios *</span></td>
											</tr>
EOT;
							}
							if($msgIns != ""){
								$Html .=<<<EOT
											<tr>
												<td colspan="4">{$msgIns}</td>
											</tr>
EOT;

							} 
							$html .=<<<EOT
                                            <tr>
                                                <td colspan="4">
                                                    <span><b><font color="red">Atenção:</font> Preencha corretamente todos os campos do formulário, caso algum dado esteja incorreto o pedido poderá sofrer um atraso desnecessário</b></span><br />
                                                </td>
                                            </tr>
											<tr>
												<td align="right" style="width: 10%;">Nome *</td>
												<td style="width: 20%;"><input style="width: 280px;{$erros['D024_Nome_Empresa']}" type="text" {$disabled} placeholder="Digite seu nome" name="D024_Nome_Empresa" value="{$values['D024_Nome_Empresa']}" /></td>
												<td style="width: 14%;" align="right">Apelido *</td>
												<td style="width: 30%;"><input style="width:300px;{$erros['T006_Observacao_Apelido']}" type="text" placeholder="Apelido utilizado no mercado livre" {$disabled} name="T006_Observacao_Apelido" id="apelido" value="{$values['T006_Observacao_Apelido']}" /></td>												
											</tr>
											<tr>
												<td align="right">CPF *</td>
												<td><input style="width: 200px;{$erros['D024_CPF']}" class="inputText" {$disabled} type="text" name="D024_CPF" placeholder="Digite seu CPF" onKeyPress="return(Formatar_Cpf('8',this,event));" maxlength="14" value="{$values['D024_CPF']}"/></td>
												<td align="right">E-mail *</td>
												<td><input style="width:300px;{$erros['D024_E_Mail']}" placeholder="E-mail cadastrado no mercado livre" type="text" {$disabled} name="D024_E_Mail" value="{$values['D024_E_Mail']}" /></td>
											</tr>
											<tr>
												<td align="right">Telefone *</td>
												<td><input type="text" style="{$erros['D024_DDD_Telefone_1']}width:33px;" {$disabled} name="D024_DDD_Telefone_1" placeholder="DDD" style="width:30px;" value="{$values['D024_DDD_Telefone_1']}" maxlength="2" /> - <input style="width:151px;{$erros['D024_Telefone_1']}" {$disabled} type="text" placeholder="Telefone" name="D024_Telefone_1" value="{$values['D024_Telefone_1']}" /></td>
												<td align="right">Telefone 2</td>
												<td><input type="text" style="{$erros['D024_DDD_Telefone_2']}width:33px;" {$disabled} name="D024_DDD_Telefone_2" placeholder="DDD" style="width:30px;" value="{$values['D024_DDD_Telefone_2']}" maxlength="2" /> - <input style="width:153px;{$erros['D024_Telefone_2']}" {$disabled} type="text" placeholder="Telefone" name="D024_Telefone_2" value="{$values['D024_Telefone_2']}" /></td>												
											</tr>
											<tr>
												<td align="right">Endereço *</td>
												<td><input style="width:280px;{$erros['D024_Endereco']}" type="text" {$disabled} placeholder="Digite seu endereço" name="D024_Endereco" value="{$values['D024_Endereco']}" /></td>
												<td align="right">Número *</td>
												<td><input style="width:200px;{$erros['D024_Numero']}" type="text" {$disabled} name="D024_Numero"  placeholder="Digite seu numero" value="{$values['D024_Numero']}"/></td>
											</tr>
											<tr>
												<td align="right">Complemento</td>
												<td><input style="width:200px;{$erros['D024_Complemento']}" {$disabled} type="text" placeholder="Digite seu complemento" value="{$values['D024_Complemento']}" name="D024_Complemento" /></td>
												<td align="right">Bairro *</td>
												<td><input style="width:200px;{$erros['D024_Bairro']}" {$disabled} type="text" value="{$values['D024_Bairro']}" placeholder="Digite seu bairro" name="D024_Bairro" /></td>
												
											</tr>
											<tr>
												<td align="right">CEP *</td>
												<td><input style="width:200px;{$erros['D024_Cep']}" {$disabled} OnKeyPress="return formatar(this, '#####-###', event);" maxlength="9" placeholder="Digite seu cep" type="text" value="{$values['D024_Cep']}" name="D024_Cep" /></td>
												<td align="right"></td><td style="width: 50px;">
												</td>
											</tr>
											<tr>
                                                <td align="right">Estado *</td>
                                                <td style="width: 50px;">
                                                    <select id="D024_D018_Id" name="D024_D018_Id" style="{$erros['D024_D018_Id']}" {$disabled} onchange="alteraEstado(this)">
                                                        <option style="width:50px;" value="">...</option>
EOT;
                                                        while($mD018 = mysql_fetch_array($D018)){
                                                            $html .= <<<EOT
                                                            <option style="width:50px;" value="{$mD018['D018_Id']}">{$mD018['D018_UF']}</option>
EOT;
                                                        }
                                      $html .= <<<EOT
                                                    </select>
                                                </td>
												<td align="right">Cidade *</td>
												<td>
													<select name="D024_D020_Id" id="D024_D020_Id" {$disabled} style="width:280px;{$erros['D024_D020_Id']};">
EOT;
														while($mD020 = mysql_fetch_array($D020)){
															$html .= <<<EOT
															<option style="width:380px;" value="{$mD020['D020_Id']}">{$mD020['D020_Nome_Cidade']}</option>
EOT;
														}
														$html .= <<<EOT
													</select>
												</td>
											</tr>
											<tr>
												<th colspan="4">Selecione o produto</th>
											</tr>
EOT;
									
									if($msg != ""){
									$html .= <<<EOT
											<tr>
												<td colspan="4">{$msg}</td>
											</tr>
EOT;
									}
									$html .=<<<EOT
											<tr>
												<td colspan="4">
													<div class="contentProduto">
														<table class="tableProdutos">
															<tr>
																<td style="border-bottom:1px solid #ccc;">
																	&nbsp;
																</td>
																<td style="border-bottom:1px solid #ccc;">
																	&nbsp;
																</td>
																<td style="border-bottom:1px solid #ccc;">
																	Qnt
																</td>
																<td style="border-bottom:1px solid #ccc;">
																	Produto
																</td>
																<td style="border-bottom:1px solid #ccc;">
																	&nbsp;
																</td>
																<td style="border-bottom:1px solid #ccc;">
																	&nbsp;
																</td>
																<td style="border-bottom:1px solid #ccc;">
																	Qnt
																</td>
																<td style="border-bottom:1px solid #ccc;">
																	Produto
																</td>
															</tr>
EOT;
												$count=1;
												$sum=0;
												$numrows = mysql_num_rows($D009);
												while($mD009 = mysql_fetch_array($D009)){
													$coluna = $count % 2;
													if($coluna != 0){
														$html .=<<<EOT
															<tr>
EOT;
													}	
														//echo 'count: '.$_POST['D009_Id-'.$count]."<br />D009: ".$mD009['D009_Id']."<br /><br />";
														if($_POST['D009_Id-'.$count] == $mD009['D009_Id']){
															$checked = 'checked="checked"';
														}else{
															$checked="";
														}
														
														/**
														* Pegando a imagem do produto
														*/
														$extensao = explode(".", $mD009['T172_Nome_Arquivo']);
	
														if(!empty($mD009['T172_Nome_Arquivo'])){
															$caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}produtos/{$mD009['D001_Id']}/fotos/{$mD009['T172_Id']}.{$extensao[1]}";
														}else{
															$caminhoImagem="";
														}
														if($caminhoImagem == ""){
															$imgProd="<img src='http://tudo.hardness.com.br//hardness3/static/img/indisponivel100x100.jpg' style='width:70px;height:70px;'>";
														}else{
															$imgProd='<img src="'.$caminhoImagem.'" style="width:70px;height:70px;">';
														}
														$html .=<<<EOT
																	<td style="border-bottom:1px solid #ccc;width:70px;">
																		{$imgProd}
																	</td>
																	<td style="border-bottom:1px solid #ccc;">
																		<input type="checkbox" {$checked} value="{$mD009['D009_Id']}" {$disabled} name="D009_Id-{$count}" />
																	</td>
																	<td style="border-bottom:1px solid #ccc;">
																		<input type="text" {$disabled} name="T006_Quantidade-{$count}" value="1" style="text-align:center;width:40px;"/>
																	</td>
																	<td style="border-bottom:1px solid #ccc;">
																		{$mD009['D001_Descricao_Produto']}
																	</td>
EOT;
													if($coluna == 0){
														$html .=<<<EOT
															</tr>
EOT;
													}
													$count++;
													$sum++;
												}
                                   $html .=<<<EOT
                                   						<input type="hidden" value='{$count}' name="count"/>
														</table>
													</div>
												</td>
											</tr>
											<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
												if('{$D018_Id}' != '') {
													$('#D024_D018_Id').val({$resD024['D024_D018_Id']});
													$.getJSON('/cad/cad001/form_func-ajax/alteraEstado/?ajax=true&D024_D018_Id=' + encodeURIComponent({$resD024['D024_D018_Id']}) + '&callback=?', function(request) {
														if (request.code) {
															$('#D024_D020_Id').html(request.data['D024_D020_Id']);
														}
													});
													$('#D024_D020_Id').val({$resD024['D024_D020_Id']});
												}
											}</script></div>
											<tr>
												<td colspan="4">
													<b style="font-size:12px;">
														CASO NÃO TENHA ENCONTRADO O SEU PRODUTO OU HAJA ALGUMA MODIFICAÇÃO DO SEU PEDIDO, DESCREVA NO CAMPO ABAIXO
													</b>
												</td>
											</tr>
											<tr>
                                                <td colspan="4">
                                                    <table>
                                                        <tr>
                                                            <td><i style="font-size:12px;">Ex. Comprei um kit e desejo modificar a configuração das anilhas.</i></td>
                                                            <td><textarea style="{$erros['T005_Observacao']} height:120px; width:650px;{$erros['T005_Observacao']}" {$disabled} name="T005_Observacao" />{$values['T005_Observacao']}</textarea></td>
                                                        </tr>
                                                    </table>
												</td>
											</tr>
											<tr>
												<th colspan="4">Selecione a forma de pagamento</th>
											</tr>
EOT;
									if($msgPagamento != ""){
										$html .=<<<EOT
											<tr>
												<td colspan="4">{$msgPagamento}</td>
											</tr>
EOT;
									}
									$html.=<<<EOT
											<tr>
												<td colspan="4">
													<table>
														<tr>
															<td><input type="radio" name="formaPagamento" value="JÁ REALIZEI O PAGAMENTO ATRAVÉS DO MERCADO PAGO" /></td>
															<td>JÁ REALIZEI O PAGAMENTO ATRAVÉS DO MERCADO PAGO</td>
														</tr>
														<tr>
															<td><input type="radio" name="formaPagamento" value="DESEJO RECEBER OS DADOS BANCÁRIOS PARA DEPÓSITO OU TRANSFERÊNCIA EM CONTA NO MEU E-MAIL" /></td>
															<td>DESEJO RECEBER OS DADOS BANCÁRIOS PARA DEPÓSITO OU TRANSFERÊNCIA EM CONTA NO MEU E-MAIL</td>
														</tr>
														<tr>
															<td><input type="radio" name="formaPagamento" value="VOU RETIRAR O PRODUTO PESSOALMENTE E FAZER O PAGAMENTO NO LOCAL" /></td>
															<td>VOU RETIRAR O PRODUTO PESSOALMENTE E FAZER O PAGAMENTO NO LOCAL</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<th colspan="4">Selecione a forma de envio</th>
											</tr>
EOT;
									if($msgEnvio != ""){
										$html .=<<<EOT
											<tr>
												<td colspan="4">{$msgEnvio}</td>
											</tr>
EOT;
									}
									$html.=<<<EOT
	
											<tr>
												<td colspan="4">
													<table>
														<tr>
                                                            <td><input type="radio" name="formaEnvio" value="AINDA NÃO SEI VALOR DO FRETE E DESEJO RECEBER O VALOR E OS DADOS PARA PAGAMENTO NO MEU E-MAIL" /></td>
                                                            <td>AINDA NÃO SEI VALOR DO FRETE E DESEJO RECEBER O VALOR E OS DADOS PARA PAGAMENTO NO MEU E-MAIL</td>
                                                        </tr>
                                                        <tr>
															<td><input type="radio" name="formaEnvio" value="JÁ SEI O VALOR DO FRETE E PAGUEI JUNTO COM O PRODUTO" /></td>
															<td>JÁ SEI O VALOR DO FRETE E PAGUEI JUNTO COM O PRODUTO</td>
														</tr>
														<tr>
															<td><input type="radio" name="formaEnvio" value="JÁ SEI O VALOR DO FRETE E VOU REALIZAR O PAGAMENTO POR DEPÓSITO EM CONTA" /></td>
															<td>JÁ SEI O VALOR DO FRETE E VOU REALIZAR O PAGAMENTO POR DEPÓSITO EM CONTA<br /><font size="2"><i>(Bancos para realizar o depósito: Bradesco Ag. 2471 C/c 13761-8 / Itaú Ag. 3812 C/c 23759-6 / CEF Ag. 1971 C/c 1513-7 op 003 - Após efetuado o depósito, envie o comprovante por email para vendas@tudoesportes.com.br)</i></font></td>
														</tr>
														<tr>
															<td><input type="radio" name="formaEnvio" value="VOU RETIRAR O PRODUTO PESSOALMENTE E DESEJO RECEBER O ENDEREÇO PARA RETIRADA NO MEU E-MAIL" /></td>
															<td>VOU RETIRAR O PRODUTO PESSOALMENTE E DESEJO RECEBER O ENDEREÇO PARA RETIRADA NO MEU E-MAIL</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="4" align="center"><input style="margin-right:20px; width: 100px; height: 30px;" type="submit" value="Enviar" /></td>
											</tr>
											<tr>
												<td style="border-top:1px solid silver;" colspan="4" align="center">Se tiver alguma dúvida entre em contato com nossa Central de Atendimento pelo email vendas@tudoesportes.com.br ou pelo telefone 41-3042 3939 de segunda a sexta-feira das 9:00 as 18:00hrs</td>
											</tr>
										</table>
									</form>
								</div>
							</div>
						</div>
					</body>
				</html>
EOT;
			echo $html;
			
		break;
	}
?>