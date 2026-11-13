<?php
namespace hardness;
$g['usarClickHouse'] = true;

$resposta = array('code' => true, 'data' => array());

/**
* Ações
*/
switch($g['r'][3]) {
	case 'adicionarItemDashboard':
        $r_D132_Id = mysql_real_escape_string($r_D132_Id);
		$sql = "SELECT D132_Descricao, D132_Salvo, D132_Filtro FROM D132 WHERE D132_Id = '{$r_D132_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		$dadosSalvos = unserialize($row['D132_Salvo']);
		$r_exibir = isset($dadosSalvos['exibir']) ? $dadosSalvos['exibir'] : 'true';
		if (!isset($r_exibir) || $r_exibir == 'undefined' || empty($r_exibir)) { $r_exibir = 'grid'; }
		if ($r_exibir == 'grid') {
			$resposta['overflow'] = true;
		} else {
			$resposta['overflow'] = false;
		}
	break;

	case 'limparFiltroSalvo':
        $r_D132_Id = mysql_real_escape_string($r_D132_Id);
		
		$dir = "{$g['pathDados']}personalizacaoGrid/";
		$gridFiltroUsuario = $dir . "relatorioBI_{$r_D132_Id}-{$g['usuarioAtual']}";
		if(file_exists($gridFiltroUsuario)){
			unlink($gridFiltroUsuario);
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

    case 'visualizarIndicador':
        $r_D132_Id = mysql_real_escape_string($r_D132_Id);
        $sql = <<<EOT
SELECT D132_Descricao, D132_Salvo, D132_Filtro, D182_DivId, D182_Grid_Salvo
FROM D132
LEFT JOIN D182 ON D182_D132_Id = D132_Id AND D182_C007_Id = '{$g['usuarioAtual']}'
WHERE D132_Id = '{$r_D132_Id}'
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_array($resultado);

        if (!empty($row['D182_Grid_Salvo'])) {
            echo $row['D182_Grid_Salvo'];
            die();
        }

        // Disponibilizar $extra para a função
        $gridFiltro = array(
            'DIM002_Nome_Empresa, DIM002_Nome_Fantasia' => array('titulo' => 'Cliente/Fornecedor', 'tipo' => 'texto'),
            'DIM001_Data' => array('titulo' => 'Data Emissão', 'tipo' => 'data'),
            'DIM001_Dia' => array('titulo' => 'Data Emissão (dia)', 'tipo' => 'texto'),
            'DIM001_Mes' => array('titulo' => 'Data Emissão (mês)', 'tipo' => 'texto'),
            'DIM001_Ano' => array('titulo' => 'Data Emissão (ano)', 'tipo' => 'texto'),
            'CONCAT(DIM001_Mes, '/', DIM001_Ano)' => array('titulo' => 'Data Emissão (mês/ano)', 'tipo' => 'texto'),
            'DIM001_Dia_Da_Semana' => array('titulo' => 'Data Emissão (dia da semana)', 'tipo' => 'texto'),
            'DIM003_Estado' => array('titulo' => 'Estado', 'tipo' => 'texto'),
            'DIM003_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'DIM003_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
            //'DIM003_Endereco' => array('titulo' => 'Endereço', 'tipo' => 'texto'),
            'D4Vendedor.DIM004_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D4Vendedor.DIM004_C007_Id' => array('titulo' => 'Vendedor (id)', 'tipo' => 'texto'),
            'D4Supervisor.DIM004_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'D4Supervisor.DIM004_C007_Id' => array('titulo' => 'Supervisor (id)', 'tipo' => 'texto'),
            'DIM011_Nome_Empresa' => array('titulo' => 'Representante', 'tipo' => 'texto'),
            'DIM007_Conta' => array('titulo' => 'Conta', 'tipo' => 'texto'),
            'DIM007_Flag_Deb_Crd' => array('titulo' => 'Débido/Crédito', 'tipo' => 'texto'),
            'DIM008_D006_Flag_Entrada_Saida' => array('titulo' => 'NF Tipo (S/E)', 'tipo' => 'texto'),
            'DIM010_D006_Codigo_CFOP_e_Descricao' => array('titulo' => 'NF CFOP', 'tipo' => 'texto'),
            'DIM009_D001_Descricao_Produto' => array('titulo' => 'Produto Descrição', 'tipo' => 'texto'),
            'DIM009_D082_Marca' => array('titulo' => 'Produto Marca', 'tipo' => 'texto'),
            'DIM009_C008_Tipo' => array('titulo' => 'Produto Tipo', 'tipo' => 'texto'),
            'DIM009_D003_Nome_Linha' => array('titulo' => 'Produto Linha', 'tipo' => 'texto'),
            'DIM006_Data' => array('titulo' => 'Recebimento/Pagamento', 'tipo' => 'data'),
            'DIM005_Data' => array('titulo' => 'Entrada/Saída/Vencimento', 'tipo' => 'data'),
            'FAC001_FLAG_CANCELADO' => array('titulo' => 'Cancelado', 'tipo' => 'texto'),
            'DIM012_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'DIM013_Nome_Caixa' => array('titulo' => 'Caixa', 'tipo' => 'texto'),
            'DIM013_Nome_Banco' => array('titulo' => 'Banco', 'tipo' => 'texto'),
        );
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, md5("bi-{$r_tab}"));
        $extra = gPopularExtraBI($extra);

        $joins = "
        LEFT JOIN DIM002 ON DIM002_Id=FAC001_DIM002_Id
        LEFT JOIN DIM003 ON DIM003_Id=FAC001_DIM003_Id
        LEFT JOIN DIM004 D4Vendedor   ON FAC001_DIM004_Id_Vendedor = D4Vendedor.DIM004_Id
        LEFT JOIN DIM004 D4Supervisor ON FAC001_DIM004_Id_Supervisor = D4Supervisor.DIM004_Id
        LEFT JOIN DIM005 ON DIM005_Id=FAC001_DIM005_Id
        LEFT JOIN DIM006 ON DIM006_Id=FAC001_DIM006_Id
        LEFT JOIN DIM007 ON DIM007_Id=FAC001_DIM007_Id
        LEFT JOIN DIM008 ON DIM008_Id=FAC001_DIM008_Id
        LEFT JOIN DIM009 ON DIM009_Id=FAC001_DIM009_Id
        LEFT JOIN DIM010 ON DIM010_Id=FAC001_DIM010_Id
        LEFT JOIN DIM011 ON DIM011_Id=FAC001_DIM011_Id
        LEFT JOIN DIM012 ON DIM012_Id=FAC001_DIM012_Id
        LEFT JOIN DIM013 ON DIM013_Id=FAC001_DIM013_Id
        ";

        $codigo = $row['D132_Salvo'];
        $codigo = str_replace('<?php', '', $codigo);
        $indicador = eval($codigo);
        if (!empty($indicador['sql'])){
            $extra = str_replace('WHERE','',$extra);
            $indicador['sql'] = str_replace('WHERE',$joins.' WHERE',$indicador['sql']);
            $indicador['sql'] = gInsertExtraWhere($indicador['sql'], $extra, true);
            $indicadorSql = mysqli_query("/* ClickHouse */ {$indicador['sql']}");
            $mIndicadorSql = mysqli_fetch_array($indicadorSql);
            $valor = number_format($mIndicadorSql[0],$indicador['decimais'],',','.');
            $indicador['valor'] = str_replace('valor',$valor,$indicador['formato']);
        }
        //$indicador = $code($extra);
        $resposta = <<<EOT
            <div class="bi-indicador bi-indicador-{$indicador['classe']}">
                <div class="label" style="display: flex; justify-content: center;">
                    <div>{$indicador['nome']}</div>
                </div>
                <div class="valor" style="display: flex; justify-content: center;">
                    <div>{$indicador['valor']}</div>
                </div>
            <div>
EOT;
        // Salvar
        $D182_Grid_Salvo = mysql_real_escape_string($resposta);
        mysql_query("INSERT INTO D182 (D182_D132_Id, D182_C007_Id, D182_Grid_Salvo) VALUES('{$r_D132_Id}', '{$g['usuarioAtual']}', '{$D182_Grid_Salvo}') ON DUPLICATE KEY UPDATE D182_Grid_Salvo='{$D182_Grid_Salvo}'");
        //
        echo $resposta;
        die();
        break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
