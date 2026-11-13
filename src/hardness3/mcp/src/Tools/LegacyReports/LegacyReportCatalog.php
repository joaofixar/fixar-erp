<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\LegacyReports;

/** Server-owned inventory of legacy report routes. Custom reports are appended per tenant. */
final class LegacyReportCatalog
{
    /** @var array<string,array<string,mixed>> */ private array $reports=[];
    /** @param array<int,array<string,mixed>> $custom */
    public function __construct(array $custom=[], bool $includeStandard=true)
    {
        $dated=['orcamentos','orcamentosProdutos','pedidos','pedidosExpedicao','pedidosProdutos','notasSaida','notasSaidaDetalhes','notasSaidaProdutos','cotacoes','cotacoesProdutos','ordensDeCompra','ordensDeCompraProdutos','conhecimentos','notasEntrada','notasEntradaProdutos','notasEntradaProdutosTributos','contasReceber','contasReceberDetalhes','contasPagar','contasPagarDetalhes','lancamentosBancarios','RMA','custoRMA','atividades','assuntosAtividades','produtoImportacao'];
        $standard=['clientes'=>'Clientes','empresas'=>'Empresas','transportadoras'=>'Transportadoras','produtos'=>'Produtos','portadoresReceber'=>'Portadores à Receber','contas'=>'Contas','centroDeCustos'=>'Centro de Custo','portadoresPagar'=>'Portadores à Pagar','orcamentos'=>'Orçamentos','orcamentosProdutos'=>'Produtos Orçamentos','pedidos'=>'Pedidos','pedidosExpedicao'=>'Pedidos Expedição','pedidosProdutos'=>'Produtos Pedidos','notasSaida'=>'NF\'s Saída','notasSaidaDetalhes'=>'NF\'s Saída Detalhes','notasSaidaProdutos'=>'NF\'s Saída Produtos','cotacoes'=>'Cotações','cotacoesProdutos'=>'Produtos Cotações','ordensDeCompra'=>'Ordens de Compra','ordensDeCompraProdutos'=>'Produtos OC','conhecimentos'=>'Conhecimentos','notasEntrada'=>'NF\'s Entrada','notasEntradaProdutos'=>'NF\'s Entrada Produtos','notasEntradaProdutosTributos'=>'NF\'s Entrada Tributos','contasReceber'=>'Contas à Receber','contasReceberDetalhes'=>'Contas à Receber Detalhes','contasPagar'=>'Contas à Pagar','contasPagarDetalhes'=>'Contas à Pagar Detalhes','lancamentosBancarios'=>'Lançamentos Bancários','tiposCFOP'=>'Tipos CFOP','CFOP'=>'CFOP\'s','usuarios'=>'Usuários','vendedoresInternos'=>'Vendedores Internos','supervisores'=>'Supervisores','vendedoresExternos'=>'Vendedores Externos','supervisores2'=>'Supervisores 2','vendedoresSubstitutos'=>'Vendedores Substitutos','compradoresInternos'=>'Compradores Internos','compradoresExternos'=>'Compradores Externos','estoque'=>'Estoque','estoque_terceiros'=>'Estoque Terceiros','RMA'=>'RMA\'s','custoRMA'=>'Custos RMA','atividades'=>'Atividades','assuntosAtividades'=>'Assuntos das atividades','produtoImportacao'=>'Produtos Importação'];
        if($includeStandard)foreach($standard as $route=>$name)$this->add(['id'=>$this->slug($route),'name'=>$name,'description'=>$name,'route'=>$route,'requires_dates'=>in_array($route,$dated,true),'parameters'=>in_array($route,$dated,true)?['DATAINICIAL','DATAFINAL']:[]]);
        foreach($custom as $report) if(is_array($report)) $this->add($report);
    }
    public static function fromTenant(string $erpRootPath, string $database): self
    {
        $root=rtrim($erpRootPath,'/');
        $custom=$root.'/dados_usuarios/'.$database.'/personalizacaoConf/api-api001-outro-API001OutroLinksApi-alterarPrograma.php';
        $source=is_file($custom)?$custom:$root.'/prog/api/api001/outro.php';
        $text=@file_get_contents($source); if(!is_string($text)||$text==='')return new self();
        $reports=[];
        if(preg_match_all('/array\(\s*"metodo"\s*=>\s*"([^"]+)"(.*?)"link"\s*=>\s*"([^"]+)"/s',$text,$matches,PREG_SET_ORDER))foreach($matches as $match){
            $link=html_entity_decode($match[3],ENT_QUOTES,'UTF-8');$path=parse_url('http://legacy/'.ltrim(str_replace('{$dominio}','legacy',$link),'/'),PHP_URL_PATH);$query=parse_url('http://legacy/'.ltrim(str_replace('{$dominio}','legacy',$link),'/'),PHP_URL_QUERY);if(!is_string($path))continue;
            $parts=array_values(array_filter(explode('/',trim($path,'/'))));$index=array_search('rel',$parts,true);if($index===false||!isset($parts[$index+1]))continue;$route=$parts[$index+1]==='get'?($parts[$index+2]??''):$parts[$index+1];if($route==='')continue;
            parse_str((string)$query,$params);$allowed=[];foreach($params as $key=>$_)if($key!=='API_AUTH'&&$key!=='DATAINICIAL'&&$key!=='DATAFINAL'&&$key!=='codigoApp')$allowed[]=$key;
            $reports[]=['id'=>self::sourceSlug($match[1]),'name'=>$match[1],'description'=>self::sourceObservation($match[2]),'route'=>$route,'app_code'=>isset($params['codigoApp'])?(string)$params['codigoApp']:null,'requires_dates'=>strpos($link,'DATAINICIAL')!==false||strpos($link,'DATAFINAL')!==false,'parameters'=>$allowed];
        }
        return $reports===[]?new self():new self($reports,false);
    }
    /** @param array<string,mixed> $report */ private function add(array $report):void { $id=$report['id']??''; $route=$report['route']??''; if(!is_string($id)||preg_match('/\A[a-z][a-z0-9_]{0,79}\z/',$id)!==1||!is_string($route)||preg_match('/\A[A-Za-z0-9_]+\z/',$route)!==1)return; $report['name']=is_string($report['name']??null)?$report['name']:$id;$report['description']=is_string($report['description']??null)?$report['description']:$report['name'];$report['requires_dates']=(bool)($report['requires_dates']??false);$report['parameters']=is_array($report['parameters']??null)?array_values($report['parameters']):[];$this->reports[$id]=$report; }
    /** @return array<int,array<string,mixed>> */ public function all():array { return array_values($this->reports); }
    /** @return array<string,mixed>|null */ public function find(string $id):?array { return $this->reports[$id]??null; }
    private function slug(string $value):string { return strtolower(preg_replace('/[^a-z0-9]+/i','_',$value)??'report'); }
    private static function sourceSlug(string $value):string { return strtolower(trim(preg_replace('/[^a-z0-9]+/i','_',$value)??'report','_')); }
    private static function sourceObservation(string $chunk):string { return preg_match('/"obs"\s*=>\s*"([^"]*)"/s',$chunk,$m)?$m[1]:''; }
}
