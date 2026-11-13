<?php
namespace hardness;

// Autoriza a abertura da tela existente do ERP; não grava documentos.
function dashboardCotacoesDocumentoContexto($conexao, $indicador, $autorizacao, $sessao, $entrada, &$erro)
{
    $erro = '';
    if (!dashboardCotacoesHabilitado($indicador)) {
        $erro = 'A abertura de documentos está disponível no Painel de Cotações.'; return false;
    }
    if (!dashboardAgendaSessaoValida($autorizacao, $sessao)) {
        $erro = 'A sessão do ERP mudou ou expirou. Reabra o dashboard pela empresa atual do sistema.'; return false;
    }
    $rotas = array(
        'orcamentos' => array('caminho' => '/ven/ven001/content/ven001ContentEditarOrcamento/', 'tabela' => 'T003', 'permissao' => 14, 'titulo' => 'Orçamento'),
        'pedidos' => array('caminho' => '/ven/ven002/content/ven002content02/', 'tabela' => 'T005', 'permissao' => 15, 'titulo' => 'Pedido'),
        'notas' => array('caminho' => '/ven/ven003/content/ven003contentIncluirNotaFiscal/', 'tabela' => 'T007', 'permissao' => 16, 'titulo' => 'Nota fiscal')
    );
    $tipo = isset($entrada['documentoTipo']) && is_string($entrada['documentoTipo']) ? $entrada['documentoTipo'] : '';
    $id = isset($entrada['documentoId']) && is_scalar($entrada['documentoId']) ? (string) $entrada['documentoId'] : '';
    $empresa = isset($entrada['documentoEmpresa']) && is_scalar($entrada['documentoEmpresa']) ? (string) $entrada['documentoEmpresa'] : '';
    if (!isset($rotas[$tipo]) || !preg_match('/^[1-9][0-9]{0,9}$/', $id) || !preg_match('/^[1-9][0-9]{0,9}$/', $empresa)) {
        $erro = 'Selecione um documento válido na lista.'; return false;
    }
    // O parâmetro p do ERP altera a sessão global; nunca usá-lo para abrir outra empresa.
    if ((int) $empresa !== (int) $autorizacao['empresa']) {
        $erro = 'Este documento pertence a outra empresa. Selecione a empresa do documento no ERP e reabra o dashboard para acessá-lo.'; return false;
    }
    $periodo = dashboardCotacoesPeriodo($entrada, $erro, null, !empty($indicador['painelCotacoes']['carregamentoProgressivo']));
    if ($periodo === false) return false;
    $config = $indicador['painelCotacoes'];
    $configuradas = dashboardCotacoesEmpresasConfiguradas($config, $erro);
    if ($configuradas === false) return false;
    $params = array('empresa' => (int) $autorizacao['empresa'], 'usuario' => (int) $autorizacao['usuario']);
    $empresas = dashboardIndicadorConsultar($conexao, "SELECT C004_Id AS id, C004_Cnpj AS cnpj, C004_Nome_Empresa AS nome FROM C004
        WHERE C004_Id=:empresa AND C004_Flag_Ativo='S'" . dashboardCotacoesFiltroEmpresas($configuradas) . ' LIMIT 1', $params, $erro);
    if ($empresas === false) return false;
    if (!$empresas) { $erro = 'A empresa do documento não está disponível neste painel.'; return false; }
    $config['_empresas'] = dashboardCotacoesIdentificarEmpresas($empresas, $configuradas, $erro);
    if ($config['_empresas'] === false) return false;
    $rota = $rotas[$tipo];
    $params['permissao'] = $rota['permissao'];
    $params['rootSessao'] = !empty($sessao['usuarioLogadoComPermissao']) ? 1 : 0;
    $permitidos = dashboardIndicadorConsultar($conexao, "SELECT autor.C007_Id AS id FROM C007 autor
        WHERE autor.C007_Id=:usuario AND autor.C007_Ativo='S'
        AND (autor.C007_Id=63 OR autor.C007_Usuario_Suporte='S' OR :rootSessao=1 OR
            EXISTS (SELECT 1 FROM C019 JOIN C030 ON C030_C021_Id=C019_C021_Id
                WHERE C019_C007_Id=autor.C007_Id AND C019_C004_Id=:empresa
                AND C030_C029_Id=:permissao AND C030_Acesso_Liberado='S')) LIMIT 1", $params, $erro);
    if ($permitidos === false) return false;
    if (!$permitidos) { $erro = 'Seu usuário não tem acesso à tela de ' . strtolower($rota['titulo']) . ' no ERP.'; return false; }
    $params = array_merge($params, dashboardCotacoesEscopo($indicador), array('documentoId' => (int) $id,
        'dataInicio' => $periodo['inicio'], 'dataFim' => $periodo['fim']));
    $linhas = dashboardIndicadorConsultar($conexao, dashboardCotacoesSql($tipo, $config, false, true), $params, $erro);
    if ($linhas === false) return false;
    if (count($linhas) !== 1 || !empty($linhas[0]['excluido']) || (int) $linhas[0]['empresaId'] !== (int) $empresa || (int) $linhas[0]['id'] !== (int) $id) {
        $erro = 'O documento não está mais disponível no período ou no acesso liberado ao seu usuário.'; return false;
    }
    $registro = $linhas[0];
    return array('documentoId' => (int) $registro['id'], 'numero' => $registro['numero'], 'tipo' => $tipo,
        'titulo' => $rota['titulo'], 'usuario' => (int) $autorizacao['usuario'], 'empresaSessao' => (int) $autorizacao['empresa'],
        'url' => $rota['caminho'] . '?' . http_build_query(array('oneWindow' => 'true', 'acaoId' => (int) $registro['id'], 'tabela' => $rota['tabela'])));
}
