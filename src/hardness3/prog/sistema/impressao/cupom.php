<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'romaneioestoque':
	$T005_Id = mysql_real_escape_string($r_T005_Id);
$sql = <<<EOT
SELECT 
	* 
FROM T005 
LEFT JOIN D024 ON D024_Id = T005_D024_Id
WHERE T005_Id = '{$T005_Id}'
EOT;
$resT005 = mysql_query($sql);
$rowT005 = mysql_fetch_assoc($resT005);

// cabecalho
$rowT005['D024_Nome_Empresa'] = substr($rowT005['D024_Nome_Empresa'], 0, 20);
$rowT005['D024_Nome_Empresa'] = str_pad($rowT005['D024_Nome_Empresa'], 35, ' ', STR_PAD_RIGHT);
echo <<<EOT
var dll = $('#appletmp2032PreCarregado').contents().find('applet')[0];
console.log(dll.IniciaPorta('USB'));
console.log(dll.Le_Status());
console.log(dll.AjustaLarguraPapel(80));
console.log(dll.SelecionaQualidadeImpressao(3));
console.log(dll.ConfiguraModeloImpressora(0));
dll.FormataTX('      ELÉTRICA CEIGON COMERCIO DE MATERIAIS ELÉTRICOS LTDA\\n', 1, 0, 0, 0, 1);
dll.FormataTX('                Av. João Bettega, 830 - Portão\\n', 1, 0, 0, 0, 0);
dll.FormataTX('                 Curitiba/PR - CEP: 81070000\\n', 1, 0, 0, 0, 0);
dll.FormataTX('               www.ceigon.com.br - (41) 3023-4466\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('  Cliente: {$rowT005['D024_Nome_Empresa']} Número: ', 1, 0, 0, 0, 0);
dll.FormataTX('{$rowT005['T005_Id']}\\n', 1, 0, 0, 0, 1);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('  Descrição                         Qtde    Unitário  Total\\n', 1, 0, 0, 0, 0);
dll.FormataTX('  --------------------------------------------------------------\\n', 1, 0, 0, 0, 0);

EOT;

// produtos
$sql = <<<EOT
SELECT
	*
FROM T006
left join D009 on D009_Id=T006_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D082 on D082_Id=D049_D082_Id
left join D001 on D001_Id=D049_D001_Id
WHERE
	T006_T005_Id = '{$T005_Id}'
EOT;
$resT006 = mysql_query($sql);
while($rowT006 = mysql_fetch_assoc($resT006)) {
	$rowT006['T006_Descricao_Produto'] = trim($rowT006['D001_Codigo_Barras']) . ' - ' . $rowT006['T006_Descricao_Produto'];
	$desc = $rowT006['T006_Descricao_Produto'];
	$rowT006['T006_Descricao_Produto'] = substr($rowT006['T006_Descricao_Produto'], 0, 32);
	$rowT006['T006_Descricao_Produto'] = str_pad($rowT006['T006_Descricao_Produto'], 32, ' ');
	$rowT006['T006_Quantidade'] = gCorrigeNumero($rowT006['T006_Quantidade']);
	$rowT006['T006_Quantidade'] = str_pad($rowT006['T006_Quantidade'], 7, ' ');
	$rowT006['T006_Valor_Total_Preco'] = gCorrigeNumero($rowT006['T006_Valor_Total_Preco']);
	$rowT006['T006_Valor_Preco_Sem_Desconto_Unitario'] = gCorrigeNumero($rowT006['T006_Valor_Preco_Sem_Desconto_Unitario']);
	$rowT006['T006_Valor_Preco_Sem_Desconto_Unitario'] = str_pad($rowT006['T006_Valor_Preco_Sem_Desconto_Unitario'], 9, ' ');
	echo <<<EOT
	dll.FormataTX('  {$rowT006['T006_Descricao_Produto']}  {$rowT006['T006_Quantidade']} {$rowT006['T006_Valor_Preco_Sem_Desconto_Unitario']} {$rowT006['T006_Valor_Total_Preco']}\\n', 1, 0, 0, 0, 0);
EOT;
	if (strlen($desc) > 32) {
		$tmp = substr($desc, 32, 32);
		echo "dll.FormataTX('  {$tmp}\\n', 1, 0, 0, 0, 0);";
	}
}
$rowT005['T005_Valor_Total'] = gCorrigeNumero($rowT005['T005_Valor_Total']);
echo <<<EOT
dll.FormataTX('                                               Total: ', 1, 0, 0, 0, 0);
dll.FormataTX('{$rowT005['T005_Valor_Total']}\\n', 1, 0, 0, 0, 1);
EOT;

// fim, cod barra
echo <<<EOT
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('      ----------------------             --------------------\\n', 1, 0, 0, 0, 0);
dll.FormataTX('         Assinado/Retirado                   Data Retirada\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.ConfiguraCodigoBarras(100, 1, 2, 0, 180);
dll.ImprimeCodigoBarrasCODE39("{$rowT005['T005_Id']}");
dll.FormataTX('\\n', 2, 0, 0, 0, 0);
dll.FormataTX('         Retirar a(s) mercadoria(s) acima na expedição.\\n', 1, 0, 0, 0, 1);
dll.FormataTX('       Favor conferir a(s) mercadoria(s) no ato da entrega!\\n', 1, 0, 0, 0, 1);
dll.FormataTX('                Não aceitaremos reclamações.\\n', 1, 0, 0, 0, 1);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.FormataTX('\\n', 1, 0, 0, 0, 0);
dll.AcionaGuilhotina(1);
EOT;
	break;
}
?>