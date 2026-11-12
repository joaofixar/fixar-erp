<?php
namespace hardness;

switch ($g['r'][3]) {

	case 'dadosDisco':
		global $g;
		echo "<div style='margin-left:15px;'>";
			echo "<font style='font-family:Arial;colot:blue;font-size:20px;font-weight:bold;'>DADOS DISCO</font>";

			$caminhoComando = "{$g['pathRaiz']}dados_usuarios/{$g['dbDatabase']}/";
			$caminhoArquivo = "{$caminhoComando}tmp/dadosSistema.txt";

			echo "<br><br><font style='font-family:Arial;font-size:16px;'>COMANDO:</font> <font style='font-family:Arial;color:blue;font-size:16px;font-weight:bold;'>df -h </font>";

			shell_exec("df -h | sort -h > {$caminhoArquivo}");
			$retorno = file("{$caminhoArquivo}");

			echo "<font style='font-family:Arial;font-size:14px;'>";
			echo "<pre>";
			print_r($retorno);
			echo "</pre>";
			echo "</font>";
		echo "</div>";

	break;

	case 'dadosEmpresasServidor':
		global $g;
		echo "<div style='margin-left:15px;'>";
			echo "<font style='font-family:Arial;colot:blue;font-size:20px;font-weight:bold;'>DADOS EMPRESAS DO SERVIDOR</font>";

			$caminhoComando = "{$g['pathRaiz']}dados_usuarios";
			$caminhoArquivo = "{$caminhoComando}/{$g['dbDatabase']}/tmp/dadosSistema.txt";
			echo "<br><br><font style='font-family:Arial;font-size:16px;'>COMANDO:</font> <font style='font-family:Arial;color:blue;font-size:16px;font-weight:bold;'> du -shc $caminhoComando/* | sort -h</font>";

			shell_exec("du -shc $caminhoComando/* | sort -h > {$caminhoArquivo}");
			$retorno = file("{$caminhoArquivo}");

			echo "<font style='font-family:Arial;font-size:14px;'>";
			echo "<pre>";
			print_r($retorno);
			echo "</pre>";
			echo "</font>";
		echo "</div>";
	break;

	case 'dadosUsuarios':
		global $g;
		echo "<div style='margin-left:15px;'>";
			echo "<font style='font-family:Arial;colot:blue;font-size:20px;font-weight:bold;'>DADOS USUÁRIOS EMPRESA ATUAL</font>";

			$caminhoComando = "{$g['pathRaiz']}dados_usuarios/{$g['dbDatabase']}";
			$caminhoArquivo = "{$caminhoComando}/tmp/dadosSistema.txt";
			echo "<br><br><font style='font-family:Arial;font-size:16px;'>COMANDO:</font> <font style='font-family:Arial;color:blue;font-size:16px;font-weight:bold;'> du -shc $caminhoComando/usuarios/* | sort -h </font>";

			shell_exec("du -shc $caminhoComando/usuarios/* | sort -h > {$caminhoArquivo}");
			$retorno = file("{$caminhoArquivo}");

			echo "<font style='font-family:Arial;font-size:14px;'>";
			echo "<pre>";
			print_r($retorno);
			echo "</pre>";
			echo "</font>";
		echo "</div>";
	break;

	case 'dadosEmpresaAtual':
		global $g;
		echo "<div style='margin-left:15px;'>";
			echo "<font style='font-family:Arial;colot:blue;font-size:20px;font-weight:bold;'>DADOS EMPRESA ATUAL</font>";

			$caminhoComando = "{$g['pathRaiz']}dados_usuarios/{$g['dbDatabase']}";
			$caminhoArquivo = "{$caminhoComando}/tmp/dadosSistema.txt";
			echo "<br><br><font style='font-family:Arial;font-size:16px;'>COMANDO:</font> <font style='font-family:Arial;color:blue;font-size:16px;font-weight:bold;'> du -shc $caminhoComando/* | sort -h </font>";

			shell_exec("du -shc $caminhoComando/* | sort -h > {$caminhoArquivo}");
			$retorno = file("{$caminhoArquivo}");

			echo "<font style='font-family:Arial;font-size:14px;'>";
			echo "<pre>";
			print_r($retorno);
			echo "</pre>";
			echo "</font>";
		echo "</div>";
	break;

	case 'dadosBancoDados':
		global $g;
		echo "<div style='margin-left:15px;'>";
			echo "<font style='font-family:Arial;colot:blue;font-size:20px;font-weight:bold;'>DADOS EMPRESA ATUAL</font>";

			$caminhoArquivo = "{$g['pathRaiz']}scripts/dadosSistema/banco.txt";

			echo "<br><br><font style='font-family:Arial;font-size:16px;'>COMANDO:</font> <font style='font-family:Arial;color:blue;font-size:16px;font-weight:bold;'> du -shc $caminhoComando | sort -h </font>";

			$retorno = file("{$caminhoArquivo}");

			echo "<font style='font-family:Arial;font-size:14px;'>";
			echo "<pre>";
			print_r($retorno);
			echo "</pre>";
			echo "</font>";
		echo "</div>";
	break;

	case 'dadosBancoDadosEmpresaAtual':
		global $g;
		echo "<div style='margin-left:15px;'>";
			echo "<font style='font-family:Arial;colot:blue;font-size:20px;font-weight:bold;'>DADOS EMPRESA ATUAL</font>";

			$caminhoComando = "/var/lib/mysql/*";
			$caminhoArquivo = "{$g['pathRaiz']}scripts/dadosSistema/banco.txt";

			echo "<br><br><font style='font-family:Arial;font-size:16px;'>COMANDO:</font> <font style='font-family:Arial;color:blue;font-size:16px;font-weight:bold;'> du -shc $caminhoComando | sort -h </font>";

			//setadando banco manualmente para testes pois nosso servidor do mysql é separado do servidor do sistema
			// $g['dbDatabase'] = "e223_novo";
			$retorno = file("{$caminhoArquivo}");

			foreach ($retorno as $key => $banco) {
				if(stripos($banco,$g['dbDatabase'])){
					break;
				}
			}
			echo "<font style='font-family:Arial;font-size:14px;'>";
			echo "<br><br>Banco de dados cliente: ".$banco;
			echo "</font>";
		echo "</div>";
	break;
}


