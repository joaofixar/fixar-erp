# Task 33 — Correção de feedback da aba Processar

Correção de apresentação: a aba voltou a exibir validação local de XML,
recepção aceita com recibo, rejeição e retorno inconclusivo sem mostrar SOAP,
inclusive os motivos persistidos de rejeição individual (código, descrição e
campo quando presente). O template usa inputs CSRF diretamente no HTML e
mantém a confirmação antes da transmissão. A consulta manual continua PRG e
escolhe a GNRE005 mais recente quando houver recibos históricos.

Também foi corrigida a validação genérica de referência obrigatória: ela aplica
o flag já persistido no snapshot oficial de cada receita/UF, sem criar ou mudar
snapshot. Não houve migration, banco, schema ou XML persistido alterado pelo
código. O código não fez chamada automática ao Portal; a transmissão e consulta
de homologação da guia #26 foram ações manuais do usuário.
