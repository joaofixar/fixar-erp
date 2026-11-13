<?php
namespace hardness;

require_once('grid_func-callback.php');
require_once('grid_func-js.php');

$_REQUEST['idTabForm4'] = isset($_REQUEST['idTabForm4']) ? $_REQUEST['idTabForm4'] : false;

switch($g['r'][3]){
	case 'cad020lista':
		$filtroObrigatorio = true;

		$C008_Id = $r_acaoId;
	    $filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
			$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
			$filtro = '';
		}

		$from = 'D001';
		$extra = <<<EOT
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D024_Id=D049_D024_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join D037 on D001_D037_Id=D037_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			left join D005 on D001_D005_Id=D005_Id
			left join D072 on D072_D001_Id=D001_Id
			where (D009_C004_Id='{$g['c004']['C004_Id']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			{$filtro}
			group by D009_Id

EOT;
		// Foi retirado o left join T066 on T066_D009_Id=D009_Id e com left join D004 on D004_Id=T066_D004_Id pois eles tem uma relação de 1 para muitos fazendo com que somasse errado o contador
		
		$extraTotal = <<<EOT
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D024_Id=D049_D024_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join D037 on D001_D037_Id=D037_Id
			left join D005 on D001_D005_Id=D005_Id
			left join D072 on D072_D001_Id=D001_Id
			where (D009_C004_Id='{$g['c004']['C004_Id']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			{$filtro}
EOT;

  		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D049_Modelo' => array('titulo' => 'Sub-item', 'tipo' => 'texto'),
            'D001_Codigo_Barras,D001_Codigo_Barra1' => array('titulo' => 'EAN', 'tipo' => 'texto'),
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto'),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estoque', 'tipo' => 'numero'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false),
			'D005_Classificacao_Fiscal' => array('titulo' => 'NCM'),
			'D009_Data_Reprocessa_Historico' => array('titulo' => 'Atual.Estoque', 'tipo' => 'data'),

        );

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
 		    if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
  		       return 'linhaCinza';
 		    }
 		    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
  		       return 'linhaVermelho';
 		    }
		}


        /**
	    * Botões (Versão Acima do GRID)
	    */
	    // <button type="button" id="cad020DesmarcarTodos">Desmarcar Todos</button>

	    /*echo <<<EOT
	        <div class="buttonsBottomTop">
	        <button type="button" id="cad020ImprimirEtiquetas">Imprimir Etiquetas</button>
	        </div>
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            $('#cad020ImprimirEtiquetas').unbind('click').bind('click', function(data) {
	                $.getJSON('/cad/cad020/grid_func-ajax/cad020VerificaProdutosImprimir/?ajax=true&callback=?', function(request) {
	                    if(request.code){
	                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiquetas', '/cad/cad020/content/cad020ContentImprimirEtiquetas/', '', [800,500]);
	                    }else{
	                        var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	                        dialogConfirm('Erro', 'Não existem produtos selecionados para impressão.', \$buttons);
	                    }
	                });
	            });
	            $('#cad020DesmarcarTodos').unbind('click').bind('click', function(data) {
	                $('#{$g['divId']}').showLoading();
	                $.getJSON('/cad/cad020/grid_func-ajax/cad020DesmarcarImprimir/?ajax=true&callback=?', function(request) {
	                    $('#{$g['divId']}').hideLoading();
	                    divRefresh('{$g['divId']}', true);
	                });
	            });
	        } </script></div>
EOT;*/
		/**
		 * Definição do grid tabela
		 */

	        //array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;'),
			if($g['C031']['impressaoEtiquetasPadraoJava'] == 'S'){
				$grid[] = array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 650), 'style' => 'width: 1%;', 'colunaExibir' => false);
			}else{
				$grid[] = array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produtoTxt/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 650), 'style' => 'width: 1%;', 'colunaExibir' => false);
			}
	        $grid[] = array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'D009_Flag_Imprimir_Etiqueta', 'colunaTitulo' => 'Imprimir', 'callback' => 'validaCampoImprimir', 'style' => 'width:50px', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'campoImpTag', 'colunaTitulo' => '-', 'colunaAlinhar' => 'center', 'style' => 'width: 1%', 'callback' => 'campoImpTag', 'colunaTipo' => 'livre', 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'T066_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false);
			$grid[] = array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px');
			$grid[] = array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'font-weight:normal');
	        $grid[] = array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'style' => 'font-weight:normal');
	        $grid[] = array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'style' => 'font-weight:normal');
	        $grid[] = array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:normal');
	        $grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca');
	        $grid[] = array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Unidade', 'colunaAlinhar' => 'center');
		    $grid[] = array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque');
		    $grid[] = array('colunaValor' => 'D009_Quantidade_OE', 'colunaTitulo' => 'Estoque OE', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueOE','colunaExibir' => false);
		    $grid[] = array('colunaValor' => 'percentualEstoqueFisico', 'colunaTitulo' => '%Fis', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'percentualEstoqueFisico', 'colunaCasasDecimais' => '2');
	        $grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq.Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0));
		    $grid[] = array('colunaValor' => 'percentualEstoqueLiquido', 'colunaTitulo' => '%Liq', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'percentualEstoqueLiquido', 'colunaCasasDecimais' => '2');
	        $grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq.Loja', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%');
	        $grid[] = array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qte.OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%');
			$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq.Fora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueFora');
	        $grid[] = array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo unitário', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'D009_Valor_Custo_Unitario*D009_Quantidade_Estoque', 'colunaTitulo' => 'Custo totat', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo' => 'Peso Liq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true);
	        $grid[] = array('colunaValor' => 'D001_Peso_Unitario_Bruto', 'colunaTitulo' => 'Peso Brt', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true);
	        $grid[] = array('colunaValor' => '(select group_concat(D004_Local SEPARATOR \'<br /> \') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local', 'colunaTitulo' => 'Locação', 'callback' => 'campoLocacao', 'colunaExibir' => false);
	        $grid[] = array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => '', 'colunaExibir' => false);
			$grid[] = array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => '', 'colunaExibir' => false);
			$grid[] = array('colunaValor' => 'D009_Data_Reprocessa_Historico', 'colunaTitulo' => 'Atual.Estoque', 'callback' => 'GCorrigeData','colunaExibir' => true);


		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
	    $novoIdTelaForm = uniqid();
	    echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			    var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
			        // somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var D001_Id = id[0].replace(/D001_Id-/, '');
					var D024_Id = id[1].replace(/D024_Id-/, '');
			    	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad020/content/cad020form2Comum/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

			/*echo <<<EOT
		        <div class="buttonsBottomTop">
	        		<button type="button" id="est003ImpNovasEtiq">Imprimir Novas Etiquetas</button>
					<button type="button" id="cad020PrecosGridAtualizarEstoqueRapido">Atualizar Estoque Rápido</button>
				</div>
EOT;*/

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);

		/**
        * Totais
        */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
            array('nome' => 'Estoque_Fisico', 'valor' => 'D009_Quantidade_Estoque', 'titulo' => 'Estoque físico'),
            array('nome' => 'Estoque_Liquido', 'valor' => 'D009_Quantidade_Estoque_Liquido', 'titulo' => 'Estoque líquido'),
            array('nome' => 'Quantidade_OC', 'valor' => 'D009_Quantidade_OC', 'titulo' => 'Quantidade OC'),
            array('nome' => 'Estoque_Fora', 'valor' => 'D009_Quantidade_Estoque_Fora', 'titulo' => 'Estoque fora'),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Imprimir Novas Etiquetas', 'id' => 'est003ImpNovasEtiq'),
			array('titulo' => 'Atualizar Estoque Rápido', 'id' => 'cad020PrecosGridAtualizarEstoqueRapido'),
		);
		echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$etapas = round($valorTotal / 100) ; 

		$sql_ = urlencode(base64_encode($geradoSql));
		$extra_ = base64_encode($extra);

		$estoque = 1;
		$estoqueLiquido = 1;

		$optionValue = "<option value=>[Escolha a impressora]</option>";
		$D175 = mysql_query("SELECT D175_Id, D175_Nome FROM D175 ");

		while($mD175 = mysql_fetch_array($D175)){

			$optionValue .= "<option value=" . $mD175['D175_Id'] . ">" . $mD175['D175_Nome'] . "</option>";
		}

		$extraImpressao = gRetirarGroupBy(gInsertExtraWhere($extra, "D009_Flag_Imprimir_Etiqueta = 'S'"));
		$where = urlencode(base64_encode($extraImpressao));

		$select = '<select style="width:250px; height:24px" id="impressoras">' . $optionValue . '</select>';
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		        $('#est003ImpNovasEtiq').unbind('click').bind('click', function(data) {
		        	var buttons = { 
						"Produto": function() { 
							var impressora = $(this).parent().find('select').val();
							var where = "$where";
							$.ajax('/sistema/impressao/etiquetas/produto/?ajax=true&acaoId=0&imprimir=produto&where="' + where + '"&impressora=' + encodeURIComponent(impressora));
							$(this).dialog("destroy").remove();
							dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
						},
						"Locação": function() { 
							var impressora = $(this).parent().find('select').val();
							var where = "$where";
							$.ajax('/sistema/impressao/etiquetas/produto/?ajax=true&acaoId=0&imprimir=locacao&where="' + where + '"&impressora=' + encodeURIComponent(impressora));
							$(this).dialog("destroy").remove();
							dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
						},
						"Locação - Caixa": function() { 
							var impressora = $(this).parent().find('select').val();
							var where = "$where";
							$.ajax('/sistema/impressao/etiquetas/produto/?ajax=true&acaoId=0&imprimir=locacaoc&where="' + where + '"&impressora=' + encodeURIComponent(impressora));
							$(this).dialog("destroy").remove();
							dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
						},
						"Gondola": function() { 
							var impressora = $(this).parent().find('select').val();
							var where = "$where";
							$.ajax('/sistema/impressao/etiquetas/produto/?ajax=true&acaoId=0&imprimir=gondola&where="' + where + '"&impressora=' + encodeURIComponent(impressora));
							$(this).dialog("destroy").remove();
							dialogConfirm('Impressora', 'Etiqueta enviada para a fila de impresão.');
						},
						"Fechar": function() {
							$(this).dialog("destroy").remove(); 
						}
					};
					dialogConfirm('Imprimir Novas Etiquetas (Itens Selecionados)', '<div id="est003ImpNovasEtiqJava">$select</div>', buttons);
				});
				$('#cad020PrecosGridAtualizarEstoqueRapido').unbind('click').bind('click', function() {
					var data    = new Date();
					var horIni  = data.getHours();          // 0-23
					var minIni  = data.getMinutes();        // 0-59
					var segIni  = data.getSeconds();        // 0-59
					var buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$('#{$g['divId']}').showLoading();
							cad020PrecosAtualizarEstoqueRapidoLoop(idProgressBar,1,0,0,horIni,minIni,segIni);
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
				});
				function cad020PrecosAtualizarEstoqueRapidoLoop(idProgressBar, etapa, erro, ok, horIni, minIni, segIni){
					$.getJSON('/cad/cad020/grid_func-ajax/atualizarEstoqueRapido/?extraSql={$extra_}&ajax=true&callback=?'+'&valorTotal=$valorTotal&etapas=$etapas&etapa=' + etapa, function(request) {
						var data   = new Date();
						var horFim = data.getHours();          // 0-23
						var minFim = data.getMinutes();        // 0-59
						var segFim = data.getSeconds();        // 0-59
						var horaInicio = ("00" + horIni).slice(-2) + ':' + ("00" + minIni).slice(-2) + ':' + ("00" + segIni).slice(-2);
						var horaFim    = ("00" + horFim).slice(-2) + ':' + ("00" + minFim).slice(-2) + ':' + ("00" + segFim).slice(-2);

						var duracao    = ( ( (horFim * 3600) + (minFim * 60) + segFim ) - ( (horIni * 3600) + (minIni * 60) + segIni ) ) ;
						var horDur = Math.floor(duracao/3600);
 						var minDur = Math.floor((duracao - (horDur*3600))/60);
 						var segDur = duracao - (Math.floor(duracao/60)*60);
						var horaDuracao= ("00" + horDur).slice(-2) + ':' + ("00" + minDur).slice(-2) + ':' + ("00" + segDur).slice(-2);

						var previsao = ( duracao / etapa ) * $etapas + ( (horIni * 3600) + (minIni * 60) + segIni );
						var horPrev = Math.floor(previsao/3600);
 						var minPrev = Math.floor((previsao - (horPrev*3600))/60);
 						var segPrev = previsao - (Math.floor(previsao/60)*60);
						var horaPrevisao= ("00" + horPrev).slice(-2) + ':' + ("00" + minPrev).slice(-2) + ':' + ("00" + segPrev).slice(-2);

						if (request.etapa<=$etapas && request.concluido == 0) {
							erro += request.erro;
							ok += request.ok;
							$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br/><br/>Hora inicio: ' + horaInicio + '<br/>Hora atual: ' + horaFim + '<br/>Duracao: ' + horaDuracao + '<br/>Previsão: ' + horaPrevisao + '<br/> <font color="red">' + request.erros + '</font></b>' );
							cad020PrecosAtualizarEstoqueRapidoLoop(idProgressBar,request.etapa, erro, ok, horIni, minIni, segIni);
						}else{
							erro += request.erro;
							ok += request.ok;
							$('#dc-' + idProgressBar).dialog("destroy").remove();
							dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br/><br/>Hora inicio: ' + horaInicio + '<br/>Hora atual: ' + horaFim + '<br/>Duracao: ' + horaDuracao + '<br/>Previsão: ' + horaPrevisao + '<br/> <font color="red">' + request.erros + '</font></b>');
							if (request.code) {
								divRefresh('{$g['divId']}');
							} else {
								dialogConfirm('Erro!', request.data);
							}
						} 
					})
					.error(function() {
						$('#dc-' + idProgressBar).dialog("destroy").remove();
						dialogConfirm('ERRO!','<b><font style="color: var(--BGVermelho)">Ocorreu um erro de timeout no processamento, favor tentar novamente ou informar o suporte técnico</font></b>');
						divRefresh('{$g['divId']}');
					});
				}
			} </script></div>
EOT;

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
	break;

	/**
	 * Tab Comercial
	 */
	case 'cad020comercial':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D001_Id = $r_acaoId;

	    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id=$D001_Id");
	    $mD001=mysql_fetch_array($D001);

	    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
	    $mC008=mysql_fetch_array($C008);

	    $C004=mysql_query("select C004_Flag_Decimais_Sugestao_Compra from C004 where C004_Id={$g['empresaAtual']}");
	    $mC004=mysql_fetch_array($C004);

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'validaCampoExcluir', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir'=>false, 'chavePrimaria' => true),
			array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id',  'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => $mC008['C008_Apelido_Marca'], 'callback' => 'validaMarca', 'style' => 'font-weight:normal'),
			//array('colunaValor' => 'D049_Flag_Padrao', 'colunaTitulo' => '', 'callback'=>'validarFlagPadrao', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Quantidade_Separado_3(D009_Id) Separado', 'colunaTitulo' => 'Separado', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Quantidade_OC(D009_Id) OC', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Compra Min', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaCasasDecimais' => '2', 'colunaTitulo' => 'Estoque Min', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Frequencia_Venda', 'colunaCasasDecimais' => '2', 'colunaTitulo' => 'Freq.Venda', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D041_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			//array('colunaValor' => 'D082_Marca', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => '', 'colunaExibir'=>false),
		);



		$from = 'D009';

		$extra = <<<EOT
			left join D049 on D049_Id=D009_D049_Id
	        left join D001 on D001_Id=D049_D001_Id
	        left join D024 on D024_Id=D049_D024_Id
	        left join D041 on D041_D049_Id=D049_Id and (ifnull(D041_C004_Id,0)<=0 or D041_C004_Id={$g['empresaAtual']})
	        left join D082 on D049_D082_Id=D082_Id
	        where D001_Id='$D001_Id'
	        and D009_C004_Id={$g['empresaAtual']}
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		aoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	/**
	 * Tab Estoque
	 */
	case 'cad020estoque':
		//$C008_Id = $_REQUEST['C008_Id'];

		$D001_Id = $r_acaoId;

	    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id=$D001_Id");
	    $mD001=mysql_fetch_array($D001);

	    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
	    $mC008=mysql_fetch_array($C008);

	    $C004=mysql_query("select C004_Flag_Decimais_Sugestao_Compra from C004 where C004_Id={$g['empresaAtual']}");
	    $mC004=mysql_fetch_array($C004);

		/**
		 * Definição do grid tabela
		 */

		$grid = array(
			array('colunaValor' => '', 'colunaTipo' => 'livre', 'callback'=>'validaCampoExcluir'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => $mC008['C008_Apelido_Marca'], 'callback' => 'validaMarca'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Separado_3(D009_Id) Separado', 'colunaTitulo' => 'Separado', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_OC(D009_Id) OC', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaTitulo' => 'Min.', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Maxima_Estoque', 'colunaTitulo' => 'Max.', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => 'Freq.', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D009_Custo_Unitario(D009_Id) Custo', 'colunaTitulo' => '', 'colunaExibir' => false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D041_Valor_Base', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D041_Valor_Preco_Consumidor', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D041_Valor_Preco_Revenda', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => '', 'colunaExibir' => false),
		);



		$from = 'D009';

		$extra = <<<EOT
            left join D049 on D049_Id=D009_D049_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D024 on D024_Id=D049_D024_Id
            left join D041 on D041_D049_Id=D049_Id and (ifnull(D041_C004_Id,0)<=0 or D041_C004_Id={$g['empresaAtual']})
            left join D082 on D049_D082_Id=D082_Id
            where D001_Id='$D001_Id'
            and D009_C004_Id={$g['empresaAtual']}
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
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
	break;

	/**
	 * Tab Custos
	 */
	case 'cad020custos':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D001_Id = $r_acaoId;

	    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id=$D001_Id");
	    $mD001=mysql_fetch_array($D001);

	    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
	    $mC008=mysql_fetch_array($C008);

	    $C004=mysql_query("select C004_Flag_Decimais_Sugestao_Compra from C004 where C004_Id={$g['empresaAtual']}");
	    $mC004=mysql_fetch_array($C004);

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => '', 'colunaTitulo' =>'', 'callback' => 'validaCampoExcluir', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>$mC008['C008_Apelido_Marca'], 'callback' => 'validaMarca'),
			array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' =>'Trabalho', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTipo' =>'livre', 'colunaTitulo' =>'ICF', 'callback' => 'validaICF', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'if(D049_Flag_Tipo="CP",0,D082_Percentual_Acrescimo) D082_Percentual_Acrescimo', 'colunaTitulo' =>'%', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Valor_Custo_Compra', 'colunaTitulo' =>'Custo', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' =>'Médio', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_OC(D009_Id) OC', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Custo_Unitario(D009_Id) Custo', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D041_Valor_Base', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D041_Valor_Preco_Consumidor', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D041_Valor_Preco_Revenda', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_Maxima_Estoque', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Codigo_Produto_Fornecedor', 'colunaTitulo' => '', 'colunaExibir' => false),
		);


		$from = 'D009';

		$extra = <<<EOT
            left join D049 on D049_Id=D009_D049_Id
            left join D001 on D001_Id=D049_D001_Id
            left join D024 on D024_Id=D049_D024_Id
            left join D041 on D041_D049_Id=D049_Id
            left join D082 on D049_D082_Id=D082_Id
            where D001_Id='$D001_Id'
            and D009_C004_Id={$g['empresaAtual']}
            group by D009_Id
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
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
	break;

	/**
	 * Tab SUBSTITUIÇÃO
	 */
	case 'substituicao':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D001_Id = $r_acaoId;

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => '', 'colunaTitulo' =>'', 'callback' => 'validaCampoExcluir', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D017_Data_Inclusao', 'colunaTitulo' =>'Data', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D017_Codigo_Antigo', 'colunaTitulo' =>'Substituição'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' =>'Usuário'),

		);

		$from = 'D017';

		$extra = <<<EOT
            left join D001 on D001_Id=D017_D001_Id
            left join C007 on D017_C007_Id=C007_Id
            where D017_D001_Id='$D001_Id'
            order by D017_Codigo_Antigo
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$r_idTabForm4 = (isset($r_idTabForm4)) ? $r_idTabForm4 : false;
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$r_idTabForm4}', id);
					divRefresh('{$r_idTabForm4}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));

		if($D001_Id){
			$g['smarty']->display('grid.tpl');
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
	break;

	/**
	 * Tab Empresas
	 */
	case 'cad020empresas':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D001_Id = $r_acaoId;

		/**
		 * Definição do grid tabela
		 */
	    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id='$D001_Id'");
	    $mD001=mysql_fetch_array($D001);

	    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
	  	$mC008=mysql_fetch_array($C008);

		$grid = array(
			array('colunaValor' => '', 'colunaTitulo' =>'', 'callback' => 'validaCampoExcluir', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' =>'Código'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' =>'Fornecedor'),
			array('colunaValor' => 'D083_Flag_Fornecedor', 'colunaTitulo' =>'F'),
			array('colunaValor' => 'D083_Flag_Cliente', 'colunaTitulo' =>'C'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>$mC008['C008_Apelido_Marca']),
			array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' =>'Código produto'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D083_Quantidade_Minima_Compra', 'colunaTitulo' => '', 'colunaExibir' => false),
			array('colunaValor' => 'D083_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		);

		$from = 'D083';

		$extra = <<<EOT
            left join D024 on D024_Id=D083_D024_Id
            left join D049 on D049_Id=D083_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            where D083_D001_Id='$D001_Id'
            order by D024_Nome_Fantasia
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesEsq .= '<button type="button" style="margin:0 0px 20px 10px;">Incluir</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));

		if($D001_Id){
			$g['smarty']->display('buttons-floatBottom.tpl');
			$g['smarty']->display('grid.tpl');
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
	break;

	/**
	 * Tab Histórico
	 */
	case 'cad020historico':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D001_Id = $r_acaoId;

		/**
		 * Definição do grid tabela
		 */
	    $cClausula  = " select ";
	    $cClausula .= " D001_Codigo_Produto, ";
	    $cClausula .= " D001_Descricao_Produto,";
	    $cClausula .= " D009_Id";
	    $cClausula .= " from D001";
	    $cClausula .= " left join D049 on D049_D001_Id=D001_Id";
	    $cClausula .= " left join D009 on D009_D049_Id=D049_Id";
	    $cClausula .= " where D049_Id='$D001_Id'";
	    $cClausula .= "   and D009_C004_Id={$g['empresaAtual']}";

	    $D001 = mysql_query($cClausula);
		$mD001=mysql_fetch_array($D001);
		$D009_Id = (isset($mD001['D009_Id']) ? $mD001['D009_Id'] : '');

		$grid = array(
			array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' =>'Data', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'if((T001_Flag_Operacao=\'E\'
									or T001_Flag_Operacao=\'B\')
									and T001_T008_Id is null,Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa) Cliente', 'colunaTitulo' =>'Cliente'),
			array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' =>'Descrição'),
			array('colunaValor' => 'T001_Valor_Custo_Unitario', 'colunaTitulo' =>'T001_Valor_Custo_Unitario', 'colunaAlinhar' => 'right','colunaExibir' => false),
			array('colunaValor' => 'Custo', 'colunaTitulo' =>'Custo', 'colunaAlinhar' => 'right','colunaTipo' => 'livre'),
			array('colunaValor' => 'T001_Valor_Custo_Medio_Unitario', 'colunaTitulo' =>'Médio', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T001_Valor_Preco_Unitario', 'colunaTitulo' =>'Preço', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'IPV', 'colunaTitulo' => 'IPV', 'callback' => 'validaIpvHistorico', 'colunaAlinhar' => 'right','colunaTipo' => 'livre'),
			array('colunaValor' => '(T001_Quantidade * if(T001_Flag_Operacao=\'S\',-1,1)) T001_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T001_Flag_Operacao', 'colunaTitulo' => '', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T001_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T001_Flag_MMF', 'colunaTitulo' => 'F', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T001_Flag_Atualiza_Custo', 'colunaTitulo' => 'C', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T001_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => 'T001_T007_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => 'T001_T013_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => '(T001_Quantidade * if(T001_Flag_Operacao=\'S\',-1,1)) T001_Quantidade', 'colunaTitulo' => '', 'colunaExibir'=>false),
			array('colunaValor' => 'T001_Flag_Devolucao', 'colunaTitulo' => '', 'colunaExibir'=>false),
		);

		$from = 'T001';

		$extra = <<<EOT
    		left join D024 as Forn on Forn.D024_Id=T001_D024_Id
			left join D024 as Cli on Cli.D024_Id=T001_D024_Id_Cliente
			where T001_D009_Id='$D009_Id'
			order by T001_Data_Lancamento desc,T001_Id desc
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));

		if($D001_Id){
			$g['smarty']->display('grid.tpl');
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
	break;

	/**
	 * Tab Montagem
	 */
	case 'cad020montagem':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D049_Id = $r_acaoId;

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => '', 'colunaTitulo' =>'', 'callback' => 'validaCampoExcluir', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D042_Quantidade', 'colunaTitulo' =>'Quant.', 'colunaEditar' => true, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' =>'Produto'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' =>'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'Marca'),
			array('colunaValor' => 'D009_Valor_Custo_Compra', 'colunaTitulo' =>'D009_Valor_Custo_Compra','colunaExibir' => false),
			array('colunaValor' => 'Custo Compta', 'colunaTitulo' =>'Custo Compra', 'callback'=>'formatarNumeroCustoCompra', 'colunaAlinhar' => 'right','colunaTipo' => 'livre'),
			array('colunaValor' => 'Custo Total', 'colunaTitulo' =>'Custo Total', 'callback'=>'validaCustoTotalMontagem', 'colunaAlinhar' => 'right','colunaTipo' => 'livre'),
			array('colunaValor' => 'D049_Valor_Preco_Tabela*(1-(D049_Percentual_Desconto_Tabela/100)) as D049_Valor_Preco_Tabela', 'colunaTitulo' =>'D049_Valor_Preco_Tabela', 'colunaExibir' => false),
			array('colunaValor' => 'Custo Tabela', 'colunaTitulo' =>'Custo Tabela', 'callback'=>'formatarNumeroCustoTabela','colunaTipo' => 'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'Custo Total', 'colunaTitulo' =>'Custo Total', 'callback' =>'validaCustoTotal2Montagem','colunaTipo' => 'livre', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' =>'Estoque', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'floor(D009_Quantidade_Estoque_Real/D042_Quantidade) as Quantidade_Minima', 'colunaTitulo' =>'Disponib.', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D042_Observacao', 'colunaTitulo' =>'Observação', 'callback'=>'validaObervacaoMontagem'),
			array('colunaValor' => 'D042_Id', 'colunaTitulo' =>'', 'colunaExibir' => false),
			array('colunaValor' => '(select D041_Valor_Base from D041 where D041_D049_Id=D049_Id limit 1) as Preco_Unit', 'colunaTitulo' =>'', 'colunaExibir' => false),
			array('colunaValor' => '((select D041_Valor_Base from D041 where D041_D049_Id=D049_Id limit 1)*D042_Quantidade) as Preco_Total', 'colunaTitulo' =>'', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' =>'', 'colunaExibir' => false),
		);

		$from = 'D042';

		$extra = <<<EOT
      		left join D049 on D049_Id=D042_D049_Id_Itens
    		left join D082 on D049_D082_Id=D082_Id
		 	left join D001 on D001_Id=D049_D001_Id
    		left join D009 on D009_D049_Id=D042_D049_Id_Itens and D009_C004_Id={$g['empresaAtual']}
	    	where D042_D049_Id='$D049_Id'
	    	and D042_C004_Id={$g['empresaAtual']}
	    	and D009_C004_Id={$g['empresaAtual']}
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesEsq .= '<button type="button" style="margin:0 0px 20px 10px;">Incluir</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));

		if($D049_Id){
			$g['smarty']->display('grid.tpl');
			$g['smarty']->display('buttons-floatBottom.tpl');
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
	break;

	/**
	 * Tab Locação
	 */
	case 'cad020locacao':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D049_Id = $r_acaoId;

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T066_Id', $g['divId'])),
			array('colunaValor' => "T066_Id", 'colunaTitulo' =>'ID', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D004_Id', 'colunaTitulo' =>'Locação', 'colunaExibir' => false),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' =>'Locação'),
			array('colunaValor' => 'D004_Id', 'colunaTitulo' =>'Locação', 'colunaExibir' => false),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' =>'Estoque', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D048_Estoque', 'colunaTitulo' =>'', 'colunaExibir'=>false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'D009_Id', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T066_Id', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' =>'', 'colunaExibir'=>false),

		);

		$from = 'T066';

		$extra = <<<EOT
      		left join D009 on T066_D009_Id=D009_Id
			left join D048 on D048_Id=T066_D048_Id
			left join D004 on D004_Id=T066_D004_Id
		    where D009_D049_Id='$D049_Id'
			and D009_C004_Id={$g['empresaAtual']}
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T066_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/cad/cad002/content/cad002ContentCadastroLocacaoEditar/','&D049_Id=' + encodeURIComponent($r_acaoId) + '&acaoId=' + encodeURIComponent(id), [400,200]);
				});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		if($r_acaoId){
			/**
			* Botões (Versão Acima do GRID)
			*/
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="cad002IncluirLocacao">Incluir</button>
				</div>
			    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#cad002IncluirLocacao').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/cad/cad002/content/cad002ContentCadastroLocacao/','&D049_Id=' + encodeURIComponent($r_acaoId), [400,200]);
					});
				} </script></div>
EOT;
		}
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));

		if($D049_Id){
			$g['smarty']->display('grid.tpl');
			$g['smarty']->display('buttons-floatBottom.tpl');
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
	break;

		/**
		 * Tab Pedidos
		 */
	case 'cad020pedidos':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D009_Id = $r_acaoId;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => 'T006_Id', 'colunaTitulo' =>'ID'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' =>'Data', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' =>'Nº Pedido', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' =>'Cliente'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'Marca'),
			array('colunaValor' => 'T006_Flag_Tipo_Produto', 'colunaTitulo' =>'Tp', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' =>'Quantidade', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' =>'Separado', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' =>'Estoque', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' =>'IPV', 'callback' => 'validaIpvPedidos', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'group_concat(distinct T007_Id) NF', 'colunaTitulo' =>'NF'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' =>'Vendedor'),
			array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) Status', 'colunaTitulo' =>'Status'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		);

		$from = 'T005';

		$extra = <<<EOT
		    left join D024 on D024_Id=T005_D024_Id
		    left join T006 on T006_T005_Id=T005_Id
		    left join D009 on D009_Id=T006_D009_Id
		    left join D049 on D049_Id=D009_D049_Id
		    left join D082 on D082_Id=D049_D082_Id
		    left join T008 on T008_T006_Id=T006_Id
		    left join T007 on T007_Id=T008_T007_Id and T007_Flag_Cancelada!='S'
		    left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
	    	where T006_D009_Id='$D009_Id'
			and (T006_Quantidade>0 or T006_Quantidade_Separacao>0)
			and T007_Id is null
			and T005_Flag_Status!=5
			and T006_Flag_Status!='3'
			and T005_Flag_Status!='7'
			and T005_Flag_Status!='8'
			and T005_Flag_Status!='11'
			group by T006_Id
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));

		if($D009_Id){
			$g['smarty']->display('grid.tpl');
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
	break;

	/**
	 * Tab OC
	 */
	case 'cad020oc':
		//$C008_Id = $_REQUEST['C008_Id'];
		$D009_Id = $r_acaoId;

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' =>'Data', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T011_Id', 'colunaTitulo' =>'OC'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' =>'Fornecedor'),
			array('colunaValor' => 'T012_Quantidade', 'colunaTitulo' =>'Quantidade', 'colunaAlinhar' => 'right'),
			//O campo NF não existe no select do código antigo produtos/produtos_oc.php
			//array('colunaValor' => 'NF', 'colunaTitulo' =>'NF'),
			array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' =>'Nº Pedido', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' =>'Id item pedido venda', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T012_Quantidade_Falta_Lancar(T012_Id,0) as T012_Quantidade_Pendente', 'colunaTitulo' =>'', 'colunaExibir'=>false, 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T012_Codigo_Produto', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T012_Valor_Custo_Unitario', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T012_Valor_Total_Custo', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T012_Descricao_Produto', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T012_Flag_Cancelada', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T012_Flag_Travar_Pedido', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'D049_Codigo_Produto_Fornecedor', 'colunaTitulo' =>'', 'colunaExibir'=>false),
			array('colunaValor' => 'T012_Id', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		);


		$from = 'T012';

		$extra = <<<EOT
			left join D009 on D009_Id=T012_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			left join T011 on T011_Id=T012_T011_Id
			left join T006 on T006_Id=T012_T006_Id
			left join D024 on D024_Id=T011_D024_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_Id='$D009_Id'
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));

		if($D009_Id){
			$g['smarty']->display('grid.tpl');
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
	break;

	/**
	 * Tab Certificados
	 */
	case 'certificados':
		$D001_Id = $r_acaoId;
		$D024_Id = $r_D024_Id;

		$sqlD024 = "SELECT * FROM D024 WHERE D024_Id='$D024_Id'";
		$queryD024 = mysql_query($sqlD024);
		$resultD024 = mysql_fetch_array($queryD024);

		$sqlD001 = "SELECT * FROM D001 WHERE D001_Id='$D001_Id'";
		$queryD001 = mysql_query($sqlD001);
		$resultD001 = mysql_fetch_array($queryD001);

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => '', 'colunaTitulo' =>'', 'callback' => 'validaCampoExcluir1', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'T140_Id', 'colunaTitulo' =>'ID', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T140_D024_Id', 'colunaTitulo' =>'Fornecedor', 'callback' => 'validaFornecedorCertificados'),
			array('colunaValor' => $resultD001['D001_Descricao_Produto'], 'colunaTitulo' =>'Produto', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'T140_Data_Emissao', 'colunaTitulo' =>'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T140_Data_Validade', 'colunaTitulo' =>'Validade', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T140_Certificadora', 'colunaTitulo' =>'Certificadora'),
			array('colunaValor' => 'T140_Tipo_Certificado', 'colunaTitulo' =>'Tipo'),
			array('colunaValor' => '', 'colunaTitulo' =>'Certificado', 'colunaTipo'=>'livre', 'callback'=>'abrirCertificado'),
		);

		$from = 'T140';

		$extra = <<<EOT
			where T140_D001_Id = '$D001_Id'
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T140_Id-/, '');
					// divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					// divRefresh('{$g['divId']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->display('grid.tpl');
	break;

    case 'cad020GridProdutosImprimir':
	    $C008_Id = $r_acaoId;
	    /**
	     * Definição do grid tabela
	     */
	    $grid = array(
	        array('colunaValor' => 'D009_Flag_Imprimir_Etiqueta', 'colunaTitulo' => 'Imprimir', 'callback' => 'validaCampoImprimir', 'style' => 'width:1%'),
	        array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
	        array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
	        array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
	        array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
	        array('colunaValor'=>'D009_Id', 'colunaTitulo'=>'ID', 'colunaExibir' => false),
	        array('colunaValor'=>'T066_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
	        array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código', 'style' => 'font-weight:normal'),
	        array('colunaValor'=>'D009_Quantidade_Estoque', 'colunaTitulo'=>'Estoque', 'colunaAlinhar' => 'center'),
	        array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN'),
	        array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição', 'style' => 'font-weight:normal'),
	        array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Unidade', 'colunaAlinhar' => 'center'),
	        array('colunaValor'=>'D049_Id', 'colunaTitulo'=>'D049_Id', 'colunaExibir' => false ),
	        array('colunaValor'=>'D004_Local', 'colunaTitulo'=>'Local', 'colunaExibir' => false ),
	        array('colunaValor'=>'D082_Marca', 'colunaTitulo'=>'Marca'),
	        array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'', 'colunaExibir' => false ),
	        array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'', 'colunaExibir' => false ),
	        //array('colunaValor'=>"(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo'=>'Local'),
	    );

	    $from = 'D001';
	    $extra = <<<EOT
            left join D003 on D003_Id=D001_D003_Id
            left join D049 on D049_D001_Id=D001_Id
            left join D009 on D009_D049_Id=D049_Id
            left join C008 on D001_C008_Id=C008_Id
            left join D082 on D049_D082_Id=D082_Id
            left join D024 on D024_Id=D049_D024_Id
            left join D060 on D001_D060_Id=D060_Id
            left join D002 on D001_D002_Id=D002_Id
            left join D015 on D001_D015_Id=D015_Id
            left join D037 on D001_D037_Id=D037_Id
            left join T066 on T066_D009_Id=D009_Id
            left join D004 on D004_Id=T066_D004_Id
            left join D005 on D001_D005_Id=D005_Id
            left join D072 on D072_D001_Id=D001_Id
            where (D009_C004_Id='{$g['c004']['C004_Id']}' or D009_Id is null)
            AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
            AND (D009_Flag_Imprimir_Etiqueta='S')
            group by D009_Id
EOT;

        /**
        * Filtro
        $gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            //'D001_Flag_Ativo' => array('titulo' => 'Ativo? (S/N)', 'tipo' => 'texto'),
            'D001_Flag_Ativo' => array('titulo' => 'Ativo?', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todas', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ), 'naoGerarExtra' => false),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),
        );
        */
        /**
        * Função que define classes CSS para cada linha do grid
        */
        function gridLinha($array) {
            if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
                return 'linhaCinza';
            }
            if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
                return 'linhaVermelho';
            }
        }
        /*
	        // Gera o filtro e acrescenta ao $extra
	        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	        // Imprime o filtro do GRID (normalmente executado acima do GRID)
	        echo $g['ui']->gridFiltroPrint($gridFiltro);
        */

        /**
        * Botões (Versão Acima do GRID)

	    echo <<<EOT
	        <div class="buttonsBottomTop">
	        <button type="button" id="cad020ImprimirEtiquetas">Imprimir Etiquetas</button>
	        <button type="button" id="cad020DesmarcarTodos">Desmarcar Todos</button>
	        </div>
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            $('#cad020ImprimirEtiquetas').unbind('click').bind('click', function(data) {
	                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiquetas', '/cad/cad020/content/cad020ContentImprimirEtiquetas/', '', [550,550]);
	            });
	            $('#cad020DesmarcarTodos').unbind('click').bind('click', function(data) {
	                    $('#{$g['divId']}').showLoading();
	                    $.getJSON('/cad/cad020/grid_func-ajax/cad020DesmarcarImprimir/?ajax=true&callback=?', function(request) {
	                        $('#{$g['divId']}').hideLoading();
	                        divRefresh('{$g['divId']}', true);
	                    });
	                });
	        } </script></div>
EOT;
	    /**
	    * Define ação do click de abertura (ao clicar em uma linha do grid)
           $novoIdTelaForm = uniqid();
           echo  <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                var dbclickPrevent = false;
                $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
                    // Previne duplo click (abrir duas janelas)
                    if (dbclickPrevent) { return false; }
                    dbclickPrevent = true;
                    setTimeout(function() { dbclickPrevent = false; }, 500);
                    // somente executa a ação ao clicar na linha ou na coluna
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    // pega o ID da linha
                    var id = $(this).closest('tr').attr('id').split('|');
                    var D001_Id = id[0].replace(/D001_Id-/, '');
                    var D024_Id = id[1].replace(/D024_Id-/, '');
                   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad020/content/cad020form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
               });
               $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;
*/
	    /**
	     * Geração: Monta o SQL e retorna o dados
	     */
	    list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
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
    break;
}
