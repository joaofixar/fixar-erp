<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$pesquisa = isset($_REQUEST['pesquisa']) ? $_REQUEST['pesquisa'] : '';

//Retira pontos (.) e traços (-) quando for pesquisa por cpf
$pesquisa = str_replace('-', '', str_replace('.', '', $pesquisa));

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageClientesProcurar">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div class="ui-block-a">
                		<a data-role="button" data-inline="true" data-transition="slide" href="./clientes.php?T005_Id={$T005_Id}" data-ajax="false">
                            Voltar
                        </a>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-inline="true" data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="btnProcurarProduto" onclick="\$.mobile.changePage( './clientesProcurar.php?T005_Id={$T005_Id}&pesquisa=' + encodeURIComponent($('#procurarCliente2').val()), { transition: 'slide'} );" data-ajax="false">
			            		Procurar
				            </a>
				        </div>
                    	<div style="width: 70%; float:right; margin-top: 14px;">
                    		<input placeholder="Digite o Nome do cliente ou Placa do Veículo ou CPF" data-type="search" class="ui-input-text ui-body-c" id="procurarCliente2">
                    	</div>
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 5%"; align="center">Código</td>
	    						<td style="width: 35%";>Cliente</td>
	    						<td style="width: 13%";>Telefone</td>
	    						<td style="width: 22%";>Cidade</td>
	    						<td style="width: 5%"; align="center">UF</td>
	    						<td style="width: 20%"; align="center">CPF/CNPJ</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

// Produtos
$sql = <<<EOT
	SELECT D085_Data, D024_Flag_Curva_ABC, D024_Id, D024_Nome_Empresa, D024_Bairro, D020_Nome_Cidade, D018_UF, substr(D021_Nome_Empresa,1,10), D046_Nome, D024_CNPJ, D024_CPF, D024_RG, D024_Data_Cadastro, Vendedor.C007_Primeiro_Nome Vendedor, Supervisor.C007_Primeiro_Nome Supervisor, D024_Flag_Pre_Cadastro, D024_Flag_Comercial, D024_Flag_Tempo, D084_Regiao, (select D087_Id from D087 where D087_D024_Id=D024_Id limit 1) D087_Id, D085_Mensagem, datediff(current_date(),D085_Data) D085_Dias, (select D013_DDD_Telefone_1 from D013 where D013_D024_Id=D024_Id and D013_Telefone_1 != '' and D013_Telefone_1 is not null ORDER BY D013_Id ASC LIMIT 1) as D013_DDD_Telefone_1,(select D013_Telefone_1 from D013 where D013_D024_Id=D024_Id and D013_Telefone_1 != '' and D013_Telefone_1 is not null ORDER BY D013_Id ASC LIMIT 1) as D013_Telefone_1 FROM D024 
	left join D020 on D020_Id=D024_D020_Id 
	left join D018 on D018_Id=D024_D018_Id 
	left join D084 on D084_Id=D020_D084_Id 
	left join D085 on D085_Id=D024_D085_Id 
	left join D046 on D046_Id=D024_D046_Id 
	left join D021 on D021_Id=D024_D021_Id 
	left join D116 on D024_Id=D116_D024_Id
	left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
	left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
	left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
	left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
	WHERE ((1=1) AND ( (D024_Id LIKE '%{$pesquisa}%') OR (D024_Bairro LIKE '%{$pesquisa}%') OR (D020_Nome_Cidade LIKE '%{$pesquisa}%') OR (D018_UF LIKE '%{$pesquisa}%') OR (D024_Nome_Empresa LIKE '%{$pesquisa}%') OR ( D024_Nome_Fantasia LIKE '%{$pesquisa}%') OR ( REPLACE(D116_Placa, '-', '') LIKE '%{$pesquisa}%') OR ( REPLACE(REPLACE(D024_Cpf, '.', ''), '-', '') LIKE '%{$pesquisa}%'))) AND D024_Flag_Ativo='S'
	{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
	GROUP BY D024_Nome_Empresa
	ORDER BY {$g['C031']['definirOrdemDeOrdenacao']}
    LIMIT 0,100
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	if(empty($row['D024_CNPJ'])){
		$documentoCliente = $row['D024_CPF'];
	}else{
		$documentoCliente = $row['D024_CNPJ'];
	}
	$row['D024_Nome_Empresa'] = substr($row['D024_Nome_Empresa'], 0, 30);
	$row['D020_Nome_Cidade'] = substr($row['D020_Nome_Cidade'], 0, 20);
	$D013_DDD_Telefone_1 = $row['D013_DDD_Telefone_1'];
	$D013_Telefone_1 = $row['D013_Telefone_1'];
	if(!empty($D013_DDD_Telefone_1)){
		$telefoneCliente = "({$D013_DDD_Telefone_1}) {$D013_Telefone_1}";
	}else{
		$telefoneCliente = "{$D013_Telefone_1}";
	}
	echo <<<EOT
		<li data-theme="c">
			<a href="./clientes.php?T005_Id={$T005_Id}&D024_Id={$row['D024_Id']}" data-ajax="false">
	    		<table style="width: 100%";>
	    			<tr>
	    				<td style="width: 5%"; align="center">{$row['D024_Id']}</td>
	    				<td style="width: 35%";>{$row['D024_Nome_Empresa']}</td>
	    				<td style="width: 13%";>{$telefoneCliente}</td>
	    				<td style="width: 22%";>{$row['D020_Nome_Cidade']}</td>
	    				<td style="width: 5%"; align="center">{$row['D018_UF']}</td>
	    				<td style="width: 20%"; align="center">{$documentoCliente}</td>
	    			</tr>
	    		</table>
		    </a>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageClientesProcurar", "pageinit");
    	$( document ).delegate("#pageClientesProcurar", "pageinit", function() {
EOT;

echo <<<EOT
    	});
    </script>
EOT;
