<?php
namespace hardness;
definirClasse('CAD044');
class CAD044_
{
	/**
     * getVar
     *
     * @param String $nome
     * @return String
     */ 
    public function getVar($nome)
    {
        $sql       = "SELECT D130_Conteudo FROM D130 WHERE D130_Nome = '{$nome}'";
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        if (empty($row)) {
            return "Nome não existe: {$nome}";
        }
        return '<p>' . $row['D130_Conteudo'] . '</p>';
    }
}
