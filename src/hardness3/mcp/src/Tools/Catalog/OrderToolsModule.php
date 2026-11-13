<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Catalog;
use PDO;use HardnessMcp\Tools\ToolInterface;use HardnessMcp\Tools\Order\Domain\OrderBacklogRepositoryInterface;use HardnessMcp\Tools\Order\Infrastructure\PdoOrderBacklogRepository;use HardnessMcp\Tools\Order\OrderBacklogHandler;use HardnessMcp\Tools\Order\OrderBacklogTool;
final class OrderToolsModule implements ToolModuleInterface {private OrderBacklogRepositoryInterface $r;public function __construct(OrderBacklogRepositoryInterface $r){$this->r=$r;}public static function fromPdo(PDO $p,bool $proposalCustomerAvailable=false):self{return new self(new PdoOrderBacklogRepository($p,$proposalCustomerAvailable));}public function tools():array{return [new OrderBacklogTool(new OrderBacklogHandler($this->r))];}}
