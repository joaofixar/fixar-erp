<?php
/** Testes isolados: php scripts/testes/nf-debito-credito-compatibilidade.php
 * Com DOM habilitado, testa também o trecho real do gerador contra o XSD do projeto.
 * Não carrega o bootstrap, não conecta ao banco e não transmite documentos.
 */
namespace hardness;

if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL);
set_error_handler(function ($nivel, $mensagem) { throw new \RuntimeException($mensagem); });
$raiz = dirname(__DIR__, 2);
chdir($raiz);
function definirClasse($nome) {}
require_once $raiz . '/bibliotecas/classes/ReformaTributaria.php';
class_alias(__NAMESPACE__ . '\\ReformaTributaria_', __NAMESPACE__ . '\\ReformaTributaria');
$fiscal = new ReformaTributaria();
if (in_array('--regras-json', $argv, true)) {
    echo json_encode($fiscal->getRegrasNFDebitoCredito());
    exit;
}
$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
function itemFiscal($classe = '000001', $cfop = '5949', $grupo = '') {
    $item = ['T008A_Situacao_Tributaria_CBS_IBS' => substr($classe, 0, 3),
        'T008A_Classificacao_Tributaria_CBS_IBS' => $classe, 'T008_Codigo_CFOP' => $cfop];
    if ($grupo === 'ajuste') {
        $item += ['T008A_Competencia_Ajuste' => '08/2026', 'T008A_Valor_IBS_Ajuste_Competencia' => 10, 'T008A_Valor_CBS_Ajuste_Competencia' => 0];
    }
    if ($grupo === 'transferencia') {
        $item += ['T008A_Valor_IBS_Transferido' => 0, 'T008A_Valor_CBS_Transferido' => 10];
    }
    if ($grupo === 'estorno') {
        $item += ['T008A_Valor_IBS_Estornado' => 0, 'T008A_Valor_CBS_Estornado' => 0];
    }
    return $item;
}

// Matriz para diagnóstico técnico; não bloqueia geração/transmissão.
foreach ($fiscal->getRegrasNFDebitoCredito() as $chave => $regra) {
    list($finalidade, $tipo) = explode(':', $chave);
    $classe = $regra['classe'] ?? '000001';
    $cfop = $finalidade === '6' ? '5949' : (in_array($tipo, ['03', '06'], true) ? '1202' : '1949');
    $item = itemFiscal($classe, $cfop, $regra['grupo']);
    $erro = $fiscal->validarItemNFDebitoCredito($finalidade, $tipo, $item, '2026-09-05');
    conferir(in_array($chave, ['5:02', '5:05'], true) ? $erro !== null : $erro === null, "$chave: $erro");
    foreach (['410030', '800001', '800002', '810001', '811001', '811002', '811003'] as $reservada) {
        $erro = $fiscal->validarClassificacaoNFDebitoCredito($finalidade, $tipo, substr($reservada, 0, 3), $reservada);
        conferir(($reservada === $regra['classe'] && $chave !== '5:05') ? $erro === null : $erro !== null, "Classe reservada $reservada / $chave");
    }
}
foreach ([['999', '000001'], ['000', '999999'], ['000', '800001'], ['820', '820001'], ['', ''], ['000', '']] as $par) {
    conferir($fiscal->validarClassificacaoNFDebitoCredito('6', '04', $par[0], $par[1]) !== null, 'CST/classe inválido aceito');
}
foreach ([['5','03','1949'], ['5','06','1102'], ['6','01','5202'], ['5','01','1202'], ['5','05','1202'],
    ['5','03','5202'], ['6','04','1949'], ['6','04','5351'], ['6','04','9999']] as $caso) {
    conferir($fiscal->validarCFOPNFDebitoCredito(...$caso) !== null, 'CFOP incompatível aceita: ' . implode('/', $caso));
}
foreach ([['5','03','1.202'], ['5','06','2202'], ['5','04','1202'], ['5','04','1949'], ['6','06','5922']] as $caso) {
    conferir($fiscal->validarCFOPNFDebitoCredito(...$caso) === null, 'CFOP válida bloqueada');
}
conferir($fiscal->validarCFOPNFDebitoCredito('5', '03', '1201', '4') !== null, 'MEI sem validação');
conferir($fiscal->validarCFOPNFDebitoCredito('5', '03', '1202', '4') === null, 'MEI válido bloqueado');
$ajuste = itemFiscal('811002', '5949', 'ajuste');
foreach (['13/2026', '09/20', '10/2026', '2026-13', '12/2024', '01/0000', ''] as $competencia) {
    conferir($fiscal->validarItemNFDebitoCredito('6', '03', array_replace($ajuste, ['T008A_Competencia_Ajuste'=>$competencia]), '2026-09-05') !== null, 'Competência inválida/futura aceita');
}
foreach (['09/2026', '2026-09', '08/2026'] as $competencia) {
    conferir($fiscal->validarItemNFDebitoCredito('6', '03', array_replace($ajuste, ['T008A_Competencia_Ajuste'=>$competencia]), '2026-09-05') === null, 'Competência válida recusada');
}
foreach ([-1, 'abc', INF, NAN, 0, 0.004, 1E15] as $valor) {
    conferir($fiscal->validarItemNFDebitoCredito('6', '03', array_replace($ajuste, ['T008A_Valor_IBS_Ajuste_Competencia'=>$valor]), '2026-09-05') !== null, 'Valor inválido/zerado no XML aceito');
}
conferir($fiscal->validarItemNFDebitoCredito('6', '04', itemFiscal() + ['T008A_Valor_IBS_Transferido'=>1], '2026-09-05') !== null, 'Grupo indevido foi ignorado');
conferir($fiscal->validarItemNFDebitoCredito('6', '04', itemFiscal('620001'), '2026-09-05') !== null, 'Grupo monofásico não implementado ignorado');
conferir($fiscal->validarClassificacaoNFDebitoCredito('6', '04', '200', '200001') !== null, 'Indicador indNFe desatualizado');
conferir($fiscal->validarClassificacaoNFDebitoCredito('6', '04', '000', '000005') === null, 'Nova classificação oficial ausente');
foreach ($fiscal->getCclassTrib() as $classe => $dadosClasse) {
    conferir(is_array($fiscal->getAliquotas((string) $classe)), 'Nova tabela falhou ao carregar alíquotas: ' . $classe);
}
conferir($fiscal->validarItemNFDebitoCredito('6', '04', itemFiscal('200054'), '2026-09-05') !== null, 'Estorno obrigatório pela classificação ignorado');
conferir($fiscal->validarItemNFDebitoCredito('6', '04', itemFiscal('200054') + ['T008A_Valor_IBS_Estornado'=>1], '2026-09-05') === null, 'Estorno válido pela classificação bloqueado');
conferir($fiscal->validarItemNFDebitoCredito('6', '04', itemFiscal('550001'), '2026-09-05') !== null, 'Tributação regular incompleta aceita');

// Callback real: coluna livre não é projetada pelo SELECT do framework.
$g = ['c020'=>['C020_Casas_Decimais_Quantidade'=>3], 'empresaAtual'=>1,
    'C004'=>['C004_Flag_Tipo_Preenchimento_Reforma_Tributaria'=>'1']];
$fonteCallback = file_get_contents($raiz . '/prog/ven/ven003/grid_func-callback.php');
$inicioCallback = strpos($fonteCallback, 'function ven003NFDcCampoQuantidade(');
$fimCallback = strpos($fonteCallback, 'function T008A_D014_Id_PlanoContas(', $inicioCallback);
eval('namespace hardness; ' . substr($fonteCallback, $inicioCallback, $fimCallback - $inicioCallback));
$campo = ven003NFDcCampoQuantidade(['todosCampos'=>['T008_Id'=>7, 'T008_Quantidade'=>'8.000000']]);
conferir(strpos($campo, 'data-quantidade-origem="8.000000"') !== false, 'Quantidade perdida na coluna livre');

// Exercita o método real com banco/cópia/cálculo simulados. Nenhuma função global mysql é usada.
class LinhasNFDC { public $linhas; public function __construct($linhas) { $this->linhas=$linhas; } }
$consultas = []; $sqlExecutado = [];
function mysql_query($sql) {
    global $consultas, $sqlExecutado;
    $sqlExecutado[] = $sql;
    conferir(count($consultas) > 0, 'SQL inesperado no teste');
    $esperado = array_shift($consultas);
    conferir(strpos($sql, $esperado[0]) !== false, 'Ordem SQL incorreta: ' . $esperado[0]);
    return is_array($esperado[1]) ? new LinhasNFDC($esperado[1]) : $esperado[1];
}
function mysql_fetch_assoc($linhas) { return array_shift($linhas->linhas); }
function mysql_num_rows($linhas) { return count($linhas->linhas); }
function mysql_error() { return ''; }
function mysql_real_escape_string($valor) { return addslashes((string) $valor); }
$fonte = file_get_contents($raiz . '/bibliotecas/classes/VEN003.php');
$inicio = strpos($fonte, 'public function gerarNFDebitoCreditoNotaAutorizada(');
$fim = strpos($fonte, 'public function imprimirNFeMarcada(', $inicio);
conferir($inicio !== false && $fim !== false, 'Método de geração não localizado');
eval('namespace hardness; class GeradorNFDC {
    public $excluida=false;
    public $sqlAntesRecalculo="";
    public $recalculoCompleto=false;
    public function gerarNotaTransporte(...$dados) { return ["T007_Id"=>200,"numero"=>20,"serie"=>1]; }
    public function atualizarAliquotasNF($id, $novaNFDebitoCredito=false) { global $sqlExecutado; $this->recalculoCompleto=$novaNFDebitoCredito; $this->sqlAntesRecalculo=implode("\n",$sqlExecutado); return true; }
    public function ven003excluirNota($id) { $this->excluida=($id===200); return true; }
    ' . substr($fonte, $inicio, $fim-$inicio) . '}');
function prepararGeracao($classeDivergente = false, $erroConsulta = false, $erroTotais = false, $cfop = '5949', $referenciaCabecalho = false) {
    global $consultas, $sqlExecutado;
    $sqlExecutado=[];
    $origem=['T007_Id'=>100, 'T007_D024_Id'=>2, 'T007_C004_Id'=>1, 'T007_D006_Id'=>3,
        'T007_Flag_Destino_Produto'=>3, 'T007_Flag_ACP'=>6, 'T007_Flag_Cancelada'=>'N', 'T007_Flag_Inutilizado'=>'N',
        'T007_Flag_Denegado'=>'N', 'T007_Numero_Nota_Fiscal'=>1, 'T007_Serie'=>1, 'T007_Data_Emissao'=>'2026-08-01',
        'T007_Chave_Acesso_Nfe'=>'35260812345678000190550010000000011000000010', 'T007_Numero_Protocolo_Nfe'=>'123'];
    $item=itemFiscal($classeDivergente ? '000001' : '811002', $cfop);
    $item+=['T008_Id'=>201, 'T007_Data_Emissao'=>'2026-09-05', 'T007_Empresa_Usuaria_CRT'=>'3'];
    $consultas=[['SHOW COLUMNS', [['Field'=>'T007A_T007_Id_Origem_NF_Debito_Credito']]], ['FROM T007', [$origem]],
        ['FROM D006', [['D006_Id'=>3,'Codigo_CFOP'=>$cfop]]],
        ['FROM T008', [['T008_Id'=>101,'T008_Codigo_Produto'=>'X','T008_Descricao_Produto'=>'Teste','T008_Quantidade'=>10]]],
        ['GET_LOCK', [['Bloqueado'=>1]]], ['SET AUTOCOMMIT=0',true], ['START TRANSACTION',true],
        ['UPDATE T007',true], ['UPDATE T007A',true], ['SELECT T007A_T007_Id', [['T007A_T007_Id'=>200]]],
        ['DELETE FROM T132',true], ['UPDATE T008A A',true], ['SELECT T008_Id', [['T008_Id'=>201]]],
        ['UPDATE T008',true], ['UPDATE T008A',true], ['SELECT I.T008_Id', $erroConsulta ? false : [$item]]];
    if ($referenciaCabecalho) {
        array_splice($consultas, count($consultas) - 1, 0, [['INSERT INTO T132', true]]);
    }
    if ($erroConsulta) {
        $consultas=array_merge($consultas,[['ROLLBACK',true],['SET AUTOCOMMIT=1',true],['DELETE FROM T132',true],['SET AUTOCOMMIT=1',true],['RELEASE_LOCK',true]]);
    } else {
        $consultas=array_merge($consultas,[['UPDATE T008A SET',true],['CALL T007_Gravar_Totalizacao_4',true],['UPDATE T007A CAB', !$erroTotais]]);
        $consultas=array_merge($consultas, $erroTotais
            ? [['ROLLBACK',true],['SET AUTOCOMMIT=1',true],['DELETE FROM T132',true],['SET AUTOCOMMIT=1',true],['RELEASE_LOCK',true]]
            : [['COMMIT',true],['SET AUTOCOMMIT=1',true],['RELEASE_LOCK',true]]);
    }
}
$dados=['T007_Id'=>100,'D006_Id'=>3,'tipoOperacao'=>'debito','tipoFiscal'=>'03',
    'quantidades'=>[101=>'3,500'], 'reforma'=>[101=>['T008A_Competencia_Ajuste'=>'08/2026',
        'T008A_Valor_IBS_Ajuste_Competencia'=>'10,00','T008A_Valor_CBS_Ajuste_Competencia'=>'0,00']]];
foreach ([[false,false], [true,false], [false,true], [false,false,true]] as $cenario) {
    prepararGeracao(...$cenario); $gerador=new GeradorNFDC();
    $resultado=$gerador->gerarNFDebitoCreditoNotaAutorizada($dados);
    conferir(count($consultas)===0, 'Consultas esperadas não executadas');
    conferir(($cenario[1] || ($cenario[2] ?? false)) ? is_string($resultado) && $gerador->excluida : is_array($resultado) && !$gerador->excluida, 'Classe não deve bloquear a cópia; falha de consulta/totalização deve cancelar');
    if ($cenario[2] ?? false) {
        conferir(!in_array('COMMIT', $sqlExecutado, true), 'Falha nos totais confirmou a geração');
    }
    if (is_array($resultado)) {
        conferir($gerador->recalculoCompleto, 'Gerador não solicitou renovação completa dos parâmetros fiscais');
        conferir(strpos($gerador->sqlAntesRecalculo, "T007_Flag_Calcular = 'S'") !== false, 'Cabeçalho herdou cálculo desativado');
        conferir(strpos($gerador->sqlAntesRecalculo, "T008_Flag_Calculo_Manual='N'") !== false, 'Item herdou cálculo manual');
        conferir(strpos($gerador->sqlAntesRecalculo, "T008_D006_Id='3'") !== false, 'CFOP selecionada não aplicada ao item');
        foreach (['Base_IPI','IPI','ICMS','ICMS_Substituicao_Retencao','Base_ICMS_Substituicao',
            'FCP','FCP_ST','FCP_Retido','PIS','COFINS','Partilha_ICMS_Destino'] as $imposto) {
            conferir(strpos($gerador->sqlAntesRecalculo, 'I.T008_Valor_' . $imposto . '=0') !== false, 'Imposto herdado antes de recalcular: ' . $imposto);
        }
        conferir(strpos($gerador->sqlAntesRecalculo, "WHERE I.T008_T007_Id='200'") !== false, 'Limpeza sem filtro da nova NF');
        conferir($resultado['referencia']==='item', 'Referência do débito 03 alterada');
        conferir(strpos(implode("\n",$sqlExecutado), "T008A_Valor_IBS_Ajuste_Competencia='10.0000'")!==false, 'Ajuste não persistido');
        conferir(strpos(implode("\n",$sqlExecutado), "T008A_Item_DFe_Referenciado='0'")!==false, 'Débito 03 recebeu nItem');
    }
}
$consultas=[];
conferir(is_string((new GeradorNFDC())->gerarNFDebitoCreditoNotaAutorizada(array_replace($dados,['tipoFiscal'=>'06']))), 'Débito 06 liberado na NF autorizada');

// Preço opcional: não arredondar a centavos nem converter DECIMAL(20,10) em float.
$campoPreco = ven003NFDcCampoPreco(['todosCampos'=>['T008_Id'=>101]]);
conferir(strpos($campoPreco, 'class="precoNFDCOrigem"') !== false && strpos($campoPreco, 'data-t008-id="101"') !== false, 'Preço não associado ao item da origem');
conferir(strpos($campoPreco, 'value=""') !== false && strpos($campoPreco, 'Manter preço') !== false, 'Preço opcional não preserva o original');
foreach (['75,00'=>'75.0000000000', '1.234,5678901234'=>'1234.5678901234',
    '0,0000000001'=>'0.0000000001', '0'=>'0.0000000000', '12.3456789012'=>'12.3456789012',
    '9999999999,9999999999'=>'9999999999.9999999999', '00012,30'=>'12.3000000000', ''=>null, '   '=>null] as $entrada=>$esperado) {
    prepararGeracao(); $gerador=new GeradorNFDC();
    $retorno=$gerador->gerarNFDebitoCreditoNotaAutorizada(array_replace($dados,['precos'=>[101=>(string)$entrada]]));
    conferir(is_array($retorno) && !$consultas, 'Preço válido impediu geração: ' . $entrada);
    foreach (['T008A_Preco_Unitario_Comercial', 'T008_Valor_Preco_Sem_Desconto_Unitario'] as $campo) {
        if ($esperado === null) {
            conferir(strpos(implode("\n",$sqlExecutado), $campo . '=') === false, 'Preço em branco sobrescreveu a origem');
        } else {
            conferir(strpos($gerador->sqlAntesRecalculo, $campo . "='{$esperado}'") !== false, 'Preço não aplicado antes do recálculo: ' . $campo);
        }
    }
    foreach ($sqlExecutado as $sql) {
        if (strpos($sql,'T008A_Preco_Unitario_Comercial=') !== false) {
            conferir(strpos($sql,"WHERE T008A_T008_Id='201'") !== false, 'Preço gravado no item errado');
        }
        if (strpos($sql,'T008_Valor_Preco_Sem_Desconto_Unitario=') !== false) {
            conferir(strpos($sql,"WHERE T008_Id='201'") !== false && strpos($sql,"T008_T007_Id='200'") !== false, 'Preço alterou a nota de origem');
        }
    }
}
foreach (['-1', 'abc', 'NaN', INF, NAN, '1e3', '10000000000', '0,12345678901', '1.2,50', "1';DROP TABLE T008;--", [], true] as $entrada) {
    prepararGeracao(); $consultas=array_slice($consultas,0,4);
    $retorno=(new GeradorNFDC())->gerarNFDebitoCreditoNotaAutorizada(array_replace($dados,['precos'=>[101=>$entrada]]));
    conferir(is_string($retorno) && strpos($retorno,'Preço unitário inválido') !== false && !$consultas, 'Preço inválido chegou à cópia da nota');
}
prepararGeracao(); $consultas=array_slice($consultas,0,4);
$retorno=(new GeradorNFDC())->gerarNFDebitoCreditoNotaAutorizada(array_replace($dados,['precos'=>[999=>'1,00']]));
conferir(is_string($retorno) && strpos($retorno,'não corresponde') !== false && !$consultas, 'Preço de outra origem aceito');
$consultas=[];
conferir(is_string((new GeradorNFDC())->gerarNFDebitoCreditoNotaAutorizada(array_replace($dados,['precos'=>null]))), 'JSON inválido ignorado');
foreach (['UPDATE T008', 'UPDATE T008A'] as $falha) {
    prepararGeracao();
    foreach ($consultas as $indice=>$consulta) {
        if ($consulta[0] === $falha) {
            $consultas=array_slice($consultas,0,$indice+1);
            $consultas[$indice][1]=false;
            $consultas=array_merge($consultas,[['ROLLBACK',true],['SET AUTOCOMMIT=1',true],['DELETE FROM T132',true],['SET AUTOCOMMIT=1',true],['RELEASE_LOCK',true]]);
            break;
        }
    }
    $gerador=new GeradorNFDC();
    $retorno=$gerador->gerarNFDebitoCreditoNotaAutorizada(array_replace($dados,['precos'=>[101=>'75,00']]));
    conferir(is_string($retorno) && $gerador->excluida && !$consultas && !in_array('COMMIT',$sqlExecutado,true), 'Falha de preço não cancelou a cópia');
}

// Dois itens da mesma nota: preços independentes, com um item preservado ou excluído.
foreach (['dois', 'preservar', 'excluir'] as $cenarioPreco) {
    prepararGeracao(true, false, false, '1949', true);
    foreach ($consultas as &$consulta) {
        if ($consulta[0] === 'FROM T008') {
            $consulta[1][] = ['T008_Id'=>102,'T008_Codigo_Produto'=>'Y','T008_Descricao_Produto'=>'Segundo','T008_Quantidade'=>5];
        }
        if ($consulta[0] === 'SELECT T008_Id') { $consulta[1][] = ['T008_Id'=>202]; }
        if ($consulta[0] === 'SELECT I.T008_Id' && $cenarioPreco !== 'excluir') {
            $segundo=$consulta[1][0]; $segundo['T008_Id']=202; $consulta[1][]=$segundo;
        }
    }
    unset($consulta);
    foreach ($consultas as $indice=>$consulta) {
        if ($consulta[0] === 'INSERT INTO T132') {
            $operacoesSegundo = $cenarioPreco === 'excluir'
                ? [['DELETE FROM T001',true],['DELETE FROM T008A',true],['DELETE FROM T008',true]]
                : [['UPDATE T008',true],['UPDATE T008A',true]];
            array_splice($consultas,$indice,0,$operacoesSegundo);
            break;
        }
    }
    if ($cenarioPreco !== 'excluir') {
        foreach ($consultas as $indice=>$consulta) {
            if ($consulta[0] === 'CALL T007_Gravar_Totalizacao_4') { array_splice($consultas,$indice,0,[['UPDATE T008A SET',true]]); break; }
        }
    }
    $dadosPrecos = array_replace($dados, ['tipoOperacao'=>'credito','tipoFiscal'=>'04',
        'quantidades'=>[101=>'3,500',102=>$cenarioPreco === 'excluir' ? '0' : '2,000'],
        'precos'=>[101=>'10,50',102=>$cenarioPreco === 'preservar' ? '' : '20,75'],
        'reforma'=>[101=>['T008A_Valor_IBS_Estornado'=>'0'],102=>['T008A_Valor_IBS_Estornado'=>'0']]]);
    $gerador=new GeradorNFDC();
    $retorno=$gerador->gerarNFDebitoCreditoNotaAutorizada($dadosPrecos);
    conferir(is_array($retorno) && !$consultas, 'Geração com vários preços falhou: ' . $cenarioPreco);
    $gravacoesPreco=[];
    foreach ($sqlExecutado as $sql) {
        if (preg_match("/T008A_Preco_Unitario_Comercial='([^']+)'[\\s\\S]*WHERE T008A_T008_Id='([0-9]+)'/",$sql,$m)) { $gravacoesPreco[$m[2]]=$m[1]; }
    }
    conferir(($gravacoesPreco[201] ?? '') === '10.5000000000', 'Preço do primeiro item foi trocado');
    conferir($cenarioPreco === 'dois' ? ($gravacoesPreco[202] ?? '') === '20.7500000000' : !isset($gravacoesPreco[202]), 'Preço de outro item sobrescrito indevidamente');
}

// Reprodução do exemplo: Débito 01 com CST 000 / classe 000001 e valores transferidos.
prepararGeracao(true);
$dadosTransferencia = array_replace($dados, ['tipoFiscal'=>'01', 'reforma'=>[101=>[
    'T008A_Valor_IBS_Transferido'=>'4,20', 'T008A_Valor_CBS_Transferido'=>'1,20']]]);
$gerador = new GeradorNFDC();
conferir(is_array($gerador->gerarNFDebitoCreditoNotaAutorizada($dadosTransferencia)) && !$gerador->excluida, 'Débito 01 bloqueado pela classificação');
conferir(!$consultas, 'Geração com classe divergente incompleta');
conferir(strpos(implode("\n", $sqlExecutado), "T008A_Valor_IBS_Transferido='4.2000'") !== false, 'Valor informado perdido ao liberar geração');
foreach ([['6','01','000','000001','5949'], ['6','05','800','800002','5949'],
    ['5','05','800','800001','1949'], ['6','01','000','800002','5949']] as $caso) {
    list($finalidade, $tipo, $cst, $classe, $cfop) = $caso;
    $item = itemFiscal($classe, $cfop, 'transferencia');
    $item['T008A_Situacao_Tributaria_CBS_IBS'] = $cst;
    conferir($fiscal->validarItemNFDebitoCredito($finalidade, $tipo, $item, '2026-09-08', '3', false) === null, 'Enquadramento ainda bloqueia fluxo operacional');
}

// Crédito 01: permitir gerar com estorno preenchido antes de corrigir a classificação.
prepararGeracao(true, false, false, '1949', true);
$dadosEstorno = array_replace($dados, ['tipoOperacao'=>'credito', 'tipoFiscal'=>'01',
    'reforma'=>[101=>['T008A_Valor_IBS_Estornado'=>'0,50', 'T008A_Valor_CBS_Estornado'=>'0,50']]]);
$gerador = new GeradorNFDC();
$resultado = $gerador->gerarNFDebitoCreditoNotaAutorizada($dadosEstorno);
conferir(is_array($resultado) && !$gerador->excluida && !$consultas, 'Estorno bloqueou geração do Crédito 01');
conferir($resultado['referencia'] === 'cabecalho', 'Estorno alterou referência do Crédito 01');
foreach (['IBS', 'CBS'] as $tributo) {
    conferir(strpos(implode("\n", $sqlExecutado), "T008A_Valor_{$tributo}_Estornado='0.5000'") !== false, 'Estorno não persistido no item');
    conferir(strpos(implode("\n", $sqlExecutado), "T007A_Valor_Total_{$tributo}_Estornado = TOTAIS.{$tributo}_Estornado") !== false, 'Estorno não sincronizado no cabeçalho');
}
foreach ([-1, 'abc', INF, NAN, 1E15] as $valor) {
    $item = itemFiscal('000001', '1949') + ['T008A_Valor_IBS_Estornado'=>$valor];
    conferir($fiscal->validarItemNFDebitoCredito('5', '01', $item, '2026-09-08', '3', false) !== null, 'Liberação aceitou valor inválido');
}
// A atualização real de alíquotas e os recálculos não devem sobrescrever os valores especiais.
$inicioAliquotas = strpos($fonte, 'public function atualizarAliquotasNF(');
$fimAliquotas = strpos($fonte, 'public function ', $inicioAliquotas + 16);
$atualizacaoAliquotas = substr($fonte, $inicioAliquotas, $fimAliquotas - $inicioAliquotas);
conferir($inicioAliquotas !== false && $fimAliquotas > $inicioAliquotas, 'Atualização de alíquotas não encontrada');
foreach (['IBS', 'CBS'] as $tributo) {
    conferir(strpos($atualizacaoAliquotas, "T008A_Valor_{$tributo}_Estornado") === false, 'Atualização de alíquotas sobrescreve estorno');
    foreach (['T008_Gravar_Totalizacao_4.sql', 'T008_Gravar_Totalizacao_4_Reforma.sql'] as $arquivo) {
        conferir(!preg_match('/T008A_Valor_' . $tributo . '_Estornado\s*=/', file_get_contents($raiz . '/scripts/sql/' . $arquivo)), 'Recálculo sobrescreve estorno');
    }
}

if (class_exists('DOMDocument')) {
    $fonte=file_get_contents($raiz . '/hardness/class/nfe4.class.php');
    $inicio=strpos($fonte, '// Conferência final também cobre');
    $fim=strpos($fonte, '// Grupo de informações implementado 08/10/2018', $inicio);
    conferir($inicio!==false && $fim!==false, 'Trecho XML não localizado');
    eval('namespace hardness; class XMLCompatibilidadeNFDC {
        public $errorStatus=false, $mErrorMsg=[], $finalidade;
        public function getFinalidadeNFe() { return $this->finalidade; }
        public function gerar($finalidade,$tipo,$mT008) {
            $this->finalidade=$finalidade;
            $reformaTributaria=new ReformaTributaria();
            $rtcFinalidadeCreditoDebito=in_array($finalidade,["5","6"],true);
            $rtcTipoDebito=$finalidade==="6"?$tipo:""; $rtcTipoCredito=$finalidade==="5"?$tipo:"";
            $mT007=["T007_Data_Emissao"=>"2026-09-05","T007_Empresa_Usuaria_CRT"=>"3"];
            $nSequencial=1; $valorTotalIBSEstornado=0; $valorTotalCBSEstornado=0;
            $oNovo=new \\DOMDocument(); $oM01=$oNovo->createElement("imposto"); $oNovo->appendChild($oM01);
            ' . substr($fonte,$inicio,$fim-$inicio) . '
            return $oNovo;
        }
    }');
    $schema='<?xml version="1.0"?><xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns="http://www.portalfiscal.inf.br/nfe" targetNamespace="http://www.portalfiscal.inf.br/nfe" elementFormDefault="qualified"><xs:include schemaLocation="'
        . $raiz . '/hardness/class/PL/PL_Nfe/DFeTiposBasicos_v1.00.xsd"/><xs:element name="IBSCBS" type="TTribNFe"/></xs:schema>';
    foreach (['01'=>'800002','02'=>'811001','03'=>'811002','05'=>'800001','07'=>'410030','08'=>'811003'] as $tipo=>$classe) {
        $grupo=$fiscal->getRegrasNFDebitoCredito()['6:'.$tipo]['grupo'];
        $gerador=new XMLCompatibilidadeNFDC(); $dom=$gerador->gerar('6',$tipo,itemFiscal($classe,'5949',$grupo));
        conferir($dom!==false && !$gerador->errorStatus, 'XML bloqueou cenário válido');
        $tag=['transferencia'=>'gTransfCred','ajuste'=>'gAjusteCompet','estorno'=>'gEstornoCred'][$grupo];
        conferir($dom->getElementsByTagName($tag)->length===1, 'Grupo especial ausente/duplicado: '.$tipo);
        $fragmento=new \DOMDocument();
        $fragmento->loadXML(str_replace('<IBSCBS>', '<IBSCBS xmlns="http://www.portalfiscal.inf.br/nfe">', $dom->saveXML($dom->getElementsByTagName('IBSCBS')->item(0))));
        conferir($fragmento->schemaValidateSource($schema), 'Grupo incompatível com XSD: '.$tipo);
        if ($grupo==='ajuste') { conferir($dom->getElementsByTagName('competApur')->item(0)->textContent==='2026-08', 'Competência fora do padrão XML'); }
    }
    preg_match_all('/\$mT008\[\x27([^\x27]+)\x27\]/', substr($fonte,$inicio,$fim-$inicio), $camposXML);
    $zeros = array_fill_keys($camposXML[1], '0.00');
    $zeros['T008A_Competencia_Ajuste'] = '';
    foreach ([['6','06','000001','5922'], ['5','04','200054','1202'], ['6','04','550001','5949'],
        ['6','01','000001','5922'], ['6','05','800002','5949'], ['5','05','800001','1949']] as $caso) {
        list($finalidade,$tipo,$classe,$cfop)=$caso;
        $item=array_replace($zeros,itemFiscal($classe,$cfop));
        if ($tipo==='01' || $tipo==='05') { $item['T008A_Valor_IBS_Transferido']=4.2; $item['T008A_Valor_CBS_Transferido']=1.2; }
        if ($classe==='200054') { $item['T008A_Valor_IBS_Estornado']=1; }
        if ($classe==='550001') {
            $item['T008A_Situacao_Tributaria_CBS_IBS_Regular']='000';
            $item['T008A_Classificacao_Tributaria_CBS_IBS_Regular']='000001';
        }
        $gerador=new XMLCompatibilidadeNFDC(); $dom=$gerador->gerar($finalidade,$tipo,$item);
        conferir($dom!==false && !$gerador->errorStatus, 'Cenário tributado válido bloqueado: '.$classe);
        conferir($dom->getElementsByTagName('CST')->item(0)->textContent === substr($classe, 0, 3), 'XML substituiu CST escolhido');
        conferir($dom->getElementsByTagName('cClassTrib')->item(0)->textContent === $classe, 'XML substituiu classificação escolhida');
        $fragmento=new \DOMDocument();
        $fragmento->loadXML(str_replace('<IBSCBS>', '<IBSCBS xmlns="http://www.portalfiscal.inf.br/nfe">', $dom->saveXML($dom->getElementsByTagName('IBSCBS')->item(0))));
        conferir($fragmento->schemaValidateSource($schema), 'Cenário tributado falhou no XSD: '.$classe);
        if ($classe==='200054') { conferir($dom->getElementsByTagName('gEstornoCred')->length===1, 'Estorno da nova classificação ausente'); }
        if ($classe==='550001') { conferir($dom->getElementsByTagName('gTribRegular')->length===1, 'Tributação regular ausente'); }
    }
    // Mesmo item, mesmos valores: o grupo aparece só após o fiscal corrigir a classificação.
    $itemSalvo = array_replace($zeros, itemFiscal('000001', '1949'), [
        'T008A_Valor_IBS_Estornado'=>'0.50', 'T008A_Valor_CBS_Estornado'=>'0.50']);
    foreach ([['000001', 0], ['200054', 1], ['000001', 0]] as $cenarioEstorno) {
        list($classe, $quantidadeGrupos) = $cenarioEstorno;
        $itemSalvo['T008A_Situacao_Tributaria_CBS_IBS'] = substr($classe, 0, 3);
        $itemSalvo['T008A_Classificacao_Tributaria_CBS_IBS'] = $classe;
        $gerador = new XMLCompatibilidadeNFDC();
        $dom = $gerador->gerar('5', '01', $itemSalvo);
        conferir($dom !== false && !$gerador->errorStatus, 'Valores salvos impediram reconstruir o XML');
        conferir($dom->getElementsByTagName('gEstornoCred')->length === $quantidadeGrupos, 'XML não seguiu o indicador atual da classificação');
        if ($quantidadeGrupos) {
            conferir($dom->getElementsByTagName('vIBSEstCred')->item(0)->textContent === '0.50', 'IBS salvo não reaproveitado');
            conferir($dom->getElementsByTagName('vCBSEstCred')->item(0)->textContent === '0.50', 'CBS salvo não reaproveitado');
        }
        $fragmento = new \DOMDocument();
        $fragmento->loadXML(str_replace('<IBSCBS>', '<IBSCBS xmlns="http://www.portalfiscal.inf.br/nfe">', $dom->saveXML($dom->getElementsByTagName('IBSCBS')->item(0))));
        conferir($fragmento->schemaValidateSource($schema), 'Grupo após reenquadramento falhou no XSD');
        conferir($itemSalvo['T008A_Valor_IBS_Estornado'] === '0.50' && $itemSalvo['T008A_Valor_CBS_Estornado'] === '0.50', 'Reconstrução do XML alterou valores salvos');
    }
    echo "XML: grupos reais de transferência, ajuste e estorno validados contra TTribNFe do XSD local.\n";
} else {
    echo "AVISO: DOM indisponível; execute também em PHP com DOM para validar XML/XSD.\n";
}
echo "OK: {$verificacoes} verificações, sem banco de dados ou SEFAZ.\n";
