<?php
declare(strict_types=1);
namespace Hardness\Observability\Security;
final class LoginRateLimiter
{
    private $file;
    public function __construct(){ $this->file=getenv('OBSERVABILITY_TEST_MODE')==='1'&&getenv('OBSERVABILITY_TEST_RATE_PATH')?getenv('OBSERVABILITY_TEST_RATE_PATH'):'/var/lib/hardness/observability/rate-limit.json'; }
    public function blocked(string $ip, string $login): bool { if(!$this->useApcu() && (!is_dir(dirname($this->file)) || !is_writable(dirname($this->file)))){return true;} $d=$this->read(); $k=$this->key($ip,$login); return isset($d[$k]) && ($d[$k]['until']??0)>time(); }
    public function failure(string $ip, string $login): void { $d=$this->read(); $k=$this->key($ip,$login); $v=$d[$k]??array('count'=>0,'first'=>time(),'until'=>0); if(time()-$v['first']>900){$v=array('count'=>0,'first'=>time(),'until'=>0);} $v['count']++; if($v['count']>=5){$v['until']=time()+900;} $d[$k]=$v; $this->write($d); }
    public function success(string $ip, string $login): void { $d=$this->read(); unset($d[$this->key($ip,$login)]); $this->write($d); }
    private function key(string $ip,string $login):string{return hash('sha256',$ip.'|'.$login);}
    private function read():array { if($this->useApcu()){$v=apcu_fetch('hardness_observability_rate',$ok);return $ok&&is_array($v)?$v:array();} if(!is_file($this->file)){return array();}$v=json_decode((string)@file_get_contents($this->file),true);return is_array($v)?$v:array(); }
    private function write(array $data):void { if($this->useApcu()){apcu_store('hardness_observability_rate',$data,1800);return;} $dir=dirname($this->file);if(!is_dir($dir)){return;} @file_put_contents($this->file,json_encode($data),LOCK_EX); }
    private function useApcu():bool{return function_exists('apcu_enabled')&&apcu_enabled();}
}
