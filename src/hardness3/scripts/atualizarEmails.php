<?php
namespace hardness;
$home = __FILE__;
$home = str_replace('scripts', '', dirname($home));
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'sistema/comunicacaoEmail.php');

$g = array();

$sql = "SELECT * FROM C007;";
$resultado = mysql_query($sql);
echo "Atualizando base: {$confUsuario['dbDatabase']}\n";
while ($C007 = mysql_fetch_array($resultado)) {
	$g['usuarioAtual'] = $C007['C007_Id'];
	echo "Verificando e-mails: {$C007['C007_Login']} ... ";
	if (empty($C007['C007_Email_POP_Servidor']) || empty($C007['C007_Email_SMTP_Usuario']) || empty($C007['C007_Email_SMTP_Senha'])) { echo "(e-mail não configurado para este usuário)\n"; continue; }
	echo "\n";

	$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'E', false, $C007['C007_Email_POP_Porta']);
	$totalEntrada = $mail->atualizarEmails();
	if ($totalEntrada === false) {
		echo "\tErro ao tentar conectar. Por favor, verifique se o Servidor/Login/Senha estão corretos.\n";
	} else {
		$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'S', false, $C007['C007_Email_POP_Porta']);
		$totalSaida = $mail->atualizarEmails();
		if ($totalSaida === false) {
			echo "\tErro ao tentar conectar.<br />Por favor, verifique se o Servidor/Login/Senha estão corretos.\n";
		} else {
			$total = $totalEntrada + $totalSaida;
			$totalEntrada = (int) $totalEntrada;
			$totalSaida = (int) $totalSaida;
			if ($total == 0) {
				echo "\tAtualização completa. Nenhum novo e-mail.\n";
			} else {
				echo "\tAtualização completa. Você tem novo(s) e-mail(s). Entrada ($totalEntrada)<br />Saída ($totalSaida)\n";
			}
		}
	}
}
