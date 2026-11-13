<?php
declare(strict_types=1);
final class GnreGuideItemEditPolicy {
    public static function mode(string $type, int $itemCount, bool $chosenItem, bool $newItem): string {
        if ($type==='0') {
            if ($itemCount===0) return 'new';
            if ($itemCount!==1) return 'invalid';
            return $chosenItem ? 'edit' : 'summary';
        }
        return $chosenItem?'edit':($newItem?'new':'list');
    }
}
