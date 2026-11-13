<?php

  @session_destroy();
  @session_start();

  $e="{$_REQUEST['e']}";
  $l="{$_REQUEST['l']}";
  $s="{$_REQUEST['s']}";
  $Banco_Dados="{$_REQUEST['b']}";
  $w="{$_REQUEST['w']}";
  $sb="{$_REQUEST['sb']}";
  $us="{$_REQUEST['us']}";
  $uc="{$_REQUEST['uc']}";
  $Empresa_Usuaria="{$_REQUEST['Empresa_Usuaria']}";
  
  $PaginaQueIniciou="{$_POST['PaginaQueIniciou']}";
  
  include 'bibliotecas/confUsuario.php';
  $user = explode('/', $confUsuario['pathRaiz']);
  $user = $user[2];

  include 'bibliotecas/conexaoBanco.php';
  
if (empty($l)) {
   echo "<script>
           alert('Digite o seu Login');
           parent.document.getElementById('l').focus();
         </script>";
   exit;
}

if (empty($s)) {
   echo "<script>
           alert('Digite a Senha');
           parent.document.getElementById('formulario_login').getElementById('s').focus();
         </script>";
   exit;
}

if (empty($e)) {
   echo "<script>
           alert('Selecione uma empresa');
           parent.document.getElementById('formulario_login').getElementById('e').focus();
         </script>";
   exit;
}

$cC004=mysql_query("select * from C004 where C004_Id='$e'");
$mC004=mysql_fetch_array($cC004);
If (empty($mC004[C004_Banco_Configuracao])) {
   echo "<script>
	      alert('Banco de configuração nao definido');
	    </script>"; 
   exit;
}
$cBd_Configuracao=$mC004[C004_Banco_Configuracao];


$cC020=mysql_query("select C020_Modelo_Sistema_Nfe from C020 where C020_C004_Id='$mC004[C004_Id]'");
$mC020=mysql_fetch_array($cC020);

   // Consulta a Senha Digitada do Usuario Selecionado
   $Sql=mysql_query("select * from $cBd_Configuracao.C007
                              left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
                              left join C021 on C021_Id=C019_C021_Id
                              where C007_Login='$l'
                                and C007_Ativo='S'
                                and (C007_D024_Id=0 or C007_D024_Id is null)");

   echo mysql_error();
   if (mysql_num_rows($Sql)==0) {
      echo "<script>
              alert('Login ou Senha Inv�lida (2)');
              parent.document.getElementById('l').value='';
              parent.document.getElementById('s').value='';
              parent.document.getElementById('l').focus();
            </script>";
      exit;
   }

   $mSenha=mysql_fetch_array($Sql);

   $Senha=base64_encode($s);

   $cSenha=base64_decode($mSenha[C007_Senha]);

   // Verifica se a Senha Digitada pelo usuario � valida.

   if ($Senha!=$mSenha[C007_Senha]) {
      echo "<script>
              alert('Login ou Senha Inv�lida (3)');
              parent.document.getElementById('l').value='';
              parent.document.getElementById('s').value='';
              parent.document.getElementById('l').focus();
            </script>";
      exit;
   }

   if (!empty($mSenha[C007_Cargo])) {
      $mSenha[C007_Cargo]='  -   (  '.$mSenha[C007_Cargo].'  )';
   }

   // verifica se a senha esta vencida 30 DIAS
   $Sql=mysql_query("select * from $cBd_Configuracao.C007 where C007_Login='$l'
                                                 and C007_Ativo='S'
                                                 and C007_Data_Alteracao_Senha<=date_sub(current_date(), interval 30 day)");
   echo mysql_error();
          If (empty($mC004[C004_Caminho_Sistema])) {
             // Pasta da empresa
             echo "<script>
                     alert('Pasta da empresa não definida');
                   </script>";  
             exit;
          }

          // Atribui a Variavel de Sessao o Nome e o Codigo do Usuario
          session_start();
          $_SESSION[Modulo_NFe]='S';
          $_SESSION['Usuario_Atual']="$mSenha[C007_Id]";
          $_SESSION['Nome_Usuario']="$mSenha[C007_Nome]";
          $_SESSION['Cargo_Usuario']="$mSenha[C007_Cargo]";
          $_SESSION['Perfil_Usuario']="$mSenha[C021_Id]";
          $_SESSION['Empresa_Atual']="$e";
          $_SESSION['Empresa_Logada']="$e";
          $_SESSION['Nome_Empresa_Atual']="$mC004[C004_Nome_Empresa]";
          $_SESSION['Banco_Dados']="$confUsuario[dbDatabase]";
          $_SESSION['sb']="$sb";
          $_SESSION['nWidth_Padrao']="$nWidth_Padrao";
          $_SESSION['nHeight_Padrao']="$nHeight_Padrao";
          $_SESSION['Pasta_Empresa']="$mC004[C004_Caminho_Sistema]"; //-- pasta onde estao localizados os dados da empresa
          $_SESSION['cBd_Configuracao']="$cBd_Configuracao";
          $_SESSION['Usuario_Suporte']="$mSenha[C007_Usuario_Suporte]";
          $_SESSION['User_Suport']="$us";
          $_SESSION['Usuario_Contador']="$uc";
          $_SESSION['Empresa_Usuaria']="$Empresa_Usuaria";
          
          // -> Registra um historico de Enderecos
          $_SESSION['URL_Historico']="";

          // -> Registra um historico de Tipos de Telas
          $_SESSION['URL_Tipo_Historico']="";

          // -> Registra um historico de nomes l�gicos das p�ginas acessadas
          $_SESSION['URL_Descricao_Historico']="";

          // -> Registra um historico com as Entidades Principais
          $_SESSION['URL_Entidade_Principal']="";

          //
          // Guarda o numero pagina acessada no monta_consultas_fme
          // Utiliza como chave da matriz a Entidade+cTela+Entidade_Principal+Id_Entidade_Principal
          //
          $_SESSION['Pagina_Monta_Consulta']="";
          $_SESSION['Chave_Pagina_Monta_Consulta']="";

	   	  $_SESSION['PaginaQueIniciou']=$PaginaQueIniciou;
	   	  
	   	  if ($user == 'www') { $user = 'crv.dnsalias.com:8080'; }
	   	  //if ($user == 'marcelo') { $user = '201.22.57.137:8181'; }
	   	  echo "<script>location.replace('http://{$user}/site/');</script>";
	   	  
?>
</body>
</HTML>
