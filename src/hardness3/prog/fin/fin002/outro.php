<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'fin002CabecalhoFornecedor':
		// Select
		$sql = mysql_query("SELECT D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_Cpf,D014_Id,D014_SubConta,D154_Descricao FROM T015 LEFT JOIN D014 ON D014_Id=T015_D014_Id LEFT JOIN D154 ON D154_Id=D014_D154_Id LEFT JOIN D024 ON D024_Id=T015_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE T015_Id='{$r_acaoId}'");
		$row = mysql_fetch_assoc($sql);
		// Registro vazios = FALTA
		/*foreach ($row as &$row_) {
			$row_ = (empty($row_)) ? '<span style="color: var(--BGVermelho);">FALTA</span>' : $row_;
		}*/
		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 25);
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

		$row['D014_SubConta'] = (!empty($row['D154_Descricao'])) ? $row['D154_Descricao'] . ' - ' . $row['D014_SubConta'] : $row['D014_SubConta'];
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" style="cursor: pointer;" >
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='270px'>Conta</td>
						<td width='70px'>Código</td>
						<td width='270px'>Fornecedor</td>
						<td width='180px'>CNPJ/CPF</td>
						<td width='180px'>Cidade</td>
						<td width='80px'>Estado</td>
					</tr>
					<tr class="linhaVal">
						<td align='left' onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Projeção orçamentária', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&acaoId=' + encodeURIComponent('{$row['D014_Id']}'), [960,620]);">{$row['D014_SubConta']}</td>
						<td align='center' onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Fornecedor', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}'), [960,583]);">{$row['D024_Id']}</td>
						<td onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Fornecedor', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}'), [960,583]);">{$D024_Nome_Empresa}</td>
						<td onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Fornecedor', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}'), [960,583]);">{$row['D024_Cnpj']}{$row['D024_Cpf']}</td>
						<td onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Fornecedor', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}'), [960,583]);">{$D020_Nome_Cidade}</td>
						<td onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Fornecedor', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}'), [960,583]);">{$row['D018_UF']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'fin002ManualCSVCliente':

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
        <br/>-Ao final do documento não deve haver nenhuma linha em branco. 
        <br/>-Os campos com * são obrigatórios.
        <br/>-Preencha apenas o <strong>CPF ou o CNPJ</strong>, não sendo necessário inserir os dois. 
        <br/>-O campo <strong>EMPRESA</strong> deve ser preenchido com o ID da Empresa usuária.       
        <br/>-Informe o <strong>CPF</strong> do Cliente/Fornecedor no formato "XXX.XXX.XXX-XX".
        <br/>-Informe o <strong>CNPJ</strong> do Cliente/Fornecedor no formato "XX.XXX.XXX/XXXX-XX"          
        <br/>-O campo <strong>GRUPO</strong> deve ser preenchido conforme o nome que está no cadastro. 
        <br/>-O campo <strong>CONTA</strong> deve ser preenchido conforme o nome que está no cadastro.
        <br/>-O campo <strong>PORTADOR</strong> deve ser preenchido conforme o nome que está no cadastro.
        <br/>-O campo <strong>CENTRO CUSTO</strong> deve sempre ser preenchido com o número 1, sendo equivalente a Matriz.
        </p>
        </div>
        <table cellspacing=1px bgcolor="black">     
            <tr>  
                <td class="linhaTabelaManual" > *EMPRESA </td> 
            	<td class="linhaTabelaManual" > *CPF </td> 
                <td class="linhaTabelaManual" > *CNPJ </td> 
                <td class="linhaTabelaManual" > *GRUPO </td>     
                <td class="linhaTabelaManual" > *CONTA </td>
                <td class="linhaTabelaManual" > CODIGO DE BARRAS </td>
                <td class="linhaTabelaManual" > *DATA EMISSAO </td>
                <td class="linhaTabelaManual" > *DATA VENCIMENTO </td>
                <td class="linhaTabelaManual" > NUMERO DOCUMENTO </td>
                <td class="linhaTabelaManual" > *NUMERO DUPLICATA </td>
                <td class="linhaTabelaManual" > PORTADOR </td>
                <td class="linhaTabelaManual" > *CENTRO CUSTO </td>
                <td class="linhaTabelaManual" > *VALOR TITULO </td>
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
