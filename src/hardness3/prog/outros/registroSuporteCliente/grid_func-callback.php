<?php
namespace hardness;

function campoAbrirDocumento($array,$param){
	$id = $array['todosCampos']['T272_Id'];
	$mes = $array['todosCampos']['T272_Mes'];
	$ano = $array['todosCampos']['T272_Ano'];
	$nomeArquivo = $array['todosCampos']['T272_Nome_Arquivo'];
	//$urlRaiz = $param[0];
	$urlRaiz = 'http://23.23.29.24/hardness3/';
	if (!empty($nomeArquivo)){
		$cArquivo = $urlRaiz . $nomeArquivo;
		$retorno = "<a href='#' onclick=\"window.open('$cArquivo')\">Abrir</a>"; 
	} else {
        $retorno = '';
	}
	return $retorno;
}

function nivelPrioridade($array, $param){
	switch ($array['todosCampos']['D167_Nivel_Prioridade']) {
		case '1':
			return "<div style=\"background-color: #f45858; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">ALTO</div>";
			break;
		case '2':
			return "<div style=\"background-color: #f2e087; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">MÉDIO</div>";
			break;
		case '3':
			return "<div style=\"background-color: #97f4ed; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">BAIXO</div>";
			break;
	}
}

function moverListaNome($array, $param){
	$D167_Mover_Lista_Id = $array['todosCampos']['D167_Mover_Lista_Id'];

	$sqlD167 = mysql_query("SELECT D167_Nome_Lista from D167 where D167_Id={$D167_Mover_Lista_Id}");
	$D167 = mysql_fetch_array($sqlD167);

	return $D167['D167_Nome_Lista'];

}

function tabelaChamadosHardness($array, $param){
	global $g;
	$T272_Id = $array['todosCampos']['T272_Id'];
	$condicao = "";

	$htmlTable = <<<EOT
		<style>
			table a:link {
		    color: #666;
		    font-weight: bold;
		    text-decoration:none;
			}
			table#t1 a:visited {
			    color: #999999;
			    font-weight:bold;
			    text-decoration:none;
			}
			table#t1 a:active,
			table#t1 a:hover {
			    color: #bd5a35;
			    text-decoration:underline;
			}
			table#t1 {
			    width: 99%;
			    font-family:Arial, Helvetica, sans-serif;
			    color:#666;
			    font-size:12px;
			    background:#eaebec;
			    margin:5px 5px 5px 5px;
			    border:#ccc 1px solid;
			 
			    -moz-border-radius:3px;
			    -webkit-border-radius:3px;
			    border-radius:3px;
			}
			table#t1 th {
			    padding:10px 12px;
			    border-top:1px solid #fafafa;
			    border-bottom:1px solid #e0e0e0;
			 
			    background: #ededed;
			    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
			    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
			}
			table#t1 th:first-child{
			    text-align: left;
			    padding-left:10px;
			}
			table#t1 tr:first-child th:first-child{
			    -moz-border-radius-topleft:3px;
			    -webkit-border-top-left-radius:3px;
			    border-top-left-radius:3px;
			}
			table#t1 tr:first-child th:last-child{
			    -moz-border-radius-topright:3px;
			    -webkit-border-top-right-radius:3px;
			    border-top-right-radius:3px;
			}
			table#t1 tr{
			    text-align: center;
			    padding-left:10px;
			}
			table#t1 tr td:first-child{
			    text-align: left;
			    padding-left:10px;
			    border-left: 0;
			}
			table#t1 tr td {
			    padding:12px;
			    border-top: 1px solid #ffffff;
			    border-bottom:1px solid #e0e0e0;
			    border-left: 1px solid #e0e0e0;
			     
			    background: #fafafa;
			    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
			    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
			}
			table#t1 tr.even td{
			    background: #f6f6f6;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
			    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
			}
			table#t1 tr:last-child td{
			    border-bottom:0;
			}
			table#t1 tr:last-child td:first-child{
			    -moz-border-radius-bottomleft:3px;
			    -webkit-border-bottom-left-radius:3px;
			    border-bottom-left-radius:3px;
			}
			table#t1 tr:last-child td:last-child{
			    -moz-border-radius-bottomright:3px;
			    -webkit-border-bottom-right-radius:3px;
			    border-bottom-right-radius:3px;
			}
			table#t1 tr:hover td{
			    background: #f2f2f2;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
			    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);  
			}
		</style>        

		<table cellspacing='0' id="t1"> <!-- Não retirar essa linha --!>
			<!-- Cabeçalho -->  
		    <tr>
		        <th style="text-align:center;">Chamado</th>
		        <th>Horas</th>
		        <th>Data abertura</th>
		        <th>Cliente</th>
		        <th>Solicitante</th>
		        <th>Quadro</th>
		        <th>Lista</th>		        
		    </tr>
EOT;
		if(!empty($param)){
			$condicao = "AND (D024_Nome_Empresa like '%$param%' or D024_Nome_Fantasia like '%$param%')";
		}

		$sqlT234 = mysql_query("SELECT T234_Id,
									  T234_Horas_Desenvolvimento,
									  D024_Nome_Empresa,
									  T234_Solicitante,
									  D167_Nome_Lista,
									  D166_Grupo_Suporte,
									  T234_Data_Hora_Abertura 
								 FROM T272
							LEFT JOIN T234 ON T234_T272_Id = T272_Id
							Left join D167 on T234_D167_Id = D167_Id
							left join D166 on T234_D166_Id = D166_Id
							left join D024 on T234_D024_Id = D024_Id
							where T272_Id={$T272_Id} {$condicao}");

		while($T234 = mysql_fetch_array($sqlT234)){
			if($T234['T234_Id']>0){
				$dataAbertura = gcorrigeData($T234['T234_Data_Hora_Abertura']);
				$chamado = str_pad($T234['T234_Id'],8,"0",STR_PAD_LEFT);
				$cliente = substr($T234['D024_Nome_Empresa'], 0, 30);


				$htmlTable .= <<<EOT
				    <tr id='{$chamado}' style="cursor: pointer;">
						<td colunaTabela="Tabela" style="text-align:center;">{$chamado}</td>
						<td colunaTabela="Tabela">{$T234['T234_Horas_Desenvolvimento']}</td>
						<td colunaTabela="Tabela">{$dataAbertura}</td>
						<td colunaTabela="Tabela" style="text-align:left;">{$cliente}</td>
						<td colunaTabela="Tabela">{$T234['T234_Solicitante']}</td>
						<td colunaTabela="Tabela">{$T234['D166_Grupo_Suporte']}</td>
						<td colunaTabela="Tabela">{$T234['D167_Nome_Lista']}</td>
					</tr>
EOT;
			}
		}

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$htmlTable .= <<<EOT

				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#t1 tr').unbind('click').unbind('click').bind('click', function(data) {
						var id = $(this).attr('id');
						abrirJanela(false, divIdRootAberto(), '' , unique(), '', 'Editar Chamado', '/outros/registroSuporte/registroSuporte/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}&divIdRefresh={$g['divId']}', [1000, 600]);
					});						

				} </script></div>

			</table>

EOT;

return $htmlTable;


}

function tabelaChamadosCliente($array, $param){
	global $g;
	$T272_Id = $array['todosCampos']['T272_Id'];
	$D024_Id = $param;
	log("D024_Id=".$D024_Id);

	$htmlTable = <<<EOT
		<style>
			table a:link {
		    color: #666;
		    font-weight: bold;
		    text-decoration:none;
			}
			table#t1 a:visited {
			    color: #999999;
			    font-weight:bold;
			    text-decoration:none;
			}
			table#t1 a:active,
			table#t1 a:hover {
			    color: #bd5a35;
			    text-decoration:underline;
			}
			table#t1 {
			    width: 99%;
			    font-family:Arial, Helvetica, sans-serif;
			    color:#666;
			    font-size:12px;
			    background:#eaebec;
			    margin:5px 5px 5px 5px;
			    border:#ccc 1px solid;
			 
			    -moz-border-radius:3px;
			    -webkit-border-radius:3px;
			    border-radius:3px;
			}
			table#t1 th {
			    padding:5px 5px;
			    border-top:1px solid #fafafa;
			    border-bottom:1px solid #e0e0e0;
			 
			    background: #ededed;
			    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
			    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
			}
			table#t1 th:first-child{
			    text-align: left;
			    padding-left:10px;
			}
			table#t1 tr:first-child th:first-child{
			    -moz-border-radius-topleft:3px;
			    -webkit-border-top-left-radius:3px;
			    border-top-left-radius:3px;
			}
			table#t1 tr:first-child th:last-child{
			    -moz-border-radius-topright:3px;
			    -webkit-border-top-right-radius:3px;
			    border-top-right-radius:3px;
			}
			table#t1 tr{
			    text-align: center;
			    padding-left:10px;
			}
			table#t1 tr td:first-child{
			    text-align: left;
			    padding-left:10px;
			    border-left: 0;
			}
			table#t1 tr td {
			    padding:5px;
			    border-top: 1px solid #ffffff;
			    border-bottom:1px solid #e0e0e0;
			    border-left: 1px solid #e0e0e0;
			     
			    background: #fafafa;
			    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
			    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
			}
			table#t1 tr.even td{
			    background: #f6f6f6;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
			    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
			}
			table#t1 tr:last-child td{
			    border-bottom:0;
			}
			table#t1 tr:last-child td:first-child{
			    -moz-border-radius-bottomleft:3px;
			    -webkit-border-bottom-left-radius:3px;
			    border-bottom-left-radius:3px;
			}
			table#t1 tr:last-child td:last-child{
			    -moz-border-radius-bottomright:3px;
			    -webkit-border-bottom-right-radius:3px;
			    border-bottom-right-radius:3px;
			}
			table#t1 tr:hover td{
			    background: #f2f2f2;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
			    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);  
			}
		</style>        

		<table cellspacing='0' id="t1"> <!-- Não retirar essa linha --!>
			<!-- Cabeçalho -->  
		    <tr>
		        <th style="text-align:center;">Chamado</th>
		        <th>Horas</th>
		        <th>Data abertura</th>
		        <th>Solicitante</th>
		        <th>Quadro</th>
		        <th>Lista</th>		        
		    </tr>
EOT;

		$sqlT234 = mysql_query("SELECT T234_Id,
									  T234_Horas_Desenvolvimento,
									  D024_Nome_Empresa,
									  T234_Solicitante,
									  D167_Nome_Lista,
									  D166_Grupo_Suporte,
									  T234_Data_Hora_Abertura,
									  T234_C007_Id_Cliente 
								 FROM T272
							LEFT JOIN T234 ON T234_T272_Id = T272_Id  AND T234_D024_Id = '{$D024_Id}'
							Left join D167 on T234_D167_Id = D167_Id
							left join D166 on T234_D166_Id = D166_Id
							left join D024 on T234_D024_Id = D024_Id
							where T272_Id={$T272_Id}");

		while($T234 = mysql_fetch_array($sqlT234)){
			if($T234['T234_Id']>0){
				$dataAbertura = gcorrigeData($T234['T234_Data_Hora_Abertura']);
				$chamado = str_pad($T234['T234_Id'],8,"0",STR_PAD_LEFT);
				$cliente = substr($T234['D024_Nome_Empresa'], 0, 30);


				$htmlTable .= <<<EOT
				    <tr idCliente = '{$T234['T234_C007_Id_Cliente']}' id='{$chamado}'style="cursor: pointer;">
						<td colunaTabela="Tabela" style="text-align:center;">{$chamado}</td>
						<td colunaTabela="Tabela">{$T234['T234_Horas_Desenvolvimento']}</td>
						<td colunaTabela="Tabela">{$dataAbertura}</td>
						<td colunaTabela="Tabela">{$T234['T234_Solicitante']}</td>
						<td colunaTabela="Tabela">{$T234['D166_Grupo_Suporte']}</td>
						<td colunaTabela="Tabela">{$T234['D167_Nome_Lista']}</td>
					</tr>
EOT;
			}			
		}
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$htmlTable .= <<<EOT

				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#t1 tr').unbind('click').unbind('click').bind('click', function(data) {
						var id = $(this).attr('id');
						var idCliente = $(this).attr('idCliente');

						if(idCliente=={$g['usuarioAtual']} || {$g['nivelAlcada']}==4){
							abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Chamado', '/outros/registroSuporte/registroSuporte/contentInteracoes/', '&acaoId=' + encodeURIComponent(id), [1000, 600]);
						}
					});						

				} </script></div>

			</table>

EOT;
return $htmlTable;


}

?>