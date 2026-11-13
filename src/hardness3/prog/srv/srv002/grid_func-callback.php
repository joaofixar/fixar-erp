<?php
namespace hardness;
function geraImagem($array){
	global $g;
	return "<a href=\"#\" onclick=\"abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processos', '/srv/srv002/tab/srv002tab03/', '&T147_Id={$array['todosCampos']['T147_Id']}', [600,300]);\">
				<li title=\".ui-icon-zoomin\" class=\"ui-state-default ui-corner-all\">
					<span class=\"ui-icon ui-icon-zoomin\"></span>
				</li>
			</a>";
}

function validaPausado($array){
	global $g;
	
	if($array['todosCampos']['T147_Pausado'] == 'S'){
		return "<font color='red'><b>Sim</b></font>";
	}else{
		return "Não";
	}
}

function validaCampoTempoEstimadoRestante($array){
	//return $array['todosCampos']['T147_Tempo_Estimado'];
	
	$separar = explode(':',$array['todosCampos']['T147_Tempo_Estimado']);
	$valor = $array['todosCampos']['Total'];
	
	$horas = $separar[0]*3600;
	$minutos = $separar[1]*60;
	$segundos = $separar[2];
	$seconds = ($horas + $minutos + $segundos);
	$total = $seconds * $valor; 
	$horaSomada = sprintf('%02d',floor($total/3600)).':'. sprintf('%02d',floor(($total%3600)/60));
	
	// **** Para exibir os segundos basta adicionar a linha abaixo antes do ;
	//.':'. sprintf('%02d',($total%3600)%60)
	
	return $horaSomada;
	
}
function validaCampoTempoAlocado($array){
	global $g;
	$sql = "select * from T147 WHERE T147_D109_Id='{$array['todosCampos']['D109_Id']}' AND (T147_Status='P' OR T147_Status='E')";
	$result = mysql_query($sql);
	$total = 0;
	while($linha = mysql_fetch_array($result)){
		$separar = explode(':', $linha['T147_Tempo_Estimado']);
		$horas = $separar[0]*3600;
		$minutos = $separar[1]*60;
		$segundos = $separar[2];
		$seconds = ($horas + $minutos + $segundos);
		$total = $total + $seconds;
	}
	return $horaSomada = sprintf('%02d',floor($total/3600)).':'. sprintf('%02d',floor(($total%3600)/60));
}
function validaCampoEstimativaLiberacao($array){
	global $g;
	$tempoAlocado = validaCampoTempoAlocado($array);
	$separar = explode(':', $tempoAlocado);
	$horas = $separar[0]*60;
	$minutos = $separar[1];
	$minutosAlocado = ($horas + $minutos);
	$data = strtotime("+".$minutosAlocado."minutes", strtotime(date("H:i")));
	return date("H:i", $data);
}
function validaCampoCargaHorariaDisponivel($array){
	$D110 = $array['todosCampos']['D110_Id'];
	
	$sql = "SELECT * FROM D115 left join D109 on D109_Id=D115_D109_Id WHERE D115_D110_Id='{$D110}' AND D109_Flag_Ativo='S'";
	$result = mysql_query($sql);
	$total = 0;
	
	while($linha = mysql_fetch_assoc($result)){
		if (empty($linha['D109_Carga_Horaria']))
		{
			$linha['D109_Carga_Horaria']='00:00:00';
		}
		$separar = explode(':', $linha['D109_Carga_Horaria']);
		$horas = $separar[0]*3600;
		$minutos = $separar[1]*60;
		$segundos = $separar[2];
		$seconds = ($horas + $minutos + $segundos);
		$total = ($total + $seconds); 
		$horaSomada = sprintf('%02d',floor($total/3600)).':'. sprintf('%02d',floor(($total%3600)/60));
		// **** Para exibir os segundos basta adicionar a linha abaixo antes do ;
		//.':'. sprintf('%02d',($total%3600)%60)
	}
	return $horaSomada;
}

function validaCampoCargaPrevisaoConclusao($array){
	$cargaDisponivel = validaCampoCargaHorariaDisponivel($array);
	$tempoRestante = validaCampoTempoEstimadoRestante($array);
	
	return "Carga Disponível: " . $cargaDisponivel ."<br />Tempo Restante: ". $tempoRestante;
	
}

function validaCampoPrevisao($array){
	
	//return date("d/m/Y H:i:s", strtotime("+400 seconds"));
	
	$TempoEstimado = $array['todosCampos']['T147_Tempo_Estimado'];
	$HoraInicio = strtotime($array['todosCampos']['T147_Hora_Inicio']);
	if($HoraInicio == ""){
		return "-";
	}else{
		$separar = explode(':', $TempoEstimado);
		$horas = $separar[0]*3600;
		$minutos = $separar[1]*60;
		$segundos = $separar[2];
		$seconds = ($horas + $minutos + $segundos);
		return $result = date("d/m/Y H:i", strtotime("+".$seconds." seconds", $HoraInicio));
	}
	
}

function validaCampoHoraInicio($array){
	global $g;
	if($array['todosCampos']['T147_Hora_Inicio'] == "0000-00-00 00:00:00"){
		return "-";
	}else{
		return gCorrigeTempo($array['todosCampos']['T147_Hora_Inicio']);
	}
}

function validaCampoStatus($array){
	global $g;
	
	$D109_Id = $array['todosCampos']['D109_Id'];
	
	$sql = "SELECT * FROM T147 WHERE T147_D109_Id='{$D109_Id}' AND T147_Status='E' AND (T147_Pausado<>'S' OR T147_Pausado IS NULL)";
	$result = mysql_query($sql);
	$retorno = "";
	if (mysql_num_rows($result)>0){
		$retorno .= <<<EOT
			<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Serviços Atribuídos', '/srv/srv002/content/srv002contentPendentes/','&acaoId={$array['todosCampos']['D109_Id']}', [950,553]);">
EOT;
		while ($linha = mysql_fetch_assoc($result)){
			$sql = "SELECT * FROM D116 WHERE D116_Id='{$linha['T147_D116_Id']}'";
			$result2 = mysql_query($sql);
			$linha2 = mysql_fetch_assoc($result2);	
			
			$retorno .= <<<EOT
				Executando <b>{$linha['T147_Titulo']}</b> em <b>{$linha2['D116_Modelo']} - {$linha2['D116_Cor']} - {$linha2['D116_Placa']}</b><br />
EOT;
		}
		$retorno .= <<<EOT
		</span>
EOT;
	}else{
		$retorno = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Serviços Atribuídos', '/srv/srv002/content/srv002contentPendentes/','&acaoId={$array['todosCampos']['D109_Id']}', [950,553]);">
			<font color="green"><b>Livre</b></font>
		</span>
EOT;
	}
	return $retorno;
	 

}

function validaCampoStatusVeiculos($array){
	if($array['todosCampos']['T147_Pausado']=="S"){
		return "<font color='red'><b>Pausado</b></font>";
	}else{
	if($array['todosCampos']['T147_Status']=='P'){
		return "<font color='#CD950C'>Pendente</font>";
	}else if($array['todosCampos']['T147_Status'] == "C"){
		return "<font color='blue'>Concluído</font>";
	}elseif($array['todosCampos']['T147_Status'] == "E"){
		return "<font color='green'>Execução</font>";
	}elseif($array['todosCampos']['T147_Status'] == "N"){
		return "<b>Cancelado</b>";
	}
}
}

function validaCampoVeiculo($array){
	return $array['todosCampos']['D116_Placa']." - ".$array['todosCampos']['D116_Modelo'].' - '.$array['todosCampos']['D116_Cor'];
}

function validaCampoPrevisaoFimVeiculo($array){
	
	//return $array['todosCampos']['T147_Hora_Inicio'];
	
	if($array['todosCampos']['T147_Hora_Inicio'] == "0000-00-00 00:00:00"){
		return "";
	}else{
		$TempoEstimado = $array['todosCampos']['T147_Tempo_Estimado'];
		$HoraInicio = strtotime($array['todosCampos']['T147_Hora_Inicio']);
		
		$separar = explode(':', $TempoEstimado);
		$horas = $separar[0]*3600;
		$minutos = $separar[1]*60;
		$segundos = $separar[2];
		$seconds = ($horas + $minutos + $segundos);
		return $result = date("d/m/Y H:i", strtotime("+".$seconds." seconds", $HoraInicio));
	}
}

function funcionarioPorCompetencia($array) {

	$geraSelect = array('D109', 'D109_Id', 'D109_Nome', 'left join D115 on D115_D109_Id=D109_Id
														 WHERE D115_D110_Id='.$array['todosCampos']['T147_D110_Id']
														 , true);
	$geraSelect = base64_encode(serialize($geraSelect));
	return <<<EOT
	<div class="campoEditavel" onclick="gEditarCampo('T147_D109_Id', \$(this), '', '', '{$geraSelect}'); return false;">
		<span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;"></span><span style="padding-right: 16px;">{$array['todosCampos']['T147_D109_Id']}</span>
	</div>
EOT;

	// $sql = <<<EOT
		// select D109_Nome from D109 
		// left join D115 on D115_D109_Id
		// WHERE D116_D110_Id='{$array['todosCampos']['T147_D110_Id']}'
// EOT;
// 	
	// $result = mysql_query($sql);

}

function validaCampoCompetencias($array){
	global $g;
	
	$sql = "SELECT * FROM D115 left join D110 on D110_Id=D115_D110_Id WHERE D115_D109_Id='{$array['todosCampos']['D109_Id']}'";
	$result = mysql_query($sql);
	$retorno = "";
	
	while($linha = mysql_fetch_array($result)){
		//$retorno .= substr($linha['D110_Titulo'], 0, 7).", ";
		$retorno .= $linha['D110_Titulo']. " - ";
	}
	return $retorno;
}

function validaCampoPendentes($array){
	global $g;
	
	$sql = "SELECT * FROM T147 WHERE T147_D109_Id='{$array['todosCampos']['D109_Id']}' AND T147_Status='P'";
	$result = mysql_query($sql);
	$servicos = mysql_num_rows($result);
	
	$retorno = <<<EOT
	<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Serviços Atribuídos', '/srv/srv002/content/srv002contentPendentes/','&acaoId={$array['todosCampos']['D109_Id']}', [950,553]);">
   			<span title="Serviços Pendentes" style='font-size:14px;'><b>{$servicos}</b></span>
	</span>
EOT;

return $retorno;
}
function validaCampoExecucao($array){
	global $g;
	
	$sql = "SELECT * FROM T147 WHERE T147_D109_Id='{$array['todosCampos']['D109_Id']}' AND T147_Status='E' AND (T147_Pausado IS NULL OR T147_Pausado='N')";
	$result = mysql_query($sql);
	$servicos = mysql_num_rows($result);
	
	$retorno = <<<EOT
	<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Serviços Atribuídos', '/srv/srv002/content/srv002contentExecucao/','&acaoId={$array['todosCampos']['D109_Id']}', [950,553]);">
   			<span title="Serviços em Execução" style='font-size:14px;'><b>{$servicos}</b></span>
	</span>
EOT;
	return $retorno;
}
function validaCampoPausados($array){
	global $g;
	
	$sql = "SELECT * FROM T147 WHERE T147_D109_Id='{$array['todosCampos']['D109_Id']}' AND T147_Status='E' AND T147_Pausado='S'";
	$result = mysql_query($sql);
	$servicos = mysql_num_rows($result);
	
	$retorno = <<<EOT
	<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Serviços Atribuídos', '/srv/srv002/content/srv002contentPausados/','&acaoId={$array['todosCampos']['D109_Id']}', [950,553]);">
   			<span title="Serviços Pausados" style='font-size:14px;'><b>{$servicos}</b></span>
	</span>
EOT;

return $retorno;
	
}

function validaCampoPrioridade($array){
	if($array['todosCampos']['T147_Prioridade'] == '99999'){
		return "-";
	}else{
		return "<font color='red'><b>".$array['todosCampos']['T147_Prioridade']."<b/></font>";
	}
}

function validaCampoStatusSrv002($array){
	if($array['todosCampos']['T147_Pausado']=="S"){
		return "<font color='red'><b>PAUSADO</b></font>";
	}elseif($array['todosCampos']['T147_Status']=="E"){
		return "<b>EXECUÇÃO</b>";
	}else if($array['todosCampos']['T147_Status']=="P"){
		return "<b>PENDENTE</b>";
	}
}

function validaCampoVeiculoSrv002($array){
	return $array['todosCampos']['D116_Placa']." - ".substr($array['todosCampos']['D116_Modelo'], 0, 10)." - ".$array['todosCampos']['D116_Cor'];
}


function validaCampoPrioridade99999($array){
	if($array['todosCampos']['T147_Prioridade'] == "99999"){
		return "-";
	}else{
		return $array['todosCampos']['T147_Prioridade'];
	}
}

function mostrarNome($array){
	return $array['todosCampos']['D109_Nome'];
}

function validaCampoHoraCombinada($array){
	$sql = "select T171_Hora_combinada from T171 WHERE T171_T147_Id = '{$array['todosCampos']['T147_Id']}'";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	return $linha['T171_Hora_combinada'];
}

function abrirEdicaoFuncionario($array){
	global $g;
	$novoIdTelaForm = uniqid();
	return <<<EOT
	<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cadastro - ID: ' + id, '/cad/cad010/content/cad010content03/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D109_Id']}'), [900,600]);">{$array['todosCampos']['D109_Nome']}</span>
EOT;
}

function campoMarcarConferido($array){
	global $g;
	if ($array['todosCampos']['T147_Flag_Conferido']=='S')
		return '<span style="color:green;font-weight:bold">OK</span>';
		
	$imagem = <<<EOT
		<span onclick="srv002MarcarConferido('{$array['todosCampos']['T147_Id']}', '{$g['divId']}');"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-check" title="Aprovar Serviço"></span>
		   </div>
		</span>
		<span onclick="srv002MarcarReprovado('{$array['todosCampos']['T147_Id']}', '{$g['divId']}');"> 
		   <div class="ui-state-default ui-corner-all"  style="margin-left:40px;">
		   		<span class="ui-icon ui-icon-circle-minus" title="Reprovar Serviço"></span>
		   </class>
		</span>
EOT;
	return $imagem;
	
}
function validaCampoProduto($array){
	$sql = "SELECT D001_Descricao_Produto FROM T006 
	LEFT JOIN D009 ON D009_Id=T006_D009_Id 
	LEFT JOIN D049 ON D049_Id=D009_D049_Id 
	LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE T006_Id='{$array['todosCampos']['T147_T006_Id']}'";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	return $linha['D001_Descricao_Produto'];
}

?>
