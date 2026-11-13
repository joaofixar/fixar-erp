<?php
namespace hardness;
/*
* class.exportacao.php
*/
Class Exportacao
{
	static $empresa_atual;
	static $xml_tags;
    static $item_produto_count;
	
	public function __construct( $ecommerce )
	{
        global $g;
		if( file_exists('bibliotecas/webservices/impNota/'.$ecommerce.'.tags.php') )
		{
			/* Arquivo que contem as tags utilizadas para listar o XML */
			self::$xml_tags = 'bibliotecas/webservices/impNota/'.$ecommerce.'.tags.php';
            
		}else
		{
			echo '<b>Tags</b> do webservice "'.ucfirst($ecommerce).'" nao encontrada.';

			exit;
		}
		
		if (!empty($g['empresaAtual']))
		{
			self::$empresa_atual = $g['empresaAtual'];
		}
        elseif( !empty ($_GET['p']) )
        {
            self::$empresa_atual = $_GET['p'];
        }
        else
        {
	        self::$empresa_atual = 1;
            file_put_contents('/tmp/impNota_ERRO_EMPRESA_EMPTY', self::$empresa_atual);
        }
	}
        
    /*
     * E a funcao de inicializacao da Classe, chamada no documento que recebe o POST
     * Recebe como parametro:
     *      $dados            -> E o XML total do pedido, recebido via POST do ecommerce
	*/
	public function comeco( $dados )
	{
        global $g;
    	include(self::$xml_tags);

        #$xml = file_get_contents($dados['xml']);
        #print_r($dados['xml']);
        echo "<br>iniciando a leitura do xml";
        $xml = $dados['XML'];    /* NOME DO CAMPO PELO QUAL FOI ENVIADO O XML VINDA DO ECOMMERCE impNota */
        $xml = utf8_encode($xml);
        
        /* Caso a requisicao tenha dado certo, retornado um xml valido */
        $domDocument = new \DomDocument();
        $domDocument->loadXML($xml);
        $domPatch = new \DomXPath($domDocument);
        
   		$filtro = $domDocument->getElementsByTagName($tag['export']['filtro'])->item(0);
		
        if( $domPatch->query(".//data_hora_exportacao_xml", $filtro)->length )
        {
        	echo "<br>processar o pedido";
        	$this->processa_pedido( $xml );
        }else
			echo '<br>Nenhum resultado do Webservice [pedido]';	
   }

   /*
   * Funcao que processa todo o pedido, chama as outras funcoes para criar cabecalho,cliente ...
   * Recebe como parametro:
   *      $xml            -> E o XML total do pedido, enviado pela funcao 'comeco'
   */
    private function processa_pedido( $xml )
    {
        global $g;
   		include(self::$xml_tags);
        set_time_limit(0);

	    echo "<br>inicio processar o pedido"; 
		
        $dom = new \DOMDocument;
        $dom->loadXML( $xml );
        $pedidos = $dom->getElementsByTagName( $tag['export']['pedido'] );
		
        $total_pedidos=0;
        foreach( $pedidos AS $pedido )
        {
        	echo "<br>loop nos pedidos";

			mysql_query("BEGIN");
			
			$idPedido = $pedido->getElementsByTagName( $export['pedido']['idPedido'] )->item(0)->nodeValue;
			$dataPedido = $pedido->getElementsByTagName( $export['pedido']['dataPedido'] )->item(0)->nodeValue;
			$desconto = str_replace(',','.',$pedido->getElementsByTagName( $export['pedido']['desconto'] )->item(0)->nodeValue);
			$observacao_T007 = $pedido->getElementsByTagName( $export['pedido']['observacao'] )->item(0)->nodeValue;
			//$frete = str_replace(',','.',$pedido->getElementsByTagName( $export['pedido']['frete'] )->item(0)->nodeValue);
			$frete = 0;
			
			#$valor_total_produtos = $pedido->getElementsByTagName( $export['pedido']['valorTotalPedido'] )->item(0)->nodeValue;
			$valor_total_produtos=0;
			
			
			echo "<br>Criado log do XML";
        	file_put_contents('/tmp/LOG_'.$idPedido.'_'.date("d-m-Y"), $xml);
			
			$produtos = $pedido->getElementsByTagName( $tag['export']['produtos'] );
			foreach( $produtos AS $produto )
			{
				$valor_total_produtos += str_replace(',','.',$produto->getElementsByTagName( $export['pedido']['quantidade'] )->item(0)->nodeValue) * str_replace(',','.',$produto->getElementsByTagName( $export['pedido']['valorUnitario'] )->item(0)->nodeValue);
			}
			

			/* Verifica a existencia do cliente */
			$idCliente = $this->search_client( $xml, $pedido->getElementsByTagName( $export['pedido']['cpf_cnpj'] )->item(0)->nodeValue );
			
			/* VERIFICA QUAL CFOP VAI SER UZADA */
			$sql = mysql_query("
            					SELECT
                                	C004_Id,
									D018_UF,
									C004_CFOP_Venda_Dentro_Estado,
									C004_CFOP_Venda_Fora_Estado
                                FROM C004
									LEFT JOIN D018 ON D018_Id=C004_D018_Id
                                WHERE
									C004_Id='".self::$empresa_atual."'
				  ");  	
    		$mC004 = mysql_fetch_assoc($sql);
		
			$sql2 = mysql_query("
		    					SELECT
                                	D024_Id,
									D018_UF
                                FROM D024
									LEFT JOIN D018 ON D018_Id=D024_D018_Id
                                WHERE
									D024_Id='".$idCliente."'
                            ");
			$mD024 = mysql_fetch_assoc($sql2);
		
			if( $mC004['D018_UF'] == $mD024['D018_UF']) // Colocado FIXO por estar com problemas, verificar!!!
    		{
            	$CFOP = $mC004['C004_CFOP_Venda_Dentro_Estado'];
    		}
    		else
    		{
            	$CFOP = $mC004['C004_CFOP_Venda_Fora_Estado'];
    		}
            
			/* FIM VERIFICACAO DA CFOP */

        	/* Array a ser passado para criar o cabecalho do pedido */
			$to_cabecalho = array(
              'idCliente'=>$idCliente,
              'idPedido'=>trim($idPedido),
              'dataPedido'=>$dataPedido,
              'desconto'=>$desconto,
              'frete'=>$frete,
              'valor_total_produtos'=>$valor_total_produtos,
              'CFOP'=>$CFOP,
              'observacao'=>$observacao_T007
            );
								
			/* Verifica se o cabecalho com o numero do pedido vindo do ecommerce ja existe */
			/* Se nao existir cria e retorna o ID do T007, senao apenas retorna o ID do T007 */
			$sql = mysql_query("SELECT T007_Id FROM T007 WHERE T007_Numero_Pedido_Venda='".$to_cabecalho['idPedido']."'");
			if(mysql_num_rows($sql)==0)
			{
				$idCabecalho = $this->cria_cabecalho_pedido($xml,$to_cabecalho);
				$item_numero=0;
				foreach( $produtos AS $produto )
				{
                	$item = array(
						'idProduto'=>$produto->getElementsByTagName( $export['pedido']['idProduto'] )->item(0)->nodeValue,
						'idPedido'=>$idPedido,
						'codigo'=>$produto->getElementsByTagName( $export['pedido']['codigo'] )->item(0)->nodeValue,
						/* Retirado a descricao, para caso seja um KIT, coloca uma */
                        'descricao'=>$produto->getElementsByTagName( $export['pedido']['descricao'] )->item(0)->nodeValue,
                        //'descricao'=>'',
                        'quantidade'=>str_replace(',','.',$produto->getElementsByTagName( $export['pedido']['quantidade'] )->item(0)->nodeValue),
						'valorUnitario'=>str_replace(',','.',$produto->getElementsByTagName( $export['pedido']['valorUnitario'] )->item(0)->nodeValue),
						'idCabecalho'=>$idCabecalho,
						'idKit'=>'',
						'type'=>'NORMAL',
						'CFOP'=>$CFOP
                    );
                    $item_numero++;
                    $this->add_produto_pedido($xml,$item,$item_numero);
				}
				//$this->atualizarAliquotas($idCabecalho); 
				$total_pedidos++;

                if (!is_dir("{$g['pathDados']}/ecommerce/xmlPedido/")) {
                    mkdir("{$g['pathDados']}/ecommerce/xmlPedido/", 0777,true);
                }

                $cArquivoSalvar = "{$g['pathDados']}/ecommerce/xmlPedido/PEDIDO_{$idCabecalho}.xml";

                $fp = fopen($cArquivoSalvar, "a");
                if (!@fwrite($fp,"$xml"))
                { 
                    mysql_query("ROLLBACK");
                    return "Não foi possível continuar, o arquivo: " . $cArquivoSalvar . "Não pode ser salvo. Verique as permissões na pasta do arquivo.";
                }

			}

        	/* SO ENTRA NO ELSE SE O PEDIDO JA EXISTIR */
            // echo "<br>O pedido ja existe";
            // file_put_contents('/tmp/impNota_ERRO_'.$idPedido.'_PedidoExistente', $xml);
			
			/* Atualizando aliquotas */
   			echo '<br>'.$idCabecalho.'<br>';
   			require_once("bibliotecas/classes/VEN003.php");
   			$VEN003 = new VEN003();
   			$retorno = $VEN003->ven003atualizarCFOP($idCabecalho);
		
			if ($retorno === true) {
				echo $resposta['code'] = true;
				echo $resposta['data'] = "Atualizado!";
			} else {
				// excluir
				echo $resposta['code'] = false;
				echo $resposta['data'] = $retorno;
			}
			/* Fim atualizar aliquotas */
			
            mysql_query("COMMIT");
   		}/* Fim foreach */

    	echo '<br>Finalizado com sucesso<br>Foi adicionado '.$total_pedidos.' pedidos.';
	} /* Fim funcao processa_pedido */

   /*
   * Funcao que e chamada no foreach dos produtos, adiciona 1 produto de cada vez
   * Recebe como parametros:
   *      $xml            -> E o XML total do pedido, para caso aconteca algum erro
   *      $item           -> Array com as informacoes do produto
   *      $item_numero    -> numero do item (1,2,3,4 ...)
   */
   private function add_produto_pedido( $xml, $item, $item_numero )
   {
   	   	require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
   		echo "<br>inicio add produto pedido:\n<br />"; 
        print_r($item);
        echo "<br />\n";
   	    
        $sql = mysql_query("
							SELECT
                            	D001_D005_Id,
                                D001_D037_Id,
                                D009_Id,
                                D049_Id,
                                D049_Flag_Tipo,
                                D009_D049_Id,
                                D001_Descricao_Produto,
                                D082_Marca
                            FROM D001
                                LEFT JOIN D049 ON D049_D001_Id=D001_Id
                                LEFT JOIN D082 ON D082_Id=D049_D082_Id
                                LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id='".self::$empresa_atual."'
							WHERE
                				D001_Codigo_Produto='".$item['codigo']."'
							LIMIT 1
                            ");


        $mD009=mysql_fetch_array($sql);

        if(mysql_num_rows($sql)==0)
        {
			//mysql_query("ROLLBACK");
			echo '<br>Nao existe o produto "'.$item['codigo'].'" cadastrado no impNotatema Hardness.<br>idPedido vindo do webservice "'.$item['idPedido'].'"';
            file_put_contents('/tmp/impNota_ERRO_'.$item['idPedido'].'_ProdutoInezistente', $xml);
            #$descricao_produto = $item['descricao'];
            //exit;

            echo "<br />Pré cadastrando o produto<br />";
            $mD009['D009_Id'] = $this->preCadastroProduto($item);
            $D009=mysql_query("SELECT * FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$mD009['D009_Id']}'");
            $mD009=mysql_fetch_array($D009);
        }
        //else
        //{
        	/* Verifica se e um KIT */
        	if($mD009['D049_Flag_Tipo'] == 'MT')
        	{
				$sql_kit = mysql_query("
 									SELECT * FROM D042
										LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens
    									LEFT JOIN D082 ON D049_D082_Id=D082_Id
										LEFT JOIN D001 ON D001_Id=D049_D001_Id
    									LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens
    									LEFT JOIN D041 ON D041_D049_Id=D049_Id
    									AND D009_C004_Id=".self::$empresa_atual."
									WHERE D042_D049_Id='".$mD009['D049_Id']."'
    									AND D042_C004_Id=".self::$empresa_atual."
    									AND D009_C004_Id=".self::$empresa_atual."
        		");
        		$kit_item_numero = 0;
        		while($open = mysql_fetch_array($sql_kit))
        		{
        			$D001_mD009_descricao_produto = empty($mD009['D001_Descricao_Produto']) ? $item['descricao'] : $mD009['D001_Descricao_Produto'];
        			
        			// Foi coocado fixo o lista preço porque no na inserção da T007 está inserindo o lista preço fixo
        			$preco = $CAD002->retornaPrecoVenda($open['D009_Id'], '1', null);
        			
        			$kit_itens = array(                                                                              
						'idPedido'=>$item['idPedido'],
						'codigo'=>$open['D001_Codigo_Produto'],
                        'descricao'=>"{$open['D001_Descricao_Produto']}", //'descricao'=>"{$open['D001_Descricao_Produto']} ({$D001_mD009_descricao_produto})", <- com nome do kit entre parenteses
                        'quantidade'=>$item['quantidade']*$open['D042_Quantidade'],
						'valorUnitario'=>$item['quantidade']*$preco,
						'idCabecalho'=>$item['idCabecalho'],
						'idKit'=>$mD009['D009_Id'],
						'type'=>'KIT',
						'CFOP'=>$item['CFOP']
						);        
					$kit_item_numero++;
					$this->add_produto_pedido($xml,$kit_itens,$kit_item_numero);
        		}
        		//die();
        		return false;
        	}
        	
        	$descricao_produto = ( !empty($mD009['D001_Descricao_Produto']) AND $item['type']=='NORMAL' ) ? $mD009['D001_Descricao_Produto'] : $item['descricao'];
        	$id_kit = empty($item['idKit']) ? '0' : $item['idKit'];
        //}        
		
		self::$item_produto_count++;
        mysql_query("
                	INSERT INTO T008(
                            T008_T007_Id,
                            T008_D006_Id,
                            T008_D009_Id,
                            T008_C004_Id,
                            T008_D005_Id,
                            T008_D037_Id,
                            T008_Codigo_Produto,
                            T008_Descricao_Produto,
                            T008_Quantidade,
                            T008_Valor_Preco_Sem_Desconto_Unitario,
                            T008_Valor_Preco_Unitario
					) VALUES (
                            '".$item['idCabecalho']."',
                            '".$item['CFOP']."',
                            '".$mD009['D009_Id']."',
                            '".self::$empresa_atual."',
                            '".$mD009['D001_D005_Id']."',
                            '".$mD009['D001_D037_Id']."',
                            '".$item['codigo']."',
                            '".$descricao_produto."',
                            '".$item['quantidade']."',
                            '".$item['valorUnitario']."',
                            '".$item['valorUnitario']."'
					);
			");
        /* T008_Peso_Unitario */
        $cErro = mysql_error();
        if (!empty($cErro))
        {
        	#mysql_query("ROLLBACK");
            echo 'Erro ao cadastrar o produto "'.$item['codigo'].'". idCabecalho="'.$item['idCabecalho'].'"';
            file_put_contents('/tmp/impNota_ERRO_'.$item['idPedido'].'_CadastroProduto', $xml);
            //exit;    
        }     

        $T008_Id=mysql_insert_id();
        mysql_query("call T008_Gravar_Totalizacao_4('$T008_Id')");

   }

        
        
        
        
        
   /*
   * Funcao que cria o cabecalho do pedido, e retorna o ID do cabecalho
   * Recebe como parametros:
   *      $xml            -> E o XML total do pedido, para caso aconteca algum erro
   *      $content           -> Array com as informacoes para o cabecalho, e gerado pela funcao processa_pedidos
   */
   private function cria_cabecalho_pedido( $xml, $content )
   {
   		include(self::$xml_tags);
		
   	    echo "<br>cria cabecalho pedido"; 
		
        $com_desconto = $content['valor_total_produtos'] - $content['desconto'];
        $percentual_desconto = (100*$com_desconto) / $content['valor_total_produtos'];
        $percentual_desconto = 100 - $percentual_desconto;
		
        $valor_percentual_desconto = ($content['valor_total_produtos']*$percentual_desconto) / 100;
            
        $dataPedido = substr($content['dataPedido'], 6,4).'-'.substr($content['dataPedido'], 3,2).'-'.substr($content['dataPedido'], 0,2);
            
        /* Verifica se existe o usuario ecommerce, se tiver pega o ID dele, senao joga o pedido para o user ROOT */
        $sql_user = mysql_query("SELECT C007_Id FROM c007 WHERE C007_Nome='ecommerce' OR C007_Primeiro_Nome='ecommerce'");
        if( @mysql_num_rows($sql_user) )
        {
        	$open = mysql_fetch_assoc($sql_user);
            $usuario = $open['C007_Id'];
        }else
        	$usuario = '63';
		
        mysql_query ("
			INSERT INTO T007(
                            T007_Numero_Pedido_Venda,
                            T007_Flag_Revenda_Consumidor,
                            T007_D024_Id,
                            T007_C004_Id,
                            T007_D006_Id,
                            T007_C007_Id_Vendedor_Interno,
                            T007_Data_Emissao,
                            T007_Data_Saida,
                            T007_Flag_ACP,
                            T007_Valor_Desconto,
                            T007_Percentual_Desconto,
                            T007_Valor_Percentual_Desconto,
                            T007_Valor_Total_Produtos,
                            T007_Valor_Total_Produtos_Sem_Desconto,
                            T007_Valor_Total,
                            T007_Flag_Travar_Nota,          					
                            T007_Flag_Expedicao,
                            T007_Observacao_1,
                            T007_Flag_Impresso,
                            T007_Flag_Entrada_Saida
			         ) VALUES (
                            '".$content['idPedido']."',
                            '1',
                            '".$content['idCliente']."',
                            '".self::$empresa_atual."',
                            '".$content['CFOP']."',
                            '".$usuario."',
                            '".$dataPedido."',
                            current_date(),
                            '6',
                            '".$content['desconto']."',
                            '".$percentual_desconto."',
                            '".$valor_percentual_desconto."',
                            '".($content['valor_total_produtos']-$content['desconto'])."',
                            '".$content['valor_total_produtos']."',
                            '".($content['valor_total_produtos']-$content['desconto']+$content['frete'])."',
                            '2',
                            '2',
                            '".$content['observacao']."',
                            'N',
                            'S'
			);
			");
		/*
        T007_Valor_Frete, -> '".$content['frete']."',
        T007_Peso_Total,
        T007_Nome_Transportadora,
        T007_Peso_Liquido,
        T007_Peso_Bruto,
        */
					  
        $cErro = mysql_error();
        if (!empty($cErro))
        {
        	//mysql_query("ROLLBACK");
   			echo '<br>Erro ao criar cabecalho do pedido. idPedido vindo do webservice "'.$content['idPedido'].'"'.'<br>'.$cErro;
            file_put_contents('/tmp/impNota_ERRO_'.$content['idPedido'].'_CriandoCabecalho', $xml);
			//exit;
        }
        else
        {
        	$sql = mysql_query("SELECT T007_Id FROM T007 WHERE T007_Numero_Pedido_Venda='".$content['idPedido']."'");
   			$open = mysql_fetch_assoc($sql);
            //Cria histórico do pedido
            mysql_query("insert into T178 (T178_T007_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$open['T007_Id']}','{$usuario}',CURRENT_TIMESTAMP(),'Importado Ecommerce','')");
            
   			return $open['T007_Id'];
        }
   }
	
        
        
        
        
        
   /*
   * Funcao que procura o cliente, se ja tiver ele cadastradado, atualiza os dados e retorna o ID
   * Se nao tiver o cliente cadastrado cadastra ele, e retorna o ID dele
   * Recebe como parametros:
   *      $xml            -> E o XML total do pedido, para caso aconteca algum erro
   *      $cpf_cnpj       -> E o numero do CPF ou CNPJ, para consultar a DB
   */
   private function search_client( $xml, $cpf_cnpj )
   {
   		/*PESQUISA POR CPF*/
        if( strlen( $cpf_cnpj ) == 11 )
        {
        	$cpf = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
        	$cpf = substr( $cpf, 0, 3 ).".".substr( $cpf, 3, 3 ).".".substr( $cpf, 6, 3 )."-".substr( $cpf, 9, 2 );
			
   			$sql = mysql_query("SELECT * FROM D024 WHERE d024_Cpf='{$cpf}'");

			/* Se retornar 0, e que o cliente nao existe, entao cadastra o cliente */
			if( mysql_num_rows( $sql ) == 0 )
			{				
            	return $this->cadastro_client( $xml, $cpf_cnpj );
			}
			/* Se nao for 0 retorna o id do cliente para ser usado no pedido */
   			else{
   				return $this->atualiza_client( $xml, $cpf_cnpj );
            	//$sql = mysql_query("SELECT d024_Id FROM d024 WHERE D024_Cpf='{$cpf}'");
            	//$open = mysql_fetch_assoc($sql);
                //return $open['d024_Id'];
			}
        }
            
        /*PESQUISA POR CNPJ*/
        elseif( strlen( $cpf_cnpj ) == 14 )
        {
        	$cnpj = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
	    	$cnpj = substr( $cnpj, 0, 2 ).".".substr( $cnpj, 2, 3 ).".".substr( $cnpj, 5, 3 )."/".substr( $cnpj, 8, 4 )."-".substr( $cnpj, 12, 2 );
			
			$sql = mysql_query("SELECT * FROM D024 WHERE d024_cnpj='{$cnpj}'");
			
			/* Se retornar 0, e que o cliente nao existe, entao cadastra o cliente */
			if( mysql_num_rows( $sql ) == 0 )
			{
            	return $this->cadastro_client( $xml, $cpf_cnpj );
   			}
			/* Se nao for 0 retorna o id do cliente para ser usado no pedido */
			else{
				return $this->atualiza_client( $xml, $cpf_cnpj );
            	//$sql = mysql_query("SELECT d024_Id FROM d024 WHERE D024_Cnpj='{$cpj}'");
                //$open = mysql_fetch_assoc($sql);
                //return $open['d024_Id'];
			}
        }
        else
        	return FALSE;
   }
   
   
   
   
   
   
   /*
   * Funcao que atualiza todos os dados do cliente que JA ESTA CADASTRADO
   * Recebe como parametros:
   *      $xml_cliente       -> E o XML total do pedido, serve para pegar os dados do cliente e para caso aconteca algum erro
   *      $cpf_cnpj          -> E o numero do CPF ou CNPJ
   */
   private function atualiza_client( $xml_cliente, $cpf_cnpj )
   {
        include(self::$xml_tags);

   	    echo "<br>Atualiza cadastro cliente"; 
		
   		$dom = new \DOMDocument('1.0', 'iso-8859-1');
        $dom->loadXML( $xml_cliente );
		
        /*NORMAS DO CPF_CNPJ*/
        if( strlen( $cpf_cnpj ) == 11 )
        {
        	$cpf = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
        	$cpf = substr( $cpf, 0, 3 ).".".substr( $cpf, 3, 3 ).".".substr( $cpf, 6, 3 )."-".substr( $cpf, 9, 2 );
			$cnpj = '';
			$pessoa_fisica_juridica = 'F';
            $where = " WHERE D024_Cpf='{$cpf}' ";
        }
        elseif( strlen( $cpf_cnpj ) == 14 )
        {
        	$cnpj = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
	    	$cnpj = substr( $cnpj, 0, 2 ).".".substr( $cnpj, 2, 3 ).".".substr( $cnpj, 5, 3 )."/".substr( $cnpj, 8, 4 )."-".substr( $cnpj, 12, 2 );
			$cpf = '';
			$pessoa_fisica_juridica = 'J';
            $where = " WHERE D024_Cnpj='{$cnpj}' ";
        }
            
            
        /* VERIFICA SE E RG OU InscrisaoEstadual */
        if( $dom->getElementsByTagName( $export['cliente']['rg_ie'] )->item(0)->nodeValue == 'RG' )
        {
        	$rg = $dom->getElementsByTagName( $export['cliente']['documento'] )->item(0)->nodeValue;
            $ie = '';
		}
        else
        {
			$rg = '';
			$ie = $dom->getElementsByTagName( $export['cliente']['documento'] )->item(0)->nodeValue;
        }
			
			
        /* @TELEFONE */
        /* Coloca nas normas o Telefone e o DDD do cadastro do cliente */
        $ddd = substr( $dom->getElementsByTagName( $export['cliente']['telefone1'] )->item(0)->nodeValue, 0, 2);
        $telefone = substr( $dom->getElementsByTagName( $export['cliente']['telefone1'] )->item(0)->nodeValue, 2);
        $ddd2 = substr( $dom->getElementsByTagName( $export['cliente']['telefone2'] )->item(0)->nodeValue, 0, 2);
        $telefone2 = substr( $dom->getElementsByTagName( $export['cliente']['telefone2'] )->item(0)->nodeValue, 2);
			
        /* Coloca nas normas o Telefone e o DDD PARA A ENTREGA */
        $ddd_entrega = substr( $dom->getElementsByTagName( $export['pedido']['entrega']['telefone'] )->item(0)->nodeValue, 0, 2);
        $telefone_entrega = substr( $dom->getElementsByTagName( $export['pedido']['entrega']['telefone'] )->item(0)->nodeValue, 2);
		
		
		
		
        /* Cidade,estado,pais no cadastro do cliente */
        $sql = mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF='".utf8_decode($dom->getElementsByTagName( $export['cliente']['estado'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado}' AND D020_Nome_Cidade='".utf8_decode($dom->getElementsByTagName( $export['cliente']['cidade'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $cidade = $open['D020_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);		
        $pais = $open['d030_Id'];
        unset($open);
		
		
		
		
        /* Cidade,estado,pais PARA A ENTREGA */
        $sql = mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF='".utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['estado'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado_entrega = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado_entrega}' AND D020_Nome_Cidade='".utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['cidade'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $cidade_entrega = $open['D020_Id'];
        unset($open);
	
    	$sql = mysql_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);		
        $pais_entrega = $open['d030_Id'];
        unset($open);
            
        $nome = utf8_decode($dom->getElementsByTagName( $export['cliente']['nome'] )->item(0)->nodeValue);
        $nome_fantasia = utf8_decode($dom->getElementsByTagName( $export['cliente']['nomeFantasia'] )->item(0)->nodeValue);
        $nome_fantasia = ($nome_fantasia=='' OR $nome_fantasia==NULL) ? $nome : $nome_fantasia;
        
        
                        
                        
    	$dados_up = array(
    					$pessoa_fisica_juridica,
                        strtoupper($nome),
                        strtoupper($nome_fantasia),
                        '1',
                        'N',
                        'S',
                        $cnpj,
                        $cpf,
                        $rg,
                        $ie,
                        strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['endereco'] )->item(0)->nodeValue)),
                        $dom->getElementsByTagName( $export['cliente']['numero'] )->item(0)->nodeValue,
                        strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['complemento'] )->item(0)->nodeValue)),
                        strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['bairro'] )->item(0)->nodeValue)),
                        $dom->getElementsByTagName( $export['cliente']['cep'] )->item(0)->nodeValue,
                        self::$empresa_atual,
                        $cidade,
                        $estado,
                        $pais,
                        '1',
                        'S',
                        'N',
                        '1',
                        'N',
                        'DATA DO CADASTRO = IGNORAR',
                        $ddd,
                        $ddd2,
                        $telefone,
                        $telefone2,
                        strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['email'] )->item(0)->nodeValue)),
                        $cidade_entrega,
                        $estado_entrega,
                        $pais_entrega,
                        strtoupper(utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['endereco'] )->item(0)->nodeValue)),
                        strtoupper(utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['complemento'] )->item(0)->nodeValue)),
                        strtoupper(utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['bairro'] )->item(0)->nodeValue)),
                        $dom->getElementsByTagName( $export['pedido']['entrega']['cep'] )->item(0)->nodeValue,
                        $telefone_entrega,
                        $ddd_entrega
    	);
    	# D024_E_Mail = IF($email='' OR $email<0 OR $email=D024_E_Mail,D024_E_Mail,$email),
    	mysql_query("
    				UPDATE D024 SET
    					D024_Flag_Pessoa_Fisica_Juridica 	= IF( '{$dados_up[0]}'='' OR '{$dados_up[0]}'<0 OR '{$dados_up[0]}'=D024_Flag_Pessoa_Fisica_Juridica, D024_Flag_Pessoa_Fisica_Juridica,'{$dados_up[0]}' ),
                        D024_Nome_Empresa					= IF( '{$dados_up[1]}'='' OR '{$dados_up[1]}'<0 OR '{$dados_up[1]}'=D024_Nome_Empresa,D024_Nome_Empresa ,'{$dados_up[1]}' ),
                        D024_Nome_Fantasia					= IF( '{$dados_up[2]}'='' OR '{$dados_up[2]}'<0 OR '{$dados_up[2]}'=D024_Nome_Fantasia,D024_Nome_Fantasia ,'{$dados_up[2]}' ),
                        D024_Flag_Contribuinte				= '1',
                        D024_Flag_Restricao					= 'N',
                        D024_Flag_Liberado					= 'S',
                        D024_Cnpj							= IF( '{$dados_up[6]}'='' OR '{$dados_up[6]}'<0 OR '{$dados_up[6]}'=D024_Cnpj,D024_Cnpj ,'{$dados_up[6]}' ),
                        D024_Cpf							= IF( '{$dados_up[7]}'='' OR '{$dados_up[7]}'<0 OR '{$dados_up[7]}'=D024_Cpf,D024_Cpf ,'{$dados_up[7]}' ),
                        D024_Rg								= IF( '{$dados_up[8]}'='' OR '{$dados_up[8]}'<0 OR '{$dados_up[8]}'=D024_Rg,D024_Rg ,'{$dados_up[8]}' ),
                        D024_Inscricao_Estadual				= IF( '{$dados_up[9]}'='' OR '{$dados_up[9]}'<0 OR '{$dados_up[9]}'=D024_Inscricao_Estadual,D024_Inscricao_Estadual ,'{$dados_up[9]}' ),
                        D024_Endereco						= IF( '{$dados_up[10]}'='' OR '{$dados_up[10]}'<0 OR '{$dados_up[10]}'=D024_Endereco,D024_Endereco ,'{$dados_up[10]}' ),
                        D024_Numero							= IF( '{$dados_up[11]}'='' OR '{$dados_up[11]}'<0 OR '{$dados_up[11]}'=D024_Numero,D024_Numero ,'{$dados_up[11]}' ),
                        D024_Complemento					= IF( '{$dados_up[12]}'='' OR '{$dados_up[12]}'<0 OR '{$dados_up[12]}'=D024_Complemento,D024_Complemento ,'{$dados_up[12]}' ),
                        D024_Bairro							= IF( '{$dados_up[13]}'='' OR '{$dados_up[13]}'<0 OR '{$dados_up[13]}'=D024_Bairro,D024_Bairro ,'{$dados_up[13]}' ),
                        D024_Cep							= IF( '{$dados_up[14]}'='' OR '{$dados_up[14]}'<0 OR '{$dados_up[14]}'=D024_Cep,D024_Cep ,'{$dados_up[14]}' ),
                        D024_C004_Id						= IF( '{$dados_up[15]}'='' OR '{$dados_up[15]}'<0 OR '{$dados_up[15]}'=D024_C004_Id,D024_C004_Id ,'{$dados_up[15]}' ),
                        D024_D020_Id						= IF( '{$dados_up[16]}'='' OR '{$dados_up[16]}'<0 OR '{$dados_up[16]}'=D024_D020_Id,D024_D020_Id ,'{$dados_up[16]}' ),
                        D024_D018_Id						= IF( '{$dados_up[17]}'='' OR '{$dados_up[17]}'<0 OR '{$dados_up[17]}'=D024_D018_Id,D024_D018_Id ,'{$dados_up[17]}' ),
                        D024_D030_Id						= IF( '{$dados_up[18]}'='' OR '{$dados_up[18]}'<0 OR '{$dados_up[18]}'=D024_D030_Id,D024_D030_Id ,'{$dados_up[18]}' ),
                        D024_Flag_Lista_Preco				= '1',
                        D024_Flag_Cliente					= 'S',
                        D024_Flag_Fornecedor				= 'N',
                        D024_Flag_Revenda_Consumidor		= '1',
                        D024_Flag_Pre_Cadastro				= 'N',
                        D024_Data_Cadastro					= D024_Data_Cadastro,
                        D024_DDD_Telefone_1					= IF( '{$dados_up[25]}'='' OR '{$dados_up[25]}'<0 OR '{$dados_up[25]}'=D024_DDD_Telefone_1,D024_DDD_Telefone_1 ,'{$dados_up[25]}' ),
                        D024_DDD_Telefone_2					= IF( '{$dados_up[26]}'='' OR '{$dados_up[26]}'<0 OR '{$dados_up[26]}'=D024_DDD_Telefone_2,D024_DDD_Telefone_2 ,'{$dados_up[26]}' ),
                        D024_Telefone_1						= IF( '{$dados_up[27]}'='' OR '{$dados_up[27]}'<0 OR '{$dados_up[27]}'=D024_Telefone_1,D024_Telefone_1 ,'{$dados_up[27]}' ),
                        D024_Telefone_2						= IF( '{$dados_up[28]}'='' OR '{$dados_up[28]}'<0 OR '{$dados_up[28]}'=D024_Telefone_2,D024_Telefone_2 ,'{$dados_up[28]}' ),
                        D024_E_Mail							= IF( '{$dados_up[29]}'='' OR '{$dados_up[29]}'<0 OR '{$dados_up[29]}'=D024_E_Mail,D024_E_Mail ,'{$dados_up[29]}' ),
                        D024_D020_Entrega					= IF( '{$dados_up[30]}'='' OR '{$dados_up[30]}'<0 OR '{$dados_up[30]}'=D024_D020_Entrega,D024_D020_Entrega ,'{$dados_up[30]}' ),
                        D024_D018_Entrega					= IF( '{$dados_up[31]}'='' OR '{$dados_up[31]}'<0 OR '{$dados_up[31]}'=D024_D018_Entrega,D024_D018_Entrega ,'{$dados_up[31]}' ),
                        D024_D030_Entrega					= IF( '{$dados_up[32]}'='' OR '{$dados_up[32]}'<0 OR '{$dados_up[32]}'=D024_D030_Entrega,D024_D030_Entrega ,'{$dados_up[32]}' ),
                        D024_Entrega_Rua					= IF( '{$dados_up[33]}'='' OR '{$dados_up[33]}'<0 OR '{$dados_up[33]}'=D024_Entrega_Rua,D024_Entrega_Rua ,'{$dados_up[33]}' ),
                        D024_Entrega_Complemento			= IF( '{$dados_up[34]}'='' OR '{$dados_up[34]}'<0 OR '{$dados_up[34]}'=D024_Entrega_Complemento,D024_Entrega_Complemento ,'{$dados_up[34]}' ),
                        D024_Entrega_Bairro					= IF( '{$dados_up[35]}'='' OR '{$dados_up[35]}'<0 OR '{$dados_up[35]}'=D024_Entrega_Bairro,D024_Entrega_Bairro ,'{$dados_up[35]}' ),
                        D024_Entrega_Cep					= IF( '{$dados_up[36]}'='' OR '{$dados_up[36]}'<0 OR '{$dados_up[36]}'=D024_Entrega_Cep,D024_Entrega_Cep ,'{$dados_up[36]}' ),
                        D024_Entrega_Telefone				= IF( '{$dados_up[37]}'='' OR '{$dados_up[37]}'<0 OR '{$dados_up[37]}'=D024_Entrega_Telefone,D024_Entrega_Telefone ,'{$dados_up[37]}' ),
                        D024_DDD_Entrega_Telefone			= IF( '{$dados_up[38]}'='' OR '{$dados_up[38]}'<0 OR '{$dados_up[38]}'=D024_DDD_Entrega_Telefone,D024_DDD_Entrega_Telefone ,'{$dados_up[38]}' )
                        {$where}
    	");
       
					  
        $cErro = mysql_error();
        if (!empty($cErro))
        {
        	#mysql_query("ROLLBACK");
			echo '<br>Erro ao cadastrar o cliente '.$nome.'<br>'.$cErro.'<br>';
            file_put_contents('/tmp/impNota_ERRO_'.$dom->getElementsByTagName( $export['pedido']['idPedido'] )->item(0)->nodeValue.'_CadastroCliente', $xml_cliente);
			//exit;
        }
            
        /* Retorna o id do cliente */
        $sql = mysql_query("SELECT D024_Id FROM D024 WHERE D024_Cpf='{$cpf}' AND D024_Cnpj='{$cnpj}'");
        $open = mysql_fetch_assoc($sql);
        return $open['D024_Id'];
   }

        
     
        
        
        
        
   /*
   * Funcao que cadastra um cliente, e retorna o ID do cliente para a funcao search_cliente
   * Recebe como parametros:
   *      $xml_cliente       -> E o XML total do pedido, serve para pegar os dados do cliente e para caso aconteca algum erro
   *      $cpf_cnpj          -> E o numero do CPF ou CNPJ
   */
   private function cadastro_client( $xml_cliente, $cpf_cnpj )
   {
   		include(self::$xml_tags);

   	    echo "<br>cadastra cliente"; 
		
   		$dom = new \DOMDocument('1.0', 'iso-8859-1');
        $dom->loadXML( $xml_cliente );
		
        /*NORMAS DO CPF_CNPJ*/
        if( strlen( $cpf_cnpj ) == 11 )
        {
        	$cpf = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
        	$cpf = substr( $cpf, 0, 3 ).".".substr( $cpf, 3, 3 ).".".substr( $cpf, 6, 3 )."-".substr( $cpf, 9, 2 );
			$cnpj = '';
			$pessoa_fisica_juridica = 'F';
        }
        elseif( strlen( $cpf_cnpj ) == 14 )
        {
        	$cnpj = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
	    	$cnpj = substr( $cnpj, 0, 2 ).".".substr( $cnpj, 2, 3 ).".".substr( $cnpj, 5, 3 )."/".substr( $cnpj, 8, 4 )."-".substr( $cnpj, 12, 2 );
			$cpf = '';
			$pessoa_fisica_juridica = 'J';
        }
            
            
        /* VERIFICA SE E RG OU InscrisaoEstadual */
        if( $dom->getElementsByTagName( $export['cliente']['rg_ie'] )->item(0)->nodeValue == 'RG' )
        {
        	$rg = $dom->getElementsByTagName( $export['cliente']['documento'] )->item(0)->nodeValue;
            $ie = '';
		}
        else
        {
			$rg = '';
			$ie = $dom->getElementsByTagName( $export['cliente']['documento'] )->item(0)->nodeValue;
        }
			
			
        /* @TELEFONE */
        /* Coloca nas normas o Telefone e o DDD do cadastro do cliente */
        $ddd = substr( $dom->getElementsByTagName( $export['cliente']['telefone1'] )->item(0)->nodeValue, 0, 2);
        $telefone = substr( $dom->getElementsByTagName( $export['cliente']['telefone1'] )->item(0)->nodeValue, 2);
        $ddd2 = substr( $dom->getElementsByTagName( $export['cliente']['telefone2'] )->item(0)->nodeValue, 0, 2);
        $telefone2 = substr( $dom->getElementsByTagName( $export['cliente']['telefone2'] )->item(0)->nodeValue, 2);
			
        /* Coloca nas normas o Telefone e o DDD PARA A ENTREGA */
        $ddd_entrega = substr( $dom->getElementsByTagName( $export['pedido']['entrega']['telefone'] )->item(0)->nodeValue, 0, 2);
        $telefone_entrega = substr( $dom->getElementsByTagName( $export['pedido']['entrega']['telefone'] )->item(0)->nodeValue, 2);
		
		
		
		
        /* Cidade,estado,pais no cadastro do cliente */
        $sql = mysql_query("SELECT D018_Id FROM d018 WHERE d018_UF='".utf8_decode($dom->getElementsByTagName( $export['cliente']['estado'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado}' AND D020_Nome_Cidade='".utf8_decode($dom->getElementsByTagName( $export['cliente']['cidade'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $cidade = $open['D020_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);		
        $pais = $open['d030_Id'];
        unset($open);
		
		
		
		
        /* Cidade,estado,pais PARA A ENTREGA */
        $sql = mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF='".utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['estado'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado_entrega = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado_entrega}' AND D020_Nome_Cidade='".utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['cidade'] )->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $cidade_entrega = $open['D020_Id'];
        unset($open);
	
    	$sql = mysql_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);		
        $pais_entrega = $open['d030_Id'];
        unset($open);
            
        $nome = utf8_decode($dom->getElementsByTagName( $export['cliente']['nome'] )->item(0)->nodeValue);
        $nome_fantasia = utf8_decode($dom->getElementsByTagName( $export['cliente']['nomeFantasia'] )->item(0)->nodeValue);
        $nome_fantasia = ($nome_fantasia=='' OR $nome_fantasia==NULL) ? $nome : $nome_fantasia;
	

		
        mysql_query("
					INSERT INTO D024(
                    	D024_Flag_Pessoa_Fisica_Juridica,
                        D024_Nome_Empresa,
                        D024_Nome_Fantasia,
                        D024_Flag_Contribuinte,
                        D024_Flag_Restricao,
                        D024_Flag_Liberado,
                        D024_Cnpj,
                        D024_Cpf,
                        D024_Rg,
                        D024_Inscricao_Estadual,
                        D024_Endereco,
                        D024_Numero,
                        D024_Complemento,
                        D024_Bairro,
                        D024_Cep,
                        D024_C004_Id,
                        D024_D020_Id,
                        D024_D018_Id,
                        D024_D030_Id,
                        D024_Flag_Lista_Preco,
                        D024_Flag_Cliente,
                        D024_Flag_Fornecedor,
                        D024_Flag_Revenda_Consumidor,
                        D024_Flag_Pre_Cadastro,
                        D024_Data_Cadastro,
                        D024_DDD_Telefone_1,
                        D024_DDD_Telefone_2,
                        D024_Telefone_1,
                        D024_Telefone_2,
                        D024_E_Mail,
                        D024_D020_Entrega,
                        D024_D018_Entrega,
                        D024_D030_Entrega,
                        D024_Entrega_Rua,
                        D024_Entrega_Complemento,
                        D024_Entrega_Bairro,
                        D024_Entrega_Cep,
                        D024_Entrega_Telefone,
                        D024_DDD_Entrega_Telefone
					) VALUES(
                    	'".$pessoa_fisica_juridica."',
                        '".strtoupper($nome)."',
                        '".strtoupper($nome_fantasia)."',
                        '1',
                        'N',
                        'S',
                        '".$cnpj."',
                        '".$cpf."',
                        '".$rg."',
                        '".$ie."',
                        '".strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['endereco'] )->item(0)->nodeValue))."',
                        '".$dom->getElementsByTagName( $export['cliente']['numero'] )->item(0)->nodeValue."',
                        '".strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['complemento'] )->item(0)->nodeValue))."',
                        '".strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['bairro'] )->item(0)->nodeValue))."',
                        '".$dom->getElementsByTagName( $export['cliente']['cep'] )->item(0)->nodeValue."',
                        '".self::$empresa_atual."',
                        '".$cidade."',
                        '".$estado."',
                        '".$pais."',
                        '1',
                        'S',
                        'N',
                        '1',
                        'N',
                        now(),
                        '".$ddd."',
                        '".$ddd2."',
                        '".$telefone."',
                        '".$telefone2."',
                        '".strtoupper(utf8_decode($dom->getElementsByTagName( $export['cliente']['email'] )->item(0)->nodeValue))."',
                        '".$cidade_entrega."',
                        '".$estado_entrega."',
                        '".$pais_entrega."',
                        '".strtoupper(utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['endereco'] )->item(0)->nodeValue))."',
                        '".strtoupper(utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['complemento'] )->item(0)->nodeValue))."',
                        '".strtoupper(utf8_decode($dom->getElementsByTagName( $export['pedido']['entrega']['bairro'] )->item(0)->nodeValue))."',
                        '".$dom->getElementsByTagName( $export['pedido']['entrega']['cep'] )->item(0)->nodeValue."',
                        '".$telefone_entrega."',
                        '".$ddd_entrega."'
					)
			");
					  
        $cErro = mysql_error();
        if (!empty($cErro))
        {
        	#mysql_query("ROLLBACK");
			echo 'Erro ao cadastrar o cliente '.$nome.'';
            file_put_contents('/tmp/impNota_ERRO_'.$dom->getElementsByTagName( $export['pedido']['idPedido'] )->item(0)->nodeValue.'_CadastroCliente', $xml_cliente);
			//exit;
        }
            
        /* Retorna o id do cliente */
        $sql = mysql_query("SELECT D024_Id FROM D024 WHERE D024_Cpf='{$cpf}' AND D024_Cnpj='{$cnpj}'");
        $open = mysql_fetch_assoc($sql);
        return $open['D024_Id'];	
   }

        
        
        
        
        
   /*
   * Funcao que atualiza as aliquotas
   * Recebe como parametro:
   *      $T007_Id       -> O id do cabecalho
   */
   private function atualizarAliquotas($T007_Id)
   {
   		echo "<br>atualiza aliquotas $T007_Id"; 

        $Empresa_Atual = self::$empresa_atual;
        $cSql_T008 = mysql_query("
        						SELECT 
                                	D009_IPI(T007_C004_Id,T008_D009_Id,T007_D024_Id,T008_D006_Id,1,T007_Flag_ACP) AS T008_Aliquota_IPI_Correto,
                                    D009_IPI(T007_C004_Id,T008_D009_Id,T007_D024_Id,T008_D006_Id,2,T007_Flag_ACP)  AS T008_Percentual_Reducao_IPI_Correto,
                                    D009_IPV_ICMS(T007_C004_Id,T007_D024_Id,T008_D009_Id,T008_D006_Id,D020_D018_Id,T007_Flag_Revenda_Consumidor,T007_Flag_ACP,2) AS T008_Aliquota_ICMS_Correto,
                                    D009_IPV_ICMS(T007_C004_Id,T007_D024_Id,T008_D009_Id,T008_D006_Id,D020_D018_Id,T007_Flag_Revenda_Consumidor,T007_Flag_ACP,3) AS T008_Percentual_Reducao_ICMS_Correto,
                                    T008_Id,
                                    D049_Flag_Substituicao_Tributaria,
                                    D005_Id,
									D005_Classificacao_Fiscal,
									D149_Flag_ST,
									D149_Valor_VA,
									D053_ST_VA,
									D053_ST_VA_2,
									D059_Codigo_ST,
									D059_Id,
									D088_Codigo_ST,
									T007_Flag_Revenda_Consumidor,
									D024_Situacao_Tributaria_PIS_COFINS,
									D024_Flag_Isento_PIS_COFINS,
									D006_Flag_Substituicao_Tributaria,
									T007_Flag_Acp,
									D006_Flag_ST_VA
                                FROM T007
									LEFT JOIN T008 ON T007_Id=T008_T007_Id
									LEFT JOIN D024 ON D024_Id=T007_D024_Id
									LEFT JOIN D020 ON D020_Id=D024_D020_Id
									LEFT JOIN D009 ON D009_Id=T008_D009_Id
									LEFT JOIN D049 ON D049_Id=D009_D049_Id
									LEFT JOIN D001 ON D001_Id=D049_D001_Id
									LEFT JOIN D006 ON D006_Id=T008_D006_Id
									LEFT JOIN D005 ON D005_Id=D009_D005_Id(D009_Id)
                                    LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id='{$Empresa_Atual}'
									LEFT JOIN D088 ON D088_Id=if(D024_D088_Id>0,D024_D088_Id,D149_D088_Id)
									LEFT JOIN D059 ON D059_Id=if(D024_D059_Id>0,D024_D059_Id,D006_D059_Id)
									LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id
									AND D053_C004_Id='$Empresa_Atual'
                                WHERE T007_Id='$T007_Id'
		");


        while( $mT008 = mysql_fetch_array($cSql_T008) )
        {
        	if( $mT008['D149_Valor_VA'] <= 0 )
			{
            	if( $mT008['T007_Flag_Revenda_Consumidor'] == '1' )
					$mT008['D149_Valor_VA'] = $mT008['D053_ST_VA'];
                else
					$mT008['D149_Valor_VA'] = $mT008['D053_ST_VA_2'];
			}
			if( $mT008['D006_Flag_ST_VA'] == "N" )
            	$mT008['D149_Valor_VA'] = 0;
	
			if( ($mT008['T007_Flag_Acp']!='1' AND $mT008['T007_Flag_Acp']!='6') OR 
            	($mT008['D049_Flag_Substituicao_Tributaria']!='S') OR 
                ($mT008['D006_Flag_Substituicao_Tributaria']!='S') )
			{
            	$mT008['D149_Flag_ST']="N";
                $mT008['D149_Valor_VA']=0;
			}
	
			$cClausula = "
            			UPDATE T008
                        	LEFT JOIN D006 ON D006_Id=T008_D006_Id
	        				LEFT JOIN D059 ON D059_Id=D006_D059_Id
	        				LEFT JOIN T007 ON T007_Id=T008_T007_Id
	        				LEFT JOIN C004 ON C004_Id=T007_C004_Id
                        SET
	        			T008_Aliquota_IPI='".$mT008['T008_Aliquota_IPI_Correto']."',
	        			T008_Percentual_Reducao_IPI='".$mT008['T008_Percentual_Reducao_IPI_Correto']."',
	        			T008_Situacao_Tributaria_IPI='".$mT008['D088_Codigo_ST']."', 
	        			T008_Aliquota_ICMS='".$mT008['T008_Aliquota_ICMS_Correto']."',
	        			T008_Percentual_Reducao_ICMS='".$mT008['T008_Percentual_Reducao_ICMS_Correto']."',
	        			T008_Flag_ST=D006_Flag_Substituicao_Tributaria,
	        			T008_D005_Id='".$mT008['D005_Id']."',
	        			T008_D059_Id='".$mT008['D059_Id']."',
	        			T008_ST_VA='".$mT008['D149_Valor_VA']."',
            ";
			if( !empty( $mT008['D024_Situacao_Tributaria_PIS_COFINS'] ) )
            {
    			$cClausula .= "
								T008_Situacao_Tributaria_PIS='".$mT008['D024_Situacao_Tributaria_PIS_COFINS']."',
								T008_Situacao_Tributaria_COFINS='".$mT008['D024_Situacao_Tributaria_PIS_COFINS']."',
				";
                if( $mT008['D024_Flag_Isento_PIS_COFINS'] == 'S' )
                	$cClausula .= "
                                	T008_Flag_Isento_PIS='".$mT008['D024_Flag_Isento_PIS_COFINS']."',
									T008_Aliquota_PIS='0',
									T008_Flag_Isento_COFINS='".$mT008['D024_Flag_Isento_PIS_COFINS']."',
									T008_Aliquota_COFINS='0',
                    ";
                else
                	$cClausula .= "
									T008_Flag_Isento_PIS=D006_Flag_Isento_PIS,
									T008_Aliquota_PIS=C004_Percentual_PIS,
									T008_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,
									T008_Aliquota_COFINS=C004_Percentual_COFINS,		
					";
			}else
            	$cClausula .= "
                				T008_Flag_Isento_PIS=D006_Flag_Isento_PIS,
	        					T008_Aliquota_PIS=C004_Percentual_PIS,
	        					T008_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,
	        					T008_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,
	        					T008_Aliquota_COFINS=C004_Percentual_COFINS,
	        					T008_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS,
                ";

			$cClausula .="
            				T008_Classificacao_Fiscal='".$mT008['D005_Classificacao_Fiscal']."',
	        				T008_Flag_Situacao_Tributaria=concat('0','".$mT008['D059_Codigo_ST'].")
						WHERE T008_Id='".$mT008['T008_Id']."'
	        ";
	
	        mysql_query($cClausula);
	          
	        mysql_query("call T008_Gravar_Totalizacao_4('".$mT008['T008_Id']."')");
            }/* Fim while */
	
            $cSql_T007 = mysql_query("
            			     SELECT
							 	D009_IPV_ICMS(T007_C004_Id,T007_D024_Id,0,T007_D006_Id,D020_D018_Id,T007_Flag_Revenda_Consumidor,T007_Percentual_Comissao,T007_Flag_ACP,2)
                                            AS T007_Aliquota_ICMS_Correto,
								D009_IPV_ICMS(T007_C004_Id,T007_D024_Id,0,T007_D006_Id,D020_D018_Id,T007_Flag_Revenda_Consumidor,T007_Percentual_Comissao,T007_Flag_ACP,3)
                                            AS T007_Percentual_Reducao_ICMS_Correto
                             FROM T007
							 	LEFT JOIN D024 ON D024_Id=T007_D024_Id
								LEFT JOIN D020 ON D020_Id=D024_D020_Id
                             WHERE T007_Id='".$T007_Id."'
							");
            $mT007 = mysql_fetch_array($cSql_T007);
	
            mysql_query("UPDATE T007
                            SET
								T007_Aliquota_ICMS='".$mT007['T007_Aliquota_ICMS_Correto']."',
								T007_Percentual_Reducao_ICMS='".$mT007['T007_Percentual_Reducao_ICMS_Correto']."'
                            WHERE T007_Id='$T007_Id'");	
		
            mysql_query("call T007_Gravar_Totalizacao_4('$T007_Id')");

   }

   public function preCadastroProduto($item){
        global $g,$D009_Id_Cotacao;
        require_once("bibliotecas/classes/CAD002.php");

        $precoTabelado = $g['conf']->ini('precoTabelado');

        $descricao      = utf8_decode($item['descricao']);
        $codigo_Produto = utf8_decode($item['codigo']);
        $preco          = utf8_decode($item['valorUnitario']);

        $D001 = <<<EOT
        INSERT INTO D001 (
            D001_Codigo_Produto,
            D001_Flag_Pre_Cadastro,
            D001_Descricao_Produto,
            D001_Flag_Tipo
        ) VALUES (
            '{$codigo_Produto}',
            'S',
            '{$descricao}',
            'COMPRADO'
        );
EOT;
        //echo "<br /><br />D001: {$D001}<br /><br />";
        $resultD001 = mysql_query($D001);
        $D001_Id=$g['mysqlLastId'];

        $D049=mysql_query("INSERT INTO D049 (D049_D001_Id, D049_D082_Id, D049_Data_Cadastro, D049_Flag_Tipo) VALUES ('{$D001_Id}', 1, CURDATE(), 'CP');");
        $D049_Id=$g['mysqlLastId'];

        $CAD002 = new CAD002();
        $CAD002->preCadastroMarca($D049_Id,$D001_Id,$preco,$precoTabelado);

        if(isset($D009_Id_Cotacao)){
            mysql_query("UPDATE D009 set D009_Preco_1='{$preco}', D009_Flag_Preco_Tabelado='{$precoTabelado}', where D009_Id='{$D009_Id_Cotacao}'");
        }

        echo "<br />D009_Id: ".$D009_Id_Cotacao."<br />";
        return $D009_Id_Cotacao;

    }
	
}
?>
