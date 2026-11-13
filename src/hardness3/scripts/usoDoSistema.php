<?php
namespace hardness;

/**
* Este arquivo é processado 1 vez por dia
*/
set_time_limit(0);
$time_start = microtime(true);
$g = array();
$g['usuarioAtual'] = 63;
$g['empresaAtual'] = 1;
$g['time'] = array();
$g['time']['sql'] = '';
$_SERVER['REMOTE_ADDR'] = '';

// Verificações
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || empty($argv[1])) {
	echo "Faltou o parametro com nome da empresa a ser executado!\nExemplo: fixar\n";
	die();
}
$dominio = $argv[1];
$conf = "/var/www/sites/{$dominio}.hardness.com.br-confUsuario.php";
$local = '/var/www/html/hardness3/';
if ($dominio == 'flycker') {
	$conf = "/home/flycker/html/hardness3/bibliotecas/confUsuario.php";
	$local = '/home/flycker/html/hardness3/';
}
if ($dominio == 'fixar') {
	$conf = "/var/www/hardness3/bibliotecas/confUsuario.php";
	$local = '/var/www/hardness3/';
}
if ($dominio == 'ceigon') {
	$conf = "/var/www/html/hardness3/bibliotecas/confUsuario.php";
	$local = '/var/www/html/hardness3/';
}
if (!file_exists($conf)) {
	echo "Empresa não encontrada!\n";
	die();
}

// Carrega as bibliotecas necessárias...
require_once($conf);
$home = $confUsuario['pathRaiz'];
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'sistema/confSistema.php');
require_once($home . 'bibliotecas/funcoesGerais.php');
$g['conf'] = new confSistema();
$g['pathRaiz'] = $confUsuario['pathRaiz'];
$g['urlRaiz'] = $confUsuario['urlRaiz'];
chdir($g['pathRaiz']);

// Caminhos
// dados_usuarios/{DB}/
$g['pathDados'] = "dados_usuarios/{$confUsuario['dbDatabase']}/";
$g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
$g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
$g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';
$g['pathDados'] = $home . $g['pathDados'];
$g['pathDadosUsuario'] = $home . $g['pathDadosUsuario'];

$empresas = array(
	array('525', 'http://eletron.ceigon.com.br:8080/'),
	array('443', 'http://gp.hardness.com.br/'),
	array('50', 'http://hardness.alumifix.com.br/'),
	array('112', 'http://tudo.hardness.com.br/'),
	array('520', 'http://hardness.hardness.com.br/'),
	array('469', 'http://fosprojetos.hardness.com.br/'),
	array('418', 'http://sigma.hardness.com.br/'),
	array('450', 'http://fixar.infoqos.com.br:8080/'),
	array('504', 'http://fcx.hardness.com.br/'),
	array('419', 'http://eurosul.hardness.com.br/'),
	array('466', 'http://rdll.hardness.com.br/'),
	array('489', 'http://dio.hardness.com.br/'),
	array('522', 'http://dex.hardness.com.br/'),
	array('495', 'http://azpr.hardness.com.br/'),
	array('527', 'http://lehman.hardness.com.br/'),
	array('51', 'http://aneisrcs.no-ip.org:8080/'),
	array('485', 'http://hardness.guimepa.com.br:8080/'),
	array('454', 'http://koisas.hardness.com.br:8080/'),
);

foreach ($empresas as $empresa) {
	$resultado = file_get_contents($empresa[1] . '/sistema/funcoes/usoDoSistema/?ajax=true&noEmp=true&p=true');
	echo "{$empresa[1]}... ";
	if (!empty($resultado)) {
		$resultado = unserialize($resultado);
		echo count($resultado);
		foreach ($resultado as $key => $usuario) {
			if ($usuario['hits'] > 0 || $usuario['size'] > 0) {
				$sql = "SELECT P001_Id FROM P001 WHERE P001_D024_Id = '{$empresa[0]}' AND P001_C007_Id = '{$key}' AND P001_Data = CURDATE()";
				$res = mysql_query($sql);
				if (mysql_num_rows($res) == 0) {
					$sql = <<<EOT
INSERT INTO P001
(P001_D024_Id, P001_C007_Id, P001_Data, P001_Login, P001_Hits, P001_Size)
VALUES
('$empresa[0]}', '{$key}', CURDATE(), '{$usuario['login']}', '{$usuario['hits']}', '{$usuario['size']}')
EOT;
					mysql_query($sql);
				} else {
					$row = mysql_fetch_assoc($res);
					$sql = <<<EOT
UPDATE P001
SET 
	P001_Hits = P001_Hits + {$usuario['hits']},
	P001_Size = P001_Size + {$usuario['size']}
WHERE
	P001_Id = '{$row['P001_Id']}'
EOT;
					mysqli_query($sql);
				}
			}
		}
	}
	echo "\n";
}
