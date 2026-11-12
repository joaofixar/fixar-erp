<?php
 namespace hardness; if ($g['modoDesenvolvedor']) { goto syb0I; } if (!$g['modoDesenvolvedorApp']) { goto NDY2l; } Personalizacoes::$rootDir = Apps::getDirEditor(); NDY2l: goto r2fCE; syb0I: Personalizacoes::$rootDir = 'ide/'; r2fCE: $formNaoRecarregar = true; require_once 'prog/cad/cad020/grid_func-js.php'; echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\tfunction carregarEditor(divId, classe, doc) {
\t\t\tvar conteudo = \$('#' + divId + ' .' + classe).html();
\t\t\tvar docColunas = \$('#docColunas').html();
\t\t\tvar docFiltros = \$('#docFiltros').html();
\t\t\tvar docExtra = \$('#docExtra').html();
\t\t\tvar docBotoes = \$('#docBotoes').html();
\t\t\tvar docTotais = \$('#docTotais').html();
\t\t\tvar docTotaisNovo = \$('#docTotaisNovo').html();
\t\t\tvar div = \$('#' + divId);
\t\t\tif (\$('#' + divId + ' textarea').data('gerarEditor') == 'true') { return true; }
\t\t\t/*div.html('');
\t\t\t\$('<textarea>').addClass(classe).html(conteudo).appendTo(div);
\t\t\tvar editor = CodeMirror.fromTextArea(\$('#' + divId + ' .' + classe).get()[0], {
\t\t\t\tlineNumbers: true,
\t\t\t\tmatchBrackets: true,
\t\t\t\tmode: "application/x-httpd-php",
\t\t\t\tindentUnit: 4,
\t\t\t\tindentWithTabs: true,
\t\t\t\tenterMode: "keep",
\t\t\t\ttabMode: "shift",
\t\t\t\tvalue: conteudo
\t\t\t});
\t\t\tsetTimeout(function() {
\t\t\t\t\$('#' + divId + ' .CodeMirror').width(\$('#' + divId).width() - 300);
\t\t\t\t\$('#' + divId + ' .CodeMirror-scroll').height(\$('#' + divId).height() - 30);
\t\t\t\t\$('#' + divId + ' .CodeMirror-scrollbar').height(\$('#' + divId).height() - 30);
\t\t\t\t\$('#' + divId + ' .CodeMirror-gutter').height(20000);
\t\t\t}, 100);*/
\t\t\t\$('#' + divId + ' textarea').width(\$('#' + divId).width() - 310);
\t\t\t\$('#' + divId + ' textarea').height(\$('#' + divId).height() - 63);
\t\t\t// documentação
\t\t\tif (!isempty(doc)) {
\t\t\t\tdiv = \$('<div>')
\t\t\t\t\t.addClass('formInput')
\t\t\t\t\t.css({
\t\t\t\t\t\t'position': 'absolute',
\t\t\t\t\t\t'right': '7px',
\t\t\t\t\t\t'top': '35px',
\t\t\t\t\t\t'width': '280px',
\t\t\t\t\t\t'height': (\$('#' + divId).height() - 30) + 'px',
\t\t\t\t\t\t'overflow': 'auto'
\t\t\t\t\t})
\t\t\t\t\t.appendTo(div);
\t\t\t\t\$('<div>')
\t\t\t\t\t.html((doc == 'docColunas') ? docColunas : (doc == 'docFiltros') ? docFiltros : (doc == 'docExtra' ? docExtra : (doc == 'docBotoes' ? docBotoes : (doc == 'docTotais' ? docTotais : docTotaisNovo))))
\t\t\t\t\t.css({
\t\t\t\t\t\t'width': '800px',
\t\t\t\t\t})
\t\t\t\t\t.appendTo(div);
\t\t\t\treloadUI(divId);
\t\t\t}
\t\t\t\$('#' + divId + ' textarea').data('gerarEditor', 'true');
\t\t\t\$('#' + divId + ' textarea').trigger('gerarEditor');
\t\t}
\t} </script></div>
EOT;
$docColunas = <<<'EOT'
<span style="font-size: 1.2em; font-weight: bold;">Obrigatórios:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaValor</span> 			: Campo / Expressão SQL / Campo a ser expandido (pivo)
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaTitulo</span> 			: Título

<span style="font-size: 1.2em; font-weight: bold;">Mais utilizados:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">chavePrimaria</span>			: (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaExibir</span>			: (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaExibirNivel</span>		: 1 (normal, vendedor, comprador) / 2 (supervisor) / 4 (diretor)
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaExibirCodigo</span>		: exibir ou não de acordo com um código PHP
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaAlinhar</span>			: left / right / center
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaCasasDecimais</span>		: (número)
<span style="font-weight: bold; border-bottom: 1px solid black;">callback</span>				: gCorrigeNumero / gCorrigeData / etc...
<span style="font-weight: bold; border-bottom: 1px solid black;">callbackCarregar</span>        : (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">style</span>					: (css)
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditar</span>			: (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarNivel</span>		: 1 (normal, vendedor, comprador) / 2 (supervisor) / 4 (diretor)
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaCorDeFundo</span>		: (vazio) / #F4F4F4,#C4F7FC
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaOrdenar</span>			: (vazio) / S (habilita/desabilita a opção de ordenar a coluna)
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaOcultar</span>			: (vazio) / S (habilita/desabilita a opção de ocultar a coluna)

<span style="font-size: 1.2em; font-weight: bold;">Todos outros:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaTipo</span> 	: livre / banco / formula (para operações matemáticas utilizando as colunas existentes do grid) / pivo
<span style="font-weight: bold; border-bottom: 1px solid black;">callbackParameter</span>		: passa um segundo parametro para a função callback ( funcao($array, $segundoParametro) {} ) - use array para passar vários parametros
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaExibirCallbackNivel</span>: 1 (normal, vendedor, comprador) / 2 (supervisor) / 4 (diretor) - O callback é executado de acordo com a alçada
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarJSCallback</span>: nome da função JS que será executado após o campo ser editado - funcaoJs(idChavePrimaria, campoEditado, valorAntigo, novoValor)
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarGeraSelect</span>: Array()
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarUppercase</span> : define o style para uppercase
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarInsert</span>	: se true, faz o INSERT caso o campo não exista
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarTodos</span> 	: permite a edição de todos os campos da coluna de uma vez
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarTipo</span> 	: texto, moeda ou flag (vazio = automático)
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaEditarExtra</span> 	: obrigatório no colunaEditarTipo 'flag': S|N ou 1|0
<span style="font-weight: bold; border-bottom: 1px solid black;">styleTH</span>				: (style do TH) pode ser utilizado para adicionar um css na tag 'style', exemplo: style=>"width: 16px;"
<span style="font-weight: bold; border-bottom: 1px solid black;">extraTH</span>				: (extra do TH) pode ser utilizado para adicionar um html extra, exemplo: colspan="2"
<span style="font-weight: bold; border-bottom: 1px solid black;">classTH</span>				: (class do TH) pode ser utilizado para adicionar uma classe extra, exemplo: classex
<span style="font-weight: bold; border-bottom: 1px solid black;">usarHaving</span> 			: no filtro utilizar HAVING em vez de WHERE (utilizado em campos com 'AS')
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaAgrupar</span> 		: utilizada em conjunto com o Filtro 'AGRUPAR': irá fazer um rowspan para todos os itens do agrupamento
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaTotal</span> 		: utilizada em conjunto com o Filtro 'AGRUPAR': sub-totais de colunas no agrupamento
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaSelect</span> 		: utilizado para gerar combo no filtro deste campo
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaSelectNaoGerarExtra</span>: este filtro não gera um WHERE automatico no select
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaTotalFinal</span> 	: adiciona um total final (no final do grid) no modo de impressão, variáveis disponíveis: $soma, $media, $registros
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaNaoAtualizar</span> 	: não atualizar o valor desse campo no refresh da linha

<span style="font-size: 1.2em; font-weight: bold;">Agrupamento:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaDimensao</span>			: (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaMetrica</span>			: (vazio) / S

<span style="font-size: 1.2em; font-weight: bold;">Pivo:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaPivoLigacao</span>       : campo de ligação que deve existir na definição do grid
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaPivoValor</span>         : campo para cada coluna
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaPivoMostrar</span>       : mostrar esse campo ao invés de colunaValor
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaPivoOrdenacao</span>     : ordenação do pivo
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaPivoLimite</span>        : limite do pivo

<span style="font-size: 1.2em; font-weight: bold;">Porcentagem:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaPorcentagemCampo</span>       : campo para o cálculo da porcentagem

<span style="font-size: 1.2em; font-weight: bold;">Curva ABC:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaAbcCampo</span>       : campo de cálculo da curva ABC
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaAbcLigacao</span>       : campo de ligação (utilizado no agrupamento) que deve existir na definição do grid

<span style="font-size: 1.2em; font-weight: bold;">Callbacks:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">callback [abrirJanela]</span>	: callbackParameter [array('Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/', 'T003_D024_Id', '960,583')]
<span style="font-weight: bold; border-bottom: 1px solid black;">callback [novaJanela]</span>	: callbackParameter [array('Título da Janela', '/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?D024_Id=', 'T003_D024_Id', '800,500')]
<span style="font-weight: bold; border-bottom: 1px solid black;">callback [abrirNotificacao]</span>	: callbackParameter [array('Estoque Distribuidoras', '/crm/crm001/outro/estoqueED/', 'D049_Id', 'top', true)]
<span style="font-weight: bold; border-bottom: 1px solid black;">callback [gCheckbox]</span>	: callbackParameter [array('T225_Flag_Autorizado', 'N|S', '', '', 'full|no')] — o quinto item controla o refresh: vazio/ausente atualiza somente a linha, 'full' recarrega o grid e 'no' nao recarrega
<span style="font-weight: bold; border-bottom: 1px solid black;">callback [gContador]</span>

<span style="font-size: 1.2em; font-weight: bold;">Tipo formula:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">Exemplo concatenando strings</span>
colunaValor[&nbsp;&nbsp;&nbsp;&nbsp;"[[D001_Codigo_Produto]] - "."[[D001_Descricao_Produto]]"&nbsp;&nbsp;&nbsp;&nbsp;]
<span style="font-weight: bold; border-bottom: 1px solid black;">Exemplo operações aritiméticas</span>
colunaValor[&nbsp;&nbsp;&nbsp;&nbsp;[[T006_Quantidade]] * [[T006_Valor_preco_Unitario]]&nbsp;&nbsp;&nbsp;&nbsp;]

* Quando a coluna possuir alias com o 'as', utilizar o alias, caso contrario informar todo o conteudo do colunaValor
Ex: colunaValor[SUM(T004_Quantidade) as Quantidade] -> Utilizar 'Quantidade'
Ex: colunaValor[SUM(T004_Quantidade) Quantidade] -> Utilizar 'SUM(T004_Quantidade) Quantidade'
Ex: colunaValor[SUM(T004_Quantidade)] -> Utilizar 'SUM(T004_Quantidade)'

EOT;
$docColunas = str_replace("\n", '<br />', $docColunas); echo '<div style="display: none;" id="docColunas">' . $docColunas . '</div>'; $docFiltros = <<<'EOT'
<span style="font-size: 1.2em; font-weight: bold;">Obrigatórios:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">colunaValor</span> 			: Campo (vários campos - separar por ,)
<span style="font-weight: bold; border-bottom: 1px solid black;">titulo</span> 					: Título
<span style="font-weight: bold; border-bottom: 1px solid black;">tipo</span> 					: texto / numero / data / select / agrupar

<span style="font-size: 1.2em; font-weight: bold;">Mais utilizados:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">exibir</span>					: (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">valor</span>					: Filtro já vem preenchido com um valor padrao

<span style="font-size: 1.2em; font-weight: bold;">Todos outros:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">naoGerarExtra</span> 			: este filtro nao irá gerar um WHERE no SQL automáticamente (pode ser pego via $g['ui']->getGridFiltroCampoBusca())
<span style="font-weight: bold; border-bottom: 1px solid black;">select</span>					: Array()
<span style="font-weight: bold; border-bottom: 1px solid black;">campos</span>					: Array()
<span style="font-weight: bold; border-bottom: 1px solid black;">having</span>: (vazio) / S
EOT;
$docFiltros = str_replace("\n", '<br />', $docFiltros); echo '<div style="display: none;" id="docFiltros">' . $docFiltros . '</div>'; $docExtra = <<<'EOT'
<span style="font-size: 1.2em; font-weight: bold;">Variáveis:</span>
[[VARIAVEL]] onde VARIAVEL é o nome da váriavel disponível dentro do grid.

<span style="font-size: 1.2em; font-weight: bold;">Mais utilizados:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">[[g-empresaAtual]]</span> 		: Empresa Atual
<span style="font-weight: bold; border-bottom: 1px solid black;">[[g-usuarioAtual]]</span> 		: Usuário Atual
<span style="font-weight: bold; border-bottom: 1px solid black;">[[r_acaoId]]</span> 			: ID do registro
EOT;
$docExtra = str_replace("\n", '<br />', $docExtra); echo '<div style="display: none;" id="docExtra">' . $docExtra . '</div>'; $docBotoes = <<<'EOT'
<span style="font-size: 1.2em; font-weight: bold;">Botões</span>
Colocar os botões dentro deste código no grid:
<hr>if (!personalizacaoBotoes()) {
&nbsp;&nbsp;&nbsp;&nbsp;....
}
EOT;
$docBotoes = str_replace("\n", '<br />', $docBotoes); echo '<div style="display: none;" id="docBotoes">' . $docBotoes . '</div>'; $docTotais = <<<'EOT'
<span style="font-size: 1.2em; font-weight: bold;">Totais</span>
Colocar os totais dentro deste código no grid:
<hr>if (!personalizacaoTotais()) {
&nbsp;&nbsp;&nbsp;&nbsp;....
}
EOT;
$docTotais = str_replace("\n", '<br />', $docTotais); echo '<div style="display: none;" id="docTotais">' . $docTotais . '</div>'; $docTotaisNovo = <<<'EOT'
<span style="font-size: 1.2em; font-weight: bold;">Obrigatórios:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">nome</span> 			        : nome
<span style="font-weight: bold; border-bottom: 1px solid black;">valor</span> 			        : campo SQL ou fórmula (opcional para totalizador 'count')
<span style="font-weight: bold; border-bottom: 1px solid black;">titulo</span> 			        : título

<span style="font-size: 1.2em; font-weight: bold;">Todos:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">tipo</span>			        : sql, formula ou totais
<span style="font-weight: bold; border-bottom: 1px solid black;">exibir</span>			        : (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">totalizador</span>			    : soma, count, distinct, min, max e media (válido para os tipos = 'sql' e 'formula')
<span style="font-weight: bold; border-bottom: 1px solid black;">class</span>			        : classe CSS
<span style="font-weight: bold; border-bottom: 1px solid black;">callback</span>				: gCorrigeNumero / gCorrigeData / etc...
<span style="font-weight: bold; border-bottom: 1px solid black;">callbackCarregar</span>        : (vazio) / S
<span style="font-weight: bold; border-bottom: 1px solid black;">callbackParameter</span>		: array(...)
EOT;
$docTotaisNovo = str_replace("\n", '<br />', $docTotaisNovo); echo '<div style="display: none;" id="docTotaisNovo">' . $docTotaisNovo . '</div>'; $dir = Personalizacoes::getDirPersonalizacoesGrid(); $arquivos = array('sistemaPGridColunas' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) : $dir . 'PRE' . md5($r_grid), 'sistemaPGridFiltros' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-filtros' : $dir . 'PRE' . md5($r_grid) . '-filtros', 'sistemaPGridTotaisNovo' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-totais-novo' : $dir . 'PRE' . md5($r_grid) . '-totais-novo', 'sistemaPGridExtra' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-extra' : $dir . 'PRE' . md5($r_grid) . '-extra', 'sistemaPGridJS' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-js' : $dir . 'PRE' . md5($r_grid) . '-js', 'sistemaPGridHTMLColunas' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-print-colunas' : $dir . 'PRE' . md5($r_grid) . '-print-colunas', 'sistemaPGridHTMLCabecalho' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-print-cabecalho' : $dir . 'PRE' . md5($r_grid) . '-print-cabecalho', 'sistemaPGridGridLinha' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-gridLinha' : $dir . 'PRE' . md5($r_grid) . '-gridLinha', 'sistemaPGridBotoes' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-botoes' : $dir . 'PRE' . md5($r_grid) . '-botoes', 'sistemaPGridTotais' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-totais' : $dir . 'PRE' . md5($r_grid) . '-totais', 'sistemaPGridConfiguracoes' => !empty($r_pesquisaGridId) ? $dir . 'PRE' . $r_pesquisaGridId . '_' . md5($r_grid) . '-ordem-' . $g['usuarioAtual'] : $dir . 'PRE' . md5($r_grid) . '-ordem-' . $g['usuarioAtual']); $arquivosModelo = array('sistemaPGridColunas' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid), 'sistemaPGridFiltros' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-filtros' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-filtros', 'sistemaPGridTotaisNovo' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-totais-novo' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-totais-novo', 'sistemaPGridExtra' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-extra' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-extra', 'sistemaPGridJS' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-js' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-js', 'sistemaPGridHTMLColunas' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-print-colunas' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-print-colunas', 'sistemaPGridHTMLCabecalho' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-print-cabecalho' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-print-cabecalho', 'sistemaPGridGridLinha' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-gridLinha' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-gridLinha', 'sistemaPGridBotoes' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-botoes' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-botoes', 'sistemaPGridTotais' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-totais' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-totais', 'sistemaPGridConfiguracoes' => !empty($r_pesquisaGridId) ? $dir . 'modelo-' . $r_modoModelo . '-' . $r_pesquisaGridId . '_' . md5($r_grid) . '-config' : $dir . 'modelo-' . $r_modoModelo . '-' . md5($r_grid) . '-config'); if ($g['r'][3] == 'sistemaPGridOrdem') { goto RN4xk; } if (!empty($r_sistemaPGridColunasForm['Modelo_Nomexxx'])) { goto IE1BG; } if ($g['r'][3] == 'sistemaPGridColunasxxx' && file_exists($arquivosModelo['sistemaPGridColunas']) || $g['r'][3] == 'sistemaPGridFiltrosxxx' && file_exists($arquivosModelo['sistemaPGridFiltros']) || $g['r'][3] == 'sistemaPGridExtraxxx' && file_exists($arquivosModelo['sistemaPGridExtra']) || $g['r'][3] == 'sistemaPGridJSxxx' && file_exists($arquivosModelo['sistemaPGridJS']) || $g['r'][3] == 'sistemaPGridHTMLColunasxxx' && file_exists($arquivosModelo['sistemaPGridHTMLColunas']) || $g['r'][3] == 'sistemaPGridHTMLCabecalhoxxx' && file_exists($arquivosModelo['sistemaPGridHTMLCabecalho']) || $g['r'][3] == 'sistemaPGridGridLinhaxxx' && file_exists($arquivosModelo['sistemaPGridGridLinha']) || $g['r'][3] == 'sistemaPGridBotoesxxx' && file_exists($arquivosModelo['sistemaPGridBotoes']) || $g['r'][3] == 'sistemaPGridTotaisxxx' && file_exists($arquivosModelo['sistemaPGridTotais']) || $g['r'][3] == 'sistemaPGridTotaisNovoxxx' && file_exists($arquivosModelo['sistemaPGridTotaisNovo']) || $g['r'][3] == 'sistemaPGridConfiguracoesxxx' && file_exists($arquivosModelo['sistemaPGridConfiguracoes'])) { goto zJ7N3; } if (!$r_modoModelo) { goto EuEow; } $pre = 'modelo-' . $r_modoModelo . '-'; EuEow: $alterandoUsuario = false; $r_alterandoPara = isset($r_alterandoPara) && !empty($r_alterandoPara) ? $r_alterandoPara : -2; $opcoes = gGeraSelect('C021', 'C021_Id', 'C021_Id,C021_Nome_Perfil', 'ORDER BY C021_Nome_Perfil ASC', false, $r_alterandoPara); array_unshift($opcoes, array('title' => 'optgroup', 'value' => 'Perfis')); array_unshift($opcoes, array('title' => 'Geral: todos os usuários', 'value' => -1, 'selected' => $r_alterandoPara == -1)); array_unshift($opcoes, array('title' => "Usuário: {$g['C007']['C007_Login']}", 'value' => -2, 'selected' => $r_alterandoPara == -2)); foreach ($opcoes as &$item) { goto DK5UL; hVVb1: AM2JA: goto ZPlJk; SgYek: if (!file_exists($arquivo)) { goto TgcNL; } goto ABetG; Rm0xy: c7BId: goto RgqMe; Jzyv5: die('Erro, opção não implementada.'); goto Rm0xy; ZPlJk: $arquivo = ''; goto Gj7qO; U86zF: $arquivo = $g['r'][3] == 'sistemaPGridTotaisNovo' ? $arquivos['sistemaPGridTotaisNovo'] : $arquivo; goto EkWby; h8u4l: goto YULje; goto APjRv; Gj7qO: $arquivo = $g['r'][3] == 'sistemaPGridColunas' ? $arquivos['sistemaPGridColunas'] : $arquivo; goto WYYMP; yEqXf: $arquivo = $g['r'][3] == 'sistemaPGridExtra' ? $arquivos['sistemaPGridExtra'] : $arquivo; goto NVhn5; WYYMP: $arquivo = $g['r'][3] == 'sistemaPGridFiltros' ? $arquivos['sistemaPGridFiltros'] : $arquivo; goto yEqXf; EkWby: $arquivo = $g['r'][3] == 'sistemaPGridConfiguracoes' ? $arquivos['sistemaPGridConfiguracoes'] : $arquivo; goto Iwu47; WP2W4: mvvwR: goto iiiMj; x1ggk: $arquivo = $g['r'][3] == 'sistemaPGridBotoes' ? $arquivos['sistemaPGridBotoes'] : $arquivo; goto q27C8; Nimg8: goto bFngs; goto hVVb1; uaGfX: bFngs: goto mXmzS; eyuDd: $arquivo = $g['r'][3] == 'sistemaPGridGridLinha' ? $arquivos['sistemaPGridGridLinha'] : $arquivo; goto x1ggk; iiiMj: $arquivo = str_replace('PRE', "u{$g['C007']['C007_Id']}-", $arquivo); goto XQ7Cb; Iwu47: if ($arquivo) { goto c7BId; } goto Jzyv5; APjRv: dmDYQ: goto me0TS; RgqMe: if ($item['value'] == -2) { goto mvvwR; } goto MY5nD; ABetG: $item['title'] .= ' *'; goto yc_2H; gmSPu: $arquivo = $g['r'][3] == 'sistemaPGridHTMLCabecalho' ? $arquivos['sistemaPGridHTMLCabecalho'] : $arquivo; goto eyuDd; a92zr: $arquivo = $g['r'][3] == 'sistemaPGridHTMLColunas' ? $arquivos['sistemaPGridHTMLColunas'] : $arquivo; goto gmSPu; XQ7Cb: hBJ_B: goto SgYek; yc_2H: TgcNL: goto uaGfX; iiXjQ: $arquivo = str_replace('PRE', "{$item['value']}-", $arquivo); goto h8u4l; FdM31: goto hBJ_B; goto WP2W4; qVdNy: YULje: goto FdM31; me0TS: $arquivo = str_replace('PRE', '', $arquivo); goto qVdNy; DK5UL: if (!($item['title'] == 'optgroup')) { goto AM2JA; } goto Nimg8; MY5nD: if ($item['value'] == -1) { goto dmDYQ; } goto iiXjQ; NVhn5: $arquivo = $g['r'][3] == 'sistemaPGridJS' ? $arquivos['sistemaPGridJS'] : $arquivo; goto a92zr; q27C8: $arquivo = $g['r'][3] == 'sistemaPGridTotais' ? $arquivos['sistemaPGridTotais'] : $arquivo; goto U86zF; mXmzS: } Fk8Ix: if (!($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp'])) { goto uOpc7; } $opcoes = array(array('title' => 'Geral: todos os usuários', 'value' => -1, 'selected' => $r_alterandoPara == -1)); uOpc7: $local = gProcessaSelect($opcoes); echo <<<EOT
\t<div style="margin: 4px 8px;">
\t\tAlterando para: 
\t\t<select onchange="divSetarAcaoId('{$r_divRefresh}', \$(this).val(), 'alterandoPara'); divRefresh('{$r_divRefresh}');">
\t\t\t{$local}
\t\t</select>&nbsp;
\t</div>
EOT;
$pre .= $r_alterandoPara > 0 ? "{$r_alterandoPara}-" : ''; if (!($r_alterandoPara == -2)) { goto PFjI9; } $pre = 'u' . $g['C007']['C007_Id'] . '-'; PFjI9: goto Q_PXF; zJ7N3: $alterandoUsuario = true; $pre = 'modelo-' . $r_modoModelo . '-'; $nomeModelo = $r_modoModelo; echo <<<EOT
\t<div style="margin: 4px 8px;">
\t\t<span style="color: red;">Atenção! </span>Personalizado para o modelo: <strong>{$nomeModelo}</strong>
\t</div>
EOT;
Q_PXF: goto AxzBA; IE1BG: $alterandoUsuario = true; $pre = 'modelo-' . strtr(base64_encode($r_sistemaPGridColunasForm['Modelo_Nome']), '+/=', '-_,') . '-'; echo <<<EOT
\t<div style="margin: 4px 8px;">
\t\t<span style="color: red;">Atenção! Modelo Salvo! </span>Para fazer personalizações para este novo modelo, você deve abrir ele.</strong>
\t</div>\t
EOT;
AxzBA: goto dD23X; RN4xk: dD23X: switch ($g['r'][3]) { case 'sistemaPGridColunas': goto AMTmm; bw3FD: $botoesMid = $botoesEsq = $botoesDir = ''; goto ZU1cC; UpexR: olIcq: goto pnZRG; JahPH: $res = mysql_query($sql); goto epB9f; ptLMZ: $formArray = array('campos' => array('geral' => array('arrayColunas' => array('name' => 'arrayColunas', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'plaintext', 'value' => $arrayColunas), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto xuKgt; iHZyp: goto U8SPL; goto ytY0q; BonWJ: $arquivoPath = $confUsuario['pathRaiz'] . $arquivoReltivo; goto u1R3C; wO8sA: Qgr1G: goto W6lMi; PIdUY: $sql = "SELECT * FROM D140 WHERE D140_Nome_Md5 = '{$gridIdTmp}'"; goto JahPH; Xnh2T: $h = fopen($arquivo, 'w'); goto k0uFg; DwUIo: $g['smarty']->assign('botoesId', $g['divId']); goto yjS21; c30fL: $form_tabela = 'D000'; goto t3Y4C; xv8aw: if (!($formCodigo == 2)) { goto UsBS8; } goto xnARi; O7lcQ: k3aKQ: goto DiFv4; u1R3C: if (strpos($gridId, 'D140') !== false) { goto Q_RH4; } goto qHEQ5; ZxkER: $g['smarty']->assign('formularioID', $form->formularioID()); goto ylq9B; v2PSq: $formMultiTab = false; goto ot2sp; gSOwo: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto ZxkER; c89kn: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto PXAhx; a6z7n: $arquivoExiste = true; goto BV_uc; ibNsG: l7Z9i: goto lzoyG; PXAhx: echo $gerado['pagina']; goto FyLRp; b8P1E: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto Q4xUj; HOhSw: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto GYmWo; Tdc94: xH1Wc: goto NbXy1; zsQaP: $hidden = !empty($r_sistemaPGridColunasForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto Lamel; epB9f: $row = mysql_fetch_assoc($res); goto Xnh2T; r8gT0: $enviar_btnId = 'sistemaPGridColunasBtn'; goto sGVqL; ZA_D9: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto FGR72; } goto Aq8Of; mw103: YUh8x: goto HOhSw; d5Vvp: is_dir($dir) or mkdir($dir, 0777, true); goto zh0Qk; t3Y4C: $form_campo_pk = 'D000_Id'; goto r8gT0; ZYL4V: $g["smarty"]->assign("formId", $formId); goto O7lcQ; xnARi: file_put_contents($arquivo, $form->campoValorEnviado('arrayColunas')); goto mBuzl; RKc8_: $botoesDir .= '<button type="submit" id="sistemaPGridColunasBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto iHZyp; IJ07Q: $cmd = "cd {$confUsuario['pathRaiz']}{$g['pathDados']} && tar cvf - dump.sql personalizacaoGrid/*{$gridId}* | gzip -9c > {$arquivoPath}"; goto vVKDy; RiFpU: $g['smarty']->assign('botoesMid', $botoesMid); goto Nt4Bi; y1XhU: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto v2PSq; cnR0p: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!<br>.'); goto lQBbD; s2BhJ: $arquivo = "{$confUsuario['pathRaiz']}{$g['pathDados']}dump.sql"; goto RFScO; sGVqL: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridColunas/'; goto qYFcu; ZU1cC: if ($r_modoModeloxxx) { goto V5Eao; } goto zsQaP; OoSHo: $form = new Formulario('sistemaPGridColunasForm'); goto ptLMZ; fB2z9: $recover = !empty($r_recover) ? 1 : 0; goto DZP9p; b2LLm: FGR72: goto gSOwo; pnZRG: echo gGeraAlertMsgErroForm($form); goto bTqlq; bNjdm: goto c2vSC; goto UpexR; Aq8Of: if ($formCodigo == 1) { goto olIcq; } goto xv8aw; lzoyG: $botoesEsq .= '<a target="_blank" href="/hardness3/' . $arquivoReltivo . '"><button>Baixar</button></a>'; goto jKe1k; QGTwP: $carregarOriginal = 0; goto fB2z9; YdWgp: unlink($arquivo); goto ibNsG; GYmWo: TS33u: goto c30fL; mRbdN: goto l7Z9i; goto oQ1c6; V5K1H: fclose($h); goto IJ07Q; FyLRp: if ($formMultiTab) { goto k3aKQ; } goto ZYL4V; qkiq4: $botoesDir .= '<button type="submit" id="sistemaPGridColunasBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto QJgV4; bTqlq: c2vSC: goto b2LLm; CD2QQ: $g['smarty']->display('buttons-floatBottom.tpl'); goto QGTwP; kD_gh: $arrayColunas = ''; goto FP2f3; T639l: goto TS33u; goto mw103; w67Pg: $dir = "{$g['pathDados']}tmp/"; goto d5Vvp; gzket: goto UDEWx; goto jVkr6; DZP9p: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridColunasBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridColunasBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridColunasBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridColunasBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tfunction pegarColunas(opcao) {
\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t// no apache ele vem o caminho relativo
\t\t\t\t\tvar url = '{$r_grid}';
\t\t\t\t\tvar matches = url.match(/^\\/.*\$/);
\t\t\t\t\tif (!matches) {
\t\t\t\t\t\turl = '/' + url;
\t\t\t\t\t}
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: url,
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&pegarColunas=true&gridFiltrado=true' + \$('#{$g['divIdParent']}').data('vars'),
\t\t\t\t\t\ttype: 'post',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\tvar matches = data.match(/<pegarErro>([\\s\\S]*)<\\/pegarErro>/m);
\t\t\t\t\t\t\t// mensagem de erro (somente notificar)
\t\t\t\t\t\t\tif (matches) {
\t\t\t\t\t\t\t\t\$('<div>').html(matches[1]).appendTo(\$('#{$g['divId']}'));
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tvar matches = data.match(/<pegarColunas>([\\s\\S]*)<\\/pegarColunas>/m);
\t\t\t\t\t\t\tif (!matches) {
\t\t\t\t\t\t\t\t// mensagens de erro
\t\t\t\t\t\t\t\tif ('{$recover}' == 0) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayColunas').html(data);
\t\t\t\t\t\t\t\t\tfecharJanela('{$g['divId']}');
\t\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayColunas').html(data);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tif (opcao == 'show') {
\t\t\t\t\t\t\t\t\tdialogConfirm('Personalização do Grid Original', '<textarea style="width: 500px; height: 400px;">' + matches[1] + '</textarea>');
\t\t\t\t\t\t\t\t} else if ('{$carregarOriginal}' == 1) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayColunas').html(matches[1]);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayColunas', 'docColunas');
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t}
\t\t\t\tpegarColunas();

\t\t\t\tfunction pegarColunasUsuario() {
\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t// no apache ele vem o caminho relativo
\t\t\t\t\tvar url = '{$r_grid}';
\t\t\t\t\tvar matches = url.match(/^\\/.*\$/);
\t\t\t\t\tif (!matches) {
\t\t\t\t\t\turl = '/' + url;
\t\t\t\t\t}
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: url,
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&pegarColunasUsuario=true&gridFiltrado=true' + \$('#{$g['divIdParent']}').data('vars'),
\t\t\t\t\t\ttype: 'post',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\tvar matches = data.match(/<pegarErro>([\\s\\S]*)<\\/pegarErro>/m);
\t\t\t\t\t\t\t// mensagem de erro (somente notificar)
\t\t\t\t\t\t\tif (matches) {
\t\t\t\t\t\t\t\t\$('<div>').html(matches[1]).appendTo(\$('#{$g['divId']}'));
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tvar matches = data.match(/<pegarColunasUsuario>([\\s\\S]*)<\\/pegarColunasUsuario>/m);
\t\t\t\t\t\t\tif (!matches) {
\t\t\t\t\t\t\t\t// 
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tdialogConfirm('Personalização do Grid - Usuário', '<textarea style="width: 500px; height: 400px;">' + matches[1] + '</textarea>');
\t\t\t\t\t\t\t}
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t}
\t\t\t} </script></div>
EOT;
goto gzket; DiFv4: $g['smarty']->display('form.tpl'); goto bw3FD; J0ItU: if (!$arquivoExiste) { goto Qgr1G; } goto wubpQ; QJgV4: U8SPL: goto b8P1E; AMTmm: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto y1XhU; Q4xUj: $botoesEsq .= '<button type="submit" onclick="pegarColunas(\'show\'); return false;">Mostrar Original</button>'; goto J0ItU; jKe1k: $botoesEsq .= ' <b>MD5 grid</b> ' . md5($r_grid); goto DwUIo; vVKDy: echo shell_exec($cmd); goto YdWgp; ot2sp: if ($formMultiTab) { goto YUh8x; } goto dMXxB; DgdCB: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) : $dir . $pre . md5($r_grid); goto ovEWw; AUmDu: X7sLq: goto uIgPK; dMXxB: $formId = uniqid(); goto T639l; oQ1c6: Q_RH4: goto s2BhJ; uIgPK: $arrayColunas = file_get_contents($arquivo); goto a6z7n; wubpQ: $botoesEsq .= '<button type="submit" id="sistemaPGridColunasBtnRemover">Remover</button>'; goto wO8sA; X_ch7: echo shell_exec($cmd); goto mRbdN; DmMQN: if (file_exists($arquivo)) { goto X7sLq; } goto o7OEb; k0uFg: fwrite($h, "INSERT INTO D140 (D140_Nome, D140_Nome_Md5, D140_SQL, D140_Data_Criacao) VALUES ('" . mysql_real_escape_string($row['D140_Nome']) . "', '" . mysql_real_escape_string($row['D140_Nome_Md5']) . "', '" . mysql_real_escape_string($row['D140_SQL']) . "', '" . mysql_real_escape_string($row['D140_Data_Criacao']) . "');"); goto V5K1H; yjS21: $g['smarty']->assign('botoesEsq', $botoesEsq); goto RiFpU; qYFcu: $r_grid = urldecode($r_grid); goto DgdCB; ovEWw: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId : $arquivo; goto kD_gh; qHEQ5: $cmd = "cd {$confUsuario['pathRaiz']}{$g['pathDados']} && tar cvf - personalizacaoGrid/*{$gridId}* | gzip -9c > {$arquivoPath}"; goto X_ch7; W6lMi: $gridId = !empty($r_pesquisaGridId) ? $r_pesquisaGridId : md5($r_grid); goto w67Pg; Lamel: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto RKc8_; lQBbD: UsBS8: goto bNjdm; Nt4Bi: $g['smarty']->assign('botoesDir', $botoesDir); goto CD2QQ; FP2f3: $arquivoExiste = false; goto DmMQN; o7OEb: if (isset($r_sistemaPGridColunasForm)) { goto xH1Wc; } goto Tdc94; zh0Qk: $arquivoReltivo = $dir . $gridId . '.tar.gz'; goto BonWJ; RFScO: $gridIdTmp = str_replace('D140_', '', $gridId); goto PIdUY; mBuzl: $arrayColunas = $form->campoValorEnviado('arrayColunas'); goto cnR0p; xuKgt: $form->definirFormularioArray($formArray); goto ZA_D9; ytY0q: V5Eao: goto qkiq4; NbXy1: goto x3dX4; goto AUmDu; BV_uc: x3dX4: goto OoSHo; ylq9B: $g['smarty']->assign('table', $gerado['table']); goto c89kn; jVkr6: case 'sistemaPGridFiltros': goto eDfeX; IQhUL: $formId = uniqid(); goto QyA13; h0Za1: $hidden = !empty($r_sistemaPGridFiltrosForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto KyWK1; J9hZl: goto UDEWx; goto nhHgL; GrL5j: $carregarOriginal = 0; goto msSrs; JSwaL: VwwtT: goto zXbHN; Cp4el: $botoesDir .= '<button type="submit" id="sistemaPGridFiltrosBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto Irc_Y; IFYVK: goto UsZew; goto VA2CM; eDfeX: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto k8USH; d2E4w: if (!($formCodigo == 2)) { goto YRI6A; } goto z62Tg; kNJKq: $arrayFiltros = file_get_contents($arquivo); goto cVjEc; gN8XE: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto oY2sw; Y4aLI: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto jjdWa; } goto KcQqL; oEBDv: $botoesDir .= '<button type="submit" id="sistemaPGridFiltrosBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto OC459; IYnEK: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto By0Kb; AN8ai: if ($formMultiTab) { goto xNbIW; } goto IQhUL; qM5OU: $arrayFiltros = ''; goto CJjYJ; bY6pR: $botoesEsq .= '<button type="submit" onclick="pegarFiltros(\'show\'); return false;">Mostrar Original</button>'; goto GgRUT; z_Hs1: if ($r_modoModelo) { goto lIv3K; } goto h0Za1; By0Kb: echo $gerado['pagina']; goto APSfS; QyA13: goto bCe88; goto W_giq; GgRUT: if (!$arrayFiltros) { goto VhLEf; } goto tyA3i; ciiAX: $g['smarty']->assign('formularioID', $form->formularioID()); goto q2M06; OC459: Jcxiy: goto X7pxE; ge6Jw: $g['smarty']->display('buttons-floatBottom.tpl'); goto GrL5j; VA2CM: ypd3V: goto kNJKq; fZ1L3: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-filtros' : $dir . $pre . md5($r_grid) . '-filtros'; goto H1qnB; LZqUe: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridFiltros/'; goto y9HaZ; hMuC4: echo gGeraAlertMsgErroForm($form); goto DIU4m; Yz1tr: $g['smarty']->assign('botoesId', $g['divId']); goto nKDwG; BCs13: $g['smarty']->assign('botoesDir', $botoesDir); goto ge6Jw; mslvN: $form_tabela = 'D000'; goto jJB3S; H1qnB: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-filtros' : $arquivo; goto qM5OU; CJjYJ: if (file_exists($arquivo)) { goto ypd3V; } goto Glyht; ym02H: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridFiltrosBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridFiltrosBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridFiltrosBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridFiltrosBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tfunction pegarFiltros(opcao) {
\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t// no apache ele vem o caminho relativo
\t\t\t\t\tvar url = '{$r_grid}';
\t\t\t\t\tvar matches = url.match(/^\\/.*\$/);
\t\t\t\t\tif (!matches) {
\t\t\t\t\t\turl = '/' + url;
\t\t\t\t\t}
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: url,
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&pegarFiltros=true&gridFiltrado=true' + \$('#{$g['divIdParent']}').data('vars'),
\t\t\t\t\t\ttype: 'post',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t// mensagem de erro (somente notificar)
\t\t\t\t\t\t\tif (matches) {
\t\t\t\t\t\t\t\t\$('<div>').html(matches[1]).appendTo(\$('#{$g['divId']}'));
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tvar matches = data.match(/<pegarFiltros>([\\s\\S]*)<\\/pegarFiltros>/m);
\t\t\t\t\t\t\tif (!matches) {
\t\t\t\t\t\t\t\t// mensagens de erro
\t\t\t\t\t\t\t\tif ('{$recover}' == 0) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayFiltros').html(data);
\t\t\t\t\t\t\t\t\tfecharJanela('{$g['divId']}');
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tif (opcao == 'show') {
\t\t\t\t\t\t\t\t\tdialogConfirm('Personalização do Grid Original', '<textarea style="width: 500px; height: 400px;">' + matches[1] + '</textarea>');
\t\t\t\t\t\t\t\t} else if ('{$carregarOriginal}' == 1) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayFiltros').html(matches[1]);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayFiltros', 'docFiltros');
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t}
\t\t\t\tpegarFiltros();
\t\t\t} </script></div>
EOT;
goto J9hZl; jJB3S: $form_campo_pk = 'D000_Id'; goto XBPq5; fWQfW: $g['smarty']->assign('botoesMid', $botoesMid); goto BCs13; zXbHN: $g['smarty']->display('form.tpl'); goto F3TUJ; F3TUJ: $botoesMid = $botoesEsq = $botoesDir = ''; goto z_Hs1; W_giq: xNbIW: goto gN8XE; oY2sw: bCe88: goto mslvN; k8USH: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto dYVP3; BtRc6: $arrayFiltros = $form->campoValorEnviado('arrayFiltros'); goto k_1AG; Glyht: if (isset($r_sistemaPGridFiltrosForm)) { goto grpQ8; } goto sas0Y; A4nmR: $form = new Formulario('sistemaPGridFiltrosForm'); goto DQvEL; XBPq5: $enviar_btnId = 'sistemaPGridFiltrosBtn'; goto LZqUe; Irc_Y: goto Jcxiy; goto Ts2Iv; cVjEc: UsZew: goto A4nmR; sas0Y: grpQ8: goto IFYVK; VWTo3: goto QfjeD; goto q6AKd; DIU4m: QfjeD: goto G321Z; X7pxE: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto bY6pR; G321Z: jjdWa: goto lbYVT; y9HaZ: $r_grid = urldecode($r_grid); goto fZ1L3; L1evX: VhLEf: goto Yz1tr; KcQqL: if ($formCodigo == 1) { goto XpoFg; } goto d2E4w; z62Tg: file_put_contents($arquivo, $form->campoValorEnviado('arrayFiltros')); goto BtRc6; lbYVT: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto ciiAX; k_1AG: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto amTgB; XQLdf: $form->definirFormularioArray($formArray); goto Y4aLI; q2M06: $g['smarty']->assign('table', $gerado['table']); goto IYnEK; APSfS: if ($formMultiTab) { goto VwwtT; } goto o81Zh; o81Zh: $g["smarty"]->assign("formId", $formId); goto JSwaL; q6AKd: XpoFg: goto hMuC4; dYVP3: $formMultiTab = false; goto AN8ai; msSrs: $recover = !empty($r_recover) ? 1 : 0; goto ym02H; tyA3i: $botoesEsq .= '<button type="submit" id="sistemaPGridFiltrosBtnRemover">Remover</button>'; goto L1evX; amTgB: YRI6A: goto VWTo3; nKDwG: $g['smarty']->assign('botoesEsq', $botoesEsq); goto fWQfW; KyWK1: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto Cp4el; DQvEL: $formArray = array('campos' => array('geral' => array('arrayFiltros' => array('name' => 'arrayFiltros', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'plaintext', 'value' => $arrayFiltros), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto XQLdf; Ts2Iv: lIv3K: goto oEBDv; nhHgL: case 'sistemaPGridExtra': goto x9SFN; ge5A5: $form_tabela = 'D000'; goto Gk1JS; ZOwy2: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto DwlLH; VW5vY: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto ueJfd; qslXg: $botoesDir .= '<button type="submit" id="sistemaPGridExtraBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto O11H4; HvltT: LvLnC: goto F6dzl; H9UOy: Eytcp: goto UsMkm; jHsBl: JZ03e: goto H9UOy; Scrrc: echo $gerado['pagina']; goto be3fI; t02WW: $form->definirFormularioArray($formArray); goto TTdfo; YBMnQ: if (isset($r_sistemaPGridExtraForm)) { goto nccvn; } goto xyAiD; af1fS: $g['smarty']->assign('botoesDir', $botoesDir); goto e1TZa; C3v_t: $arrayExtra = $form->campoValorEnviado('arrayExtra'); goto ZOwy2; l4dpO: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto z1zg1; hUHmJ: $g['smarty']->assign('formularioID', $form->formularioID()); goto mE5rK; oCiKR: if (!($formCodigo == 2)) { goto LTE78; } goto NNP1O; gsQIB: if (!$arrayExtra) { goto LvLnC; } goto Jqvak; GKI15: $form = new Formulario('sistemaPGridExtraForm'); goto VhErP; l2qh4: $arrayExtra = ''; goto Xou7G; xyAiD: nccvn: goto ob2Vb; wRt7y: $carregarOriginal = empty($arrayExtra) ? 1 : 0; goto SIGwr; OyMg2: goto vtiDu; goto jwo7q; vvwC_: J5mX_: goto l4dpO; O11H4: goto J5mX_; goto HgS_o; F6dzl: $g['smarty']->assign('botoesId', $g['divId']); goto hXklE; uN2X3: goto JZ03e; goto mdTeR; jJJeJ: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto hIaub; PBOg1: rny9W: goto Y_od_; UsMkm: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto hUHmJ; iwikK: echo gGeraAlertMsgErroForm($form); goto jHsBl; hIaub: $formMultiTab = false; goto JCHSB; Rq9PJ: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridExtra/'; goto bSI21; N764C: $enviar_btnId = 'sistemaPGridExtraBtn'; goto Rq9PJ; HgS_o: A1Llo: goto K6WUr; FaoES: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto Scrrc; mE5rK: $g['smarty']->assign('table', $gerado['table']); goto FaoES; VhErP: $formArray = array('campos' => array('geral' => array('arrayExtra' => array('name' => 'arrayExtra', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'sql', 'value' => $arrayExtra), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto t02WW; NNP1O: file_put_contents($arquivo, $form->campoValorEnviado('arrayExtra')); goto C3v_t; eyPC9: f949x: goto uUJC5; SIGwr: $recover = !empty($r_recover) ? 1 : 0; goto DcNUj; ob2Vb: goto KvtrE; goto PBOg1; RCMyN: if ($r_modoModelo) { goto A1Llo; } goto JF4OA; DwlLH: LTE78: goto uN2X3; K6WUr: $botoesDir .= '<button type="submit" id="sistemaPGridExtraBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto vvwC_; Xjefk: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-extra' : $dir . $pre . md5($r_grid) . '-extra'; goto KKS9H; uUJC5: $g['smarty']->display('form.tpl'); goto L3Vye; KKS9H: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-extra' : $arquivo; goto l2qh4; JF4OA: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto qslXg; JCHSB: if ($formMultiTab) { goto hLLB_; } goto ArWxe; Gk1JS: $form_campo_pk = 'D000_Id'; goto N764C; e1TZa: $g['smarty']->display('buttons-floatBottom.tpl'); goto wRt7y; z1zg1: $botoesEsq .= '<button type="submit" onclick="pegarExtra(\'show\'); return false;">Mostrar Original</button>'; goto gsQIB; ph5tm: $g['smarty']->assign('botoesMid', $botoesMid); goto af1fS; WU9Pn: if ($formCodigo == 1) { goto WCTVi; } goto oCiKR; jwo7q: hLLB_: goto VW5vY; TTdfo: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto Eytcp; } goto WU9Pn; xQAIm: $hidden = !empty($r_sistemaPGridExtraForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto RCMyN; JaTu5: $g["smarty"]->assign("formId", $formId); goto eyPC9; be3fI: if ($formMultiTab) { goto f949x; } goto JaTu5; mdTeR: WCTVi: goto iwikK; Y_od_: $arrayExtra = file_get_contents($arquivo); goto H6LwS; bSI21: $r_grid = urldecode($r_grid); goto Xjefk; Jqvak: $botoesEsq .= '<button type="submit" id="sistemaPGridExtraBtnRemover">Remover</button>'; goto HvltT; Xou7G: if (file_exists($arquivo)) { goto rny9W; } goto YBMnQ; x9SFN: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto jJJeJ; zafP5: goto UDEWx; goto upk_D; hXklE: $g['smarty']->assign('botoesEsq', $botoesEsq); goto ph5tm; ueJfd: vtiDu: goto ge5A5; L3Vye: $botoesMid = $botoesEsq = $botoesDir = ''; goto xQAIm; H6LwS: KvtrE: goto GKI15; ArWxe: $formId = uniqid(); goto OyMg2; DcNUj: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridExtraBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridExtraBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridExtraBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridExtraBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tfunction pegarExtra(opcao) {
\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t// no apache ele vem o caminho relativo
\t\t\t\t\tvar url = '{$r_grid}';
\t\t\t\t\tvar matches = url.match(/^\\/.*\$/);
\t\t\t\t\tif (!matches) {
\t\t\t\t\t\turl = '/' + url;
\t\t\t\t\t}
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: url,
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&pegarExtra=true&gridFiltrado=true' + \$('#{$g['divIdParent']}').data('vars'),
\t\t\t\t\t\ttype: 'post',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t// mensagem de erro (somente notificar)
\t\t\t\t\t\t\tif (matches) {
\t\t\t\t\t\t\t\t\$('<div>').html(matches[1]).appendTo(\$('#{$g['divId']}'));
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tvar matches = data.match(/<pegarExtra>([\\s\\S]*)<\\/pegarExtra>/m);
\t\t\t\t\t\t\tif (!matches) {
\t\t\t\t\t\t\t\t// mensagens de erro
\t\t\t\t\t\t\t\tif ('{$recover}' == 0) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayExtra').html(data);
\t\t\t\t\t\t\t\t\tfecharJanela('{$g['divId']}');
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tif (opcao == 'show') {
\t\t\t\t\t\t\t\t\tdialogConfirm('Personalização do Grid Original', '<textarea style="width: 500px; height: 400px;">' + matches[1] + '</textarea>');
\t\t\t\t\t\t\t\t} else if ('{$carregarOriginal}' == 1) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayExtra').html(matches[1]);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayExtra', 'docExtra');
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t}
\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayExtra', 'docExtra');
\t\t\t\t//pegarExtra();
\t\t\t} </script></div>
EOT;
goto zafP5; upk_D: case 'sistemaPGridJS': goto swBAc; lQ9Q2: $form = new Formulario('sistemaPGridJSForm'); goto tqtuL; GSZw9: MFvln: goto H59pn; zizAN: uXC30: goto RSD_f; niUsw: q3Izh: goto JP1dR; y72sc: $g['smarty']->assign('botoesDir', $botoesDir); goto SUoWZ; nbpEV: goto FxqRY; goto cRQjO; rLIeV: $g['smarty']->assign('formularioID', $form->formularioID()); goto s7GTx; SPWvi: if ($formCodigo == 1) { goto StvsY; } goto g9N0V; LedVR: $hidden = !empty($r_sistemaPGridJSForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto AMDRu; s7GTx: $g['smarty']->assign('table', $gerado['table']); goto E2s_h; j24pE: $form->definirFormularioArray($formArray); goto QSImT; C8Uw7: D35sw: goto b_imd; YT4A8: $arrayJS = $form->campoValorEnviado('arrayJS'); goto sZ8fH; PJI1z: echo gGeraAlertMsgErroForm($form); goto HQO3V; E2s_h: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto tddYt; F3eAu: $botoesDir .= '<button type="submit" id="sistemaPGridJSBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto d7cwK; Wk9c2: $formMultiTab = false; goto XAwxN; PvYyl: $botoesEsq .= '<button type="submit" onclick="mostrarJsOriginal(); return false;">Mostrar Original</button>'; goto TvTDu; Zqa2B: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-js' : $dir . $pre . md5($r_grid) . '-js'; goto PQUBN; SbtoJ: sgJCS: goto bSXR2; s5K4S: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridJS/'; goto pp0c5; bmrgu: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridJSBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridJSBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridJSBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayJS');
                function mostrarJsOriginal() {
\t\t\t\t    dialogConfirm('Personalização do JS Original', '<textarea style="width: 1000px; height: 600px;">' + \$('#originalJs').html() + '</textarea>');
\t\t\t\t}
\t\t\t} </script></div>
EOT;
goto gKCC_; tddYt: echo $gerado['pagina']; goto NCaf1; Pyf39: $existeArquivo = true; goto ULPtu; hzfRA: if (isset($r_sistemaPGridJSForm)) { goto Rmcgd; } goto XACH2; AMDRu: if ($r_modoModelo) { goto vDXq2; } goto xSVm5; Rr9wG: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto Wk9c2; VayNF: $g['smarty']->assign('botoesMid', $botoesMid); goto y72sc; wQ9lS: $enviar_btnId = 'sistemaPGridJSBtn'; goto s5K4S; NCaf1: if ($formMultiTab) { goto sgJCS; } goto uvNv_; epicr: $existeArquivo = false; goto ZcCHq; HQO3V: FxqRY: goto zizAN; Dz0LH: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto niUsw; H59pn: $arrayJS = file_get_contents($arquivo); goto Pyf39; sZ8fH: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto DygQC; g9N0V: if (!($formCodigo == 2)) { goto umfd1; } goto Ugcz5; ja9d7: goto q3Izh; goto guYX9; X1cci: goto cIikK; goto GSZw9; cRQjO: StvsY: goto PJI1z; necGj: $g['smarty']->assign('botoesEsq', $botoesEsq); goto VayNF; H83py: $formId = uniqid(); goto ja9d7; ebi3r: goto UDEWx; goto lgUsz; RSD_f: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto rLIeV; VMXHH: $botoesEsq .= '<button type="submit" id="sistemaPGridJSBtnRemover">Remover</button>'; goto C8Uw7; PQUBN: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-js' : $arquivo; goto NomxJ; tqtuL: $formArray = array('campos' => array('geral' => array('arrayJS' => array('name' => 'arrayJS', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'javascript', 'value' => $arrayJS), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto j24pE; J6NP3: $gridId = md5($r_grid); goto Zvpsz; DygQC: umfd1: goto nbpEV; swBAc: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto Rr9wG; JP1dR: $form_tabela = 'D000'; goto CzW9q; XACH2: Rmcgd: goto X1cci; Y_d48: goto nPsPF; goto KagVt; KagVt: vDXq2: goto F3eAu; uvNv_: $g["smarty"]->assign("formId", $formId); goto SbtoJ; pp0c5: $r_grid = urldecode($r_grid); goto Zqa2B; rDFz4: echo <<<EOT
<pre id="originalJs" style="display: none;">
{$jsOriginal}
</pre>
EOT;
goto ebi3r; d7cwK: nPsPF: goto HyYAD; ZcCHq: if (file_exists($arquivo)) { goto MFvln; } goto hzfRA; b_imd: $g['smarty']->assign('botoesId', $g['divId']); goto necGj; bSXR2: $g['smarty']->display('form.tpl'); goto bmrgu; HyYAD: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto PvYyl; QSImT: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto uXC30; } goto SPWvi; XAwxN: if ($formMultiTab) { goto JxCkp; } goto H83py; guYX9: JxCkp: goto Dz0LH; CzW9q: $form_campo_pk = 'D000_Id'; goto wQ9lS; TvTDu: if (!$existeArquivo) { goto D35sw; } goto VMXHH; xSVm5: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto r6RrB; SUoWZ: $g['smarty']->display('buttons-floatBottom.tpl'); goto J6NP3; Zvpsz: $jsOriginal = apc_fetch("grid-js-{$gridId}"); goto rDFz4; ULPtu: cIikK: goto lQ9Q2; Ugcz5: file_put_contents($arquivo, $form->campoValorEnviado('arrayJS')); goto YT4A8; gKCC_: $botoesMid = $botoesEsq = $botoesDir = ''; goto LedVR; NomxJ: $arrayJS = <<<EOT
// Variáveis disponíveis:
// [[divId]] [[divIdRoot]] [[divIdParent]]

// Click na linha do grid
/*
acaoClickLinha('[[divId]]', function(data, array) {
    // campos disponíveis no array: todosCampos, divId, divIdRoot, divIdParent, parametros
    abrirJanela(data, array['divIdRoot'], array['divId'], unique(), '', 'Título janela - ID: ' + array['todosCampos']['CAMPO'], 'URL', '&acaoId=' + encodeURIComponent(array['todosCampos']['CAMPO']), [980,550]);
});
*/

// Remover campos editáveis
//\$('#[[divId]] .grid .campoEditavel').unbind('click').unbind('click').bind('click', function(event) { return false; });

// Não permitir ordenação
//\$('#[[divId]] .grid th').removeAttr('onclick').css({'cursor': 'auto'});

// Remover filtro
//\$('#[[divId]] .gridFiltro').remove();

// Desabilitar o refresh automático deste grid
//\$('#[[divId]] .grid').data('lastTimeUpdate', 'never');

// Já vir selecionado a primeira linha (o recurso setas para baixo e cima)
//\$('#[[divId]]').find('tr:eq(1)').addClass('linhaSelecionada');

// Alterar o campo de um formulário
// \$('#[[divIdParent]] .itemForm-CAMPO').val('');

// Fechar a janela atual
// fecharJanela('[[divId]]');

// Remover as opções (menu) do grid
//\$('#[[divId]] .gridOptTop').remove();

// Remover os totais do grid
//\$('#[[divId]] .gridTotais').remove();

// Remover o botão de maximizar do grid
//\$('#[[divId]]').addClass('removerGridTheadMaximizar');

// Ação ao fechar a janela
/*
acaoFecharJanela('[[divId]]', function(data, ui) {
    atualizarLinhaGrid('[[divIdParent]]');
});
*/
EOT;
goto epicr; r6RrB: $botoesDir .= '<button type="submit" id="sistemaPGridJSBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto Y_d48; lgUsz: case 'sistemaPGridHTMLColunas': goto U67Aj; R2HAK: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto uOFuz; KtTJ4: $botoesEsq .= '<button type="submit" onclick="pegarHTMLColunas(\'show\'); return false;">Mostrar Original</button>'; goto wxC7v; KVRqb: if (!($formCodigo == 2)) { goto AoCRs; } goto r2Fm9; HCpgj: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridHTMLColunas/'; goto KOsIS; v9mxA: echo $gerado['pagina']; goto zSNEb; yM1Xt: $formMultiTab = false; goto W5z6r; tQ61t: $arrayHTMLColunas = file_get_contents($arquivo); goto TK6ZJ; j2lhm: $g['smarty']->assign('botoesId', $g['divId']); goto LTtar; zS5e3: goto od0J0; goto w7T71; zs7z9: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto U53OL; EmNXy: if ($formCodigo == 1) { goto o6IYr; } goto KVRqb; boGEv: fg_A5: goto j2lhm; AGR4z: ghQAO: goto drdCK; zZh0P: $g['smarty']->display('buttons-floatBottom.tpl'); goto JONMV; W5z6r: if ($formMultiTab) { goto tJzlo; } goto ulaLq; rctIV: y4YEb: goto o93cE; qfiUB: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto v9mxA; qIoh2: if ($r_modoModeloxxx) { goto y4YEb; } goto R2HAK; TK6ZJ: qrwQl: goto jN2Zv; ANuqh: goto UDEWx; goto GsBdV; uOFuz: $botoesDir .= '<button type="submit" id="sistemaPGridHTMLColunasBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto V39Ti; Stjkz: if (isset($r_sistemaPGridHTMLColunasForm)) { goto bte2P; } goto Ikz4k; WSiYb: echo gGeraAlertMsgErroForm($form); goto EtTXt; ulaLq: $formId = uniqid(); goto USTyk; V39Ti: goto JbcfB; goto rctIV; SYBY0: lcCp5: goto nf82H; F04BZ: $g['smarty']->assign('botoesMid', $botoesMid); goto BXg1m; jN2Zv: $form = new Formulario('sistemaPGridHTMLColunasForm'); goto xp1T6; ZIgOD: $enviar_btnId = 'sistemaPGridHTMLColunasBtn'; goto HCpgj; lj2S8: r4UxJ: goto tQ61t; Lsb32: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto SYBY0; ePB9H: y0qyu: goto O92_E; U53OL: AoCRs: goto zS5e3; zSNEb: if ($formMultiTab) { goto y0qyu; } goto jbO6g; dJfJk: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto ghQAO; } goto EmNXy; ipFIb: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-print-colunas' : $arquivo; goto u5ELV; LTtar: $g['smarty']->assign('botoesEsq', $botoesEsq); goto F04BZ; qdxXS: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridHTMLColunasBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridHTMLColunasBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridHTMLColunasBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridHTMLColunasBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tfunction pegarHTMLColunas(opcao) {
\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t// no apache ele vem o caminho relativo
\t\t\t\t\tvar url = '{$r_grid}';
\t\t\t\t\tvar matches = url.match(/^\\/.*\$/);
\t\t\t\t\tif (!matches) {
\t\t\t\t\t\turl = '/' + url;
\t\t\t\t\t}
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: url,
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&pegarHTMLColunas=true&mostrarOriginal=true&gridFiltrado=true' + \$('#{$g['divIdParent']}').data('vars'),
\t\t\t\t\t\ttype: 'post',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t// mensagem de erro (somente notificar)
\t\t\t\t\t\t\tif (matches) {
\t\t\t\t\t\t\t\t\$('<div>').html(matches[1]).appendTo(\$('#{$g['divId']}'));
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tvar matches = data.match(/<pegarHTMLColunas>([\\s\\S]*)<\\/pegarHTMLColunas>/m);
\t\t\t\t\t\t\tif (!matches) {
\t\t\t\t\t\t\t\t// mensagens de erro
\t\t\t\t\t\t\t\tif ('{$recover}' == 0) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayHTMLColunas').html(data);
\t\t\t\t\t\t\t\t\tfecharJanela('{$g['divId']}');
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tif (opcao == 'show') {
\t\t\t\t\t\t\t\t\tdialogConfirm('Personalização do Grid Original', '<textarea style="width: 500px; height: 400px;">' + matches[1] + '</textarea>');
\t\t\t\t\t\t\t\t} else if ('{$carregarOriginal}' == 1) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayHTMLColunas').html(matches[1]);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayHTMLColunas', 'docColunas');
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t}
\t\t\t\tpegarHTMLColunas();
\t\t\t} </script></div>
EOT;
goto ANuqh; nf82H: $form_tabela = 'D000'; goto eRxXw; r2Fm9: file_put_contents($arquivo, $form->campoValorEnviado('arrayHTMLColunas')); goto FfJEk; tE30b: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-print-colunas' : $dir . $pre . md5($r_grid) . '-print-colunas'; goto ipFIb; EtTXt: od0J0: goto AGR4z; Ikz4k: bte2P: goto zJSJD; drdCK: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto pkXgW; u5ELV: $arrayHTMLColunas = ''; goto UyeAu; pF461: $g['smarty']->assign('table', $gerado['table']); goto qfiUB; JONMV: $carregarOriginal = 0; goto BrXHP; zN7ik: tJzlo: goto Lsb32; FfJEk: $arrayHTMLColunas = $form->campoValorEnviado('arrayHTMLColunas'); goto zs7z9; w7T71: o6IYr: goto WSiYb; wm0rS: $botoesEsq .= '<button type="submit" id="sistemaPGridHTMLColunasBtnRemover">Remover</button>'; goto boGEv; AwK4y: $hidden = !empty($r_sistemaPGridHTMLColunasForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto qIoh2; pkXgW: $g['smarty']->assign('formularioID', $form->formularioID()); goto pF461; BrXHP: $recover = !empty($r_recover) ? 1 : 0; goto qdxXS; KOsIS: $r_grid = urldecode($r_grid); goto tE30b; WvEpj: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto yM1Xt; xp1T6: $formArray = array('campos' => array('geral' => array('arrayHTMLColunas' => array('name' => 'arrayHTMLColunas', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'plaintext', 'value' => $arrayHTMLColunas), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto SYyMC; BXg1m: $g['smarty']->assign('botoesDir', $botoesDir); goto zZh0P; U67Aj: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto WvEpj; avuuM: $botoesMid = $botoesEsq = $botoesDir = ''; goto AwK4y; UyeAu: if (file_exists($arquivo)) { goto r4UxJ; } goto Stjkz; lrr4s: JbcfB: goto DXEi5; zJSJD: goto qrwQl; goto lj2S8; SYyMC: $form->definirFormularioArray($formArray); goto dJfJk; USTyk: goto lcCp5; goto zN7ik; eRxXw: $form_campo_pk = 'D000_Id'; goto ZIgOD; o93cE: $botoesDir .= '<button type="submit" id="sistemaPGridHTMLColunasBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto lrr4s; DXEi5: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto KtTJ4; O92_E: $g['smarty']->display('form.tpl'); goto avuuM; wxC7v: if (!$arrayHTMLColunas) { goto fg_A5; } goto wm0rS; jbO6g: $g["smarty"]->assign("formId", $formId); goto ePB9H; GsBdV: case 'sistemaPGridHTMLCabecalho': goto ISkmN; TtAlQ: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-print-cabecalho' : $arquivo; goto ADtzb; U3Wuc: $g['smarty']->assign('botoesDir', $botoesDir); goto LAf3b; uL7KM: $form->definirFormularioArray($formArray); goto o_Hxf; WcoOo: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto A7xgm; IrXYK: $form_campo_pk = 'D000_Id'; goto ove1b; zlHlp: if ($r_modoModelo) { goto gp5b6; } goto vp77S; qW3hF: goto eoahk; goto mbWjR; DBi2f: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto Ip4KL; ONf_v: $botoesDir .= '<button type="submit" id="sistemaPGridHTMLCabecalhoBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto vIKfp; AW4lY: $g['smarty']->assign('botoesMid', $botoesMid); goto U3Wuc; Mc7ob: goto UDEWx; goto b9qAG; N9CJz: NfiXt: goto WcoOo; S0iBU: $formArray = array('campos' => array('geral' => array('arrayHTMLCabecalho' => array('name' => 'arrayHTMLCabecalho', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'html', 'value' => $arrayHTMLCabecalho), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto uL7KM; BGqHb: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto bPS_B; IcXzm: $hidden = !empty($r_sistemaPGridHTMLCabecalhoForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto zlHlp; m9Qo6: $g['smarty']->assign('botoesId', $g['divId']); goto MAJaH; lzqBn: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto yXKKf; Ztcwv: echo gGeraAlertMsgErroForm($form); goto RRue_; nzbtf: $arrayHTMLCabecalho = $form->campoValorEnviado('arrayHTMLCabecalho'); goto ITBo3; n5xR1: $botoesEsq .= '<button type="submit" id="sistemaPGridHTMLCabecalhoBtnRemover">Remover</button>'; goto sHSZL; A7xgm: if (!$existeArquivo) { goto zdinK; } goto n5xR1; MAJaH: $g['smarty']->assign('botoesEsq', $botoesEsq); goto AW4lY; sHSZL: zdinK: goto m9Qo6; jMs01: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridHTMLCabecalho/'; goto i3bOp; r3BZc: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridHTMLCabecalhoBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridHTMLCabecalhoBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridHTMLCabecalhoBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayHTMLCabecalho');
\t\t\t} </script></div>
EOT;
goto tEwU2; fLDXM: $formMultiTab = false; goto lVlY6; vp77S: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto ONf_v; fJwqR: $form = new Formulario('sistemaPGridHTMLCabecalhoForm'); goto S0iBU; u4Xox: $botoesDir .= '<button type="submit" id="sistemaPGridHTMLCabecalhoBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto N9CJz; i3bOp: $r_grid = urldecode($r_grid); goto yH_Cy; wzjIN: T1HcD: goto lzqBn; o_Hxf: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto T1HcD; } goto d8vRF; q_kQq: $formId = uniqid(); goto qW3hF; e9Eaa: $arrayHTMLCabecalho = file_get_contents($arquivo); goto iFm3d; bPS_B: eoahk: goto c1PtH; wqMKu: goto Nphdi; goto Ftv5A; cycpg: if (!($formCodigo == 2)) { goto sFmJf; } goto EL9xk; OagGM: wzJDv: goto fJwqR; B3Mq3: $g['smarty']->display('form.tpl'); goto r3BZc; yH_Cy: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-print-cabecalho' : $dir . $pre . md5($r_grid) . '-print-cabecalho'; goto TtAlQ; FCBj3: if (isset($r_sistemaPGridHTMLCabecalhoForm)) { goto cPBnR; } goto TdzGN; Ftv5A: iBS31: goto Ztcwv; yXKKf: $g['smarty']->assign('formularioID', $form->formularioID()); goto Fmtam; iFm3d: $existeArquivo = true; goto OagGM; c1PtH: $form_tabela = 'D000'; goto IrXYK; mbWjR: FzpIh: goto BGqHb; Fmtam: $g['smarty']->assign('table', $gerado['table']); goto DBi2f; myrAT: sFmJf: goto wqMKu; aHNqO: goto wzJDv; goto kVJbM; Lq9L1: G9diy: goto B3Mq3; yFi6_: $existeArquivo = false; goto vNnS8; p4Amk: $g["smarty"]->assign("formId", $formId); goto Lq9L1; vIKfp: goto NfiXt; goto bjVgI; Ip4KL: echo $gerado['pagina']; goto bm1_W; d8vRF: if ($formCodigo == 1) { goto iBS31; } goto cycpg; EL9xk: file_put_contents($arquivo, $form->campoValorEnviado('arrayHTMLCabecalho')); goto nzbtf; vNnS8: if (file_exists($arquivo)) { goto NFFLH; } goto FCBj3; kVJbM: NFFLH: goto e9Eaa; bm1_W: if ($formMultiTab) { goto G9diy; } goto p4Amk; ISkmN: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto iUge4; tEwU2: $botoesMid = $botoesEsq = $botoesDir = ''; goto IcXzm; LAf3b: $g['smarty']->display('buttons-floatBottom.tpl'); goto Mc7ob; ove1b: $enviar_btnId = 'sistemaPGridHTMLCabecalhoBtn'; goto jMs01; ITBo3: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto myrAT; RRue_: Nphdi: goto wzjIN; bjVgI: gp5b6: goto u4Xox; ADtzb: $arrayHTMLCabecalho = <<<EOT
<div>
\t<img src="[[logoMarca]]" style="float: left;" />
\t<div style="float: left; margin: 20px;">
\t\t<h1>Título do Relatório</h1>
\t</div>
</div>
EOT;
goto yFi6_; iUge4: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto fLDXM; TdzGN: cPBnR: goto aHNqO; lVlY6: if ($formMultiTab) { goto FzpIh; } goto q_kQq; b9qAG: case 'sistemaPGridGridLinha': goto iV0Md; E5jUi: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-gridLinha' : $dir . $pre . md5($r_grid) . '-gridLinha'; goto emnKo; da56d: $carregarOriginal = 0; goto fpTtT; u4vN2: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridLinhaBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridLinhaBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridGridLinhaBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridGridLinhaBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tfunction pegarGridLinha(opcao) {
\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t// no apache ele vem o caminho relativo
\t\t\t\t\tvar url = '{$r_grid}';
\t\t\t\t\tvar matches = url.match(/^\\/.*\$/);
\t\t\t\t\tif (!matches) {
\t\t\t\t\t\turl = '/' + url;
\t\t\t\t\t}
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: url,
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&pegarGridLinha=true&gridFiltrado=true' + \$('#{$g['divIdParent']}').data('vars'),
\t\t\t\t\t\ttype: 'post',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t// mensagem de erro (somente notificar)
\t\t\t\t\t\t\tif (matches) {
\t\t\t\t\t\t\t\t\$('<div>').html(matches[1]).appendTo(\$('#{$g['divId']}'));
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tvar matches = data.match(/<pegarGridLinha>([\\s\\S]*)<\\/pegarGridLinha>/m);
\t\t\t\t\t\t\tif (!matches) {
\t\t\t\t\t\t\t\t// mensagens de erro
\t\t\t\t\t\t\t\tif ('{$recover}' == 0) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayGridLinha').html(data);
\t\t\t\t\t\t\t\t\tfecharJanela('{$g['divId']}');
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tif (opcao == 'show') {
\t\t\t\t\t\t\t\t\tdialogConfirm('Personalização do Grid Original', '<textarea style="width: 500px; height: 400px;">' + matches[1] + '</textarea>');
\t\t\t\t\t\t\t\t} else if ('{$carregarOriginal}' == 1) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayGridLinha').html(matches[1]);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayGridLinha');
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t}
\t\t\t\tpegarGridLinha();
\t\t\t} </script></div>
EOT;
goto lqkQq; edztS: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridGridLinha/'; goto yGHLk; m279G: if (!($formCodigo == 2)) { goto tpQiS; } goto PMCBm; ldvi_: $arrayGridLinha = ''; goto zTJrI; us6jt: $botoesDir .= '<button type="submit" id="sistemaPGridGridLinhaBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto hU5Ch; nqwom: $enviar_btnId = 'sistemaPGridGridLinhaBtn'; goto edztS; ZLnbJ: if (!$arrayGridLinha) { goto eSQxW; } goto wpIEa; zxAME: R3ked: goto Mwbve; Pz1Go: tpQiS: goto IBnXY; HCfjz: $hidden = !empty($r_sistemaPGridLinhaForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto ZoUBM; fpTtT: $recover = !empty($r_recover) ? 1 : 0; goto u4vN2; LJdpa: $formId = uniqid(); goto XMhb8; sBNVp: LLRsw: goto kXPHx; G7atT: goto QycWw; goto X9F88; B5g6d: $form_campo_pk = 'D000_Id'; goto nqwom; IBnXY: goto SEOKA; goto mCnx3; SmfxT: echo $gerado['pagina']; goto n_VOW; XiLR4: SEOKA: goto zxAME; wpIEa: $botoesEsq .= '<button type="submit" id="sistemaPGridGridLinhaBtnRemover">Remover</button>'; goto IR2ZO; kszWC: if (isset($r_sistemaPGridGridLinhaForm)) { goto yWKeq; } goto jGiUi; o8nbM: $form->definirFormularioArray($formArray); goto u3yBH; Quw6k: $formMultiTab = false; goto CXhYL; Mwbve: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto amRyC; zLImI: rcxIG: goto YZvnh; yGHLk: $r_grid = urldecode($r_grid); goto E5jUi; oN7EC: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto cZhMJ; Ckclk: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto Quw6k; zff5l: if ($formCodigo == 1) { goto TVD_K; } goto m279G; PMCBm: file_put_contents($arquivo, $form->campoValorEnviado('arrayGridLinha')); goto pfREa; uBTXt: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto SmfxT; utkeH: $g['smarty']->assign('botoesDir', $botoesDir); goto wkyCq; Fw6YK: $g['smarty']->assign('table', $gerado['table']); goto uBTXt; XewIC: $botoesDir .= '<button type="submit" id="sistemaPGridGridLinhaBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto G7atT; c9uho: c1kuE: goto h43ZT; wkyCq: $g['smarty']->display('buttons-floatBottom.tpl'); goto da56d; iV0Md: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto Ckclk; mFKhS: vyHhx: goto ilohX; jGiUi: yWKeq: goto zXQsF; BQUga: $g['smarty']->assign('botoesMid', $botoesMid); goto utkeH; emnKo: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-gridLinha' : $arquivo; goto ldvi_; RMtdq: $formArray = array('campos' => array('geral' => array('arrayGridLinha' => array('name' => 'arrayGridLinha', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php', 'value' => $arrayGridLinha), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto o8nbM; CXhYL: if ($formMultiTab) { goto vyHhx; } goto LJdpa; mCnx3: TVD_K: goto p315l; zTJrI: if (file_exists($arquivo)) { goto c1kuE; } goto kszWC; Y2dtO: XysBf: goto GA3PJ; X9F88: vE688: goto us6jt; n_VOW: if ($formMultiTab) { goto XysBf; } goto AxPyi; cZhMJ: $botoesEsq .= '<button type="submit" onclick="pegarGridLinha(\'show\'); return false;">Mostrar Original</button>'; goto ZLnbJ; lqkQq: goto UDEWx; goto GP9vD; kXPHx: $form = new Formulario('sistemaPGridGridLinhaForm'); goto RMtdq; pfREa: $arrayGridLinha = $form->campoValorEnviado('arrayGridLinha'); goto YVneW; u1Wr5: $g['smarty']->assign('botoesEsq', $botoesEsq); goto BQUga; YVneW: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto Pz1Go; amRyC: $g['smarty']->assign('formularioID', $form->formularioID()); goto Fw6YK; AxPyi: $g["smarty"]->assign("formId", $formId); goto Y2dtO; p315l: echo gGeraAlertMsgErroForm($form); goto XiLR4; XMhb8: goto rcxIG; goto mFKhS; zXQsF: goto LLRsw; goto c9uho; u3yBH: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto R3ked; } goto zff5l; tLV22: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto XewIC; IR2ZO: eSQxW: goto GLXU5; GA3PJ: $g['smarty']->display('form.tpl'); goto SJLD_; ilohX: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto zLImI; ZoUBM: if ($r_modoModelo) { goto vE688; } goto tLV22; GLXU5: $g['smarty']->assign('botoesId', $g['divId']); goto u1Wr5; YZvnh: $form_tabela = 'D000'; goto B5g6d; hU5Ch: QycWw: goto oN7EC; SJLD_: $botoesMid = $botoesEsq = $botoesDir = ''; goto HCfjz; h43ZT: $arrayGridLinha = file_get_contents($arquivo); goto sBNVp; GP9vD: case 'sistemaPGridBotoes': goto jsduv; azGuh: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto CyISn; mhBBh: if (!$arquivoExiste) { goto PB5D5; } goto Hnje4; Qof13: $r_grid = urldecode($r_grid); goto AM0s9; WCkEx: $form = new Formulario('sistemaPGridBotoesForm'); goto Al3dO; YvzyJ: if (!empty($arrayBotoes)) { goto afF9I; } goto FrMbP; iQY0s: $g['smarty']->assign('botoesEsq', $botoesEsq); goto bbM7h; tyjAB: $formId = uniqid(); goto o2c0z; Al3dO: $formArray = array('campos' => array('geral' => array('arrayBotoes' => array('name' => 'arrayBotoes', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php', 'value' => $arrayBotoes), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto yeTuY; eCH6e: $botoesDir .= '<button type="submit" id="sistemaPGridBotoesBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto Wr4GO; dXVTi: $botoesDir .= '<button type="submit" id="sistemaPGridBotoesBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto ydEbm; oW3EX: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridBotoes/'; goto Qof13; Jekso: $arrayBotoes = file_get_contents($arquivo); goto QS84w; AhU3Z: $g['smarty']->assign('botoesDir', $botoesDir); goto kU5h8; MgJd6: goto UDEWx; goto Tw8ih; Wr4GO: PiInH: goto tnEFZ; bbM7h: $g['smarty']->assign('botoesMid', $botoesMid); goto AhU3Z; ibgSf: $arrayBotoes = ''; goto JNWu7; aa8uL: afF9I: goto WCkEx; bwzG8: jgFWB: goto DowSp; cwqWh: zpgUM: goto IAnFm; xsDp3: if (!($formCodigo == 2)) { goto vcGmy; } goto vK7WK; eANx1: q5wuv: goto Jekso; nDWB3: $g["smarty"]->assign("formId", $formId); goto bwzG8; nCe6S: $enviar_btnId = 'sistemaPGridBotoesBtn'; goto oW3EX; FisMH: echo gGeraAlertMsgErroForm($form); goto H6mdC; wVSOG: $arrayBotoes = $form->campoValorEnviado('arrayBotoes'); goto Hooyl; tMN0J: M_cWT: goto eCH6e; qXCl3: goto mjXLU; goto fvklC; H6mdC: mjXLU: goto OFmvQ; UC7ei: $g['smarty']->assign('botoesId', $g['divId']); goto iQY0s; zGwYa: iRFnz: goto WjDuK; a3quF: $g['smarty']->assign('formularioID', $form->formularioID()); goto qLfvT; WlNzt: goto CYhlI; goto eANx1; qLfvT: $g['smarty']->assign('table', $gerado['table']); goto Ts9wy; Ts9wy: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto z1sp6; o2c0z: goto iRFnz; goto cwqWh; whXJt: fOwiO: goto WlNzt; OFmvQ: uU2tS: goto gv8RE; FrMbP: $arrayBotoes = <<<'EOT'
<?
namespace hardness;

?>
EOT;
goto aa8uL; gv8RE: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto a3quF; Hnje4: $botoesEsq .= '<button type="submit" id="sistemaPGridBotoesBtnRemover">Remover</button>'; goto kolLt; IAnFm: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto zGwYa; Xqeng: CYhlI: goto YvzyJ; z1sp6: echo $gerado['pagina']; goto SrVJK; WjDuK: $form_tabela = 'D000'; goto yMvLH; vK7WK: file_put_contents($arquivo, $form->campoValorEnviado('arrayBotoes')); goto wVSOG; yMvLH: $form_campo_pk = 'D000_Id'; goto nCe6S; fvklC: TMGkw: goto FisMH; QS84w: $arquivoExiste = true; goto Xqeng; HWTWr: if ($r_modoModelo) { goto M_cWT; } goto YIPnx; Nporz: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto uU2tS; } goto ixrWd; yeTuY: $form->definirFormularioArray($formArray); goto Nporz; GqUBE: if ($formMultiTab) { goto zpgUM; } goto tyjAB; RT4m6: $hidden = !empty($r_sistemaPGridBotoesForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto HWTWr; Hooyl: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto b7ZqW; HkCFA: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-botoes' : $arquivo; goto ibgSf; b7ZqW: vcGmy: goto qXCl3; tnEFZ: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto mhBBh; ixrWd: if ($formCodigo == 1) { goto TMGkw; } goto xsDp3; JNWu7: $arquivoExiste = false; goto DjkA7; AM0s9: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-botoes' : $dir . $pre . md5($r_grid) . '-botoes'; goto HkCFA; MEAZC: $botoesMid = $botoesEsq = $botoesDir = ''; goto RT4m6; YIPnx: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto dXVTi; jsduv: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto azGuh; DowSp: $g['smarty']->display('form.tpl'); goto MEAZC; kolLt: PB5D5: goto UC7ei; CyISn: $formMultiTab = false; goto GqUBE; fd6S6: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridBotoesBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridBotoesBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridBotoesBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridBotoesBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayBotoes', 'docBotoes');
\t\t\t} </script></div>
EOT;
goto MgJd6; DjkA7: if (file_exists($arquivo)) { goto q5wuv; } goto T5n0R; SrVJK: if ($formMultiTab) { goto jgFWB; } goto nDWB3; ydEbm: goto PiInH; goto tMN0J; T5n0R: if (isset($r_sistemaPGridBotoesForm)) { goto fOwiO; } goto whXJt; kU5h8: $g['smarty']->display('buttons-floatBottom.tpl'); goto fd6S6; Tw8ih: case 'sistemaPGridTotais': goto f5ckk; wabSy: $g['smarty']->assign('table', $gerado['table']); goto DuvUZ; vZ8jr: wTL7s: goto pMn9f; DrDHt: $g['smarty']->assign('formularioID', $form->formularioID()); goto wabSy; AUsVk: goto gKgZl; goto vZ8jr; tVLd5: $formMultiTab = false; goto QZwA6; m562Q: echo $gerado['pagina']; goto W6ndq; jWDu4: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto q9wzl; q9wzl: Gw7Nh: goto H2643; CHcZ1: $formArray = array('campos' => array('geral' => array('arrayTotais' => array('name' => 'arrayTotais', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php', 'value' => $arrayTotais), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto wL2_u; R0u3L: file_put_contents($arquivo, $form->campoValorEnviado('arrayTotais')); goto KGKb3; q8sRZ: if (!$arquivoExiste) { goto ba8ca; } goto c8k3A; TeZKf: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridTotaisBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridTotaisBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridTotaisBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridTotaisBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayTotais', 'docTotais');
\t\t\t} </script></div>
EOT;
goto FpRjk; bV1_R: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto q8sRZ; TtRHd: if ($r_modoModelo) { goto W1bzm; } goto rHN52; pMn9f: echo gGeraAlertMsgErroForm($form); goto X1Y7C; IC77d: Ru_lM: goto AUsVk; okv5G: oMjui: goto QCGCu; bF2ND: $arquivoExiste = false; goto oCsq0; hBgNh: $form = new Formulario('sistemaPGridTotaisForm'); goto CHcZ1; TWlr3: if (!($formCodigo == 2)) { goto Ru_lM; } goto R0u3L; wL2_u: $form->definirFormularioArray($formArray); goto hmKpL; iJha9: goto iRJVV; goto okv5G; GARNz: Z_GuE: goto hBgNh; f5ckk: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto cw238; St7ka: goto Gw7Nh; goto seMJF; VONK6: iRJVV: goto Js9xE; CXy7O: $form_campo_pk = 'D000_Id'; goto J8N1G; Z9iJd: $g['smarty']->assign('botoesDir', $botoesDir); goto KQYrl; vntYY: $hidden = !empty($r_sistemaPGridTotaisForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto TtRHd; ALRTz: $g['smarty']->assign('botoesMid', $botoesMid); goto Z9iJd; SIaSm: lOQ58: goto ZBAeX; J8N1G: $enviar_btnId = 'sistemaPGridTotaisBtn'; goto aPSLU; bluq5: $botoesMid = $botoesEsq = $botoesDir = ''; goto vntYY; m7Avo: ba8ca: goto TzbWM; zeJTR: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-totais' : $dir . $pre . md5($r_grid) . '-totais'; goto P8fft; KUjph: pNlhQ: goto bV1_R; Js9xE: if (!empty($arrayTotais)) { goto Z_GuE; } goto IAgCc; ZBAeX: $g['smarty']->display('form.tpl'); goto bluq5; DuvUZ: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto m562Q; nKGWu: $botoesDir .= '<button type="submit" id="sistemaPGridTotaisBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto yuLmr; KGKb3: $arrayTotais = $form->campoValorEnviado('arrayTotais'); goto s3Fzg; QZwA6: if ($formMultiTab) { goto Xz1lU; } goto cOd3Y; X1Y7C: gKgZl: goto Q3PUV; EyMKZ: W1bzm: goto cNUM7; MsSW2: if (isset($r_sistemaPGridTotaisForm)) { goto LHYBE; } goto vsJqW; yNGvG: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto DrDHt; KQYrl: $g['smarty']->display('buttons-floatBottom.tpl'); goto I4vQJ; hmKpL: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto En56k; } goto zfaRj; n0FXn: $arrayTotais = ''; goto bF2ND; FpRjk: goto UDEWx; goto Ja13N; c8k3A: $botoesEsq .= '<button type="submit" id="sistemaPGridTotaisBtnRemover">Remover</button>'; goto m7Avo; uwHPc: $g['smarty']->assign('botoesEsq', $botoesEsq); goto ALRTz; FgYa4: $r_grid = urldecode($r_grid); goto zeJTR; Q3PUV: En56k: goto yNGvG; aUUjm: $g["smarty"]->assign("formId", $formId); goto SIaSm; seMJF: Xz1lU: goto jWDu4; vsJqW: LHYBE: goto iJha9; cw238: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto tVLd5; zfaRj: if ($formCodigo == 1) { goto wTL7s; } goto TWlr3; P8fft: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-totais' : $arquivo; goto n0FXn; EFRmX: $arquivoExiste = true; goto VONK6; TzbWM: $g['smarty']->assign('botoesId', $g['divId']); goto uwHPc; oCsq0: if (file_exists($arquivo)) { goto oMjui; } goto MsSW2; rHN52: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto nKGWu; H2643: $form_tabela = 'D000'; goto CXy7O; s3Fzg: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto IC77d; W6ndq: if ($formMultiTab) { goto lOQ58; } goto aUUjm; aPSLU: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridTotais/'; goto FgYa4; cNUM7: $botoesDir .= '<button type="submit" id="sistemaPGridTotaisBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto KUjph; QCGCu: $arrayTotais = file_get_contents($arquivo); goto EFRmX; yuLmr: goto pNlhQ; goto EyMKZ; I4vQJ: $recover = !empty($r_recover) ? 1 : 0; goto TeZKf; cOd3Y: $formId = uniqid(); goto St7ka; IAgCc: $arrayTotais = <<<'EOT'
<?
namespace hardness;

?>
EOT;
goto GARNz; Ja13N: case 'sistemaPGridTotaisNovo': goto fE0wS; CPbxy: $formArray = array('campos' => array('geral' => array('arrayTotais' => array('name' => 'arrayTotais', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'plaintext', 'value' => $arrayTotais), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto egpPa; EPa8P: TCSIc: goto MiKyw; GxTrR: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto XVBpl; ngEIj: eQTPj: goto EPa8P; T6KYC: $form_campo_pk = 'D000_Id'; goto bWFxP; cRBaG: file_put_contents($arquivo, $form->campoValorEnviado('arrayTotais')); goto UVz1o; nFcsP: s0S0K: goto Z0QpZ; lK76A: $g['smarty']->display('buttons-floatBottom.tpl'); goto SfanK; ifQGX: $g['smarty']->assign('botoesId', $g['divId']); goto i1eZf; cw28n: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto I9j1r; ntG3b: $arquivoExiste = false; goto zfowe; pmM7o: Kb2MJ: goto za3K4; gKh5Z: $botoesEsq .= '<button type="submit" id="sistemaPGridTotaisNovoBtnRemover">Remover</button>'; goto YHVOX; MiKyw: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto vtYbx; CIP8z: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridTotaisNovoBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridTotaisNovoBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridTotaisNovoBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridTotaisNovoBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayTotais', 'docTotaisNovo');
\t\t\t\tfunction mostrarOriginal() {
\t\t\t\t    dialogConfirm('Personalização dos Totais Original', '<textarea style="width: 500px; height: 400px;">' + \$('#originalTotais').html() + '</textarea>');
\t\t\t\t}
\t\t\t} </script></div>
EOT;
goto Dib3K; Gjnmz: $arrayTotais = file_get_contents($arquivo); goto MrAr2; cz_2u: echo <<<EOT
<pre id="originalTotais" style="display: none;">
{$gridTotaisOriginal}
</pre>
EOT;
goto U03YG; XVBpl: $formMultiTab = false; goto CqFA4; elu0N: if ($formCodigo == 1) { goto MSfZK; } goto TUdx_; za3K4: goto eQTPj; goto Y5BWy; qe5mz: $g["smarty"]->assign("formId", $formId); goto yWFw4; Dib3K: $gridId = md5($r_grid); goto OU6cf; A42DJ: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridTotaisNovo/'; goto UzGR5; F7Qx8: if (!$arquivoExiste) { goto RUO4y; } goto gKh5Z; JFyiX: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto JqiFF; SfanK: $recover = !empty($r_recover) ? 1 : 0; goto CIP8z; uXeH5: $g['smarty']->display('form.tpl'); goto rXIgX; HZHrL: goto prKCK; goto u8jLf; vtYbx: $g['smarty']->assign('formularioID', $form->formularioID()); goto PwIEI; UzGR5: $r_grid = urldecode($r_grid); goto LLvvL; gUmZg: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto rs3K5; wmrYi: $form = new Formulario('sistemaPGridTotaisNovoForm'); goto CPbxy; IIpB0: J23Oy: goto Oh3Lp; i1eZf: $g['smarty']->assign('botoesEsq', $botoesEsq); goto i4_oV; ltl18: if ($r_modoModelo) { goto mdU2a; } goto JFyiX; rXIgX: $botoesMid = $botoesEsq = $botoesDir = ''; goto UGcQD; yWFw4: BHlej: goto uXeH5; YHVOX: RUO4y: goto ifQGX; FAR5V: if ($formMultiTab) { goto BHlej; } goto qe5mz; h54Nt: $arrayTotais = ''; goto ntG3b; OU6cf: $gridTotais = Personalizacoes::getGridTotais($gridId); goto uPpuD; u8jLf: mdU2a: goto peiVT; X8mA9: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-totais-novo' : $arquivo; goto h54Nt; UVz1o: $arrayTotais = $form->campoValorEnviado('arrayTotais'); goto Dmd1H; U03YG: goto UDEWx; goto ZckPB; rs3K5: echo $gerado['pagina']; goto FAR5V; egpPa: $form->definirFormularioArray($formArray); goto XwsLj; JqiFF: $botoesDir .= '<button type="submit" id="sistemaPGridTotaisNovoBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto HZHrL; CqFA4: if ($formMultiTab) { goto J23Oy; } goto W4K1p; Y5BWy: MSfZK: goto WsHn6; bWFxP: $enviar_btnId = 'sistemaPGridTotaisNovoBtn'; goto A42DJ; Dmd1H: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto pmM7o; heeAe: FMrri: goto wmrYi; Z0QpZ: $form_tabela = 'D000'; goto T6KYC; unOBo: prKCK: goto cw28n; WsHn6: echo gGeraAlertMsgErroForm($form); goto ngEIj; Kq6bx: goto s0S0K; goto IIpB0; i4_oV: $g['smarty']->assign('botoesMid', $botoesMid); goto mDhdK; zfowe: if (!file_exists($arquivo)) { goto FMrri; } goto Gjnmz; LLvvL: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-totais-novo' : $dir . $pre . md5($r_grid) . '-totais-novo'; goto X8mA9; TUdx_: if (!($formCodigo == 2)) { goto Kb2MJ; } goto cRBaG; MrAr2: $arquivoExiste = true; goto heeAe; fE0wS: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto GxTrR; W4K1p: $formId = uniqid(); goto Kq6bx; peiVT: $botoesDir .= '<button type="submit" id="sistemaPGridTotaisNovoBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto unOBo; UGcQD: $hidden = !empty($r_sistemaPGridTotaisNovoForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto ltl18; XwsLj: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto TCSIc; } goto elu0N; Oh3Lp: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto nFcsP; mDhdK: $g['smarty']->assign('botoesDir', $botoesDir); goto lK76A; PwIEI: $g['smarty']->assign('table', $gerado['table']); goto gUmZg; uPpuD: $gridTotaisOriginal = Personalizacoes::converterPersonalizacao($gridTotais); goto cz_2u; I9j1r: $botoesEsq .= '<button type="submit" onclick="mostrarOriginal(); return false;">Mostrar Original</button>'; goto F7Qx8; ZckPB: case 'sistemaPGridConfiguracoes': goto KM0IE; f9kg2: if (!$arquivoExiste) { goto hSHkH; } goto ym0QH; OxoGB: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto YUbsV; KM0IE: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto OxoGB; jZQ8e: JdfLY: goto zOENl; IvCgI: $desabilitaOpcaoEmail = $arrayConfig['desabilitaOpcaoEmail']; goto jU0Yd; B1hO6: $botoesMid = $botoesEsq = $botoesDir = ''; goto YQBnL; Wltk5: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto f9kg2; DHgKY: hSHkH: goto uG8ra; RTWw_: zGQMs: goto pTdXV; D4GHb: DikVg: goto tfsIh; eX5c7: if (isset($r_sistemaPGridConfiguracoesForm)) { goto yFgJI; } goto nN1w4; nN1w4: yFgJI: goto I0XrG; UDaO4: goto JdfLY; goto Pnr7O; V7Ht2: $form = new Formulario('sistemaPGridConfiguracoesForm'); goto VLzGv; igWD_: if ($formMultiTab) { goto cxsAw; } goto CM3dR; uG8ra: $g['smarty']->assign('botoesId', $g['divId']); goto gmqmR; vYsFU: $arrayConfig = file_get_contents($arquivo); goto lKNsc; Z6IpZ: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridConfiguracoes/'; goto zm6l7; UkrE5: $form_campo_pk = 'D000_Id'; goto UuNXL; wvjaq: goto zGQMs; goto PfLOx; OEmyz: $g['smarty']->display('buttons-floatBottom.tpl'); goto IkP3p; pTdXV: G46cS: goto vRoU7; C28jw: foreach ($form->formArray['campos']['geral'] as $campo => $vars) { goto tiuoL; vKCba: $arrayConfig[$campo] = $vars['processed_value']; goto mjJUC; K3Sft: goto R4yIs; goto cRc11; mjJUC: R4yIs: goto EmQTJ; cRc11: Ir72x: goto vKCba; tiuoL: if (!($campo == 'Usuario_C007_Id' || $campo == 'Modelo_Nome' || empty($vars['processed_value']))) { goto Ir72x; } goto K3Sft; EmQTJ: } goto qsdGU; gzxoe: gSc8A: goto Wltk5; UuNXL: $enviar_btnId = 'sistemaPGridConfiguracoesBtn'; goto Z6IpZ; qp8EW: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-config' : $arquivo; goto pdyrN; a0pxN: GOCEi: goto V7Ht2; vRoU7: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto h5k_G; fZlGi: goto gSc8A; goto UGceJ; gpDGo: $g['smarty']->assign('table', $gerado['table']); goto FY8Nf; FY8Nf: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto nf3Dw; UGceJ: hhZox: goto WgiMR; OPsI1: $filtroObrigatorio = $arrayConfig['filtroObrigatorio']; goto G8FCX; DUFoS: if (file_exists($arquivo)) { goto oLvr2; } goto eX5c7; h5k_G: $g['smarty']->assign('formularioID', $form->formularioID()); goto gpDGo; fSHYX: if ($formMultiTab) { goto h9x_3; } goto nikjI; XsKI5: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto W42hQ; xsS6x: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-config' : $dir . $pre . md5($r_grid) . '-config'; goto qp8EW; ym0QH: $botoesEsq .= '<button type="submit" id="sistemaPGridConfiguracoesBtnRemover">Remover</button>'; goto DHgKY; SWLdI: if (!($formCodigo == 2)) { goto bFGHU; } goto J3vYS; J3vYS: $arrayConfig = array(); goto C28jw; W42hQ: bFGHU: goto wvjaq; etp7E: NOJrE: goto kl0Je; nikjI: $formId = uniqid(); goto UDaO4; jKIbR: $historicoFiltros = $arrayConfig['historicoFiltros']; goto FHYTU; CM3dR: $g["smarty"]->assign("formId", $formId); goto MLapv; gZdFv: file_put_contents($arquivo, serialize($arrayConfig)); goto XsKI5; lKNsc: $arquivoExiste = true; goto T0PAc; gmqmR: $g['smarty']->assign('botoesEsq', $botoesEsq); goto evTDH; ZT5Fv: $limiteRegistros = '3500'; goto WQMBu; WgiMR: $botoesDir .= '<button type="submit" id="sistemaPGridConfiguracoesBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto gzxoe; Z9K5v: $g['smarty']->display('formEditor.tpl'); goto B1hO6; I0XrG: goto GEXSg; goto uiAOn; Pnr7O: h9x_3: goto PuygZ; c5kmp: goto UDEWx; goto zlfr2; kl0Je: $arrayConfig = unserialize($arrayConfig); goto fZmSW; mQgrh: $botoesDir .= '<button type="submit" id="sistemaPGridConfiguracoesBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto fZlGi; jU0Yd: $desabilitaOpcaoPDF = $arrayConfig['desabilitaOpcaoPDF']; goto Wo2pF; NpLrW: $form->definirFormularioArray($formArray); goto R20vP; PfLOx: EJ0sz: goto NqOOY; uiAOn: oLvr2: goto vYsFU; pxf1I: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto mQgrh; Hk3mg: $arquivoExiste = false; goto DUFoS; YQBnL: $hidden = !empty($r_sistemaPGridConfiguracoesForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto S3i90; YUbsV: $formMultiTab = false; goto fSHYX; S3i90: if ($r_modoModelo) { goto hhZox; } goto pxf1I; nf3Dw: echo $gerado['pagina']; goto igWD_; WQMBu: goto DikVg; goto etp7E; evTDH: $g['smarty']->assign('botoesMid', $botoesMid); goto dnbcy; rPY7A: echo "<th>"; goto G0ZsJ; zm6l7: $r_grid = urldecode($r_grid); goto xsS6x; VhR7e: $form_editor = array('geral' => '{"limiteRegistros":{"top":99,"left":79.6953125,"width":146,"hidden":1},"filtroObrigatorio":{"top":7,"left":25.5703125,"width":154,"hidden":0},"historicoFiltros":{"top":30,"left":25.5703125,"width":154,"hidden":0},"importacaoDados":{"top":53,"left":22.4296875,"width":80,"hidden":1},"scrollLoading":{"top":76,"left":27.765625,"width":80,"hidden":1}}'); goto a0pxN; zOENl: $form_tabela = 'D000'; goto UkrE5; m7ef_: $scrollLoading = $arrayConfig['scrollLoading']; goto jKIbR; O2U6t: if (!empty($arrayConfig)) { goto NOJrE; } goto ZT5Fv; tfsIh: $form_editor = array('geral' => '{"limiteRegistros":{"top":6.98529052734375,"left":79.67218017578125,"width":146.039216,"hidden":0},"filtroObrigatorio":{"top":29.993896484375,"left":89.56802368164062,"width":154,"hidden":0},"importacaoDados":{"top":52.987152099609375,"left":22.45709228515625,"width":80,"hidden":0},"scrollLoading":{"top":75.9957275390625,"left":27.772674560546875,"width":80,"hidden":0},"historicoFiltros":{"top":98.98898315429688,"left":38.602935791015625,"width":79,"hidden":0},"desabilitaOpcaoImprimir":{"top":121.99755859375,"left":67.58578491210938,"width":79,"hidden":0},"desabilitaOpcaoEmail":{"top":144.99081420898438,"left":92.20281982421875,"width":79,"hidden":0},"desabilitaOpcaoPDF":{"top":167.9993896484375,"left":99.20343017578125,"width":79,"hidden":0},"desablitaOpcaoCSV":{"top":190.99264526367188,"left":98.74386596679688,"width":79,"hidden":0}}'); goto Odi3C; pdyrN: $arrayConfig = ''; goto Hk3mg; fZmSW: $limiteRegistros = $arrayConfig['limiteRegistros']; goto OPsI1; G0ZsJ: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridConfiguracoesBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridConfiguracoesBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridConfiguracoesBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridConfiguracoesBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayTotais');
\t\t\t} </script></div>
EOT;
goto c5kmp; qsdGU: KbSbj: goto gZdFv; Wo2pF: $desablitaOpcaoCSV = $arrayConfig['desablitaOpcaoCSV']; goto D4GHb; PuygZ: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto jZQ8e; R20vP: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto G46cS; } goto t1vL9; MLapv: cxsAw: goto Z9K5v; IkP3p: $recover = !empty($r_recover) ? 1 : 0; goto rPY7A; FHYTU: $desabilitaOpcaoImprimir = $arrayConfig['desabilitaOpcaoImprimir']; goto IvCgI; dnbcy: $g['smarty']->assign('botoesDir', $botoesDir); goto OEmyz; t1vL9: if ($formCodigo == 1) { goto EJ0sz; } goto SWLdI; NqOOY: echo gGeraAlertMsgErroForm($form); goto RTWw_; VLzGv: $formArray = array('campos' => array('geral' => array('limiteRegistros' => array('name' => 'limiteRegistros', 'label' => 'Limite de registros', 'type' => 'text', 'uppercase' => false, 'value' => @$limiteRegistros), 'filtroObrigatorio' => array('name' => 'filtroObrigatorio', 'label' => 'Filtro obrigatorio', 'type' => 'select', 'select' => array(array('title' => '...', 'value' => ''), array('title' => 'Sim', 'value' => 'true', 'selected' => isset($filtroObrigatorio) && $filtroObrigatorio == 'true' ? true : false), array('title' => 'Não', 'value' => 'false', 'selected' => isset($filtroObrigatorio) && $filtroObrigatorio == 'false' ? true : false))), 'importacaoDados' => array('name' => 'importacaoDados', 'label' => 'Habilitar importação de dados', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => ''), array('title' => 'Sim', 'value' => 'true', 'selected' => isset($importacaoDados) && $importacaoDados == 'true' ? true : false))), 'scrollLoading' => array('name' => 'scrollLoading', 'label' => 'Habilitar loading com o scroll', 'type' => 'select', 'select' => array(array('title' => 'Sim', 'value' => 'true', 'selected' => isset($scrollLoading) && $scrollLoading == 'true' ? true : false), array('title' => 'Não', 'value' => 'false', 'selected' => isset($scrollLoading) && $scrollLoading == 'false' ? true : false))), 'historicoFiltros' => array('name' => 'historicoFiltros', 'label' => 'Habilitar histórico de filtros', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => 'false', 'selected' => !isset($historicoFiltros) || $historicoFiltros != 'true'), array('title' => 'Sim', 'value' => 'true', 'selected' => isset($historicoFiltros) && $historicoFiltros == 'true'))), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden'), 'desabilitaOpcaoImprimir' => array('name' => 'desabilitaOpcaoImprimir', 'label' => 'Desabilita Impressão', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => ''), array('title' => 'Sim', 'value' => 'true', 'selected' => isset($desabilitaOpcaoImprimir) && $desabilitaOpcaoImprimir == 'true' ? true : false))), 'desabilitaOpcaoEmail' => array('name' => 'desabilitaOpcaoEmail', 'label' => 'Desabilita Email', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => ''), array('title' => 'Sim', 'value' => 'true', 'selected' => isset($desabilitaOpcaoEmail) && $desabilitaOpcaoEmail == 'true' ? true : false))), 'desabilitaOpcaoPDF' => array('name' => 'desabilitaOpcaoPDF', 'label' => 'Desabilita PDF', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => ''), array('title' => 'Sim', 'value' => 'true', 'selected' => isset($desabilitaOpcaoPDF) && $desabilitaOpcaoPDF == 'true' ? true : false))), 'desablitaOpcaoCSV' => array('name' => 'desablitaOpcaoCSV', 'label' => 'Desabilita CSV', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => ''), array('title' => 'Sim', 'value' => 'true', 'selected' => isset($desablitaOpcaoCSV) && $desablitaOpcaoCSV == 'true' ? true : false)))))); goto NpLrW; T0PAc: GEXSg: goto O2U6t; Odi3C: if (!(strpos($r_grid, 'sistema/personalizacao/bi/') !== false)) { goto GOCEi; } goto VhR7e; G8FCX: $importacaoDados = $arrayConfig['importacaoDados']; goto m7ef_; zlfr2: case 'sistemaPGridOrdem': goto lshBv; O6L1m: $formId = uniqid(); goto hFA7R; chIWI: $recover = !empty($r_recover) ? 1 : 0; goto bH4k3; GW3lb: $arrayOrdem = ''; goto br4lF; ljWVi: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto s75JW; } goto bSCNu; uNk11: MCZeY: goto xdvKg; bH4k3: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#sistemaPGridOrdemBtnModelo').unbind('click').bind('click', function() {
\t\t\t\t\tabrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Salvar Modelo', '/sistema/personalizacaoGrid/salvarModelo/content/','', [430,130]);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridOrdemBtnUser').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\$('#{$g['divId']} .itemForm-Usuario_C007_Id').val('{$g['C007']['C007_Id']}');
\t\t\t\t\t\t\t\$('#sistemaPGridOrdemBtn').trigger('click');
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); } 
\t\t\t\t\t};
\t\t\t\t\tdialogConfirm('Confirmação', 'A personalização será feita apenas para este usuário, deseja continuar?', buttons);
\t\t\t\t});
\t\t\t\t\$('#sistemaPGridOrdemBtnRemover').unbind('click').bind('click', function() {
\t\t\t\t\tvar buttons = {
\t\t\t\t\t\t"Deletar": function() {
  \t\t\t\t\t\t\t\$.getJSON('/sistema/personalizacaoGrid/form_func-ajax/excluir/?ajax=true&arquivo=' + encodeURIComponent('{$arquivo}') + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tdialogConfirm('Ok', "Excluído!");
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t},
\t\t\t\t\t\t"Cancelar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t}
\t\t\t\t\tdialogConfirm('Deseja Remover?', "ATENCAO! Excluir esta personalização?", buttons);
\t\t\t\t});
\t\t\t\tfunction pegarOrdem(opcao) {
\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t// no apache ele vem o caminho relativo
\t\t\t\t\tvar url = '{$r_grid}';
\t\t\t\t\tvar matches = url.match(/^\\/.*\$/);
\t\t\t\t\tif (!matches) {
\t\t\t\t\t\turl = '/' + url;
\t\t\t\t\t}
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: url,
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&pegarOrdem=true&gridFiltrado=true' + \$('#{$g['divIdParent']}').data('vars'),
\t\t\t\t\t\ttype: 'post',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t// mensagem de erro (somente notificar)
\t\t\t\t\t\t\tif (matches) {
\t\t\t\t\t\t\t\t\$('<div>').html(matches[1]).appendTo(\$('#{$g['divId']}'));
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tvar matches = data.match(/<pegarOrdem>([\\s\\S]*)<\\/pegarOrdem>/m);
\t\t\t\t\t\t\tif (!matches) {
\t\t\t\t\t\t\t\t// mensagens de erro
\t\t\t\t\t\t\t\tif ('{$recover}' == 0) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayOrdem').html(data);
\t\t\t\t\t\t\t\t\tfecharJanela('{$g['divId']}');
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tif (opcao == 'show') {
\t\t\t\t\t\t\t\t\tdialogConfirm('Personalização do Grid Original', '<textarea style="width: 500px; height: 400px;">' + matches[1] + '</textarea>');
\t\t\t\t\t\t\t\t} else if ('{$carregarOriginal}' == 1) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']} #arrayOrdem').html(matches[1]);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tcarregarEditor('{$g['divId']}', 'itemForm-arrayOrdem', '');
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t}
\t\t\t\tpegarOrdem();
\t\t\t} </script></div>
EOT;
goto BpZb6; qPC4f: $g['smarty']->display('form.tpl'); goto M5EJI; Ai_xw: $botoesDir .= '<button type="submit" id="sistemaPGridOrdemBtn" class="btnSalvar" ' . $hidden . '>Salvar</button>'; goto Oxx3d; bSCNu: if ($formCodigo == 1) { goto Cq7ph; } goto EHQ5l; D3S1Q: echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); goto CteQf; g0RsN: $carregarOriginal = 0; goto chIWI; ekoH8: if (isset($r_sistemaPGridOrdemForm)) { goto SjK2v; } goto GiStQ; EHQ5l: if (!($formCodigo == 2)) { goto UmnL3; } goto hcjVF; bfSAu: if ($formMultiTab) { goto mpBf7; } goto O6L1m; YJEHe: $botoesEsq .= '<button type="submit" onclick="pegarOrdem(\'show\'); return false;">Mostrar Original</button>'; goto jvyut; G9ETq: $g['smarty']->display('buttons-floatBottom.tpl'); goto g0RsN; QxKrD: $enviar_acaoHref = '/sistema/personalizacaoGrid/form/sistemaPGridOrdem/'; goto bd9tV; M1Q3p: Cq7ph: goto UbWo3; jvyut: if (!$arrayOrdem) { goto MCZeY; } goto RMpRi; rOGTg: bWVKx: goto wF4hP; WSfO6: $g['smarty']->assign('botoesMid', $botoesMid); goto u5fnr; RMpRi: $botoesEsq .= '<button type="submit" id="sistemaPGridOrdemBtnRemover">Remover</button>'; goto uNk11; Oxx3d: goto PCNrz; goto KahyU; CteQf: UmnL3: goto z0_vO; Bj69b: $enviar_btnId = 'sistemaPGridOrdemBtn'; goto QxKrD; q_mnM: $hidden = $alterandoUsuario ? 'style="display: none;"' : ''; goto Ai_xw; vixma: Us1L3: goto qPC4f; xdvKg: $g['smarty']->assign('botoesId', $g['divId']); goto rzv9C; j4C_V: $form_tabela = 'D000'; goto fXkSI; wF4hP: $form = new Formulario('sistemaPGridOrdemForm'); goto JTv30; Q2cau: QeVGc: goto j4C_V; KahyU: iIBOC: goto u8Qyh; M5EJI: $botoesMid = $botoesEsq = $botoesDir = ''; goto T5fDb; lshBv: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto VS3pf; rWlli: ZmXXW: goto FxDyD; Z_BPQ: $g['smarty']->assign('table', $gerado['table']); goto qvhVC; JumqW: goto bWVKx; goto rWlli; pqp6K: CV_Ed: goto gk0X9; u5fnr: $g['smarty']->assign('botoesDir', $botoesDir); goto G9ETq; qvhVC: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto dhxGd; nqDLZ: $hidden = !empty($r_sistemaPGridOrdemForm['Modelo_Nome']) ? 'style="display: none;"' : ''; goto q_mnM; FxDyD: $arrayOrdem = file_get_contents($arquivo); goto rOGTg; opkJu: $g["smarty"]->assign("formId", $formId); goto vixma; SIgjE: $form->definirFormularioArray($formArray); goto ljWVi; gk0X9: s75JW: goto YwpBI; jH801: mpBf7: goto QoiKC; hFA7R: goto QeVGc; goto jH801; fXkSI: $form_campo_pk = 'D000_Id'; goto Bj69b; z0_vO: goto CV_Ed; goto M1Q3p; KHFsU: $sulfixoUsuario = $g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp'] ? '' : "-{$g['usuarioAtual']}"; goto mkvWn; kmOTn: if ($formMultiTab) { goto Us1L3; } goto opkJu; BpZb6: goto UDEWx; goto f_DBf; bd9tV: $r_grid = urldecode($r_grid); goto KHFsU; hcjVF: file_put_contents($arquivo, $form->campoValorEnviado('array')); goto MlFbA; cbiZI: $formMultiTab = false; goto bfSAu; cUGMA: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto YJEHe; br4lF: if (file_exists($arquivo)) { goto ZmXXW; } goto ekoH8; er1OS: $g['smarty']->assign('formularioID', $form->formularioID()); goto Z_BPQ; GiStQ: SjK2v: goto JumqW; KOw1n: $arquivo = !empty($r_pesquisaGridId) && substr($r_pesquisaGridId, 0, 4) == 'D140' ? $dir . $pre . $r_pesquisaGridId . '-ordem' . $sulfixoUsuario : $arquivo; goto GW3lb; MlFbA: $arrayOrdem = $form->campoValorEnviado('arrayOrdem'); goto D3S1Q; dhxGd: echo $gerado['pagina']; goto kmOTn; VS3pf: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto cbiZI; QoiKC: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto Q2cau; u8Qyh: $botoesDir .= '<button type="submit" id="sistemaPGridOrdemBtn" class="btnSalvar">Salvar Modelo: ' . $r_modoModelo . '</button>'; goto NEGIY; NEGIY: PCNrz: goto cUGMA; JTv30: $formArray = array('campos' => array('geral' => array('arrayOrdem' => array('name' => 'arrayOrdem', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'plaintext', 'value' => $arrayOrdem), 'Usuario_C007_Id' => array('name' => 'Usuario_C007_Id', 'label' => '', 'type' => 'hidden'), 'Modelo_Nome' => array('name' => 'Modelo_Nome', 'label' => '', 'type' => 'hidden')))); goto SIgjE; T5fDb: if ($r_modoModelo) { goto iIBOC; } goto nqDLZ; rzv9C: $g['smarty']->assign('botoesEsq', $botoesEsq); goto WSfO6; YwpBI: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto er1OS; UbWo3: echo gGeraAlertMsgErroForm($form); goto pqp6K; mkvWn: $arquivo = !empty($r_pesquisaGridId) ? $dir . $pre . $r_pesquisaGridId . '_' . md5($r_grid) . '-ordem-' . $g['usuarioAtual'] : $dir . $pre . md5($r_grid) . '-ordem' . $sulfixoUsuario; goto KOw1n; f_DBf: } ScXIl: UDEWx: