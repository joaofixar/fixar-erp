<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');
$uniqId = uniqid();
// Vars
$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;

// Copiado de ven001/form_func-ajax/atualizarCamposProdutos/
$D112_Id = mysql_real_escape_string($D112_Id);

$sql = <<<EOT
SELECT 
	D112_Titulo,
	D112_Descricao,
	D112_Tempo_Estimado,
	D112_Valor_Unitario,
	D112_Id,
	D112_D110_Id,
	D110_Titulo,
	D112_T212_Id
	   	from D112
   	left join D110 on D110_Id=D112_D110_Id
   	WHERE D112_Id = '{$D112_Id}'
EOT;
$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);
$T212_Id=$row['D112_T212_Id'];
//Verifica se o valor unitário é zero, para deixar o input em branco
$row['D112_Valor_Unitario'] = gCorrigeNumero($row['D112_Valor_Unitario']);
$row['D112_Valor_Unitario'] = ($row['D112_Valor_Unitario'] == "0,00" ? "" : $row['D112_Valor_Unitario']);
//Verifica se o tempo estimado é zero, para deixar o input em branco
$teste = strtotime($row['D112_Tempo_Estimado']);
$teste = date("H:i", $teste);
$teste = ($teste == "00:00" ? "" : $teste);
$row['D112_Tempo_Estimado'] = $teste;

// Adicionar
$temErro = '';
if (isset($_REQUEST['D112_Titulo'])) {
	$_REQUEST['D112_Titulo'] = strtoupper(mysql_real_escape_string($_REQUEST['D112_Titulo']));
	$_REQUEST['D112_Descricao'] = strtoupper(mysql_real_escape_string($_REQUEST['D112_Descricao']));
	$_REQUEST['D112_Valor_Unitario'] = strtoupper(mysql_real_escape_string(gCorrigeNumeroInverte($_REQUEST['D112_Valor_Unitario'])));
	$_REQUEST['D112_Tempo_Estimado'] = strtoupper(mysql_real_escape_string($_REQUEST['D112_Tempo_Estimado'] . ':00'));
	$sql = <<<EOT
		INSERT INTO t145 
			(T145_T003_Id, T145_D112_Id, T145_Titulo, T145_Descricao, T145_Tempo_Estimado, T145_Quantidade, T145_Quantidade_Confirmada, T145_Valor_Unitario, T145_Valor_Total) 
		VALUES 
			('{$T003_Id}','{$D112_Id}','{$_REQUEST['D112_Titulo']}', '{$_REQUEST['D112_Descricao']}', '{$_REQUEST['D112_Tempo_Estimado']}', 1, 1, '{$_REQUEST['D112_Valor_Unitario']}', '{$_REQUEST['D112_Valor_Unitario']}');
EOT;
	mysql_query($sql);
	$script = <<<EOT
		alert('Serviço adicionado com sucesso!');
  		$.mobile.changePage( "./orcamentoProcurarServico.php?T003_Id={$T003_Id}&T212_Id={$T212_Id}", { transition: 'slide'} );
EOT;
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageorcamentoAddServico">
EOT;
echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./orcamentoAddServico.php?T003_Id={$T003_Id}&D112_Id={$D112_Id}" method="post" data-ajax="false">
                 <div class="ui-grid-ab" style="width:100%; height:10%;">
        			<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 5px; clear:both;">
        				<li data-role="list-divider" role="heading" class="divider">
		    				<table style="width: 100%";>
		    					<tr>
		    						<td style="width: 5%; font-size: 16px;" align="center">Incluir serviço orçamento</td>
		    					</tr>
		    				</table>
	                    </li>
                    </ul>
        		</div>
                 <div class="ui-grid-a">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="textinput3">
	                                Código
	                            </label>
	                            <input id="textinput3" placeholder="" value="{$row['D112_Id']}" type="text" style="opacity: 0.5;" disabled="disabled" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D112_Titulo">
	                                Título
	                            </label>
	                            <input name="D112_Titulo" id="D112_Titulo" placeholder="" value="{$row['D112_Titulo']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D112_Descricao">
	                                Descrição
	                            </label>
	                            <input name="D112_Descricao" id="D112_Descricao" placeholder="" value="{$row['D112_Descricao']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D112_Valor_Unitario">
	                                Valor Total
	                            </label>
	                            <input name="D112_Valor_Unitario" id="D112_Valor_Unitario" placeholder="" value="{$row['D112_Valor_Unitario']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D112_Tempo_Estimado">
	                                Tempo Estimado
	                            </label>
	                            <input name="D112_Tempo_Estimado" id="D112_Tempo_Estimado" placeholder="" value="{$row['D112_Tempo_Estimado']}" type="text" />
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-b">
                    <div class="ui-block-a">
                           <input value="Voltar" type="button" onclick="\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'fade'} );" data-ajax="false" />
                    </div>
                    <div class="ui-block-b">
                        <input value="Adicionar" type="submit" />
                    </div>
                	<div class="ui-block-c">
                           <input value="Buscar" type="button" onclick="\$.mobile.changePage( './orcamentoProcurarServico.php?T003_Id={$T003_Id}', { transition: 'fade'} );" data-ajax="false" />
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageorcamentoAddServico", "pageinit");
    	$( document ).delegate("#pageorcamentoAddServico", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
