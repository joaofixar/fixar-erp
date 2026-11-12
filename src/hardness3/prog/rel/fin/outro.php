<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Cliente
	 * Chamado em: cad001tabCadastro
	 */
	case 'demonstrativoDRE':

		$extra = urldecode($r_extra);

		echo <<<EOT
			<style>
            .tabela {
				font-family:Helvetica, sans-serif;
				color:#666;
				font-size:12px;
				background:#eaebec;
				margin:5px;
				border:#ccc 1px solid;
				border-radius:3px;
				width:98%;
			}
			.despesa {
				background: #ededed;
				padding:10px;
				text-align:left;
			}
			.valortitulo {
				background: #ededed;
				padding:10px;
				text-align:right;
			}
			.subconta {
				padding:10px;
				border-bottom:1px solid #e0e0e0;
				background: #fafafa;
				text-align:left;
				height:8px;
				width:90%;
			}
			.valor {
				padding:10px;
				width:20%;
				border-bottom:1px solid #e0e0e0;
				background: #fafafa;
				text-align:right;
				height:8px;
			}
        	</style>

				<table class="tabela">
					<tr>
						<th class="despesa">DESCRIÇÃO</th>
						<th class="valortitulo">VALOR</th>
					</tr>
EOT;

		//Despesa Venda
		if ($r_acaoId == '(-) Despesa venda'){		
			$query = "select (sum(T019_Valor_Lancamento)*-1) as Valor, C004_Id, D014_SubConta from T019 left join D014 on D014_Id=T019_D014_Id left join C004 on C004_Id=T019_C004_Id left join D024 on D024_Id=T019_D024_Id {$extra} group by D014_Id";
			$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
			$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '1' AND C004_Id='$r_C004_Id'"));


			while ($contas = mysql_fetch_array($query)){

				$contas['Valor'] = gCorrigeNumero($contas['Valor']);
				echo <<<EOT
					<tr>
						<td class="subconta">{$contas['D014_SubConta']}</td>
						<td class="valor">{$contas['Valor']}</td>
					</tr>
EOT;
			}

			echo "</table>";

		}

		//Despesa Administrativa
		else if ($r_acaoId == '(-) Despesa administrativa'){
			$query = "select (sum(T019_Valor_Lancamento)*-1) as Valor, C004_Id, D014_SubConta from T019 left join D014 on D014_Id=T019_D014_Id left join C004 on C004_Id=T019_C004_Id left join D024 on D024_Id=T019_D024_Id {$extra} group by D014_Id";
			$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
			
			$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '2' AND C004_Id='$r_C004_Id'"));

			while ($contas = mysql_fetch_array($query)){
				$contas['Valor'] = gCorrigeNumero($contas['Valor']);
				echo <<<EOT
					<tr>
						<td class="subconta">{$contas['D014_SubConta']}</td>
						<td class="valor">{$contas['Valor']}</td>
					</tr>
EOT;
			}

			echo "</table>";

		}

		//Despesa Administrativa
		else if ($r_acaoId == '(-) Impostos'){
			$query = "select (sum(T019_Valor_Lancamento)*-1) as Valor, C004_Id, D014_SubConta from T019 left join D014 on D014_Id=T019_D014_Id left join C004 on C004_Id=T019_C004_Id left join D024 on D024_Id=T019_D024_Id {$extra} group by D014_Id";
			$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
			
			$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '4' AND C004_Id='$r_C004_Id'"));

			while ($contas = mysql_fetch_array($query)){
				$contas['Valor'] = gCorrigeNumero($contas['Valor']);
				echo <<<EOT
					<tr>
						<td class="subconta">{$contas['D014_SubConta']}</td>
						<td class="valor">{$contas['Valor']}</td>
					</tr>
EOT;
			}

			echo "</table>";

		}

		//Devolucoes
		else if ($r_acaoId == '(-) Devolucoes'){
			$query = "select (sum(T019_Valor_Lancamento)*-1) as Valor, C004_Id, D014_SubConta from T019 left join D014 on D014_Id=T019_D014_Id left join C004 on C004_Id=T019_C004_Id left join D024 on D024_Id=T019_D024_Id {$extra} group by D014_Id";
			$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
			
			$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '7' AND C004_Id='$r_C004_Id'"));

			while ($contas = mysql_fetch_array($query)){
				$contas['Valor'] = gCorrigeNumero($contas['Valor']);
				echo <<<EOT
					<tr>
						<td class="subconta">{$contas['D014_SubConta']}</td>
						<td class="valor">{$contas['Valor']}</td>
					</tr>
EOT;
			}

			echo "</table>";

		}

		//Devolucoes
		else if ($r_acaoId == '(-) Fornecedores'){
			$query = "select (sum(T019_Valor_Lancamento)*-1) as Valor, C004_Id, D014_SubConta from T019 left join D014 on D014_Id=T019_D014_Id left join C004 on C004_Id=T019_C004_Id left join D024 on D024_Id=T019_D024_Id {$extra} group by D014_Id";
			$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
			
			$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '8' AND C004_Id='$r_C004_Id'"));

			while ($contas = mysql_fetch_array($query)){
				$contas['Valor'] = gCorrigeNumero($contas['Valor']);
				echo <<<EOT
					<tr>
						<td class="subconta">{$contas['D014_SubConta']}</td>
						<td class="valor">{$contas['Valor']}</td>
					</tr>
EOT;
			}

			echo "</table>";

		}

		//Devolucoes
		else if ($r_acaoId == '(-) Investimentos'){
			$query = "select (sum(T019_Valor_Lancamento)*-1) as Valor, C004_Id, D014_SubConta from T019 left join D014 on D014_Id=T019_D014_Id left join C004 on C004_Id=T019_C004_Id left join D024 on D024_Id=T019_D024_Id {$extra} group by D014_Id";
			$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
			
			$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '5' AND C004_Id='$r_C004_Id'"));

			while ($contas = mysql_fetch_array($query)){
				$contas['Valor'] = gCorrigeNumero($contas['Valor']);
				echo <<<EOT
					<tr>
						<td class="subconta">{$contas['D014_SubConta']}</td>
						<td class="valor">{$contas['Valor']}</td>
					</tr>
EOT;
			}

			echo "</table>";

		}

		//Participação sócios
		else if ($r_acaoId == '(-) Participacao socios'){
			$query = "select IFNULL(sum(T019_Valor_Lancamento)*-1, 0) as Valor, C004_Id, D014_SubConta from T019 left join D014 on D014_Id=T019_D014_Id left join C004 on C004_Id=T019_C004_Id left join D024 on D024_Id=T019_D024_Id {$extra} group by D014_Id";
			$query = str_replace('T007_Data_Emissao', 'T019_Data_Lancamento', $query);
			$query = mysql_query(gInsertExtraWhere($query, "D014_Grupo_DRE = '3' AND C004_Id='$r_C004_Id'"));

			while ($contas = mysql_fetch_array($query)){
				$contas['Valor'] = gCorrigeNumero($contas['Valor']);
				echo <<<EOT
					<tr>
						<td class="subconta">{$contas['D014_SubConta']}</td>
						<td class="valor">{$contas['Valor']}</td>
					</tr>
EOT;
			}

			echo "</table>";

	}else{
		echo <<<EOT
			<style>
            .tabela {
				font-family:Helvetica, sans-serif;
				color:#666;
				font-size:12px;
				background:#eaebec;
				margin:5px;
				border:#ccc 1px solid;
				border-radius:3px;
				width:98%;
			}
			.alert {
				background: #ededed;
				padding:10px;
				text-align:center;
			}
        	</style>
				<table class="tabela">
					<tr>
						<th class="alert">Essa opção não possui um Demonstrativo DRE</th>
					</tr>
EOT;
	}
	break;

}
