<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'phpinfo':
		phpinfo();
	break;

	case 'verificaEmpresaAtual':
		$resposta = $g['empresaAtual'];
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'verificaPermissao':
		$resposta = isset($g['c029Ids'][$r_permissao]) ? 1 : 0;
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'verificaDominio':
		$resposta = ($confUsuario['dbDatabase'] == $sys['dbDatabase'] && $sys['urlEmpresa'] != 'hardness.hardness.com.br') ? 'hardness.hardness.com.br' : $sys['urlEmpresa'];
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'alterarTamanhoFonte':
		$resposta['code'] = true;
		mysql_query("UPDATE C007 SET C007_Tamanho_Fonte_Sistema = '{$r_tamanhoFonte}' WHERE C007_Id = '{$g['usuarioAtual']}'");
		$erro = mysql_error();
		if(!empty($erro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao alterar tamanho da fonte";
		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

    case 'alterarTema':
        $resposta['code'] = true;
        mysql_query("UPDATE C007 SET C007_Tema = '{$r_tema}' WHERE C007_Id = '{$g['usuarioAtual']}'");
        $erro = mysql_error();
        if(!empty($erro)){
            $resposta['code'] = false;
            $resposta['data'] = "Erro ao alterar tema do usuário";
        }
        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

	case 'alterarLayout':
        $resposta['code'] = true;
        mysql_query("UPDATE C007 SET C007_Tema_Layout = '{$r_layout}' WHERE C007_Id = '{$g['usuarioAtual']}'");
        $erro = mysql_error();
        if(!empty($erro)){
            $resposta['code'] = false;
            $resposta['data'] = "Erro ao alterar layout do usuário";
        }
        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;
}
