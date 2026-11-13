<?php
namespace hardness;


switch ($g['r'][3]) {
	/**
	 * Cabeçalho Orçamento
	 * Chamado em: ven001TabEditarOrcamento
	 */
	case 'est002SeparacaoPorLocacaoCabecalho':
		// Select
        $sql = <<<EOT
        SELECT 
            *,
            if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
        FROM 
            T005
        left join D024 on T005_D024_Id=D024_Id
        left join D020 on D024_D020_Id=D020_Id
        left join D018 on D024_D018_Id=D018_Id
        left join C007 on C007_Id=D024_C007_Vendedor_Interno
        left join D030 on D024_D030_Id=D030_Id
        WHERE
            (T005_Id='$r_acaoId')
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (empty($row_)) { 
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        }
        
        $possuiRestricoes = "";
        if($row['D024_Flag_Liberado']=="N" || $row['D024_Flag_Restricao'] == "S"){
            $possuiRestricoes = "<font color='red'><b>C/RESTRIÇÕES</b></font>";
        }else{
            $possuiRestricoes = "<font color='green'><b>LIBERADO</b></font>";
        }
        $Contribuinte="<font color='yellow' style='background:red'><b>&nbsp;INFORMAR!&nbsp;</b></font>";
        if($row['D024_Flag_Contribuinte']=="0"){
            $Contribuinte = "<font color='blue'><b>NÃO CONTRIB.</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="1"){
            $Contribuinte = "<font color='blue'><b>CONSUM.FINAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="2"){
            $Contribuinte = "<font color='blue'><b>REVENDA</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="3"){
            $Contribuinte = "<font color='blue'><b>PROD.RURAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="4"){
            $Contribuinte = "<font color='blue'><b>SIMPLES</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="5"){
            $Contribuinte = "<font color='blue'><b>INDUSTRIA</b></font>";
        }   
        
        $dataEmissao = gCorrigeData($row['T005_Data_Emissao']);
        if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Fantasia'], 35);
        } else {
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 35);
        }
        $D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 15);
        
        switch($row['T005_Flag_Lista_Preco']){
            case '1':   
                $listaPreco="Preço 1";
                break;
            case '2':
                $listaPreco="Preço 2";
                break;
            case '3':
                $listaPreco = "Preço 3";
                break;
            default:
                $listaPreco="<font color='red'><b>FALTA</b></font>";
        }
        
        // Print
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc">
                        <td width='70px'>Nº</td>
                        <td width='30px'>Código</td>
                        <td width='400px'>Cliente</td>
                        <td width='150px'>Cidade</td>
                        <td width='30px'>UF</td>
                        <td width='70px'>Emissão</td>
                        <!--<td width='50px'>Tipo</td>
                        <td width='50px'>Financeiro</td>
                        <td width='75px'>Lista</td>-->
                    </tr>
                    <tr class="linhaVal">
                        <td align='center'>{$row['T005_Id']}</td>
                        <td>{$row['D024_Id']}</td>
                        <td>{$D024_Nome_Empresa}</td>
                        <td>{$D020_Nome_Cidade}</td>
                        <td>{$row['D018_UF']}</td>
                        <td>{$dataEmissao}</td>
                        <!--<td>{$Contribuinte}</td>
                        <td>{$possuiRestricoes}</td>
                        <td>{$listaPreco}</td>-->
                    </tr>
                </table>
            </div>
EOT;

	break;

    case 'est002ImprimirRomaneioDeColeta':

      $T275_Id = isset($_REQUEST['T275_Id']) ? $_REQUEST['T275_Id'] : false;

      //Configurações CSS
      $Cor_Normal="#ffffff";
      $Linhas_Normais=30;
      $Linhas_Ultima_Pagina=25;
      $Tamanho_Maximo_Tabela=580;
      $Tamanho_Minimo_Tabela=400;
      $Width_Relatorio='98%';

      $cSql_T007=mysql_query("select  T005_Id,
                                      T007_Data_Emissao,
                                      T007_Peso_Liquido,
                                      T007_Peso_Bruto,
                                      T007_Especie,
                                      T007_Quantidade_volumes,
                                      T007_Flag_ACP,
                                      D024_Nome_Empresa,
                                      D022_Id,
                                      D022_Nome_Empresa,
                                      D022_DDD_Telefone_1,
                                      D022_Telefone_1,
                                      D022_CNPJ,
                                      D020_Nome_Cidade,
                                      D018_UF,
                                      D022_Endereco,
                                      T007_Numero_Nota_Fiscal,
                                      T275_Data_Emissao
                                      from T005
                                      left join D024 on D024_Id=T005_D024_Id
                                      left join D022 on D022_Id=T005_D022_Id
                                      left join D020 on D020_Id=D022_D020_Id
                                      left join D018 on D018_Id=D022_D018_Id
                                      left join D021 on D021_Id=T005_D021_Id
                                      left join D006 on D006_Id=T005_D006_Id
                                      left join C004 on C004_Id=T005_C004_Id
                                      left join D036 on T005_D036_Id=D036_Id 
                                      left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
                                      left join T275 on T005_T275_Id=T275_Id 
                                      where (T005_C004_Id='{$g['empresaAtual']}')
                                      AND T005_T275_Id='{$T275_Id}'
                                      group by D022_Id");

      $erro =  mysql_error();

      if(!empty($erro)){
          echo $erro;
      }
     
       echo "<html>";
        echo "<head>";
        echo "<title>Romaneio de Coleta</title>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "<link rel=stylesheet href=\"../classes/classe_tamanho_pequeno_firefox.css\" type=\"text/css\">";

        echo "<style>
            .fonte{
                font-family:arial,verdana;
                font-size:11px;
            }

            body{
              padding: 18px
            }

          .grid-container {
              display: grid;
              grid-template-columns: 1fr 1fr;
              grid-gap: 20px;
          }
          </style>";

        echo "</head>";
        echo "<body bgcolor=white leftmargin='0' topmargin='0'>";


        ////////////////////////////////////////////////////////////
        // VERIFICA QUANTAS PAGINAS SERÃO IMPRESSAS
        ////////////////////////////////////////////////////////////
        // Encontra o total das linhas a ser impressa
        $Total_Linhas=mysql_num_rows($cSql_T007);
       
        if ($Total_Linhas==0) {
           //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS É DEFINIDO PARA 1,
           //-- PARA QUE SEJA IMPRESSO O CABECALHO.
           $Total_Linhas=1;
        }

        if ($Total_Linhas>=1) {

            if (count($mLimit)==0) {
                // Encontra o numero de paginas cheias
                $Total_Paginas = intval($Total_Linhas / $Linhas_Normais);
                // Encontra o numero de itens restantes
                if ($Total_Paginas>=1) {
                  $Resto = ($Total_Linhas - ($Total_Paginas * $Linhas_Normais));
                } else {
                  $Resto = $Total_Linhas;
                }

                // Verifica caso o resto seja maior que o número máximo de itens que cabe na última folha
                if ($Resto > $Linhas_Ultima_Pagina) {
                  // Sendo maior soma mais duas folhas
                  $Total_Paginas += 2 ;
                } else {
                  // Sendo menor soma mais uma folha
                  $Total_Paginas += 1 ;
                }
            } else {
                $Total_Paginas=count($mLimit);

                $Linhas_Normais=$mLimit[0];
            }
        }

        //////////////////////////////////////////////////////////////

        // Inicia com o tamanho maximo estabelecido
        $Tamanho_Tabela_Itens=$Tamanho_Maximo_Tabela;

        // Inicializa Variavel utilizada no limit da consulta dos itens
        $Inicio=0;
        $Pagina_Atual=0;

        $Pagina_Atual++;
        $pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
                

        while ($mT007=mysql_fetch_array($cSql_T007)) {


            echo "<div style='width:730px;align-content: center;page-break-before: always;'>";
                   echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='735' bgcolor=white bgcolor=white style='border:0px solid black'>";
                     echo "<tr bgcolor=white>";
                      echo "<td valign=top>";
                       echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top style='border:0px solid'>";
                        echo "<tr>";
                         echo "<td bgcolor=white width=80%>";
                        echo <<<EOT
                        <table style='width:100%'>
                            <tr>
                                <td style="width: 1%">
                                    <img src="{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}" style="float: left; margin-right: 8px;" onerror="this.src='{$confUsuario['urlRaiz']}{$g['pathWeb']}/static/img/placeholder_logo.png'">
                                </td>
                                <td class=fonte style="font-size:14px;">
                                    <font><b>{$g['c004']['C004_Nome_Abreviado']}</b></font><br />
                                    {$g['c004']['C004_Logradouro']}, {$g['c004']['C004_Numero']} {$g['c004']['C004_Complemento']}<br />
                                    {$g['c004']['C004_Cep']} - {$g['c004']['D020_Nome_Cidade']} - {$g['c004']['D018_UF']}<br />
                                    Fone/Fax: {$g['c004']['C004_Telefone']} Fax: {$g['c004']['C004_Fax']}<br />
                                    {$g['c004']['C004_Site']}<br />
                                </td>
                                
                            </tr>
                        </table>
EOT;
                         echo "</td>";
                         echo "<td bgcolor=white align=center width=20%>";
                              echo "<font class='fonte' style='font-size:18px;font-weight:bold'>";
                              echo "ROMANEIO nº".$T275_Id;
                              echo "</font>";
                           echo "</font>";
                           echo "<br><font class=fonte style='font-size:12px;'>Gerado em:".gCorrigeData($mT007['T275_Data_Emissao'])."</font>";
                         echo "</td>";
                       echo "</tr>";

            //imprimir o tipo
            $tipoDocumento = '';
            if(empty($mT007['T007_Flag_ACP'])){$mT007['T007_Flag_ACP']=1;}else
            if($mT007['T007_Flag_ACP']==1){$tipoDocumento='NF';}else
            if($mT007['T007_Flag_ACP']==2){$tipoDocumento='ROM';}else
            if($mT007['T007_Flag_ACP']==3){$tipoDocumento='ACP';}else
            if($mT007['T007_Flag_ACP']==4){$tipoDocumento='CF';}else
            if($mT007['T007_Flag_ACP']==5){$tipoDocumento='D1';}else
            if($mT007['T007_Flag_ACP']==6){$tipoDocumento='NF-e';}else
            if($mT007['T007_Flag_ACP']==7){$tipoDocumento='NFS-e';}else
            if($mT007['T007_Flag_ACP']==8){$tipoDocumento='NFC-e';}

                       echo "<tr bgcolor=white>";
                         echo "<td colspan=2>";
                           echo "<table cellpadding=7 width=100% border=0>";
                             echo "<tr bgcolor=white>";
                               echo "<td colspan=3>";
                               echo "<table width=100% border=0>";
                                echo "<tr><td style='width:50%'>";
                                       echo "<font class='fonte' style='font-size:20px'><b>Transportadora</b></font><br>";
                                       echo "<font class='fonte' style='font-size:12px'>Transportadora : <b>{$mT007['D022_Id']}-{$mT007['D022_Nome_Empresa']}</b></font><br>";
                                       echo "<font class='fonte' style='font-size:12px'>Telefone: <b>({$mT007['D022_DDD_Telefone_1']}) {$mT007['D022_Telefone_1']}</b></font><br>";
                                       echo "<font class='fonte' style='font-size:12px'>CNPJ/CPF: <b>{$mT007['D022_CNPJ']}</b></font><br>";
                                       echo "<font class='fonte' style='font-size:12px'>Endereco: <b>{$mT007['D022_Endereco']}</font></b><br>";
                                       echo "<font class='fonte' style='font-size:12px'>Cidade/UF: <b>{$mT007['D020_Nome_Cidade']} / {$mT007['D018_UF']}</font></b><br>";
                                echo "</td>";
                            
                                echo "<tr><td colspan=2>";
                                 
                                echo "</td></tr>";
                               echo "</table>";
                             echo "</td>";
                           echo "</tr>";
                         echo "</table>";
                       echo "</td>";
                     echo "</tr>";
                    echo "</table>";
                   echo "</td>";
                 echo "</tr>";

                 //////////////////////////////////////////////////////////////////////////////////////////////
                 // CORPO DO RELATORIO
                 //////////////////////////////////////////////////////////////////////////////////////////////

                   $Csql_T007=mysql_query("select  T007_Data_Emissao,
                                            T007_Peso_Liquido,
                                            T007_Peso_Bruto,
                                            T007_Especie,
                                            T007_Quantidade_volumes,
                                            T007_Flag_ACP,
                                            D024_Nome_Empresa,
                                            D022_Id,
                                            D022_Nome_Empresa,
                                            D022_Telefone_1,
                                            D022_CNPJ,
                                            D020_Nome_Cidade,
                                            D018_UF,
                                            D022_Endereco,
                                            T007_Numero_Nota_Fiscal
                                            from T005
                                            left join D024 on D024_Id=T005_D024_Id
                                            left join D020 on D020_Id=D024_D020_Id
                                            left join D018 on D018_Id=D024_D018_Id
                                            left join D021 on D021_Id=T005_D021_Id
                                            left join D022 on D022_Id=T005_D022_Id
                                            left join D006 on D006_Id=T005_D006_Id
                                            left join C004 on C004_Id=T005_C004_Id
                                            left join D036 on T005_D036_Id=D036_Id 
                                            left join T007 on T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
                                            where (T005_C004_Id='{$g['empresaAtual']}')
                                            AND T005_T275_Id='{$T275_Id}'");

                 echo "<tr valign=top height=100% bgcolor=white>";
                   echo "<td valign=top>";

                     echo "<table align=center cellspacing=1 cellpadding=3 border='1px solid black' style='border-collapse: collapse' width=100% bgcolor=black valign=top>";

                       echo "<tr bgcolor=#f4f4f4>";
                         echo "<td align='center' valign='center' width='30'";
                           echo "<font class='fonte'><b>Nº</b></font>";
                         echo "</td>";

                         echo "<td align='center' valign='center'";
                           echo "<font class='fonte'><b>Data Emissão</b></font>";
                         echo "</td>";

                         echo "<td align='center' valign='center'";
                           echo "<font class='fonte'><b>Tipo</b></font>";
                         echo "</td>";

                         echo "<td align='left' valign='left'";
                           echo "<font class='fonte'><b>Cliente</b></font>";
                         echo "</td>";

                         echo "<td align='left' valign='left'";
                           echo "<font class='fonte'><b>Espécie</b></font>";
                         echo "</td>";

                         echo "<td align='right' valign='right'";
                           echo "<font class='fonte'><b>Qtde</b></font>";
                         echo "</td>";

                         echo "<td align='right' valign='right'";
                           echo "<font class='fonte'><b>Peso L.</b></font>";
                         echo "</td>";
                         
                         echo "<td align='right' valign='right'";
                           echo "<font class='fonte'><b>Peso B.</b></font>";
                         echo "</td>";

                       echo "</tr>";

                     $Sequencial=0;
                     $nTotal_Linhas_Impressas=0;
                     $totalVolumes = 0.000;
                     $totalPesoLiquido = 0.000;
                     $totalPesoBruto = 0.000;

                     while ($rT007=mysql_fetch_assoc($Csql_T007)) {
                        if($rT007['D022_Id']==$mT007['D022_Id']){
                            $dataEmissao=gCorrigeData($rT007['T007_Data_Emissao']);
                            $pesoLiquido= number_format($rT007['T007_Peso_Liquido'],3,',','.');
                            $pesoBruto= number_format($rT007['T007_Peso_Bruto'],3,',','.');
                            $qtdeVolumes = number_format($rT007['T007_Quantidade_volumes'],3,',','.');

                            $totalVolumes += $rT007['T007_Quantidade_volumes'];
                            $totalPesoLiquido += $rT007['T007_Peso_Liquido'];
                            $totalPesoBruto += $rT007['T007_Peso_Bruto'];

                            echo "<tr bgcolor=white height=25>";

                                echo "<td align='center' valign='center'>";
                                echo "<font class='fonte'>{$rT007['T007_Numero_Nota_Fiscal']}</font>";
                                echo "</td>";

                                echo "<td align='center' valign='center'>";
                                echo "<font class='fonte'>{$dataEmissao}</font>";
                                echo "</td>";

                                echo "<td align='center' valign='left'>";
                                echo "<font class='fonte'>{$tipoDocumento}</font>";
                                echo "</td>";

                                echo "<td align='left' valign='left'>";
                                echo "<font class='fonte'>{$rT007['D024_Nome_Empresa']}</font>";
                                echo "</td>";

                                echo "<td align='left' valign='left'>";
                                echo "<font class='fonte'>{$rT007['T007_Especie']}</font>";
                                echo "</td>";

                                echo "<td align='right' valign='right'>";
                                echo "<font class='fonte'>{$qtdeVolumes}</font>";
                                echo "</td>";

                                echo "<td align='right' valign='right'>";
                                echo "<font class='fonte'>{$pesoLiquido}</font>";
                                echo "</td>";

                                echo "<td align='right' valign='right'>";
                                echo "<font class='fonte'>{$pesoBruto}</font>";
                                echo "</td>";

                            echo "</tr>";

                            $nTotal_Linhas_Impressas++;
                        }else{
                            continue;
                        }
                     }

                     $totalVolumes = number_format( $totalVolumes,3,',','.');;
                     $totalPesoLiquido = number_format($totalPesoLiquido,3,',','.');;
                     $totalPesoBruto = number_format($totalPesoBruto,3,',','.');;

              echo "<tr bgcolor=white height=25>";
                echo "<td align='left' valign='right' colspan='5'>";
                echo "<font class='fonte'><b>Notas coletadas:</b> $nTotal_Linhas_Impressas</font>";
                echo "</td>";
                echo "<td align='right' valign='right'>";
                echo "<font class='fonte'><b>$totalVolumes</b></font>";
                echo "</td>";
                echo "<td align='right' valign='right'>";
                echo "<font class='fonte'><b>$totalPesoLiquido</b></font>";
                echo "</td>";
                echo "<td align='right' valign='right'>";
                echo "<font class='fonte'><b>$totalPesoBruto</b></font>";
                echo "</td>";
              echo "</tr>";
              echo "</table></td></tr>";       

                echo "</table>";
                

                echo "</td></tr>";

            echo "<tr bgcolor=white><td>";

            echo "<div class='grid-container'>";

              echo "<div>";
                echo "<br><p>Nome:________________________________________<br>";
                echo "<br>Documento:___________________________________<br>";
                echo "<br>Placa do Veículo:_______________________________</p><br>";
              echo "</div>";

            echo "<div>";
              echo "<table style='border-collapse:separate;border-spacing: 10px 0rem;margin:42px auto 0 auto'>";
              echo "<tr>";
                  echo "<th align='center' width='50%' class='fonte' style='border: 1px solid black;padding: 0 15px;'>Total Notas</th>";
                  echo "<th align='center' width='50%' class='fonte' style='border: 1px solid black;padding: 0 15px;'>Quantidade Total</th>";
              echo "</tr>";
              echo "<tr>";
                  echo "<td align='center' valign='center' class='fonte' style='border: 1px solid black;font-size:43px'>$nTotal_Linhas_Impressas</td>";
                  echo "<td align='center' valign='center' class='fonte' style='border: 1px solid black;font-size:43px'>$totalVolumes</td>";
              echo "</tr>";
              echo "</table>";        
            echo "</div>";


            echo "</div>";


            echo "<br><br>Assinatura motorista:_______________________________________________________________________<br><br>";
            echo "<font style='align-content:center' class=fonte>Sistema Hardness - www.hardness.com.br</font>";
            // echo "<br/>";
            mysqli_query("UPDATE T007 SET T007_Flag_Imprimir_Romaneio='N' WHERE T007_Flag_Imprimir_Romaneio='S'");
        }

    echo "</div>";

    break;

    /**
     * Cabeçalho Orçamento
     * Chamado em: est002VolumesConferencia
     */
    case 'est002VolumesConferenciaCabecalho':
      // Select
          $sql = <<<EOT
          SELECT *
          FROM T005
               left join D024 on T005_D024_Id=D024_Id
               left join D020 on D024_D020_Id=D020_Id
               left join D018 on D024_D018_Id=D018_Id
               left join D022 on T005_D022_Id=D022_Id
          WHERE (T005_Id='$r_acaoId')
  EOT;
          $resultado = mysql_query($sql);
          $row = mysql_fetch_assoc($resultado);
          
          // Registro vazios = FALTA
          foreach ($row as &$row_) {
              if (empty($row_)) { 
                  $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
              }
          }

          $dataEmissao = gCorrigeData($row['T005_Data_Emissao']);
          if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
              $D024_Nome_Empresa = $row['D024_Nome_Fantasia'];
          } else {
              $D024_Nome_Empresa = $row['D024_Nome_Empresa'];
          }
          $D020_Nome_Cidade = $row['D020_Nome_Cidade'];
          $D022_Nome_Empresa = $row['D022_Nome_Empresa'];

          // Print
          echo <<<EOT
              <div class="contentCabecalhoLayout">
                  <table class="tabela ui-state-default ui-corner-all" style="padding: 3px" >
                      <tr class="linhaDesc">
                          <td width='70px'>Nº</td>
                          <td width='auto'>Cliente</td>
                          <td width='auto'>Cidade</td>
                          <td width='70px'>Emissão</td>
                          <td width='auto'>Transportadora</td>
                      </tr>
                      <tr class="linhaVal">
                          <td>{$row['T005_Id']}</td>
                          <td>{$row['D024_Id']} - {$D024_Nome_Empresa}</td>
                          <td>{$D020_Nome_Cidade} - {$row['D018_UF']}</td>
                          <td>{$dataEmissao}</td>
                          <td>{$D022_Nome_Empresa}</td>
                      </tr>
                  </table>
              </div>
  EOT;
  
    break;

    case 'est002GridConferenciaVolumesLegenda':
			global $g;
			if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
        echo <<<EOT
          <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all" style="padding-top: 5px">
                    <tr class="linhaDesc">
                        <td width='50%'><canvas id="myCanvas" width="10" height="10" style="background: green;"></canvas><span style="margin-left: 5px">Produtos com conversão de quantidade</span></td>
                        <td width='50%'><canvas id="myCanvas" width="10" height="10" style="background: blue;"></canvas><span style="margin-left: 5px">Produtos sem conversão de quantidade</span></td>
                    </tr>
                </table>
            </div>
EOT;
			}

    break;

    case 'est002VolumesConferenciaFinalizarOutro':
      $parcial = $_REQUEST['parcial'];
      if ($parcial == 1) {
          echo <<<EOT
            <div style='margin-top: 10px'>
              <span style='font-size: 14px; margin-left: 5px; color: red'>Este pedido foi conferido de forma parcial, o saldo irá gerar um novo pedido.</span>
            </div>
EOT;
      }

    break;
}

// style="cursor:pointer;" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Cliente/Fornecedor - ID: ' + '{$row['D024_Id']}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', [930,583]);"
// style="cursor:pointer;" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Cliente/Fornecedor - ID: ' + '{$row['D024_Id']}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', [930,583]);"
