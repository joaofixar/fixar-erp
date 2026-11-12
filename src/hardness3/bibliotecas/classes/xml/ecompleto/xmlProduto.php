<?php
namespace hardness;
class xmlProduto
{
    
    public function gerarXMLProduto($D009_Id_Inicio=null,$D009_Id_Fim=null)
    {
    	global $g;
    	global $confUsuario;
    	    	
		#versao do encoding xml
		$documento = new \DOMDocument("1.0", "UTF-8"); 

		#retirar os espacos em branco
		$documento->preserveWhiteSpace = false;

		#gerar o codigo
		$documento->formatOutput = true;	
		
		#criando o node principal (root)
		$ecompleto = $documento->createElement("ecompleto"); 
			   	

		if ($D009_Id_Inicio && $D009_Id_Fim)
			$prod=mysql_query("SELECT * FROM D009 LEFT JOIN D049 on D049_Id=D009_D049_Id LEFT JOIN D001 on D001_Id=D049_D001_Id LEFT JOIN C008 on C008_Id=D001_C008_Id LEFT JOIN D003 on D003_Id=D001_D003_Id LEFT JOIN D082 on D082_Id=D049_D082_Id WHERE D009_Id>=$D009_Id_Inicio AND D009_Id<=$D009_Id_Fim");
		else if ($D009_Id_Inicio)
    		$prod=mysql_query("SELECT * FROM D009 LEFT JOIN D049 on D049_Id=D009_D049_Id LEFT JOIN D001 on D001_Id=D049_D001_Id LEFT JOIN C008 on C008_Id=D001_C008_Id LEFT JOIN D003 on D003_Id=D001_D003_Id LEFT JOIN D082 on D082_Id=D049_D082_Id WHERE D009_Id='$D009_Id_Inicio'");
    	else
    		$prod=mysql_query("SELECT * FROM D009 LEFT JOIN D049 on D049_Id=D009_D049_Id LEFT JOIN D001 on D001_Id=D049_D001_Id LEFT JOIN C008 on C008_Id=D001_C008_Id LEFT JOIN D003 on D003_Id=D001_D003_Id LEFT JOIN D082 on D082_Id=D049_D082_Id");
    		
    	while ($produto=mysql_fetch_assoc($prod)){
    		
			$produto['D001_Descricao_Produto'] = preg_replace("[^a-zA-Z0-9_]", "", strtr($produto['D001_Descricao_Produto'], "&", "e"));    		
			$produto['D001_Especificacoes'] = preg_replace("[^a-zA-Z0-9_]", "", strtr($produto['D001_Especificacoes'], "&", "e"));    		
			$produto['D082_Marca'] = preg_replace("[^a-zA-Z0-9_]", "", strtr($produto['D082_Marca'], "&", "e"));    		
    		
			$a_id=$produto['D001_Codigo_Produto'];
			$a_nome=$produto['D001_Descricao_Produto'];
			$a_nome_categoria_1=$produto['C008_Tipo'];
			$a_nome_categoria_2=$produto['D003_Nome_Linha'];
			$a_nome_fabricante=$produto['D082_Marca'];
			$a_tipo_produto='N';
			$a_texto_descricao=$produto['D001_Especificacoes'];
			$a_periodo_garantia;
			$a_quantidade_embalagem=number_format($produto['D001_Qte_Embalagem'],2);
			$a_largura;
			$a_altura;
			$a_profundidade;
			$a_quantidade_estoque_fisico=$produto['D009_Quantidade_Estoque'];
			$a_quantidade_estoque_virtual=$produto['D009_Quantidade_Estoque_Liquido'];
			$a_id_disponibilidade;
			$a_valor_venda;
			$a_valor_compra;
			$a_desconto_vista;
			$a_tipo_estoque;   

			#nó filho (contato)
			$produto = $documento->createElement("produto");

			
			#setanto nomes e atributos dos elementos xml (nós)
			$id = $documento->createElement("id", $a_id);
			$nome = $documento->createElement("nome", $a_nome);
			$nome_categoria_1 = $documento->createElement("nome_categoria_1", $a_nome_categoria_1);
			$nome_categoria_2 = $documento->createElement("nome_categoria_2", $a_nome_categoria_2);
			$nome_fabricante = $documento->createElement("nome_fabricante", $a_nome_fabricante);
			$tipo_produto = $documento->createElement("tipo_produto", $a_tipo_produto);
			$texto_descricao = $documento->createElement("texto_descricao", $a_texto_descricao);
			$periodo_garantia = $documento->createElement("periodo_garantia", $a_periodo_garantia);
			$quantidade_embalagem = $documento->createElement("quantidade_embalagem", $a_quantidade_embalagem);
			$largura = $documento->createElement("largura", $a_largura);
			$altura = $documento->createElement("altura", $a_altura);
			$profundidade = $documento->createElement("profundidade",$a_profundidade);
			$quantidade_estoque_fisico = $documento->createElement("quantidade_estoque_fisico", $a_quantidade_estoque_fisico);
			$quantidade_estoque_virtual = $documento->createElement("quantidade_estoque_virtual", $a_quantidade_estoque_virtual);
			$id_disponibilidade = $documento->createElement("id_disponibilidade", $a_id_disponibilidade);
			$valor_venda = $documento->createElement("valor_venda", $a_valor_venda);
			$valor_compra = $documento->createElement("valor_compra", $a_valor_compra);
			$desconto_vista = $documento->createElement("desconto_vista", $a_desconto_vista);
			$tipo_estoque = $documento->createElement("tipo_estoque", $a_tipo_estoque);			

			#adiciona os nós (informacaoes do produto) em produto
			$produto->appendChild($id);
			$produto->appendChild($nome);
			$produto->appendChild($nome_categoria_1);
			$produto->appendChild($nome_categoria_2);
	        $produto->appendChild($nome_fabricante);            
	        $produto->appendChild($tipo_produto);            
	        $produto->appendChild($texto_descricao);            
	        $produto->appendChild($periodo_garantia);            
	        $produto->appendChild($quantidade_embalagem);            
	        $produto->appendChild($largura);            
	        $produto->appendChild($altura);            
	        $produto->appendChild($profundidade);            
	        $produto->appendChild($quantidade_estoque_fisico);            
	        $produto->appendChild($quantidade_estoque_virtual);            
	        $produto->appendChild($id_disponibilidade);            
	        $produto->appendChild($valor_venda);            
	        $produto->appendChild($valor_compra);            
	        $produto->appendChild($desconto_vista);            
	        $produto->appendChild($tipo_estoque);                          
	        
			#adiciona o nó contato no principal (ecompleto) 
			$ecompleto->appendChild($produto);
		}
		
		
		
		$documento->appendChild($ecompleto);

		# Para salvar o arquivo, descomente a linha
		//$documento->save("contatos.xml");

		#cabeçalho da página
		//header("Content-Type: text/xml");
		# imprime o xml na tela
		$dirName = $g['pathDados'].'tmp/';
		is_dir($dirName) or mkdir($dirName, 0777, true);
		$filename=date("Ymd").'-produtos.xml';		
		$pathname=$dirName.$filename;			
		if ($documento->save($pathname))
			return date("Ymd");
		else 
			return false;
	
		/*	
		// abre o XML para download
		//header('Content-type: text/xml');
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Pragma: no-cache');
		header('Expires: 0');		
		
		return true;
		*/
    }
    	

}

?>
