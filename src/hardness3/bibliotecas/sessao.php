<?php
namespace hardness;
/**
* Sessão
*/

/*
Variáveis antigas:

Array
(
    [Modulo_NFe] => S
    [Usuario_Atual] => 63
    [Nome_Usuario] => ROOT - ADMINISTRADOR
    [Cargo_Usuario] => 
    [Perfil_Usuario] => 12
    [Empresa_Atual] => 2
    [Empresa_Logada] => 2
    [Nome_Empresa_Atual] => TESTE
    [Banco_Dados] => e009
    [sb] => 
    [nWidth_Padrao] => 
    [nHeight_Padrao] => 
    [Pasta_Empresa] => E003
    [cBd_Configuracao] => e009
    [Usuario_Suporte] => S
    [User_Suport] => 
    [Usuario_Contador] => 
    [Empresa_Usuaria] => 
    [URL_Historico] => 
    [URL_Tipo_Historico] => 
    [URL_Descricao_Historico] => 
    [URL_Entidade_Principal] => 
    [Pagina_Monta_Consulta] => 
    [Chave_Pagina_Monta_Consulta] => 
    [PaginaQueIniciou] => 
)
*/
//session_start();

$g['empresaAtual'] = '';
$g['usuarioAtual'] = '';
$g['perfilUsuario'] = '';
$g['clienteAtual'] = '';
$g['clienteAtualContato'] = '';
$g['clienteAtualVendedor'] = '';
$g['clienteAtualVendedorExterno'] = '';
$g['clienteAtualRepresentante'] = '';
$g['dbDatabase'] = '';
$g['versaoB2B'] = '';
foreach ($_SESSION as $variavel => $value) {
	switch ($variavel) {
		case 'Empresa_Atual':
			$g['empresaAtual'] = $value;
			break;
		case 'Usuario_Atual':
			$g['usuarioAtual'] = $value;
			break;
		case 'Perfil_Usuario':
			$g['perfilUsuario'] = $value;
			break;
        case 'Cliente_Atual':
            $g['clienteAtual'] = $value;
            break;
        case 'Cliente_Atual_Contato':
            $g['clienteAtualContato'] = $value;
            break;
        case 'Versao_B2B':
            $g['versaoB2B'] = $value;
            break;
        case 'Cliente_Atual_Vendedor':
            $g['clienteAtualVendedor'] = $value;
            break;
        case 'Cliente_Atual_Vendedor_Externo':
            $g['clienteAtualVendedorExterno'] = $value;
            break;
        case 'Cliente_Atual_Representante':
            $g['clienteAtualRepresentante'] = $value;
            break;     
        case 'Tipo_Login_Usuario_B2B':
            $g['tipoLoginUsuarioB2B'] = $value;
            break;     
        case 'Usuario_Alcada':
            $g['usuarioAlcada'] = $value;
            break;    
        case 'Banco_Dados':
            $g['dbDatabase'] = $value;
            break; 
	}
	$$variavel = $value;
}

/*
if (!empty($URL_Historico)) {
	$mURL_Historico=explode(",,",$URL_Historico);
	$mURL_Tipo_Historico=explode(",,",$URL_Tipo_Historico);
	$mURL_Descricao_Historico=explode(",,",$URL_Descricao_Historico);
	$mURL_Entidade_Principal=explode(",,",$URL_Entidade_Principal);
}
if (!empty($Pagina_Monta_Consulta)) {
	$mPagina_Posicao_Monta_Consulta=explode(",,",$Pagina_Monta_Consulta);
	$mChave_Pagina_Monta_Consulta=explode(",,",$Chave_Pagina_Monta_Consulta);

	foreach($mChave_Pagina_Monta_Consulta as $Chave => $Valor) {
		$mPagina_Monta_Consulta[$Valor]=$mPagina_Posicao_Monta_Consulta[$Chave];
	}
}

$mErroPermiteAlterarNF[267]="NF Complementar referencia uma NF-e inexistente";
$mErroPermiteAlterarNF[269]="CNPJ Emitente da NF Complementar difere do CNPJ da NF referenciada";
$mErroPermiteAlterarNF[210]="IE do destinat�rio inv�lida";
$mErroPermiteAlterarNF[243]="XML de Dados Mal Formado";
*/
?>