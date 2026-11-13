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
    public $dom;
    public $pedidos;
    public $filtro;
	
	public function __construct(){
        global $g;
        //echo 'bibliotecas/webservices/webStorm/webStorm.tags.php';
		if(file_exists('bibliotecas/webservices/webStorm/webStorm.tags.php')){
			/* Arquivo que contem as tags utilizadas para listar o XML */
			self::$xml_tags = 'bibliotecas/webservices/webStorm/webStorm.tags.php';
            // bibliotecas/webservices/sis/'.$ecommerce.'.tags.php
		}else{
            echo '<b>Tags</b> do webservice webStorm nao encontrada.';
			exit;
		}
		
		if (!empty($g['empresaAtual'])){
			self::$empresa_atual = $g['empresaAtual'];
		}elseif(!empty($_GET['p'])){
            self::$empresa_atual = $_GET['p'];
        }else{
	        self::$empresa_atual = 1;
            file_put_contents('/tmp/webStorm_ERRO_EMPRESA_EMPTY', self::$empresa_atual);
        }
	}
        
    /*
     * E a funcao de inicializacao da Classe, chamada no documento que recebe o POST
     * Recebe como parametro:
     *      $dados            -> E o XML total do pedido, recebido via POST do ecommerce
	*/
	public function comeco($dados)
	{
        global $g;
        #$xml = file_get_contets($dados['xml']);
        //print_r($dados['XML']);
        echo "<br>iniciando a leitura do xml";
        $xml = $dados['XML'];    /* NOME DO CAMPO PELO QUAL FOI ENVIADO O XML VINDA DO ECOMMERCE SIS */
        $xml = utf8_encode($xml);

        /* Caso a requisicao tenha dado certo, retornado um xml valido */
        $this->dom = new \DomDocument();
        $this->dom->loadXML($xml);
        $domPatch = new \DomXPath($this->dom);
        
        //include(self::$xml_tags);

   		$filtro = $this->dom->getElementsByTagName('ws_integracao')->item(0);
        // ************ VERIFICAR O QUE É ISSO
        if($domPatch->query(".//dataPedido", $filtro)->length){
        	echo "<br>processar o pedido";
        	$this->processa_pedido( $xml );
        }else{
            echo '<br>Nenhum resultado do Webservice [pedido]';
        }
   }

   /*
   * Funcao que processa todo o pedido, chama as outras funcoes para criar cabecalho,cliente ...
   * Recebe como parametro:
   *      $xml            -> E o XML total do pedido, enviado pela funcao 'comeco'
   */
   private function processa_pedido($xml)
   {
        global $g;

	    echo "<br>inicio processar o pedido"; 
		
        $i=0;

        $total_pedidos=0;
        $idFiltro=1;

        // foreach ($this->dom->getElementsByTagName('*') as $key => $pedido) {

        //     if (!preg_match('#^pedido-[0-9]+$#', $pedido->nodeName)) { continue; }
        //     //$this->pedido = $pedido;
        //     $idPedido = $pedido->getElementsByTagName('idPedido')->item(0)->nodeValue;

        //     echo "<br /><br /><Br />idPedido->".$pedido->getElementsByTagName('idPedido')->item(0)->nodeValue."<br />";

        //     foreach($pedido->getElementsByTagName('*') as $key => $produto){
        //         if (!preg_match('#^produto-[0-9]+$#', $produto->nodeName)) { continue; }
        //         echo "idProduto->".$produto->getElementsByTagName('idProduto')->item(0)->nodeValue."<br />";
        //         echo "idProduto->idItemVenda->".$produto->getElementsByTagName('idItemVenda')->item(0)->nodeValue."<br /><br />";
        //     }

        //     echo "------------------------------  Fim pedido<br/>";
        // }  
        // die("Alaor");

        // loop em todos os pedidos
        foreach ($this->dom->getElementsByTagName('*') as $key => $pedido) {
            if (!preg_match('#^pedido-[0-9]+$#', $pedido->nodeName)) { continue; }
        	$this->pedido = $pedido;
            //include(self::$xml_tags);
            set_time_limit(0);
            echo "<br>loop nos pedidos";
            echo "AAA->".$pedido->getElementsByTagName('idPedido')->item(0)->nodeValue."<br />";

			mysql_query("BEGIN");
			$idPedido = $pedido->getElementsByTagName('idPedido')->item(0)->nodeValue;
			$dataPedido = $pedido->getElementsByTagName('dataPedido')->item(0)->nodeValue;
            //$desconto = str_replace(',','.',$pedido->getElementsByTagName('discount_amount')->item(0)->nodeValue);
			$desconto = '';
            //$icmsArt21 = str_replace(',','.',$pedido->getElementsByTagName('tax_amount')->item(0)->nodeValue);
            $icmsArt21 = "";
            //$observacao_t005 = "Descrição Frete: ". utf8_decode($pedido->getElementsByTagName('shipping_description')->item(0)->nodeValue);
			$observacao_t005 = "";
			//$frete = str_replace(',','.',$pedido->getElementsByTagName( $export['pedido']['frete'] )->item(0)->nodeValue);
			$frete = 0;
			
			#$valor_total_produtos = $pedido->getElementsByTagName( $export['pedido']['valorTotalPedido'] )->item(0)->nodeValue;
			$valor_total_produtos=0;
			
			echo "<br>Criado log do XML";
        	file_put_contents('/tmp/LOG_TUDO_'.$idPedido.'_'.date("d-m-Y"), $xml);
			
            $teste=1;
            // loop em todos os produtos do pedido
			foreach($pedido->getElementsByTagName('*') as $key => $produto){
                if (!preg_match('#^produto-[0-9]+$#', $produto->nodeName)) { continue; }
				$valor_total_produtos += 
                str_replace(',','.',$produto->getElementsByTagName('quantidade')->item(0)->nodeValue) 
                * 
                str_replace(',','.',$produto->getElementsByTagName('valorUnitario')->item(0)->nodeValue);
                $teste++;
			}
            //die($export['pedido']['cpf_cnpj'] . "AAAAAAAA");
			/* Verifica a existencia do cliente */
            $cpf = $pedido->getElementsByTagName('cpf_cnpj')->item(0)->nodeValue;
			$idCliente = $this->search_client($xml, $cpf);

            require_once("bibliotecas/classes/VEN001.php");
            $VEN001 = new VEN001();

            $D006_Id=$VEN001->retornaCFOP($idCliente,false,1);
		
        	/* Array a ser passado para criar o scabecalho do pedido */
			$to_cabecalho = array(
                              'idCliente'=>$idCliente,
                              'idPedido'=>trim($idPedido),
                              'dataPedido'=>$dataPedido,
                              'desconto'=>$desconto,
                              'frete'=>$frete,
                              'valor_total_produtos'=>$valor_total_produtos,
                              'CFOP'=>$D006_Id,
                              'icmsArt21'=>$icmsArt21,
                              'observacao'=>$observacao_t005
		    );
								
			/* Verifica se o cabecalho com o numero do pedido vindo do ecommerce ja existe */
			/* Se nao existir cria e retorna o ID do T005, senao apenas retorna o ID do T005 */
			$sql = mysql_query("SELECT T005_Id FROM T005 WHERE T005_Numero_Pedido_Venda='".$to_cabecalho['idPedido']."'");
            //echo "SELECT T005_Id FROM T005 WHERE T005_Numero_Pedido_Venda='".$to_cabecalho['idPedido']."'";
			if(mysql_num_rows($sql)==0)
			{
				$idCabecalho = $this->cria_cabecalho_pedido($xml,$to_cabecalho);
                $T005=mysql_query("select (T005_Valor_Desconto/T005_Valor_Total_Produtos) as desconto from T005 where T005_Id='$idCabecalho'");
                $mT005=mysql_fetch_array($T005);
				$item_numero=0;
				foreach($pedido->getElementsByTagName('*') as $key => $produto){
                    if (!preg_match('#^produto-[0-9]+$#', $produto->nodeName)) { continue; }
                    //echo "LOOPING PEDIDOS: " .$item_numero;
                	$item = array(
					            'idProduto'=>$produto->getElementsByTagName('idProduto')->item(0)->nodeValue,
					            'idPedido'=>$idPedido,
					            'codigo'=>$produto->getElementsByTagName('idProduto')->item(0)->nodeValue,
					            /* Retirado a descricao, para caso seja um KIT, coloca uma */
                                //'descricao'=>$produto->getElementsByTagName('name')->item(0)->nodeValue,
                                'descricao'=>'',
                                //'descricao'=>'',
                                'quantidade'=>str_replace(',','.',$produto->getElementsByTagName('quantidade')->item(0)->nodeValue),
					            'valorUnitario'=>str_replace(',','.',$produto->getElementsByTagName('valorUnitario')->item(0)->nodeValue),
                                //'T006_Percentual_Desconto'=>str_replace(',','.',$produto->getElementsByTagName('discount_percent')->item(0)->nodeValue),
                                'T006_Percentual_Desconto'=>'',
					            'idCabecalho'=>$idCabecalho,
					            'idKit'=>'',
					            'type'=>'NORMAL',
					            'CFOP'=>$CFOP,
                                //'peso'=>$produto->getElementsByTagName('weight')->item(0)->nodeValue,
                                'peso'=>'',
                                //'especificacoes'=>$produto->getElementsByTagName('sku')->item(0)->nodeValue,
                                'especificacoes'=>'',
					);
                    $item_numero++;
                    $this->add_produto_pedido($xml, $item, $item_numero, $idCliente, $mT005['desconto']);
				}
				$this->atualizarAliquotas($idCabecalho); 
				$total_pedidos++;

                if (!is_dir("{$g['pathDados']}/ecommerce/xmlPedido/")) {
                    mkdir("{$g['pathDados']}/ecommerce/xmlPedido/", 0777,true);
                }

                $cArquivoSalvar = "{$g['pathDados']}/ecommerce/xmlPedido/PEDIDO_{$idCabecalho}.xml";

                $fp = fopen($cArquivoSalvar, "a");
                if (!@fwrite($fp,"$xml"))
                { 
                    mysql_query("ROLLBACK");
                    return "Não foi possível continuar, o arquivo:" . $cArquivoSalvar . "Não pode ser salvo. Verique as permissões na pasta do arquivo";
                }
			}
			/* Atualizando aliquotas */
   			echo '<br>'.$idCabecalho.'<br>';
   			require_once("bibliotecas/classes/VEN002.php");
   			$VEN002 = new VEN002();
   			$retorno = $VEN002->atualizarAliquotasPedidos($idCabecalho);

            mysql_query("call T005_Gravar_Totalizacao_3('$idCabecalho')");
		
			if ($retorno === true) {
				echo $resposta['code'] = true;
				echo $resposta['data'] = "Atualizado!";
			} else {
				// excluir
				echo $resposta['code'] = false;
				echo $resposta['data'] = $retorno;
			}
			/* Fim atualizar aliquotas */
			$i++;
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
   private function add_produto_pedido( $xml, $item, $item_numero, $idCliente, $desconto)
   {
   	   	require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
   		echo "<br>inicio add produto pedido:\n<br />"; 
        //print_r($item);
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
                
        if(mysql_num_rows($sql)==0) {
			//mysql_query("ROLLBACK");
			echo '<br>Nao existe o produto "'.$item['codigo'].'" cadastrado no Sistema Hardness.<br>idPedido vindo do webservice "'.$item['idPedido'].'"';
            file_put_contents('/tmp/webStorm_ERRO_'.$item['idPedido'].'_ProdutoInesistente', $xml);
            // $descricao_produto = $this->pedido->getElementsByTagName("items")->item(0)->getElementsByTagName("item")->item(0)->getElementsByTagName("name")->item(0)->nodeValue;
            // $produto = $this->pedido->getElementsByTagName("item")->item(0);
            // echo "<br />Pré cadastrando o produto<br />";
            // $mD009['D009_Id'] = $this->preCadastroProduto($item);
            exit;
        } else {
        	/* Verifica se e um KIT */
        	if($mD009['D049_Flag_Tipo'] == 'MT') {
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
        			
        			// Foi coocado fixo o lista preço porque no na inserção da T005 está inserindo o lista preço fixo
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
					$this->add_produto_pedido($xml,$kit_itens,$kit_item_numero, $idCliente, $desconto);
        		}
        		return false;
        	}
        	$descricao_produto = ( !empty($mD009['D001_Descricao_Produto']) AND $item['type']=='NORMAL' ) ? $mD009['D001_Descricao_Produto'] : $item['descricao'];
        	$id_kit = empty($item['idKit']) ? '0' : $item['idKit'];

            //Verifica se o campo especificacoes (SKU) está preenchido no cadastro do D001
            $sqlD001 = mysql_query("SELECT * FROM D001 WHERE LENGTH(D001_Especificacoes) > 0  AND D001_Codigo_Produto = '{$item['codigo']}'");
            if(mysql_num_rows($sqlD001) == 0){
                mysql_query("UPDATE D001 SET D001_Especificacoes = '{$item['especificacoes']}' WHERE D001_Codigo_Produto = '{$item['codigo']}'");
            }
        }

        $T006_Valor_Preco_Sem_Desconto_Unitario = $item['valorUnitario'] - ($item['valorUnitario'] * ($item['T006_Percentual_Desconto']/100));

        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();
        $D006_Id=$VEN001->retornaCFOP($idCliente,$mD009['D009_Id'],1);
		
		self::$item_produto_count++;
        mysql_query("
                	INSERT INTO T006(
                            T006_T005_Id,
                            T006_D006_Id,
                            T006_D009_Id,
                            T006_C004_Id,
                            T006_D049_Id,
                            T006_D005_Id,
                            T006_D037_Id,
                            T006_Item,
                            T006_Codigo_Produto,
                            T006_Descricao_Produto,
                            T006_Quantidade,
                            T006_Valor_Preco_Original,
                            T006_Valor_Preco_Sem_Desconto_Unitario,
                            T006_Valor_Preco_Unitario,
                            T006_Percentual_Desconto,
                            T006_Marca,
                            T006_Flag_Status,
                            T006_D009_Id_Kit,
                            T006_Observacao
					) VALUES (
                            '".$item['idCabecalho']."',
                            '".$D006_Id."',
                            '".$mD009['D009_Id']."',
                            '".self::$empresa_atual."',
                            '".$mD009['D009_D049_Id']."',
                            '".$mD009['D001_D005_Id']."',
                            '".$mD009['D001_D037_Id']."',
                            '".self::$item_produto_count."',
                            '".$item['codigo']."',
                            '".$item['descricao']."',
                            '".$item['quantidade']."',
                            '".$item['valorUnitario']."',
                            '".$T006_Valor_Preco_Sem_Desconto_Unitario."',
                            '".$item['valorUnitario']."',
                            '".$item['T006_Percentual_Desconto']."',
                            '".$mD009['D082_Marca']."',
                            '',
                            '".$id_kit."',
                            '".$item['especificacoes']."'
					)
			");
        /* T006_Peso_Unitario */
					  
        $cErro = mysql_error();
        if (!empty($cErro))
        {
        	#mysql_query("ROLLBACK");
            echo 'Erro ao cadastrar o produto "'.$item['codigo'].'". idCabecalho="'.$item['idCabecalho'].'"';
            file_put_contents('/tmp/webStorm_ERRO_'.$item['idPedido'].'_CadastroProduto', $xml);
            //exit;    
        }     

        $T006_Id=mysql_insert_id();
 
        mysql_query("call T006_Gravar_Totalizacao_4('$T006_Id')");

   }

   /*
   * Funcao que cria o cabecalho do pedido, e retorna o ID do cabecalho
   * Recebe como parametros:
   *      $xml            -> E o XML total do pedido, para caso aconteca algum erro
   *      $content           -> Array com as informacoes para o cabecalho, e gerado pela funcao processa_pedidos
   */
   private function cria_cabecalho_pedido( $xml, $content )
   {
   		//include(self::$xml_tags);
		
   	    echo "<br>cria cabecalho pedido"; 
		
        $com_desconto = $content['valor_total_produtos'] - $content['desconto'];
        $percentual_desconto = (100 * $com_desconto) / $content['valor_total_produtos'];
        $percentual_desconto = 100 - $percentual_desconto;
		
        $valor_percentual_desconto = ($content['valor_total_produtos']*$percentual_desconto) / 100;
            
        $dataPedido = explode(" ", $content['dataPedido']);
        //echo "data pedido: ".$dataPedido[0];

        if ($content['desconto']<0){
            $content['desconto']=$content['desconto']*-1;
        }
            
        /* Verifica se existe o usuario ecommerce, se tiver pega o ID dele, senao joga o pedido para o user ROOT */
        $sql_user = mysql_query("SELECT C007_Id FROM c007 WHERE C007_Nome='ecommerce' OR C007_Primeiro_Nome='ecommerce'");
        if(mysql_num_rows($sql_user)){
        	$open = mysql_fetch_assoc($sql_user);
            $usuario = $open['C007_Id'];
        }else{
            $usuario = '63';
        }
        $totalPedido = ($this->pedido->getElementsByTagName('grand_total')->item(0)->nodeValue - $content['desconto']);
		// '".$content['observacao']."',
        mysql_query ("
			INSERT INTO T005(
                            T005_Numero_Pedido_Venda,
                            T005_Flag_Lista_Preco,
                            T005_Flag_Revenda_Consumidor,
                            T005_D024_Id,
                            T005_C004_Id,
                            T005_D006_Id,
                            T005_C007_Id_Vendedor_Interno,
                            T005_Data_Emissao,
                            T005_Data_Saida,
                            T005_Flag_ACP,
                            T005_Flag_Situacao,
                            T005_Valor_Desconto,
                            T005_Valor_Desconto_Manual,
                            T005_Percentual_Desconto,
                            T005_Valor_Percentual_Desconto,
                            T005_Valor_Total_Produtos,
                            T005_Valor_Total_Produtos_Sem_Desconto,
                            T005_Valor_Total,
                            T005_Flag_Travar_Pedido,
                            T005_Flag_Status,
                            T005_Flag_Expedicao,
                            T005_Valor_Frete,
                            T005_Flag_Frete,
                            T005_Valor_Entrada,
                            T005_D022_Id,
                            T005_Nome_Status
			        ) VALUES (
                            '".$content['idPedido']."',
                            '1',
                            '1',
                            '".$content['idCliente']."',
                            '".self::$empresa_atual."',
                            '".$content['CFOP']."',
                            '".$usuario."',
                            '".$dataPedido[0]."',
                            CURDATE(),
                            '6',
                            '1',
                            '".$content['desconto']."',
                            '0',
                            '0',
                            '".$valor_percentual_desconto."',
                            '".($content['valor_total_produtos']-$content['desconto'])."',
                            '".$content['valor_total_produtos']."',
                            '".$totalPedido."',
                            '2',
                            '0',
                            '2',
                            '".$this->pedido->getElementsByTagName('shipping_amount')->item(0)->nodeValue."',
                            '0',
                            '".$content['icmsArt21']."',
                            '1912',
                            'T005_Status_Pedido (T005_Flag_Status, 1)'
                    )
		");
		/*
        T005_Valor_Frete, -> '".$content['frete']."',
        T005_Peso_Total,
        T005_Nome_Transportadora,
        T005_Peso_Liquido,
        T005_Peso_Bruto,
        */
					  
        $cErro = mysql_error();
        if (!empty($cErro))
        {
        	//mysql_query("ROLLBACK");
   			echo '<br>Erro ao criar cabecalho do pedido. idPedido vindo do webservice "'.$content['idPedido'].'"'.'<br>'.$cErro;
            file_put_contents('/tmp/webStorm_ERRO_'.$content['idPedido'].'_CriandoCabecalho', $xml);
			//exit;
        }
        else
        {
        	  $sql = mysql_query("SELECT T005_Id FROM t005 WHERE T005_Numero_Pedido_Venda='".$content['idPedido']."'");
   			    $open = mysql_fetch_assoc($sql);
            //Cria histórico do pedido
            mysql_query("insert into T178 (T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ('{$open['T005_Id']}','{$usuario}',CURRENT_TIMESTAMP(),'Importado Ecommerce','')");
  
   			    return $open['T005_Id'];
        }
   }

   /*
   * Funcao que procura o cliente, se ja tiver ele cadastradado, atualiza os dados e retorna o ID
   * Se nao tiver o cliente cadastrado cadastra ele, e retorna o ID dele
   * Recebe como parametros:
   *      $xml            -> E o XML total do pedido, para caso aconteca algum erro
   *      $cpf_cnpj       -> E o numero do CPF ou CNPJ, para consultar a DB
   */
   private function search_client($xml, $cpf_cnpj){
    	$cpf = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
    	$cpf = substr( $cpf, 0, 3 ).".".substr( $cpf, 3, 3 ).".".substr( $cpf, 6, 3 )."-".substr( $cpf, 9, 2 );
    	
    	$sql = mysql_query("SELECT * FROM D024 WHERE D024_Cpf='{$cpf}'");

    	/* Se retornar 0, e que o cliente nao existe, entao cadastra o cliente */
    	if( mysql_num_rows( $sql ) == 0 ){	
        	return $this->cadastro_client($xml, $cpf_cnpj);
    	}else{
            /* Se nao for 0 retorna o id do cliente para ser usado no pedido */
    		return $this->atualiza_client($xml, $cpf_cnpj);
        	//$sql = mysql_query("SELECT d024_Id FROM d024 WHERE D024_Cpf='{$cpf}'");
        	//$open = mysql_fetch_assoc($sql);
            //return $open['d024_Id'];
    	}
   }

   /*
   * Funcao que atualiza todos os dados do cliente que JA ESTA CADASTRADO
   * Recebe como parametros:
   *      $xml_cliente       -> E o XML total do pedido, serve para pegar os dados do cliente e para caso aconteca algum erro
   *      $cpf_cnpj          -> E o numero do CPF ou CNPJ
   */
   private function atualiza_client( $xml_cliente, $cpf_cnpj )
   {
   	    echo "<br>Atualiza cadastro cliente"; 

        //include(self::$xml_tags);

        /*NORMAS DO CPF_CNPJ*/

    	$cpf = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
    	$cpf = substr( $cpf, 0, 3 ).".".substr( $cpf, 3, 3 ).".".substr( $cpf, 6, 3 )."-".substr( $cpf, 9, 2 );
		$cnpj = '';
		$pessoa_fisica_juridica = 'F';
        $where = " WHERE D024_Cpf='{$cpf}' ";

        /* VERIFICA SE E RG OU InscrisaoEstadual */
        if($export['cliente']['rg_ie'] == 'RG'){
        	$rg = '';
            $ie = '';
		} else {
			$rg = '';
			$ie = '';
        }

        /* @TELEFONE */
        /* Coloca nas normas o Telefone e o DDD do cadastro do cliente */
        $ddd = substr($this->pedido->getElementsByTagName('telefone')->item(0)->nodeValue, 1, 2);
        $telefone = substr($this->pedido->getElementsByTagName('telefone')->item(0)->nodeValue, 4);
        $telefone = str_replace("-", "", $telefone);
        $ddd2 = '';
        $telefone2 = '';
			
        /* Coloca nas normas o Telefone e o DDD PARA A ENTREGA */
        $ddd_entrega = substr($this->pedido->getElementsByTagName('telefone')->item(0)->nodeValue, 0, 2);
        $telefone_entrega = substr($this->pedido->getElementsByTagName('telephone')->item(0)->nodeValue, 2);

        $estado = $this->pedido->getElementsByTagName('estado')->item(0)->nodeValue;

        /* Cidade,estado,pais no cadastro do cliente */
        $sql = mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF LIKE '%".utf8_decode($estado)."%'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado}' AND D020_Nome_Cidade='".utf8_decode($this->pedido->getElementsByTagName('cidade')->item(0)->nodeValue)."'");
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
        $sql = mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF LIKE '%".utf8_decode($estado)."%'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado_entrega = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado_entrega}' AND D020_Nome_Cidade='".utf8_decode($this->pedido->getElementsByTagName('cidade')->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $cidade_entrega = $open['D020_Id'];
        unset($open);
	
    	$sql = mysql_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);		
        $pais_entrega = $open['d030_Id'];
        unset($open);
        $nomeCliente = $this->pedido->getElementsByTagName('nome')->item(0)->nodeValue;

        $nome = utf8_decode($nomeCliente);
        $nome_fantasia = utf8_decode($nomeCliente);
        $endereco = $this->pedido->getElementsByTagName('logradouro')->item(0)->nodeValue;
        $complemento="";
        $bairro="";
        $cep = $this->pedido->getElementsByTagName('cep')->item(0)->nodeValue;
        $email = $this->pedido->getElementsByTagName('email')->item(0)->nodeValue;
        $numero=0;
        $enderecoEntrega=$this->pedido->getElementsByTagName('logradouro')->item(0)->nodeValue;
        $complemento="";
        $bairro=$this->pedido->getElementsByTagName('bairro')->item(0)->nodeValue;
        $cep=$this->pedido->getElementsByTagName('cep')->item(0)->nodeValue;

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
                        strtoupper(utf8_decode($endereco)),
                        $numero,
                        strtoupper(utf8_decode($complemento)),
                        strtoupper(utf8_decode($bairro)),
                        $cep,
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
                        strtoupper(utf8_decode($email)),
                        $cidade_entrega,
                        $estado_entrega,
                        $pais_entrega,
                        strtoupper(utf8_decode($enderecoEntrega)),
                        strtoupper(utf8_decode($complemento)),
                        strtoupper(utf8_decode($bairro)),
                        $cep,
                        $telefone_entrega,
                        $ddd_entrega
    	);

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
            file_put_contents('/tmp/webStorm_ERRO_'.$this->pedido->getElementsByTagName('idPedido')->item(0)->nodeValue.'_CadastroCliente', $xml_cliente);
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
        global $g;

   	    echo "<br>cadastra cliente"; 
        /* Caso a requisicao tenha dado certo, retornado um xml valido */
        //include(self::$xml_tags);
        /*NORMAS DO CPF_CNPJ*/

    	$cpf = str_replace( array(".","/","-",","," "), "", $cpf_cnpj );
    	$cpf = substr( $cpf, 0, 3 ).".".substr( $cpf, 3, 3 ).".".substr( $cpf, 6, 3 )."-".substr( $cpf, 9, 2 );
		$cnpj = '';
		$pessoa_fisica_juridica = 'F';

        /* VERIFICA SE E RG OU InscrisaoEstadual */
        $export['cliente']['rg_ie']="";
        if($export['cliente']['rg_ie'])
        {
        	$rg ="";
            $ie = '';
		}
        else
        {
			$rg = '';
			$ie ="";
        }
			
        /* @TELEFONE */
        /* Coloca nas normas o Telefone e o DDD do cadastro do cliente */
        $ddd = substr($this->pedido->getElementsByTagName('telefone')->item(0)->nodeValue, 0, 2);
        $telefone = substr($this->pedido->getElementsByTagName('telefone')->item(0)->nodeValue, 2);
        $telefone = str_replace("-", "", $telefone);
        $telefone = trim($telefone);
        $telefone2 = '';
        $cidade=$this->pedido->getElementsByTagName('cidade')->item(0)->nodeValue;

			
        /* Coloca nas normas o Telefone e o DDD PARA A ENTREGA */
        $ddd_entrega = substr($ddd, 0, 2);
        $telefone_entrega = substr($telefone, 2);
        $estado = $this->pedido->getElementsByTagName('estado')->item(0)->nodeValue;

        /* Cidade,estado,pais no cadastro do cliente */
        $sql = mysql_query("SELECT D018_Id FROM d018 WHERE D018_UF LIKE '%".utf8_decode($estado)."%'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado}' AND D020_Nome_Cidade='".utf8_decode($cidade)."'");
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
        $sql = mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF='".utf8_decode($estado)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $estado_entrega = $open['D018_Id'];
        unset($open);
		
        $sql = mysql_query("SELECT D020_Id FROM D020 WHERE D020_D018_Id='{$estado_entrega}' AND D020_Nome_Cidade='".utf8_decode($this->pedido->getElementsByTagName('cidade')->item(0)->nodeValue)."'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $cidade_entrega = $open['D020_Id'];
        unset($open);
	
    	$sql = mysql_query("SELECT d030_Id FROM d030 WHERE D030_Nome_Pais='Brasil'");
        $open = mysql_fetch_assoc($sql);
        mysql_free_result($sql);
        $pais_entrega = $open['d030_Id'];
        unset($open);
            
        $nome = "";
        $nome_fantasia = $nome;
        $endereco = $this->pedido->getElementsByTagName('logradouro')->item(0)->nodeValue;
        $numero="";
        $bairro=$this->pedido->getElementsByTagName('bairro')->item(0)->nodeValue;
        $complemento="";
        $cep=$this->pedido->getElementsByTagName('cep')->item(0)->nodeValue;
        $email = $this->pedido->getElementsByTagName('email')->item(0)->nodeValue;
        $regiao = " Cidade: ".utf8_decode($this->pedido->getElementsByTagName('cidade')->item(0)->nodeValue) 
        . " Região: " . utf8_decode($this->pedido->getElementsByTagName('estado')->item(0)->nodeValue);
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
                        D024_DDD_Entrega_Telefone,
                        D024_Observacoes
					) VALUES (
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
                        '".strtoupper(utf8_decode($endereco))."',
                        '".$numero."',
                        '".strtoupper(utf8_decode($complemento))."',
                        '".strtoupper(utf8_decode($bairro))."',
                        '".$cep."',
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
                        '".strtoupper(utf8_decode($email))."',
                        '".$cidade_entrega."',
                        '".$estado_entrega."',
                        '".$pais_entrega."',
                        '".strtoupper(utf8_decode($endereco))."',
                        '".strtoupper(utf8_decode($complemento))."',
                        '".strtoupper(utf8_decode($bairro))."',
                        '".$cep."',
                        '".$telefone_entrega."',
                        '".$ddd_entrega."',
                        '".strtoupper(utf8_decode($endereco)).$regiao."'
					);
			");
					  
        $cErro = mysql_error();
        mysql_query("commit");
        if (!empty($cErro))
        {
        	#mysql_query("ROLLBACK");
			echo 'Erro ao cadastrar o cliente '.$nome.'';
            file_put_contents('/tmp/webStorm_ERRO_'.$this->pedido->getElementsByTagName('idPedido')->item(0)->nodeValue.'_CadastroCliente', $xml_cliente);
        }
        return $g['mysqlLastId'];
   }
   
   /*
   * Funcao que atualiza as aliquotas
   * Recebe como parametro:
   *      $T005_Id       -> O id do cabecalho
   */
   private function atualizarAliquotas($T005_Id)
   {
   		echo "<br>atualiza aliquotas $T005_Id"; 

        $Empresa_Atual = self::$empresa_atual;
        $cSql_T006 = mysql_query("
        						SELECT 
                                	D009_IPI(T005_C004_Id,T006_D009_Id,T005_D024_Id,T006_D006_Id,1,T005_Flag_ACP) AS T006_Aliquota_IPI_Correto,
                                    D009_IPI(T005_C004_Id,T006_D009_Id,T005_D024_Id,T006_D006_Id,2,T005_Flag_ACP)  AS T006_Percentual_Reducao_IPI_Correto,
                                    D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,T006_D009_Id,T006_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,2) AS T006_Aliquota_ICMS_Correto,
                                    D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,T006_D009_Id,T006_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,3) AS T006_Percentual_Reducao_ICMS_Correto,
                                    T006_Id,
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
									T005_Flag_Revenda_Consumidor,
									D024_Situacao_Tributaria_PIS_COFINS,
									D024_Flag_Isento_PIS_COFINS,
									D006_Flag_Substituicao_Tributaria,
									T005_Flag_Acp,
									D006_Flag_ST_VA
                                FROM T005
									LEFT JOIN T006 ON T005_Id=T006_T005_Id
									LEFT JOIN D024 ON D024_Id=T005_D024_Id
									LEFT JOIN D020 ON D020_Id=D024_D020_Id
									LEFT JOIN D009 ON D009_Id=T006_D009_Id
									LEFT JOIN D049 ON D049_Id=D009_D049_Id
									LEFT JOIN D001 ON D001_Id=D049_D001_Id
									LEFT JOIN D006 ON D006_Id=T006_D006_Id
									LEFT JOIN D005 ON D005_Id=D009_D005_Id(D009_Id)
                                    LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id='{$Empresa_Atual}'
									LEFT JOIN D088 ON D088_Id=if(D024_D088_Id>0,D024_D088_Id,D149_D088_Id)
									LEFT JOIN D059 ON D059_Id=if(D024_D059_Id>0,D024_D059_Id,D006_D059_Id)
									LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id
									AND D053_C004_Id='$Empresa_Atual'
                                WHERE T005_Id='$T005_Id'
		");

        while( $mT006 = mysql_fetch_array($cSql_T006) )
        {
        	if( $mT006['D149_Valor_VA'] <= 0 )
			{
            	if( $mT006['T005_Flag_Revenda_Consumidor'] == '1' )
					$mT006['D149_Valor_VA'] = $mT006['D053_ST_VA'];
                else
					$mT006['D149_Valor_VA'] = $mT006['D053_ST_VA_2'];
			}
			if( $mT006['D006_Flag_ST_VA'] == "N" )
            	$mT006['D149_Valor_VA'] = 0;
	
			if( ($mT006['T005_Flag_Acp']!='1' AND $mT006['T005_Flag_Acp']!='6') OR 
            	($mT006['D049_Flag_Substituicao_Tributaria']!='S') OR 
                ($mT006['D006_Flag_Substituicao_Tributaria']!='S') )
			{
            	$mT006['D149_Flag_ST']="N";
                $mT006['D149_Valor_VA']=0;
			}
	
			$cClausula = "
            			UPDATE T006
                        	LEFT JOIN D006 ON D006_Id=T006_D006_Id
	        				LEFT JOIN D059 ON D059_Id=D006_D059_Id
	        				LEFT JOIN T005 ON T005_Id=T006_T005_Id
	        				LEFT JOIN C004 ON C004_Id=T005_C004_Id
                        SET
	        			T006_Aliquota_IPI='".$mT006['T006_Aliquota_IPI_Correto']."',
	        			T006_Percentual_Reducao_IPI='".$mT006['T006_Percentual_Reducao_IPI_Correto']."',
	        			T006_Situacao_Tributaria_IPI='".$mT006['D088_Codigo_ST']."', 
	        			T006_Aliquota_ICMS='".$mT006['T006_Aliquota_ICMS_Correto']."',
	        			T006_Percentual_Reducao_ICMS='".$mT006['T006_Percentual_Reducao_ICMS_Correto']."',
	        			T006_Flag_ST=D006_Flag_Substituicao_Tributaria,
	        			T006_D005_Id='".$mT006['D005_Id']."',
	        			T006_D059_Id='".$mT006['D059_Id']."',
	        			T006_ST_VA='".$mT006['D149_Valor_VA']."',
            ";
			if( !empty( $mT006['D024_Situacao_Tributaria_PIS_COFINS'] ) )
            {
    			$cClausula .= "
								T006_Situacao_Tributaria_PIS='".$mT006['D024_Situacao_Tributaria_PIS_COFINS']."',
								T006_Situacao_Tributaria_COFINS='".$mT006['D024_Situacao_Tributaria_PIS_COFINS']."',
				";
                if( $mT006['D024_Flag_Isento_PIS_COFINS'] == 'S' )
                	$cClausula .= "
                                	T006_Flag_Isento_PIS='".$mT006['D024_Flag_Isento_PIS_COFINS']."',
									T006_Aliquota_PIS='0',
									T006_Flag_Isento_COFINS='".$mT006['D024_Flag_Isento_PIS_COFINS']."',
									T006_Aliquota_COFINS='0',
                    ";
                else
                	$cClausula .= "
									T006_Flag_Isento_PIS=D006_Flag_Isento_PIS,
									T006_Aliquota_PIS=C004_Percentual_PIS,
									T006_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,
									T006_Aliquota_COFINS=C004_Percentual_COFINS,		
					";
			}else
            	$cClausula .= "
                				T006_Flag_Isento_PIS=D006_Flag_Isento_PIS,
	        					T006_Aliquota_PIS=C004_Percentual_PIS,
	        					T006_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,
	        					T006_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,
	        					T006_Aliquota_COFINS=C004_Percentual_COFINS,
	        					T006_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS,
                ";

			$cClausula .="
            				T006_Classificacao_Fiscal='".$mT006['D005_Classificacao_Fiscal']."',
	        				T006_Flag_Situacao_Tributaria=concat('0','".$mT006['D059_Codigo_ST'].")
						WHERE T006_Id='".$mT006['T006_Id']."'
	        ";
	        mysql_query($cClausula);
	          
	        mysql_query("call T006_Gravar_Totalizacao_4('".$mT006['T006_Id']."')");
            }/* Fim while */
	
            $cSql_T005 = mysql_query("
            			     SELECT
							 	D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,0,T005_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,2)
                                            AS T005_Aliquota_ICMS_Correto,
								D009_IPV_ICMS(T005_C004_Id,T005_D024_Id,0,T005_D006_Id,D020_D018_Id,T005_Flag_Revenda_Consumidor,T005_Percentual_Comissao,T005_Flag_ACP,3)
                                            AS T005_Percentual_Reducao_ICMS_Correto
                             FROM T005
							 	LEFT JOIN D024 ON D024_Id=T005_D024_Id
								LEFT JOIN D020 ON D020_Id=D024_D020_Id
                             WHERE T005_Id='".$T005_Id."'
							");
            $mT005 = mysql_fetch_array($cSql_T005);
            mysql_query("UPDATE T005
                            SET
								T005_Aliquota_ICMS='".$mT005['T005_Aliquota_ICMS_Correto']."',
								T005_Percentual_Reducao_ICMS='".$mT005['T005_Percentual_Reducao_ICMS_Correto']."'
                            WHERE T005_Id='$T005_Id'");	
            mysql_query("call T005_Gravar_Totalizacao_3('$T005_Id')");

   }

    public function preCadastroProduto($item){
        global $g,$D009_Id_Cotacao;
        require_once("bibliotecas/classes/CAD002.php");

        $precoTabelado = $g['conf']->ini('precoTabelado');

        $descricao      = utf8_decode($item['descricao']);
        $codigo_Produto = utf8_decode($item['codigo']);
        $preco          = utf8_decode($item['valorUnitario']);
        $peso           = utf8_decode($item['peso']);
        $especificacoes = utf8_decode($item['especificacoes']);

        $D001 = <<<EOT
        INSERT INTO D001 (
            D001_Codigo_Produto,
            D001_Flag_Pre_Cadastro,
            D001_Descricao_Produto,
            D001_Peso_Unitario_Kg,
            D001_Especificacoes,
            D001_Flag_Tipo
        ) VALUES (
            '{$codigo_Produto}',
            'S',
            '{$descricao}',
            '{$peso}',
            '{$especificacoes}',
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
