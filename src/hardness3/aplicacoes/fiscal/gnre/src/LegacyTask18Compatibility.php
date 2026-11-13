<?php
declare(strict_types=1);

/**
 * Temporary POST/session adapter for browser pages cached before Task 31.
 * The active guide-draft flow emits only guide_draft_* contracts. Remove after
 * the cache/shortcut compatibility window has elapsed and no callers remain.
 */
function gnre_legacy_task18_adapt_request(): bool {
    $action=(string)($_POST['action']??'');
    if(strpos($action,'task18_')!==0)return false;
    $_POST['action']='guide_draft_'.substr($action,7);
    $temporary=&$_SESSION[GNRE_SESSION]['temporary'];
    foreach(['task18'=>'guide_draft','task18_form'=>'guide_draft_form'] as $legacy=>$canonical) {
        if(isset($temporary[$legacy])&&!isset($temporary[$canonical]))$temporary[$canonical]=$temporary[$legacy];
        unset($temporary[$legacy]);
    }
    if(isset($_SESSION[GNRE_SESSION]['task18_flash'])&&!isset($_SESSION[GNRE_SESSION]['guide_draft_flash']))$_SESSION[GNRE_SESSION]['guide_draft_flash']=$_SESSION[GNRE_SESSION]['task18_flash'];
    unset($_SESSION[GNRE_SESSION]['task18_flash']);
    return true;
}
