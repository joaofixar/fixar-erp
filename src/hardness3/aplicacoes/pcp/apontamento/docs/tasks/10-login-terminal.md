# Tarefa 10 — Login de terminal e seleção de empresa

Concluída em 16/10/2026.

O acesso público canônico é `/apontamento/`; sem sessão própria válida, ele
redireciona para a tela de login do terminal.

- O terminal usa `C007` e `hardness\SegurancaSenha::validarSenhaArmazenada`, como o Balanço; não cria senha, usuário ou tabela próprios.
- O contexto fica em `$_SESSION['pcp_apontamento']`; login não escreve a identidade ERP e logout preserva a sessão global.
- O terminal expira após 8 horas sem atividade, controlado somente em
  `pcp_apontamento`; a expiração da sessão ERP não é alterada.
- Empresas são revalidadas em `C019/C030/C004`, exigindo C029 203 e 308. Uma empresa é automática; várias exigem escolha.
- O card ERP mantém a URL atual e pode criar somente o contexto local de seu C007/empresa autorizados. Após sair do terminal, exige novo login local.
- `pcp_apontamento_authenticate_with_connection()` recebe conexão somente para permitir o teste descartável apontar ao schema temporário; o fluxo produtivo continua usando `pcp_apontamento_authenticate()` e sua conexão normal.
- `tests/task10-integration.php` cria `pcp_task10_validation` e chama a autenticação real contra C007s de teste: credencial válida, senha inválida, C007 inativo, ausência de 203/308, remoção de senha/hash do retorno e da sessão, regeneração do ID de sessão e logout isolado. Não usa credenciais reais nem consulta e032.
