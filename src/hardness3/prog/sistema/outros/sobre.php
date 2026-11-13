<?php
namespace hardness;

switch($g['r'][3]){
	case 'content':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/outros/sobre/sobre/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'sobre':
		$versao = file_get_contents('version.txt');
		mysql_query("UPDATE C007 SET C007_Notificar_Versao = '{$versao}' WHERE C007_Id = '{$g['usuarioAtual']}';");
		$changelog = file_get_contents('changelog.txt');
		$dominio = str_replace('online','com.br',$sys['dominio']);
		echo <<<EOT
<img src="{$sys['logoSobre']}" style="float: left; width: 155; height: 73px; margin: 19px 26px;"></img>
<div style="float: left; padding: 3px 0 0 0; width: 315px;">
	<h1 style="margin: 0; line-height: 26px;">{$sys['nomeEmpresa']}</h1>
	<div style="font-size: 14px; font-family: Helvetica;margin-bottom: 2px;">{$sys['telefone']}</div>
	<div style="margin-bottom: 2px;"><a href="http://www.{$dominio}/">http://www.{$dominio}/</a></div>
	<div style="margin-bottom: 2px;"><a href="mailto: suporte@{$dominio}">suporte@{$dominio}</a></div>
	<div style="font-size: 11px; font-family: Helvetica;margin-bottom: 2px;">Versão: <b>{$versao}</b></div>
	® Hardness Sistemas - Todos os direitos resevados
	<div style="position:absolute; width:75px; height:60px; top: 3px; right: 15px;">
	  <a href="http://www.teamviewer.com/link/?url=505374&id=700304618" style="text-decoration:none;">
	    <img src="/hardness3/static/img/icon_teamviewer.png" alt="Baixe o TeamViewer QuickSupport" title="Baixe o TeamViewer QuickSupport" border="0" width="90" height="60" />
	    <span style="top:25.5px; margin-left:10px; display:block; cursor:pointer; color: #222; font-family:Arial; font-size:10px; line-height:1.2em; font-weight:bold; text-align:center; width:65px;">
	      Baixe o TeamViewer
	    </span>
	  </a>
	</div>
</div>

<textarea style="width: 518px; height: 221px; text-transform: none; background: white; float: left; clear: both; font-size: 12px;" readonly="readonly">
Histórico de alterações desta versão.

{$changelog}
</textarea>
EOT;
	break;
}
