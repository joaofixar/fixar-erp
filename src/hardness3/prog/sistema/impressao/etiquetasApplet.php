<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'notafiscal':
	$r_quantidade = empty($r_quantidade) ? 1 : $r_quantidade;

if (!isset($r_imprimir)) {
	// Conteúdo do abrirNotificação
	echo <<<EOT
<div>
<input type="text" value="1" style="width: 17px; text-align: right;" />  
<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/notafiscal/', '{$r_acaoId}', 'expedicao', $(this).parent().find('input').val());">Expedição</button> 
<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/notafiscal/', '{$r_acaoId}', 'despacho', $(this).parent().find('input').val());">Minuta de Despacho</button>
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	// Pré carrega um applet oculto - assim o alerta de restrição só será pedido um vez, e não toda vez que quiser imprimir
	impressaoPreCarregarApplet();
} </script></div>
EOT;
EOT;
} elseif ($r_imprimir == 'expedicao') {
	// Botão 'Expedição'
	$sqlT007 = <<<EOT
SELECT 
	*, D020D.D020_Nome_Cidade as D020D_D020_Nome_Cidade, D020T.D020_Nome_Cidade as D020T_D020_Nome_Cidade, D018T.D018_UF as D018T_D018_UF, D018D.D018_UF as D018D_D018_UF
FROM T007
left join D024 on D024_Id=T007_D024_Id
left join D018 as D018D on D018_Id=D024_D018_Id
left join D020 as D020D on D020_Id=D024_D020_Id
left join D006 on D006_Id=T007_D006_Id
left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
left join D021 on D021_Id=D024_D021_Id
left join D022 on D022_Id=T007_D022_Id
left join D018 as D018T on D018T.D018_Id=D022_D018_Id
left join D020 as D020T on D020T.D020_Id=D022_D020_Id
left join T005 on T005_Id=T007_T005_Id
WHERE T007_Id = '{$r_acaoId}'
EOT;
	$resT007 = mysql_query($sqlT007);
	$rowT007 = mysql_fetch_assoc($resT007);
	
	if(!empty($rowT007['T007_Quantidade_Volumes']) && $rowT007['T007_Quantidade_Volumes'] > 0){
		$rowT007['T005_Quantidade_Volumes'] = $rowT007['T007_Quantidade_Volumes'];
	}
	if (empty($rowT007['T005_Quantidade_Volumes']) && $rowT007['T005_Quantidade_Volumes'] <=0)  {
		$rowT007['T005_Quantidade_Volumes'] = 1;
	}
	for ($pacote = 1; $pacote <= $rowT007['T005_Quantidade_Volumes']; $pacote++) {
		$print = file_get_contents('outros/etiquetas/modelos/6.prn');
		
		$print = str_replace('[[COD]]', $rowT007['T007_Numero_Nota_Fiscal'], $print);
		$print = str_replace('[[DESTINATARIO]]', substr($rowT007['D024_Nome_Empresa'], 0, 35), $print);
		$print = str_replace('[[ENDERECO]]', substr($rowT007['D024_Endereco'], 0, 30), $print);
		$print = str_replace('[[T-TRANSPORTADORA]]', substr($rowT007['D022_Nome_Empresa'], 0, 30), $print);
		$print = str_replace('[[BAIRRO]]', $rowT007['D024_Bairro'], $print);
		$print = str_replace('[[T-CIDADE]]', $rowT007['D020T_D020_Nome_Cidade'], $print);
		$print = str_replace('[[T-TELEFONE]]', $rowT007['D022_Telefone_1'], $print);
		$print = str_replace('[[CEP]]', $rowT007['D024_Cep'], $print);
		$print = str_replace('[[CIDADE]]', $rowT007['D020D_D020_Nome_Cidade'], $print);
		$print = str_replace('[[TELEFONE]]', $rowT007['D024_DDD_Telefone_1'] . $rowT007['D024_Telefone_1'], $print);
		$print = str_replace('[[T-ESTADO]]', $rowT007['D018T_D018_UF'], $print);
		$print = str_replace('[[ESTADO]]', $rowT007['D018D_D018_UF'], $print);
		$print = str_replace('[[PEDIDOINTERNO]]', '', $print);
		$print = str_replace('[[PESO]]', gCorrigeNumero($rowT007['T005_Peso_Bruto'], 3), $print);
		$print = str_replace('[[VOLUME]]', str_pad($pacote, 3, "0", STR_PAD_LEFT) . '/' . str_pad($rowT007['T005_Quantidade_Volumes'], 3, "0", STR_PAD_LEFT), $print);
		$print = str_replace('[[CODBARRA]]', $rowT007['T007_Numero_Nota_Fiscal'], $print);
		
		for ($i = 1; $i <= $r_quantidade; $i++) {	
			echo trim(gCodigoEtiqueta($print));
		}
	}
} elseif ($r_imprimir == 'despacho') {
	// Botão 'Minuta de Despacho'
	$sqlT007 = <<<EOT
SELECT 
	*, 
	D020D.D020_Nome_Cidade as D020D_D020_Nome_Cidade, 
	D020T.D020_Nome_Cidade as D020T_D020_Nome_Cidade, 
	D018T.D018_UF as D018T_D018_UF, 
	D018D.D018_UF as D018D_D018_UF
FROM T007
left join D024 on D024_Id=T007_D024_Id
left join D018 as D018D on D018D.D018_Id=D024_D018_Id
left join D020 as D020D on D020D.D020_Id=D024_D020_Id
left join D006 on D006_Id=T007_D006_Id
left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
left join D021 on D021_Id=D024_D021_Id
left join D022 on D022_Id=T007_D022_Id
LEFT JOIN D018 AS D018T ON D018T.D018_ID=D022_D018_ID
LEFT JOIN D020 AS D020T ON D020T.D020_ID=D022_D020_ID
WHERE T007_Id = '{$r_acaoId}'
EOT;
	$resT007 = mysql_query($sqlT007);
	log($sqlT007);
	$rowT007 = mysql_fetch_assoc($resT007);
	
	$print = <<<EOT
              M i n u t a    d e    D e s p a c h o

Transportadora..: [[TRANSPORTADORA]]

Remetente.......: Eletrica Ceigon Ltda
                  Rua Joao Bettega 830
                  Portao - 81.070.000
                  Curitiba - PR

Destinatario....: [[CNPJ]]
                  [[DESTINATARIO]]
Area............: [[AREA]]
Endereco........: [[ENDERECO]]
Bairro..........: [[BAIRRO]]
Cidade..........: [[CIDADE]]
U.F.............: [[ESTADO]]
Fone............: [[TELEFONE]]

Notas Fiscais...: [[NFS]]
Peso............: [[PESO]]
Frete...........: [[FRETE]]
Volume..........: [[VOLUME]]

Data............: ___ / ___ / ______ Assinatura...: ___________________

EOT;
	
	$print = str_replace('[[TRANSPORTADORA]]', substr($rowT007['D022_Nome_Empresa'], 0, 30), $print);
	$print = str_replace('[[CNPJ]]', $rowT007['D024_Cnpj'], $print);
	$print = str_replace('[[DESTINATARIO]]', $rowT007['D024_Nome_Empresa'], $print);
	$print = str_replace('[[AREA]]', '', $print);
	$print = str_replace('[[ENDERECO]]', substr($rowT007['D024_Endereco'], 0, 30), $print);
	$print = str_replace('[[BAIRRO]]', $rowT007['D024_Bairro'], $print);
	$print = str_replace('[[CIDADE]]', $rowT007['D020D_D020_Nome_Cidade'], $print);
	$print = str_replace('[[ESTADO]]', $rowT007['D018D_D018_UF'], $print);
	$print = str_replace('[[TELEFONE]]', $rowT007['D024_DDD_Telefone_1'] . $rowT007['D024_Telefone_1'], $print);
	$print = str_replace('[[NFS]]', $rowT007['T007_Numero_Nota_Fiscal'], $print);
	$print = str_replace('[[PESO]]', gCorrigeNumero($rowT007['T007_Peso_Bruto'], 3), $print);
	$print = str_replace('[[FRETE]]', gCorrigeNumero($rowT007['T007_Valor_Frete']), $print);
	$print = str_replace('[[VOLUME]]', $rowT007['T007_Quantidade_Volumes'], $print);

	for ($i = 1; $i <= $r_quantidade; $i++) {	
		echo gCodigoEtiqueta($print);
	}
}
	break;

	case 'precoloja':
		$sql = base64_decode($r_sql);
		$sql = str_replace('LIMIT 0,100', '', $sql);
		if (strpos($sql, 'ORDER BY') !== false) {
			$sql = explode('ORDER BY', $sql);
			$sql = $sql[0];
		}
		$sql .= " AND D009_Flag_Preco_Loja = 'S'";
		$res = mysql_query($sql);
		if (mysql_num_rows($res) == 0) {
			//
		}
		while ($row = mysql_fetch_assoc($res)) {
			$sqlD001 = "SELECT D001_Codigo_Barras, D001_Descricao_Produto FROM D001 WHERE D001_Id = '{$row['D001_Id']}'";
			$resD001 = mysql_query($sqlD001);
			$rowD001 = mysql_fetch_assoc($resD001);

			$print = file_get_contents('outros/etiquetas/modelos/8.prn');
			$print = str_replace('[[DESC1]]', substr($rowD001['D001_Descricao_Produto'], 0, 22), $print);
			$print = str_replace('[[DESC2]]', substr($rowD001['D001_Descricao_Produto'], 22, 22), $print);
			$print = str_replace('[[DESC3]]', substr($rowD001['D001_Descricao_Produto'], 45, 22), $print);
			$print = str_replace('[[REF]]', $rowD001['D001_Codigo_Barras'], $print);
			$row['D009_Preco_Loja_Real'] = gCorrigeNumero($row['D009_Preco_Loja_Real']);
			$print = str_replace('[[VALOR]]', 'R$ '.$row['D009_Preco_Loja_Real'], $print);
			$print = str_replace('[[CODBARRA]]', $rowD001['D001_Codigo_Barras'], $print);

			echo gCodigoEtiqueta($print);
		}
	break;

	case 'produto':
		$r_quantidade = empty($r_quantidade) ? 1 : $r_quantidade;

if (!isset($r_imprimir)) {
	$dProduto = $dLocacao = $dLocacaoC = $dGondola = $dRecebimento = '';
	if (isset($r_T014_Id)) {
		$r_T014_Id = $r_acaoId;
		$sql = <<<EOT
SELECT
	D001_Id,
	T014_T066_Id,
	D004_Id,
	D001_Codigo_Barras
FROM T014
LEFT JOIN D009 ON T014_D009_Id=D009_Id 
LEFT JOIN D049 ON D049_Id=D009_D049_Id
LEFT JOIN D001 ON D001_Id=D049_D001_Id
LEFT JOIN T066 ON T066_Id=T014_T066_Id
LEFT JOIN D004 ON D004_Id=T066_D004_Id
WHERE
	T014_Id = '{$r_T014_Id}'
	AND D009_C004_Id = '{$g['empresaAtual']}'
EOT;
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$r_acaoId = $row['D001_Id'];
		if (empty($row['T014_T066_Id'])) {
			$dLocacao = $dRecebimento = ' style="opacity: 0.4;" title="Para imprimir esta etiqueta, selecione uma locação para o item." disabled';
		}
		$D004_Id = $row['D004_Id'];
	} else {
		$sql = <<<EOT
SELECT 
	D004_Id,
	D001_Codigo_Barras
FROM T066
LEFT JOIN D009 ON T066_D009_Id=D009_Id 
LEFT JOIN D049 ON D049_Id=D009_D049_Id
LEFT JOIN D001 ON D001_Id=D049_D001_Id
LEFT JOIN D004 ON D004_Id=T066_D004_Id 
WHERE 
D001_Id='{$r_acaoId}' 
AND D009_C004_Id='{$g['empresaAtual']}'
AND D004_Local != 'Loja'
LIMIT 1
EOT;
  		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$D004_Id = $row['D004_Id'];
	}
	if($g['C031']['quantidadeDigitosEAN'] != '6'){
		if(strlen($row['D001_Codigo_Barras']) > $g['C031']['quantidadeDigitosEAN']){
			echo "O código de barras deve possuir no máximo ".$g['C031']['quantidadeDigitosEAN']." caracteres.";
			break;
		}
	}else{
		if(strlen($row['D001_Codigo_Barras']) > 6){
			echo "O código de barras deve possuir no máximo 6 caracteres.";
			break;
		}
	}
  // Conteúdo do abrirNotificação
  echo <<<EOT
<div>
<input type="text" value="1" style="width: 17px; text-align: right;" />  
<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/produto/', '{$r_acaoId}', 'produto', $(this).parent().find('input').val());" {$dProduto}>Produto</button>
<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/produto/', '{$D004_Id}', 'locacao', $(this).parent().find('input').val());" {$dLocacao}>Locação</button>
<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/produto/', '{$r_acaoId}', 'locacaoc', $(this).parent().find('input').val());" {$dLocacaoC}>Locação - Caixa</button>
<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/produto/', '{$r_acaoId}', 'gondola', $(this).parent().find('input').val());" {$dGondola}>Gondola</button>
EOT;
	if (isset($r_T014_Id)) {
		echo <<<EOT
<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/produto/', '{$r_T014_Id}', 'recebimento', $(this).parent().find('input').val());" {$dRecebimento}>Recebimento</button>
EOT;
	}
	echo <<<EOT
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
  // Pré carrega um applet oculto - assim o alerta de restrição só será pedido um vez, e não toda vez que quiser imprimir
  impressaoPreCarregarApplet();
} </script></div>
EOT;
} elseif ($r_imprimir == 'produto') {
  // Impressão botão 'Produto'
  $sqlD001 = "SELECT D001_Codigo_Barras, D001_Descricao_Produto FROM D001 WHERE D001_Id = '{$r_acaoId}'";
  $resD001 = mysql_query($sqlD001);
  $rowD001 = mysql_fetch_assoc($resD001);
  
  $totEtiq = ceil($r_quantidade / 3);
  $count = 0;
  for ($nrEtiq = 1; $nrEtiq <= $totEtiq; $nrEtiq++) {
    $print = file_get_contents('outros/etiquetas/modelos/5.prn');
    for ($i = 1; $i <= 3; $i++) {
      $count++;
      if ($count > $r_quantidade) {
        $print = str_replace("[[{$i}-LINHA1]]", '', $print);
        $print = str_replace("[[{$i}-LINHA2]]", '', $print);
        $print = str_replace("[[{$i}-CODBARRA]]", '', $print);
      } else {
        $print = str_replace("[[{$i}-LINHA1]]", substr($rowD001['D001_Descricao_Produto'], 0, 20), $print);
        $print = str_replace("[[{$i}-LINHA2]]", substr($rowD001['D001_Descricao_Produto'], 20, 20), $print);
        $print = str_replace("[[{$i}-CODBARRA]]", $rowD001['D001_Codigo_Barras'], $print);
      }
    }
    echo gCodigoEtiqueta($print);
  }
} elseif ($r_imprimir == 'locacao') {
  	// Impressão botão 'Locação'
  	$print = file_get_contents('outros/etiquetas/modelos/2.prn');
  
  	$sqlD001 = <<<EOT
SELECT 
	D004_Local
FROM D004 
WHERE 
	D004_Id='{$r_acaoId}'
EOT;
  	$resD001 = mysql_query($sqlD001);
	if (mysql_num_rows($resD001) > 0) {
  		$rowD001 = mysql_fetch_assoc($resD001);
  	
		//$locacao = 'I15B010203';
		$locacao = $rowD001['D004_Local'];
  
  		$print = str_replace('[[COD]]', $locacao, $print);
  		$print = str_replace('[[LOCAL]]', substr($locacao, 0, 1), $print);
  		$print = str_replace('[[RUA]]', substr($locacao, 1, 3), $print);
  		$print = str_replace('[[VERTICAL]]', substr($locacao, 4, 2), $print);
  		$print = str_replace('[[HORIZONTAL]]', substr($locacao, 6, 2), $print);
  		$print = str_replace('[[POSICAO]]', substr($locacao, 8, 2), $print);
  		$print = str_replace('[[DATA]]', date('d/m/Y'), $print);
  		$print = str_replace('[[HORA]]', date('H:i:s'), $print);
  		$print = str_replace('[[CONFERENTE]]', $g['C007']['C007_Primeiro_Nome'], $print);

  		for ($i = 1; $i <= $r_quantidade; $i++) { 
    		echo gCodigoEtiqueta($print);
  		}
	}
} elseif ($r_imprimir == 'locacaoc') {
  	// Impressão botão 'Locação Caixa'
	$print = file_get_contents('outros/etiquetas/modelos/3.prn');
  	$sqlD001 = "SELECT D001_Codigo_Barras, D001_Descricao_Produto, C008_Id, C008_Tipo, D001_Aplicacao FROM D001 LEFT JOIN C008 ON C008_Id=D001_C008_Id WHERE D001_Id = '{$r_acaoId}'";
  	$resD001 = mysql_query($sqlD001);
	if (mysql_num_rows($resD001) > 0) {
  		$rowD001 = mysql_fetch_assoc($resD001);
  
  		$print = str_replace('[COD]', $rowD001['D001_Codigo_Barras'], $print);
  		$print = str_replace('[DESC1]', substr($rowD001['D001_Descricao_Produto'], 0, 40), $print);
  		$print = str_replace('[DESC2]', substr($rowD001['D001_Descricao_Produto'], 40, 40), $print);
		$print = str_replace('[EXTRA]', $rowD001['C008_Id'] . '    ' .  $rowD001['C008_Tipo'], $print);
		$print = str_replace('[REF]', $rowD001['D001_Aplicacao'], $print);
		$print = str_replace('[BARRA]', $rowD001['D001_Codigo_Barras'], $print);

  		for ($i = 1; $i <= $r_quantidade; $i++) { 
    		echo gCodigoEtiqueta($print);
  		}
	}
} elseif ($r_imprimir == 'gondola') {
  // Impressão botão 'Produto'
  $sqlD001 = <<<EOT
SELECT 
	D001_Codigo_Barras, D001_Descricao_Produto, D009_Preco_Loja
FROM D001
left join D049 on D049_D001_Id=D001_Id
left join D009 on D009_D049_Id=D049_Id
WHERE D001_Id = '{$r_acaoId}'
AND D009_C004_Id='{$g['empresaAtual']}'
EOT;
  $resD001 = mysql_query($sqlD001);
  $rowD001 = mysql_fetch_assoc($resD001);

  $print = file_get_contents('outros/etiquetas/modelos/8.prn');
  $print = str_replace('[[DESC1]]', substr($rowD001['D001_Descricao_Produto'], 0, 22), $print);
  $print = str_replace('[[DESC2]]', substr($rowD001['D001_Descricao_Produto'], 22, 22), $print);
  $print = str_replace('[[DESC3]]', substr($rowD001['D001_Descricao_Produto'], 45, 22), $print);
  $print = str_replace('[[REF]]', $rowD001['D001_Codigo_Barras'], $print);
  $rowD001['D009_Preco_Loja'] = gCorrigeNumero($rowD001['D009_Preco_Loja']);
  $print = str_replace('[[VALOR]]', 'R$ ' . $rowD001['D009_Preco_Loja'], $print);
  $print = str_replace('[[CODBARRA]]', $rowD001['D001_Codigo_Barras'], $print);
    
  for ($i = 1; $i <= $r_quantidade; $i++) { 
    echo gCodigoEtiqueta($print);
  }
} elseif ($r_imprimir == 'recebimento') {
  // Impressão botão 'Produto'
	$sql = <<<EOT
SELECT
	D001_Codigo_Barras, D001_Descricao_Produto, D004_Local, T014_Quantidade, D009_Quantidade_Estoque, T014_T013_Id, T014_Id
FROM T014
LEFT JOIN D009 ON T014_D009_Id=D009_Id 
LEFT JOIN D049 ON D049_Id=D009_D049_Id
LEFT JOIN D001 ON D001_Id=D049_D001_Id
LEFT JOIN T066 ON T066_Id=T014_T066_Id
LEFT JOIN D004 ON D004_Id=T066_D004_Id
WHERE
	T014_Id = '{$r_acaoId}'
	AND D009_C004_Id = '{$g['empresaAtual']}'
	AND (D004_Local != 'Loja' OR D004_Local IS NULL)
LIMIT 1
EOT;
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	
	// achar número do item
	$sql = <<<EOT
SELECT T014_Id FROM T014 WHERE T014_T013_Id='{$row['T014_T013_Id']}' ORDER BY T014_Id ASC
EOT;
	$resItem = mysql_query($sql);
	$count = 0;
	$item = 0;
	while($rowItem = mysql_fetch_assoc($resItem)) {
		$count++;
		if ($row['T014_Id'] == $rowItem['T014_Id']) {
			$item = $count;
		}
	}
  
  $print = file_get_contents('outros/etiquetas/modelos/7.prn');
  $print = str_replace('[[DESC1]]', substr($row['D001_Descricao_Produto'], 0, 32), $print);
  $print = str_replace('[[DESC2]]', substr($row['D001_Descricao_Produto'], 32, 32), $print);
  $print = str_replace('[[ITEM]]', $item, $print);
  $print = str_replace('[[QTD]]', gCorrigeNumero($row['T014_Quantidade']), $print);
  $print = str_replace('[[ESTOQUE]]', gCorrigeNumero($row['D009_Quantidade_Estoque']), $print);
  $print = str_replace('[[LOCACAO]]', $row['D004_Local'], $print);
  $print = str_replace('[[CODBARRA]]', $row['D001_Codigo_Barras'], $print);
    
  for ($i = 1; $i <= $r_quantidade; $i++) { 
    echo gCodigoEtiqueta($print);
  }
}
	break;

	case 'locacao':
		$r_quantidade = empty($r_quantidade) ? 1 : $r_quantidade;

		if (!isset($r_imprimir)) {
			$dProduto = $dLocacao = $dLocacaoC = $dGondola = $dRecebimento = '';
			  // Conteúdo do abrirNotificação
			  echo <<<EOT
		<div>
			<input type="text" value="1" style="width: 17px; text-align: right;" />  
			<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/produto/', '{$r_acaoId}', 'locacao', $(this).parent().find('input').val());">Locação</button>
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		  // Pré carrega um applet oculto - assim o alerta de restrição só será pedido um vez, e não toda vez que quiser imprimir
		  impressaoPreCarregarApplet();
		} </script></div>
EOT;
		}
	break;

	case 'pedido':

		$r_quantidade = empty($r_quantidade) ? 1 : $r_quantidade;

		if (!isset($r_imprimir)) {
			$dProduto = $dLocacao = $dLocacaoC = $dGondola = $dRecebimento = '';
			  // Conteúdo do abrirNotificação
			echo <<<EOT
			<div>
				<!-- <input type="text" value="1" style="width: 17px; text-align: right;" />-->
				<button type="button" onclick="impressaoEtiquetas(this, '/sistema/impressao/etiquetas/pedido/', '{$r_acaoId}', 'pedido', $(this).parent().find('input').val());">Pedido</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			  // Pré carrega um applet oculto - assim o alerta de restrição só será pedido um vez, e não toda vez que quiser imprimir
			  impressaoPreCarregarApplet();
			} </script></div>
EOT;
		} else {	

			$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
			$T006 = <<<EOT
				SELECT * FROM T006 
				LEFT JOIN T005 ON T005_Id=T006_T005_Id
				LEFT JOIN C004 ON C004_Id=T005_C004_Id
				LEFT JOIN D024 ON D024_Id=T005_D024_Id
				left join D037 on D037_Id=T006_D037_Id
				WHERE T006_T005_Id='{$r_acaoId}'
EOT;
			$T006 = mysql_query($T006);
			while ($mT006 = mysql_fetch_assoc($T006)) {

				$telefone = "(" . $mT006['C004_DDD_Telefone'] . ")" . $mT006['C004_Telefone'];
				$celular = "(" . $mT006['C004_DDD_Fax'] . ")" . $mT006['C004_Fax'];
				$qnt = gCorrigeNumero($mT006['T006_Quantidade']);
				$print = file_get_contents('outros/etiquetas/modelos/9.prn');
				$print = str_replace('[[EMPRESA]]', substr($mT006['C004_Nome_Abreviado'], 0, 25), $print);
				$print = str_replace('[[TELEFONE]]', $telefone, $print);
				$print = str_replace('[[CELULAR]]', $celular, $print);
				$print = str_replace('[[CLIENTE]]', substr($mT006['D024_Nome_Fantasia'], 0, 22), $print);
				$print = str_replace('[[ITEM]]', substr($mT006['T006_Descricao_Produto'], 0, 27), $print);
				$print = str_replace('[[QNT]]', $qnt, $print);
				$print = str_replace('[[UN]]', $mT006['D037_Unidade'], $print);
				$print = str_replace('[[COD]]', $mT006['T006_Codigo_Produto'], $print);
				$print = str_replace('[[OC]]', $mT006['T006_Numero_Pedido_Compra'], $print);
				echo gCodigoEtiqueta($print);
			}
		}	
	break;
}
