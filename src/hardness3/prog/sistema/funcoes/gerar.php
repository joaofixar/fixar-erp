<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'pdf':
		$dir = $g['pathDados'] . 'pdf/';
		is_dir($dir) or mkdir($dir, 0777, true);
		$filename = md5($r_link . time()) . '.pdf';
		$file = $dir . $filename;
		$fileFull = $g['pathRaiz'] . '/' . $file;
		
		// Corrige os parametros do link com urlencode();
		preg_match_all('#&([^&]+)#', $r_link, $matches);
		$link = '?ajax=true';
		foreach ($matches[1] as $value) {
			if (preg_match('#=1$#', $value)) {
				$link .= '&' . preg_replace('#=1$#', '', $value) . '=1';
			} else {
				$tmp = explode('=', $value);
				$tmp2 = array_pop($tmp);
				$tmp = implode('=', $tmp);
				//$tmp = urlencode($tmp);
				$link .= '&' . $tmp . '=' . $tmp2;
			}
		}
		//$r_link = str_replace(' ', '+', $r_link);
		$r_link = explode('?ajax=true', $r_link);
		$r_link = $r_link[0] . $link;

        // Gerar PDF
        $sessionCookie = $_COOKIE[session_name()];
		shell_exec("xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&titulo={$r_titulo}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}&sessionCookie={$sessionCookie}\" \"{$fileFull}\"");

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//window.location = "/sistema/funcoes/gerar/abrirPdf/?ajax=true&filename={$filename}";
				nova_janela('/hardness3/{$file}',20,20,900,700,'no','no','no','no','yes','yes',0);
			} </script></div>
EOT;
	break;
	
	case 'abrirPdf':
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename={$r_filename};"); // size=$fileSize;
		$dir = $g['pathDados'] . 'pdf/';
		$file = $dir . $r_filename;
		$handle = fopen($file, "rb");
		if ($handle) {
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			fclose($handle);
		}
	break;
}
?>