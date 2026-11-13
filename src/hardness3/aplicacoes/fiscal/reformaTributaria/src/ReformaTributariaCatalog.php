<?php
declare(strict_types=1);

namespace hardness;

/* A consulta própria não inicializa o carregador legado de classes. Este
 * adaptador permite reutilizar somente o catálogo fiscal canônico, sem
 * carregar contexto, empresa atual ou permissões do framework. */
if (!function_exists(__NAMESPACE__ . '\\definirClasse')) {
    function definirClasse(string $class): bool
    {
        $base = __NAMESPACE__ . '\\' . $class . '_';
        $target = __NAMESPACE__ . '\\' . $class;
        if (!class_exists($target, false) && class_exists($base, false)) {
            eval('namespace hardness; class ' . $class . ' extends ' . $class . '_ {}');
        }
        return false;
    }
}
