<?php
declare(strict_types=1);
namespace Hardness\Observability\Security;
final class SecurityLogger
{
    public function event(string $event, array $fields = array()): void
    {
        $allowed=array('request_id','server_id','ip','login','c007_id','role','code'); $entry=array('timestamp'=>gmdate('c'),'event'=>$event);
        foreach ($allowed as $key) { if (isset($fields[$key])) { $entry[$key]=is_scalar($fields[$key])?(string)$fields[$key]:'invalid'; } }
        $dir='/var/log/hardness/observability'; if (!is_dir($dir)) { return; }
        @file_put_contents($dir.'/security.log', json_encode($entry, JSON_UNESCAPED_SLASHES)."\n", FILE_APPEND|LOCK_EX);
    }
}
