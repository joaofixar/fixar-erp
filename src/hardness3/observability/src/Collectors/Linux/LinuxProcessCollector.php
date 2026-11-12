<?php
declare(strict_types=1);
namespace Hardness\Observability\Collectors\Linux;

/** Leitura limitada de /proc; não executa comandos nem abre terminal. */
final class LinuxProcessCollector
{
    public function collect(): array
    {
        $first=$this->sample(); usleep(150000); $second=$this->sample();
        $total=max(1,$second['total']-$first['total']); $rows=array(); $counts=array('running'=>0,'sleeping'=>0,'zombie'=>0,'other'=>0);
        foreach($second['processes'] as $pid=>$row){$state=$row['state'];$group=$state==='R'?'running':(($state==='Z')?'zombie':(in_array($state,array('S','D','I'),true)?'sleeping':'other'));$counts[$group]++;$before=$first['processes'][$pid]??null;$cpu=$before?max(0,100*(($row['ticks']-$before['ticks'])/$total)):0;$parent=$second['processes'][$row['parent_pid']]??null;$rows[]=array('pid'=>$pid,'parent_pid'=>$row['parent_pid'],'parent_command'=>$parent?$parent['command']:'Indisponível','name'=>$row['name'],'command'=>$row['command'],'executable'=>$row['executable'],'working_directory'=>$row['working_directory'],'uid'=>$row['uid'],'threads'=>$row['threads'],'open_files'=>$row['open_files'],'type'=>$this->type($row['name']),'cpu'=>round($cpu,1),'memory'=>round($row['rss_kb']/1024,1),'state'=>$state,'category'=>$group,'runtime'=>$this->duration($row['runtime']),'severity'=>$cpu>=90||$row['rss_kb']>1048576?'critical':($cpu>=60||$row['rss_kb']>524288?'attention':'normal'));
        }
        usort($rows,function($a,$b){return $b['cpu']==$a['cpu']?$b['memory']<=>$a['memory']:$b['cpu']<=>$a['cpu'];});
        return array('rows'=>array_slice($rows,0,20),'counts'=>$counts,'io'=>$this->io(), 'pressure'=>$this->pressure());
    }
    private function sample():array{$raw=@file('/proc/stat',FILE_IGNORE_NEW_LINES);$parts=isset($raw[0])?preg_split('/\s+/',trim(substr($raw[0],3))):array();$total=array_sum(array_map('intval',$parts));$out=array();foreach(glob('/proc/[0-9]*',GLOB_NOSORT) as $dir){$pid=(int)basename($dir);$stat=@file_get_contents($dir.'/stat');if(!is_string($stat)||!preg_match('/^\d+ \((.*)\) ([A-Z]) (.+)$/',$stat,$m))continue;$v=preg_split('/\s+/', $m[3]);$status=(string)@file_get_contents($dir.'/status');preg_match('/^VmRSS:\s+(\d+)/m',$status,$rss);preg_match('/^Uid:\s+(\d+)/m',$status,$uid);preg_match('/^Threads:\s+(\d+)/m',$status,$threads);$ticks=(int)($v[10]??0)+(int)($v[11]??0);$start=(int)($v[18]??0);$uptime=(float)explode(' ',trim((string)@file_get_contents('/proc/uptime')))[0];$command=str_replace("\0",' ',(string)@file_get_contents($dir.'/cmdline'));$files=@glob($dir.'/fd/*',GLOB_NOSORT);$out[$pid]=array('name'=>$m[1],'command'=>$this->sanitizeCommand($command,$m[1]),'parent_pid'=>(int)($v[0]??0),'executable'=>$this->safePath(@readlink($dir.'/exe')),'working_directory'=>$this->safePath(@readlink($dir.'/cwd')),'uid'=>$uid[1]??'Indisponível','threads'=>(int)($threads[1]??0),'open_files'=>is_array($files)?count($files):null,'state'=>$m[2],'ticks'=>$ticks,'rss_kb'=>(int)($rss[1]??0),'runtime'=>max(0,(int)$uptime-(int)($start/100))); }return array('total'=>$total,'processes'=>$out);}
    private function sanitizeCommand(string $command,string $fallback):string{$command=trim(preg_replace('/\s+/', ' ', $command));if($command===''){$command=$fallback;}$command=preg_replace('/((?:pass(?:word)?|token|secret|key)=)[^\s]+/i','$1[redigido]',$command);$command=preg_replace('/((?:--?)(?:pass(?:word)?|token|secret|key))(?:=|\s+)[^\s]+/i','$1=[redigido]',$command);return mb_strlen($command)>240?mb_substr($command,0,237).'…':$command;}
    private function safePath($path):string{$path=is_string($path)?$path:'';return $path===''?'Indisponível':(mb_strlen($path)>240?mb_substr($path,0,237).'…':$path);}
    private function type(string $name):string{$n=strtolower($name);if(strpos($n,'php')!==false)return 'PHP';if(strpos($n,'apache')!==false||strpos($n,'httpd')!==false)return 'Apache';if(strpos($n,'mysql')!==false||strpos($n,'mariadb')!==false)return 'MySQL';if(strpos($n,'cron')!==false)return 'Cron';return 'Sistema';}
    private function io():array{$lines=@file('/proc/diskstats',FILE_IGNORE_NEW_LINES);$read=0;$write=0;if(is_array($lines))foreach($lines as $line){$p=preg_split('/\s+/',trim($line));if(count($p)>=10&&preg_match('/^(sd|vd|xvd|nvme)/',$p[2])){$read+=(int)$p[5]*512;$write+=(int)$p[9]*512;}}return array('read_bytes'=>$read,'write_bytes'=>$write,'available'=>is_array($lines));}
    private function pressure():array{$raw=@file_get_contents('/proc/pressure/memory');if(!is_string($raw)||!preg_match('/some avg10=([0-9.]+)/',$raw,$m))return array('available'=>false);return array('available'=>true,'memory_avg10'=>(float)$m[1]);}
    private function duration(int $s):string{return $s>=3600?intdiv($s,3600).' h '.intdiv($s%3600,60).' min':intdiv($s,60).' min';}
}
