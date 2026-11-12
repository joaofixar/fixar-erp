<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use PDO;

final class ApplyPricesRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo=$pdo; }
    private function query(string $sql,array $params=[]): \PDOStatement {
        $s=$this->pdo->prepare($sql); $s->execute($params); return $s;
    }
    private function batch(string $public,ExecutionContext $c,string $type): array {
        $b=$this->query('SELECT *,MCP020_Data_Expiracao<=UTC_TIMESTAMP() AS expired FROM MCP020 WHERE MCP020_Public_Id=? AND MCP020_C004_Id=? AND MCP020_MCP001_Id=? FOR UPDATE',[$public,$c->getCompanyId(),$c->getConnectionId()])->fetch(PDO::FETCH_ASSOC);
        if (!$b || $b['MCP020_Tipo']!==$type || (int)$b['MCP020_C007_Id']!==$c->getResponsibleUserId()) throw new \RuntimeException('batch_unavailable');
        return $b;
    }
    public function apply(array $a,ExecutionContext $c,string $type='update'): array {
        if (!$c->hasErpActor() || !$c->getResponsibleUserId()) throw new \RuntimeException('actor_required');
        if (($a['confirmed']??false)!==true || !preg_match('/\A[0-9a-f]{32}\z/',$a['batch_id']) || !preg_match('/\A[0-9a-f]{64}\z/',$a['preview_hash'])) throw new \RuntimeException('approval_required');
        if ($this->pdo->inTransaction()) throw new \RuntimeException('apply_unavailable');
        $engines=$this->query("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('MCP020','MCP021','D009','D009A','D001','D049','D001A')")->fetchAll(PDO::FETCH_COLUMN);
        if(count($engines)!==7 || count(array_filter($engines,static function($e){return strtoupper($e)==='INNODB';}))!==7) throw new \RuntimeException('transactional_storage_required');
        $this->pdo->beginTransaction();
        try {
            $b=$this->batch($a['batch_id'],$c,$type);
            $rows=$this->query('SELECT * FROM MCP021 WHERE MCP021_MCP020_Id=? AND MCP021_C004_Id=? ORDER BY MCP021_Linha FOR UPDATE',[$b['MCP020_Id'],$c->getCompanyId()])->fetchAll(PDO::FETCH_ASSOC);
            if (!$b['MCP020_Hash_Previa'] || !hash_equals(bin2hex($b['MCP020_Hash_Previa']),$a['preview_hash']) || !hash_equals($b['MCP020_Hash_Previa'],PricePreviewIntegrity::hash($rows))) throw new \RuntimeException('preview_changed');
            if($b['MCP020_Data_Aprovacao']!==null) {
                if((int)$b['MCP020_C007_Id_Aprovacao']!==$c->getResponsibleUserId() || (bool)$b['MCP020_Aprovar_Parcial']!==$a['allow_partial'] || (bool)$b['MCP020_Aprovar_Zero']!==$a['allow_zero']) throw new \RuntimeException('approval_mismatch');
                if(!in_array($b['MCP020_Status'],['processing','partial','completed'],true)) throw new \RuntimeException('batch_unavailable');
            } else {
                if($b['expired']) throw new \RuntimeException('preview_expired');
                if($b['MCP020_Status']!=='prepared') throw new \RuntimeException('batch_unavailable');
                foreach($rows as $row) {
                    if($row['MCP021_Status']==='blocked' && !$a['allow_partial']) throw new \RuntimeException('partial_approval_required');
                    if($row['MCP021_Status']==='ready') for($n=1;$n<=5;$n++) {
                        if($row['MCP021_Selecionado_'.$n] && $row['MCP021_Preco_'.$n.'_Depois']==='0.0000000000' && !$a['allow_zero']) throw new \RuntimeException('zero_approval_required');
                    }
                }
                $this->query("UPDATE MCP020 SET MCP020_Status='processing',MCP020_Data_Aprovacao=UTC_TIMESTAMP(),MCP020_Data_Inicio=UTC_TIMESTAMP(),MCP020_C007_Id_Aprovacao=?,MCP020_MCP001_Id_Aprovacao=?,MCP020_Aprovar_Parcial=?,MCP020_Aprovar_Zero=? WHERE MCP020_Id=?",[$c->getResponsibleUserId(),$c->getConnectionId(),(int)$a['allow_partial'],(int)$a['allow_zero'],$b['MCP020_Id']]);
            }
            $this->pdo->commit();
        } catch(\Throwable $e) { if($this->pdo->inTransaction())$this->pdo->rollBack(); throw $e; }
        $start=microtime(true); $applied=0;
        foreach($rows as $row) {
            if(!in_array($row['MCP021_Status'],['ready','failed'],true))continue;
            if(microtime(true)-$start>20)break;
            $this->pdo->beginTransaction();
            try {
                $b=$this->batch($a['batch_id'],$c,$type);
                if($b['expired']) { $this->pdo->rollBack(); break; }
                $item=$this->query('SELECT * FROM MCP021 WHERE MCP021_Id=? AND MCP021_MCP020_Id=? AND MCP021_C004_Id=? FOR UPDATE',[$row['MCP021_Id'],$b['MCP020_Id'],$c->getCompanyId()])->fetch(PDO::FETCH_ASSOC);
                if(!in_array($item['MCP021_Status'],['ready','failed'],true)) { $this->pdo->commit();continue; }
                if (!hash_equals(PricePreviewIntegrity::hash([$row]), PricePreviewIntegrity::hash([$item]))) throw new \RuntimeException('preview_changed');
                $error=$this->applyItem($item,$c->getCompanyId());
                $this->query('UPDATE MCP021 SET MCP021_Status=?,MCP021_Codigo_Erro=?,MCP021_Tentativas=MCP021_Tentativas+1,MCP021_Data_Ultima_Tentativa=UTC_TIMESTAMP(),MCP021_Data_Aplicacao='.($error?'NULL':'UTC_TIMESTAMP()').' WHERE MCP021_Id=?',[$error?'conflict':'applied',$error,$item['MCP021_Id']]);
                $this->pdo->commit();
                if(!$error)$applied++;
            } catch(\Throwable $e) {
                if($this->pdo->inTransaction())$this->pdo->rollBack();
                // Failed SQL/history rolls back both price tables. Preserve committed items.
                $this->pdo->beginTransaction();
                try {
                    $this->batch($a['batch_id'],$c,$type);
                    $this->query("UPDATE MCP021 SET MCP021_Status='failed',MCP021_Codigo_Erro='storage_failure',MCP021_Tentativas=MCP021_Tentativas+1,MCP021_Data_Ultima_Tentativa=UTC_TIMESTAMP() WHERE MCP021_Id=? AND MCP021_MCP020_Id=? AND MCP021_Status IN ('ready','failed')",[$row['MCP021_Id'],$b['MCP020_Id']]);
                    $this->pdo->commit();
                } catch(\Throwable $failure) { if($this->pdo->inTransaction())$this->pdo->rollBack(); throw $failure; }
                break;
            }
        }
        $this->pdo->beginTransaction();
        try {
            $b=$this->batch($a['batch_id'],$c,$type);
            $items=$this->query('SELECT MCP021_Linha,MCP021_Codigo_Produto,MCP021_Status,MCP021_Codigo_Erro FROM MCP021 WHERE MCP021_MCP020_Id=? ORDER BY MCP021_Linha',[$b['MCP020_Id']])->fetchAll(PDO::FETCH_ASSOC);
            $summary=array_fill_keys(['applied','unchanged','blocked','conflict','failed','ready'],0); $output=[];
            foreach($items as $item) { $summary[$item['MCP021_Status']]++; $output[]=['line'=>(int)$item['MCP021_Linha'],'code'=>$item['MCP021_Codigo_Produto'],'status'=>$item['MCP021_Status'],'error'=>$item['MCP021_Codigo_Erro']]; }
            $remaining=$summary['ready']+$summary['failed'];
            $status=($remaining || $summary['blocked'] || $summary['conflict'])?'partial':'completed';
            $this->query('UPDATE MCP020 SET MCP020_Status=?,MCP020_Data_Alteracao=UTC_TIMESTAMP(),MCP020_Data_Conclusao='.($remaining?'NULL':'COALESCE(MCP020_Data_Conclusao,UTC_TIMESTAMP())').' WHERE MCP020_Id=?',[$status,$b['MCP020_Id']]);
            $this->pdo->commit();
            return ['batch_id'=>$a['batch_id'],'status'=>$status,'applied_this_call'=>$applied,'summary'=>$summary,'can_resume'=>$remaining>0 && !$b['expired'],'expired'=>(bool)$b['expired'],'items'=>$output];
        } catch(\Throwable $e) { if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e; }
    }

    private function applyItem(array $item,int $company): ?string {
        $p=$this->query('SELECT d.*,p.D001_Codigo_Produto,p.D001_Flag_Ativo FROM D009 d JOIN D049 v ON v.D049_Id=d.D009_D049_Id JOIN D001 p ON p.D001_Id=v.D049_D001_Id WHERE d.D009_Id=? AND d.D009_C004_Id=? FOR UPDATE',[$item['MCP021_D009_Id'],$company])->fetch(PDO::FETCH_ASSOC);
        if(!$p || $p['D001_Flag_Ativo']!=='S' || $p['D001_Codigo_Produto']!==$item['MCP021_Codigo_Produto'])return 'product_changed';
        $extra=$this->query('SELECT * FROM D009A WHERE D009A_D009_Id=? FOR UPDATE',[$p['D009_Id']])->fetchAll(PDO::FETCH_ASSOC);
        if(count($extra)>1)return 'ambiguous_product';
        $a=$extra[0]??null; $set=[]; $values=[];
        // Compare all five snapshot prices to retain an accurate complete before/after history.
        for($n=1;$n<=5;$n++) {
            $current=$n===5?($a['D009A_Preco_5']??null):$p['D009_Preco_'.$n];
            if($current!==$item['MCP021_Preco_'.$n.'_Antes'])return 'price_changed';
            if(!$item['MCP021_Selecionado_'.$n])continue;
            if($n===5 && !$a)return 'price_5_record_missing';
            if((float)($n===5?$a['D009A_IPT_5']:$p['D009_IPT_'.$n])>0)return 'automatic_price';
            $new=$item['MCP021_Preco_'.$n.'_Depois'];
            if($new===null || PreparePricesInput::decimal($new)!==$new)throw new \RuntimeException('invalid_snapshot');
            if($n<5) { $set[]='D009_Preco_'.$n.'=?'; $values[]=$new; }
        }
        // SQL triggers remain active. Do not invoke unrelated legacy PHP stock/cost hooks.
        if($set || $item['MCP021_Selecionado_5']) {
            $set[]='D009_Data_Alteracao_Preco=UTC_TIMESTAMP()';
            $values[]=$p['D009_Id']; $values[]=$company;
            $this->query('UPDATE D009 SET '.implode(',',$set).' WHERE D009_Id=? AND D009_C004_Id=?',$values);
        }
        if($item['MCP021_Selecionado_5'])$this->query('UPDATE D009A SET D009A_Preco_5=? WHERE D009A_Id=? AND D009A_D009_Id=?',[$item['MCP021_Preco_5_Depois'],$a['D009A_Id'],$p['D009_Id']]);
        $after=$this->query('SELECT D009_Preco_1,D009_Preco_2,D009_Preco_3,D009_Preco_4 FROM D009 WHERE D009_Id=?',[$p['D009_Id']])->fetch(PDO::FETCH_ASSOC);
        $fifth=$this->query('SELECT D009A_Preco_5 FROM D009A WHERE D009A_D009_Id=?',[$p['D009_Id']])->fetch(PDO::FETCH_ASSOC);
        for($n=1;$n<=5;$n++)if(($n===5?($fifth['D009A_Preco_5']??null):$after['D009_Preco_'.$n])!==$item['MCP021_Preco_'.$n.'_Depois'])throw new \RuntimeException('price_not_persisted');
        return null;
    }
}
