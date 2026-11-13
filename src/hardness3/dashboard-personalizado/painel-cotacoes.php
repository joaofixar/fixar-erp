<?php
namespace hardness;
require_once __DIR__ . '/cotacoes-metas.php';
require_once __DIR__ . '/analise-comercial.php';

/* Consultas compatíveis com os resumos do MCP; executadas pelo preparador do dashboard. */
function dashboardCotacoesHabilitado($indicador)
{
    return !empty($indicador['painelCotacoes']['habilitado']);
}

function dashboardCotacoesPeriodo($entrada, &$erro, $hoje = null, $ampliado = false)
{
    $hoje = $hoje ?: date('Y-m-d');
    $inicio = isset($entrada['f_dataInicio']) ? $entrada['f_dataInicio'] : date('Y-m-d', strtotime($hoje . ' -13 days'));
    $fim = isset($entrada['f_dataFim']) ? $entrada['f_dataFim'] : $hoje;
    foreach (array($inicio, $fim) as $valor) {
        if (!is_string($valor)) { $erro = 'Período inválido.'; return false; }
        $d = \DateTime::createFromFormat('!Y-m-d', $valor);
        if (!$d || $d->format('Y-m-d') !== $valor) { $erro = 'Período inválido.'; return false; }
    }
    if ($inicio > $fim || $fim > $hoje || (!$ampliado && (new \DateTime($inicio))->diff(new \DateTime($fim))->days > 365)
        || ($ampliado && $inicio < (new \DateTime($fim))->modify('-2 years')->format('Y-m-d'))) {
        $erro = $ampliado ? 'Selecione até 2 anos, sem datas futuras.' : 'Selecione até 366 dias, sem datas futuras.'; return false;
    }
    return array('inicio' => $inicio, 'fim' => $fim);
}

function dashboardCotacoesEscopo($indicador)
{
    $r = isset($indicador['_cotacoesRestricao']) ? $indicador['_cotacoesRestricao'] : false;
    return array('restrito' => $r ? (int) $r['vendedorId'] : 0,
        'equipeRestrita' => $r && isset($r['escopo']) && $r['escopo'] === 'equipe' ? 1 : 0,
        'secundario' => 0);
}

function dashboardCotacoesClausulaEscopo($campo)
{
    // A entrada do navegador jamais substitui o limite de acesso individual.
    return " AND (:restrito = 0 OR $campo = :restrito OR (:equipeRestrita = 1 AND
        v.C007_C007_Id = :restrito))";
}

function dashboardCotacoesIds($valores)
{
    return array_values(array_unique(array_filter(array_map('intval', (array) $valores), function ($id) { return $id > 0; })));
}

function dashboardCotacoesEmpresasConfiguradas($config, &$erro)
{
    if (!isset($config['empresasConfiguradas'])) return array();
    if (!is_array($config['empresasConfiguradas']) || !$config['empresasConfiguradas']) {
        $erro = 'Informe as empresas do Painel de Cotações.'; return false;
    }
    $empresas = array();
    foreach ($config['empresasConfiguradas'] as $empresa) {
        if (!is_array($empresa)) { $erro = 'Empresa inválida na configuração do painel.'; return false; }
        $id = isset($empresa['id']) ? (int) $empresa['id'] : 0;
        $cnpj = isset($empresa['cnpj']) ? preg_replace('/\D/', '', (string) $empresa['cnpj']) : '';
        if (($id <= 0 && $cnpj === '') || ($cnpj !== '' && strlen($cnpj) !== 14)) {
            $erro = 'Informe o código ou CNPJ válido de cada empresa do painel.'; return false;
        }
        $empresa['id'] = $id; $empresa['cnpj'] = $cnpj;
        $empresas[] = $empresa;
    }
    return $empresas;
}

function dashboardCotacoesFiltroEmpresas($configuradas)
{
    $condicoes = array();
    foreach ($configuradas as $empresa) {
        $partes = array();
        if ($empresa['id'] > 0) $partes[] = 'C004_Id=' . $empresa['id'];
        if ($empresa['cnpj'] !== '') $partes[] = "REPLACE(REPLACE(REPLACE(REPLACE(C004_Cnpj,'.',''),'/',''),'-',''),' ','')='" . $empresa['cnpj'] . "'";
        $condicoes[] = '(' . implode(' AND ', $partes) . ')';
    }
    return $condicoes ? ' AND (' . implode(' OR ', $condicoes) . ')' : '';
}

function dashboardCotacoesIdentificarEmpresas($empresas, $configuradas, &$erro)
{
    $resultado = array(); $identificadas = array();
    foreach ($empresas as $empresa) {
        $regra = array();
        if ($configuradas) {
            $encontradas = array_filter($configuradas, function ($c) use ($empresa) {
                return (!$c['id'] || $c['id'] === (int) $empresa['id'])
                    && ($c['cnpj'] === '' || $c['cnpj'] === preg_replace('/\D/', '', (string) $empresa['cnpj']));
            });
            if (count($encontradas) !== 1) { $erro = 'Não foi possível identificar unicamente uma empresa do painel.'; return false; }
            $indice = key($encontradas);
            if (isset($identificadas[$indice])) { $erro = 'O CNPJ configurado identifica mais de uma empresa.'; return false; }
            $identificadas[$indice] = true;
            $regra = reset($encontradas);
        }
        $empresa['nome'] = !empty($regra['rotulo']) ? $regra['rotulo'] : $empresa['nome'];
        $empresa['toneladas'] = !empty($regra['toneladas']);
        $empresa['clientesTransferencia'] = dashboardCotacoesIds(isset($regra['clientesTransferencia']) ? $regra['clientesTransferencia'] : array());
        $empresa['servicosIndustrializacao'] = dashboardCotacoesIds(isset($regra['servicosIndustrializacao']) ? $regra['servicosIndustrializacao'] : array());
        $resultado[] = $empresa;
    }
    return $resultado;
}

function dashboardCotacoesTransferencia($tabela, $config)
{
    $partes = array();
    foreach (isset($config['_empresas']) ? $config['_empresas'] : array() as $empresa) {
        $clientes = dashboardCotacoesIds($empresa['clientesTransferencia']);
        if ($clientes) $partes[] = '(d.' . $tabela . '_C004_Id=' . (int) $empresa['id'] . ' AND COALESCE(d.' . $tabela . '_D024_Id,0) IN (' . implode(',', $clientes) . '))';
    }
    return $partes ? '(' . implode(' OR ', $partes) . ')' : '0';
}

function dashboardCotacoesEquipes($config, $empresas, $vendedores, &$erro)
{
    if (!isset($config['equipes'])) return null; // Preserva a descoberta automática de outras personalizações.
    if (!is_array($config['equipes']) || !$config['equipes'] || count($config['equipes']) > 30) {
        $erro = 'Informe de uma a 30 equipes comerciais no painel.'; return false;
    }
    $resultado = array(); $ids = array(); $vinculos = array();
    $nomes = array();
    foreach ($vendedores as $v) {
        if (!empty($v['id'])) $nomes[(int) $v['id']] = $v['nome'];
        if (!empty($v['supervisorId']) && !empty($v['supervisor'])) $nomes[(int) $v['supervisorId']] = $v['supervisor'];
    }
    foreach ($config['equipes'] as $equipe) {
        if (!is_array($equipe)) { $erro = 'Equipe inválida na configuração do painel.'; return false; }
        $id = isset($equipe['id']) && is_string($equipe['id']) ? $equipe['id'] : '';
        $nome = isset($equipe['nome']) && is_string($equipe['nome']) ? trim($equipe['nome']) : '';
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $id) || in_array($id, array('all', 'none'), true) || isset($ids[$id]) || $nome === '') {
            $erro = 'Cada equipe precisa de identificador único e nome.'; return false;
        }
        $ids[$id] = true;
        $empresaId = isset($equipe['empresaId']) ? (int) $equipe['empresaId'] : 0;
        $cnpj = isset($equipe['empresaCnpj']) ? preg_replace('/\D/', '', (string) $equipe['empresaCnpj']) : '';
        if ((!$empresaId && $cnpj === '') || ($cnpj !== '' && strlen($cnpj) !== 14)) {
            $erro = 'Informe a empresa de cada equipe pelo código ou CNPJ.'; return false;
        }
        $encontradas = array_values(array_filter($empresas, function ($e) use ($empresaId, $cnpj) {
            return (!$empresaId || (int) $e['id'] === $empresaId) && ($cnpj === '' || preg_replace('/\D/', '', (string) $e['cnpj']) === $cnpj);
        }));
        if (count($encontradas) > 1) { $erro = 'A empresa da equipe não foi identificada unicamente.'; return false; }
        if (!$encontradas) continue; // Equipe de empresa não autorizada não aparece na resposta.
        $empresaId = (int) $encontradas[0]['id'];
        $supervisores = dashboardCotacoesIds(isset($equipe['supervisores']) ? $equipe['supervisores'] : array());
        foreach (isset($equipe['supervisoresNomes']) ? (array) $equipe['supervisoresNomes'] : array() as $busca) {
            if (!is_string($busca) || trim($busca) === '') { $erro = 'Nome de supervisor inválido.'; return false; }
            $achados = array_keys(array_filter($nomes, function ($nome) use ($busca) { return strcasecmp(trim($nome), trim($busca)) === 0; }));
            if (count($achados) > 1) { $erro = 'Há supervisores com o mesmo nome. Configure seus códigos na equipe.'; return false; }
            $supervisores = array_merge($supervisores, $achados);
        }
        $supervisores = dashboardCotacoesIds($supervisores);
        foreach ($supervisores as $supervisor) {
            $chave = $empresaId . ':' . $supervisor;
            if (isset($vinculos[$chave])) { $erro = 'Um supervisor não pode pertencer a duas equipes da mesma empresa.'; return false; }
            $vinculos[$chave] = $id;
        }
        $resultado[] = array('id' => $id, 'nome' => $nome, 'empresaId' => $empresaId, 'supervisores' => $supervisores);
    }
    return $resultado;
}

function dashboardCotacoesFiltroNotas()
{
    // Mesma população de hardness_sales_summary; o valor é o total da NF, com IPI e frete.
    return " AND d.T007_Flag_Entrada_Saida='S' AND d.T007_Flag_Cancelada='N'
        AND d.T007_Numero_Protocolo_Nfe IS NOT NULL AND TRIM(d.T007_Numero_Protocolo_Nfe)<>''";
}

function dashboardCotacoesSql($tipo, $config, $historico = false, $documento = false)
{
    $tabelas = array('orcamentos' => 'T003', 'pedidos' => 'T005', 'notas' => 'T007');
    $t = $tabelas[$tipo];
    $valor = "COALESCE(d.{$t}_Valor_Total,0)";
    $excluido = $tipo === 'orcamentos' ? "(COALESCE(d.T003_Flag_Status_Orcamento,'')='C')"
        : ($tipo === 'pedidos' ? '(COALESCE(d.T005_Flag_Status,0)=8)' : '0');
    $transferencia = dashboardCotacoesTransferencia($t, $config);
    $status = $tipo === 'orcamentos' ? "CASE d.T003_Flag_Status_Orcamento WHEN 'P' THEN 'Pendente' WHEN 'F' THEN 'Finalizado' WHEN 'C' THEN 'Cancelado' WHEN 'V' THEN 'Vencido' WHEN 'A' THEN 'Aprovado' WHEN 'N' THEN 'Negado' WHEN 'L' THEN 'Em análise' ELSE COALESCE(d.T003_Flag_Status_Orcamento,'') END" : ($tipo === 'pedidos' ? "COALESCE(d.T005_Nome_Status,CAST(d.T005_Flag_Status AS CHAR))" : "'Autorizada'");
    $joins = " LEFT JOIN C007 v ON v.C007_Id=d.{$t}_C007_Id_Vendedor_Interno";
    $where = " WHERE d.{$t}_C004_Id=:empresa AND d.{$t}_Data_Emissao >= :dataInicio
        AND d.{$t}_Data_Emissao < DATE_ADD(:dataFim, INTERVAL 1 DAY)" . dashboardCotacoesClausulaEscopo("d.{$t}_C007_Id_Vendedor_Interno");
    if ($documento) $where .= " AND d.{$t}_Id=:documentoId";
    if ($tipo === 'notas') $where .= dashboardCotacoesFiltroNotas();
    if ($tipo !== 'orcamentos') $where .= " AND NOT $transferencia";
    $excluir = array_values(array_unique(array_filter(array_map('intval', isset($config['clientesExcluidos']) ? $config['clientesExcluidos'] : array()), function ($id) { return $id > 0; })));
    if ($excluir) $where .= " AND COALESCE(d.{$t}_D024_Id,0) NOT IN (" . implode(',', $excluir) . ')';
    $responsavel = "COALESCE(d.{$t}_C007_Id_Vendedor_Interno,0)";
    if ($historico) {
        return "SELECT d.{$t}_C004_Id AS empresaId, DATE(d.{$t}_Data_Emissao) AS data, $responsavel AS vendedorId,
            COALESCE(d.{$t}_D021_Id,0) AS representanteId, SUM($valor) AS valor, COUNT(*) AS quantidade
            FROM $t d $joins $where
            GROUP BY d.{$t}_C004_Id,DATE(d.{$t}_Data_Emissao),$responsavel,COALESCE(d.{$t}_D021_Id,0) LIMIT 40001";
    }
    $proposta = !empty($config['clienteProposta']) ? "(SELECT COALESCE(NULLIF(p.D024_Nome_Fantasia,''),p.D024_Nome_Empresa)
        FROM {$t}A a INNER JOIN D024 p ON p.D024_Id=a.{$t}A_D024_Id_Cliente_Proposta
        WHERE a.{$t}A_{$t}_Id=d.{$t}_Id ORDER BY p.D024_Id LIMIT 1)" : 'NULL';
    $grupoProposta = !empty($config['clienteProposta']) ? "(SELECT pg.D046_Nome
        FROM {$t}A a INNER JOIN D024 p ON p.D024_Id=a.{$t}A_D024_Id_Cliente_Proposta
        LEFT JOIN D046 pg ON pg.D046_Id=p.D024_D046_Id
        WHERE a.{$t}A_{$t}_Id=d.{$t}_Id ORDER BY p.D024_Id LIMIT 1)" : 'NULL';
    $clienteId = "COALESCE(d.{$t}_D024_Id,0)";
    $joinProposta = '';
    $iaAgrupada = $tipo === 'orcamentos' && !empty($config['origemIA']) && !empty($config['atribuirClienteProposta']);
    $propostaCorrelacionada = !empty($config['propostaCorrelacionada']);
    if (!empty($config['atribuirClienteProposta'])) {
        // A população da proposta recebe o mesmo escopo do documento. IA também
        // considera extensões sem cliente; a atribuição continua no menor ID positivo.
        $propostaId = 'cliente_proposta.clientePropostaId';
        $whereProposta = strtr($where, array('d.' => 'proposta_doc.', 'v.' => 'proposta_v.'));
        $origemProposta = $iaAgrupada ? ' LEFT JOIN D201 origem_proposta ON origem_proposta.D201_Id=a.T003A_D201_Id' : '';
        $campoIA = $iaAgrupada ? ", MAX(CASE WHEN origem_proposta.D201_Chave_Sistema='MCP_IA' THEN 1 ELSE 0 END) AS origemIA" : '';
        $joinProposta = " LEFT JOIN (SELECT a.{$t}A_{$t}_Id AS documentoId,
            MIN(CASE WHEN a.{$t}A_D024_Id_Cliente_Proposta>0 THEN a.{$t}A_D024_Id_Cliente_Proposta ELSE NULL END) AS clientePropostaId $campoIA
            FROM {$t}A a INNER JOIN $t proposta_doc ON proposta_doc.{$t}_Id=a.{$t}A_{$t}_Id
            LEFT JOIN C007 proposta_v ON proposta_v.C007_Id=proposta_doc.{$t}_C007_Id_Vendedor_Interno
            $origemProposta $whereProposta
            GROUP BY a.{$t}A_{$t}_Id) AS cliente_proposta ON cliente_proposta.documentoId=d.{$t}_Id";
        if ($propostaCorrelacionada) {
            // Mesmo menor cliente positivo, para o documento já filtrado. As
            // condições de empresa, vendedor, período e transferência também
            // permanecem na subconsulta, sem reler/agrupar todos os documentos.
            $propostaId = "cliente_proposta.{$t}A_D024_Id_Cliente_Proposta";
            $condicaoProposta = preg_replace('/^\s*WHERE\s+/i', '', $where);
            $joinProposta = " LEFT JOIN {$t}A cliente_proposta ON cliente_proposta.{$t}A_Id=(
                SELECT a.{$t}A_Id FROM {$t}A a
                WHERE a.{$t}A_{$t}_Id=d.{$t}_Id AND a.{$t}A_D024_Id_Cliente_Proposta>0
                AND ($condicaoProposta)
                ORDER BY a.{$t}A_D024_Id_Cliente_Proposta,a.{$t}A_Id LIMIT 1)";
        }
        $clienteId = "COALESCE($propostaId,d.{$t}_D024_Id,0)";
        if (!empty($config['clienteProposta'])) {
            $proposta = "CASE WHEN $propostaId IS NOT NULL THEN COALESCE(NULLIF(c.D024_Nome_Fantasia,''),NULLIF(c.D024_Nome_Empresa,''),CONCAT('Cliente proposta ',$propostaId)) ELSE NULL END";
            $grupoProposta = "CASE WHEN $propostaId IS NOT NULL THEN g.D046_Nome ELSE NULL END";
        }
    }
    $ia = $tipo === 'orcamentos' && !empty($config['origemIA']) ? "EXISTS (SELECT 1 FROM T003A a INNER JOIN D201 origem ON origem.D201_Id=a.T003A_D201_Id
        WHERE a.T003A_T003_Id=d.T003_Id AND origem.D201_Chave_Sistema='MCP_IA')" : '0';
    if ($iaAgrupada && !$propostaCorrelacionada) $ia = 'COALESCE(cliente_proposta.origemIA,0)';
    $convertido = $tipo === 'orcamentos' ? "EXISTS (SELECT 1 FROM T005 pedido WHERE pedido.T005_C004_Id=d.T003_C004_Id
        AND pedido.T005_T003_Id=d.T003_Id AND COALESCE(pedido.T005_Flag_Status,0)<>8)" : '0';
    $numero = $tipo === 'notas' ? 'd.T007_Numero_Nota_Fiscal' : "d.{$t}_Id";
    $fallbackCliente = $tipo === 'orcamentos' ? "NULLIF(d.T003_Nome_Cliente,'')," : ($tipo === 'notas' ? "NULLIF(d.T007_Cliente_Razao,'')," : '');
    if (!empty($config['atribuirClienteProposta'])) {
        $fallbackOriginal = $fallbackCliente === '' ? "'Sem cliente'" : "COALESCE({$fallbackCliente}'Sem cliente')";
        $fallbackCliente = "CASE WHEN $propostaId IS NOT NULL THEN CONCAT('Cliente proposta ',$propostaId) ELSE $fallbackOriginal END,";
    }
    $analise = '';
    if (!empty($config['segmentos'])) $analise .= ', ' . dashboardAnaliseSegmentoSql($clienteId) . ' AS segmento';
    if ($tipo === 'orcamentos' && !empty($config['cancelamentos'])) {
        $analise .= ', ' . dashboardAnaliseCancelamentoSql('d') . ' AS cancelamentoReal, ' . dashboardAnaliseMotivoSql('d') . ' AS motivoCancelamento';
    }
    return "SELECT d.{$t}_C004_Id AS empresaId, d.{$t}_Id AS id, $numero AS numero, DATE(d.{$t}_Data_Emissao) AS data,
        $valor AS valor, $excluido AS excluido, $transferencia AS transferencia, $status AS status, $ia AS ia, $convertido AS convertido,
        $responsavel AS vendedorId, COALESCE(NULLIF(v.C007_Nome,''),v.C007_Primeiro_Nome,'Sem vendedor') AS vendedor,
        COALESCE(d.{$t}_D021_Id,0) AS representanteId, COALESCE(r.D021_Nome_Empresa,'Sem representante') AS representante,
        $clienteId AS clienteId, COALESCE(NULLIF(c.D024_Nome_Fantasia,''),NULLIF(c.D024_Nome_Empresa,''),{$fallbackCliente}'Sem cliente') AS cliente,
        g.D046_Nome AS grupo, $proposta AS proposta, $grupoProposta AS grupoProposta $analise
        FROM $t d $joins $joinProposta LEFT JOIN D024 c ON c.D024_Id=$clienteId
        LEFT JOIN D046 g ON g.D046_Id=c.D024_D046_Id LEFT JOIN D021 r ON r.D021_Id=d.{$t}_D021_Id
        $where ORDER BY d.{$t}_Data_Emissao,d.{$t}_Id LIMIT " . ($documento ? '1' : '20001');
}

function dashboardCotacoesSqlToneladas($tipo, $config, $historico = false)
{
    $tabelas = array('orcamentos' => array('T003', 'T004'), 'pedidos' => array('T005', 'T006'), 'notas' => array('T007', 'T008'));
    list($t, $i) = $tabelas[$tipo];
    $ids = array(); $servicos = array();
    foreach ($config['_empresas'] as $empresa) {
        if (empty($empresa['toneladas'])) continue;
        $id = (int) $empresa['id']; $ids[] = $id;
        $codigos = dashboardCotacoesIds($empresa['servicosIndustrializacao']);
        if ($codigos) $servicos[] = '(d.' . $t . '_C004_Id=' . $id . ' AND i.' . $i . '_Codigo_Produto IN (' . implode(',', $codigos) . '))';
    }
    if (!$ids) return '';
    $from = "$i i INNER JOIN $t d ON d.{$t}_Id=i.{$i}_{$t}_Id LEFT JOIN C007 v ON v.C007_Id=d.{$t}_C007_Id_Vendedor_Interno";
    if ($tipo === 'notas') {
        $unit = 'i.T008_Unidade';
        $item = "COALESCE(i.T008_Flag_Excluir,'N')<>'S'" . dashboardCotacoesFiltroNotas();
    } else {
        $from .= " LEFT JOIN D037 unidade ON unidade.D037_Id=i.{$i}_D037_Id";
        $unit = 'unidade.D037_Unidade';
        $item = "COALESCE(i.{$i}_Flag_Cancelado,'N')<>'S' AND COALESCE(i.{$i}_Flag_Excluir,2)<>1";
    }
    $scope = "d.{$t}_C004_Id IN (" . implode(',', $ids) . ')' . dashboardCotacoesClausulaEscopo("d.{$t}_C007_Id_Vendedor_Interno");
    // Descoberta por preço somente no histórico de 270 dias e no escopo autorizado.
    // Os totais monetários nunca são reduzidos por serviços de industrialização.
    // Delimita os códigos necessários; cada média ainda usa os 270 dias completos,
    // inclusive os clientes/transferências que participavam do histórico original.
    $codigosPeriodo = "SELECT DISTINCT d.{$t}_C004_Id AS empresaId, i.{$i}_Codigo_Produto AS codigo
        FROM $from WHERE $scope AND $item AND UPPER(TRIM($unit))='KG'
        AND d.{$t}_Data_Emissao>=:dataInicio AND d.{$t}_Data_Emissao<DATE_ADD(:dataFim, INTERVAL 1 DAY)";
    $history = "SELECT d.{$t}_C004_Id AS empresaId, i.{$i}_Codigo_Produto AS codigo
        FROM $from INNER JOIN ($codigosPeriodo) codigos_periodo
        ON codigos_periodo.empresaId=d.{$t}_C004_Id AND codigos_periodo.codigo=i.{$i}_Codigo_Produto
        WHERE $scope AND $item AND UPPER(TRIM($unit))='KG'
        AND d.{$t}_Data_Emissao>=:servicosInicio AND d.{$t}_Data_Emissao<DATE_ADD(:dataFim, INTERVAL 1 DAY)
        GROUP BY d.{$t}_C004_Id,i.{$i}_Codigo_Produto
        HAVING SUM(i.{$i}_Quantidade)>0 AND SUM(i.{$i}_Valor_Total_Preco)/SUM(i.{$i}_Quantidade)<1";
    $where = " WHERE $scope AND $item AND UPPER(TRIM($unit))='KG'
        AND d.{$t}_Data_Emissao>=:dataInicio AND d.{$t}_Data_Emissao<DATE_ADD(:dataFim, INTERVAL 1 DAY)
        AND NOT " . dashboardCotacoesTransferencia($t, $config) . ' AND servico.codigo IS NULL';
    if ($servicos) $where .= ' AND NOT (' . implode(' OR ', $servicos) . ')';
    $excluir = dashboardCotacoesIds(isset($config['clientesExcluidos']) ? $config['clientesExcluidos'] : array());
    if ($excluir) $where .= " AND COALESCE(d.{$t}_D024_Id,0) NOT IN (" . implode(',', $excluir) . ')';
    $chave = $historico ? "d.{$t}_C004_Id,DATE(d.{$t}_Data_Emissao),COALESCE(d.{$t}_C007_Id_Vendedor_Interno,0),COALESCE(d.{$t}_D021_Id,0)" : "d.{$t}_C004_Id,d.{$t}_Id";
    $select = $historico ? "DATE(d.{$t}_Data_Emissao) AS data, COALESCE(d.{$t}_C007_Id_Vendedor_Interno,0) AS vendedorId,
        COALESCE(d.{$t}_D021_Id,0) AS representanteId, COUNT(DISTINCT d.{$t}_Id) AS quantidade," : "d.{$t}_Id AS id,";
    return "SELECT d.{$t}_C004_Id AS empresaId, $select SUM(i.{$i}_Quantidade)/1000.0 AS valor
        FROM $from LEFT JOIN ($history) servico ON servico.empresaId=d.{$t}_C004_Id AND servico.codigo=i.{$i}_Codigo_Produto
        $where GROUP BY $chave LIMIT " . ($historico ? '40001' : '20001');
}

function dashboardCotacoesSqlVendedores()
{
    return "SELECT v.C007_Id AS id, COALESCE(NULLIF(v.C007_Nome,''),v.C007_Primeiro_Nome,'Sem nome') AS nome,
        COALESCE(v.C007_C007_Id,0) AS supervisorId, s.C007_Nome AS supervisor
        FROM C007 v LEFT JOIN C007 s ON s.C007_Id=v.C007_C007_Id
        INNER JOIN (SELECT origem.vendedorId FROM (
            SELECT C019_C007_Id AS vendedorId FROM C019 WHERE C019_C004_Id=:empresa
            UNION ALL SELECT T003_C007_Id_Vendedor_Interno AS vendedorId FROM T003
                WHERE T003_C004_Id=:empresa AND T003_Data_Emissao>=:dataInicio AND T003_Data_Emissao<DATE_ADD(:dataFim, INTERVAL 1 DAY)
            UNION ALL SELECT T005_C007_Id_Vendedor_Interno AS vendedorId FROM T005
                WHERE T005_C004_Id=:empresa AND T005_Data_Emissao>=:dataInicio AND T005_Data_Emissao<DATE_ADD(:dataFim, INTERVAL 1 DAY)
            UNION ALL SELECT T007_C007_Id_Vendedor_Interno AS vendedorId FROM T007
                WHERE T007_C004_Id=:empresa AND T007_Data_Emissao>=:dataInicio AND T007_Data_Emissao<DATE_ADD(:dataFim, INTERVAL 1 DAY)
        ) origem GROUP BY origem.vendedorId) vendedores_periodo ON vendedores_periodo.vendedorId=v.C007_Id"
        . dashboardCotacoesClausulaEscopo('v.C007_Id') . ' ORDER BY nome LIMIT 2001';
}

function dashboardCotacoesExecutar($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $publico = dashboardIndicadorDefinicaoPublica($indicador, array());
    $resposta = array('indicador' => $publico, 'card' => array('valor' => 0, 'quantidade' => 0, 'formato' => 'moeda'),
        'listagem' => array(), 'painelCotacoes' => array('habilitado' => true));
    if (empty($entrada['cotacoesDados'])) return $resposta;
    $fontesComparativo = null;
    if ($entrada['cotacoesDados'] === 'comparativo') {
        $solicitadas = isset($entrada['f_cotacoesFontes']) ? $entrada['f_cotacoesFontes'] : '';
        if (!is_string($solicitadas)) { $erro = 'Fontes do comparativo inválidas.'; return false; }
        $solicitadas = array_values(array_unique(explode(',', $solicitadas)));
        if (!$solicitadas || array_diff($solicitadas, array('orcamentos', 'pedidos', 'notas'))) {
            $erro = 'Fontes do comparativo inválidas.'; return false;
        }
        $fontesComparativo = array_values(array_intersect(array('orcamentos', 'pedidos', 'notas'), $solicitadas));
    }
    $periodo = dashboardCotacoesPeriodo($entrada, $erro, null, !empty($indicador['painelCotacoes']['carregamentoProgressivo']));
    if ($periodo === false) return false;
    $config = $indicador['painelCotacoes'];
    $configuradas = dashboardCotacoesEmpresasConfiguradas($config, $erro);
    if ($configuradas === false) return false;
    $params = array_merge(dashboardCotacoesEscopo($indicador), array('empresa' => (int) $empresa,
        'dataInicio' => $periodo['inicio'], 'dataFim' => $periodo['fim']));
    $params['usuario'] = isset($indicador['_cotacoesUsuario']) ? (int) $indicador['_cotacoesUsuario'] : 0;
    // Empresas na mesma conexão, limitadas ao cadastro de acesso do usuário (C019).
    $empresas = dashboardIndicadorConsultar($conexao, "SELECT C004_Id AS id, C004_Cnpj AS cnpj, COALESCE(NULLIF(C004_Nome_Abreviado,''),C004_Nome_Empresa) AS nome FROM C004
        WHERE (C004_Id=:empresa OR EXISTS (SELECT 1 FROM C019 WHERE C019_C004_Id=C004_Id AND C019_C007_Id=:usuario))"
        . dashboardCotacoesFiltroEmpresas($configuradas) . ' ORDER BY C004_Id LIMIT 31', $params, $erro);
    if ($empresas === false) return false;
    if (count($empresas) > 30 || !$empresas) { $erro = 'Não foi possível delimitar as empresas do painel.'; return false; }
    $empresas = dashboardCotacoesIdentificarEmpresas($empresas, $configuradas, $erro);
    if ($empresas === false) return false;
    $config['_empresas'] = $empresas;
    $ids = array_map(function ($e) { return (int) $e['id']; }, $empresas);
    $multiempresa = function ($sql) use ($ids) { return preg_replace('/=\s*:empresa\b/', ' IN (' . implode(',', $ids) . ')', $sql); };
    $historico = isset($entrada['cotacoesDados']) && $entrada['cotacoesDados'] === 'projecao';
    if ($historico) {
        $referencia = new \DateTime(substr($periodo['fim'], 0, 7) . '-01');
        $params['dataInicio'] = $referencia->modify('-24 months')->format('Y-m-d');
    }
    $params['servicosInicio'] = (new \DateTime($periodo['fim']))->modify('-269 days')->format('Y-m-d');
    $fontes = array();
    // A aba de projeção usa somente o histórico de pedidos.
    // Comparativos consultam somente as etapas usadas pelo gráfico. Cada etapa
    // mantém o mesmo SQL, escopo, regras de toneladas e validações do painel.
    $tipos = $historico ? array('pedidos') : ($fontesComparativo !== null ? $fontesComparativo : array('orcamentos', 'pedidos', 'notas'));
    foreach ($tipos as $tipo) {
        $sql = dashboardCotacoesSql($tipo, $config, $historico);
        $linhas = dashboardIndicadorConsultar($conexao, $multiempresa($sql), $params, $erro);
        if ($linhas === false) return false;
        if (!dashboardCargaAtiva() && count($linhas) > ($historico ? 40000 : 20000)) {
            $erro = 'O volume excedeu o limite do painel. Reduza o período ou solicite uma liberação por vendedor/equipe.'; return false;
        }
        $fontes[$tipo] = $linhas;
    }
    $fontesToneladas = array();
    foreach ($tipos as $tipo) {
        $sql = dashboardCotacoesSqlToneladas($tipo, $config, $historico);
        if ($sql === '') continue;
        $pesos = dashboardIndicadorConsultar($conexao, $sql, $params, $erro);
        if ($pesos === false) return false;
        if (!dashboardCargaAtiva() && count($pesos) > ($historico ? 40000 : 20000)) { $erro = 'O volume de itens excedeu o limite do painel. Reduza o período.'; return false; }
        if ($historico) { $fontesToneladas[$tipo] = $pesos; continue; }
        $porDocumento = array();
        foreach ($pesos as $peso) $porDocumento[$peso['empresaId'] . ':' . $peso['id']] = (float) $peso['valor'];
        foreach ($fontes[$tipo] as &$linha) $linha['toneladas'] = isset($porDocumento[$linha['empresaId'] . ':' . $linha['id']]) ? $porDocumento[$linha['empresaId'] . ':' . $linha['id']] : 0.0;
        unset($linha);
    }
    $rosterSql = dashboardCotacoesSqlVendedores();
    $vendedores = dashboardIndicadorConsultar($conexao, $multiempresa($rosterSql), $params, $erro);
    if ($vendedores === false) return false;
    if (count($vendedores) > 2000) { $erro = 'O cadastro excedeu o limite de 2.000 vendedores do painel.'; return false; }
    $equipes = dashboardCotacoesEquipes($config, $empresas, $vendedores, $erro);
    if ($equipes === false) return false;
    $resposta['painelCotacoes'] = array('habilitado' => true, 'periodo' => $periodo, 'hoje' => date('Y-m-d'),
        'historicoInicio' => $params['dataInicio'], 'fontes' => $fontes, 'fontesToneladas' => $fontesToneladas,
        'vendedores' => $vendedores, 'equipes' => $equipes,
        'empresas' => array_map(function ($e) { return array('id' => $e['id'], 'nome' => $e['nome'], 'toneladas' => $e['toneladas']); }, $empresas),
        'escopo' => $params['restrito'] ? ($params['equipeRestrita'] ? 'equipe' : 'vendedor') : 'todos',
        'incluirSupervisorSecundario' => (bool) $params['secundario'], 'origemIA' => !empty($config['origemIA']),
        'segmentos' => !empty($config['segmentos']), 'cancelamentos' => !empty($config['cancelamentos']),
        'calendario' => isset($config['calendario']) ? $config['calendario'] : array(), 'meta' => null);
    if ($params['usuario'] > 0) {
        $erroMetas = '';
        $metas = dashboardCotacoesMetaLer(dashboardCotacoesMetaArquivo($empresa, $params['usuario'], $indicador['id']), $erroMetas);
        $resposta['painelCotacoes']['metasUsuario'] = $metas === false ? array() : $metas;
        $resposta['painelCotacoes']['metasErro'] = $erroMetas;
    }
    return $resposta;
}
