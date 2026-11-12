<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin;

use HardnessWebhooks\Admin\Context\WebhookAdminContext;

final class WebhookAdminQueryService
{
    private $repository;
    private $catalog;
    public function __construct(?WebhookAdminRepository $repository = null, ?WebhookAdminCatalogService $catalog = null) { $this->repository=$repository??new WebhookAdminRepository(); $this->catalog=$catalog??new WebhookAdminCatalogService(); }
    /** @return array<int,array<string,mixed>> */ public function list(\mysqli $connection, WebhookAdminContext $context): array { return $this->repository->list($context,$connection); }
    /** @return array<string,mixed>|null */ public function detail(\mysqli $connection, WebhookAdminContext $context, int $id, ?string $deliverySearch = null, ?string $deliveryStatus = null): ?array { $row=$this->repository->find($context,$connection,$id); if($row===null)return null;$search=$this->deliverySearch($deliverySearch);$status=$this->deliveryStatus($deliveryStatus);$catalog=$this->catalog->available($connection,$context);$labels=array();$aggregateLabels=array();$matchingTypes=array();foreach($catalog as $event){$type=$event['type'];$labels[$type]=$event['label'];$aggregateLabels[$type]=$event['aggregate_label']??'Registro';if($search!==null&&$this->contains((string)$event['label'].' '.(string)$aggregateLabels[$type],$search))$matchingTypes[]=$type;}$row['catalog']=$catalog;$row['delivery_search']=$search??'';$row['delivery_status']=$status??'';$row['deliveries']=$this->repository->deliveries($context,$connection,$id,30,$search,$matchingTypes,$status);foreach($row['deliveries'] as &$delivery){$type=(string)$delivery['event_type'];$delivery['event_label']=$labels[$type]??$type;$delivery['reference_label']=$aggregateLabels[$type]??'Registro';$delivery['reference']=$delivery['reference_label'].' #'.(string)$delivery['aggregate_id'];}unset($delivery);return $row; }
    /** @return array<int,array<string,mixed>> */ public function catalog(\mysqli $connection, WebhookAdminContext $context): array { return $this->catalog->available($connection,$context); }
    private function deliverySearch(?string $value):?string{if($value===null)return null;$value=trim($value);if($value==='')return null;if(strlen($value)>100||preg_match('/[[:cntrl:]]/',$value))throw new \InvalidArgumentException('Busca de entregas inválida.');return $value;}
    private function deliveryStatus(?string $value):?string{if($value===null||$value==='')return null;if(!in_array($value,array('P','L','R','D','X','C'),true))throw new \InvalidArgumentException('Status de entrega inválido.');return $value;}
    private function contains(string $value,string $search):bool{return function_exists('mb_stripos')?mb_stripos($value,$search,0,'UTF-8')!==false:stripos($value,$search)!==false;}
}
