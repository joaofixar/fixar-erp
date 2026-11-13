<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$T147_Id = isset($_REQUEST['T147_Id']) ? $_REQUEST['T147_Id'] : false; 
$uniqId = uniqid();

// Copiado de ven001/form_func-ajax/atualizarCamposProdutos/
$T147_Id = mysql_real_escape_string($T147_Id);

$sql = <<<EOT
SELECT 
	* FROM T147 
   	WHERE T147_Id = '{$T147_Id}';
EOT;
$resultado = mysql_query($sql);
$row = mysql_fetch_assoc($resultado);
$row['T147_Valor_Unitario'] = gCorrigeNumero($row['T147_Valor_Unitario']);
$teste = strtotime($row['T147_Tempo_Estimado']);
$teste = date("H:i", $teste);
$row['T147_Tempo_Estimado'] = $teste;

// Adicionar
$temErro = '';
if (isset($_REQUEST['T147_Titulo'])) {
	$_REQUEST['T147_Titulo'] = mysql_real_escape_string($_REQUEST['T147_Titulo']);
	$_REQUEST['T147_Descricao'] = mysql_real_escape_string($_REQUEST['T147_Descricao']);
	$_REQUEST['T147_Valor_Unitario'] = mysql_real_escape_string(gCorrigeNumeroInverte($_REQUEST['T147_Valor_Unitario']));
	$_REQUEST['T147_Tempo_Estimado'] = mysql_real_escape_string($_REQUEST['T147_Tempo_Estimado'] . ':00');
	$sql = <<<EOT
		UPDATE T147 SET 
			T147_Titulo = '{$_REQUEST['T147_Titulo']}', 
			T147_Descricao = '{$_REQUEST['T147_Descricao']}', 
			T147_Tempo_Estimado = '{$_REQUEST['T147_Tempo_Estimado']}', 
			T147_Valor_Unitario = '{$_REQUEST['T147_Valor_Unitario']}', 
			T147_Valor_Total = '{$_REQUEST['T147_Valor_Unitario']}'
		WHERE
			T147_Id = '{$T147_Id}';
EOT;
	mysql_query($sql);
	$script = <<<EOT
  		$.mobile.changePage( "./pedido.php?T005_Id={$T005_Id}{$acao}", { transition: 'slide'} );
EOT;
}

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoDelServico">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
				{$temErro}
				<form action="./pedidoDelServico.php?T005_Id={$T005_Id}&T147_Id={$T147_Id}{$acao}" method="post" data-ajax="false">
				<div class="ui-block-ab" style="width:100%;">
        			<ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 5px; clear:both;">
        				<li data-role="list-divider" role="heading" class="divider">
		    				<table style="width: 100%";>
		    					<tr>
		    						<td style="width: 5%; font-size: 16px;" align="center">Alterar serviço pedido</td>
		    					</tr>
		    				</table>
	                    </li>
                    </ul>
        		</div>
				<div class="ui-grid-a">
                 	<div class="ui-block-a">
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Titulo">
	                                Título
	                            </label>
	                            <input name="T147_Titulo" id="T147_Titulo" placeholder="" value="{$row['T147_Titulo']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Descricao">
	                                Descrição
	                            </label>
	                            <input name="T147_Descricao" id="T147_Descricao" placeholder="" value="{$row['T147_Descricao']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Valor_Unitario">
	                                Valor Total
	                            </label>
	                            <input name="T147_Valor_Unitario" id="T147_Valor_Unitario" placeholder="" value="{$row['T147_Valor_Unitario']}" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Tempo_Estimado">
	                                Tempo Estimado
	                            </label>
	                            <input name="T147_Tempo_Estimado" id="T147_Tempo_Estimado" placeholder="" value="{$row['T147_Tempo_Estimado']}" type="text" />
	                        </fieldset>
	                    </div>
	                </div>
                </div>
                <div class="ui-grid-b">
                    <div class="ui-block-a">
                        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedido.php?T005_Id={$T005_Id}&refresh={$uniqId}{$acao}', { transition: 'slide'} );">
                            Voltar
                        </a>
                    </div>
                    <div class="ui-block-b">
                        <input value="Editar" type="submit" />
                    </div>
                    <div class="ui-block-c">
                    	<a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './pedidoServicoRemover.php?T005_Id={$T005_Id}&T147_Id={$T147_Id}{$acao}', { transition: 'slide'} );">
                            Cancelar
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
    	$( document ).undelegate("#pagepedidoDelServico", "pageinit");
    	$( document ).delegate("#pagepedidoDelServico", "pageinit", function() {
EOT;

echo <<<EOT
			{$script}
    	});
    </script>
EOT;
