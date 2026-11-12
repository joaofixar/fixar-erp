<?php

  @session_destroy();
  @session_start();

  include 'bibliotecas/confUsuario.php';

  include 'bibliotecas/conexaoBanco.php';

?>
 
<body bgcolor='#969593' topmargin='50' leftmargin='0' onLoad=poe_focus();>
<script language='javascript'>
   var nHeight_Padrao=screen.availHeight;
   var nWidth_Padrao=screen.availWidth;


   function enviar_senha() {
      var Login=document.getElementById('l').value;
      var Empresa=document.getElementById('e').value;
      document.getElementById('fme_geral').style.display='inline';
      document.getElementById('fme_geral').src='./config/enviar_senha_email.php3?l='+Login+'&e='+Empresa;
   }

   function poe_focus() {
      document.getElementById('nWidth_Padrao').value=nWidth_Padrao;
      document.getElementById('nHeight_Padrao').value=nHeight_Padrao;
      document.getElementById('l').focus();
   }

   // Maximiza a Tela
   maximiza();

</script>

<form name='formulario_login' id='formulario_login' method='post' action='/hardness3/login.php'>
 <input type='hidden' name='nHeight_Padrao' id='nHeight_Padrao' value=''>
 <input type='hidden' name='nWidth_Padrao' id='nWidth_Padrao' value=''>

<table border="0" width=550 height=300 cellpadding="0" cellspacing="0" valign=center align=center style='font-family:arial;font-size:12px;'>
	<tbody>
		<tr>

          <td align=center bgcolor=e5e5e5>
            <table cellspacing=0 cellpadding=3 border=0  height=250>
	        	<tr>
                  <td colspan=2>
                    <table align=center width=100% border=0 cellspacing=0 cellpadding=0>
                    	<tbody>
                    		<tr>
                                <td>
                                    <table border="0" cellpadding="0" cellspacing="0">
                                    	<tbody>
                                    		<tr height=24>
                                                <td align=center width="100%"><font class='fonte_titulo'>Acesso ao Sistema</td>
                                    		</tr>
                                    	</tbody>
                                    </table>
                                </td>
                    		</tr>
                    	</tbody>
                    </table>
                    
                    <table align=center width=100%  border=0 cellspacing=0 cellpadding=0>
                    	<tbody>
                    		<tr >
                                <td colspan=2 >
                                    <table border="0" width=100% cellpadding="0" cellspacing="0">
                                    	<tbody>
                                    		<tr valign=top height=96>
                                                <td width=100% ><?php
                                                  $cC004=mysql_query("select * from $cBd_Configuracao.C004 left join D020 on D020_Id=C004_D020_Id WHERE C004_Flag_Ativo != 'N' order by C004_Id");
                                                   echo "<select name='e' id='e' class='fonte_empresas' onKeyPress=\"Skip_Campo('1',event);\" size=6 border=0 style='background:transparent;border:1px solid gray;width:100%;height:100%;'>";
                                                   $i=0;
                                                   while ($mC004=mysql_fetch_array($cC004)) {
                                                         $i++;
                                                         echo "<option value='$mC004[C004_Id]'";
                                                         if ((empty($Id_Empresa)) and ($i==1)) {
                                                            echo " SELECTED";
                                                         } else if ($mC004[C004_Id]==$Id_Empresa) {
                                                            echo " SELECTED";
                                                         }
                                                         echo ">$mC004[C004_Nome_Empresa] ($mC004[D020_Nome_Cidade])</option>";
                                                   }
                                                   echo "</select>";
                                                ?></td>
                                    		</tr>
                                    	</tbody>
                                    </table>
                                </td>
                    		</tr>
                    	</tbody>
                    </table>
                    
                    <table align=center width=100% background='./imagens/acesso_login/acesso-imga_1_2.gif' border=0 cellspacing=0 cellpadding=0>
                    	<tbody>
                    		<tr>
                                <td>
                                    <table border="0" width=100% cellpadding="0" cellspacing="0">
                                    	<tbody>

                                    	</tbody>
                                    </table>
                                </td>
                    		</tr>
                    	</tbody>
                    </table>


                   </td>
    	        </tr>
        		<tr>
                    <td>
                        <table align=center width=330 border=0 cellspacing=0 cellpadding=0>
                        	<tbody>
                        		<tr>
                                    <td>
                                        <table border="0" width=100% cellpadding="0" cellspacing="0">
                                        	<tbody>
                                        		<tr height=20>
                                                    <td><font class=fonte_label>Login</td>
                                                    <td><font class=fonte_label>Senha</td>
                                        		</tr>
                                        		<tr>
                                                    <td height=30 ><input type='text' style='background:transparent;border:1px solid gray;padding:3px' value='' name='l' id='l' class=campo maxlength=20 autocomplete='off'></td>
                                                    <td height=30 width=151 ><input type='password' style='background:transparent;border:1px solid gray;padding:3px' value='' name='s' id='s' class=campo maxlength=10 autocomplete='off'></td>
                                        		</tr>
                                        	</tbody>
                                        </table>
                                    </td>
                        		</tr>
                        	</tbody>
                        </table>
                    </td>
                    <td><button type='submit'  title='Efetuar Login' style='background:transparent;border:0;'><font style='color:green;font-size:14px'>Entrar</font></button></td>
        		</tr>
             </table>
          </td>

    	</tr>

		
	</tbody>
</table>

</form>

<iframe id='fme_geral' name='fme_geral' style='display:none;width:1;height:1;'>
</body>
</HTML>
