<?php
declare(strict_types=1);

/** Local, side-effect-free comparison for persisted individual GNRE XML. */
final class GnreXmlSemanticComparator
{
    public static function firstDifference(string $left, string $right): ?array
    {
        $a=self::load($left);$b=self::load($right);
        if(!$a||!$b)return ['path'=>'/','authorized'=>$a?'valid':'invalid','rejected'=>$b?'valid':'invalid'];
        $aa=self::node($a->documentElement);$bb=self::node($b->documentElement);
        return self::diff($aa,$bb,'/'.($a->documentElement->localName?:$a->documentElement->nodeName));
    }
    private static function load(string $xml): ?DOMDocument
    {
        if(!class_exists('DOMDocument'))return null;
        $dom=new DOMDocument();return @$dom->loadXML($xml,LIBXML_NONET)?$dom:null;
    }
    private static function node(DOMElement $element): array
    {
        $attributes=[];foreach($element->attributes as $attribute)$attributes[$attribute->localName]=$attribute->value;ksort($attributes);
        $children=[];foreach($element->childNodes as $child)if($child instanceof DOMElement)$children[$child->localName][]=self::node($child);
        ksort($children);
        return ['attributes'=>$attributes,'value'=>$children?null:trim($element->textContent),'children'=>$children];
    }
    private static function diff(array $a,array $b,string $path): ?array
    {
        foreach(['attributes','value'] as $key)if($a[$key]!==$b[$key])return ['path'=>$path.($key==='attributes'?'/@':'/text()'),'authorized'=>$a[$key],'rejected'=>$b[$key]];
        $keys=array_values(array_unique(array_merge(array_keys($a['children']),array_keys($b['children']))));sort($keys);
        foreach($keys as $key){$x=$a['children'][$key]??[];$y=$b['children'][$key]??[];if(count($x)!==count($y))return ['path'=>$path.'/'.$key,'authorized'=>'count='.count($x),'rejected'=>'count='.count($y)];foreach($x as $i=>$child)if($difference=self::diff($child,$y[$i],$path.'/'.$key.'['.($i+1).']'))return $difference;}
        return null;
    }
}
