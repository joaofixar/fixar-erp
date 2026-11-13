<?php
declare(strict_types=1);

return [
    'name' => 'Apontamento de Produção',
    // Rota pública curta, configurada no .htaccess raiz.
    'base_path' => '/apontamento',
    // C029 existente: PCP, no grupo Estoque. Mantido como pré-requisito.
    'permission_pcp' => 203,
    'permission_operation' => 308,
    // 308 libera o menu e a operação do terminal; 120 é a configuração PCP
    // já existente no ERP e governa as telas de gestão deste módulo.
    'permission_operation_name' => 'Acessar Apontamento PCP',
    'permission_manage' => 120,
    // 309 libera alterações administrativas em apontamentos; a verificação
    // será aplicada separadamente do acesso operacional e das configurações.
    'permission_edit' => 309,
    // Tempo máximo sem atividade no terminal, em segundos (8 horas).
    // É aplicado somente ao contexto pcp_apontamento, não à sessão do ERP.
    'terminal_idle_timeout' => 28800,
    // O modelo e o backend de retrabalho permanecem prontos, mas a opção não
    // faz parte do piloto enquanto não houver motivos cadastrados em PCP005.
    'show_rework' => false,
];
