<?php
/**
* Geração automatica de elementos UI
*/
class UI {
	/**
	 * Gera automaticamente um Grid com base nos dados enviados (essa função pode ser usada em conjunto com SQL->gerarGridSQL())
	 * 
	 * @param array $grid
	 * array(
	 * 		array(								// Nova Coluna
	 * 			'colunaTipo' 	=> 'banco',		// (opcional) 		- pode ser: 'banco' ou 'livre'
	 * 			'colunaValor' 	=> '',			// (obrigatório) 	- Se tipo 'banco' = valor é o nome do campo da tabela | Se tipo 'livre' = é o ID da tabela
	 * 			'colunaTitulo' 	=> '',			// (obrigatório) 	- titulo da coluna
	 * 			'colunaExibir	=> true,		// (opcional) 		- exibir ou não no grid (utilizado quando você precisa do campo no SQL mais não quer exibir ele)
	 * 			'callback'		=> '',			// (opcional) 		- nome de uma função que irá processar o campo
	 * 			'extra'			=> '',			// (opcional) 		- pode ser utilizado para adicionar qualquer campo html extra no 'td', exemplo: 'class="teste" style="font-size: 110%;'
	 * 		)
	 * )
	 * 
	 * Função Callback irá receber:
	 * array(
	 * 		'campoAtual' => '',
	 * 		'todosCampos' => array()
	 * )
	 * 
	 * @param array $dados ( pode ser o retorno da função sql->gerarGridSQL() )
	 * array(
	 * 		array(
	 * 			'conteudo campo coluna 1', 'conteudo campo coluna 2', ...
	 * 		)
	 * 		... vários registros ...
	 * )
	 * @return array('thead', 'tbody', 'tfoot')
	 */		 
	function grid($grid, $dados) {
		global $g;
		$return = array();
		
		// THEAD
		$return['thead'] = array();
		foreach ($grid as $grid_valor) {
			if (isset($grid_valor['colunaExibir']) && !$grid_valor['colunaExibir']) { continue; }
			$return['thead'][] = $grid_valor['colunaTitulo'];
		}
		
		// TBODY
		$return['tbody'] = array();
		// Para cada coluna de dados
		foreach ($dados as $dados_key => $dados_valor) {
			$tmpBancoKey = -1;
			// Passa por todos os campos do grid
			foreach ($grid as $grid_key => $grid_valor) {
				// Coluna precisa ser exibida?
				if (isset($grid_valor['colunaExibir']) && !$grid_valor['colunaExibir']) {
					if (strtolower($grid_valor['colunaTipo']) != 'livre') {
						$tmpBancoKey++;
					}
				} else {
					if (isset($grid_valor['callback'])) {
						// retorna todos os campos da linha (com o 'colunaValor' como key do array)
						$todosCampos = array();
						$tmpBancoKey_ = -1;
						foreach ($grid as $grid_key_ => $grid_valor_) {
							if (strtolower($grid_valor_['colunaTipo']) == 'livre') {
								$todosCampos[$grid_valor_['colunaValor']] = '';
							} else {
								$tmpBancoKey_++;
								$todosCampos[$grid_valor_['colunaValor']] = $dados_valor[$tmpBancoKey_];
							}
						}
					}
					if (strtolower($grid_valor['colunaTipo']) == 'livre') {
						// Campo sem ligações ao banco
						if (isset($grid_valor['callback'])) {
							$return['tbody'][$dados_key][$grid_key] = call_user_func($grid_valor['callback'], array('campoAtual' => '', 'todosCampos' => $todosCampos));
						} else {
							$return['tbody'][$dados_key][$grid_key] = '';
						}
					} else {
						// Campo ligado a uma Tabela no Banco
						$tmpBancoKey++;
						if (isset($grid_valor['callback'])) {
							// chama a função do usuário para processar o campo
							$return['tbody'][$dados_key][$grid_key] = call_user_func($grid_valor['callback'], array('campoAtual' => $dados_valor[$tmpBancoKey], 'todosCampos' => $todosCampos));
						} else {
						    $return['tbody'][$dados_key][$grid_key] = $dados_valor[$tmpBancoKey];
						}
					}
				}			
			}
		}
		
		// TFOOT
		$return['tfoot'] = array();
		
		return $return;
	}
	
    /**
     * 
     * Gera arquivo CSV utilizando parametros do grid
     * 
     */
	function csv($grid,$dados) {
		$txt = '';
		// Gera titulos
		foreach ($grid as $valor) {
			$txt .= "{$valor[0]};";
		}
		$txt .= chr(13).chr(10);
		// Gera dados
		foreach ($dados as $valor) {
			foreach ($valor as $valor2) {
				$txt .= "{$valor2};";
			}
    		$txt .= chr(13).chr(10);
		}
		$arquivo="tmp/arquivo.csv";
		if (file_exists($cArquivo)) {
		    unlink($cArquivo);
		}
	    //-- Cria o arquivo
		if (!$handle = file_put_contents($arquivo,$txt)) {
		  	die("Erro ao criar o arquivo: $arquivo");
		 	exit;
		}
		CHMOD($arquivo,0777);
		$saida = "<script>document.location.href='/hardness3/$arquivo';</script>";
		// Retorna
		return $saida;
	}
	
	/**
	 * 
	 * Gera grid no formato parta impressao
	 * @param unknown_type $dados
	 */
	function impressao($grid,$dados) {
		// ...
	}
		
}
?>