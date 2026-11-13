<?php

declare(strict_types=1);

$ven001Source = file_get_contents(dirname(__DIR__, 3) . '/bibliotecas/classes/VEN001.php');
mcpAssert($ven001Source !== false, 'VEN001 quote-header source must be available.');
mcpAssert(
    strpos((string) $ven001Source, 'D024_C007_Vendedor_Interno') !== false
    && strpos((string) $ven001Source, 'D024_C007_Vendedor_Externo') !== false
    && strpos((string) $ven001Source, 'D024_D021_Id') !== false,
    'quote header must load the commercial-seller and representative defaults from the customer.'
);
mcpAssert(
    strpos((string) $ven001Source, 'ven001VendedorInternoClienteOuAtor') !== false
    && strpos((string) $ven001Source, "C007_Flag_Ausente'] ?? '') === 'S'") !== false
    && strpos((string) $ven001Source, 'return $usuarioId;') !== false,
    'quote header must fall back to its authenticated ERP actor when the customer internal seller is missing or absent.'
);
mcpAssert(
    strpos((string) $ven001Source, "'T003_C007_Id_Vendedor_Interno', 'T003_C007_Id', 'T003_Flag_Lista_Preco'") !== false
    && strpos((string) $ven001Source, "\$colunas[] = 'T003_C007_Id_Vendedor_Externo';") !== false
    && strpos((string) $ven001Source, "\$colunas[] = 'T003_D021_Id';") !== false,
    'quote header must persist commercial sellers and representative separately from the responsible ERP actor and price list when customer defaults exist.'
);
mcpAssert(
    strpos((string) $ven001Source, "'T003_D013_Id'") !== false
    && strpos((string) $ven001Source, "'T003_Flag_Lista_Preco'") !== false,
    'quote header changes must preserve the existing contact and price-list columns.'
);
