<?php
/**
* Tabela D009A
*/
/*
* camposLabelForm
* $conf['camposLabelForm']['D009_Valor_Custo_Unitario'] = 'Custo x ICF';
* $conf['camposLabelForm']['D009_Valor_Preco_Tabela'] = 'Custo tabela';
*/

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/

$conf['triggers']['D009A']['updateAfter'] = <<<'EOT'
	$D009A = mysqli_query("SELECT D009A_IPT_5 FROM D009A WHERE D009A_D009_Id='{$chaveValor}'");
	$mD009A = mysqli_fetch_array($D009A);
    
	if ($mD009A['D009A_IPT_5']>0) {
		mysqli_query("update D009A left join D009 on D009_Id = D009A_D009_Id
                         set D009A_Preco_5 = if({$mD009A['D009A_IPT_5']} > 0, D009_Valor_Custo_Unitario * {$mD009A['D009A_IPT_5']},D009A_Preco_5)
                       where D009A_D009_Id = '{$chaveValor}'");
    }
EOT;