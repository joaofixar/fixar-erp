<?php

echo "<link rel=stylesheet href='estilo.css' type='text/css'>";

$IpServidor="http://nfe.hardness.com.br";

if ($User_Suport>0 or $Usuario_Contador>0)
{
    $Top_Extra=32;
}

if (($_SESSION[Modulo_NFe]=='S') or ($mC007[C007_Flag_Mostrar_Boleto]=='S'))
{
    if ($_SESSION[Modulo_NFe]=='S')
    {
        echo "<div id='Div_Mensagem_Inicial1' style='zIndex:-1;display:inline;position:absolute;top:".(123+$Top_Extra).";left:520px;width:520;height:470;border:0px solid silver'>";
        echo "<iframe src='http://www.hardness.com.br/2010/mensagens.php?i=".md5($mEmpresa[C004_Cnpj])."&Usuario_Atual=$Usuario_Atual' FRAMEBORDER=0 style='border:0px;padding:0px;width:99%;height:98%'></iframe>";
        echo "</div>";
    }
    else
    {
        echo "<div id='Div_Mensagem_Inicial2' style='zIndex:-1;display:inline;position:absolute;top:70;left:515px;width:550;height:470'>";
        echo "<iframe src='http://www.hardness.com.br/2010/mensagens.php?i=".md5($mEmpresa[C004_Cnpj])."&Usuario_Atual=$Usuario_Atual' FRAMEBORDER=0 style='border:0px;padding:0px;width:99%;height:98%'></iframe>";
        echo "</div>";
    }
}

?>
<script>
function abre_doc(parametro,nome_janela) {
  var param='';
  param+='menubar=yes,';
  param+='toolbar=no,';
  param+='location=no,';
  param+='top=0,';
  param+='left=0,';
  param+='status=no,';
  param+='scrollbars=yes,';
  param+='resizable=yes,';
  var Total_Largura=950;
  var Total_Altura=600;
  param+='width='+Total_Largura+',';
  param+='height='+Total_Altura+'';
  new_window=window.open(parametro,nome_janela,param);
  new_window.focus();
}

</script>
