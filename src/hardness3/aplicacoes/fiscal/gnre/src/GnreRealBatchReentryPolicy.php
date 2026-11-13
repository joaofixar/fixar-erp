<?php
declare(strict_types=1);
/** Pure, conservative admission rule for an immutable retry through a physical batch. */
final class GnreRealBatchReentryPolicy
{
    public static function allowed(array $evidence): bool
    {
        if(!empty($evidence['authorized'])||!empty($evidence['receipt'])||!empty($evidence['active'])||!empty($evidence['pending'])||!empty($evidence['technical'])||!empty($evidence['ambiguous']))return false;
        if(in_array((string)($evidence['situation']??''),['0','4'],true))return false;
        return in_array((string)($evidence['situation']??''),['1','2'],true)||!empty($evidence['reception_rejected'])||!empty($evidence['batch_rejected']);
    }
}
