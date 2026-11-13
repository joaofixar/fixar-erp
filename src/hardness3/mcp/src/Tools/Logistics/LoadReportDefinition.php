<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Logistics;

use HardnessMcp\Tools\ToolDefinition;

final class LoadReportDefinition
{
    public static function summary(): ToolDefinition
    {
        return new ToolDefinition('hardness.logistics.load.summary', 'Returns a read-only summary of loads registered in the partner logistics module. It groups loads by status, creation day, or scheduled day. The report only includes the company bound to this MCP connection.', self::periodSchema(['group_by' => ['type' => 'string', 'enum' => ['status', 'created_day', 'scheduled_day']]], ['date_from', 'date_to', 'group_by']), ['logistics.load:read']);
    }

    public static function listing(): ToolDefinition
    {
        return new ToolDefinition('hardness.logistics.load.list', 'Lists loads from the partner logistics module, read-only. When supported by the installation, each load includes compact customer, destination, outgoing-NF, driver, carrier and freight information; use get_by_id for full orders and invoice details. The list only includes the company bound to this MCP connection.', self::periodSchema(['status' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 30], 'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50]], ['date_from', 'date_to']), ['logistics.load:read']);
    }

    public static function detail(): ToolDefinition
    {
        return new ToolDefinition('hardness.logistics.load.get_by_id', 'Returns a read-only load header and its linked orders. When supported by the installation it includes driver, carrier, vehicle, capacity, photos, freight indicators and, per order, the delivery destination resolved as redelivery address, delivery address or customer address, region, freight, status, payment terms and outgoing invoices. Use after the load list to answer operational logistics questions. It never changes logistics data and only returns a load owned by the company bound to this MCP connection.', ['type' => 'object', 'properties' => ['load_id' => ['type' => 'integer', 'minimum' => 1]], 'required' => ['load_id'], 'additionalProperties' => false], ['logistics.load:read']);
    }

    /** @param array<string,mixed> $extra @param string[] $required @return array<string,mixed> */
    private static function periodSchema(array $extra, array $required): array
    {
        return ['type' => 'object', 'properties' => array_merge(['date_from' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'date_to' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 10], 'date_basis' => ['type' => 'string', 'enum' => ['created_at', 'scheduled_at']]], $extra), 'required' => $required, 'additionalProperties' => false];
    }
}
