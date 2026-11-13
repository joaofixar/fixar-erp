<?php
declare(strict_types=1);
/* Limpa somente o ciclo guiado da empresa e guia informadas; não toca XML,
   tentativas, fila nem qualquer serviço do Portal. */
require_once __DIR__.'/../bootstrap/app.php';
$context=gnre_require_access();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
gnre_require_csrf();
$guideId=(int)($_POST['guide_id']??0);
if($guideId<1){http_response_code(422);exit;}
unset($_SESSION[GNRE_SESSION]['temporary']['gnre34_cycle'][(string)$context['company_id']][$guideId]);
http_response_code(204);
