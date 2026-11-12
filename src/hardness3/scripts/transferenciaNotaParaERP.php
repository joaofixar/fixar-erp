<?php
namespace hardness;
die("Não está mais em uso.");

$confUsuarioERP = array(
	'dbDatabase' => 'global',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => '',
	//'dbPass' => '',
);
$conERP=@mysql_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioERP['dbDatabase'], $conERP) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');

$confUsuarioNOTA = array(
	'dbDatabase' => 'global',
	'dbHost' => '186.202.64.122',
	'dbUser' => 'root',
	'dbPass' => '',
);
$conNOTA=mysql_connect($confUsuarioNOTA['dbHost'], $confUsuarioNOTA['dbUser'], $confUsuarioNOTA['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioNOTA['dbDatabase'], $conNOTA) or die (mysql_error());
mysql_set_charset('utf8', $conNOTA);
mysql_query('SET NAMES utf8;', $conNOTA);
mysql_query("USE {$sys['dbDatabase']}", $conNOTA);

// Verificações
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || empty($argv[1])) {
	echo "Faltou o parametro com o Banco de Dados a ser transferido.\n";
	die();
}
$sql = "SELECT * FROM C025 WHERE LOWER(C025_Banco_Dados) = '{$argv[1]}'";
$res = mysql_query($sql, $conNOTA);
if (mysql_num_rows($res) != 1) {
	echo "Banco invalido.\n";
	die();
}
$C025 = mysql_fetch_assoc($res);
if (empty($C025['C025_Dominio'])) {
	echo "C025_Dominio em branco para este Banco.\n";
	die();
}
if (!empty($C025['C025_Flag_Transferido'])) {
	echo "Banco ja transferido.\n";
	die();
}

if ($C025['C025_Dominio'] == 'encaixe' || $C025['C025_Dominio'] == 'maximus' || $C025['C025_Dominio'] == 'sigma') {
	echo "Conflito de dominio.\n";
	die();
}

// Flag
$sql = "UPDATE C025 SET C025_Flag_Transferido = 1 WHERE C025_Id = '{$C025['C025_Id']}'";
$res = mysql_query($sql, $conNOTA);

// Copia BANCO
shell_exec('ionice -c3 rm -f /tmp/tmp.sql');
shell_exec("echo 'CREATE DATABASE IF NOT EXISTS {$argv[1]}; USE {$argv[1]};' > /tmp/tmp.sql && mysqldump --host='{$confUsuarioNOTA['dbHost']}' --user='{$confUsuarioNOTA['dbUser']}' --password='{$confUsuarioNOTA['dbPass']}' --routines {$argv[1]} >> /tmp/tmp.sql");
shell_exec("ionice -c3 mysql --user='{$confUsuarioERP['dbUser']}' --password='{$confUsuarioERP['dbPass']}' < /tmp/tmp.sql");

// Cria usuario no banco
mysql_query("DROP USER '{$argv[1]}'@'localhost';", $conERP);
mysql_query("FLUSH PRIVILEGES;", $conERP);
mysql_query("CREATE USER '{$argv[1]}'@'localhost' IDENTIFIED BY '{$C025['C025_Login']}';", $conERP);
mysql_query("FLUSH PRIVILEGES;", $conERP);
mysql_query("GRANT ALL PRIVILEGES ON `{$argv[1]}`.* TO '{$argv[1]}'@'localhost' WITH GRANT OPTION;", $conERP);
mysql_query("GRANT ALL PRIVILEGES ON `global`.* TO '{$argv[1]}'@'localhost' WITH GRANT OPTION;", $conERP);
mysql_query("FLUSH PRIVILEGES;", $conERP);

// Altera C004_Localizacao_Certificados
mysql_query("USE {$argv[1]}", $conERP);
mysql_query("UPDATE C004 SET C004_Localizacao_Certificados = '/var/www/dados/v2/dados_usuarios/{$argv[1]}/nfe/certs/'", $conERP);

// Dados usuarios
shell_exec("ionice -c3 rsync -av -e \"ssh -p 2022\" root@186.202.64.122:/var/www/hardness/dados_usuarios/{$argv[1]} /var/www/dados/v2/dados_usuarios/");

// Apache
shell_exec("ionice -c3 rm -f /etc/apache2/sites-enabled/{$C025['C025_Dominio']}.hardness.com.br");
$arquivo = <<<EOT
<VirtualHost *:80>
        ServerName {$C025['C025_Dominio']}.hardness.com.br
        ServerAdmin marcelo@hardness.com.br
        DocumentRoot /var/www/nota
        ErrorLog /var/log/apache2/{$C025['C025_Dominio']}.hardness.com.br-error.log
        <Directory />
                Options -Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>
</VirtualHost>
EOT;
file_put_contents("/etc/apache2/sites-enabled/{$C025['C025_Dominio']}.hardness.com.br", $arquivo);

// Conexão v2 e v3
shell_exec("ionice -c3 rm -f /var/www/sites/{$C025['C025_Dominio']}.hardness.com.br-confUsuario.php");
$arquivo = <<<EOT
<?php
namespace hardness;
\$confUsuario = array(
        // Banco de dados
        'dbDatabase' => '{$argv[1]}',
        'dbHost' => '127.0.0.1',
        'dbUser' => '{$argv[1]}',
        'dbPass' => '{$C025['C025_Login']}',
        // Caminho
        'pathRaiz' => '/var/www/nota/hardness3/',
        // WWW
        'urlRaiz' => 'http://{$C025['C025_Dominio']}.hardness.com.br/'
);
?>
EOT;
file_put_contents("/var/www/sites/{$C025['C025_Dominio']}.hardness.com.br-confUsuario.php", $arquivo);
shell_exec("ionice -c3 rm -f /var/www/sites/{$C025['C025_Dominio']}.hardness.com.br-conexao.inc");
$arquivo = <<<EOT
<?php

########################################################################################
#
#       - facilitar o processo de conexão com o Mysql
#       - Atravez do dominio verifico o tipo de conexao a ser usada
#
########################################################################################

    \$Banco_Dados='{$argv[1]}';

    \$Banco_Dados=strtolower(\$Banco_Dados);

    \$cBd_Padrao="\$Banco_Dados";

    if (substr(\$_SERVER["DOCUMENT_ROOT"],strlen(\$_SERVER["DOCUMENT_ROOT"]),1)!='/')
    {
       DEFINE(DIR_WWW,\$_SERVER["DOCUMENT_ROOT"].'/hardness');
    }
    else
    {
       DEFINE(DIR_WWW,\$_SERVER["DOCUMENT_ROOT"].'hardness');
    }
    if (!file_exists(DIR_WWW)) {
           echo "Falha no caminho DIR_WWW ".DIR_WWW;
           exit;
        }

    //\$CAMINHO_IMAGEM = "C:\Arquivos de programas\VertrigoServ\www\imagens_hardness";
    \$cFusoHorario='00:00:00';
    \$Entidade=StrToUpper(\$Entidade);

    \$servidor=\$_SERVER['SERVER_NAME'];

        \$host='127.0.0.1';
    \$db=\$cBd_Padrao;
    \$user='{$argv[1]}';
    \$pass='{$C025['C025_Login']}';

    // abre uma conexão no servidor
    \$con=@mysql_connect(\$host,\$user,\$pass);
    \$Error=mysql_error();
    if (!empty(\$Error)) {
        echo "<table border=0 width=100% height=60%>";
          echo "<tr>";
            echo "<td align=center>";
              echo "<table border=0 width=500 height=100 bgcolor=#EBE9E0 cellpadding=0 cellspacing=1  style='border: 1px solid;border-color:  white #B0B0B0 #B0B0B0 white;'>";
                echo "<tr>";
                  echo "<td align=center>";
                    echo "<img src='../imagens/gif/proibido.gif'>";
                  echo "</td>";
                  echo "<td align=left>";
                    echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 24;font-weight:bold;'>Acesso negado para o usuário:\$servidor - \$host,\$user,\$pass);</font>";
                    echo "<br>";
                    echo "<br>";
                    echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 16;font-weight:bold;'>\$Nome_Usuario</font>";
                  echo "</td>";
                echo "</tr>";
              echo "</table>";
            echo "</td>";
          echo "</tr>";
        echo "</table>";
        exit;
    }

    \$dbs=mysql_select_db("\$db",\$con) or die (mysql_error());

    // Busca os dados do usuario
    \$resultado=mysql_query("select * from C007 where C007_Id='\$Usuario_Atual'");
    \$mUsuario=mysql_fetch_array(\$resultado);
?>
EOT;
file_put_contents("/var/www/sites/{$C025['C025_Dominio']}.hardness.com.br-conexao.inc", $arquivo);

// Apache
shell_exec("service apache2 restart");

/*
$dominios = array();
mysql_query("USE e001", $conNOTA);
$sql = "SELECT * FROM C025 LEFT JOIN D024 ON D024_Id = C025_D024_Id";
$res = mysql_query($sql, $conNOTA);
while ($row = mysql_fetch_assoc($res)) {
	if (!empty($row['D024_Nome_Empresa'])) {
		$dominio_ = $row['D024_Nome_Empresa'];
		$dominio_ = explode(' ', $dominio_);
		$dominio = strtolower($dominio_[0]);
		if (in_array($dominio, $dominios) || strlen($dominio) < 3) {
			$dominio .= strtolower($dominio_[1]);
			if (in_array($dominio, $dominios) || strlen($dominio) < 3) {
				$dominio .= strtolower($dominio_[2]);
				if (in_array($dominio, $dominios)) {
					$dominio .= strtolower($dominio_[3]);
					if (in_array($dominio, $dominios)) {
						die('dominio duplicado');
					}
				}
			}
		}
		$dominios[] = $dominio;
		echo  "{$row['C025_Id']} - {$row['C025_Banco_Dados']} - {$dominio} - {$row['D024_Nome_Empresa']}\n";
		//echo "UPDATE C025 SET C025_Dominio = '{$dominio}' WHERE C025_Id = '{$row['C025_Id']}';\n";
	}
}
*/

//echo 'CREATE DATABASE IF NOT EXISTS e300; USE e300;' > e214-e300.sql && mysqldump --user=root --password='' --routines e214 >> e214-e300.sql
//sed -i 's/`e214`/`e300`/g' e214-e300.sql
//mysql --user=root --password='' < e214-e300.sql
