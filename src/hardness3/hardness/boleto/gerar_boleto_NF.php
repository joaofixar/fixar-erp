<?php
namespace hardness;
global $g;

require_once('../../bibliotecas/confUsuario.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once("../../configuracoes/sysConfig.php");
require_once('../../bibliotecas/funcoesGerais.php');

echo "<BODY text=#000000 bgColor=#ffffff topMargin=0 rightMargin=0 leftMargin=0 bottomMargin=0>";

$nT002_Id=$_REQUEST['T002_Id'];
$nT007_Id=$_REQUEST['T007_Id'];
$nT117_Id=$_REQUEST['T117_Id'];

$cQuery = $_REQUEST['cQuery'];

$T002 = "SELECT T002_T117_Id FROM T002 WHERE md5(concat(T002_D024_Id,T002_T007_Id)) = '{$cQuery}'";
$T002 = mysqli_query($T002);
$array = '';
while($row = mysql_fetch_array($T002)){
    $array .= $row['T002_T117_Id'] . ','; 
}
$array = substr($array, 0, -1);
$array = trim($array, ',');
if (empty($cQuery))
{
	msg_erro_boleto("Não foi passado o cQuery");
}

$cClausula  = " select ";
$cClausula .= " D024_Id, "; 
$cClausula .= " D024_Nome_Empresa, ";
$cClausula .= " D024_Endereco, ";
$cClausula .= " D024_Numero, ";
$cClausula .= " D024_Cep, ";
$cClausula .= " D024_Flag_Protestar, ";
$cClausula .= " if(D024_Flag_Pessoa_Fisica_Juridica='F',D024_Cpf,if(D024_Flag_Pessoa_Fisica_Juridica='J',D024_Cnpj,''))as D024_Cpf_Cnpj, ";
$cClausula .= " D020_Nome_Cidade, ";
$cClausula .= " D018_UF, ";
$cClausula .= " T117_Id as T002_Id, ";
$cClausula .= " T002_Numero_Duplicata, ";
$cClausula .= " T117_Id as T002_Numero_Documento_T117_Id, ";
$cClausula .= " IF(T002_Numero_Documento IS NULL OR T002_Numero_Documento='', T117_Id, T002_Numero_Documento) as T002_Numero_Documento, ";
$cClausula .= " T117_Data_Emissao as T002_Data_Emissao, ";
$cClausula .= " T117_Data_Vencimento as T002_Data_Vencimento, ";
$cClausula .= " T117_Valor_Total as T002_Valor_Duplicata, ";
$cClausula .= " T117_Valor_Total as T002_Valor_Total, ";
$cClausula .= " T117_Percentual_Juros_Dia, ";
$cClausula .= " T117_Percentual_Multa, ";
$cClausula .= " T117_Instrucoes, ";
$cClausula .= " D007_Instrucoes_Protesto, ";
$cClausula .= " T117_Nosso_Numero, ";
$cClausula .= " T117_Aceite, ";
$cClausula .= " T117_Especie, ";
$cClausula .= " T117_Especie_Documento, ";
$cClausula .= " T117_Uso_Banco, ";
$cClausula .= " T117_Linha_Digitavel, ";
$cClausula .= " T117_Flag_Cancelado, ";
$cClausula .= " T117_Codigo_Barras, ";
$cClausula .= " T117_Flag_Com_Registro, ";
$cClausula .= " D062_Numero_Banco, ";
$cClausula .= " D007_Numero_Agencia, ";
$cClausula .= " D007_Digito_Verificador_Agencia, ";
$cClausula .= " D007_Numero_Conta, ";
$cClausula .= " D007_Digito_Verificador_Conta, ";
$cClausula .= " D007_Numero_Convenio, ";
$cClausula .= " D007_Numero_Contrato, ";
$cClausula .= " D007_Codigo_Baixa, ";
$cClausula .= " D007_Codigo_Cedente, ";
$cClausula .= " D007_Tipo_Carteira_Banco_Brasil, ";
$cClausula .= " D007_Flag_Tipo_Multa, ";
$cClausula .= " T002_Observacao, ";
$cClausula .= " T117_C004_Id ";
$cClausula .= " from T117 ";
$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
$cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
$cClausula .= " left join D062 on D062_Id=D007_D062_Id ";
$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
$cClausula .= " where 1=1 ";
$cClausula .= " and T117_Data_Pagamento='0000-00-00' ";
$cClausula .= " and D062_Numero_Banco!='' ";
// Array com os T117_Id para gerar todos os boletos referentes a NFE
$cClausula .= " AND T117_Id IN({$array}) ";

$cClausula .= "GROUP BY T117_Id order by T117_Data_Vencimento ";
// die($cClausula);
$cSql_T117=mysqli_query($cClausula);
echo mysql_error();
if (mysql_num_rows($cSql_T117)==0)
{
    msg_erro_boleto("Não encontrados registros para gerar o(s) boleto(s)<BR><BR>Faça as seguintes verificações:<BR><BR>. Os titulos estão pendentes ?<BR>. Não esta cancelado ?<BR>. Esta vencido ?<BR>. É Nota Fiscal ?<BR>. Foi definido Conta e Banco ?");
}

$xBoleto = new Emitir_Boleto();
$nPagina=0;
while ($mT117=mysql_fetch_array($cSql_T117))
{
    if (!empty($cQuery))
    {
       $nT117_Id=$mT117['T002_Id'];
    }
    
    if ($mT117['T117_Flag_Cancelado']=='S')
    {
       msg_erro_boleto("Este boleto foi cancelado!");
       exit;
    }

    $C004 = mysqli_query("select * from C004 left join D020 on D020_Id=C004_D020_Id left join D018 on D018_Id=C004_D018_Id where C004_Id='{$mT117['T117_C004_Id']}'");
    $mC004=mysql_fetch_array($C004);

	$nPagina++;
    unset($mInstrucao);
	
	//-- DADOS DA EMPRESA CEDENTE
	$xBoleto->setCodigoBanco($mT117['D062_Numero_Banco']);
	$xBoleto->setNomeCedente($mC004['C004_Nome_Empresa']);
	$xBoleto->setCpfCnpjCedente($mC004['C004_Cnpj']);
	$xBoleto->setEnderecoCedente($mC004['C004_Logradouro'].", ".$mC004['C004_Numero']);
	$xBoleto->setCidadeCedente($mC004['D020_Nome_Cidade']);
	$xBoleto->setEstadoCedente($mC004['D018_UF']);
    $xBoleto->setBairroCedente($mC004['C004_Bairro']);
    $xBoleto->setCepCedente($mC004['C004_Cep']);
    $titulosAgrupados = '';
    if($mT117['D007_Flag_Tipo_Multa'] != "P") {
        //Valores exibidos em REAIS
        //-- INSTRUCOES
        $nJuros_Dia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
        $nMulta_Vencimento = round(($mT117['T117_Percentual_Multa']/100)*$mT117['T002_Valor_Total'],2);
        if ($nMulta_Vencimento>0)
        {
           $mInstrucao[]= " Multa de R$ ".number_format($nMulta_Vencimento,2,',','.')." ";
        }
        if ($nJuros_Dia>0)
        {
           $mInstrucao[]= " Juros de R$ ".number_format($nJuros_Dia,2,',','.')." ao Dia.";
        }
        if ($nJuros_Dia>0 or $nMulta_Vencimento>0)
        {
           $xBoleto->setInstrucoes1 ('Após o Vencimento Cobrar '.implode("e",$mInstrucao));
        }
    } else {
        //Valores exibidos em porcentagem
        //-- INSTRUCOES
        if($mT117['T002_Observacao'] != ''){
            $titulosAgrupados = "Titulos Agrupados: ".$mT117['T002_Observacao']."<BR>";
        }
        $nJuros_Dia = $mT117['T117_Percentual_Juros_Dia'];
        $nMulta_Vencimento = $mT117['T117_Percentual_Multa'];
        if ($nMulta_Vencimento>0){
           $mInstrucao[]= " Multa de ".$nMulta_Vencimento."% ";
        }
        if ($nJuros_Dia>0){
           $mInstrucao[]= " Juros de ".$nJuros_Dia."% ao mês.";
        }
        if ($nJuros_Dia>0 or $nMulta_Vencimento>0){
           $xBoleto->setInstrucoes1 ($titulosAgrupados.'Após o Vencimento Cobrar '.implode("e",$mInstrucao));
        } else {
            $xBoleto->setInstrucoes1 ($titulosAgrupados);
        }
    }
    
    $instrucoes2 = '';
    $mT117['T117_Instrucoes']=trim($mT117['T117_Instrucoes']);
    if(!empty($mT117['T117_Instrucoes'])){
        $mT117['T117_Instrucoes'] = str_replace("\n","<BR>",$mT117['T117_Instrucoes']);
        $instrucoes2 .= $mT117['T117_Instrucoes'];
    }
    $mT117['D007_Instrucoes_Protesto']=trim($mT117['D007_Instrucoes_Protesto']);
    if(!empty($mT117['D007_Instrucoes_Protesto']) && $mT117['D024_Flag_Protestar'] != 'N'){
        $mT117['D007_Instrucoes_Protesto'] = str_replace("\n","<BR>",$mT117['D007_Instrucoes_Protesto']);
        $instrucoes2 .= ($instrucoes2 != '') ? '<BR>'.$mT117['D007_Instrucoes_Protesto'] : $mT117['D007_Instrucoes_Protesto'];
    }
    $xBoleto->setInstrucoes2($instrucoes2);

	//$xBoleto->setInstrucoes3 ('- Em caso de d�vidas entre em contato conosco: marcelo@hardness.com.br');
	//$xBoleto->setInstrucoes4 ('- Boleto emitido pelo sistema hardness - www.hardness.com.br');

	//-- TAXA PARA EMISSAO DO BOLETO
	$xBoleto->setTaxaBoleto (0);

	//-- DADOS DO BOLETO
	$xBoleto->setDadosQuantidade ('');
	$xBoleto->setDadosValorUnitario ('');
	$xBoleto->setDadosAceite ($mT117['T117_Aceite']);
	$xBoleto->setDadosUsoBanco ($mT117['T117_Uso_Banco']);
	$xBoleto->setDadosEspecie($mT117['T117_Especie']);
	$xBoleto->setDadosEspecieDoc ($mT117['T117_Especie_Documento']);


	//-- DATA DE EMISSAO DA NOTA FISCAL NO FORMATO DO BANCO DE DADOS AAAA/MM/DDD
	$xBoleto->setDataProcessamento ($mT117['T002_Data_Emissao']);

    //-- DATA DE VENCIMENTO DA DUPLICATA NO FORMATO DO BANCO DE DADOS AAAA/MM/DDD
	$xBoleto->setDataVencimento ($mT117['T002_Data_Vencimento']);

    //-- VALOR FINAL A SER COBRADO, SERA ADICIONADA A TAXA DO BOLETO SE FOR SETADA
	$xBoleto->setValorDuplicata ($mT117['T002_Valor_Total']);

    //-- NUMERO DA DUPLICATA
	$xBoleto->setNumeroDocumento ($mT117['T002_Numero_Duplicata']);

    //-- NOSSO NUMERO
	$xBoleto->setNossoNumero ($mT117['T117_Nosso_Numero']);

    //-- LINHA DIGITAVEL
	$xBoleto->setLinhaDigitavel($mT117['T117_Linha_Digitavel']);
	
    //-- CODIGO DE BARRAS
	$xBoleto->setCodigoBarras($mT117['T117_Codigo_Barras']);

    //-- COBRANCA COM OU SEM REGISTRO
	$xBoleto->setCobrancaComRegistro($mT117['T117_Flag_Com_Registro']);

	//-- DADOS DO CLIENTE - CNPJ OU CPF
	$xBoleto->setCpfCnpjSacado ($mT117['D024_Cpf_Cnpj']);
	
	//-- DADOS DO CLIENTE - ID DO CLIENTE
	if (!empty($mT117['D007_Codigo_Cedente']))
	{
        $xBoleto->setCodigoCliente($mT117['D007_Codigo_Cedente']);
    }
    else
    {
        $xBoleto->setCodigoCliente($mT117['D024_Id']);
    }

    //-- DADOS DO CLIENTE - NOME DA EMPRESA
	$xBoleto->setNomeSacado ($mT117['D024_Nome_Empresa']);

    //-- DADOS DO CLIENTE - ENDERECO
    $endereco = !empty($mT117['D024_Numero']) ? $mT117['D024_Endereco'].", ".$mT117['D024_Numero'] : $mT117['D024_Endereco'];
    $xBoleto->setEnderecoSacado ($endereco);

    //-- DADOS DO CLIENTE - BAIRRO
	$xBoleto->setBairroSacado ($mT117['D024_Bairro']);

    //-- DADOS DO CLIENTE - CIDADE
	$xBoleto->setCidadeSacado ($mT117['D020_Nome_Cidade']);

    //-- DADOS DO CLIENTE - ESTADO
	$xBoleto->setEstadoSacado ($mT117['D018_UF']);

    //-- DADOS DO CLIENTE - CEP
	$xBoleto->setCepSacado ($mT117['D024_Cep']);

	//-- DEMONSTRATIVO
	$xBoleto->setDemonstrativo1 ("REFERENTE A NOTA FISCAL: {$mT117['T002_Numero_Documento_T117_Id']}");
	$xBoleto->setDemonstrativo2 ("");
	$xBoleto->setDemonstrativo3 ("");


	//-- Num do conv�nio - REGRA: 6 ou 7 ou 8 d�gitos
	$xBoleto->setNumeroConvenio ("{$mT117['D007_Numero_Convenio']}");
	//-- Num do seu contrato
	$xBoleto->setNumeroContrato ("{$mT117['D007_Numero_Contrato']}");
	//-- C�digo da Carteira
	$xBoleto->setNumeroCarteira ("{$mT117['D007_Tipo_Carteira_Banco_Brasil']}");
	//-- Varia��o da Carteira, com tra�o (opcional)
	$xBoleto->setNumeroVariacaoCarteira ('-019');

	//-- CODIGO DA BAIXAO (UNIBANCO)
	$xBoleto->setCodigoBaixa($mT117['D007_Codigo_Baixa']);

	//-- TIPO DO BOLETO
	if (strlen($xBoleto->numero_convenio)>=8)
	{
	   $xBoleto->setFormatacaoConvenio("8");
	}
	else
	{
	   $xBoleto->setFormatacaoConvenio(strlen($xBoleto->numero_convenio));
	}
	$xBoleto->setFormatacaoNossoNumero("2");
	
	//-- Num da agencia, sem digito
	$xBoleto->setNumeroAgencia("{$mT117['D007_Numero_Agencia']}");
    //-- Digito verificador da agencia
    $xBoleto->setDigitoVerificadorAgencia("$mT117[D007_Digito_Verificador_Agencia]");
	
	//-- Num da conta, sem digito
	$xBoleto->setNumeroConta("{$mT117['D007_Numero_Conta']}");
	$xBoleto->setDVConta("{$mT117['D007_Digito_Verificador_Conta']}");

	if ($nPagina==1)
	{
        ?>
        <HTML>
        <HEAD>
        <title><?php echo  $xBoleto->nome_cedente; ?></title>
        <meta http-equiv=Content-Type content=text/html charset=UTF-8>
        <link rel=stylesheet href='style.css' type='text/css'>
        </head>

        <?php
    }
    
	if ($nPagina>1)
	{
	    echo "<p style='page-break-after: always'></p>";
	}
	
    ?>
    <div id="container">

    	<div id="instr_header">
            <table width=666 cellspacing=5 cellpadding=0 border=0 align=Default>
              <tr>
                <?php
                $Diretorio_Imagens = base64_decode($_REQUEST['path']) . "imagens/logo";
                //$Diretorio_Imagens = "/hardness3/dados_usuarios/{$g['pathDados']}/imagens/logo";                
                $Arquivo_Logo="$Diretorio_Imagens/$mC004[C004_Id]" . substr($mC004['C004_Logo_Marca'], -4);                
                if (!empty($mC004['C004_Logo_Marca']) and !empty($Arquivo_Logo))
                {
                   echo "<td width=41><IMG SRC='$Arquivo_Logo' style='max-height: 80px;'/></td>";
                }
                ?>
                <td class=ti width=455><?php echo $xBoleto->nome_cedente; ?> <?php echo isset($xBoleto->cpf_cnpj_cedente) ? "<br>".$xBoleto->cpf_cnpj_cedente : '' ?><br>
            	<?php echo $xBoleto->endereco_cedente; ?><br>
            	<?php echo $xBoleto->cidade_cedente." / ".$xBoleto->estado_cedente; ?><br>
                </td>
                <td align=RIGHT width=150 class=ti>&nbsp;</td>
              </tr>
            </table>
    	</div>	<!-- id="instr_header" -->
    <?php
	
	//-- GERA O BOLETO
	$xBoleto->Gerar_Boleto();

    echo "</div>";
}


//echo "<script>self.print();</script>";



function msg_erro_boleto($cErro)
{
    echo "<table width=100% height=100% border=0 bgcolor=#0000FF >";
         echo "<tr>";
              echo "<td align=center>";
                  echo "<table border=0 bgcolor=#FF0000>";
                      echo "<tr>";
                          echo "<td align=center>";
                             echo "<fieldset>";

                                echo "<table border=0>";
                                     echo "<tr>";
                                          echo "<td align=left>";
                                               echo "<font style='font-size:14px;font-family:arial,verdana;color:white;font-weight:bold;'>";
                                               echo $cErro;
                                               echo "</font>";
                                          echo "</td>";
                                     echo "</tr>";
                                 echo "</table>";

                              echo "</fieldset>";
                          echo "</td>";
                     echo "</tr>";
                echo "</table>";
             echo "</td>";
         echo "</tr>";
    echo "</table>";
    exit;
}




class Emitir_Boleto
{
	public function Gerar_Boleto()
	{
	    global $mBanco;
	    global $nT117_Id;
	    $t=$this->getCodigoBanco();
	    if (empty($mBanco[$t]))
	    {
            $mBanco[$t]='S';
   		    include("include/funcoes_".$this->getCodigoBanco().".php");
        }
        
		include("include/layout_".$this->getCodigoBanco().".php");
		
	}
	
	/////////////////////////////////////////////////////
	//-- CODIGO DO BANCO
    public function setCodigoBanco($x)
	{
        $this->codigo_banco = $x;
    }
    public function getCodigoBanco()
	{
        return $this->codigo_banco;
    }
    

	/////////////////////////////////////////////////////
	//-- TAXA BOLETO
    public function setTaxaBoleto($Taxa_Boleto)
	{
        $this->taxa_boleto = $Taxa_Boleto;
    }
    public function getTaxaBoleto()
	{
        return $this->taxa_boleto;
    }
    public function getTextoTaxaBoleto()
	{
        return "Taxa Bancária R$ ".number_format($this->getTaxaBoleto(),2,',','.');
    }

	/////////////////////////////////////////////////////
    //-- DATA DO DOCUMENTO
    public function getDataEmissaoBoleto()
	{
        return date("d/m/Y");
    }

	/////////////////////////////////////////////////////
    //-- DATA DE PROCESSAMENTO
    public function setDataProcessamento($Data_Processamento)
	{
		list($Ano,$Mes,$Dia) = explode("-",$Data_Processamento);
        $this->data_processamento = $Dia."/".$Mes."/".$Ano;
    }

	/////////////////////////////////////////////////////
    //-- DATA DE VENCIMENTO
    public function setDataVencimento($Data_Vencimento)
	{
		list($Ano,$Mes,$Dia) = explode("-",$Data_Vencimento);
        $this->data_vencimento = $Dia."/".$Mes."/".$Ano;
    }

	/////////////////////////////////////////////////////
    //-- VALOR DA DUPLICATA (VALOR A SER COBRADO)
    public function setValorDuplicata($Valor_Duplicata)
	{
        $this->valor_duplicata = $Valor_Duplicata;
    }
    public function getValorDuplicata()
	{
        return $this->valor_duplicata;
    }

   	/////////////////////////////////////////////////////
    //-- VALOR TOTAL DA DUPLICATA
    public function getValorTotal()
	{
	    $Valor = $this->getValorDuplicata()+$this->getTaxaBoleto();
        return $Valor;
    }

	/////////////////////////////////////////////////////
    //-- NUMERO DO DOCUMENTO
    public function setNumeroDocumento($Numero_Documento)
	{
        $this->numero_documento = $Numero_Documento;
    }

	/////////////////////////////////////////////////////
    //-- NOSSO NUMERO
    public function setNossoNumero($Numero)
	{
        $this->nosso_numero = $Numero;
    }

	/////////////////////////////////////////////////////
    //-- SACADO: CPF ou CNPJ
    public function setCpfCnpjSacado($x)
	{
        $this->cpf_cnpj_sacado = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - NOME EMPRESA
    public function setNomeSacado($Nome_Sacado)
	{
        $this->nome_sacado = $Nome_Sacado;
    }

	/////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - ENDERECO
    public function setEnderecoSacado($Endereco)
	{
        $this->endereco_sacado = $Endereco;
    }


	/////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - BAIRRO
    public function setBairroSacado($Bairro)
	{
        $this->bairro_sacado = $Bairro;
    }
    public function getBairroSacado()
	{
        return $this->bairro_sacado;
    }

	/////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - CIDADE
    public function setCidadeSacado($Cidade)
	{
        $this->cidade_sacado = $Cidade;
    }
    public function getCidadeSacado()
	{
        return $this->cidade_sacado;
    }

	/////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - ESTADO
    public function setEstadoSacado($Estado)
	{
        $this->estado_sacado = $Estado;
    }
    public function getEstadoSacado()
	{
        return $this->estado_sacado;
    }

	/////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - CEP
    public function setCepSacado($Cep)
	{
        $this->cep_sacado = "CEP: $Cep";
    }
    public function getCepSacado()
	{
        return $this->cep_sacado;
    }

	/////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - ENDERECO 2 - BAIRRO CIDADE ESTADO E CEP
    public function getEndereco2Sacado()
	{
	    $Bairro = $this->getBairroSacado();
	    $Retorno = "";

	    if (!empty($Bairro))
		{
			$Retorno .= "Bairro: $Bairro - ";
		}
	    $Retorno .= $this->getCidadeSacado()." - ";
	    $Retorno .= $this->getEstadoSacado()." - ";
	    $Retorno .= $this->getCepSacado();
        return $Retorno;
    }

	/////////////////////////////////////////////////////
    //-- DEMONSTRATIVOS LINHA 1
    public function setDemonstrativo1($Texto)
	{
        $this->demonstrativo1 = $Texto;
    }

	/////////////////////////////////////////////////////
    //-- DEMONSTRATIVOS LINHA 2
    public function setDemonstrativo2($Texto)
	{
        $this->demonstrativo2 = $Texto;
    }

	/////////////////////////////////////////////////////
    //-- DEMONSTRATIVOS LINHA 3
    public function setDemonstrativo3($Texto)
	{
        $this->demonstrativo3 = $Texto;
    }

	/////////////////////////////////////////////////////
    //-- INSTRUCOES LINHA 1
    public function setInstrucoes1($Texto)
	{
        $this->instrucoes1 = $Texto;
    }
    public function getInstrucoes1()
	{
        return $this->instrucoes1;
    }

	/////////////////////////////////////////////////////
    //-- INSTRUCOES LINHA 2
    public function setInstrucoes2($Texto)
	{
        $this->instrucoes2 = $Texto;
    }
    public function getInstrucoes2()
	{
        return $this->instrucoes2;
    }

	/////////////////////////////////////////////////////
    //-- INSTRUCOES LINHA 3
    public function setInstrucoes3($Texto)
	{
        $this->instrucoes3 = $Texto;
    }
    public function getInstrucoes3()
	{
        return $this->instrucoes3;
    }

	/////////////////////////////////////////////////////
    //-- INSTRUCOES LINHA 4
    public function setInstrucoes4($Texto)
	{
        $this->instrucoes4 = $Texto;
    }
    public function getInstrucoes4()
	{
        return $this->instrucoes4;
    }

	/////////////////////////////////////////////////////
    //-- QUANTIDADE
    public function setDadosQuantidade($x)
	{
        $this->dados_quantidade = $x;
    }

	/////////////////////////////////////////////////////
    //-- VALOR UNITARIO
    public function setDadosValorUnitario($x)
	{
        $this->dados_valor_unitario = $x;
    }

	/////////////////////////////////////////////////////
    //-- CODIGO DA BAIXA
    public function setCodigoBaixa($x)
	{
        $this->codigo_baixa = $x;
    }

	/////////////////////////////////////////////////////
    //-- ACEITE
    public function setDadosAceite($x)
	{
        $this->dados_aceite = $x;
    }

	/////////////////////////////////////////////////////
    //-- USO DO BANCO
    public function setDadosUsoBanco($x)
	{
        $this->dados_uso_banco = $x;
    }
    public function getDadosUsoBanco()
	{
        return $this->dados_uso_banco;
    }

	/////////////////////////////////////////////////////
    //-- ESPECIE
    public function setDadosEspecie($x)
	{
        $this->dados_especie = $x;
    }

	/////////////////////////////////////////////////////
    //-- ESPECIE DOC
    public function setDadosEspecieDoc($x)
	{
        $this->dados_especie_doc = $x;
    }
    public function getDadosEspecieDoc()
	{
        return $this->dados_especie_doc;
    }

	/////////////////////////////////////////////////////
    //-- NUMERO DO CONVENIO
    public function setNumeroConvenio($x)
	{
        $this->numero_convenio = $x;
    }


	/////////////////////////////////////////////////////
    //-- NUMERO DO CONTRATO
    public function setNumeroContrato($x)
	{
        $this->numero_contrato = $x;
    }
    public function getNumeroContrato()
	{
        return $this->numero_contrato;
    }

	/////////////////////////////////////////////////////
    //-- NUMERO DO CARTEIRA
    public function setNumeroCarteira($x)
	{
        $this->numero_carteira = trim($x);
    }
    public function getNumeroCarteira()
	{
        return $this->numero_carteira;
    }

	/////////////////////////////////////////////////////
    //-- VARIACAO CARTEIRA
    public function setNumeroVariacaoCarteira($x)
	{
        $this->numero_variacao_carteira = trim($x);
    }
    public function getNumeroVariacaoCarteira()
	{
        return $this->numero_variacao_carteira;
    }

	/////////////////////////////////////////////////////
    //-- NUMERO DA AGENCIA
    public function setNumeroAgencia($x)
	{
        $this->numero_agencia = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- DIGITO VERIFICADOR DA AGENCIA
    public function setDigitoVerificadorAgencia($x)
	{
        $this->digito_verificador_agencia = trim($x);
    }


	/////////////////////////////////////////////////////
    //-- NUMERO DA CONTA
    public function setNumeroConta($x)
	{
        $this->numero_conta = trim($x);
    }
    public function getNumeroConta()
	{
        return $this->numero_conta;
    }

	/////////////////////////////////////////////////////
    //-- DIGITO VERIFICADOR DA CONTA
    public function setDVConta($x)
	{
        $this->digito_verificador_conta = trim($x);
    }
    public function getDVConta()
	{
	    if (empty($this->digito_verificador_conta)) $this->digito_verificador_conta=0;
        return $this->digito_verificador_conta;
    }
    
    public function getNumeroContaDV()
	{
        return $this->numero_conta.'-'.$this->digito_verificador_conta;
    }

	/////////////////////////////////////////////////////
    //-- CEDENTE: NOME
    public function setNomeCedente($x)
	{
        $this->nome_cedente = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- CEDENTE: CPF ou CNPJ
    public function setCpfCnpjCedente($x)
	{
        $this->cpf_cnpj_cedente = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- CEDENTE: ENDERECO
    public function setEnderecoCedente($x)
	{
        $this->endereco_cedente = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- CEDENTE: CIDADE
    public function setCidadeCedente($x)
	{
        $this->cidade_cedente = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- CEDENTE: ESTADO
    public function setEstadoCedente($x)
	{
        $this->estado_cedente = trim($x);
    }

    /////////////////////////////////////////////////////
    //-- CEDENTE - BAIRRO
    public function setBairroCedente($Bairro)
    {
        $this->bairro_cedente = $Bairro;
    }
    public function getBairroCedente()
    {
        return $this->bairro_cedente;
    }

    /////////////////////////////////////////////////////
    //-- CEDENTE - CEP
    public function setCepCedente($Cep)
    {
        $this->cep_cedente = $Cep;
    }
    public function getCidadeCedente()
    {
        return $this->cidade_cedente;
    }

    /////////////////////////////////////////////////////
    //-- TEXTO CARTEIRA
    public function setTextoCarteira($texto_carteira)
    {
        $this->TextoCarteira = $texto_carteira;
    }

    /////////////////////////////////////////////////////
    //-- DADOS DO CLIENTE - ENDERECO 2 - BAIRRO CIDADE ESTADO E CEP
    public function getEndereco2Cedente()
    {
        $Bairro = $this->getBairroCedente();
        $Retorno = "";

        if (!empty($Bairro))
        {
            $Retorno .= "$Bairro - ";
        }
        $Retorno .= $this->getCidadeCedente()."/";
        $Retorno .= $this->getEstadoCedente()." - ";
        $Retorno .= $this->getCepCedente();
        return $Retorno;
    }
    public function getEstadoCedente()
    {
        return $this->estado_cedente;
    }
    public function getCepCedente()
    {
        return $this->cep_cedente;
    }

	/////////////////////////////////////////////////////
    //-- FORMATACAO: CONVENIO
    public function setFormatacaoConvenio($x)
	{
        $this->formatacao_convenio = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- FORMATACAO: NOSSO NUMERO
    public function setFormatacaoNossoNumero($x)
	{
        $this->formatacao_nosso_numero = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- CODIGO DO CLIENTE
    public function setCodigoCliente($x)
	{
        $this->codigo_cliente = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- CODIGO DE BARRAS
    public function setCodigoBarras($x)
	{
        $this->codigo_barras = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- LINHA DIGITAVEL
    public function setLinhaDigitavel($x)
	{
        $this->linha_digitavel = trim($x);
    }

	/////////////////////////////////////////////////////
    //-- TIPO DA COBRANCA
    public function setCobrancaComRegistro($x)
	{
        $this->cobranca_com_registro = trim($x);
    }

}

?>
