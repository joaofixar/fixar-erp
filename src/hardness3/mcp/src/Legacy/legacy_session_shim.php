<?php

declare(strict_types=1);

/*
 * The legacy helpers live in the hardness namespace and call session_start()
 * unqualified.  Providing that namespaced no-op prevents PHP from resolving
 * the call to the native session extension.  The Bridge supplies only the
 * one in-memory value required by INDEX and restores it afterwards.
 */
namespace hardness;

if (!function_exists(__NAMESPACE__ . '\\session_start')) {
    function session_start(array $options = []): bool
    {
        return true;
    }
}
