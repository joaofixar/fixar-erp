<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$T145_Id = isset($_REQUEST['T145_Id']) ? $_REQUEST['T145_Id'] : false; 
$uniqId = uniqid();

// Copiado de ven001/form_func-ajax/atualizarCamposProdutos/
$T145_Id = mysql_real_escape_string($T145_Id);

$sql = <<<EOT
SELECT 
	* FROM T145 
   	WHERE T145_Id = '{$T145_Id}';
EOT;
$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);
$row['T145_Valor_Unitario'] = gCorrigeNumero($row['T145_Valor_Unitario']);
$teste = strtotime($row['T145_Tempo_Estimado']);
$teste = date("H:i", $teste);
$row['T145_Tempo_Estimado'] = $teste;

// Adicionar
$temErro = '';
if (isset($_REQUEST['T145_Titulo'])) {
	$_REQUEST['T145_Titulo'] = mysql_real_escape_string($_REQUEST['T145_Titulo']);
	$_REQUEST['T145_Descricao'] = mysql_real_escape_string($_REQUEST['T145_Descricao']);
	$_REQUEST['T145_Valor_Unitario'] = mysql_real_escape_string(gCorrigeNumeroInverte($_REQUEST['T145_Valor_Unitario']));
	$_REQUEST['T145_Tempo_Estimado'] = mysql_real_escape_string($_REQUEST['T145_Tempo_Estimado'] . ':00');
	$sql = <<<EOT
		UPDATE T145 SET 
			T145_Titulo = '{$_REQUEST['T145_Titulo']}', 
			T145_Descricao = '{$_REQUEST['T145_Descricao']}', 
			T145_Tempo_Estimado = '{$_REQUEST['T145_Tempo_Estimado']}', 
			T145_Valor_Unitario = '{$_REQUEST['T145_Valor_Unitario']}', 
			T145_Valor_Total = '{$_REQUEST['T145_Valor_Unitario']}'
		WHERE
			T145_Id = '{$T145_Id}';
EOT;
	mysql_query($sql);
	$script = <<<EOT
  		$.mobile.changePage( "./orcamento.php?T003_Id={$T003_Id}", { transition: 'slide'} );
EOT;
}

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageorcamentoDelServico">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">

				{$temErro}
				<form action="./orcamentoDelServico.php?T003_Id={$T003_Id}&T145_Id={$T145_Id}" method="post" data-ajax="false">
				<div class="ui-grid-a" style="width:100%; height:10%;">
	        			<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 5px; clear:both;">
	        				<li data-role="list-divider" role="heading" class="divider">
			    				<table style="width: 100%";>
			    					<tr>
			    						<td style="width: 5%; font-size: 16px;" align="center">Alterar serviço orçamento</td>
			    					</tr>
			    				</table>
		                    </li>
	                    </ul>
        			</div>
				<div class="ui-grid-b">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T145_Titulo">
	                                Título
	                            </label>
	                            <input name="T145_Titulo" id="T145_Titulo" placeholder="" value="{$row['T145_Titulo']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T145_Descricao">
	                                Descrição
	                            </label>
	                            <input name="T145_Descricao" id="T145_Descricao" placeholder="" value="{$row['T145_Descricao']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T145_Valor_Unitario">
	                                Valor Total
	                            </label>
	                            <input name="T145_Valor_Unitario" id="T145_Valor_Unitario" placeholder="" value="{$row['T145_Valor_Unitario']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T145_Tempo_Estimado">
	                                Tempo Estimado
	                            </label>
	                            <input name="T145_Tempo_Estimado" id="T145_Tempo_Estimado" placeholder="" value="{$row['T145_Tempo_Estimado']}" type="text" />
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-c">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './orcamento.php?T003_Id={$T003_Id}&refresh={$uniqId}', { transition: 'slide'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <input value="Editar" type="submit" />
                    </div>
                    <div class="ui-block-c">
                    	<a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './orcamentoServicoRemover.php?T003_Id={$T003_Id}&T145_Id={$T145_Id}', { transition: 'slide'} );">
                            Remover
                        </a>
                    </div>
                </div>
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageorcamentoDelServico", "pageinit");
    	$( document ).delegate("#pageorcamentoDelServico", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
