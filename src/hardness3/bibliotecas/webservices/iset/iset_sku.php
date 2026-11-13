<?php
namespace hardness;

/** Mantém a representação de SKU específica de cada lado da integração. */
class iset_sku_
{
    public function paraIset($sku)
    {
        $sku = trim((string) $sku);
        if (!preg_match('/^[0-9]{3}\.[0-9]{4}$/', $sku)) return $sku;
        return substr($sku, 0, 3) . '-' . substr($sku, 4);
    }

    public function paraHardness($sku)
    {
        $sku = trim((string) $sku);
        if (!preg_match('/^[0-9]{3}-[0-9]{4}$/', $sku)) return $sku;
        return substr($sku, 0, 3) . '.' . substr($sku, 4);
    }

    public function iguais($skuHardness, $skuIset)
    {
        return trim((string) $skuHardness) === $this->paraHardness($skuIset);
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) definirClasse('ISET_SKU');
if (!class_exists(__NAMESPACE__ . '\\iset_sku', false)) class_alias(__NAMESPACE__ . '\\iset_sku_', __NAMESPACE__ . '\\iset_sku');
