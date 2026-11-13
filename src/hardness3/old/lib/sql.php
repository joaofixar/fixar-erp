<?php
/**
* Classe para acesso a banco, execução de querys e geração automatica de SQLs
*/
class SQL {
	/**
	 * Utiliza as informações da criação do Grid + outras informações para gerar a SQL automaticamente
	 * 
	 * @param array $grid	mesmo parametro enviado para UI->grid()
	 * @param string $from	query SQL posicionada após o FROM
	 * @param string $extra	query SQL posicionada após a sql (utilize aqui WHERE, JOIN, ORDER BY, GROUP BY, etc)
	 * @param string 
	 */
	function gerarSQLGrid($grid, $from, $extra = '') {
		$sqlCampos = '';
		foreach ($grid as $grid_valor) {
			if (empty($grid_valor['colunaTipo']) || $grid_valor['colunaTipo'] == 'banco') {
				// coluna é um campo associado a uma tabela no banco
				$sqlCampos .= (empty($sqlCampos)) ? $grid_valor['colunaValor'] : ', ' . $grid_valor['colunaValor'];
			} else if ($grid_valor['colunaTipo'] == 'string') {
				// a coluna é uma string (um dado qualquer)
			}
		}
		if (empty($sqlCampos)) { return 'Erro - nenhum campo do grid associado a alguma tabela. Execute o UI->grid() direto.'; }
		
		// Monta e executa a query
		$sql = 'SELECT ' . $sqlCampos . ' FROM ' . $from . ' ' . $extra;
		$mysqlQuery = mysql_query($sql);
		if (!$mysqlQuery) { return(mysql_error()); }
		$resultado = array();
		while ($row = mysql_fetch_row($mysqlQuery)) {
			$resultado[] = $row;
		} 
		return $resultado;
	}
}
?>