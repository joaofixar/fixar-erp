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
// | PHPBoleto de Joï¿½o Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenaï¿½ï¿½o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Unibanco: Elizeu Alcantara                    |
// +----------------------------------------------------------------------+

if (empty($this->codigo_barras))
{
   echo "NAO FOI GERADO O CODIGO DE BARRAS";
   exit;
}

$codigobanco = "409";
$codigo_banco_com_dv = b409_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$cvt = "04";

$dv_agencia=$this->digito_verificador_agencia;

//agencia ï¿½ 4 digitos
$agencia = b409_formata_numero($this->numero_agencia,4,0);

//dv da conta
$conta_dv = b409_formata_numero($this->getDVConta(),1,0);

//conta ï¿½ 6 digitos + 1 do dv
$conta = b409_formata_numero($this->numero_conta,6,0);

$agencia_codigo = $agencia."-".$dv_agencia."/". $conta ."-". $conta_dv;

/*


$fator_vencimento = b409_fator_vencimento($this->data_vencimento);
$vencimento_amd = substr(str_replace("-","",troca_data_amd($this->data_vencimento)),2,6);

//valor tem 10 digitos, sem virgula
$valor = b409_formata_numero($this->valor_duplicata,10,0,"valor");

//nosso nï¿½mero (sem dv) ï¿½ 14 digitos
$nnum = b409_formata_numero(substr($this->nosso_numero,0,10),10,0);
//dv do nosso nï¿½mero
$dv_nosso_numero = b409_modulo_11($nnum);
$nossonumero_dv = $nnum.$dv_nosso_numero;
$SuperDigitoNossoNumero = b409_modulo_11('1'.$nossonumero_dv);

//codigo_cliente ï¿½ 6 digitos + 1 do dv
$codigo_cliente = b409_formata_numero($this->codigo_cliente,7,0);


//-- Tamanho 3 - Nï¿½mero de identificaï¿½ï¿½o do Unibanco: 409 (nï¿½mero FIXO)
$Codigo_Barras  = $codigobanco;

//-- Tamanho 1 - Cï¿½digo da moeda. Real (R$)=9 (nï¿½mero FIXO)
$Codigo_Barras .= $nummoeda;

//-- Tamanho 4 - fator de vencimento em 4 algarismos, conforme tabela da pï¿½gina 14
$Codigo_Barras .= str_pad(substr($fator_vencimento,0,4),4,'0',STR_PAD_LEFT);

//-- Tamanho 10 - valor do tï¿½tulo com zeros ï¿½ esquerda
$Codigo_Barras .= str_pad(substr($valor,0,10),10,'0',STR_PAD_LEFT);

//-- Tamanho 2 - Cï¿½digo para transaï¿½ï¿½o CVT =  04 (nï¿½mero FIXO) (04=5539-5)
$Codigo_Barras .= str_pad(substr($cvt,0,2),2,'0',STR_PAD_LEFT);

//-- Tamanho 6 - data de vencimento (AAMMDD)
$Codigo_Barras .= str_pad(substr($vencimento_amd,0,6),6,'0',STR_PAD_LEFT);

//-- Tamanho 5 - Cï¿½digo da agï¿½ncia + dï¿½gito verificador
$Codigo_Barras .= str_pad(substr($agencia.$dv_agencia,0,6),5,'0',STR_PAD_LEFT);

//-- "Nosso Nï¿½mero" (NNNNNNNNNND) onde D ï¿½ o dï¿½gito a ser calculado pelo Mï¿½dulo 11 (pï¿½gina 11)
$Codigo_Barras .= $nossonumero_dv;

//-- Super dï¿½gito do "Nosso Nï¿½mero" onde S ï¿½ o Super Dï¿½gito calculado pelo mï¿½dulo 11 (pï¿½gina 11) -
//-- utilizando os algarismos do "Nosso Nï¿½mero" acrescido do nï¿½mero 1 ï¿½ esquerda = 1NNNNNNNNNNDS
$Codigo_Barras .= $SuperDigitoNossoNumero;


// 43 numeros para o calculo do digito verificador
$dv = b409_digitoVerificador("$Codigo_Barras");


//-- Tamanho 3 - Nï¿½mero de identificaï¿½ï¿½o do Unibanco: 409 (nï¿½mero FIXO)
$Codigo_Barras  = $codigobanco;

//-- Tamanho 1 - Cï¿½digo da moeda. Real (R$)=9 (nï¿½mero FIXO)
$Codigo_Barras .= $nummoeda;

//-- dï¿½gito verificador do Cï¿½DIGO DE BARRAS
//-- Calculado pelo mï¿½dulo 11 (pï¿½gina 11), onde deverï¿½ ser utilizado as 43 dï¿½gitos desta seqï¿½ï¿½ncia numï¿½rica que darï¿½ origem ao Cï¿½DIGO DE BARRAS
$Codigo_Barras .= $dv;

//-- Tamanho 4 - fator de vencimento em 4 algarismos, conforme tabela da pï¿½gina 14
$Codigo_Barras .= str_pad(substr($fator_vencimento,0,4),4,'0',STR_PAD_LEFT);

//-- Tamanho 10 - valor do tï¿½tulo com zeros ï¿½ esquerda
$Codigo_Barras .= str_pad(substr($valor,0,10),10,'0',STR_PAD_LEFT);

//-- Tamanho 2 - Cï¿½digo para transaï¿½ï¿½o CVT =  04 (nï¿½mero FIXO) (04=5539-5)
$Codigo_Barras .= str_pad(substr($cvt,0,2),2,'0',STR_PAD_LEFT);

//-- Tamanho 6 - data de vencimento (AAMMDD)
$Codigo_Barras .= str_pad(substr($vencimento_amd,0,6),6,'0',STR_PAD_LEFT);

//-- Tamanho 5 - Cï¿½digo da agï¿½ncia + dï¿½gito verificador
$Codigo_Barras .= str_pad(substr($agencia.$dv_agencia,0,6),5,'0',STR_PAD_LEFT);

//-- "Nosso Nï¿½mero" (NNNNNNNNNND) onde D ï¿½ o dï¿½gito a ser calculado pelo Mï¿½dulo 11 (pï¿½gina 11)
$Codigo_Barras .= $nossonumero_dv;

//-- Super dï¿½gito do "Nosso Nï¿½mero" onde S ï¿½ o Super Dï¿½gito calculado pelo mï¿½dulo 11 (pï¿½gina 11) -
//-- utilizando os algarismos do "Nosso Nï¿½mero" acrescido do nï¿½mero 1 ï¿½ esquerda = 1NNNNNNNNNNDS
$Codigo_Barras .= $SuperDigitoNossoNumero;

//echo $Codigo_Barras;

// Numero para o codigo de barras com 44 digitos
$linha = $Codigo_Barras;

//echo "<font style='font-famly:arial;font-size:30px;'<hr>B:40999439500001556220409101904880816017784100<BR>H:$linha<BR><hr>";


$Linha_Digitavel  = $codigobanco;

$Linha_Digitavel  .= $nummoeda;

$Linha_Digitavel  .= $cvt;

$Linha_Digitavel  .= substr($codigo_cliente,0,4);

$Linha_Digitavel  .= substr($codigo_cliente,0,4);


$nossonumero = $nnum.'-'.$dv_nosso_numero."/".$SuperDigitoNossoNumero;

$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = b409_monta_linha_digitavel($linha);

$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
//echo "<BR>".$dadosboleto["linha_digitavel"];
*/

?>

<table cellspacing=0 cellpadding=0 width=666 border=0>
               <tr>
                   <td class=cp width=150><span class="campo"><IMG src="imagens/logounibanco.jpg" width="150" height="40" border=0></span></td>
                   <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                   <td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $codigo_banco_com_dv?></font></div></td>
                   <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                   <td class=ld align=right width=453 valign=bottom><span class=ld><span style='font-size:16px'><?php echo $this->linha_digitavel; ?></span></span></td>
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
                   <td class=ct valign=top width=126 height=13>Agência/Código do Cedente</td>
                   <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=2 border=0></td>
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
                   <td class=cp valign=top width=126 height=12>  <span class="campo"> <?php echo $agencia_codigo?> </span></td>
                   <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                   <td class=cp valign=top  width=34 height=12><span class="campo"> <?php echo $this->dados_especie?> </span> </td>
                   <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                   <td class=cp valign=top  width=53 height=12><span class="campo"> <?php echo $this->dados_quantidade?> </span> </td>
                   <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                   <td class=cp valign=top align=right width=120 height=12> <span class="campo"> <?php echo $this->nosso_numero;?> </span></td>
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
                   <td class=cp valign=top colspan=3 height=12> <span class="campo"> <?php echo $this->numero_documento?> </span></td>
                   <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                   <td class=cp valign=top width=132 height=12> <span class="campo"><?php echo $this->cpf_cnpj_cedente?> </span></td>
                   <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                   <td class=cp valign=top width=134 height=12> <span class="campo"> <?php echo $this->data_vencimento?> </span></td>
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
                  <td class=cp valign=top width=659 height=12><span class="campo"> <?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?></span></td>
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
                  <td class=ct  width=88 >Autenticação mecânica</td>
              </tr>
              <tr>
                  <td  width=7 ></td>
                  <td class=cp width=564 ><span class="campo">
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
                           echo "<font class=fonte>".gCorrigeData($mT002['T002_Data_Vencimento'])."</font>";
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
                  <td  width=7></td>
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
                  <td class=cp width=150> <span class="campo"><IMG src="imagens/logounibanco.jpg" width="150" height="40" border=0></span></td>
                  <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                  <td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $codigo_banco_com_dv?></font></div></td>
                  <td width=3 valign=bottom><img height=22 src=imagens/3.png width=2 border=0></td>
                  <td class=ld align=right width=453 valign=bottom><span class=ld><span style='font-size:16px'><?php echo $this->linha_digitavel;?></span></span></td>
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
                  <td class=cp valign=top width=472 height=12>Pagável em qualquer Banco até o vencimento</td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo $this->data_vencimento?> </span></td>
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
                  <td class=ct valign=top width=180 height=13>Agência/Código cedente</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=472 height=12><span class="campo"> <?php echo $this->nome_cedente;?></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=180 height=12> <span class="campo"><?php echo $agencia_codigo?></span></td>
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
                  <td class=ct valign=top width=163 height=13>N<u>o</u>documento</td>
                  <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=62 height=13>Espécie doc.</td>
                  <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=34 height=13>Aceite</td>
                  <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=72 height=13>Processamento</td>
                  <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=180 height=13>Nosso número</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=113 height=12><div align=left> <span class="campo"> <?php echo $this->getDataEmissaoBoleto();?> </span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=163 height=12> <span class="campo"><?php echo $this->numero_documento?> </span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=62 height=12><div align=left><span class="campo"> <?php echo $this->dados_especie_doc; ?></span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=34 height=12><div align=left><span class="campo"><?php echo $this->dados_aceite;?></span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=72 height=12><div align=left> <span class="campo"> <?php echo $this->data_processamento;?> </span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=180 height=12> <span class="campo"> <?php echo $this->nosso_numero;?> </span></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=113 height=1><img height=1 src=imagens/2.png width=113 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=163 height=1><img height=1 src=imagens/2.png width=163 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=62 height=1><img height=1 src=imagens/2.png width=62 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=34 height=1><img height=1 src=imagens/2.png width=34 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=72 height=1><img height=1 src=imagens/2.png width=72 border=0></td>
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
                  <td class=ct valign=top width=53 height=13>Espécie</td>
                  <td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=123 height=13>Quantidade</td>
                  <td class=ct valign=top height=13 width=7><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=72 height=13>Valor Documento</td>
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
                  <td class=cp valign=top  width=83><div align=left> <span class="campo">  <?php echo $this->numero_carteira;?></span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=53><div align=left><span class="campo"><?php echo $this->dados_especie?></span> </div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=123><span class="campo"> <?php echo $this->dados_quantidade?>  </span> </td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top  width=72> <span class="campo"> <?php echo $this->dados_valor_unitario?> </span></td>
                  <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=180 height=12>   <span class="campo"> <?php echo number_format($this->valor_duplicata,2,',','.');?>    </span></td>
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
                  <td valign=top width=468 rowspan=5><font class=ct>Instruções (Texto de responsabilidade do cedente)</font><br><br><span class=cp> <FONT class=campo>
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
                  <td class=ct valign=top width=659 height=13>Sacado</td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=659 height=12><span class="campo"><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?></span></td>
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
                  <td class=cp valign=top width=472 height=13> <span class="campo"> <?php echo $this->getEndereco2Sacado(); ?></span></td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=180 height=13>
                                    <?php
                                    if (!empty($this->codigo_baixa))
                                    {
                                       echo "Cód.baixa &nbsp;&nbsp;&nbsp;<span class='cp'>".$this->codigo_baixa;
                                    }
                                    ?>

                                   </span>
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
<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
       <TBODY>
              <TR>
                  <TD vAlign=bottom align=left height=50><?php b409_fbarcode($this->codigo_barras); ?> </TD>
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
