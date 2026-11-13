<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Quote\Item;
use HardnessMcp\Context\ExecutionContext; use HardnessMcp\Legacy\LegacyExecutionContextAdapter; use HardnessMcp\Legacy\McpFrameworkBridge; use HardnessMcp\Tenant\ResolvedTenantContext;
final class LegacyQuoteItemGateway implements QuoteItemGatewayInterface {
 private LegacyExecutionContextAdapter $adapter; private McpFrameworkBridge $bridge; private ResolvedTenantContext $tenant;
 public function __construct(LegacyExecutionContextAdapter $adapter,McpFrameworkBridge $bridge,ResolvedTenantContext $tenant){$this->adapter=$adapter;$this->bridge=$bridge;$this->tenant=$tenant;}
 public function add(array $arguments,ExecutionContext $context):array{$legacy=$this->adapter->adapt($context,$this->tenant,['remote_ip'=>$context->getTrustedRemoteIp()]);return $this->bridge->executeMcp001QuoteItemAdd($legacy,$arguments);}
 public function update(array $arguments,ExecutionContext $context):array{$legacy=$this->adapter->adapt($context,$this->tenant,['remote_ip'=>$context->getTrustedRemoteIp()]);return $this->bridge->executeMcp001QuoteItemUpdate($legacy,$arguments);}
 public function delete(int $id,ExecutionContext $context):array{$legacy=$this->adapter->adapt($context,$this->tenant,['remote_ip'=>$context->getTrustedRemoteIp()]);return $this->bridge->executeMcp001QuoteItemDelete($legacy,$id);}
}
