---
status: accepted
---

# O sistema novo nasce do legado e diverge sob demanda

A Fixar comprou o código do Hardness e vai operar o próprio ERP com duas pessoas. O sistema novo é interno: não acompanha release do fornecedor, não atende outro cliente e não consulta a infraestrutura dele. Há várias empresas; a validação usa a que concentra orçamento, pedido e nota fiscal de saída.

O caminho é copiar o legado para uma árvore executável, fora de `sistema-legado/`, e ter login local dessa empresa antes de reescrever. Um trecho só é reescrito quando uma mudança da operação não cabe num ajuste localizado. A produção continua onde está.

## Considered Options

- Reescrever módulo a módulo antes de o legado subir. Estoque, nota, boleto e financeiro compartilham núcleo e tabelas; com duas pessoas a primeira tela atrasaria anos.
- Manter um fork compatível com atualização do fornecedor. O bootstrap do legado liga em telemetria e em trava de boleto do fornecedor, e a Fixar já tem regra própria em orçamento, pedido e boleto.

## Consequences

- O ambiente local não emite nota, não registra boleto, não envia e-mail e não fala com o fornecedor.
- O primeiro banco local recebe a estrutura atual da produção, sem linhas de negócio, mais uma empresa e um usuário locais. Um dump antigo de outro cliente não serve de schema.
- O importador lê a produção com usuário somente leitura e grava só no banco local, sem trava que pare a escrita de quem está operando. Cada execução substitui os dados locais. O usuário de login permanece. Edição local não volta para a produção.
- A cópia traz o banco inteiro. Senha, certificado, token e credencial de banco não ficam utilizáveis. O restante vem como está na produção, inclusive documento de cliente e de funcionário, e fica fora do git. Função fora do ciclo some do menu; o código compartilhado permanece.
- No git entra o código que faz o sistema subir, o contêiner, o importador e a documentação. Ficam de fora segredo, cache, arquivo de uso, mídia pesada, pastas de outros clientes e o script de backup. `sistema-legado/` e `referencia/` continuam só leitura. A aplicação do primeiro marco é a do legado; o interpretador é PHP 8.3, em contêiner, no padrão deste repositório. O que o PHP 8 removeu e o legado ainda chama fica numa camada fina de compatibilidade.

O importador e a ativação das regras próprias de orçamento, pedido, boleto e histórico de produto ficam substituídos pelo ADR 0002. O lugar do Hardness no git e o nascimento do banco local ficam substituídos pelo ADR 0003. O restante continua.
