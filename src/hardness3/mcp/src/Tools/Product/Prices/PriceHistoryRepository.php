<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use PDO;

/** Read-only history plus preparation of a separately approved reversal batch. */
final class PriceHistoryRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo=$pdo; }
    private function query(string $sql,array $params=[]): \PDOStatement { $s=$this->pdo->prepare($sql);$s->execute($params);return $s; }

    public function history(array $a,ExecutionContext $c): array
    {
        $where='b.MCP020_C004_Id=?'; $params=[$c->getCompanyId()];
        if(isset($a['batch_id'])) { $where.=' AND b.MCP020_Public_Id=?';$params[]=$a['batch_id']; }
        $rows=$this->query("SELECT b.MCP020_Public_Id,b.MCP020_Tipo,b.MCP020_Origem,b.MCP020_Status,b.MCP020_Data_Criacao,b.MCP020_Data_Conclusao,
            b.MCP020_MCP020_Id_Original,COUNT(i.MCP021_Id) total,
            SUM(i.MCP021_Status='applied') applied,SUM(i.MCP021_Status='conflict') conflicts,SUM(i.MCP021_Status='blocked') blocked
            FROM MCP020 b LEFT JOIN MCP021 i ON i.MCP021_MCP020_Id=b.MCP020_Id
            WHERE $where GROUP BY b.MCP020_Id ORDER BY b.MCP020_Id DESC LIMIT ?",array_merge($params,[(int)$a['limit']]))->fetchAll(PDO::FETCH_ASSOC);
        $out=[];
        foreach($rows as $row) $out[]=['batch_id'=>$row['MCP020_Public_Id'],'type'=>$row['MCP020_Tipo'],'origin'=>$row['MCP020_Origem'],
            'status'=>$row['MCP020_Status'],'created_at'=>str_replace(' ','T',$row['MCP020_Data_Criacao']).'Z',
            'completed_at'=>$row['MCP020_Data_Conclusao']===null?null:str_replace(' ','T',$row['MCP020_Data_Conclusao']).'Z',
            'source_batch_id'=>$row['MCP020_MCP020_Id_Original']===null?null:$this->publicId((int)$row['MCP020_MCP020_Id_Original']),
            'summary'=>['total'=>(int)$row['total'],'applied'=>(int)$row['applied'],'conflict'=>(int)$row['conflicts'],'blocked'=>(int)$row['blocked']]];
        if(!isset($a['batch_id'])) return ['batches'=>$out];
        if($out===[]) return ['batches'=>[],'items'=>[]];
        $batch=$this->query('SELECT MCP020_Id FROM MCP020 WHERE MCP020_Public_Id=? AND MCP020_C004_Id=?',[$a['batch_id'],$c->getCompanyId()])->fetch(PDO::FETCH_ASSOC);
        $items=[];
        if($batch) foreach($this->query('SELECT * FROM MCP021 WHERE MCP021_MCP020_Id=? ORDER BY MCP021_Linha',[$batch['MCP020_Id']])->fetchAll(PDO::FETCH_ASSOC) as $row){
            $changes=[];
            for($n=1;$n<=5;$n++)if($row['MCP021_Selecionado_'.$n])$changes[]=['price'=>$n,'before'=>$row['MCP021_Preco_'.$n.'_Antes'],'after'=>$row['MCP021_Preco_'.$n.'_Depois']];
            $items[]=['line'=>(int)$row['MCP021_Linha'],'code'=>$row['MCP021_Codigo_Produto'],'status'=>$row['MCP021_Status'],'error'=>$row['MCP021_Codigo_Erro'],'changes'=>$changes];
        }
        return ['batches'=>$out,'items'=>$items];
    }

    public function prepareReversal(array $a,ExecutionContext $c): array
    {
        if(!$c->hasErpActor() || !$c->getResponsibleUserId()) throw new \RuntimeException('actor_required');
        $input=['source_batch_id'=>$a['source_batch_id'],'request_key'=>$a['request_key'],'lines'=>$a['item_lines']??[]];
        $hash=hash('sha256',json_encode($input,JSON_THROW_ON_ERROR),true);
        $existing=$this->query('SELECT * FROM MCP020 WHERE MCP020_C004_Id=? AND MCP020_MCP001_Id=? AND MCP020_Chave_Idempotencia=?',[$c->getCompanyId(),$c->getConnectionId(),$a['request_key']])->fetch(PDO::FETCH_ASSOC);
        if($existing) {
            if(!hash_equals($existing['MCP020_Hash_Entrada'],$hash) || $existing['MCP020_Tipo']!=='reversal')throw new \RuntimeException('idempotency_conflict');
            return $this->preview($existing);
        }
        $this->pdo->beginTransaction();
        try {
            $source=$this->query("SELECT * FROM MCP020 WHERE MCP020_Public_Id=? AND MCP020_C004_Id=? AND MCP020_Tipo IN ('update','reversal') FOR UPDATE",[$a['source_batch_id'],$c->getCompanyId()])->fetch(PDO::FETCH_ASSOC);
            if(!$source)throw new \RuntimeException('source_batch_unavailable');
            $sourceRows=$this->query("SELECT * FROM MCP021 WHERE MCP021_MCP020_Id=? AND MCP021_Status='applied' ORDER BY MCP021_Linha FOR UPDATE",[$source['MCP020_Id']])->fetchAll(PDO::FETCH_ASSOC);
            $selected=array_flip($a['item_lines']??[]);
            if($selected!==[])$sourceRows=array_values(array_filter($sourceRows,static function($row)use($selected){return isset($selected[(int)$row['MCP021_Linha']]);}));
            if($sourceRows===[])throw new \RuntimeException('no_applied_items');
            $this->query("INSERT INTO MCP020 (MCP020_Public_Id,MCP020_C004_Id,MCP020_MCP001_Id,MCP020_C007_Id,MCP020_Chave_Idempotencia,MCP020_Hash_Entrada,MCP020_Tipo,MCP020_Origem,MCP020_MCP020_Id_Original,MCP020_Data_Criacao,MCP020_Data_Expiracao)
                VALUES (?,?,?,?,?,?, 'reversal','manual',?,UTC_TIMESTAMP(),DATE_ADD(UTC_TIMESTAMP(),INTERVAL 30 MINUTE))",
                [bin2hex(random_bytes(16)),$c->getCompanyId(),$c->getConnectionId(),$c->getResponsibleUserId(),$a['request_key'],$hash,$source['MCP020_Id']]);
            $id=(int)$this->pdo->lastInsertId(); $rows=[];
            foreach($sourceRows as $sourceRow) { $row=$this->reverseRow($sourceRow,$c->getCompanyId());$rows[]=$row;$this->insert($id,$c->getCompanyId(),$row); }
            $persisted=$this->query('SELECT * FROM MCP021 WHERE MCP021_MCP020_Id=? ORDER BY MCP021_Linha',[$id])->fetchAll(PDO::FETCH_ASSOC);
            $this->query("UPDATE MCP020 SET MCP020_Status='prepared',MCP020_Hash_Previa=?,MCP020_Data_Alteracao=UTC_TIMESTAMP() WHERE MCP020_Id=?",[PricePreviewIntegrity::hash($persisted),$id]);
            $batch=$this->query('SELECT * FROM MCP020 WHERE MCP020_Id=?',[$id])->fetch(PDO::FETCH_ASSOC);
            $this->pdo->commit();return $this->preview($batch);
        }catch(\Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }

    private function reverseRow(array $source,int $company): array
    {
        $row=['line'=>(int)$source['MCP021_Linha'],'code'=>$source['MCP021_Codigo_Produto'],'target'=>(int)$source['MCP021_D009_Id'],
            'original'=>(int)$source['MCP021_Id'],'status'=>'ready','error'=>null,'selected'=>[],'before'=>[],'after'=>[]];
        $product=$this->query('SELECT d.*,p.D001_Codigo_Produto,p.D001_Flag_Ativo FROM D009 d JOIN D049 v ON v.D049_Id=d.D009_D049_Id JOIN D001 p ON p.D001_Id=v.D049_D001_Id WHERE d.D009_Id=? AND d.D009_C004_Id=? FOR UPDATE',[$row['target'],$company])->fetch(PDO::FETCH_ASSOC);
        $extra=$product?$this->query('SELECT * FROM D009A WHERE D009A_D009_Id=? FOR UPDATE',[$row['target']])->fetchAll(PDO::FETCH_ASSOC):[];
        $a=count($extra)===1?$extra[0]:null;
        if(!$product || $product['D001_Flag_Ativo']!=='S' || $product['D001_Codigo_Produto']!==$row['code']){$row['status']='blocked';$row['error']='product_changed';}
        if(count($extra)>1){$row['status']='blocked';$row['error']='ambiguous_product';}
        for($n=1;$n<=5;$n++) {
            $row['selected'][$n]=(int)$source['MCP021_Selecionado_'.$n];
            $current=$n===5?($a['D009A_Preco_5']??null):($product['D009_Preco_'.$n]??null);
            $row['before'][$n]=$current;
            $row['after'][$n]=$row['selected'][$n]?$source['MCP021_Preco_'.$n.'_Antes']:$current;
            if(!$row['selected'][$n])continue;
            if($current!==$source['MCP021_Preco_'.$n.'_Depois']){$row['status']='blocked';$row['error']='price_changed_since_original';}
            if($n===5&&!$a){$row['status']='blocked';$row['error']='price_5_record_missing';}
            if((float)($n===5?($a['D009A_IPT_5']??0):($product['D009_IPT_'.$n]??0))>0){$row['status']='blocked';$row['error']='automatic_price';}
        }
        return $row;
    }

    private function insert(int $batch,int $company,array $row): void
    {
        $v=['MCP021_MCP020_Id'=>$batch,'MCP021_C004_Id'=>$company,'MCP021_Linha'=>$row['line'],'MCP021_Codigo_Produto'=>$row['code'],'MCP021_D009_Id'=>$row['target'],'MCP021_MCP021_Id_Original'=>$row['original'],'MCP021_Status'=>$row['status'],'MCP021_Codigo_Erro'=>$row['error']];
        for($n=1;$n<=5;$n++){ $v['MCP021_Selecionado_'.$n]=$row['selected'][$n];$v['MCP021_Preco_'.$n.'_Antes']=$row['before'][$n];$v['MCP021_Preco_'.$n.'_Depois']=$row['after'][$n]; }
        $this->query('INSERT INTO MCP021 ('.implode(',',array_keys($v)).',MCP021_Data_Criacao,MCP021_Data_Snapshot) VALUES ('.implode(',',array_fill(0,count($v),'?')).',UTC_TIMESTAMP(),UTC_TIMESTAMP())',array_values($v));
    }

    private function preview(array $b): array
    {
        $items=$this->query('SELECT * FROM MCP021 WHERE MCP021_MCP020_Id=? ORDER BY MCP021_Linha',[$b['MCP020_Id']])->fetchAll(PDO::FETCH_ASSOC);$out=[];$summary=['ready'=>0,'blocked'=>0];
        foreach($items as $item){$changes=[];for($n=1;$n<=5;$n++)if($item['MCP021_Selecionado_'.$n])$changes[]=['price'=>$n,'current'=>$item['MCP021_Preco_'.$n.'_Antes'],'restore'=>$item['MCP021_Preco_'.$n.'_Depois']];$out[]=['line'=>(int)$item['MCP021_Linha'],'code'=>$item['MCP021_Codigo_Produto'],'status'=>$item['MCP021_Status'],'error'=>$item['MCP021_Codigo_Erro'],'changes'=>$changes];$summary[$item['MCP021_Status']]++;}
        return ['batch_id'=>$b['MCP020_Public_Id'],'source_batch_id'=>$this->publicId((int)$b['MCP020_MCP020_Id_Original']),'status'=>$b['MCP020_Status'],'expires_at'=>str_replace(' ','T',$b['MCP020_Data_Expiracao']).'Z','preview_hash'=>bin2hex($b['MCP020_Hash_Previa']),'summary'=>$summary,'items'=>$out];
    }
    private function publicId(int $id): ?string { $v=$this->query('SELECT MCP020_Public_Id FROM MCP020 WHERE MCP020_Id=?',[$id])->fetchColumn();return $v===false?null:$v; }
}
