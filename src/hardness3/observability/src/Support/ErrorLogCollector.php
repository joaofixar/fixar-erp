<?php
declare(strict_types=1);

namespace Hardness\Observability\Support;


/** Leitura limitada de arquivos explicitamente autorizados pela configuração local. */
final class ErrorLogCollector
{
    private const MAX_BYTES = 65536;
    private const MAX_LINES = 200;

    public function collect(): array
    {
        $sources = array();
        foreach ($this->configuredSources() as $source) {
            $path = $source['path']; $stat = @lstat($path);
            if ($stat === false || is_link($path) || !is_file($path) || !is_readable($path)) { $sources[] = array('label'=>$source['label'],'path'=>$path,'available'=>false,'lines'=>array()); continue; }
            $sources[] = array('label'=>$source['label'],'path'=>$path,'available'=>true,'lines'=>$this->tail($path));
        }
        return array('sources'=>$sources, 'configured'=>count($sources)>0);
    }

    private function configuredSources(): array
    {
        $path = getenv('OBSERVABILITY_TEST_ERROR_LOGS_PATH') ?: HARDNESS_OBSERVABILITY_ROOT . '/config/error-logs.json';
        $json = @file_get_contents($path); $data = is_string($json) ? json_decode($json, true) : null;
        if (!is_array($data) || !isset($data['sources']) || !is_array($data['sources'])) { return array(); }
        $sources=array(); foreach ($data['sources'] as $source) { if (!is_array($source) || empty($source['label']) || empty($source['path']) || !is_string($source['path']) || $source['path'][0] !== '/') { continue; } $sources[]=array('label'=>(string)$source['label'],'path'=>$source['path']); }
        return $sources;
    }
    private function tail(string $path): array
    {
        $handle=@fopen($path,'rb'); if(!$handle){return array();} $size=(int)@filesize($path); if($size>self::MAX_BYTES){@fseek($handle,-self::MAX_BYTES,SEEK_END); @fgets($handle);}
        $data=''; while(!feof($handle)){$data.=fread($handle,8192);} fclose($handle); $lines=preg_split('/\r?\n/',trim($data)); $lines=array_slice(array_values(array_filter($lines,function($line){return $line!=='';})),-self::MAX_LINES); return array_reverse($lines);
    }
}
