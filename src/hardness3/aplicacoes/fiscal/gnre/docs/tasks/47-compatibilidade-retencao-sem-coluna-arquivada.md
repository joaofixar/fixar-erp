# Task 47 — compatibilidade da retenção sem coluna de arquivamento

O módulo detecta centralmente, por requisição, a disponibilidade de
`GNRE001_Arquivada`. O SQL manual da Task 45 continua sendo o único meio de
habilitar arquivamento; nenhuma coluna é criada ou simulada em runtime.

Com a coluna disponível, a Central filtra arquivadas por padrão, oferece
“Incluir arquivadas” e conserva arquivar/desarquivar segundo a política da
Task 45. Sem a coluna, a consulta da Central não referencia esse campo, o filtro
e as ações de arquivamento não são exibidos e POSTs forjados são recusados com
mensagem pública segura.

O fallback sem coluna é exclusivamente a exclusão transacional de rascunho da
empresa GNRE ativa: status `R` e ausência de GNRE004, GNRE005 e GNRE008,
revalidadas sob bloqueio. Qualquer histórico bloqueia a exclusão. Esta tarefa
não usa Homologação como fallback e não altera ambiente, snapshot, XML,
transmissão, consulta, PDF, Portal, banco ou guias reais.
