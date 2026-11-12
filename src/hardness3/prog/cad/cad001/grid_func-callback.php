<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 */

function mostraImagemCadastro($array){
	global $g;
	$novoIdTelaForm = uniqid();
	if($array['todosCampos']['D024_Flag_Pre_Cadastro'] == 'N'){
		$imagem = <<<EOT
			<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Editar Cadastro', '/cad/cad001/content/cad001contentCadastro/','&acaoId={$array['todosCampos']['D024_Id']}', [950,553]);">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-person" title="Editar Cadastro"></span>
			   </div>
			</span>
EOT;
	}else{
		$imagem	= "";
	}
	return $imagem;
}

function campoeMailSenhaCliente($array){
		global $g;
		log("chamando o callback B2B");
		$D013_Senha = $array['todosCampos']['D013_Senha'];

		if(!empty($D013_Senha)){
			log("achou a senha ");

			return  "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&D013_Id=' + encodeURIComponent('{$array['todosCampos']['D013_Id']}') + '&senhaB2B=true', [900,550]);\"><img src='/hardness3/static/img/tmp/icone_enviar.gif' /></a>";
		}else{
			return "-";
		}
	}


function mostraImagemDoc($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Documentos', '/cad/cad001/content/cad001ContentAbreDocumentos/','&D024_Id={$array['todosCampos']['D024_Id']}', [900,550]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-document" title="Documentos"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}
function mostraImagemUltimoContato($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = gCorrigeData($array);
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Agenda', '/cad/cad001/content/cad001ContentAbreAgenda/','&D024_Id={$array['todosCampos']['D024_Id']}', [900,550]);" style='display: block; width: 100%; height: 15px'>
		  	<!--	
				<div class="icone ui-state-default ui-corner-all">
					<span class="ui-icon ui-icon-comment" title="Abrir Agenda"></span>
				</div>
			-->	
			{$retorno}
		</span>
EOT;
	
	return $imagem;
}
function campoAbrirDocumento($array){
	$id = $array['todosCampos']['D087_Id'];
	$retorno = "<a href=\"/cad/cad001/grid_func-ajax/abreDocumento/?ajax=true&acaoId={$id}\">Abrir</a>"; 
	return $retorno;
}

function abrirDocumento($array){
	$id = $array['todosCampos']['D127_Id'];
	$retorno = "<a href=\"/cad/cad001/grid_func-ajax/abreDocumentoVeiculo/?ajax=true&acaoId={$id}\">Abrir</a>";
	return $retorno;
}

function mostrarListaPreco($array){
	$lista = $array['todosCampos']['D024_Flag_Lista_Preco'];
	if($lista == 1){
		$return = 'PREÇO 1';
	} else if ($lista == 2){
		$return = 'PREÇO 2';
	}else if ($lista == 3){
		$return = 'PREÇO 3';
	}else if ($lista == 4){
		$return = 'PREÇO 4';
	}else if ($lista == 5){
		$return = 'PREÇO 5';
	}else{
		$return = '-';
	}
	return $return;
}

function checkboxMarcarCliente($array) {
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$D024_Flag_Mkt = $array['todosCampos']['D024_Flag_Mkt'];
	
	$retorno = "<input onclick=\"checkboxMarcarCliente(this, '{$D024_Id}', '{$g['divId']}')\" type='checkbox' id='D024_Flag_Mkt' title='Marcar Cliente'";
	if ($D024_Flag_Mkt == 'S'){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";	
}

function mostrarVendedorInterno($array) {
	return $array['todosCampos']['VendedorInterno'];
}

function mostrarVendedorExterno($array) {
	return $array['todosCampos']['VendedorExterno'];
}

function mostrarGrupo ($array) {
	return $array['todosCampos']['D046_Nome'];
}

function nomeEmpresaC004 ($array) {
	if($array['campoAtual'] > 0){
		return $array['todosCampos']['C004_Nome_Abreviado'];
	} else {
		return "TODAS EMPRESAS";
	}
}


?>