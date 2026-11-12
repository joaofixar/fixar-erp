<?php

$argv = $_SERVER['argv'];
$dominio = $argv[1];
$site = $argv[2];
$versao = "trunk";
$C025_Banco_Dados = $argv[3];
$C025_Banco_Dados_Senha = trim($argv[4]);

// Criação dos arquivos de configurações (Hardness e Hardness3) e domínio no apache
	//shell_exec("ionice -c3 rm -f /etc/apache2/sites-enabled/{$dominio}.{$site}.conf");
	$arquivo = <<<EOT
<VirtualHost *:80>
        ServerName {$dominio}.{$site}
        ServerAdmin marcelo@hardness.com.br
        DocumentRoot /var/www/versoes/v3/{$versao}
        ErrorLog /var/log/apache2/{$dominio}.{$site}-error.log
        <Directory />
                Options -Indexes +FollowSymLinks +MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>
</VirtualHost>
EOT;
	// Configuração do apache não é mais necessário no novo servidor (usamos wildcard)
	if ($site != 'fastcrud.com.br') {
        $retorno = file_put_contents("/etc/apache2/sites-enabled/{$dominio}.{$site}.conf", $arquivo);
    }

	// Conexão v2 e v3
	//shell_exec("ionice -c3 rm -f /var/www/sites/{$dominio}.{$site}-confUsuario.php");
	$arquivo = <<<EOT
<?php
namespace hardness;
\$confUsuario = array(
        // Banco de dados
        'dbDatabase' => '{$C025_Banco_Dados}',
        'dbHost' => '127.0.0.1',
        'dbUser' => '{$C025_Banco_Dados}',
        'dbPass' => '{$C025_Banco_Dados_Senha}',
        // Caminho
        'pathRaiz' => '/var/www/versoes/v3/{$versao}/hardness3/',
        // WWW
        'urlRaiz' => 'http://{$dominio}.{$site}/'
);
?>
EOT;
	$retorno = file_put_contents("/var/www/sites/{$dominio}.{$site}-confUsuario.php", $arquivo);

	//shell_exec("ionice -c3 rm -f /var/www/sites/{$dominio}.{$site}-conexao.inc");
	$arquivo = <<<EOT
<?php
error_reporting(E_ALL ^ E_NOTICE);

########################################################################################
#
#       - facilitar o processo de conexão com o Mysql
#       - Atravez do dominio verifico o tipo de conexao a ser usada
#
########################################################################################

    \$Banco_Dados='{$C025_Banco_Dados}';

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
    \$user='{$C025_Banco_Dados}';
    \$pass='{$C025_Banco_Dados_Senha}';

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
	$retorno = file_put_contents("/var/www/sites/{$dominio}.{$site}-conexao.inc", $arquivo);

// Configuração do apache não é mais necessário no novo servidor (usamos wildcard)
if ($site != 'fastcrud.com.br') {
    exec("/etc/init.d/apache2 reload");
}
