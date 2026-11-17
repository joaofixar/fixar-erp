<?php
namespace hardness;
// Carrega funções callback
require_once('prog/etc/etc006/grid_func-callback.php');
// Carrega funções do ajax
require_once('prog/etc/etc006/grid_func-js.php');

// Pega infos
require_once('sistema/comunicacaoEmail.php');
$mail = new comunicacaoEmail($g['C007']['C007_Email_POP_Servidor'], $g['C007']['C007_Email_SMTP_Usuario'], $g['C007']['C007_Email_SMTP_Senha'], false, $r_acaoId, $g['C007']['C007_Email_POP_Porta']);

/**
* Definição do Grid (Tabela)
*/
$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;

if ($g['r'][3] == 'etc006Xml' || $g['r'][3] == 'etc006XmlFornecedor') {
	$grid = array(
		array('colunaValor' => "IF(T139_Flag_Xml_Importado = 'S', '<spa style=\"color: green;\">Sim</span>', '<spa style=\"color: var(--BGVermelho); font-weight: bold;\">Não</span>') as XmlImportado", 'colunaTitulo' => 'XML Importado?', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
		array('colunaValor' => "T139_Flag_Lido", 'colunaTitulo' => 'Lido', 'callback' => 'campoLido', 'style' => 'width: 16px;', 'colunaAlinhar' => 'center'),
		array('colunaValor' => "T139_Data_Hora", 'colunaTitulo' => 'Data/Hora', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_Data_Hora', 'style' => 'width: 1%'),
		array('colunaValor' => "T139_De", 'colunaTitulo' => 'De', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_De', 'style' => 'width: 1%'),
		array('colunaValor' => "T139_Para", 'colunaTitulo' => 'Para', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_Para', 'style' => 'width: 1%'),
		array('colunaValor' => "T139_Titulo", 'colunaTitulo' => 'Título', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_Titulo'),
		array('colunaValor' => "D024_Nome_Fantasia", 'colunaTitulo' => 'Cliente/Fornecedor', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'D024_Nome_Fantasia', 'style' => 'width: 1%'),
		array('colunaValor' => "T139_T075_Id", 'colunaTitulo' => 'T139_T075_Id', 'colunaExibir' => false),
		array('colunaValor' => "D024_Id", 'colunaTitulo' => 'D024_Id', 'colunaExibir' => false),
		array('colunaValor' => "Usuario",'colunaTipo'=>'livre', 'colunaTitulo' => 'Usuário', 'callback' => 'retornaUsuario' ,'style' => 'width: 1%;text-align:center;'),
		array('colunaValor' => "T139_C007_Id", 'colunaTitulo' => 'Usuário', 'colunaExibir' => false),
		array('colunaValor' => "T139_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor' => "D085_T259_Id", 'colunaTitulo' => 'Tarefa', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
	);
} else {
	$grid = array(
		array('colunaValor' => "T139_Pasta", 'colunaTitulo' => 'Pasta', 'style' => 'width: 16px; text-align:center;', 'colunaExibir' => false),
		array('colunaValor' => "T139_Flag_Selecionado", 'colunaTitulo' => '', 'callback' => 'checkBoxLido', 'style' => 'width: 20px;', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => '1|0'),
		array('colunaValor' => "T139_Flag_Lido", 'colunaTitulo' => 'Lido', 'callback' => 'campoLido', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
		array('colunaValor' => "temAnexo", 'colunaTipo' => 'livre', 'colunaTitulo' => '', 'callback' => 'temAnexo', 'style' => 'width: 16px;'),
		array('colunaValor' => "D085_T259_Id", 'colunaTitulo' => 'Tarefa', 'style' => 'width:1%', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tarefa', '/etc/etc017/content/etc017ContentTarefa/', 'D085_T259_Id', '900,600')),
		//array('colunaValor' => "spam", 'colunaTipo' => 'livre', 'colunaTitulo' => '', 'callback' => 'spam', 'style' => 'width: 16px;'),
		array('colunaValor' => "T139_De", 'colunaTitulo' => 'De', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_De', 'style' => 'width: 1%'),
		array('colunaValor' => "T139_Para", 'colunaTitulo' => 'Para', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_Para', 'style' => 'width: 1%'),
		array('colunaValor' => "T139_Titulo", 'colunaTitulo' => 'Título', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_Titulo'),
		array('colunaValor' => "D024_Nome_Fantasia", 'colunaTitulo' => 'Cliente/Fornecedor', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'D024_Nome_Fantasia', 'style' => 'width: 1%'),
		array('colunaValor' => "T139_T003_Id", 'colunaTitulo' => 'Orçamento', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_T003_Id', 'style' => 'width: 1%', 'colunaExibir' => false),
		array('colunaValor' => "T139_Valor_Total", 'colunaTitulo' => 'Total Orçamento', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_Valor_Total', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
		array('colunaValor' => "T139_T005_Id", 'colunaTitulo' => 'Pedido', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_T005_Id', 'style' => 'width: 1%', 'colunaExibir' => false),
		array('colunaValor' => "T075_Numero_DI", 'colunaTitulo' => 'DI', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T075_Numero_DI', 'style' => 'width: 1%', 'colunaExibir' => false),
		array('colunaValor' => "T139_T075_Id", 'colunaTitulo' => 'T139_T075_Id', 'colunaExibir' => false),
		array('colunaValor' => "D024_Id", 'colunaTitulo' => 'D024_Id', 'colunaExibir' => false),
		array('colunaValor' => "T139_Data_Hora", 'colunaTitulo' => 'Data/Hora', 'callback' => 'campoLidoNegrito', 'callbackParameter' => 'T139_Data_Hora', 'style' => 'width: 1%'),
		array('colunaValor' => "Usuario",'colunaTipo'=>'livre', 'colunaTitulo' => 'Usuário', 'callback' => 'retornaUsuario' ,'style' => 'width: 1%;text-align:center;'),
		array('colunaValor' => "T139_C007_Id", 'colunaTitulo' => 'Usuário', 'colunaExibir' => false),
		array('colunaValor' => "T139_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
	);

	// TODO: futuramente criar um $grid inteiro para cada opção... evitar isto aqui
	if ($g['r'][3] == 'etc006GridInbox' || $g['r'][3] == 'etc006GridInboxCliente' || $g['r'][3] == 'etc006GridSpam') {
		unset($grid[6]);
		//unset($grid[0]);
	} else if ($g['r'][3] == 'etc006GridSent' || $g['r'][3] == 'etc006GridSentCliente') {
		unset($grid[5]);
		unset($grid[0]);
	}
	// if ($g['r'][3] == 'etc006GridInboxCliente' && !empty($r_T003_Id)) {
	if ($g['r'][3] == 'etc006GridInbox' && !empty($r_T003_Id)) {
		// Add novas colunas e ocultando 
		$link = array('colunaValor' => "vinculoOrcamento", 'colunaTipo' => 'livre', 'colunaTitulo' => 'linkar', 'callback' => 'vincularEmailOrcamentoCallback', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center');
		$orc = array('colunaValor' => "T003_Id", 'colunaTitulo' => 'Orç', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => true);
		array_splice($grid, 4, 0, [$link, $orc]);
		$grid[] = array('colunaValor' => "T139_Flag_Selecionado", 'colunaExibir' => false);
	}
}

/**
* Função que define classes CSS para cada linha do grid
*/
function gridLinha($array) {
	if (!$array['todosCampos']['T139_Flag_Lido']) {
		return 'linhaTarefaNaoLida';
	} else {
		return 'linhaTarefaLida';
	}
}

/**
* Definição da SQL
*/
$from = 'T139';

$incluirUsuario = '';
if (isset($r_T003_Id) && empty($r_T003_Id)) {
	$incluirUsuario = " AND T139_C007_Id = '{$g['usuarioAtual']}'";
}

$enviarParameter = '';
/**
* Ações
*/
switch($g['r'][3]){
	/**
	* Grid Inbox
	* Chamado em: etc006TabLeitura
	*/
	// GRID OBSOLETO NÃO TEREMOS MAIS INBOX NO SISTEMA 05/04/2023
	case 'etc006GridInbox':
		$extra = <<<EOT
		    LEFT JOIN D024 ON D024_Id=T139_D024_Id
		    LEFT JOIN T003 ON T003_Id=T139_T003_Id
		    LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id 
			WHERE (T139_Pasta = 'E' AND T139_Flag_Excluir = 0{$incluirUsuario} AND T139_C007_Id='{$g['usuarioAtual']}')
			AND T139_Flag_Spam = 'N'
			GROUP BY T139_Id
			ORDER BY T139_Id DESC
EOT;
	break;

	/**
	* Grid Inbox
	* Chamado em: etc006TabLeitura
	*/
	case 'etc006GridSent':
		/**
		* Definição da SQL
		*/
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T139_D024_Id
			LEFT JOIN T003 ON T003_Id=T139_T003_Id
			LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id 
			WHERE (T139_Pasta = 'S' AND T139_Flag_Excluir = 0{$incluirUsuario} AND T139_C007_Id='{$g['usuarioAtual']}')
			GROUP BY T139_Id
			ORDER BY T139_Id DESC
EOT;
	break;

	case 'etc006GridDump':
		/**
		* Definição da SQL
		*/
		$from = 'T139';
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T139_D024_Id
			LEFT JOIN T003 ON T003_Id=T139_T003_Id
			LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id 
			WHERE (T139_Flag_Excluir = 1{$incluirUsuario} AND T139_C007_Id='{$g['usuarioAtual']}')
			GROUP BY T139_Id
			ORDER BY T139_Id DESC
EOT;
	break;

	// GRID OBSOLETO NÃO TEREMOS MAIS INBOX NO SISTEMA 05/04/2023
	case 'etc006GridInboxCliente':
		$extra = <<<EOT
		    LEFT JOIN D024 ON D024_Id=T139_D024_Id
		    LEFT JOIN T003 ON T003_Id=T139_T003_Id
		    LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id OR IF(IFNULL(T139_T003_Id,0)>0 OR IFNULL(T139_T005_Id,0)>0, D085_T003_Id=T139_T003_Id OR D085_T005_Id=T139_T005_Id,'')
			WHERE (T139_Pasta = 'E' AND T139_Flag_Excluir = 0)
			AND T139_D024_Id = '{$r_D024_Id}'
			AND T139_Flag_Spam = 'N'
			GROUP BY T139_Id
			ORDER BY T139_Id DESC
EOT;
	break;

	/**
	* Grid Inbox
	* Chamado em: etc006TabLeitura
	*/
	case 'etc006GridSentCliente':
		/**
		* Definição da SQL
		*/
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T139_D024_Id
			LEFT JOIN T003 ON T003_Id=T139_T003_Id
			LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id OR IF(IFNULL(T139_T003_Id,0)>0 OR IFNULL(T139_T005_Id,0)>0, D085_T003_Id=T139_T003_Id OR D085_T005_Id=T139_T005_Id,'')
			WHERE (T139_Pasta = 'S' AND T139_Flag_Excluir = 0)
			AND T139_D024_Id = '{$r_D024_Id}'
			GROUP BY T139_Id
			ORDER BY T139_Id DESC
EOT;
	break;

	case 'etc006GridDumpCliente':
		/**
		* Definição da SQL
		*/
		$from = 'T139';
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T139_D024_Id
			LEFT JOIN T003 ON T003_Id=T139_T003_Id
			LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id OR IF(IFNULL(T139_T003_Id,0)>0 OR IFNULL(T139_T005_Id,0)>0, D085_T003_Id=T139_T003_Id OR D085_T005_Id=T139_T005_Id,'')
			WHERE (T139_Flag_Excluir = 1)
			AND T139_D024_Id = '{$r_D024_Id}'
			GROUP BY T139_Id
			ORDER BY T139_Id DESC
EOT;
	break;

	// GRID OBSOLETO NÃO TEREMOS MAIS INBOX NO SISTEMA 05/04/2023
	case 'etc006GridSpam':
		$extra = <<<EOT
		    LEFT JOIN D024 ON D024_Id=T139_D024_Id
		    LEFT JOIN T003 ON T003_Id=T139_T003_Id
		    LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id OR IF(IFNULL(T139_T003_Id,0)>0 OR IFNULL(T139_T005_Id,0)>0, D085_T003_Id=T139_T003_Id OR D085_T005_Id=T139_T005_Id,'')
			WHERE (T139_Pasta = 'E' AND T139_Flag_Excluir = 0{$incluirUsuario} AND T139_C007_Id='{$g['usuarioAtual']}')
			AND T139_Flag_Spam = 'S'
			GROUP BY T139_Id
			ORDER BY T139_Id DESC
EOT;
	break;

	/**
	* Grid Inbox
	* Chamado em: etc006TabLeitura
	*/
	case 'etc006GridTodos':
		/**
		* Definição da SQL
		*/
		$verificarLocal = '';
		if (isset($r_D024_Id)) {
			$verificarLocal = " AND T139_D024_Id={$r_D024_Id}";
			$enviarParameter .= '&D024_Id=' . urlencode($r_D024_Id);
		}
		if (isset($r_D117_Id)) {
			$verificarLocal = " AND T139_D117_Id={$r_D117_Id}";
			$enviarParameter .= '&D117_Id=' . urlencode($r_D117_Id);
		}
		if (isset($r_T003_Id)) {
			$verificarLocal = " AND T139_T003_Id={$r_T003_Id}";
			$enviarParameter .= '&T003_Id=' . urlencode($r_T003_Id);
		}
		if (isset($r_T005_Id)) {
			$verificarLocal = " AND T139_T005_Id={$r_T005_Id}";
			$enviarParameter .= '&T005_Id=' . urlencode($r_T005_Id);
		}
		if (isset($r_T011_Id)) {
			$verificarLocal = " AND T139_T011_Id={$r_T011_Id}";
			$enviarParameter .= '&T011_Id=' . urlencode($r_T011_Id);
		}
		if (isset($r_T075_Id)) {
			$verificarLocal = " AND T139_T075_Id={$r_T075_Id}";
			$enviarParameter .= '&T075_Id=' . urlencode($r_T075_Id);
		}
		if (isset($r_T218_Id)) {
			$verificarLocal = " AND T139_T218_Id={$r_T218_Id}";
			$enviarParameter .= '&T218_Id=' . urlencode($r_T218_Id);
		}
		if (isset($r_T224_Id)) {
			$verificarLocal = " AND T139_T224_Id={$r_T224_Id}";
			$enviarParameter .= '&T224_Id=' . urlencode($r_T224_Id);
		}
		if (isset($r_T010_Id)) {
			$verificarLocal = " AND T139_T010_Id={$r_T010_Id}";
			$enviarParameter .= '&T010_Id=' . urlencode($r_T010_Id);
			$extra = <<<EOT
			LEFT JOIN T173 on T173_Id=T139_T173_Id
			LEFT JOIN D024 on D024_Id=T173_D024_Id
			LEFT JOIN T003 ON T003_Id=T139_T003_Id
			LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id
			WHERE (T139_Flag_Excluir = 0{$incluirUsuario}{$verificarLocal}) AND T139_T173_Id IS NOT NULL AND T139_T010_Id IS NOT NULL
			ORDER BY T139_Data_Hora DESC
EOT;
		}else{
			$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T139_D024_Id
			LEFT JOIN T003 ON T003_Id=T139_T003_Id
			LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id
			WHERE (T139_Flag_Excluir = 0{$incluirUsuario}{$verificarLocal})
			ORDER BY T139_Data_Hora DESC
EOT;
		}
	break;

	case 'etc006Xml':
		/**
		* Definição da SQL
		*/
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T139_D024_Id
		    LEFT JOIN T003 ON T003_Id=T139_T003_Id
		    LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id
			WHERE (T139_Pasta = 'E' AND T139_Flag_Tem_Xml = 'S')
			ORDER BY T139_Id DESC
EOT;
	break;

	case 'etc006XmlFornecedor':
		/**
		* Definição da SQL
		*/
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=T139_D024_Id
		    LEFT JOIN T003 ON T003_Id=T139_T003_Id
		    LEFT JOIN T075 ON T075_Id=T139_T075_Id
		    LEFT JOIN D085 ON D085_T139_Id=T139_Id
			WHERE (T139_Pasta = 'E' AND T139_Flag_Tem_Xml = 'S')
			AND D024_Id='{$r_D024_Id}'
			ORDER BY T139_Id DESC
EOT;
	break;
}

if ($g['r'][3] == 'etc006Xml') {
	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
		'T139_De' => array('titulo' => 'De', 'tipo' => 'texto'),
		'T139_Para' => array('titulo' => 'Para', 'tipo' => 'texto'),
		'T139_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
		'T139_Data_Hora' => array('titulo' => 'Data', 'tipo' => 'data'),
	);
} else {
	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
		'T139_De' => array('titulo' => 'De', 'tipo' => 'texto'),
		'T139_Para' => array('titulo' => 'Para', 'tipo' => 'texto'),
		'T139_Titulo' => array('titulo' => 'Título', 'tipo' => 'texto'),
		'T139_Data_Hora' => array('titulo' => 'Data', 'tipo' => 'data'),
		'T139_T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'numero'),
		'T139_T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
        'T139_Flag_Lido' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
            array('title' => 'Todos', 'value' => ''),
            array('title' => 'Lido', 'value' => '1'),
            array('title' => 'Não lido', 'value' => '0'),
        ), 'naoGerarExtra' => false),
        'T139_Flag_Selecionado' => array('titulo' => 'Marcados', 'tipo' => 'select', 'select' => array(
            array('title' => 'Todos', 'value' => ''),
            array('title' => 'Apenas marcados', 'value' => '1'),
            array('title' => 'Não marcados', 'value' => '0'),
        ), 'naoGerarExtra' => false),
        'D085_T259_Id' => array('titulo' => 'Tarefa', 'tipo' => 'numero'),
	);
}
if ($g['r'][3] == 'etc006GridInbox' || $g['r'][3] == 'etc006GridInboxCliente') { 
	unset($gridFiltro['T139_Para']);
} else if ($g['r'][3] == 'etc006GridSent' || $g['r'][3] == 'etc006GridSentCliente') {
	unset($gridFiltro['T139_De']);
}
// Gera o filtro e acrescenta ao $extra
$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
$extra_ = urlencode(base64_encode($extra));

if($g['r'][3]!='etc006GridDump' && $g['r'][3] != 'etc006Xml'){

	//Se não estiver em Spam
	$seNaoSpamMostra = ($g['r'][3]!='etc006GridSpam') ? true : false;
	$seNaoSpamEsconde = ($g['r'][3]!='etc006GridSpam') ? false : true;
	//Se houver linha no grid
	$seVazioLinhaGridIdMostra = (empty($r_linhaGridId)) ? true : false;

	/**
	 * Botões Almir
	 */
	$botoes = array(
		array('titulo' => 'Novo e-mail', 'id' => "etc006EnviarEmail-{$g['r'][3]}",  'exibirCallback' => "return $seNaoSpamMostra;"),
		array('titulo' => 'Associar', 'id' => "etc006AssEmails-{$g['r'][3]}"),
		array('titulo' => 'Excluir marcados', 'id' => "etc006DelSelected-{$g['r'][3]}",  'exibirCallback' => "return $seNaoSpamMostra;"),
		array('titulo' => 'Excluir marcados', 'id' => "etc006DelSpamSelected-{$g['r'][3]}",  'exibirCallback' => "return $seNaoSpamEsconde;"),
		array('titulo' => 'Marcar como lidos', 'id' => "etc006MarcarComoLidos-{$g['r'][3]}",  'exibirCallback' => "return $seVazioLinhaGridIdMOstra;"),
		array('titulo' => 'Marcar como não lidos', 'id' => "etc006MarcarComoNaoLidos-{$g['r'][3]}",  'exibirCallback' => "return $seVazioLinhaGridIdMostra;"),

		/* PARTE DO SISTEMA OBSOLETA NÃO TERÁ MAIS PARTES DE BAIXAR E-MAIL */
		#array('titulo' => 'Verificar e-mails', 'id' => "etc006VerificarEmails-{$g['r'][3]}"),
	);

	if ($g['r'][3] == 'etc006GridTodos' && !empty($r_T003_Id)) {
		if ($r_D024_Id <= 0) {
			$row = mysql_fetch_assoc(mysqli_query("SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$r_T003_Id}'"));
			$D024_Id = $row['T003_D024_Id'];
		}
		$botoes[] = array('titulo' => 'Vincular ao orçamento', 'id' => "vincularOrcamento",  'exibirCallback' => "return $seVazioLinhaGridIdMostra;");
	} elseif ($g['r'][3] == 'etc006GridInbox' && !empty($r_T003_Id)) {
		$botoes = array();
	}

	echo Botoes::processar($botoes);
}
else{
	/**
	 * Botões Almir
	 */
	$botoes = array(
	);
	echo Botoes::processar($botoes);
}

// Imprime o filtro do GRID (normalmente executado acima do GRID)
echo $g['ui']->gridFiltroPrint($gridFiltro);

/**
 * Totais Almir
 */
$totais = array(
	// sql
	array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
);
/**
 * Geração: Monta o SQL e retorna o dados
 */
list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
$sql_ = base64_encode($geradoSql);
/**
* Botões (Versão Acima do GRID)
*/
/*if($g['r'][3]!='etc006GridDump' && $g['r'][3] != 'etc006Xml'){
if (!personalizacaoBotoes()) {
echo <<<EOT
	<div class="buttonsBottomTop">
EOT;
	if($g['r'][3]!='etc006GridSpam'){
		echo "<button type=\"button\" id=\"etc006EnviarEmail-{$g['r'][3]}\">Novo e-mail</button>"; 
	}
echo <<<EOT
		<!--<button type="button" id="etc006VerificarEmails-{$g['r'][3]}">Verificar e-mails</button>--> 
		<!--<button type="button" id="etc006AssEmails-{$g['r'][3]}">Associar</button>-->
		<!--<button type="button" id="etc006MarcarLidos-{$g['r'][3]}">Marcar lidos</button>-->
		<!--<button type="button" id="etc006MarcarNaoLidos-{$g['r'][3]}">Marcar não lidos</button>-->
		<!--<button type="button" id="etc006DesmarcarTodos-{$g['r'][3]}">Desmarcar todos</button>-->
EOT;
	if($g['r'][3]!='etc006GridSpam'){
		echo "<button type=\"button\" id=\"etc006DelSelected-{$g['r'][3]}\">Excluir marcados</button>";
	} else {
		echo "<button type=\"button\" id=\"etc006DelSpamSelected-{$g['r'][3]}\">Excluir marcados</button>";
	}
    // Não executar quando houver o refresh de linha
    //if (empty($r_linhaGridId)) {
        
		<!--<button type="button" id="etc006DelUnselected-{$g['r'][3]}">Excluir desmarcados</button>
		<button type="button" id="etc006MarcarComoLidos-{$g['r'][3]}">Marcar como lidos</button>
		<button type="button" id="etc006MarcarComoNaoLidos-{$g['r'][3]}">Marcar como não lidos</button>
	</div>-->*/
	$ven = ($r_T003_Id) ? 'ven001' : (($r_T005_Id) ? 'ven002' : '');
	$callback = ($r_T003_Id) ? '&callback=?' : (($r_T005_Id) ? '&acao=enviarEmail&callback=?' : '');
	$alcada = ($r_T003_Id) ? "encodeURIComponent(request.data)" : (($r_T005_Id) ? 'encodeURIComponent(request.code) + "&acao=email"' : '');
	$js_T003_Id = $r_T003_Id ?? '0';
	$js_T005_Id = $r_T005_Id ?? '0';

	echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#etc006EnviarEmail-{$g['r'][3]}').unbind('click').bind('click', function(data) {
			if ($js_T003_Id || $js_T005_Id) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/' + '{$ven}' + '/form_func-ajax/verificarParcelas/?ajax=true&'+ '{$enviarParameter}' + '&callback=?', function(request1) {
					$('#{$g['divId']}').hideLoading();
					if(request1.code) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/' + '{$ven}' + '/form_func-ajax/verificaAlcadaNovo/?ajax=true&'+ '{$enviarParameter}' + '{$callback}', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '{$enviarParameter}', [900,550]);
							} else if (request.code == 'erro') {
								dialogConfirm('Erro', request.data);
							} else if (request.code == 'analise') {
								dialogConfirm('Ok', request.data);
							} else {
								var \$buttons = {
									"Sim": function() {
										$('#{$g['divId']}').showLoading();
										$(this).dialog("destroy").remove();
										$.getJSON('/ven/' + '{$ven}' + '/form_func-ajax/enviarParaAnalise/?ajax=true&'+ '{$enviarParameter}' + '&alcadaUpdate=' + '{$alcada}' + '&callback=?', function(request2) {
											$('#{$g['divId']}').hideLoading();
											if (request2.code) {
												var \$buttons = {
													"Fechar": function() {
														$(this).dialog("destroy").remove();
														fecharJanela('{$g['divId']}');
														divRefresh('{$g['divIdParent']}', true);
													} 
												};
												dialogConfirm('Ok', request2.data, \$buttons);
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												dialogConfirm('Erro', request2.data, \$buttons);
											}
										});
									},
									"Não": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Enviar E-mail', request.data, \$buttons);
							}
						});
					} else {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request1.data, \$buttons);
					}
				});
			} else {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '{$enviarParameter}', [900,550]);
			}
		});
		$('#etc006VerificarEmails-{$g['r'][3]}').unbind('click').bind('click', function(data) {
			$('#{$g['divId']}').showLoading();
			$.getJSON('/etc/etc006/grid_func-ajax/atualizarEmails/?ajax=true&callback=?', function(request) {
				$('#{$g['divId']}').hideLoading();
				var buttons = {
					"Ok": function() {
						$(this).dialog("destroy").remove(); 
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					}
				}
				dialogConfirm('Atualizar Emails', request.data, buttons);
			});
		});
		$('#etc006AssEmails-{$g['r'][3]}').unbind('click').bind('click', function(data) {
			$('#{$g['divId']}').showLoading();
			$.getJSON('/etc/etc006/grid_func-ajax/associarEmails/?ajax=true&select=' + encodeURIComponent('{$sql_}') + '&tela=etc006AssEmails-{$g['r'][3]}&callback=?', function(request) {
				$('#{$g['divId']}').hideLoading();
				var buttons = {
					"Ok": function() {
						$(this).dialog("destroy").remove(); 
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					}
				}
				dialogConfirm('Atualizar e-mails', request.data, buttons);
			});
		});
		$('#etc006DelSelected-{$g['r'][3]}').unbind('click').bind('click', function(data) {
			var buttons1 = {
				"Ok": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/etc/etc006/grid_func-ajax/excluirMarcados/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var buttons = {
							"Ok": function() {
								$(this).dialog("destroy").remove();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								}
							}
						}
						dialogConfirm('Atualizar Emails', request.data, buttons);
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			};
			dialogConfirm('Excluir e-mails?', 'Deseja excluir os e-mails marcados?', buttons1);
		});
		$('#etc006DelSpamSelected-{$g['r'][3]}').unbind('click').bind('click', function(data) {
			var buttons1 = {
				"Ok": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/etc/etc006/grid_func-ajax/excluirSpamsMarcados/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var buttons = {
							"Ok": function() {
								$(this).dialog("destroy").remove();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								}
							}
						}
						dialogConfirm('Atualizar Emails', request.data, buttons);
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			};
			dialogConfirm('Excluir e-mails?', 'Deseja excluir os spams marcados?<br><br><span style="color: var(--BGVermelho);">Atenção - Os e-mails serão removidos permanentemente!</span>', buttons1);
		});
		$('#etc006DelUnselected-{$g['r'][3]}').unbind('click').bind('click', function(data) {
			var buttons1 = {
				"Ok": function() {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/etc/etc006/grid_func-ajax/excluirDesmarcados/?ajax=true&extra={$extra_}&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var buttons = {
							"Ok": function() {
								$(this).dialog("destroy").remove();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
								}
							}
						}
						dialogConfirm('Atualizar Emails', request.data, buttons);
					});
					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			};
			dialogConfirm('Excluir e-mails?', 'Deseja excluir os e-mails desmarcados?', buttons1);
		});
		$('#etc006MarcarLidos-{$g['r'][3]}').unbind('click').bind('click', function(data) {
				$.getJSON('/etc/etc006/grid_func-ajax/marcaLidos/?ajax=true&extra={$extra_}&callback=?', function(request) {
				divRefresh('{$g['divId']}');
				});
		});
		$('#etc006MarcarNaoLidos-{$g['r'][3]}').unbind('click').bind('click', function(data) {
				$.getJSON('/etc/etc006/grid_func-ajax/marcaNaoLidos/?ajax=true&extra={$extra_}&callback=?', function(request) {
				divRefresh('{$g['divId']}');
				});
		});
		$('#etc006DesmarcarTodos-{$g['r'][3]}').unbind('click').bind('click', function(data) {
				$.getJSON('/etc/etc006/grid_func-ajax/desmarcarTodos/?ajax=true&extra={$extra_}&callback=?', function(request) {
				divRefresh('{$g['divId']}');
				});
		});
		$('#etc006MarcarComoLidos-{$g['r'][3]}').unbind('click').bind('click', function(data) {
				$.getJSON('/etc/etc006/grid_func-ajax/marcaSelecionadosComoLidos/?ajax=true&extra={$extra_}&callback=?', function(request) {
				divRefresh('{$g['divId']}');
				});
		});
		$('#etc006MarcarComoNaoLidos-{$g['r'][3]}').unbind('click').bind('click', function(data) {
				$.getJSON('/etc/etc006/grid_func-ajax/marcaSelecionadosComoNaoLidos/?ajax=true&extra={$extra_}&callback=?', function(request) {
				divRefresh('{$g['divId']}');
				});
		});
		$('#vincularOrcamento').unbind('click').bind('click', function(data) {
			// vincularOrcamento('{$g['divId']}', '{$g['divIdRoot']}', '{$r_T003_Id}', '{$D024_Id}');
    		abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Responder', '/etc/etc006/grid/etc006GridInbox/', '&T003_Id=' + encodeURIComponent('{$r_T003_Id}') + '&D024_Id=' + encodeURIComponent('{$D024_Id}'), [1000,500]);
		});
		function gridTheadCallback() {
			$('#{$g['divId']} .gridTheadTh > .ui-icon-pencil:eq(0)')
				.removeAttr('onclick')
				.click(function() {
					var \$buttons = {
						"Marcar Todos": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/etc/etc006/grid_func-ajax/marcarTodos/?ajax=true&extra={$extra_}&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}');
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Desmarcar todos": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/etc/etc006/grid_func-ajax/desmarcarTodos/?ajax=true&extra={$extra_}&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}');
								}
							});
							$(this).dialog("destroy").remove();
						} ,
						"Cancelar": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Marcar/Desmarcar Todos', 'Você deseja marcar ou desmarcar todos? ', \$buttons);
					return false;
			});
		};
	} </script></div>
EOT;
   //}
//}
//}

if($g['r'][3] == 'etc006GridSpam') {
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			if('{$r_processar}' == '1'){
				divSetarAcaoId('{$g['divId']}', '0', 'processar');
				$('#{$g['divId']}').showLoading();
				setTimeout(function(){
					$('.loading-indicator-overlay').css({'opacity': '1', 'background': 'background: rgba(255, 255, 255, 0.5)'}).append('<div style="top: 55%; position: relative; text-align: center; color: blue;">Verificando spam\'s, por favor aguarde.</div>');
				}, 100);
				$.getJSON('/etc/etc006/grid_func-ajax/associarEmails/?ajax=true&select=' + encodeURIComponent('{$sql_}') + '&tela=etc006AssEmails-{$g['r'][3]}&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					divRefresh('{$g['divId']}', true);
				});
			}
		} </script></div>
EOT;
}



/**
* Totalizadores
*/
/*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct T139_Id)", $from, gRetirarGroupBy($extra));
if($g['r'][3]!='etc006Xml'){
	echo <<<EOT
	<div class="buttonsBottomTop">
		<span class="item" style="text-align:right;">Emails<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>	
	</div>
EOT;
}*/

/**
* Define ação do click de abertura (ao clicar em uma linha do grid)
*/
echo <<<EOT
	<script type="text/javascript">
		$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'SPAN') { return true; } 
			if (data.target.nodeName == 'SPAN' && $(data.target).hasClass('ui-icon')) { return true; }
			$(this).closest('tr').removeClass('linhaTarefaNaoLida').addClass('linhaTarefaLida');
			var id = $(this).closest('tr').attr('id').replace('T139_Id-', '');
			$(this).closest('tr').find('span').css('font-weight', 'normal');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Email', '/etc/etc006/content/etc006ContentEmail/', '&pasta=E&acaoId=' + encodeURIComponent(id), [900,550]);
		});
	</script>
EOT;

 		/**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
       /* echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:12px"><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/
/**
* Geração: Grid
*/
$gridTabela = $g['ui']->grid($grid, $geradoDados);
$g['smarty']->assign('thead', $gridTabela['thead']);
$g['smarty']->assign('tbody', $gridTabela['tbody']);
$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
gBotaoAuditoria($from);
if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
