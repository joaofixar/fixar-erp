<?php
namespace hardness;

switch($g['r'][3]){
	case 'salvar':
		$arquivo = $g['pathDadosUsuario'] . 'tamanhosJanelas.json';
		file_put_contents($arquivo, $r_json);
	break;
}