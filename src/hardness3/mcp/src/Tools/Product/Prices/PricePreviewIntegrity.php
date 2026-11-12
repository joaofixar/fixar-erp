<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

final class PricePreviewIntegrity
{
    /** Mutable execution status is excluded; approved targets and values are immutable. */
    public static function hash(array $rows): string
    {
        $values=[];
        foreach ($rows as $row) {
            $value=[(int)$row['MCP021_Linha'], $row['MCP021_Codigo_Produto'],
                $row['MCP021_D009_Id']===null ? null : (int)$row['MCP021_D009_Id']];
            for($n=1;$n<=5;$n++) $value[]=[(int)$row['MCP021_Selecionado_'.$n],$row['MCP021_Preco_'.$n.'_Antes'],$row['MCP021_Preco_'.$n.'_Depois']];
            $values[]=$value;
        }
        return hash('sha256', json_encode($values,JSON_THROW_ON_ERROR),true);
    }
}
