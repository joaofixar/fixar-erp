<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Ita�: Glauber Portella		                    |
// +----------------------------------------------------------------------+


$codigobanco = "341";
$codigo_banco_com_dv = b341_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = b341_fator_vencimento($this->data_vencimento);

//valor tem 10 digitos, sem virgula
$valor = b341_formata_numero($this->valor_duplicata,10,0,"valor");
//agencia � 4 digitos
$agencia = b341_formata_numero($this->numero_agencia,4,0);
//conta � 5 digitos + 1 do dv
$conta = b341_formata_numero($this->numero_conta,5,0);
$conta_dv = b341_formata_numero($this->getDVConta(),1,0);
//carteira 175
$carteira = $this->numero_carteira;
//nosso_numero no maximo 8 digitos
$nnum = b341_formata_numero($this->nosso_numero,8,0);

$codigo_barras = $codigobanco.$nummoeda.$fator_vencimento.$valor.$carteira.$nnum.b341_modulo_10($agencia.$conta.$carteira.$nnum).$agencia.$conta.b341_modulo_10($agencia.$conta).'000';

mysql_query("update T117 set T117_Codigo_Barras='$codigo_barras' where T117_Id='$nT117_Id'");

// 43 numeros para o calculo do digito verificador
$dv = b341_digitoVerificador_barra($codigo_barras);
// Numero para o codigo de barras com 44 digitos
$linha = substr($codigo_barras,0,4).$dv.substr($codigo_barras,4,43);

$nossonumero = $carteira.'/'.$nnum.'-'.b341_modulo_10($agencia.$conta.$carteira.$nnum);
$agencia_codigo = $agencia." / ". $conta."-".b341_modulo_10($agencia.$conta);

$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = b341_monta_linha_digitavel($linha); // verificar
$dadosboleto["agencia_codigo"] = $agencia_codigo ;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
?>

<table width=666 cellspacing=5 cellpadding=0 border=0>
       <tr>
           <td width=41></TD>
       </tr>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
               <tr>
                   <td class=cp width=150> <span class="campo"><IMG src="imagens/logoitau.jpg" width="150" height="40" border=0></span></td>
                   <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                   <td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $dadosboleto["codigo_banco_com_dv"]?></font></div></td>
                   <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                   <td class=ld align=right width=453 valign=bottom><span class=ld><span style='font-size:15px'><?php echo $dadosboleto["linha_digitavel"]?></span></span></td>
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
                  <td class=ct valign=top width=298 height=13>Cedente</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=126 height=13>Ag�ncia/C�digo do Cedente</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=34 height=13>Esp�cie</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=53 height=13>Quantidade</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=120 height=13>Nosso n�mero</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=298 height=12> <span class="campo"><?php echo $this->nome_cedente; ?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=126 height=12><span class="campo"> <?php echo $this->numero_agencia." / ".$this->numero_conta."-".$this->digito_verificador_conta; ?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=34 height=12><span class="campo"> <?php echo $this->dados_especie; ?> </span> </td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=53 height=12><span class="campo"> <?php echo $this->dados_quantidade; ?> </span> </td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=120 height=12> <span class="campo"> <?php echo $dadosboleto["nosso_numero"]; ?> </span></td>
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
                  <td class=ct valign=top colspan=3 height=13>N�mero do documento</td>
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
                  <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo number_format($this->valor_duplicata,2,',','.'); ?> </span></td>
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
                  <td class=ct valign=top width=112 height=13>(-)Outras dedu��es</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=113 height=13>(+)Mora / Multa</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=113 height=13>(+)Outros acr�scimos</td>
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
                  <td class=cp valign=top width=659 height=12> <span class="campo"> <?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado; ?> </span></td>
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
                  <td class=ct  width=7 height=12></td>
                  <td class=ct  width=564 >Demonstrativo</td>
                  <td class=ct  width=7 height=12></td>
                  <td class=ct  width=88 >Autentica��omec�nica</td>
              </tr>
              <tr>
                  <td width=7></td>
                  <td class=cp width=564><span class="campo">
                          <?php

                          $T002=mysql_query("select T002_Numero_Duplicata,T002_Data_Vencimento,T002_Valor_Total from T002 where T002_T117_Id='$nT117_Id'");
                          echo "<table border=0 width=300 cellspacing=1 bgcolor=black>";
                           echo "<tr bgcolor=white>";
                            echo "<td align=center>";
                             echo "<font class=fonte>N�mero</font>";
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
                               echo "<font class=fonte>".troca_data_dma($mT002[T002_Data_Vencimento])."</font>";
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
                  <td width=7 ></td>
                  <td  width=88 ></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
       <tbody>
              <tr>
                  <td width=7></td>
                  <td  width=500 class=cp><br><br><br></td>
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
                  <td class=cp width=150> <span class="campo"><IMG src="imagens/logoitau.jpg" width="150" height="40" border=0></span></td>
                  <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                  <td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $dadosboleto["codigo_banco_com_dv"]?></font></div></td>
                  <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                  <td class=ld align=right width=453 valign=bottom><span class=ld><span style='font-size:15px'><?php echo $dadosboleto["linha_digitavel"]?></span></span></td>
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
                  <td class=cp valign=top width=472 height=12>Pag�vel em qualquer Banco at� o vencimento</td>
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
                  <td class=ct valign=top width=472 height=13>Cedente</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=180 height=13>Ag�ncia/C�digo cedente</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=472 height=12> <span class="campo"> <?php echo $this->nome_cedente; ?> </span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo $this->numero_agencia." / ".$this->numero_conta."-".$this->digito_verificador_conta; ?> </span></td>
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
                  <td class=ct valign=top width=153 height=13>N<u>o</u>documento</td>
                  <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=62 height=13>Esp�cie doc.</td>
                  <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=34 height=13>Aceite</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=82 height=13>Data processamento</td>
                  <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=180 height=13>Nosso n�mero</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=113 height=12><div align=left>  <span class="campo"> <?php echo $this->getDataEmissaoBoleto(); ?> </span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=153 height=12> <span class="campo"> <?php echo $this->numero_documento; ?> </span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=62 height=12><div align=left><span class="campo"> <?php echo $this->dados_especie_doc; ?> </span> </div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=34 height=12><div align=left><span class="campo"> <?php echo $this->dados_aceite; ?> </span> </div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=82 height=12><div align=left> <span class="campo"> <?php echo $this->data_processamento; ?> </span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo $dadosboleto["nosso_numero"]; ?> </span></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=153 height=1><img height=1 src=imagens/2.png width=153 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=62 height=1><img height=1 src=imagens/2.png width=62 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=34 height=1><img height=1 src=imagens/2.png width=34 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=82 height=1><img height=1 src=imagens/2.png width=82 border=0></td>
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
                  <td class=ct valign=top width=53 height=13>Esp�cie</td>
                  <td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=123 height=13>Quantidade</td>
                  <td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=72 height=13>Valor Documento</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=180 height=13>(=)Valor documento</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td valign=top class=cp height=12 COLSPAN="3"><div align=left><?php echo $this->dados_uso_banco; ?></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=83><div align=left> <span class="campo"> <?php echo $this->numero_carteira ?></span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=53><div align=left><span class="campo"><?php echo $this->dados_especie; ?></span> </div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=123><span class="campo"> <?php echo $this->dados_quantidade; ?> </span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=72> <span class="campo"> <?php echo $this->dados_valor_unitario?> </span></td>
                  <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo number_format($this->valor_duplicata,2,',','.'); ?> </span></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=75 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=31 height=1><img height=1 src=imagens/2.png width=31 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=83 height=1><img height=1 src=imagens/2.png width=83 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=53 height=1><img height=1 src=imagens/2.png width=53 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=123 height=1><img height=1 src=imagens/2.png width=123 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=72 height=1><img height=1 src=imagens/2.png width=72 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=180 height=1><img height=1 src=imagens/2.png width=180 border=0></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 width=666 border=0>
       <tbody>
              <tr>
                  <td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left>
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
                   <td valign=top width=468 rowspan=5><font class=ct>Instru��es (Texto de responsabilidade do cedente)</font><br><br><span class=cp> <FONT class=campo>
                    <?php echo $this->instrucoes1; ?><br>
                    <?php echo $this->instrucoes2; ?><br>
                    <?php echo $this->instrucoes3; ?><br>
                    <?php echo $this->instrucoes4; ?></FONT><br><br>
                    </span>
                    </td>
                    <td align=right width=188><table cellspacing=0 cellpadding=0 border=0>
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
                    <td class=ct valign=top width=180 height=13>(-)Outras dedu��es</td>
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
                  <td class=ct valign=top width=180 height=13>(+)Outros acr�scimos</td>
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
                  <td class=cp valign=top align=right width=180 height=12>
                      <?php //echo number_format($this->getValorTotal(),2,',','.'); ?>
                  </td>
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
                  <td class=ct valign=top width=659 height=13>Sacado</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=659 height=12><span class="campo"><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;  ?></span></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=659 height=12><span class="campo"><?php echo $this->endereco_sacado; ?></span></td>
              </tr>
       </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=472 height=13><span class="campo"><?php echo $this->getEndereco2Sacado(); ?></span></td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=180 height=13>C�d.baixa</td>
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
                  <TD class=ct  width=250 ><div align=right>Autentica��o mec�nica - <b class=cp>Ficha de Compensa��o</b></div></TD>
              </TR>
              <TR>
                  <TD class=ct  colspan=3 ></TD>
              </tr>
       </tbody>
</table>
<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
       <TBODY>
              <TR>
                  <TD vAlign=bottom align=left height=50><?php fbarcode341($dadosboleto["codigo_barras"]); ?></TD>
              </tr>
       </tbody>
</table>
<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
              <TR>
                  <TD class=ct width=666></TD>
              </TR>
       <TBODY><TR><TD class=ct width=666><div align=right>Corte na linha pontilhada</div></TD>
              </TR>
              <TR>
                  <TD class=ct width=666><img height=1 src=imagens/6.png width=665 border=0></TD>
              </tr>
       </tbody>
</table>

</BODY></HTML>

<?php

//-- Zera as variaveis

unset($codigobanco);
unset($codigo_banco_com_dv);
unset($nummoeda);
unset($fator_vencimento);
unset($valor);
unset($agencia);
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
