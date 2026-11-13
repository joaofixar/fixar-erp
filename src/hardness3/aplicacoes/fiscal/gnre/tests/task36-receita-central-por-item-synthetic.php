<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GuideCenter.php';

function t36(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }
function i36(int $id, string $revenue): array { return ['GNRE003_Id'=>$id, 'GNRE003_Codigo_Receita'=>$revenue]; }

t36(GnreGuideCenter::projectedRevenue(['GNRE001_Codigo_Receita'=>'100102'], [i36(1, '200200')]) === '100102', 'receita do cabeçalho prevalece na projeção.');
t36(GnreGuideCenter::projectedRevenue(['GNRE001_Codigo_Receita'=>''], [i36(2, '100102')]) === '100102', 'receita única do item preenche somente a projeção.');
t36(GnreGuideCenter::projectedRevenue(['GNRE001_Codigo_Receita'=>''], [i36(9, '300300'), i36(3, '100102'), i36(7, '200200')]) === '100102 · +2 receitas', 'primeira receita persistida e receitas distintas adicionais são determinísticas.');
t36(GnreGuideCenter::projectedRevenue(['GNRE001_Codigo_Receita'=>''], [i36(1, '100102'), i36(2, '100102'), i36(3, '200200')]) === '100102 · +1 receitas', 'itens repetidos não inflam a contagem de receitas adicionais.');

$source = (string)file_get_contents(__DIR__.'/../src/GuideCenter.php');
t36(strpos($source, 'g.GNRE001_C004_Id=?') !== false, 'consulta continua limitada à empresa GNRE ativa.');
t36(strpos($source, 'EXISTS (SELECT 1 FROM GNRE003 ri WHERE ri.GNRE003_GNRE001_Id=g.GNRE001_Id AND ri.GNRE003_Codigo_Receita=?)') !== false, 'filtro encontra receita em qualquer item sem JOIN duplicador.');
t36(strpos($source, 'EXISTS (SELECT 1 FROM GNRE003 di WHERE di.GNRE003_GNRE001_Id=g.GNRE001_Id AND ') !== false, 'filtro de vencimento encontra a faixa em qualquer item sem JOIN duplicador.');
t36(strpos($source, "g.GNRE001_Data_Vencimento<>'0000-00-00'") !== false && strpos($source, "di.GNRE003_Data_Vencimento<>'0000-00-00'") !== false, 'filtro de vencimento ignora datas zeradas no cabeçalho e nos itens.');
t36(strpos($source, 'ORDER BY GNRE003_GNRE001_Id ASC,GNRE003_Id ASC') !== false, 'itens são carregados na ordem persistida para a projeção.');
t36(strpos($source, "['revenue']") !== false && strpos($source, "'ss'; \$values[]=\$filters['revenue']; \$values[]=\$filters['revenue'];") !== false, 'filtro combina cabeçalho e itens com os dois parâmetros do código.');
foreach ([['due_from'=>'2026-11-01','due_to'=>'2026-11-30'],['due_from'=>'2026-11-01','due_to'=>''],['due_from'=>'','due_to'=>'2026-11-30']] as $range) t36(GnreGuideCenter::filters($range) === array_merge(['status'=>'','uf'=>'','revenue'=>''],$range,['guide'=>0,'receipt'=>'','environment'=>'']), 'faixa de vencimento válida continua normalizada.');
foreach (['INSERT ', 'UPDATE ', 'DELETE ', 'ALTER ', 'CREATE TABLE', 'DROP TABLE'] as $forbidden) t36(stripos($source, $forbidden) === false, 'read model não contém persistência ou migration: '.$forbidden);

echo "Task 36 receita central por item synthetic: OK\n";
