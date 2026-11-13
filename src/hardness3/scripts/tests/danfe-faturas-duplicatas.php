<?php
// Teste isolado: nao carrega conexao e nao executa consultas.
error_reporting(E_ALL & ~E_DEPRECATED);
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

$raiz = dirname(__DIR__, 2);
chdir($raiz . '/hardness/danfe');
require_once $raiz . '/hardness/class/danfe400.php';

// O PHP CLI deste ambiente nao tem ext-dom. Estes objetos implementam
// apenas a leitura de nos usada pelo formatador. Com ext-dom, usa XML real.
class ListaNosDanfeTeste implements IteratorAggregate
{
    public $length;
    private $nos;

    public function __construct($nos)
    {
        $this->nos = $nos;
        $this->length = count($nos);
    }

    public function item($indice)
    {
        return isset($this->nos[$indice]) ? $this->nos[$indice] : null;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->nos);
    }
}

class NoDanfeTeste
{
    public $nodeValue;
    private $campos;

    public function __construct($campos = array(), $valor = '')
    {
        $this->campos = $campos;
        $this->nodeValue = $valor;
    }

    public function getElementsByTagName($tag)
    {
        return new ListaNosDanfeTeste(isset($this->campos[$tag])
            ? array(new self(array(), $this->campos[$tag])) : array());
    }
}

function listaDanfeTeste($tag, $registros)
{
    if (class_exists('DOMDocument')) {
        $dom = new DOMDocument();
        $raiz = $dom->appendChild($dom->createElement('raiz'));
        foreach ($registros as $registro) {
            $no = $raiz->appendChild($dom->createElement($tag));
            foreach ($registro as $campo => $valor) {
                $no->appendChild($dom->createElement($campo))->appendChild($dom->createTextNode($valor));
            }
        }
        return $dom->getElementsByTagName($tag);
    }
    return new ListaNosDanfeTeste(array_map(function ($registro) {
        return new NoDanfeTeste($registro);
    }, $registros));
}

function montarDanfeTeste($duplicatas, $pagamentos, $parcelas = array())
{
    $danfe = new \hardness\danfe();
    return $danfe->montarFaturasDuplicatas(listaDanfeTeste('dup', $duplicatas),
        listaDanfeTeste('detPag', $pagamentos), $parcelas);
}

function verificarDanfeTeste($condicao, $mensagem)
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}

$duplicata = array('nDup' => '001', 'dVenc' => '2026-11-23', 'vDup' => '200054.97');
$deposito = array('tPag' => '16', 'vPag' => '200054.97');
$semPagamento = array('tPag' => '90', 'vPag' => '0.00');
$parcela = array('T118_Numero_Parcela' => '1', 'T118_Data_Vencimento' => '2026-11-23',
    'T118_Valor_Parcela' => '200054.97', 'D027_Tipo_Forma_Pagamento_NFCE' => '16');

// SEM PAGAMENTO vem antes do deposito no XML.
$esperado = '001 DEPÓSITO BANCÁRIO <b>23/11/26</b><b> R$200.054,97&nbsp;&nbsp;</b>'
    . 'SEM PAGAMENTO <b> R$0,00&nbsp;&nbsp;</b>';
$html = montarDanfeTeste(array($duplicata), array($semPagamento, $deposito), array($parcela));
verificarDanfeTeste($html === $esperado, 'Deve mostrar o deposito e o pagamento zerado separadamente.');
verificarDanfeTeste(montarDanfeTeste(array($duplicata), array($deposito, $semPagamento), array($parcela)) === $esperado,
    'Inverter detPag nao pode mudar a identificacao da duplicata.');
verificarDanfeTeste(montarDanfeTeste(array($duplicata), array($semPagamento, $deposito)) === $esperado,
    'Um unico meio com valor pode identificar a duplicata mesmo sem T118.');

// Dois meios e duas parcelas com valores iguais, em ordens opostas.
$dups = array(array('nDup' => '001', 'dVenc' => '2026-11-23', 'vDup' => '100.00'),
              array('nDup' => '002', 'dVenc' => '2026-12-23', 'vDup' => '100.00'));
$pags = array(array('tPag' => '17', 'vPag' => '100.00'), array('tPag' => '16', 'vPag' => '100.00'));
$parcelas = array(array('T118_Numero_Parcela' => '1', 'T118_Data_Vencimento' => '2026-11-23',
                       'T118_Valor_Parcela' => '100.00', 'D027_Tipo_Forma_Pagamento_NFCE' => '16'),
                  array('T118_Numero_Parcela' => '2', 'T118_Data_Vencimento' => '2026-12-23',
                       'T118_Valor_Parcela' => '100.00', 'D027_Tipo_Forma_Pagamento_NFCE' => '17'));
$misto = montarDanfeTeste($dups, $pags, $parcelas);
verificarDanfeTeste(strpos($misto, '001 DEPÓSITO BANCÁRIO <b>23/11/26</b>') === 0,
    'A primeira parcela deve usar o deposito identificado em T118.');
verificarDanfeTeste(strpos($misto, '002 PIX <b>23/12/26</b>') !== false,
    'A segunda parcela deve usar PIX, independente da posicao em detPag.');

// Sem correspondencia segura, preserva os dados do XML e omite o meio.
$semVinculo = montarDanfeTeste($dups, $pags);
verificarDanfeTeste(strpos($semVinculo, 'PIX') === false && strpos($semVinculo, 'DEPÓSITO') === false,
    'Nao deve adivinhar o meio de pagamento quando o XML nao informa o vinculo.');
$parcelas[0]['T118_Valor_Parcela'] = '99.00';
$parcelas[1]['T118_Data_Vencimento'] = '2026-12-24';
verificarDanfeTeste(montarDanfeTeste($dups, $pags, $parcelas) === $semVinculo,
    'Dados atuais diferentes do XML nao devem vincular o portador.');

// Um pagamento cobre varias duplicatas.
$parcelado = montarDanfeTeste($dups, array(array('tPag' => '16', 'vPag' => '200.00')));
verificarDanfeTeste(substr_count($parcelado, 'DEPÓSITO BANCÁRIO') === 2,
    'Um detPag pode cobrir mais de uma duplicata.');
verificarDanfeTeste(substr_count($parcelado, 'R$100,00') === 2 && strpos($parcelado, 'R$200,00') === false,
    'O total de detPag nao pode duplicar os valores das parcelas.');

// Fluxos sem duplicatas, com e sem pagamento.
verificarDanfeTeste(montarDanfeTeste(array(), array($semPagamento)) === 'SEM PAGAMENTO <b> R$0,00&nbsp;&nbsp;</b>',
    'SEM PAGAMENTO nao deve ser rotulado como a vista.');
verificarDanfeTeste(montarDanfeTeste(array(), array($deposito)) === 'DEPÓSITO BANCÁRIO À VISTA <b> R$200.054,97&nbsp;&nbsp;</b>',
    'Pagamento sem dup deve manter a apresentacao a vista.');
verificarDanfeTeste(montarDanfeTeste(array(), array()) === '', 'Listas vazias devem gerar texto vazio.');
$dupZerada = array('nDup' => '001', 'dVenc' => '2026-10-19', 'vDup' => '0.00');
$parcelaZerada = array('T118_Numero_Parcela' => '1', 'T118_Data_Vencimento' => '2026-10-19',
    'T118_Valor_Parcela' => '0.00', 'D027_Tipo_Forma_Pagamento_NFCE' => '90');
$semDuplicar = montarDanfeTeste(array($dupZerada), array($semPagamento), array($parcelaZerada));
verificarDanfeTeste(substr_count($semDuplicar, 'SEM PAGAMENTO') === 1 && substr_count($semDuplicar, 'R$0,00') === 1,
    'Pagamento zerado ja representado por dup nao deve ser repetido.');
$semMeio = montarDanfeTeste(array($duplicata), array());
verificarDanfeTeste(strpos($semMeio, '001 <b>23/11/26</b><b> R$200.054,97') === 0,
    'Duplicata sem detPag deve preservar numero, vencimento e valor.');

// Mantem o limite de nove registros e as descricoes abreviadas.
$muitas = array();
for ($i = 1; $i <= 10; $i++) {
    $muitas[] = array('nDup' => str_pad($i, 3, '0', STR_PAD_LEFT), 'dVenc' => '2026-11-23', 'vDup' => '10.00');
}
$limitado = montarDanfeTeste($muitas, array(array('tPag' => '16', 'vPag' => '100.00')));
verificarDanfeTeste(substr_count($limitado, 'R$10,00') === 9 && strpos($limitado, '010 ') === false,
    'Deve respeitar o limite de nove registros.');
verificarDanfeTeste(substr_count($limitado, 'DEP BANC ') === 9, 'Deve abreviar o portador em muitas parcelas.');

// O XML pode vir dos titulos T002 e omitir a
// parcela zerada de T118. O valor positivo do XML deve ser preservado.
$dupLocal = array('nDup' => '001', 'dVenc' => '2026-09-25', 'vDup' => '135.00');
$pagLocal = array('tPag' => '01', 'vPag' => '135.00');
$parcelasLocais = array(
    array('T118_Numero_Parcela' => '1', 'T118_Data_Vencimento' => '2026-09-25',
          'T118_Valor_Parcela' => '67.50', 'D027_Tipo_Forma_Pagamento_NFCE' => '01'),
    array('T118_Numero_Parcela' => '2', 'T118_Data_Vencimento' => '2026-10-23',
          'T118_Valor_Parcela' => '0.00', 'D027_Tipo_Forma_Pagamento_NFCE' => '90')
);
$localSemNoXML = montarDanfeTeste(array($dupLocal), array($pagLocal), $parcelasLocais);
verificarDanfeTeste(strpos($localSemNoXML, 'SEM PAGAMENTO <b> R$0,00') !== false,
    'Deve mostrar a parcela zerada de T118 mesmo quando T002 nao a inclui no XML.');
verificarDanfeTeste(strpos($localSemNoXML, 'R$135,00') !== false && strpos($localSemNoXML, 'R$67,50') === false,
    'A diferenca do valor positivo deve ser investigada na origem; o formatador preserva o XML.');
$localComNoXML = montarDanfeTeste(array($dupLocal), array($pagLocal, $semPagamento), $parcelasLocais);
verificarDanfeTeste($localComNoXML === $localSemNoXML,
    'A parcela zerada existente no XML nao deve ser repetida por T118.');
$parcelasLocais[1]['T118_Valor_Parcela'] = '10.00';
verificarDanfeTeste(strpos(montarDanfeTeste(array($dupLocal), array($pagLocal), $parcelasLocais), 'SEM PAGAMENTO') === false,
    'Nao deve acrescentar valores financeiros de T118 que nao constam no XML.');
$parcelasLocais[1]['T118_Valor_Parcela'] = null;
verificarDanfeTeste(strpos(montarDanfeTeste(array($dupLocal), array($pagLocal), $parcelasLocais), 'SEM PAGAMENTO') === false,
    'Valor ausente nao deve ser interpretado como parcela zerada.');
$parcelasLocais[1]['T118_Valor_Parcela'] = '0.00';
$parcelasLocais[1]['D027_Tipo_Forma_Pagamento_NFCE'] = '01';
verificarDanfeTeste(strpos(montarDanfeTeste(array($dupLocal), array($pagLocal), $parcelasLocais), 'SEM PAGAMENTO') === false,
    'Somente o tipo 90 pode gerar o complemento SEM PAGAMENTO.');

echo "OK: regressao do cliente, ordens invertidas, meios mistos, dados divergentes, parcelamento, sem duplicatas e limite de impressao.\n";
echo "Modo: " . (class_exists('DOMDocument') ? 'DOM real' : 'objetos de leitura de nos (ext-dom ausente)') . ".\n";
echo "Pagamento zerado no XML: " . html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8') . "\n";
echo "Pagamento zerado somente em T118: " . html_entity_decode(strip_tags($localSemNoXML), ENT_QUOTES, 'UTF-8') . "\n";
