<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'fin001NotaPromissoria':
		// Qual template usar?
		$docNome = 'notaPromissoria.tpl';
		
		// Classes
		include 'bibliotecas/classes/FIN001.php';
		$FIN001 = new FIN001();
		
		$T002_Id = isset($r_T002_Id) ? $r_T002_Id : false;
		
		$sql = "SELECT T002_Data_Emissao_Promissoria FROM T002 WHERE T002_Id='{$T002_Id}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		if(empty($linha['T002_Data_Emissao_Promissoria'])){
			$sql = "UPDATE T002 SET T002_Data_Emissao_Promissoria=CURDATE() where T002_Id='{$T002_Id}'";
			$result = mysql_query($sql);
		}
		
		// Atribuir os elementos do template (veja documentação dentro do próprio template)
		//$g['smarty']->assign('titulo', $FIN001->getVar('PROMISSORIA_TITULO'));
		$g['smarty']->assign('numeroDuplicata', $FIN001->gerarNumeroDuplicata($T002_Id));
		$g['smarty']->assign('dataVencimento', $FIN001->gerarDiaVencimento($T002_Id));
		$g['smarty']->assign('conteudoPromissoria', $FIN001->gerarConteudoPromissoria($T002_Id));
		$g['smarty']->assign('valorPromissoria', $FIN001->gerarvalorPromissoria($T002_Id));
		// $g['smarty']->assign('tableEmpresa', $FIN001->docGerarDadosEmpresa($T156_Id));
		// $g['smarty']->assign('corpoDcto', $FIN001->docGerarCorpoDcto($T156_Id));
		// $g['smarty']->assign('rodape', $FIN001->docGerarRodape($T156_Id));
		// Template da empresa sobrescreve o templete padrão
		echo gProcessaDocumento($docNome);
	break;

	case 'fin001Carta':
		global $g;
		$C004 = mysql_query("SELECT * FROM C004 
								LEFT JOIN D018 ON D018_Id = {$g['C004']['C004_D018_Id']} 
								LEFT JOIN D020 ON D020_Id = {$g['C004']['C004_D020_Id']} 
								WHERE C004_Id = {$g['C004']['C004_Id']}");
		$C004 = mysql_fetch_array($C004);
		$T002 = mysql_query("SELECT * FROM T002 
								LEFT JOIN T007 ON T007_Id = T002_T007_Id 
								LEFT JOIN D024 ON D024_Id = T002_D024_Id
								LEFT JOIN D018 ON D018_Id = D024_D018_Id 
								LEFT JOIN D020 ON D020_Id = D024_D020_Id 
								WHERE T002_Id = '{$r_T002_Id}'");
		$T002 = mysql_fetch_array($T002);
		if(!empty($T002['T007_Id'])){
			$duplicatas = mysql_query("SELECT *,
										SUM(T002_Valor_Duplicata) as totalDuplicatas,
										GROUP_CONCAT(T002_Observacao SEPARATOR ', ') as T002_Observacao
										FROM T002
										LEFT JOIN D027 ON D027_Id = T002_D027_Id 
										WHERE T002_T007_Id = '{$T002['T007_Id']}' 
										AND D027_Portador LIKE '%HOMOLOGAÇÃO%' ");
			$duplicatas = mysql_fetch_array($duplicatas);
			$numeroDocumento = $T002['T007_Numero_Nota_Fiscal'];
		} else {
			$duplicatas = mysql_query("SELECT *,
										SUM(T002_Valor_Duplicata) as totalDuplicatas,
										GROUP_CONCAT(T002_Observacao SEPARATOR ', ') as T002_Observacao
										FROM T002
										LEFT JOIN D027 ON D027_Id = T002_D027_Id 
										WHERE T002_Id = '{$r_T002_Id}' 
										AND D027_Portador LIKE '%HOMOLOGAÇÃO%' ");
			$duplicatas = mysql_fetch_array($duplicatas);
			$numeroDocumento = $T002['T002_Numero_Duplicata'];
		}
		$D007 = mysql_query("SELECT * FROM D007 
								LEFT JOIN D062 ON D062_Id = D007_D062_Id
								WHERE D007_Id = '{$T002['T002_D007_Id']}'");
		$D007 = mysql_fetch_array($D007);
		$valorPorExtenso = gValorPorExtenso($duplicatas['totalDuplicatas']);
		$duplicatas['totalDuplicatas'] = gCorrigeNumero($duplicatas['totalDuplicatas']);
		$logo = '/hardness3/'.$g['pathDados'].'imagens/logo/'.$g['empresaAtual'].'.jpg';
		$html = <<<EOT
			<div style="width: 955px; height: 100%; margin-top: 25px; display: table; border: 1px solid #006699; margin-left: 10px;">
			<div style="width: 100%; height: 100%; font-family: Arial">
				<div style="width: 45.9%; height: 216px; border-right: 1px solid #006699; border-bottom: 1px solid #006699; float: left; text-align: center">
					<img src="{$logo}" style="margin-top: 3px;"/>
				</div>
				<div style="width: 53.95%; height: 216px; border-bottom: 1px solid #006699; float: left; font-size: 14px;">
					<div style="height: 58%; border-bottom: 1px solid #000; text-align: center; ">
						<br />{$g['C004']['C004_Nome_Empresa']}<br />
						{$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} - {$g['C004']['C004_Bairro']} - CEP. {$g['C004']['C004_Cep']} - {$C004['D020_Nome_Cidade']} - {$C004['D018_UF']} <br />
						FONEFAX: ({$g['C004']['C004_DDD_Telefone']}) {$g['C004']['C004_Telefone']}
					</div>
					<div style="height: 15%; border-bottom: 1px solid #000; padding: 2px 10px 0px 10px;">
						<span>
							CNPJ: {$g['C004']['C004_Cnpj']}
						</span>
						<span style="float: right;">
							I.E: {$g['C004']['C004_Inscricao_Estadual']}
						</span>
					</div>
					<div style="height: 27%; padding: 10px 10px 0px 10px;">
						Data de Emissão: {$T002['T007_Data_Emissao']}
					</div>
				</div>
				<div style="width: 100%; height: 511px; border-bottom: 1px solid #006699; float: left">
					<div style="width: 21%; height: 95%; border: 1px solid #000; border-radius: 5px; float: left; margin: 12px;">
						<div style=" -webkit-transform:rotate(270deg); transform:rotate(270deg); margin: 107px 0 0 -25px; width: 220%; height: 44%; text-align: center; font-family: Arial Black">
							<hr style="border: 0; background-color: #000; height: 1px; width: 90%; margin: 100px 0 -1px -10;">
							
							<span style="font-size: 12px; margin: 10px 80px 5px 30px; font-weight: bold;">Assinatura do Emitente</span>
							<div style="font-size: 12px; font-family: Arial; width: 40%; border: 1px solid #000; margin: 15px 50px 0 -25px; padding: 4px; float: left;">
								Na falta de pagamento no
								vencimento cobra-se-à juro
								de mora legal
							</div>
							<div style="font-size: 12px; font-family: Arial; width: 40%; border: 1px solid #000; margin: 15px 0px 0 0px; padding: 4px; float: left;">
								A devolução desta Duplicata
								deverá ser feita dentro do prazo
								legal
							</div>
						</div>
					</div>
					<div style="width: 74.8%; height: 75%; border: 1px solid #000; border-radius: 5px; float: left; margin: 12px 12px 0 0;">
						<div style="width: 100%; height: 40%; border-bottom: 1px solid #000;">
							<div style="width: 77%; height: 100%; border-right: 1px solid #000; float: left; font-size: 22px; text-align: center;">
								<div style="width: 100%; height: 25%; border-bottom: 1px solid #000; font-size: 13px;">
									<div style="width: 23%; height: 100%; border-right: 1px solid #000; float: left;">
										<br />Fatura Nº
									</div>
									<div style="width: 50%; height: 100%; border-right: 1px solid #000; float: left">
										Fatura/Duplicata<br />
										Valor R$
									</div>
									<div style="width: 25%; height: 100%; float: left">
										Duplicata<br />Nº de Ordem
									</div>
								</div>
								<div style="width: 100%; height: 37.5%; border-bottom: 1px solid #000; font-size: 14px;">
									<div style="width: 23%; height: 100%; border-right: 1px solid #000; float: left;">
										<br />{$numeroDocumento}
									</div>
									<div style="width: 50%; height: 100%; border-right: 1px solid #000; float: left;">
										<br />{$duplicatas['totalDuplicatas']}
									</div>
									<div style="width: 25%; height: 100%; float: left">
										<br />{$numeroDocumento}/{$T002['T002_Total_Parcelas']}
									</div>
								</div>
								<div style="width: 100%; height: 37.5%; font-size: 14px;">
									<div style="width: 99%; height 49%; float:left; padding: 5px;">
										<span style="float: left">Preço a Vista:</span><span style="float:left; margin-left: 220px;">Acrescimo</span>
									</div>
									<div style="width: 99%; height 49%; float:left; padding: 5px;">
										<span style="float: left">Descontos Especiais de</span><span style="float:left; margin-left: 155px;">%</span><span style="float:left; margin-left: 100px;">Até</span>
									</div>
								</div>
							</div>
							<div style="width: 22%; height: 100%; float: left; text-align: center; font-size: 12px; margin-top: 5px;">
								para uso da instituição financeira
							</div>
						</div>
						<div style="width: 100%; height: 30%; border-bottom: 1px solid #000; font-size: 14px;">
							<div style="padding: 8px;">
								<div style="float: left; width: 20%;">Nome do Sacado:</div>				<div style="float: left; width: 80%;"">{$T002['D024_Nome_Empresa']}</div>
								
								<div style="float: left; width: 20%;">Endereço:</div> 					<div style="float: left; width: 50%;">{$T002['D024_Endereco']}, {$T002['D024_Numero']} - {$T002['D024_Complemento']}</div>
								<div style="float: left; width: 5%;">CEP:</div> 						<div style="float: left; width: 25%;">{$T002['D024_Cep']}</div><br />
								
								<div style="float: left; width: 20%;">Município:</div> 					<div style="float: left; width: 35%;">{$T002['D020_Nome_Cidade']}</div>
								<div style="float: left; width: 7%;">Bairro:</div> 						<div style="float: left; width: 25%;"">{$T002['D024_Bairro']}</div>
								<div style="float: left; width: 7%;">Estado:</div> 					<div style="float: left; width: 3%;">{$T002['D018_UF']}</div>
								
								<div style="float: left; width: 100%;">Praça:</div><br />
								<div style="float: left; width: 20%;">CNPJ/CPF:</div> 					<div style="float: left; width: 40%;">{$T002['D024_Cnpj']}{$T002['D024_Cpf']}</div>
								<div style="float: left; width: 15%;">Insc. Estadual:</div> 			<div style="float: left; width: 18%;">{$T002['D024_Inscricao_Estadual']}</div>
							</div>
						</div>
						<div style="width: 100%; height: 30%;">
							<div style="width: 22%; height: 75%; border-right: 1px solid #000; float: left; font-size: 20px; text-align: center; font-family: Arial Black; padding-top: 30px; font-weight: bold;">
								VALOR POR EXTENSO
							</div>
							<div style="width: 77%; height: 99%; float: left;">
								<div style="margin: 40px 10px">
									{$valorPorExtenso}
								</div>
							</div>
						</div>
					</div>
					<div style="width: 73.2%; height: 20%; float: left; font-size: 13px;">
						Reconheço(emos) a exatidão desta DUPLICATA MERCANTIL na importância acima que pagarei(emos) à {$g['C004']['C004_Nome_Empresa']}, ou a sua ordem na praça e vencimentos
						<div style="width: 30%; height: 70%; float: left; text-align: center; padding-top: 12px; font-size: 14px;">
						Data do Aceite<br /><br />
						Em____/____/____
						</div>
						<div style="width: 69%; height: 70%; float: left; text-align: center;">
							<hr style="background-color: #000; height: 1px; border: 0px; width: 90%; margin-top: 35px; margin-bottom: -1px;">Assinatura do Sacado
						</div>
					</div>
				</div>
				<div style="width: 96%; border-bottom: 1px solid #006699; float: left; padding: 2px 15px 10px 25px; font-size: 13px;">
					<div style="display: block;">
						<div style="width: 100%; text-align: center; margin-bottom: 10px;">
							PARCELAMENTO
						</div>	
						<div style="width: 100px; float: left; text-align: center;">
							Parcela
						</div>
						<div style="width: 100px; float: left; text-align: center;">
							Venc. Dia
						</div>
						<div style="width: 250px; float: left">
							Valor R$
						</div>
EOT;
		$sqlTitulos = mysql_query("SELECT * FROM T002 LEFT JOIN D027 ON D027_Id = T002_D027_Id WHERE T002_Numero_Documento = '{$T002['T002_Numero_Documento']}' and T002_Data_Emissao = '{$T002['T002_Data_Emissao']}' AND T002_D024_Id = '{$T002['T002_D024_Id']}' AND D027_Portador LIKE '%HOMOLOGAÇÃO%' GROUP BY T002_Id");
		if(mysql_num_rows($sqlTitulos) > 1){
			$html .= <<<EOT
						<div style="width: 100px; float: left; text-align: center;">
							Parcela
						</div>
						<div style="width: 100px; float: left; text-align: center;">
							Venc. Dia
						</div>
						<div style="width: 250px; float: left">
							Valor R$
						</div>
EOT;
		} else {
			$html .= '<div style="width: 550px; clear: left;"></div>';
		}
		while($titulos = mysql_fetch_array($sqlTitulos)){
			$titulos['T002_Data_Vencimento'] = gCorrigeData($titulos['T002_Data_Vencimento']);
			$titulos['T002_Valor_Duplicata'] = gCorrigeNumero($titulos['T002_Valor_Duplicata']);
			$html .= <<<EOT
						<div style="width: 100px; float: left; text-align: center;">
							{$titulos['T002_Numero_Parcela']}
						</div>
						<div style="width: 100px; float: left; text-align: center;">
							{$titulos['T002_Data_Vencimento']}
						</div>
						<div style="width: 250px; float: left;">
							{$titulos['T002_Valor_Duplicata']}
						</div>
EOT;
		}
	$html .= <<<EOT
					</div>
				</div>
				<div style="width: 100%; height: 135px; float: left">
					<div style="height: 80%; width: 45%; border: 1px solid #000; border-radius: 5px; float: left; margin: 12px 8px 8px 8px;">
						<div style="float: left; width: 99%; height: 10%; font-size: 10px; font-style: italic; font-weight: bold; margin: 2px 0 0 2px;">
							INTRUÇÕES DE COBRANÇA
						</div>
						<div style="float: left; width: 99%; height: 89%; font-size: 14px; padding: 8px 5px;">
							{$D007['D062_Nome_Banco']}:<br />
							AG: {$D007['D007_Numero_Agencia']}-{$D007['D007_Digito_Verificador_Agencia']} CONTA: {$D007['D007_Numero_Conta']}-{$D007['D007_Digito_Verificador_Conta']}<br />
							FAVORECIDO: {$D007['D007_Nome_Conta']}<br />
							OBS: {$D007['D007_Observacao']}
						</div>
					</div>
					<div style="height: 80%; width: 52%; border: 1px solid #000; border-radius: 5px; float: left; margin:  12px 8px 8px 0;">
						<div style="float: left; width: 99%; height: 10%; font-size: 13px; margin: 2px 0 0 2px;">
							RECEBIMENTO REF. AO PED.: {$T002['T007_Numero_Nota_Fiscal']}<br />
							{$duplicatas['T002_Observacao']}
						</div>
					</div>
				</div>
			</div>
		</div>
EOT;
			echo $html;
	break;

	case 'fin001CartaPDF':
		$r_link = '/fin/fin001/outro/fin001Carta/?ajax=true&T002_Id='.$r_T002_Id;
		$dir = $g['pathDados'] . 'pdf/';
		is_dir($dir) or mkdir($dir, 0777, true);
		$filename = 'cobranca_'.$r_T002_Id.'.pdf';
		$file = $dir . $filename;
		$fileFull = $g['pathRaiz'] . '/' . $file;
		
		$r_link = str_replace(' ', '+', $r_link);
		shell_exec("xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");

		echo <<<EOT
			<script type="text/javascript">
				window.location = "/hardness3/{$file}";
				//nova_janela('/hardness3/{$file}',20,20,900,700,'no','no','no','no','yes','yes',0);
			</script>
EOT;
	break;

	case 'fin001CabecalhoCliente':
		// Select
		$sql = mysql_query("SELECT T002_Numero_Duplicata, D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_Cpf FROM T002 LEFT JOIN D024 ON D024_Id=T002_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE T002_Id='{$r_acaoId}'");
		$row = mysql_fetch_assoc($sql);
		// Registro vazios = FALTA
		/*foreach ($row as &$row_) {
			$row_ = (empty($row_)) ? '<span style="color: var(--BGVermelho);">FALTA</span>' : $row_;
		}*/
		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 50);
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" style="cursor: pointer;" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cliente', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}'), [960,583]);">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='50px'>Dúplicata</td>
						<td width='30px'>Código</td>
						<td width='370px'>Cliente</td>
						<td width='180px'>Cnpj/Cpf</td>
						<td width='180px'>Cidade</td>
						<td width='80px'>Estado</td>
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['T002_Numero_Duplicata']}</td>
						<td align='center'>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$row['D024_Cnpj']}{$row['D024_Cpf']}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'fin001CabecalhoRetorno':
		global $g;
        $resultado = mysql_query("SELECT * FROM T037 LEFT JOIN D007 ON D007_Id=T037_D007_Id WHERE T037_Id = '{$r_T037_Id}'");
        $row = mysql_fetch_assoc($resultado);
        $row['T037_Data_Processamento'] = gCorrigeData($row['T037_Data_Processamento']);
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc" {$urlCadastroCliente}>
                        <td min-width='10%' style="padding-left: 10px">Id</td>
                        <td min-width='10%'>Data</td>
                        <td min-width='40%'>Arquivo</td>
                        <td min-width='10%'>Títulos</td>
                        <td min-width='30%'>Banco</td>
                    </tr>
                    <tr style="font-size:1.1em; font-weight:bold;">
                        <td style='padding:1px 10px 4px 10px'>{$row['T037_Id']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['T037_Data_Processamento']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['T037_Nome_Arquivo']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['T037_Numero_Titulos']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D007_Nome_Banco']}</td>
                    </tr>
                </table>
            </div>
EOT;

	break;

	case 'fin001ManualCSVCliente':

        echo <<<EOT
        
        <style>
            .tabelaManual{
                border:1px solid black;
                border-colapse: colapse;    
                cellspace="10";
                font-size:20px;
                padding:10px 15px;
                background-color:#000;
                text-align:left;
            }
            .LinhaReferencia{
                text-align:center;
                background: rgba(76, 175, 80, 0);
                padding:0px 20px 0px 27px;
            }
            .linhaTabelaManual{               
                font-size:15px;
                padding:10px 15px;
                background-color:#FFF;
                text-align:center;
                

            }   
        </style>

        <div style="padding-left:10px;">
        <h2>Modelo de documento CSV</h2>
        <h3>Observações:</h3>
        <p style="font-size:12px">-Abaixo está o formato do modelo do documento para a importação!
        <br/>-O documento deve ter <strong>14 colunas</strong> ao todo, caso seja necessário modificar o número de colunas para a importação, contactar o suporte técnico.
        <br/>-Os nomes das colunas devem estar necessariamente na primeira linha(1) do documento e ter a mesma grafia do documento modelo, elas devem estar em <strong>MAIÚSCULO</strong>.
        <br/>-A extensão do documento deve ser CSV (Delimitado por vírgulas)(*.csv)
        <br/>-Preencha apenas o <strong>CPF ou o CNPJ</strong>, não sendo necessário inserir os dois.
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos com * são obrigatórios. 
        <br/>-O campo <strong>EMPRESA</strong> deve ser preenchido com o ID da Empresa usuária.      
        <br/>-Informe o <strong>CPF</strong> do Cliente/Fornecedor no formato "XXX.XXX.XXX-XX".
        <br/>-Informe o <strong>CNPJ</strong> do Cliente/Fornecedor no formato "XX.XXX.XXX/XXXX-XX"        
        <br/>-O campo <strong>*GRUPO</strong> deve ser preenchido conforme o nome que está no cadastro. 
        <br/>-O campo <strong>*CONTA</strong> deve ser preenchido conforme o nome que está no cadastro.
        <br/>-O campo <strong>*STATUS</strong> deve ser preenchido com um número entre 1 a 3, sendo: 1-Normal ; 2-Perdido ; 3-Cobrança. 
        <br/>-O campo <strong>*PORTADOR</strong> deve ser preenchido conforme o nome que está no cadastro.
        </p>
        </div>
        <table cellspacing=1px bgcolor="black">     
            <tr>  
            	<td class="linhaTabelaManual" > *Empresa </td>      
                <td class="linhaTabelaManual" > *CPF </td>
                <td class="linhaTabelaManual" > *CNPJ </td>
                <td class="linhaTabelaManual" > *GRUPO </td>
                <td class="linhaTabelaManual" > *CONTA </td>
                <td class="linhaTabelaManual" > COMPLEMENTO </td>
                <td class="linhaTabelaManual" > *DATA EMISSAO </td>
                <td class="linhaTabelaManual" > *DATA VENCIMENTO </td>
                <td class="linhaTabelaManual" > NUMERO DOCUMENTO </td>
                <td class="linhaTabelaManual" > *NUMERO DUPLICATA </td>
                <td class="linhaTabelaManual" > STATUS </td>
                <td class="linhaTabelaManual" > *PORTADOR </td>
                <td class="linhaTabelaManual" > *VALOR DUPLICATA </td>
                <td class="linhaTabelaManual" > OBSERVACAO </td>
                
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
               
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                
            </tr>
            <tr>            
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
               
            </tr>
            <tr>        
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
                <td class="linhaTabelaManual">---</td>
               
            </tr>   
        </table>
EOT;
 
    break;
}
