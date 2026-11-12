<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {	
	case 'orcamento':
		if(!empty($g['c004']['C004_Logo_Marca'])){
			$pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
			$caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
			$caminhoImagem = "<img src=\"{$caminhoImagem}\">";
		} else {
			$caminhoImagem="";
		}
		$r_T004_Id = mysql_real_escape_string($r_T004_Id);
		$T004 = "SELECT * FROM T004 WHERE T004_Id = '{$r_T004_Id}';";
		$T004 = mysql_query($T004);
		$T004 = mysql_fetch_assoc($T004);
		echo <<<EOT
			<a href="javascript:history.back();">voltar</a>&nbsp; - &nbsp;<a href="javascript:window.close();">fechar</a>
			<br /><br />
			<table>
				<tr>
					<td align="left">{$caminhoImagem}</td>
				</tr>
			</table>
			
			Orçamento: <b>{$r_T003_Id}</b><br />
			Item: <b>{$T004['T004_Descricao_Produto']}</b><br />
			Imagem: <br />
			<img src="{$r_src}" />
EOT;
	break;
}
?>
