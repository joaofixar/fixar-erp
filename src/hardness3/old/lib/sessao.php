<?php
/**
* Gerencia a sess�o com o usu�rio e normaliza as variav�is recebidas (POST/GET)
*/
   
// inicia sess�o
session_start();
   
   foreach ($_SESSION as $variavel => $value) {
      $$variavel=$value;
      //echo $$variavel."=$value;<br>";
   }
   if (!empty($URL_Historico)) {
      $mURL_Historico=explode(",,",$URL_Historico);
      $mURL_Tipo_Historico=explode(",,",$URL_Tipo_Historico);
      $mURL_Descricao_Historico=explode(",,",$URL_Descricao_Historico);
      $mURL_Entidade_Principal=explode(",,",$URL_Entidade_Principal);
   }
   if (!empty($Pagina_Monta_Consulta)) {
      $mPagina_Posicao_Monta_Consulta=explode(",,",$Pagina_Monta_Consulta);
      $mChave_Pagina_Monta_Consulta=explode(",,",$Chave_Pagina_Monta_Consulta);

      foreach($mChave_Pagina_Monta_Consulta as $Chave => $Valor) {
             $mPagina_Monta_Consulta[$Valor]=$mPagina_Posicao_Monta_Consulta[$Chave];
      }
   }
   
   foreach($_REQUEST as $Variavel => $Value) {
          $$Variavel = $Value;
          //echo $Variavel." = $Value<br>";
   }

   header('Content-type: text/html; charset=iso-8859-1');

   if (strpos($_SERVER['REQUEST_URI'],"monta_exportar_excel.php3")===false && empty($Pular_Script)) {
	   echo "
	         <script>
	           function abre_sessao_expirada() {
	               parent.location.replace('../index.php3');
	           }
	         </script>
	    ";

	    if (empty($Usuario_Atual)) {
	               unset($_SESSION['Usuario_Atual']);
	               unset($_SESSION['Nome_Usuario']);
	               unset($_SESSION['Cargo_Usuario']);
	               unset($_SESSION['Empresa_Atual']);
	               unset($_SESSION['Nome_Empresa_Atual']);
	               unset($_SESSION['Banco_Dados']);
	               unset($_SESSION['nWidth_Padrao']);
	               unset($_SESSION['nHeight_Padrao']);
	               unset($_SESSION['Pasta_Empresa']); //-- pasta onde estao localizados os dados da empresa
	               unset($_SESSION['cBd_Configuracao']);
	               unset($_SESSION['Usuario_Suporte']);
	               unset($_SESSION['URL_Historico']);
	               unset($_SESSION['URL_Tipo_Historico']);
	               unset($_SESSION['URL_Descricao_Historico']);
	               unset($_SESSION['URL_Entidade_Principal']);
	               unset($_SESSION['Pagina_Monta_Consulta']);
	               unset($_SESSION['Chave_Pagina_Monta_Consulta']);

                   session_destroy(); 
	          echo "<script>
	               alert('Sessao expirada, entre com sua senha');
                   abre_sessao_expirada();
                   top.self.close();
	             </script>";
	          exit;
	    } 

	    if (!empty($Nome_Usuario)) {
	      echo "<script>
	              if (document.getElementById && document.all) {
	                 window.status='Usuario: $Usuario_Atual - $Nome_Usuario $Cargo_Usuario';
	              } else {
	                 self.status='Usuario: $Usuario_Atual - $Nome_Usuario $Cargo_Usuario';
	              }
	            </script>";
	    }
	}

      $mErroPermiteAlterarNF[267]="NF Complementar referencia uma NF-e inexistente";
      $mErroPermiteAlterarNF[269]="CNPJ Emitente da NF Complementar difere do CNPJ da NF referenciada";
      $mErroPermiteAlterarNF[210]="IE do destinatário inválida";
      $mErroPermiteAlterarNF[243]="XML de Dados Mal Formado";
?>
