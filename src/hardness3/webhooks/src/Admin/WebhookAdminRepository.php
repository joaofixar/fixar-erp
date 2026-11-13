<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin;

use HardnessWebhooks\Admin\Context\WebhookAdminContext;

/** WHK persistence boundary. Every endpoint lookup is scoped by the immutable context. */
final class WebhookAdminRepository
{
    /** @return array<int,array<string,mixed>> */
    public function list(WebhookAdminContext $context, \mysqli $connection): array
    {
        $sql = "SELECT e.WHK001_Id id,e.WHK001_Nome name,e.WHK001_URL url,e.WHK001_Status status,e.WHK001_Destination_Auth_Type destination_auth_type,e.WHK001_Destination_Auth_Criptografada destination_auth_ciphertext,e.WHK001_Data_Criacao created_at,e.WHK001_Data_Alteracao updated_at,GROUP_CONCAT(CASE WHEN s.WHK002_Filtro IS NULL THEN s.WHK002_Event_Type END ORDER BY s.WHK002_Event_Type SEPARATOR ',') events FROM WHK001 e LEFT JOIN WHK002 s ON s.WHK002_WHK001_Id=e.WHK001_Id AND s.WHK002_C004_Id=e.WHK001_C004_Id WHERE e.WHK001_C004_Id=? GROUP BY e.WHK001_Id ORDER BY e.WHK001_Id DESC";
        $statement = $this->prepare($connection, $sql); try { $company = $context->getCompanyId(); $statement->bind_param('i', $company); $statement->execute(); $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC); foreach ($rows as &$row) { $row['id']=(int)$row['id']; $row['events']=$row['events'] === null || $row['events']==='' ? array() : explode(',', $row['events']); $row['destination_auth']=$this->destinationAuthProjection($row['destination_auth_type'], $row['destination_auth_ciphertext']); unset($row['destination_auth_type'],$row['destination_auth_ciphertext']); } return $rows; } finally { $statement->close(); }
    }
    /** @return array<string,mixed>|null */
    public function find(WebhookAdminContext $context, \mysqli $connection, int $id): ?array
    {
        $sql="SELECT e.WHK001_Id id,e.WHK001_Nome name,e.WHK001_URL url,e.WHK001_Status status,e.WHK001_Destination_Auth_Type destination_auth_type,e.WHK001_Destination_Auth_Criptografada destination_auth_ciphertext,e.WHK001_Data_Criacao created_at,e.WHK001_Data_Alteracao updated_at FROM WHK001 e WHERE e.WHK001_Id=? AND e.WHK001_C004_Id=? LIMIT 1";
        $statement=$this->prepare($connection,$sql); try { $company=$context->getCompanyId(); $statement->bind_param('ii',$id,$company); $statement->execute(); $row=$statement->get_result()->fetch_assoc(); if (!is_array($row)) return null; $row['id']=(int)$row['id']; $row['destination_auth']=$this->destinationAuthProjection($row['destination_auth_type'],$row['destination_auth_ciphertext']); unset($row['destination_auth_type'],$row['destination_auth_ciphertext']); $row['subscriptions']=$this->subscriptions($context,$connection,$id); return $row; } finally { $statement->close(); }
    }
    public function create(WebhookAdminContext $context, \mysqli $connection, string $name, string $url, array $events, string $destinationAuthType, ?string $destinationAuthCiphertext): int
    {
        $statement=$this->prepare($connection,"INSERT INTO WHK001 (WHK001_C004_Id,WHK001_Nome,WHK001_URL,WHK001_URL_Hash,WHK001_Destination_Auth_Type,WHK001_Destination_Auth_Criptografada,WHK001_Status) VALUES (?,?,?,?,?,?,'I')"); try { $company=$context->getCompanyId(); $hash=hash('sha256',$url,true); $statement->bind_param('isssss',$company,$name,$url,$hash,$destinationAuthType,$destinationAuthCiphertext); $statement->execute(); $id=(int)$connection->insert_id; } finally {$statement->close();}
        $this->syncSubscriptions($context,$connection,$id,$events); return $id;
    }
    /** Safe, bounded operational projection; never selects payloads or credentials.
     * @return array<int,array<string,mixed>> */
    public function deliveries(WebhookAdminContext $context, \mysqli $connection, int $id, int $limit = 30, ?string $search = null, array $matchingEventTypes = array(), ?string $status = null): array
    {
        $limit=max(1,min(50,$limit));
        $sql='SELECT d.WHK004_Id id,d.WHK004_Status status,d.WHK004_Attempt_Count attempt_count,d.WHK004_Last_Http_Status last_http_status,d.WHK004_Last_Error last_error,d.WHK004_Next_Attempt_At next_attempt_at,d.WHK004_Leased_At leased_at,d.WHK004_Delivered_At delivered_at,d.WHK004_Data_Criacao created_at,d.WHK004_Data_Alteracao updated_at,e.WHK003_Id event_record_id,e.WHK003_Type event_type,e.WHK003_Aggregate_Type aggregate_type,e.WHK003_Aggregate_Id aggregate_id,e.WHK003_Occurred_At occurred_at FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id=d.WHK004_WHK003_Id AND e.WHK003_C004_Id=d.WHK004_C004_Id WHERE d.WHK004_WHK001_Id=? AND d.WHK004_C004_Id=?';$types='ii';$values=array($id,$context->getCompanyId());
        if($status!==null){$sql.=' AND d.WHK004_Status=?';$types.='s';$values[]=$status;}
        if($search!==null){$like='%'.$search.'%';$filter='(e.WHK003_Aggregate_Id LIKE ? OR e.WHK003_Type LIKE ? OR e.WHK003_Aggregate_Type LIKE ?';$types.='sss';$values[]=$like;$values[]=$like;$values[]=$like;if($matchingEventTypes!==array()){$filter.=' OR e.WHK003_Type IN ('.implode(',',array_fill(0,count($matchingEventTypes),'?')).')';foreach($matchingEventTypes as $eventType){$types.='s';$values[]=$eventType;}}$sql.=' AND '.$filter.')';}
        $sql.=' ORDER BY d.WHK004_Data_Criacao DESC,d.WHK004_Id DESC LIMIT ?';$types.='i';$values[]=$limit;
        $statement=$this->prepare($connection,$sql);try{$this->bind($statement,$types,$values);$statement->execute();$rows=$statement->get_result()->fetch_all(MYSQLI_ASSOC);}finally{$statement->close();}
        if($rows===array())return array();$ids=array();foreach($rows as &$row){$row['id']=(int)$row['id'];$row['event_record_id']=(int)$row['event_record_id'];$row['attempt_count']=(int)$row['attempt_count'];$row['last_http_status']=$row['last_http_status']===null?null:(int)$row['last_http_status'];$row['last_error']=$this->safeOperationalError($row['last_error']);$row['attempts']=array();$ids[]=$row['id'];}unset($row);
        $attemptSql='SELECT WHK005_WHK004_Id delivery_id,WHK005_Attempt_Number attempt_number,WHK005_Started_At started_at,WHK005_Finished_At finished_at,WHK005_Result result,WHK005_Http_Status http_status,WHK005_Transport_Error transport_error FROM WHK005 WHERE WHK005_WHK004_Id IN ('.implode(',',array_map('intval',$ids)).') ORDER BY WHK005_WHK004_Id ASC,WHK005_Attempt_Number ASC';
        $result=$connection->query($attemptSql);if($result===false)throw new \RuntimeException('Could not query webhook delivery attempts.');$attempts=array();while($attempt=$result->fetch_assoc()){$deliveryId=(int)$attempt['delivery_id'];$attempts[$deliveryId][]=array('number'=>(int)$attempt['attempt_number'],'started_at'=>$attempt['started_at'],'finished_at'=>$attempt['finished_at'],'result'=>$this->safeAttemptResult($attempt['result']),'http_status'=>$attempt['http_status']===null?null:(int)$attempt['http_status'],'transport_error'=>$this->safeOperationalError($attempt['transport_error']));}foreach($rows as &$row)$row['attempts']=$attempts[$row['id']]??array();unset($row);return $rows;
    }
    public function update(WebhookAdminContext $context, \mysqli $connection, int $id, string $name, string $url, array $events, string $destinationAuthType, ?string $destinationAuthCiphertext): bool
    {
        if (!$this->exists($context,$connection,$id,true)) return false;
        $statement=$this->prepare($connection,'UPDATE WHK001 SET WHK001_Nome=?,WHK001_URL=?,WHK001_URL_Hash=?,WHK001_Destination_Auth_Type=?,WHK001_Destination_Auth_Criptografada=?,WHK001_Data_Alteracao=UTC_TIMESTAMP() WHERE WHK001_Id=? AND WHK001_C004_Id=?'); try { $hash=hash('sha256',$url,true); $company=$context->getCompanyId(); $statement->bind_param('sssssii',$name,$url,$hash,$destinationAuthType,$destinationAuthCiphertext,$id,$company); $statement->execute(); } finally {$statement->close();} $this->syncSubscriptions($context,$connection,$id,$events); return true;
    }
    /** @return array{type:string,ciphertext:?string}|null Internal-only ciphertext lookup for an authenticated mutation. */
    public function destinationAuthenticationForUpdate(WebhookAdminContext $context, \mysqli $connection, int $id): ?array
    {
        $statement=$this->prepare($connection,'SELECT WHK001_Destination_Auth_Type,WHK001_Destination_Auth_Criptografada FROM WHK001 WHERE WHK001_Id=? AND WHK001_C004_Id=? FOR UPDATE');
        try { $company=$context->getCompanyId(); $statement->bind_param('ii',$id,$company); $statement->execute(); $statement->bind_result($type,$ciphertext); if (!$statement->fetch()) return null; return array('type'=>(string)$type,'ciphertext'=>$ciphertext===null?null:(string)$ciphertext); } finally {$statement->close();}
    }
    /** @return string|null Internal HMAC state retained for future administrative actions. */
    public function secretState(WebhookAdminContext $context, \mysqli $connection, int $id): ?string
    {
        $statement=$this->prepare($connection,"SELECT v.WHK007_Status FROM WHK001 e LEFT JOIN WHK007 v ON v.WHK007_Id=e.WHK001_WHK007_Id_Atual AND v.WHK007_WHK001_Id=e.WHK001_Id WHERE e.WHK001_Id=? AND e.WHK001_C004_Id=? LIMIT 1");
        try{$company=$context->getCompanyId();$statement->bind_param('ii',$id,$company);$statement->execute();$statement->bind_result($status);if(!$statement->fetch())return null;return $status===null?'missing':($status==='A'?'active':($status==='R'?'revoked':'inactive'));}finally{$statement->close();}
    }
    public function activate(WebhookAdminContext $context, \mysqli $connection, int $id, array $allowedEvents): bool
    {
        if (!$this->exists($context,$connection,$id,true)) return false;
        $selected=$this->selectedEvents($context,$connection,$id); if ($selected===array()) throw new \InvalidArgumentException('Webhook não possui eventos selecionados.');
        foreach($selected as $event) if (!in_array($event,$allowedEvents,true)) throw new \InvalidArgumentException('Evento não disponível para assinatura.');
        $this->setEndpointStatus($context,$connection,$id,'A'); $statement=$this->prepare($connection,"UPDATE WHK002 SET WHK002_Status='A',WHK002_Data_Alteracao=UTC_TIMESTAMP() WHERE WHK002_WHK001_Id=? AND WHK002_C004_Id=? AND WHK002_Filtro IS NULL");try{$company=$context->getCompanyId();$statement->bind_param('ii',$id,$company);$statement->execute();}finally{$statement->close();} return true;
    }
    public function deactivate(WebhookAdminContext $context, \mysqli $connection, int $id): bool
    {
        if (!$this->exists($context,$connection,$id,true)) return false; $this->setEndpointStatus($context,$connection,$id,'I');
        $statement=$this->prepare($connection,"UPDATE WHK002 SET WHK002_Status='I',WHK002_Data_Alteracao=UTC_TIMESTAMP() WHERE WHK002_WHK001_Id=? AND WHK002_C004_Id=?"); try{$company=$context->getCompanyId();$statement->bind_param('ii',$id,$company);$statement->execute();}finally{$statement->close();}
        // P/R are safe to cancel atomically; leased work retains worker ownership and is prevented from future retries by endpoint status.
        $statement=$this->prepare($connection,"UPDATE WHK004 SET WHK004_Status='C',WHK004_Last_Error='endpoint_disabled',WHK004_Data_Alteracao=UTC_TIMESTAMP() WHERE WHK004_WHK001_Id=? AND WHK004_C004_Id=? AND WHK004_Status IN ('P','R')");try{$company=$context->getCompanyId();$statement->bind_param('ii',$id,$company);$statement->execute();}finally{$statement->close();} return true;
    }
    /**
     * Creates a new outbox delivery from the immutable delivery snapshot. The source
     * lookup is intentionally scoped by company and the endpoint currently being
     * administered; callers must treat a missing source as a 404.
     */
    public function rescheduleDelivery(WebhookAdminContext $context, \mysqli $connection, int $endpointId, int $deliveryId, string $newDeliveryId): int
    {
        $sql='SELECT d.WHK004_C004_Id company_id,d.WHK004_WHK003_Id event_id,d.WHK004_WHK001_Id endpoint_id,d.WHK004_WHK002_Id subscription_id,d.WHK004_WHK007_Id secret_id,d.WHK004_Destination_URL destination_url FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id=d.WHK004_WHK003_Id AND e.WHK003_C004_Id=d.WHK004_C004_Id WHERE d.WHK004_Id=? AND d.WHK004_WHK001_Id=? AND d.WHK004_C004_Id=? FOR UPDATE';
        $statement=$this->prepare($connection,$sql);
        try {$company=$context->getCompanyId();$statement->bind_param('iii',$deliveryId,$endpointId,$company);$statement->execute();$source=$statement->get_result()->fetch_assoc();} finally {$statement->close();}
        if(!is_array($source))return 0;
        $statement=$this->prepare($connection,"INSERT INTO WHK004 (WHK004_Delivery_Id,WHK004_C004_Id,WHK004_WHK003_Id,WHK004_WHK001_Id,WHK004_WHK002_Id,WHK004_WHK007_Id,WHK004_Destination_URL,WHK004_Status,WHK004_Attempt_Count,WHK004_Next_Attempt_At) VALUES (?,?,?,?,?,?,?,'P',0,UTC_TIMESTAMP())");
        try {$companyId=(int)$source['company_id'];$eventId=(int)$source['event_id'];$sourceEndpointId=(int)$source['endpoint_id'];$subscriptionId=(int)$source['subscription_id'];$secretId=$source['secret_id']===null?null:(int)$source['secret_id'];$destinationUrl=(string)$source['destination_url'];$statement->bind_param('siiiiis',$newDeliveryId,$companyId,$eventId,$sourceEndpointId,$subscriptionId,$secretId,$destinationUrl);$statement->execute();} finally {$statement->close();}
        return (int)$connection->insert_id;
    }
    /** @param array<int,string> $events */
    private function syncSubscriptions(WebhookAdminContext $context, \mysqli $connection, int $id, array $events): void
    {
        $wanted=array_fill_keys($events,true); foreach($this->subscriptions($context,$connection,$id) as $subscription) { $event=$subscription['type']; $selected=isset($wanted[$event]); $filter=$selected ? null : '{"admin_selected":false}'; $status=$selected ? $subscription['status'] : 'I'; $statement=$this->prepare($connection,'UPDATE WHK002 SET WHK002_Status=?,WHK002_Filtro=?,WHK002_Data_Alteracao=UTC_TIMESTAMP() WHERE WHK002_Id=? AND WHK002_C004_Id=?');try{$company=$context->getCompanyId();$subId=$subscription['id'];$statement->bind_param('ssii',$status,$filter,$subId,$company);$statement->execute();}finally{$statement->close();} unset($wanted[$event]); }
        foreach(array_keys($wanted) as $event){$statement=$this->prepare($connection,"INSERT INTO WHK002 (WHK002_C004_Id,WHK002_WHK001_Id,WHK002_Event_Type,WHK002_Status,WHK002_Filtro) VALUES (?,?,?,'I',NULL) ON DUPLICATE KEY UPDATE WHK002_Status='I',WHK002_Filtro=NULL,WHK002_Data_Alteracao=UTC_TIMESTAMP()");try{$company=$context->getCompanyId();$statement->bind_param('iis',$company,$id,$event);$statement->execute();}finally{$statement->close();}}
    }
    /** @return array<int,array<string,mixed>> */
    private function subscriptions(WebhookAdminContext $context, \mysqli $connection, int $id): array { $statement=$this->prepare($connection,'SELECT WHK002_Id id,WHK002_Event_Type type,WHK002_Status status,WHK002_Filtro filter_value FROM WHK002 WHERE WHK002_WHK001_Id=? AND WHK002_C004_Id=? ORDER BY WHK002_Event_Type');try{$company=$context->getCompanyId();$statement->bind_param('ii',$id,$company);$statement->execute();$rows=$statement->get_result()->fetch_all(MYSQLI_ASSOC);foreach($rows as &$row){$row['id']=(int)$row['id'];$row['selected']=$row['filter_value']===null;unset($row['filter_value']);}return $rows;}finally{$statement->close();}}
    /** @return array<int,string> */ private function selectedEvents(WebhookAdminContext $context,\mysqli $connection,int $id):array{$result=array();foreach($this->subscriptions($context,$connection,$id) as $subscription)if($subscription['selected'])$result[]=$subscription['type'];return $result;}
    private function exists(WebhookAdminContext $context,\mysqli $connection,int $id,bool $lock):bool{$statement=$this->prepare($connection,'SELECT WHK001_Id FROM WHK001 WHERE WHK001_Id=? AND WHK001_C004_Id=?'.($lock?' FOR UPDATE':''));try{$company=$context->getCompanyId();$statement->bind_param('ii',$id,$company);$statement->execute();return $statement->get_result()->num_rows===1;}finally{$statement->close();}}
    private function setEndpointStatus(WebhookAdminContext $context,\mysqli $connection,int $id,string $status):void{$statement=$this->prepare($connection,'UPDATE WHK001 SET WHK001_Status=?,WHK001_Data_Alteracao=UTC_TIMESTAMP() WHERE WHK001_Id=? AND WHK001_C004_Id=?');try{$company=$context->getCompanyId();$statement->bind_param('sii',$status,$id,$company);$statement->execute();}finally{$statement->close();}}
    private function safeOperationalError($value):?string{if(!is_string($value))return null;return in_array($value,array('transport_error','destination_rejected','endpoint_disabled','secret_unavailable','destination_auth_unavailable'),true)?$value:null;}
    private function safeAttemptResult($value):string{return in_array($value,array('http_success','http_redirect','http_retryable','http_server_error','http_client_error','transport_error','security_error'),true)?$value:'transport_error';}
    private function bind(\mysqli_stmt $statement,string $types,array $values):void{$arguments=array($types);foreach($values as $index=>$value)$arguments[]=&$values[$index];call_user_func_array(array($statement,'bind_param'),$arguments);}
    /** @return array{type:string,configured:bool} */ private function destinationAuthProjection($type,$ciphertext):array{return array('type'=>$type==='B'?'bearer':'none','configured'=>$type==='B'&&is_string($ciphertext)&&$ciphertext!=='');}
    private function prepare(\mysqli $connection,string $sql):\mysqli_stmt{$statement=$connection->prepare($sql);if($statement===false)throw new \RuntimeException('Could not prepare webhook administrative query.');return $statement;}
}
