<?php
namespace hardness;

switch($g['r'][3]){
	case 'salvar':
		$arquivo = "{$g['pathDados']}personalizacaoTab/janelasIgnorarESC.serialized";
		file_put_contents($arquivo, $r_json);
	break;
}
