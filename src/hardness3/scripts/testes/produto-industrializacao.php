<?php
/** php scripts/testes/produto-industrializacao.php
 * Exercita os formulários, callbacks, trigger de pedido e custo reais com dados
 * simulados. Não carrega o bootstrap nem conecta a uma base de cliente.
 */
namespace hardness;

if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL);
set_error_handler(function ($nivel, $mensagem) {
    if (error_reporting() & $nivel) { throw new \RuntimeException($mensagem); }
});
$raiz = dirname(__DIR__, 2);
chdir($raiz);
$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
function fonte($arquivo) {
    global $raiz;
    return file_get_contents($raiz . '/' . $arquivo);
}
function trecho($texto, $inicio, $fim) {
    $posicao = strpos($texto, $inicio);
    conferir($posicao !== false, 'Início do trecho não localizado: ' . $inicio);
    $final = strpos($texto, $fim, $posicao + strlen($inicio));
    conferir($final !== false, 'Fim do trecho não localizado: ' . $fim);
    return substr($texto, $posicao, $final - $posicao);
}

// Avalia as definições reais dos selects, preservando ordem e valor persistido.
foreach (['prog/cad/cad002/form.php', 'prog/cad/cad020/form.php'] as $arquivo) {
    preg_match_all("~'D049_Flag_Tipo' =>\s*array\([^\r\n]*?'select'\s*=>\s*(array\(.*?\n\s*\)),\s*\)~s", fonte($arquivo), $selects);
    conferir(count($selects[1]) === 3, 'Formulários de produto/marca ausentes: ' . $arquivo);
    foreach ($selects[1] as $definicao) {
        $opcoes = eval('return ' . $definicao . ';');
        $valores = array_values(array_filter(array_column($opcoes, 'value')));
        conferir($valores === ['CP', 'PD', 'MT', 'MD', 'IN'], 'Industrialização deve ser a quinta opção');
        conferir(end($opcoes)['title'] === 'INDUSTRIALIZAÇÃO', 'Nome da opção incorreto');
    }
}
foreach (['CP'=>'COMPRADO', 'PD'=>'PRODUZIDO', 'MT'=>'MONTADO', 'MD'=>'MODIFICADO', 'IN'=>'INDUSTRIALIZAÇÃO'] as $tipo=>$nome) {
    $row = ['tipo_produto'=>$tipo];
    include $raiz . '/bibliotecas/funcoesCallback/sql/validaCampoTipoProduto.php';
    conferir($row['tipo_produto'] === $nome, 'Descrição do tipo incorreta: ' . $tipo);
}

// Executa a construção real do botão; os outros tipos continuam sem Engenharia.
$botao = trecho(fonte('prog/cad/cad002/content.php'), "if(in_array(\$res['D049_Flag_Tipo']", '$botoesEsq');
foreach (['CP', 'PD', 'MT', 'MD', 'IN', ''] as $tipo) {
    $res = ['D049_Flag_Tipo'=>$tipo]; $botoesDir = '';
    eval($botao);
    conferir((strpos($botoesDir, '>Engenharia</button>') !== false) === in_array($tipo, ['PD', 'IN'], true), 'Botão Engenharia: ' . $tipo);
}

// Funções mysql locais: toda chamada precisa consumir uma resposta simulada.
class LinhasIndustrializacao {
    public $linhas;
    public function __construct($linhas) { $this->linhas = $linhas; }
}
$consultas = []; $executado = []; $erroAtual = '';
function mysql_query($sql) {
    global $consultas, $executado, $erroAtual, $g;
    $executado[] = $sql;
    conferir(count($consultas) > 0, 'Consulta sem simulação: ' . $sql);
    $simulacao = array_shift($consultas);
    list($esperado, $resposta) = $simulacao;
    conferir(strpos($sql, $esperado) !== false, 'Consulta inesperada, esperava: ' . $esperado);
    $erroAtual = $simulacao[2] ?? '';
    if (isset($simulacao[3])) { $g['mysqlLastId'] = $simulacao[3]; }
    return is_array($resposta) ? new LinhasIndustrializacao($resposta) : $resposta;
}
function mysql_fetch_array($resultado) { return array_shift($resultado->linhas); }
function mysql_fetch_assoc($resultado) { return mysql_fetch_array($resultado); }
function mysql_num_rows($resultado) { return count($resultado->linhas); }
function mysqli_query($sql) { return mysql_query($sql); }
function mysql_error() { global $erroAtual; return $erroAtual; }
function mysql_insert_id() { global $g; return $g['mysqlLastId']; }
function mysql_real_escape_string($valor) { return addslashes((string) $valor); }
function gCorrigeNumeroInverte($valor) { return (float) str_replace(',', '.', str_replace('.', '', (string) $valor)); }
function gCorrigeDataInverte($valor) { return $valor; }
function log($mensagem) {}
function gCorrigeNumero($valor, $casas = 2) { return number_format($valor, $casas, ',', '.'); }

// Descrição com componentes e aviso de engenharia ausente nas três telas.
foreach (['009', '017', '019'] as $programa) {
    $nome = 'est' . $programa . 'DescricaoProduto';
    $codigo = trecho(fonte('prog/est/est' . $programa . '/grid_func-callback.php'), 'function ' . $nome . '(', "\nfunction ");
    eval('namespace hardness; ' . $codigo);
    $callback = __NAMESPACE__ . '\\' . $nome;
    foreach ([[], [['D001_Codigo_Produto'=>'MP-1', 'D001_Descricao_Produto'=>'Matéria-prima', 'T261_Quantidade'=>2]]] as $componentes) {
        $resultados = [];
        foreach (['CP', 'PD', 'MT', 'MD', 'IN'] as $tipo) {
            $g = ['empresaAtual'=>1];
            $array = ['campoAtual'=>'Produto', 'todosCampos'=>['D049_Flag_Tipo'=>$tipo, 'T006_Id'=>10, 'Falta'=>3]];
            $consultas = in_array($tipo, ['PD', 'IN'], true) ? [['FROM T261', $componentes]] : [];
            $resultados[$tipo] = $callback($array);
            conferir(count($consultas) === 0, 'Composição não consultada: ' . $tipo);
        }
        conferir($resultados['IN'] === $resultados['PD'], 'Composição diferente de Produzido');
        conferir(strpos($resultados['IN'], $componentes ? 'Matéria-prima' : 'Falta engenharia do produto') !== false, 'Composição/aviso ausente');
        foreach (['CP', 'MT', 'MD'] as $tipo) {
            conferir($resultados[$tipo] === 'Produto', 'Comportamento alterado: ' . $tipo);
        }
    }
}

// A trigger real deve copiar a engenharia para o mesmo item do pedido.
class VEN002 {
    public function copiarEngenhariaProduto($id) { global $copias; $copias[] = $id; }
}
$g = ['C031'=>['usarDescontoProdutos'=>'S']]; $conf = [];
include $raiz . '/configuracoes/T006.php';
$trigger = trecho($conf['triggers']['T006']['insertAfter'], "if(in_array(\$mT006['D049_Flag_Tipo']", '$C007=');
// Isola apenas o carregamento da dependência, substituída pelo colaborador acima.
$trigger = preg_replace('/require_once\([^;]+;/', '', $trigger);
foreach (['CP', 'PD', 'MT', 'MD', 'IN'] as $tipo) {
    $mT006 = ['D049_Flag_Tipo'=>$tipo]; $chaveValor = 42; $copias = [];
    eval('namespace hardness; ' . $trigger);
    conferir($copias === (in_array($tipo, ['PD', 'IN'], true) ? [42] : []), 'Cópia de engenharia no pedido: ' . $tipo);
}

// Orçamentos marcados para comprar devem entrar na análise do PCP.
$arquivoPreco = 'bibliotecas/funcoesCallback/campoPrecoTabelaOrcamento.php';
require_once $raiz . '/' . $arquivoPreco;
preg_match_all('/\$array\[\x27todosCampos\x27\]\[\x27([^\x27]+)\x27\]/', fonte($arquivoPreco), $campos);
$itemPreco = array_fill_keys($campos[1], 0);
$g = ['divId'=>'teste', 'divIdRoot'=>'raiz', 'divIdParent'=>'pai', 'c029Ids'=>[],
    'C031'=>['desabilitarCotacaoEmergencia'=>'S']];
foreach (['S', 'N'] as $comprar) {
    foreach (['CP', 'PD', 'MT', 'MD', 'IN'] as $tipo) {
        $array = ['campoAtual'=>10, 'todosCampos'=>array_replace($itemPreco, [
            'T004_Id'=>1, 'T004_Flag_Comprar'=>$comprar, 'D049_Flag_Tipo'=>$tipo
        ])];
        $consultas = [['SELECT T219_Valor_Preco_Venda', [['T219_Valor_Preco_Venda'=>0]]]];
        $html = campoPrecoTabelaOrcamento($array, ['Preço', '/teste', 'T004_Id']);
        $pcp = in_array($tipo, ['PD', 'IN'], true) && $comprar === 'S';
        conferir((strpos($html, 'P.C.P') !== false) === $pcp, 'Encaminhamento ao PCP: ' . $tipo . '/' . $comprar);
        conferir(count($consultas) === 0, 'Consulta de preço não executada');
    }
}

// Método completo de custo: composição + processos + consumo e custo manual.
$metodo = trecho(fonte('bibliotecas/classes/CAD002.php'), 'public function atualizarCustoProduto(', 'public function atualizarEstatisticasCompra(');
eval('namespace hardness; class CustoIndustrializacao { ' . $metodo . ' }');
preg_match_all('/\$mD009\[\x27([^\x27]+)\x27\]/', $metodo, $campos);
$produtoBase = array_replace(array_fill_keys($campos[1], 0), [
    'D009_Id'=>10, 'D001_Id'=>20, 'D009_C004_Id'=>1, 'D009_D069_Id'=>30,
    'D009_Data_Atualizacao_Preco_Tabela'=>'2026-10-20', 'D009_Data_Ultima_Entrada'=>'2026-10-19'
]);
foreach ([0, 123.45] as $manual) {
    $saidas = [];
    foreach (['PD', 'IN'] as $tipo) {
        $g = ['empresaAtual'=>1, 'C031'=>['estoqueUtilizadoFormarCusto'=>'F']];
        $produto = array_replace($produtoBase, ['D049_Flag_Tipo'=>$tipo, 'D009_Valor_Preco_Tabela'=>$manual]);
        $consultas = [
            ['FROM D009', [$produto]],
            ["C031_Campo='inserirTabelaFornecedorPadrao'", [['C031_Valor'=>1]]],
            ['SELECT D069_Id', [['D069_Id'=>30]]],
            ['SELECT D069_Valor_Custo_Unitario', [['D069_Valor_Custo_Unitario'=>11, 'D069_Data_Atualizacao_Custo'=>'2026-10-18']]]
        ];
        if (!$manual) {
            $consultas = array_merge($consultas, [
                ['FROM D074', [['Custo'=>50, 'Data_Custo'=>'2026-10-17']]],
                ['FROM D075', [['Custo'=>15, 'Tempo'=>30]]],
                ['FROM D076', [['Custo'=>5]]]
            ]);
        }
        $consultas = array_merge($consultas, [['UPDATE D009', true], ['UPDATE D009A', true]]);
        $executado = [];
        conferir((new CustoIndustrializacao())->atualizarCustoProduto(10) === true, 'Atualização de custo falhou');
        conferir(count($consultas) === 0, 'Etapas de custo não executadas');
        $saidas[$tipo] = $executado;
        $sql = implode("\n", $executado);
        conferir(strpos($sql, "D009_Valor_Custo_Compra='" . ($manual ?: 70) . "'") !== false, 'Custo calculado incorreto');
        conferir(strpos($sql, "D009_Flag_Custo_Utilizado='" . ($manual ? 'MA' : 'T') . "'") !== false, 'Origem do custo incorreta');
    }
    conferir($saidas['PD'] === $saidas['IN'], 'Industrialização deve executar o mesmo cálculo de Produzido');
}

// A classificação nova herda as regras de CFOP de Produzido.
$codigo = trecho(fonte('bibliotecas/classes/VEN001.php'), '$tipoProdutoRegra =', '$log .=');
$codigo = substr($codigo, 0, strrpos($codigo, '{')) . '{ return true; } return false;';
foreach (['CP', 'PD', 'MT', 'MD', 'IN'] as $tipo) {
    foreach (['CP', 'PD', 'MT', 'MD'] as $regra) {
        $registro = ['D049_Flag_Tipo'=>$tipo]; $regras = ['D137_Produzido_Adquirido'=>$regra];
        $diverge = eval($codigo);
        conferir($diverge === ($regra !== ($tipo === 'IN' ? 'PD' : $tipo)), 'Regra fiscal divergente: ' . $tipo . '/' . $regra);
    }
}

// Mantém o contexto original da atualização de custos e estende a mesma
// exclusão de Produzido a Industrialização.
foreach (['015', '019'] as $programa) {
    $codigo = trecho(fonte('prog/est/est' . $programa . '/form_func-ajax.php'),
        'while($mT004 = mysql_fetch_assoc($T004)){', '$precoCotacao');
    $codigo .= '}';
    $saidas = [];
    foreach (['CP', 'PD', 'IN', 'MT', 'MD'] as $tipoContexto) {
        $T004 = new LinhasIndustrializacao(array_map(function ($tipo) {
            return ['D049_Flag_Tipo'=>$tipo];
        }, ['CP', 'PD', 'IN', 'MT', 'MD']));
        $mT006 = ['D049_Flag_Tipo'=>$tipoContexto];
        $count = 0; $contProdutosProduzidos = 0;
        eval('namespace hardness; ' . $codigo);
        $excluido = in_array($tipoContexto, ['PD', 'IN'], true);
        conferir($count === ($excluido ? 0 : 5) && $contProdutosProduzidos === ($excluido ? 5 : 0),
            'Atualização de custos deve manter o comportamento padrão: EST' . $programa);
        $saidas[$tipoContexto] = [$count, $contProdutosProduzidos];
    }
    conferir($saidas['PD'] === $saidas['IN'], 'Exclusão de custo deve ser igual para PD e IN');
}

// Executa o método completo compartilhado de cópia da engenharia, incluindo
// processos, materiais de consumo e rollback em cada etapa de erro.
$codigoCopia = trecho(fonte('bibliotecas/classes/VEN002.php'),
    'public function copiarEngenhariaProduto(', 'public function prepararCancelarPedidos(');
$codigoCopia = preg_replace('/require_once\([^;]+;/', '', $codigoCopia, -1, $carregamentos);
conferir($carregamentos === 1, 'Dependências da cópia mudaram; revisar isolamento');
eval('namespace hardness; class EngenhariaIndustrializacao { ' . $codigoCopia . ' }');
class EST017 {
    public function gatilhoT262AposInsert($parametros) {
        global $gatilhos, $erroGatilho;
        $gatilhos[] = $parametros;
        return $erroGatilho;
    }
}
function prepararCopiaEngenharia($falha) {
    global $consultas, $executado, $erroAtual, $gatilhos, $erroGatilho;
    $executado = []; $gatilhos = []; $erroAtual = '';
    $erroGatilho = $falha === 'gatilho' ? 'Falha simulada no gatilho' : '';
    $consultas = [['SET AUTOCOMMIT=0', true], ['START TRANSACTION', true],
        ['SELECT D001_Id', [['D001_Id'=>20, 'T006_Id'=>42, 'T006_T005_Id'=>77]]],
        ['INSERT INTO T261 (', true, $falha === 'componentes' ? 'Falha simulada' : '']];
    if ($falha === 'componentes') {
        $consultas[] = ['ROLLBACK', true]; return;
    }
    $consultas = array_merge($consultas, [['INSERT INTO T261A', true],
        ['SELECT D075_Id', [['D075_Id'=>100]]],
        ['INSERT INTO T262', true, $falha === 'processos' ? 'Falha simulada' : '', 900]]);
    if (in_array($falha, ['processos', 'gatilho'], true)) {
        $consultas[] = ['ROLLBACK', true]; return;
    }
    $consultas[] = ['INSERT INTO T263', true, $falha === 'consumo' ? 'Falha simulada' : ''];
    $consultas = array_merge($consultas, $falha === 'consumo'
        ? [['ROLLBACK', true]] : [['COMMIT', true], ['SET AUTOCOMMIT=1', true]]);
}
$diretorioTeste = sys_get_temp_dir() . '/industrializacao-' . getmypid() . '/';
mkdir($diretorioTeste . 'tmp', 0700, true);
try {
    foreach (['', 'componentes', 'processos', 'consumo', 'gatilho'] as $falha) {
        $resultados = []; $rastreios = [];
        foreach (['PD', 'IN'] as $tipo) {
            $g = ['pathDados'=>$diretorioTeste];
            $mT006 = ['D049_Flag_Tipo'=>$tipo];
            // Usa a condição real que encaminha o pedido para a cópia.
            $despacho = str_replace('new VEN002()', 'new EngenhariaIndustrializacao()', $trigger);
            $despacho = str_replace('$VEN002->copiarEngenhariaProduto($chaveValor);',
                '$resultadoCopia = $VEN002->copiarEngenhariaProduto($chaveValor);', $despacho);
            prepararCopiaEngenharia($falha);
            eval('namespace hardness; ' . $despacho);
            conferir(count($consultas) === 0, 'Etapas de engenharia não executadas: ' . $falha);
            $rastreios[$tipo] = [$executado, $gatilhos];
            $resultados[$tipo] = $resultadoCopia;
            conferir($falha ? is_string($resultadoCopia) : $resultadoCopia === true,
                'Resultado incorreto na cópia da engenharia: ' . $falha);
            conferir(in_array($falha, ['processos', 'componentes'], true)
                ? count($gatilhos) === 0 : $gatilhos === [[900, 100]], 'Gatilho do processo incorreto');
            if ($falha) {
                conferir(in_array('ROLLBACK', $executado, true) && !in_array('COMMIT', $executado, true),
                    'Erro na engenharia deve cancelar a transação: ' . $falha);
            } else {
                conferir(in_array('COMMIT', $executado, true), 'Engenharia não confirmada');
            }
        }
        conferir($rastreios['PD'] === $rastreios['IN'], 'Cópia/rollback diferem entre PD e IN: ' . $falha);
        conferir($resultados['PD'] === $resultados['IN'], 'Erro de cópia diferente entre PD e IN: ' . $falha);
    }
} finally {
    foreach (glob($diretorioTeste . 'tmp/*') as $arquivo) { unlink($arquivo); }
    rmdir($diretorioTeste . 'tmp'); rmdir($diretorioTeste);
}

// Limites de níveis e estoque na geração de OP para a composição.
$codigoOp = trecho(fonte('bibliotecas/classes/EST017.php'),
    'public function gerarOrdemProducaoComposicao(', 'public function processarEntrada(');
eval('namespace hardness; class ComposicaoIndustrializacao {
    public $geradas = [];
    public function gerarOrdemProducao($sql, $pai, $nivel) { $this->geradas[] = [$sql, $pai, $nivel]; }
    ' . $codigoOp . '}');
foreach ([[0,0,8,0,0], [1,1,8,0,0], [1,0,8,0,1], [2,0,8,8,0], [2,0,8,10,0]] as $cenario) {
    list($maximo, $nivel, $necessario, $estoque, $esperadas) = $cenario;
    $saidas = [];
    foreach (['PD', 'IN'] as $tipo) {
        $g = ['empresaAtual'=>1, 'C031'=>['nivelMaximoComposicaoGeracaoOP'=>$maximo]];
        $consultas = $maximo > 0 && $nivel + 1 <= $maximo ? [['FROM', [[
            'D009_Id'=>10, 'T261_Id'=>20, 'T256_Id'=>30, 'saldo'=>$necessario,
            'D009_Quantidade_Estoque'=>$estoque, 'D049_Flag_Tipo'=>$tipo
        ]]]] : [];
        $gerador = new ComposicaoIndustrializacao();
        $gerador->gerarOrdemProducaoComposicao(40, 30, $nivel);
        conferir(count($consultas) === 0, 'Seleção de composição não executada');
        conferir(count($gerador->geradas) === $esperadas, 'OP não respeitou estoque/nível máximo');
        $saidas[$tipo] = $gerador->geradas;
    }
    conferir($saidas['PD'] === $saidas['IN'], 'OP da composição diferente entre PD e IN');
}

// Respostas reais da validação agregada e da cotação agrupada por fornecedor.
foreach (['009', '019'] as $programa) {
    $arquivo = fonte('prog/est/est' . $programa . '/grid_func-ajax.php');
    $inicioCaso = strpos($arquivo, "case 'est" . $programa . "GerarCotacaoPedidoEmergencia':");
    $codigo = trecho(substr($arquivo, $inicioCaso), "if (\$row['Total_Cotar'] <= 0)", "\n\tbreak;");
    foreach ([[0,0,false], [1,1,false], [2,2,false], [1,0,true]] as $cenario) {
        $row = ['Total_Cotar'=>$cenario[0], 'Itens_Sem_Engenharia'=>$cenario[1]]; $resposta = [];
        eval($codigo);
        conferir($resposta['code'] === $cenario[2], 'Cotação não bloqueou seleção/engenharia ausente');
    }
    $posicaoTipo = strpos($arquivo, "if(in_array(\$row['D049_Flag_Tipo']");
    $inicioLoop = strrpos(substr($arquivo, 0, $posicaoTipo), 'while($row = mysql_fetch_array($retorno))');
    $codigo = trecho(substr($arquivo, $inicioLoop), 'while($row = mysql_fetch_array($retorno))', "\n\tbreak;");
    // O trecho termina também o else que precede o while no código original.
    $codigo = substr($codigo, 0, strrpos($codigo, '}'));
    foreach ([[0,1,false], [10,0,false], [10,1,true]] as $cenario) {
        $saidas = [];
        foreach (['PD', 'IN'] as $tipo) {
            $retorno = new LinhasIndustrializacao([['D049_Flag_Tipo'=>$tipo, 'D074_Id'=>$cenario[0],
                'D024_Id'=>$cenario[1], 'count'=>1, 'D024_Nome_Empresa'=>'Fornecedor de teste']]);
            $semEngenharia = 0; $semFornecedor = false; $msg = ''; $resposta = [];
            eval('namespace hardness; ' . $codigo);
            conferir($resposta['code'] === $cenario[2], 'Cotação deve validar engenharia e fornecedor');
            $saidas[$tipo] = $resposta;
        }
        conferir($saidas['PD'] === $saidas['IN'], 'Validação de cotação difere entre PD e IN');
    }
}

// As restrições reais do cadastro continuam exigindo os mesmos campos.
require_once $raiz . '/bibliotecas/restricoes.php';
$conf = ['auditoriaCampos'=>[], 'camposNaoUsar'=>[], 'ignorarRelacionamentoExcluir'=>[]];
include $raiz . '/configuracoes/D001.php';
include $raiz . '/configuracoes/D049.php';
$camposValidar = ['D001_Descricao_Produto'=>null, 'D001_D037_Id'=>'D037',
    'D001_D005_Id'=>'D005', 'D001_Flag_Tipo_Item'=>null, 'D049_D082_Id'=>'D082',
    'D049_Flag_Tipo'=>'tipo', 'D049_Origem_Mercadoria'=>'origem'];
foreach (['S', 'N'] as $preCadastro) {
    foreach (['', '1', '...'] as $entrada) {
        foreach ($camposValidar as $campo=>$tabela) {
            $saidas = [];
            foreach (['PD', 'IN'] as $tipo) {
                $g = ['C031'=>['obrigatorioNCMPreCadastroProduto'=>'S',
                    'obrigatorioTipoPreCadastroProduto'=>'S', 'obrigatorioOrigemPreCadastroProduto'=>'S']];
                $valores = ['D001_Flag_Pre_Cadastro'=>$preCadastro, 'D049_Id'=>5, 'D049_Flag_Tipo'=>$tipo];
                $valor = $campo === 'D049_Flag_Tipo' && $entrada === '1' ? $tipo : $entrada;
                $retorno = true; $consultas = [];
                if (in_array($tabela, ['tipo', 'origem'], true)) {
                    $consultas = [['SELECT D001_Flag_Pre_Cadastro', [['D001_Flag_Pre_Cadastro'=>$preCadastro]]]];
                } elseif ($tabela && $entrada !== '' && $entrada !== '...') {
                    $consultas = [['FROM ' . $tabela, [[$tabela . '_Id'=>1]]]];
                }
                eval('namespace hardness; ' . $conf['restricaoIncluirUpdate'][$campo]);
                conferir(count($consultas) === 0, 'Validação de cadastro não executada: ' . $campo);
                conferir($entrada === '' ? $retorno !== true : ($entrada !== '1' || $retorno === true),
                    'Obrigatoriedade do cadastro incorreta: ' . $campo);
                $saidas[$tipo] = $retorno;
            }
            conferir($saidas['PD'] === $saidas['IN'], 'Validação de cadastro difere: ' . $campo);
        }
    }
}

// O tipo novo não deve contornar as permissões de edição/pré-cadastro.
foreach (['S', 'N'] as $preCadastro) {
    foreach ([false, true] as $permitir) {
        $saidas = [];
        foreach (['PD', 'IN'] as $tipo) {
            $g = ['c029Ids'=>$permitir ? [$preCadastro === 'S' ? 187 : 186=>true] : []];
            $valores = ['D049_Flag_Tipo'=>$tipo]; $operacao = 'alterar'; $valorPk = 5;
            $consultas = [['SELECT D001_Flag_Pre_Cadastro', [['D001_Flag_Pre_Cadastro'=>$preCadastro]]]];
            eval('namespace hardness; ' . $conf['restricaoTravar']['D049']);
            conferir(count($consultas) === 0 && ($retorno === true) === $permitir, 'Permissão de edição incorreta');
            $saidas[$tipo] = $retorno;
        }
        conferir($saidas['PD'] === $saidas['IN'], 'Permissões diferentes entre PD e IN');
    }
}

// Quantidade inválida e quantidade abaixo do que já foi separado seguem
// as mesmas restrições no item do pedido.
$g = ['C031'=>['usarDescontoProdutos'=>'S']];
include $raiz . '/configuracoes/T006.php';
foreach ([['0',0,false], ['-1',0,false], ['2',3,false], ['3',3,true], ['5',0,true]] as $cenario) {
    $saidas = [];
    foreach (['PD', 'IN'] as $tipo) {
        $valores = ['D049_Flag_Tipo'=>$tipo]; $acaoId = 42; $valor = $cenario[0];
        $consultas = [['SELECT T006_Quantidade_Separacao', [['T006_Quantidade_Separacao'=>$cenario[1]]]]];
        eval('namespace hardness; ' . $conf['restricaoIncluirUpdate']['T006_Quantidade']);
        conferir(count($consultas) === 0 && ($retorno === true) === $cenario[2], 'Restrição de quantidade incorreta');
        $saidas[$tipo] = $retorno;
    }
    conferir($saidas['PD'] === $saidas['IN'], 'Quantidade/estoque separado diverge entre PD e IN');
}

// Salvar a marca preserva IN como classificação, executando a mesma rotina.
class FormularioIndustrializacao {
    private $tipo;
    public function __construct($tipo) { $this->tipo = $tipo; }
    public function campoValorEnviado($campo) { return $campo === 'D049_Flag_Tipo' ? $this->tipo : '1'; }
}
$codigoCadastro = trecho(fonte('bibliotecas/classes/CAD020.php'),
    'function atualizarMarca($form, $D001_Id)', "\n\n}");
eval('namespace hardness; class CadastroComplementarIndustrializacao { ' . $codigoCadastro . ' }');
$saidas = [];
foreach (['PD', 'IN'] as $tipo) {
    $g = ['empresaAtual'=>1]; $executado = [];
    $consultas = [['UPDATE D049', true]];
    conferir((new CadastroComplementarIndustrializacao())->atualizarMarca(new FormularioIndustrializacao($tipo), 20) === true,
        'Gravação da marca falhou');
    conferir(count($consultas) === 0, 'Etapas de gravação não executadas');
    $sql = implode("\n", $executado);
    conferir(strpos($sql, "D049_Flag_Tipo = '{$tipo}'") !== false, 'Classificação não preservada na gravação');
    $saidas[$tipo] = str_replace("'IN'", "'PD'", $sql);
}
conferir($saidas['PD'] === $saidas['IN'], 'Gravação possui etapas diferentes entre PD e IN');
echo "OK: {$verificacoes} verificações de Industrialização; sem conexão com banco.\n";
