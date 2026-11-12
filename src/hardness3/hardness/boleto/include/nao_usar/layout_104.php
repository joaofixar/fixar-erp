<?php
namespace hardness;

// +----------------------------------------------------------------------+
// | BoletoPhp - Versï¿½o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo estï¿½ disponï¿½vel sob a Licenï¿½a GPL disponï¿½vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Vocï¿½ deve ter recebido uma cï¿½pia da GNU Public License junto com     |
// | esse pacote; se nï¿½o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaboraï¿½ï¿½es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Joï¿½o Prado Maia e Pablo Martins F. Costa				        |
// | 														                                   			  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenaï¿½ï¿½o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+

// SIGCB - SEM REGISTRO
// SICOB - COM REGISTRO

if (empty($this->codigo_barras))
{
   echo "NAO FOI GERADO O CODIGO DE BARRAS";
   exit;
}

$codigobanco = "104";
$codigo_banco_com_dv = b104_geraCodigoBanco($codigobanco);
$nummoeda = "9";

$dvCedente = b104_digitoVerificador_cedente($this->numero_convenio);

/*
// Composiï¿½ï¿½o Nosso Numero - CEF SIGCB
$dadosboleto["nosso_numero1"] = "000"; // tamanho 3
$dadosboleto["nosso_numero_const1"] = "2"; //constanto 1 , 1=registrada , 2=sem registro
$dadosboleto["nosso_numero2"] = "000"; // tamanho 3
$dadosboleto["nosso_numero_const2"] = "4"; //constanto 2 , 4=emitido pelo proprio cliente
$dadosboleto["nosso_numero3"] = STR_PAD(substr($this->nosso_numero,0,9),9,'0',STR_PAD_LEFT); // tamanho 9

$b104_fator_vencimento = b104_fator_vencimento($this->data_vencimento);

//valor tem 10 digitos, sem virgula
$valor = b104_formata_numero(str_replace('.','',$this->valor_duplicata),10,0,"valor");
//agencia ï¿½ 4 digitos
$agencia = b104_formata_numero($this->numero_agencia,4,0);
//conta ï¿½ 5 digitos
$conta = b104_formata_numero($this->numero_conta,5,0);
//dv da conta
$conta_dv = b104_formata_numero($this->getDVConta(),1,0);

//conta cedente (sem dv) com 6 digitos
$conta_cedente = b104_formata_numero($this->numero_convenio,6,0);
//dv da conta cedente
$conta_cedente_dv = b104_digitoVerificador_cedente($conta_cedente);

//campo livre (sem dv) ï¿½ 24 digitos
$campo_livre = $conta_cedente . $conta_cedente_dv . b104_formata_numero($dadosboleto["nosso_numero1"],3,0) . b104_formata_numero($dadosboleto["nosso_numero_const1"],1,0) . b104_formata_numero($dadosboleto["nosso_numero2"],3,0) . b104_formata_numero($dadosboleto["nosso_numero_const2"],1,0) . b104_formata_numero($dadosboleto["nosso_numero3"],9,0);
//dv do campo livre
$dv_campo_livre = b104_digitoVerificador_nossonumero($campo_livre);
$campo_livre_com_dv ="$campo_livre$dv_campo_livre";

//nosso nï¿½mero (sem dv) ï¿½ 17 digitos
$nnum = b104_formata_numero($dadosboleto["nosso_numero_const1"],1,0).b104_formata_numero($dadosboleto["nosso_numero_const2"],1,0).b104_formata_numero($dadosboleto["nosso_numero1"],3,0).b104_formata_numero($dadosboleto["nosso_numero2"],3,0).b104_formata_numero($dadosboleto["nosso_numero3"],9,0);
//nosso nï¿½mero completo (com dv) com 18 digitos
$nossonumero = $nnum . b104_digitoVerificador_nossonumero($nnum);

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = b104_digitoVerificador_barra("$codigobanco$nummoeda$b104_fator_vencimento$valor$campo_livre_com_dv", 9, 0);
// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$b104_fator_vencimento$valor$campo_livre_com_dv";

echo $linha;

$dadosboleto["codigo_barras"] = $linha;
//echo $dadosboleto["codigo_barras"];
$dadosboleto["linha_digitavel"] = b104_monta_linha_digitavel($linha);
$dadosboleto["nosso_numero"]=$nossonumero;

mysqli_query("update T117 set T117_Nosso_Numero='".$this->nosso_numero.$conta_cedente_dv."0',T117_Codigo_Barras='".$dadosboleto["linha_digitavel"]."' where T117_Id='$nT117_Id'");
*/
?>
<table cellspacing=0 cellpadding=0 width=666 border=0>
                     <tr>
                        <td valign=top width=666 height=1 style="text-align: right"><b class=cp>Recibo do Sacado</b><img height=1 src=imagens/2.png width=666 border=0></td>
                     </tr>
</table>                     
<!--<table cellspacing=0 cellpadding=0 width=666 border=0>
                     <tr>
                         <td class=cp width=150>   <span class="campo"><IMG src="imagens/logocaixa.jpg" width="150" height="40" border=0></span></td>
                         <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                         <td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $codigo_banco_com_dv; ?></font></div></td>
                         <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                         <td class=ld align=right width=453 valign=bottom><span class=ld><span style='font-size:16px'><?php echo $this->linha_digitavel?></span></span></td>
                     </tr>
     <tbody>
                     <tr>
                         <td colspan=5><img height=2 src=imagens/2.png width=666 border=0></td>
                     </tr>
     </tbody>
</table>
-->
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=13>
                  <img height=13 src=imagens/1.png width=1 border=0>
                  </td>
                  <td class=ct valign=top width=298 height=13>Beneficiário</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=126 height=13>Agência/Código do Beneficiário</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=34 height=13>Espécie</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=53 height=13>Quantidade</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=120 height=13>Nosso número</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=298 height=12><span class="campo"><?php echo $this->nome_cedente; ?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=126 height=12>
                    <span class="campo">
                      <?php
                        echo $this->numero_agencia;
                        if(!empty($this->digito_verificador_agencia)){
                         echo "-".$this->digito_verificador_agencia;
                        }
                        echo  " / ". $this->numero_conta ."-". $this->digito_verificador_conta;
                      ?>
                    </span>
                  </td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=34 height=12><span class="campo"><?php echo $this->dados_especie?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=53 height=12><span class="campo"><?php echo $this->dados_quantidade?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=120 height=12><span class="campo"><?php echo $this->nosso_numero?></span></td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=298 height=12><span class="campo"><?php echo $this->cpf_cnpj_cedente; ?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=126 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=34 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=53 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=120 height=12><span class="campo">&nbsp;</span></td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=298 height=12><span class="campo"><?php echo $this->endereco_cedente; ?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=126 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=34 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=53 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=120 height=12><span class="campo">&nbsp;</span></td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=298 height=12><span class="campo"><?php echo $this->getEndereco2Cedente() ?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=126 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=34 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=53 height=12><span class="campo">&nbsp;</span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=120 height=12><span class="campo">&nbsp;</span></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=298 height=1><img height=1 src=imagens/2.png width=298 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=126 height=1><img height=1 src=imagens/2.png width=126 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=34 height=1><img height=1 src=imagens/2.png width=34 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=53 height=1><img height=1 src=imagens/2.png width=53 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=120 height=1><img height=1 src=imagens/2.png width=120 border=0></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top colspan=3 height=13>Número do documento</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=132 height=13>CPF/CNPJ</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=134 height=13>Vencimento</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>Valor documento</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top colspan=3 height=12> <span class="campo"> <?php echo $this->numero_documento; ?> </span></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top width=132 height=12> <span class="campo"> <?php echo $this->cpf_cnpj_cedente; ?> </span></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top width=134 height=12> <span class="campo"> <?php echo $this->data_vencimento; ?> </span></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo number_format($this->valor_duplicata,2,',','.'); ?></span></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=72 height=1><img height=1 src=imagens/2.png width=72 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=132 height=1><img height=1 src=imagens/2.png width=132 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=134 height=1><img height=1 src=imagens/2.png width=134 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=113 height=13>(-)Desconto / Abatimentos</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=112 height=13>(-)Outras deduções</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=113 height=13>(+)Mora / Multa</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=113 height=13>(+)Outros acréscimos</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>(=)Valor cobrado</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=113 height=12></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=112 height=12></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=113 height=12></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=113 height=12></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=112 height=1><img height=1 src=imagens/2.png width=112 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=659 height=13>Sacado</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top width=659 height=12> <span class="campo"> <?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado?> </span></td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top width=659 height=12> <span class="campo"> <?php echo $this->endereco_sacado;?> </span></td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top width=659 height=12> <span class="campo"> <?php echo $this->getEndereco2Sacado();?> </span></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=659 height=1><img height=1 src=imagens/2.png width=659 border=0></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct  width=7 height=12></td><td class=ct  width=564 >Demonstrativo</td>
                         <td class=ct  width=7 height=12></td><td class=ct  width=88 >Autenticação mecânica</td>
                     </tr>
                     <tr>
                         <td  width=7 ></td>
                         <td class=cp width=564 >
                             <span class="campo">
                             <?php

                                  $T002=mysqli_query("select T002_Numero_Duplicata,T002_Data_Vencimento,T002_Valor_Total from T002 where T002_T117_Id='$nT117_Id'");
                                  echo "<table border=0 cellpadding=2 width=300 cellspacing=1 bgcolor=black>";
                                   echo "<tr bgcolor=white>";
                                    echo "<td align=center>";
                                     echo "<font class=fonte>Número</font>";
                                    echo "</td>";
                                    echo "<td align=center>";
                                     echo "<font class=fonte>Vencimento</font>";
                                    echo "</td>";
                                    echo "<td align=right>";
                                     echo "<font class=fonte>Valor</font>";
                                    echo "</td>";
                                   echo "</tr>";
                                  while ($mT002=mysql_fetch_array($T002)) {
                                     echo "<tr bgcolor=white>";
                                      echo "<td align=center>";
                                       echo "<font class=fonte>$mT002[T002_Numero_Duplicata]</font>";
                                      echo "</td>";
                                      echo "<td align=center>";
                                       echo "<font class=fonte>".troca_data_dma($mT002['T002_Data_Vencimento'])."</font>";
                                      echo "</td>";
                                      echo "<td align=right>";
                                       echo "<font class=fonte>R$ $mT002[T002_Valor_Total]</font>";
                                      echo "</td>";
                                     echo "</tr>";
                                  }

                                  echo "</table>";

                                  ?>
                              </span>
                         </td>
                         <td  width=7 ></td>
                         <td  width=88 ></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
       <tbody>
                     <tr>
                         <td width=7></td>
                         <td  width=500 class=cp> <br><br><br> </td>
                         <td width=159></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
                     <tr>
                         <td class=ct width=666></td>
                     </tr>
       <tbody>
                     <tr>
                         <td class=ct width=666><div align=right>Corte na linha pontilhada</div></td>
                     </tr>
                     <tr>
                         <td class=ct width=666><img height=1 src=imagens/6.png width=665 border=0></td>
                     </tr>
       </tbody>
</table>
<br>
<table cellspacing=0 cellpadding=0 width=666 border=0>
                     <tr>
                         <td class=cp width=150> <span class="campo"><IMG src="imagens/logocaixa.jpg" width="150" height="40" border=0></span></td>
                         <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                         <td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $codigo_banco_com_dv;?></font></div></td>
                         <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                         <td class=ld align=right width=453 valign=bottom><span class=ld><span style='font-size:16px'> <?php echo $this->linha_digitavel?> </span></span></td>
                     </tr>
       <tbody>
                     <tr>
                         <td colspan=5><img height=2 src=imagens/2.png width=666 border=0></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=472 height=13>Local de pagamento</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>Vencimento</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top width=472 height=12>PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE</td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo $this->data_vencimento; ?> </span></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=472 height=1><img height=1 src=imagens/2.png width=472 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                    <tr>
                        <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                        <td class=ct valign=top width=472 height=13>Beneficiário</td>
                        <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                        <td class=ct valign=top width=180 height=13>Agência/Código Beneficiário</td>
                    </tr>
                    <tr>
                        <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top width=472 height=12><span class="campo"><?php echo $this->nome_cedente;?> - <?php echo $this->cpf_cnpj_cedente; ?></span></td>
                        <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top align=right width=180 height=12>
                          <span class="campo">
                            <?php
                              echo $this->numero_agencia;
                              if(!empty($this->digito_verificador_agencia)){
                               echo "-".$this->digito_verificador_agencia;
                              }
                              echo  " / ". $this->numero_conta ."-". $this->digito_verificador_conta;
                            ?>
                          </span>
                        </td>
                    </tr>
                        <tr>
                        <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top width=298 height=12><span class="campo"><?php echo $this->endereco_cedente; ?></span></td>
                        <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top align=right width=180 height=12><span class="campo">&nbsp;</span></td>
                    </tr>
                        <tr>
                        <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top width=298 height=12><span class="campo"><?php echo $this->getEndereco2Cedente() ?></span></td>
                        <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top align=right width=180 height=12><span class="campo">&nbsp;</span></td>
                    </tr>
                        <tr>
                        <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                        <td valign=top width=472 height=1><img height=1 src=imagens/2.png width=472 border=0></td>
                        <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                        <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                    </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=113 height=13>Data do documento</td>
                         <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=133 height=13>N<u>o</u>documento</td>
                         <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=62 height=13>Espécie doc.</td>
                         <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=34 height=13>Aceite</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=102 height=13>Data processamento</td>
                         <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>Nosso número</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=113 height=12><div align=left> <span class="campo"> <?php echo $this->getDataEmissaoBoleto(); ?> </span></div></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top width=133 height=12> <span class="campo"> <?php echo $this->numero_documento; ?> </span></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=62 height=12><div align=left><span class="campo"> DM </span> </div></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=34 height=12><div align=left><span class="campo"> <?php echo $this->dados_aceite;?> </span> </div></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=102 height=12><div align=left> <span class="campo"> <?php echo $this->data_processamento; ?> </span></div></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo $this->nosso_numero?> </span></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=133 height=1><img height=1 src=imagens/2.png width=133 border=0></td>
                         <td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=62 height=1><img height=1 src=imagens/2.png width=62 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=34 height=1><img height=1 src=imagens/2.png width=34 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=102 height=1><img height=1 src=imagens/2.png width=102 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top COLSPAN="3" height=13>Uso do banco</td>
                         <td class=ct valign=top height=13 width=7> <img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=83 height=13>Carteira</td>
                         <td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=43 height=13>Moeda</td>
                         <td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=103 height=13>Quantidade</td>
                         <td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=102 height=13>Valor Documento</td>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>(=)Valor documento</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td valign=top class=cp height=12 COLSPAN="3">
                             <div align=left>
                                   <?php
                                        if (!empty($this->dados_uso_banco))
                                        {
                                           echo "<span class='cp'>".$this->dados_uso_banco;
                                        }
                                        ?>
                             </div>
                         </td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=83> 
                            <div align=left> 
                                <span class="campo"> 
                                    <?php
                                        if($this->getTipoCobrancaBanco()=="1"){
                                            echo "RG";
                                        }else if($this->getTipoCobrancaBanco()=="2"){
                                            echo "SR";
                                        }else{
                                            echo $this->numero_carteira; 
                                        }
                                    ?>   
                                </span>
                            </div>
                        </td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=43><div align=left><span class="campo"> <?php echo $this->dados_especie; ?> </span> </div></td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=103><span class="campo"> <?php echo $this->dados_quantidade; ?> </span> </td>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top  width=102>
                              <span class="campo">
                               <?php echo $this->dados_valor_unitario; ?>
                              </span>
                         </td>
                         <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12>
                             <span class="campo">
                               <?php echo number_format($this->valor_duplicata,2,',','.'); ?>
                             </span>
                         </td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=75 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=31 height=1><img height=1 src=imagens/2.png width=31 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=83 height=1><img height=1 src=imagens/2.png width=83 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=43 height=1><img height=1 src=imagens/2.png width=43 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=103 height=1><img height=1 src=imagens/2.png width=103 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=102 height=1><img height=1 src=imagens/2.png width=102 border=0></td>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
       <tbody>
                     <tr>
                         <td align=right width=10>
<table cellspacing=0 cellpadding=0 border=0 align=left>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                          <td valign=top width=468 rowspan=5><font class=ct>Instruções (Texto de responsabilidade do cedente)</font><br><br>
                              <span class=cp> <FONT class=campo>
                               <?php echo $this->instrucoes1; ?><br>
                               <?php echo $this->instrucoes2; ?><br>
                               <?php echo $this->instrucoes3; ?><br>
                               <?php echo $this->instrucoes4; ?></FONT><br><br>
                              </span>
                          </td>
                          <td align=right width=188>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>(-)Desconto / Abatimentos</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                     </tr>
                     <tr>
                         <td align=right width=10>
<table cellspacing=0 cellpadding=0 border=0 align=left>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                          <td align=right width=188>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>(-)Outras deduções</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                     </tr>
                     <tr>
                         <td align=right width=10>
<table cellspacing=0 cellpadding=0 border=0 align=left>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                          <td align=right width=188>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>(+)Mora / Multa</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                     </tr>
                     <tr>
                         <td align=right width=10>
<table cellspacing=0 cellpadding=0 border=0 align=left>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                          <td align=right width=188>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
                     <tr>
                         <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                         <td class=ct valign=top width=180 height=13>(+)Outros acréscimos</td>
                     </tr>
                     <tr>
                         <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                         <td class=cp valign=top align=right width=180 height=12></td>
                     </tr>
                     <tr>
                         <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                         <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
                     </tr>
       </tbody>
</table>
                          </td>
                     </tr>
                     <tr>
                          <td align=right width=10>
<table cellspacing=0 cellpadding=0 border=0 align=left>
       <tbody>
              <tr>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                          <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
       </tbody>
</table>
                          </td>
                          <td align=right width=188>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=ct valign=top width=180 height=13>(=)Valor cobrado</td>
              </tr>
              <tr>
                          <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                          <td class=cp valign=top align=right width=180 height=12></td>
              </tr>
       </tbody>
</table>
                          </td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
       <tbody>
              <tr>
                          <td valign=top width=666 height=1><img height=1 src=imagens/2.png width=666 border=0></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=ct valign=top width=472 height=13>Sacado</td>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=ct valign=top width=180 height=13></td>
              </tr>
              <tr>
                          <td class=cp valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=cp valign=top width=472 height=13><span class="campo"><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?></span></td>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=ct valign=top width=180 height=13></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                          <td class=cp valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=cp valign=top width=472 height=13><span class="campo"> <?php echo $this->endereco_sacado; ?></span></td>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=ct valign=top width=180 height=13></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=cp valign=top width=472 height=13><span class="campo"><?php echo $this->getEndereco2Sacado(); ?> </span></td>
                          <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                          <td class=ct valign=top width=180 height=13>Cód.baixa <?php
                                                if (!empty($this->codigo_baixa))
                                                {
                                                   echo "&nbsp;&nbsp;&nbsp;<span class='cp'>".$this->codigo_baixa;
                                                }
                                           ?>
                          </td>
              </tr>
              <tr>
                          <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                          <td valign=top width=472 height=1><img height=1 src=imagens/2.png width=472 border=0></td>
                          <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                          <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
              </tr>
       </tbody>
</table>
<TABLE cellSpacing=0 cellPadding=0 border=0 width=666>
       <TBODY>
              <TR>
                          <TD class=ct  width=7 height=12></TD>
                          <TD class=ct  width=409 >Sacador/Avalista</TD>
                          <TD class=ct  width=250 ><div align=right>Autenticação mecânica - <b class=cp>Ficha de Compensação</b></div></TD>
              </TR>
              <TR>
                          <TD class=ct  colspan=3 ></TD>
              </tr>
       </tbody>
</table>
<TABLE cellSpacing=0 cellPadding=0 width=666 border=0 style="padding-left: 16px">
       <TBODY>
              <TR>
                          <TD vAlign=bottom align=left height=50><?php fbarcode($this->codigo_barras); ?> </TD>
              </tr>
       </tbody>
</table>
<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
              <TR>
                          <TD class=ct width=666></TD>
              </TR>
       <TBODY>
              <TR>
                          <TD class=ct width=666><div align=right>Corte na linha pontilhada</div></TD>
              </TR>
              <TR>
                          <TD class=ct width=666><img height=1 src=imagens/6.png width=665 border=0></TD>
              </tr>
       </tbody>
</table>
</BODY></HTML>
<?php

unset($codigobanco);
unset($codigo_banco_com_dv);
unset($nummoeda);
unset($fator_vencimento);
unset($valor);
unset($agencia);
unset($dv_agencia);
unset($conta);;
unset($conta_dv);
unset($carteira);
unset($nnum);
unset($codigo_barras);
unset($dv);
unset($linha);
unset($nossonumero);
unset($agencia_codigo);
unset($dadosboleto);
?>
