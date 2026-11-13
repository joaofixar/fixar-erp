<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Presentation;

use HardnessMcp\Tools\ToolExecutionResult;

/**
 * Canonical MCP result boundary: text is a bounded rendering of the same
 * allow-listed structured payload returned by each Tool.
 */
final class ToolResultPresenter
{
    public const DEFAULT_TEXT_ITEM_LIMIT = 10;
    public const DEFAULT_CONTENT_MAX_CHARS = 4000;

    private int $itemLimit;
    private int $contentMaxChars;

    public function __construct(int $itemLimit = self::DEFAULT_TEXT_ITEM_LIMIT, int $contentMaxChars = self::DEFAULT_CONTENT_MAX_CHARS)
    {
        $this->itemLimit = max(1, $itemLimit);
        $this->contentMaxChars = max(256, $contentMaxChars);
    }

    /** @param array<string,mixed> $structured @param array<int,array<string,mixed>> $fields */
    public function record(array $structured, string $title, array $fields, string $auditEvent = 'tools_call_success'): ToolExecutionResult
    {
        return $this->success($this->recordText($structured, $title, $fields), $structured, $auditEvent);
    }

    /** @param array<string,mixed> $structured @param array<int,array<string,mixed>> $itemFields */
    public function collection(array $structured, string $itemsKey, string $singular, string $plural, array $itemFields, string $auditEvent = 'tools_call_success'): ToolExecutionResult
    {
        $items = isset($structured[$itemsKey]) && is_array($structured[$itemsKey]) ? $structured[$itemsKey] : [];
        $count = count($items);
        if ($count === 0) return $this->success('Nenhum ' . $singular . ' encontrado.', $structured, $auditEvent);
        $text = $count . ' ' . ($count === 1 ? $singular . ' encontrado.' : $plural . ' encontrados.');
        $rendered = [];
        foreach (array_slice($items, 0, $this->itemLimit) as $item) { if (is_array($item)) $rendered[] = $this->fieldsText($item, $itemFields); }
        if ($rendered !== []) $text .= ' ' . implode(' | ', $rendered) . '.';
        if ($count > $this->itemLimit) $text .= ' Mais ' . ($count - $this->itemLimit) . ' item(ns) disponível(is) em structuredContent.';
        return $this->success($text, $structured, $auditEvent);
    }

    /** @param array<string,mixed> $structured @param array<int,array<string,mixed>> $summaryFields @param array<int,array<string,mixed>> $rowFields */
    public function report(array $structured, string $title, array $summaryFields, string $rowsKey, array $rowFields, string $auditEvent = 'tools_call_success'): ToolExecutionResult
    {
        $text = $this->recordText($structured, $title, $summaryFields);
        $rows = isset($structured[$rowsKey]) && is_array($structured[$rowsKey]) ? $structured[$rowsKey] : [];
        if ($rows === []) { return $this->success($text . ' Nenhuma linha encontrada.', $structured, $auditEvent); }

        $rendered = [];
        foreach (array_slice($rows, 0, $this->itemLimit) as $row) { if (is_array($row)) $rendered[] = $this->fieldsText($row, $rowFields); }
        if ($rendered !== []) { $text .= ' Linhas (' . count($rows) . '): ' . implode(' | ', $rendered) . '.'; }
        if (count($rows) > $this->itemLimit) { $text .= ' Mais ' . (count($rows) - $this->itemLimit) . ' linha(s) disponível(is) em structuredContent.'; }
        if (($structured['has_more'] ?? false) === true) { $text .= ' Há mais linhas disponíveis, limitadas conforme solicitado.'; if (is_string($structured['next_cursor'] ?? null) && $structured['next_cursor'] !== '') $text .= ' Para a próxima página, mantenha os mesmos filtros e use o cursor: ' . $structured['next_cursor'] . '.'; }
        return $this->success($text, $structured, $auditEvent);
    }

    /** @param array<string,mixed> $structured @param array<int,array<string,mixed>> $headerFields @param array<int,array<string,mixed>> $itemFields */
    public function detailWithItems(array $structured, string $title, array $headerFields, string $itemsKey, array $itemFields, string $auditEvent = 'tools_call_success'): ToolExecutionResult
    {
        $text = $this->recordText($structured, $title, $headerFields);
        $items = isset($structured[$itemsKey]) && is_array($structured[$itemsKey]) ? $structured[$itemsKey] : [];
        if ($items === []) return $this->success($text . ' Nenhum item encontrado.', $structured, $auditEvent);
        $rendered=[]; foreach(array_slice($items,0,$this->itemLimit) as $item){if(is_array($item))$rendered[]=$this->fieldsText($item,$itemFields);}
        $text .= ' Itens (' . count($items) . '): ' . implode(' | ', $rendered) . '.';
        if(count($items)>$this->itemLimit)$text.=' Mais '.(count($items)-$this->itemLimit).' item(ns) disponível(is) em structuredContent.';
        return $this->success($text, $structured, $auditEvent);
    }

    /** @param array<string,mixed> $extra */
    public function functionalError(string $code, string $message, string $auditEvent, array $extra = [], ?string $auditSafeCode = null): ToolExecutionResult
    {
        return new ToolExecutionResult(['content'=>[['type'=>'text','text'=>$this->limit($message)]],'structuredContent'=>['error'=>array_merge(['code'=>$code,'message'=>$message],$extra)],'isError'=>true],$auditEvent,$auditSafeCode);
    }

    /** @param array<string,mixed> $structured */
    private function success(string $text, array $structured, string $auditEvent): ToolExecutionResult
    {
        return new ToolExecutionResult(['content'=>[['type'=>'text','text'=>$this->limit($text)]],'structuredContent'=>$structured],$auditEvent);
    }

    /** @param array<string,mixed> $structured @param array<int,array<string,mixed>> $fields */
    private function recordText(array $structured, string $title, array $fields): string
    {
        $text=$this->fieldsText($structured,$fields); return $text===''?$title.'.':$title.': '.$text.'.';
    }

    /** @param array<string,mixed> $source @param array<int,array<string,mixed>> $fields */
    private function fieldsText(array $source, array $fields): string
    {
        $parts=[]; foreach($fields as $field){$value=$this->path($source,(string)($field['path']??''));if($value===null||$value==='')continue;$formatted=$this->format($value,(string)($field['format']??'text'),(array)($field['map']??[]));if($formatted==='')continue;$label=(string)($field['label']??'');$parts[]=$label===''?$formatted:$label.': '.$formatted;} return implode(', ',$parts);
    }

    /** @param array<string,mixed> $source @return mixed */
    private function path(array $source,string $path)
    {
        $value=$source; foreach(explode('.',$path) as $part){if(!is_array($value)||!array_key_exists($part,$value))return null;$value=$value[$part];} return $value;
    }

    /** @param mixed $value @param array<string,string> $map */
    private function format($value,string $format,array $map): string
    {
        if($format==='status'){ $key=(string)$value; return $map[$key]??$key; }
        if($format==='money' && is_numeric((string)$value)) return number_format((float)$value,2,',','.');
        if($format==='quantity' && is_numeric((string)$value)){ $number=rtrim(rtrim(number_format((float)$value,5,'.',''),'0'),'.'); return $number===''?'0':$number; }
        if($format==='date'){ $value=(string)$value; return preg_match('/\A\d{4}-\d{2}-\d{2}\z/',$value)===1?$value:$value; }
        return is_scalar($value)?trim((string)$value):'';
    }

    private function limit(string $text): string
    {
        $length=function_exists('mb_strlen')?(int)mb_strlen($text,'UTF-8'):strlen($text); if($length<=$this->contentMaxChars)return $text;
        $limit=$this->contentMaxChars-1; return (function_exists('mb_substr')?mb_substr($text,0,$limit,'UTF-8'):substr($text,0,$limit)).'…';
    }
}
