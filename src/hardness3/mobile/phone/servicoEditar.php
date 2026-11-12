<?php
namespace hardness;
$script = '';

include('conf.php');
include('inc.conf.php');

// Vars
$T147_Id = (isset($_REQUEST['T147_Id'])) ? $_REQUEST['T147_Id'] : false;

	
// Adicionar Requisição de Compra
$temErro = '';
if (isset($_REQUEST['T147_Titulo'])) {
	$T147_Titulo = strtoupper(mysql_real_escape_string($_REQUEST['T147_Titulo']));
	$T147_Descricao = strtoupper(mysql_real_escape_string($_REQUEST['T147_Descricao']));
	$T147_Status = strtoupper(mysql_real_escape_string($_REQUEST['T147_Status']));
	$T147_Prioridade = strtoupper(mysql_real_escape_string($_REQUEST['T147_Prioridade']));
	$T147_Hora_Inicio = strtoupper(mysql_real_escape_string($_REQUEST['T147_Hora_Inicio']));
	$T147_Hora_Fim = strtoupper(mysql_real_escape_string($_REQUEST['T147_Hora_Fim']));
	$T147_Observacao = strtoupper(mysql_real_escape_string($_REQUEST['T147_Observacao']));
	$T147_Tempo_Estimado = strtoupper(mysql_real_escape_string($_REQUEST['T147_Tempo_Estimado']));
	$T147_D109_Id = strtoupper(mysql_real_escape_string($_REQUEST['T147_D109_Id']));
	$C007_Id = $C007_Id = $g['usuarioAtual'];
	if (!empty($_REQUEST['T147_Id'])) {
		$sql = <<<EOT
			UPDATE T147 SET
				T147_Titulo = '{$T147_Titulo}',
				T147_Descricao = '{$T147_Descricao}',
				T147_Status = '{$T147_Status}',
				T147_Prioridade = '{$T147_Prioridade}',
				T147_Hora_Inicio = '0000-00-00 {$T147_Hora_Inicio}:00',
				T147_Hora_Fim = '0000-00-00 {$T147_Hora_Fim}:00',
				T147_Observacao = '{$T147_Observacao}',
				T147_Tempo_Estimado = '{$T147_Tempo_Estimado}',
				T147_D109_Id = '{$T147_D109_Id}'
			WHERE
				T147_Id = '{$T147_Id}';
EOT;
		mysql_query($sql);
		$script .="alert('Serviço alterado com sucesso!');";
		$script .= "\$.mobile.changePage( './servicos.php?refresh={$uniqId}', { transition: 'fade'} );";
	} /*insert
	else {
		$sql = <<<EOT
			INSERT INTO T147 (T147_Titulo,
								T147_Descricao,
								T147_Tempo_Estimado,
								T147_D109_Id
								) 
								VALUES 
								('{$T147_Titulo}', 
								'{$T147_Descricao}', 
								'{$T147_Tempo_Estimado}',
								'{$T147_D109_Id}'
								);
EOT;
		mysql_query($sql);
		$script .="alert('Requisição criada com sucesso!');";
		$script .= "\$.mobile.changePage( './requisicoes.php?refresh={$uniqId}', { transition: 'fade'} );";
	}*/
}

// Incluir ou Alterar?
$btns = <<<EOT
	<div class="ui-grid-a">
        <div class="ui-block-a">
            <a data-role="button" data-transition="fade" href="./requisicoes.php" data-ajax="false">
                Voltar
            </a>
        </div>
        <div class="ui-block-b">
            <input value="Adicionar" type="submit" />
        </div>
    </div>
EOT;
if ($T147_Id) {
	$sql =  <<<EOT
			 SELECT * FROM T147
				left join T005 on T005_Id=T147_T005_Id
				left join D024 on D024_Id=T005_D024_Id
				left join D116 on D116_Id=T005_D116_Id
				left join T006 on T006_Id=T147_T006_Id
				LEFT JOIN D009 ON D009_Id=T006_D009_Id 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
			 WHERE T147_Id = '{$T147_Id}';
			 
EOT;
	$rowUpdate = mysql_fetch_assoc(mysql_query($sql));
	//Formata Campo DateTime Para Horas:Minutos
	//Hora Inicio
	$T147_Hora_Inicio = explode(' ', $rowUpdate['T147_Hora_Inicio']);
	$T147_Hora_Inicio = $T147_Hora_Inicio[1];
	$T147_Hora_Inicio = gCorrigeTempo($T147_Hora_Inicio);
	
	//Hora Fim
	$T147_Hora_Fim = explode(' ', $rowUpdate['T147_Hora_Fim']);
	$T147_Hora_Fim = $T147_Hora_Fim[1];
	$T147_Hora_Fim = gCorrigeTempo($T147_Hora_Fim);
	
	//Tempo Estimado
	$T147_Tempo_Estimado = gCorrigeTempo($rowUpdate['T147_Tempo_Estimado']);
	
	//Define combobox D109_Id(Funcionario)
	$sql = "SELECT * FROM D109 WHERE D109_Flag_Ativo = 'S' ORDER BY D109_Id ";
	$sqlFuncionario = mysql_query($sql);
	$selectFuncionario = "";
	if(empty($rowUpdate['T147_D109_Id'])){
			$selectFuncionario .= "<option selected=\"selected\" value=\"\">...</option>";
	}
	while($rowFuncionario = mysql_fetch_array($sqlFuncionario)){
		if($rowFuncionario['D109_Id'] == $rowUpdate['T147_D109_Id']){
			$selectFuncionario .= "<option selected=\"selected\" value=\"{$rowFuncionario['D109_Id']}\">
			{$rowFuncionario['D109_Nome']}
							    </option>";
		}else{
			$selectFuncionario .= "<option value=\"{$rowFuncionario['D109_Id']}\">
			{$rowFuncionario['D109_Nome']}
							    </option>";
		}
	}
	
	//Define combobox D147_Status(Status)
	$T147_Status .= '<option value="P"'.(($rowUpdate['T147_Status'] == 'P') ? ' selected="selected"' : '').'> PENDENTE </option>';
	$T147_Status .= '<option value="E"'.(($rowUpdate['T147_Status'] == 'E') ? ' selected="selected"' : '').'> EXECUÇÂO </option>';
	$T147_Status .= '<option value="C"'.(($rowUpdate['T147_Status'] == 'C') ? ' selected="selected"' : '').'> CONCLUIDO </option>';
	$T147_Status .= '<option value="S"'.(($rowUpdate['T147_Pausado'] == 'S') ? ' selected="selected"' : '').'> PAUSADO </option>';
	
	//Verifica Status do Servico
	/*	$T147_Status="";
		if($rowUpdate['T147_Status']=="P"){
			$T147_Status = "Pendente";
		}else if($rowUpdate['T147_Status']=="E"){
			$T147_Status = "Execução";
		}else if($rowUpdate['T147_Status']=="C"){
			$T147_Status = "Concluído";
		}
		
		if($rowUpdate['T147_Pausado']=="S"){
			$T147_Status = "Pausado";
		}
	*/
	$btns = <<<EOT
		<div class="ui-grid-b">
		    <div class="ui-block-a">
		        <a data-role="button" data-transition="fade" href="./servicos.php" data-ajax="false">
		            Voltar
		        </a>
		    </div>
		    <div class="ui-block-b">
		        <input value="Alterar" type="submit" />
		    </div>
		    <!--<div class="ui-block-c">
		        <a data-role="button" data-transition="slide" onclick="\$.mobile.changePage( './servicosRemover.php?T147_Id={$T14_Id}', { transition: 'slide'} );">
                    Remover
                </a>
		    </div>-->
		</div>
EOT;
}


include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamentoPreCadProd">
EOT;
include('inc.header.bar.php');

echo <<<EOT
        	<div data-role="content">
        		{$temErro}
                 <form action="./servicoEditar.php" method="post" data-ajax="false">
	             <div class="ui-grid-a">
                 	<div class="ui-block-a">
                 		<div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Titulo">
	                                Título
	                            </label>
	                            <input name="T147_Titulo" id="T147_Titulo" value="{$rowUpdate['T147_Titulo']}" placeholder="" type="text"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Descricao">
	                                Descrição
	                            </label>
	                            <textarea placeholder="" name="T147_Descricao" id="T147_Descricao" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$rowUpdate['T147_Descricao']}</textarea>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D001_Descricao_Produto">
	                                Produto
	                            </label>
	                            <input name="D001_Descricao_Produto" id="D001_Descricao_Produto" value="{$rowUpdate['D001_Descricao_Produto']}" placeholder="" type="text" disabled="true"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D116_Placa">
	                                Veículo
	                            </label>
	                            <input name="D116_Placa" id="D116_Placa" value="{$rowUpdate['D116_Placa']} - {$rowUpdate['D116_Modelo']} - {$rowUpdate['D116_Cor']}" placeholder="" type="text" disabled="true"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="D024_Nome_Fantasia">
	                                Cliente
	                            </label>
	                            <input name="D024_Nome_Fantasia" id="D024_Nome_Fantasia" value="{$rowUpdate['D024_Nome_Fantasia']}" placeholder="" type="text" disabled="true"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Hora_Inicio">
	                                Hora Início
	                            </label>
	                            <input name="T147_Hora_Inicio" id="T147_Hora_Inicio" value="{$T147_Hora_Inicio}" placeholder="" type="text"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Hora_Fim">
	                                Previsão Fim
	                            </label>
	                            <input name="T147_Hora_Fim" id="T147_Hora_Fim" value="{$T147_Hora_Fim}" placeholder="" type="text" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
		                        <label for="T147_Status">
		                            Status
		                        </label>
		                        <select data-native-menu="true" name="T147_Status" id="T147_Status">       
		                        	{$T147_Status}
			                    </select>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Prioridade">
	                                Prioridade
	                            </label>
	                            <input name="T147_Prioridade" id="T147_Prioridade" value="{$rowUpdate['T147_Prioridade']}" placeholder="" type="number" onkeypress="return(Verifica_Caracter(event, this));" />
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Observacao">
	                                Observação
	                            </label>
	                            <textarea placeholder="" name="T147_Observacao" id="T147_Observacao" class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset">{$rowUpdate['T147_Observacao']}</textarea>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
	                        <fieldset data-role="controlgroup">
	                            <label for="T147_Tempo_Estimado">
	                                Tempo Estimado
	                            </label>
	                            <input name="T147_Tempo_Estimado" id="T147_Tempo_Estimado" value="{$T147_Tempo_Estimado}" placeholder="" type="text"/>
	                        </fieldset>
	                    </div>
	                    <div data-role="fieldcontain">
		                        <label for="T147_D109_Id">
		                            Funcionário
		                        </label>
		                        <select data-native-menu="true" name="T147_D109_Id" id="T147_D109_Id">       
		                        	{$selectFuncionario}
			                    </select>
	                    </div>
	                </div>
                </div>
                {$btns}
                <input type="hidden" name="T147_Id" value="{$T147_Id}" />
                </form>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageOrcamentoPreCadProd", "pageinit");
    	$( document ).delegate("#pageOrcamentoPreCadProd", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
			{$script}
    	});
    </script>
EOT;
