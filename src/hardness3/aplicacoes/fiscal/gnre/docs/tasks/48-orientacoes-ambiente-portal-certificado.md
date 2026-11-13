# Task 48 — Orientações de ambiente e acesso ao Portal GNRE

## Entrega

A tela Configuração passou a apresentar, após o painel de seleção e salvamento do Ambiente, um painel independente e apenas orientativo, intitulado “Automação via Web Service”. Ele esclarece que, para transmitir e consultar GNRE por Web Service, a empresa deve utilizar certificado digital e-CNPJ válido e solicitar a habilitação diretamente no Portal GNRE antes do primeiro uso.

Os acessos externos visíveis são:

- [Portal GNRE — Produção](https://www.gnre.pe.gov.br:444/gnre/portal/automacao.jsp)
- [Portal GNRE — Homologação](https://www.testegnre.pe.gov.br:444/gnre/portal/automacao.jsp)

Ambos abrem em nova aba com `target="_blank"` e `rel="noopener noreferrer"`, em linhas separadas. Eles são apresentados separadamente de Salvar para deixar claro que não são operações realizadas pelo ERP.

Essa informação é orientativa; não inicia integração automática nem cadastra, armazena ou ativa certificados, senha, PFX, token ou outras credenciais no ERP.

## Limites mantidos

A tela somente escolhe o ambiente das novas guias. A escolha posterior não reinterpreta guias existentes: cada guia continua usando exclusivamente o ambiente imutável do snapshot vinculado `GNRE007`.

Não houve integração, cadastro, armazenamento ou manipulação de certificado, senha, PFX, token ou outra credencial. A integração e o armazenamento seguro de certificado no ERP permanecem fora do escopo e deverão ser tratados em tarefa própria. Não houve alteração em `GNRE009`, `GNRE007`, transmissão, consulta, PDF, XML, fila, endpoints operacionais ou regras fiscais.

## Testes

`task48-environment-portal-guidance-static.php` verifica os dois links exatos, a abertura protegida em nova aba, a distinção entre orientação e integração, a ausência de campos/persistência de credenciais, a manutenção das opções `2` e `1` e do salvamento existente de ambiente.

## Roteiro manual sem operação externa

1. Abra Configuração em Homologação e em Produção.
2. Confirme que os dois links abrem em nova aba e usam exatamente as URLs acima.
3. Confirme que o texto não sugere certificado cadastrado ou salvo no ERP.
4. Altere e salve o ambiente; confirme que o bloco não interfere no salvamento.
5. Não transmita, consulte o Portal, solicite PDF nem altere guia real.
