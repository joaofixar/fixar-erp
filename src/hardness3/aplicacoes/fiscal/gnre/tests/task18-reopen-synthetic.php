<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GuideItemEditPolicy.php';
function t18r(bool $ok,string $m):void{if(!$ok)throw new RuntimeException($m);}
t18r(GnreGuideItemEditPolicy::mode('0',0,false,false)==='new','tipo 0 vazio deve criar');
t18r(GnreGuideItemEditPolicy::mode('0',1,false,false)==='summary','tipo 0 com item deve abrir o resumo');
t18r(GnreGuideItemEditPolicy::mode('0',1,true,false)==='edit','tipo 0 só deve editar após ação explícita');
t18r(GnreGuideItemEditPolicy::mode('0',2,false,false)==='invalid','tipo 0 não pode oferecer segundo item');
foreach(['1','2'] as $type){t18r(GnreGuideItemEditPolicy::mode($type,2,false,false)==='list','tipo '.$type.' deve listar');t18r(GnreGuideItemEditPolicy::mode($type,2,false,true)==='new','tipo '.$type.' deve adicionar');t18r(GnreGuideItemEditPolicy::mode($type,2,true,false)==='edit','tipo '.$type.' deve editar escolhido');}
echo "guide-draft reopen synthetic: OK\n";
