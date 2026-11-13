<?php
declare(strict_types=1);
$source=(string)file_get_contents(__DIR__.'/../public/task34-modal-state.php');
if(strpos($source,"elseif(\$guide['GNRE001_Status']!=='R'&&\$receptionOutcome['state']!=='')")===false)throw new RuntimeException('Rascunho reaberto não ignora rejeição histórica de recepção.');
if(strpos($source,"\$guide['GNRE001_Status']!=='R'&&\$row&&trim")===false)throw new RuntimeException('Rascunho reaberto não ignora erro técnico histórico.');
if(strpos($source,"\$guide['GNRE001_Status']==='F')\$terminal='final_not_authorized'")===false)throw new RuntimeException('Estado final continua sem proteção.');
echo "Reopened draft ignores historical reception terminal check passed\n";
