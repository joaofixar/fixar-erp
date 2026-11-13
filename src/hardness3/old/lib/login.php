<?php

  $e="{$_REQUEST['e']}";
  $l="{$_REQUEST['l']}";
  $s="{$_REQUEST['s']}";
  $w="{$_REQUEST['w']}";
  $sb="{$_REQUEST['sb']}";
  $Banco_Dados="{$_REQUEST['b']}";
  
  // Conexao Com Banco de Dados
  include 'conexao.php';

?>
<HTML>
<HEAD>
<TITLE>VERIFICANDO ACESSO...</TITLE>
</HEAD>
<!-- Funções Genéricas em Javascript -->
<script src='funcoes.js'></script>
<script language="JavaScript1.1">
function Abre() {
   param = "toolbar=no,menubar=no,location=no,scrollbars=no,status=yes,resizable=yes,width=790,height=542,top=0,left=0";
   nova_janela_full("hardness.php");
}
</script>
<body>
<?php

// Validacoes ----------------------------------------------------------------------------

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
   msg_alerta("Banco de configuração nao definido");
   exit;
}
$cBd_Configuracao=$mC004[C004_Banco_Configuracao];


   // Consulta a Senha Digitada do Usuario Selecionado
   $Sql=mysql_query("select * from C007
                              left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='$e'
                              left join C021 on C021_Id=C019_C021_Id
                              where C007_Login='$l'
                                and C007_Ativo='S'
                                and (C007_D024_Id=0 or C007_D024_Id is null)");

   echo mysql_error();
   if (mysql_num_rows($Sql)==0) {
      echo "<script>
              alert('Login ou Senha Inválida');
              parent.document.getElementById('l').value='';
              parent.document.getElementById('s').value='';
              parent.document.getElementById('l').focus();
            </script>";
      exit;
   }

   $mSenha=mysql_fetch_array($Sql);

   if (empty($mSenha[C007_Senha]))
   {
      echo "<script>
       parent.document.getElementById('formulario_login').target='';
       parent.document.getElementById('formulario_login').action='config/alterar_senha.php3';
       parent.document.getElementById('formulario_login').submit();
      </script>";
      exit;
   }

   // Codifica a senha digitada
   $Senha=base64_encode($s);

   // Verifica se a Senha Digitada pelo usuario é valida.

   if (($Senha!=$mSenha[C007_Senha]) and ($Senha!=$mSenha[C007_Senha_Especial])) {
      echo "<script>
              alert('Login ou Senha Inválida');
              parent.document.getElementById('l').value='';
              parent.document.getElementById('s').value='';
              parent.document.getElementById('l').focus();
            </script>";
      exit;
   }

   if (!empty($mSenha[C007_IP_Acesso]) and ($Senha!=$mSenha[C007_Senha_Especial])) {
      $IP_Origem=substr($_SERVER['REMOTE_ADDR'],0,strrpos($_SERVER['REMOTE_ADDR'],'.'));
      if (strpos(' '.$mSenha[C007_IP_Acesso],$IP_Origem)<=0) 
      {
          echo "<script>
                  alert('Desculpe, o acesso foi negado para este local');
                  parent.document.getElementById('l').focus();
                </script>";
          exit;
      }
   }

   if (!empty($mSenha[C007_Cargo])) {
      $mSenha[C007_Cargo]='  -   (  '.$mSenha[C007_Cargo].'  )';
   }

   // Grava Estilo
   mysql_query("update C007 set C007_Estilo='estilo1' where C007_Login='$l' and (C007_Estilo='' or C007_Estilo is null)");

   // verifica se a senha esta vencida 30 DIAS
   $Sql=mysql_query("select * from C007 where C007_Login='$l'
                                                 and C007_Ativo='S'
                                                 and C007_Data_Alteracao_Senha<=date_sub(current_date(), interval 30 day)");
   echo mysql_error();
   if (mysql_num_rows($Sql)>999) {
//parent.document.getElementById('formulario_login').action='../config/alterar_senha.php3?Banco_Dados=$Banco_Dados&l=$l&e=$e';
        echo "<script>
                 parent.document.getElementById('formulario_login').target='';
                 parent.document.getElementById('formulario_login').action='config/alterar_senha.php3';
                 parent.document.getElementById('formulario_login').submit();
              </script>";
              exit;
     } else {

          If (empty($mC004[C004_Caminho_Sistema])) {
             // Pasta da empresa
             msg_alerta('Pasta da empresa não definida');
             exit;
          }

          // Atribui a Variavel de Sessao o Nome e o Codigo do Usuario
          @session_start();
          $_SESSION[Modulo_NFe]='N';
          $_SESSION['Usuario_Atual']="$mSenha[C007_Id]";
          $_SESSION['Nome_Usuario']="$mSenha[C007_Nome]";
          $_SESSION['Cargo_Usuario']="$mSenha[C007_Cargo]";
          $_SESSION['Perfil_Usuario']="$mSenha[C021_Id]";
          $_SESSION['Empresa_Atual']="$e";
          $_SESSION['Empresa_Logada']="$e";
          $_SESSION['Nome_Empresa_Atual']="$mC004[C004_Nome_Empresa]";
          $_SESSION['sb']="$sb";
          $_SESSION['Banco_Dados']="$Banco_Dados";
          $_SESSION['nWidth_Padrao']="$nWidth_Padrao";
          $_SESSION['nHeight_Padrao']="$nHeight_Padrao";
          $_SESSION['Pasta_Empresa']="$mC004[C004_Caminho_Sistema]"; //-- pasta onde estao localizados os dados da empresa
          $_SESSION['cBd_Configuracao']="$cBd_Configuracao";
          $_SESSION['Usuario_Suporte']="$mSenha[C007_Usuario_Suporte]";
          $_SESSION[Mensagem_Liberacao]='S';

          // -> Registra um historico de Enderecos
          $_SESSION['URL_Historico']="";

          // -> Registra um historico de Tipos de Telas
          $_SESSION['URL_Tipo_Historico']="";

          // -> Registra um historico de nomes lógicos das páginas acessadas
          $_SESSION['URL_Descricao_Historico']="";

          // -> Registra um historico com as Entidades Principais
          $_SESSION['URL_Entidade_Principal']="";

          //
          // Guarda o numero pagina acessada no monta_consultas_fme
          // Utiliza como chave da matriz a Entidade+cTela+Entidade_Principal+Id_Entidade_Principal
          //
          $_SESSION['Pagina_Monta_Consulta']="";
          $_SESSION['Chave_Pagina_Monta_Consulta']="";

          // Abre o sistema em uma Nova Pagina
          if (empty($w))
          {
              echo "<script>
                      parent.document.getElementById('l').value='';
                      parent.document.getElementById('s').value='';
                      Abre()
                    </script>";
          }
          else
          {
              echo "<script>
                      Abre()
                    </script>";
          }
     }

?> 
</body>
</HTML>
