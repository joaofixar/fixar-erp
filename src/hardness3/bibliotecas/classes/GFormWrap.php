<?php
namespace hardness;

definirClasse('GFormWrap');
class GFormWrap_
{
    private $array;
    
    /**
     * __construct
     *
     * @param array $array
     */
    function __construct($array)
    {
        $this->array = $array;
    }
    
    /**
     * campoValorEnviado
     *
     * @param int $id
     */
    public function campoValorEnviado($id, $valorPadrao = '')
    {
        return isset($this->array[$id]) ? $this->array[$id] : $valorPadrao;
    }
}
