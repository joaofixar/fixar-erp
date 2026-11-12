<?php
namespace hardness;
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
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do    |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa                |
// |                                                                      |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +---------------------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>              |
// | Desenvolvimento Boleto Banco do Brasil: Daniel William Schultz / Leandro Maniezo|
// +---------------------------------------------------------------------------------+


$codigobanco = "033";
$codigo_banco_com_dv = b033_geraCodigoBanco($codigobanco);

//agencia � sempre 4 digitos
$agencia = b033_formata_numero($this->numero_agencia,4,0);
//conta � sempre 8 digitos
$conta = b033_formata_numero($this->numero_conta,8,0);
//agencia e conta
$agencia_codigo = $agencia."-". b033_modulo_11($agencia) ." / ". $conta ."-". b033_modulo_11($conta);
?>
<div id="boleto">
  <table cellspacing=0 cellpadding=0 border=0>
    <tbody>
      <tr>
        <td width=200>
          <table cellspacing=0 cellpadding=0 width=200 border=0>
            <tr>
              <td class=cp width=40><span class="campo"><IMG src="imagens/logosantander.jpg" width="60" height="25" border=0></span></td>
              <td width=3 valign=bottom><img height=19 src=imagens/3.png width=2 border=0></td>
              <td class=cpt width=50 valign=bottom><div align=center><font class=bc><span style='font-size:15px'><?php echo $codigo_banco_com_dv ?></span></font></div></td>
              <td width=3 valign=bottom><img height=19 src=imagens/3.png width=2 border=0></td> 
            </tr>

             <tbody>
                <tr>
                  <td colspan=5><img height=2 src=imagens/2.png width=208 border=0></td>
                </tr>
            </tbody>
          </table>

          <!-- <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=9>
                <img height=13 src=imagens/1.png width=1 border=0>
                </td>
                <td class=ct valign=top width=150 height=9>Benefici�rio</td>
                <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=33><img height=33 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=150 height=24><span class="campo"><span style='font-size:09px'><?php echo $this->nome_cedente; ?></span></span></td>
                <td class=cp valign=top width=7 height=33><img height=33 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=150 height=1><img height=1 src=imagens/2.png width=150 border=0></td>
                <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>                  
              </tr>
            </tbody>
          </table> -->

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=150 height=9>Vencimento</td>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=200 height=9 align=right><span class="campo"><span style='font-size:09px'><?php echo $this->data_vencimento?></span></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=200 height=9>Ag�ncia/C�digo Benefici�rio</td>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=200 height=9 align=right><span class="campo"><span style='font-size:09px'><?php echo $this->numero_agencia." / ". $this->codigo_cliente; ?></span></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=12><img height=13 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=96 height=9>Nosso n�mero</td>
                <td class=ct valign=top width=7 height=12><img height=13 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=97 height=9>N<u>o</u> documento</td>
                <td class=ct valign=top width=7 height=12><img height=13 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=96 height=9><span class="campo"><span style='font-size:09px'><?php echo $this->nosso_numero?></span></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=97 height=9 align=right><span class="campo"><span style='font-size:09px'><?php echo $this->numero_documento?></span></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=96 height=1><img height=1 src=imagens/2.png width=96 border=0></td>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=97 height=1><img height=1 src=imagens/2.png width=97 border=0></td>
                <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=96 height=9>Esp�cie</td>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=72 height=9>Quantidade</td>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=96 height=9><span class="campo"><span style='font-size:09px'><?php echo $this->dados_especie?></span></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=97 height=9 align=right><span class="campo"><span style='font-size:09px'><?php echo $this->nosso_numero?></span></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=96 height=1><img height=1 src=imagens/2.png width=96 border=0></td>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=97 height=1><img height=1 src=imagens/2.png width=97 border=0></td>
                <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=200 height=9>Valor documento</td>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=200 height=9 align=right><span class="campo"><span style='font-size:09px'><?php echo number_format($this->valor_duplicata,2,',','.');?></span></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                  <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                  
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0> </td>
                <td class=ct valign=top width=200 height=9>(-) Desconto / Abatimentos</td>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=200 height=9></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=200 height=9>(-) Outras dedu��es</td>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>       
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=200 height=9></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                  <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=200 height=9>(+) Mora / Multa</td>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>                  
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=200 height=9></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>                  
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                  <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=200 height=9>(+) Outros acr�scimos</td>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>                  
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=200 height=9></span></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>                  
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                  <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
            <tbody>
              <tr>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=ct valign=top width=200 height=9>(=) Valor cobrado</td>
                <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>                  
              </tr>
              <tr>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                <td class=cp valign=top width=200 height=9></span></td>
                <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0 width=200>
            <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td> 
                  <td class=ct valign=top width=200 height=13>Pagador</td>
                  <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td> 
              </tr>
              <tr>
                  <td class=cp valign=top width=0 height=40><img height=46 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=200 height=34><span class="campo"><span style='font-size:09px'><?php echo  $this->nome_sacado;?></span></span></td>
                  <td class=cp valign=top width=0 height=40><img height=46 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                <td valign=top width=200 height=1><img height=1 src=imagens/2.png width=200 border=0></td>
                <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr> 
            </tbody>
          </table>
        </td>
        <td width=30>
        </td>
        <td width=547>
        <table cellspacing=0 cellpadding=0 width=547 border=0>
            <tr>
               <td class=cp width=60><span class="campo"><IMG src="imagens/logosantander.jpg" width="60" height="25" border=0></span></td>
               <td width=3 valign=bottom><img height=19 src=imagens/3.png width=2 border=0></td>
               <td class=cpt width=50 valign=bottom><div align=center><font class=bc><span style='font-size:15px'><?php echo $codigo_banco_com_dv?></span></font></div></td>
               <td width=3 valign=bottom><img height=19 src=imagens/3.png width=2 border=0></td>
               <td class=ld align=left width=424 valign=bottom><span class=ld><span style='font-size:15px'><?php echo $this->linha_digitavel?></span></span></td>
            </tr>
            <tbody>
              <tr>
                  <td colspan=5><img height=2 src=imagens/2.png width=547 border=0></td>
              </tr>
            </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
             <tbody>
                    <tr>
                        <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=ct valign=top width=420 height=9>Local de pagamento  </td>
                        <td class=ct valign=top width=7 height=9><img height=12 src=imagens/1.png width=1 border=0></td>
                        <td class=ct valign=top width=122 height=9>Vencimento</td>
                        <td class=ct valign=top width=7 height=9><img height=12 src=imagens/1.png width=1 border=0></td>
                    </tr>
                    <tr>
                        <td class=cp valign=top width=7 height=11><img height=11 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top width=420 height=9><span style='font-size:09px'>PAG�VEL PREFERENCIALMENTE NAS COOPERATIVAS DE CR�DITO DO SICREDI</span></td>
                        <td class=cp valign=top width=7 height=11><img height=11 src=imagens/1.png width=1 border=0></td>
                        <td class=cp valign=top align=right width=122><span class="campo"><span style='font-size:09px'><?php echo $this->data_vencimento?></span></span></td>
                        <td class=ct valign=top width=7 height=9><img height=11 src=imagens/1.png width=1 border=0></td>
                    </tr>
                    <tr>
                        <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                        <td valign=top width=410 height=1><img height=1 src=imagens/2.png width=410 border=0></td>
                        <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                        <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                        <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                    </tr>
             </tbody>
          </table>

          <table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=11><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=410 height=9>Benefici�rio</td>
                  <td class=ct valign=top width=7 height=11><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=122 height=9>Ag�ncia/C�digo Benefici�rio</td>
                  <td class=ct valign=top width=7 height=9><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=14><img height=24 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=410><span class="campo"><span style='font-size:09px'><?php echo $this->nome_cedente;?> - <?php echo $this->cpf_cnpj_cedente.'<br>'; ?><?php echo $this->endereco_cedente; ?> - <?php echo $this->getEndereco2Cedente() ?></span></span></td>
                  <td class=cp valign=top width=7 height=14><img height=24 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=122><span class="campo"><span style='font-size:09px'><?php echo $this->numero_agencia." / ". $this->codigo_cliente; ?></span></span></td>
                  <td class=ct valign=top width=7 height=24><img height=24 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=410 height=1><img height=1 src=imagens/2.png width=410 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
       </tbody>
</table>

<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
       <tr>
           <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=ct valign=top width=91 height=9>Data do documento</td>
           <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=ct valign=top width=100 height=9>N<u>o</u> documento</td>
           <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=ct valign=top width=55 height=9>Esp�cie doc.</td>
           <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=ct valign=top width=45 height=9>Aceite</td>
           <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=ct valign=top width=91 height=9>Data processamento</td>
           <td class=ct valign=top width=7 height=13> <img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=ct valign=top width=122 height=9>Nosso n�mero</td>
           <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
       </tr>
       <tr>
           <td class=cp valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=cp valign=top width=91><div align=left><span class="campo"><span style='font-size:09px'><?php echo $this->getDataEmissaoBoleto();?></span></span></div></td>
           <td class=cp valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=cp valign=top width=100><span class="campo"><span style='font-size:09px'><?php echo $this->numero_documento?></span></span></td>
           <td class=cp valign=top width=7><img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=cp valign=top width=55><div align=left><span class="campo"><span style='font-size:09px'><?php echo $this->dados_especie_doc;?></span></span></div></td>
           <td class=cp valign=top width=7><img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=cp valign=top width=45><div align=left><span class="campo"><span style='font-size:09px'><?php echo  $this->dados_aceite; ?></span></span></div></td>
           <td class=cp valign=top width=7><img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=cp valign=top width=91 align=right><div align=left><span class="campo"><span style='font-size:09px'><?php echo $this->data_processamento;?></span></span></div></td>
           <td class=cp valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
           <td class=cp valign=top align=right width=100><span class="campo"><span style='font-size:09px'><?php echo $this->nosso_numero;?></span></span></td>
           <td class=ct valign=top width=7 height=13><img height=13 src=imagens/1.png width=1 border=0></td>
       </tr>
       <tr>
           <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
           <td valign=top width=91 height=1><img height=1 src=imagens/2.png width=91 border=0></td>
           <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
           <td valign=top width=100 height=1><img height=1 src=imagens/2.png width=100 border=0></td>
           <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
           <td valign=top width=55 height=1><img height=1 src=imagens/2.png width=55 border=0></td>
           <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
           <td valign=top width=45 height=1><img height=1 src=imagens/2.png width=45 border=0></td>
           <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
           <td valign=top width=91 height=1><img height=1 src=imagens/2.png width=91 border=0></td>
           <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
           <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
           <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
       </tr>
       </tbody>
</table>




<table cellspacing=0 cellpadding=0 border=0>
       <tbody>
              <tr>
                  <td class=ct valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=91 height=9>Uso do banco</td>
                  <td class=ct valign=top height=12 width=7> <img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=46 height=9>Carteira</td>
                  <td class=ct valign=top height=12 width=7><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=47 height=9>Esp�cie</td>
                  <td class=ct valign=top height=12 width=7><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=99 height=9>Quantidade</td>
                  <td class=ct valign=top height=12 width=7><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=99 height=9>Valor</td>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=ct valign=top width=122 height=9>(=)Valor documento</td>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td valign=top class=cp width=91 height=9><div align=left> </div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=46><div align=left> <span class="campo"><span style='font-size:09px'><?php echo $this->numero_carteira?></span></span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=47><div align=left><span class="campo"><span style='font-size:09px'><?php echo $this->dados_especie?></span></span></div></td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=122><span class="campo"><span style='font-size:09px'><?php echo $this->dados_quantidade?></span></span> </td>
                  <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top width=99> <span class="campo"><span style='font-size:09px'><?php echo $this->dados_valor_unitario?></span></span></td>
                  <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                  <td class=cp valign=top align=right width=99 height=12><span class="campo"><span style='font-size:09px'><?php echo number_format($this->valor_duplicata,2,',','.');?></span></span></td>
                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
              </tr>
              <tr>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=91 height=1><img height=1 src=imagens/2.png width=91 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=46 height=1><img height=1 src=imagens/2.png width=46 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=47 height=1><img height=1 src=imagens/2.png width=47 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=99 height=1><img height=1 src=imagens/2.png width=99 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=99 height=1><img height=1 src=imagens/2.png width=99 border=0></td>
                  <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                  <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                  <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
              </tr>
       </tbody>
</table>

<table cellspacing=0 cellpadding=0 width=547 border=0>
       <tbody>
              <tr>
                  <td align=right width=10>
                    <table cellspacing=0 cellpadding=0 border=0 align=left>
                           <tbody>
                                  <tr>
                                      <td class=ct valign=top width=7 height=10><img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td class=cp valign=top width=7 height=10><img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                                  </tr>
                           </tbody>
                    </table>
                  </td>
                  <td valign=top width=547 rowspan=4><font class=ct>Instru��es(Texto de responsabilidade do Benefici�rio)</font><br><br>
                                 <span class=cp>
                                             <FONT class=campo>
                                                <span style='font-size:09px'>
                                                  <?php echo $this->instrucoes1; ?>
                                                  <?php echo $this->instrucoes2; ?>
                                                  <?php echo $this->instrucoes3; ?>
                                                  <?php echo $this->instrucoes4; ?>
                                                </span>
                                             </FONT><br><br>
                                 </span>
                  </td>
                  <td align=right width=10>
                    <table cellspacing=0 cellpadding=0 border=0>
                           <tbody>
                                  <tr>
                                      <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                      <td class=ct valign=top width=122 height=8>(-)Desconto / Abatimentos</td>
                                      <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                      <td class=cp valign=top align=right width=122 height=9></td>
                                      <td class=cp valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                                      <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                                      <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
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
                                        <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
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
                   <td align=right width=10>
                    <table cellspacing=0 cellpadding=0 border=0>
                           <tbody>
                                  <tr>
                                      <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                      <td class=ct valign=top width=122 height=7>(-)Outras dedu��es</td>
                                      <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                                      <td class=cp valign=top align=right width=122 height=8></td>
                                      <td class=cp valign=top width=7 height=12> <img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                                      <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                                      <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
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
                                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
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
              <td align=right width=10>
                <table cellspacing=0 cellpadding=0 border=0>
                       <tbody>
                              <tr>
                                  <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                  <td class=ct valign=top width=122 height=8>(+)Mora / Multa</td>
                                  <td class=ct valign=top width=0 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                              </tr>
                              <tr>
                                  <td class=cp valign=top width=7 height=11><img height=11 src=imagens/1.png width=1 border=0></td>
                                  <td class=cp valign=top align=right width=122 height=8></td>
                                  <td class=cp valign=top width=0 height=11><img height=11 src=imagens/1.png width=1 border=0></td>
                              </tr>
                              <tr>
                                  <td valign=top width=7 height=1> <img height=1 src=imagens/2.png width=0 border=0></td>
                                  <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=0 border=0></td>
                              </tr>
                       </tbody>
                </table>
              </td>
            </tr>            
  </tbody>
</table>
      <table cellspacing=0 cellpadding=0 width=547 border=0>
             <tbody>
                    <tr>
                           <td valign=top width=547 height=1><img height=1 src=imagens/2.png width=547 border=0></td>
                    </tr>
             </tbody>
      </table>
<table cellspacing=0 cellpadding=0 width=547 border=0>
             <tbody>
                  <td align=right width=10 >
                      <table cellspacing=0 cellpadding=0 border=0 align=left>
                           <tbody>
                                  <tr>
                                      <td class=ct valign=top width=7 height=21><img height=30 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td class=cp valign=top width=7 height=12><img height=32 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                                  </tr>
                           </tbody>
                    </table>
                  </td>
                  <td valign=top width=547 rowspan=3><font class=ct>Pagador</font><br>
                      <span class=cp>
                        <span style='font-size:09px'><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?><span style='font-size:08px'><?php echo '<br>'.$this->endereco_sacado.' - '. $this->getEndereco2Sacado();?></span>
                      </span></td>
                    <td align=right width=10>
                    <table cellspacing=0 cellpadding=0 border=0>
                           <tbody>
                                  <tr>
                                      <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                      <td class=ct valign=top width=122 height=6>(+) Outros acr�scimos</td>
                                      <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td class=cp valign=top width=7 height=6><img height=6 src=imagens/1.png width=1 border=0></td>
                                      <td class=cp valign=top align=right width=122 height=6></td>
                                      <td class=ct valign=top width=7 height=6><img height=6 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                                      <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                                      <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                                  </tr>
                           </tbody>
                    </table>
                    <table cellspacing=0 cellpadding=0 border=0>
                           <tbody>
                                  <tr>
                                      <td class=ct valign=top width=7 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                      <td class=ct valign=top width=122 height=6>(=)Valor cobrado</td>
                                      <td class=cp valign=top width=0 height=12><img height=12 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td class=cp valign=top width=7 height=6><img height=6 src=imagens/1.png width=1 border=0></td>
                                      <td class=cp valign=top align=right width=122 height=6></td>
                                      <td class=cp valign=top width=0 height=6><img height=6 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                                      <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                                      <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                                  </tr>
                           </tbody>
                    </table>
                    <table cellspacing=0 cellpadding=0 border=0>
                           <tbody>
                                  <tr>
                                      <td class=ct valign=top width=7 height=12><img height=13 src=imagens/1.png width=1 border=0></td>
                                      <td class=ct valign=top width=122 height=6>C�d.baixa</td>
                                      <td class=cp valign=top width=0 height=12><img height=13 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td class=cp valign=top width=7 height=6><img height=11 src=imagens/1.png width=1 border=0></td>
                                      <td class=cp valign=top align=right width=122 height=6></td>
                                      <td class=cp valign=top width=0 height=8><img height=11 src=imagens/1.png width=1 border=0></td>
                                  </tr>
                                  <tr>
                                      <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=0 border=0></td>
                                      <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=0 border=0></td>

                                  </tr>
                           </tbody>
                    </table>
             </tbody>
      </table>

      <table cellspacing=0 cellpadding=0 border=0>
             <tbody>
                    <tr>
                        <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                        <td valign=top width=410 height=1><img height=1 src=imagens/2.png width=410 border=0></td>
                        <td valign=top width=7 height=1><img height=1 src=imagens/2.png width=7 border=0></td>
                        <td valign=top width=122 height=1><img height=1 src=imagens/2.png width=122 border=0></td>
                        <td valign=top width=1 height=1><img height=1 src=imagens/2.png width=1 border=0></td>
                    </tr>
             </tbody>
      </table>
      <table cellSpacing=0 cellPadding=0 border=0 width=547>
             <tbody>
                    <tr>
                        <td class=ct  width=7 height=9></td>
                        <td class=ct  width=170 >Sacador/Avalista</td>
                        <td class=ct  width=547 ><div align=right>Autentica��o mec�nica - <b class=cp>Ficha de Compensa��o</b></div></td>
                    </tr>
                    <tr>
                        <td class=ct  colspan=3 ></td>
                    </tr>
             </tbody>
      </table>
      <table cellSpacing=0 cellPadding=0 width=547 border=0>
             <tbody>
                    <tr>
                        <td vAlign=bottom align=left height=20><?php fbarcode033($this->codigo_barras); ?> </td>
                    </tr>
             </tbody>
      </table>
        </td>
          
      </tr>
    </tbody>
</table>

<table cellspacing=0 cellpadding=0 width=777 border=0>
       <tr>
           <td class=ct width=777></td>
       </tr>
       <tbody>
              <tr>
                  <td class=ct width=777><div align=right>Corte na linha pontilhada</div></td>
              </tr>
              <tr>
                  <td class=ct width=777><img height=1 src=imagens/6.png width=777 border=0></td>
              </tr>
       </tbody>
</table>
</table>

</div>
</BODY></HTML>
