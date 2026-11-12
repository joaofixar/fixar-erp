---
status: accepted
---

# O Hardness editável fica no repositório e o banco nasce local

A Fixar altera o Hardness a partir da base que já abre. O código que sobe entra no git, em `src/`, e o contêiner serve esse código. O banco inicial é uma fotografia do MariaDB local, gravada no repositório. A subida não lê a produção. Os dados de produção esperam uma ferramenta futura, quando uma melhoria os pedir.

## Considered Options

- Manter o Hardness fora do git, recriado a partir de `sistema-legado/` a cada preparação. O ajuste não fica no histórico, e a preparação seguinte apaga o que foi feito.
- Continuar lendo a produção para obter estrutura, empresa e permissões em cada máquina nova. A base limpa ficaria presa à produção antes de existir melhoria que peça esses dados.
- Trocar agora o prefixo `/hardness3/` por `/erp/`. O produto grava esse prefixo em centenas de arquivos, e a rota do menu já se chama `erp`.

## Consequences

- `src/` toma o lugar de `base/html`. O que está commitado é o Hardness que já sobe: PHP 8.3, isolamento, menu do produto e personalização fora. Os endereços continuam `/login` e `/hardness3/`.
- O produto continua se chamando Hardness. `hardness3` é a pasta e o prefixo. Matar esse prefixo é uma melhoria seguinte, e o endereço público se decide nessa hora.
- `sistema-legado/` continua a referência só de leitura, fora do git. Fora do git também ficam segredo, cache, arquivo de uso, pasta de outro cliente, documento de cliente e de funcionário, mídia pesada e a configuração local de conexão.
- A preparação deixa de recriar o produto. O que resta é gravar a configuração local de conexão e os diretórios de cache.
- A fotografia traz a estrutura, inclusive as rotinas que as telas chamam, a empresa de validação, o usuário local e as permissões. Não traz cliente, produto, orçamento, pedido, nota nem título, nem senha, certificado ou token utilizável. A subida restaura essa fotografia só quando o volume está vazio.
- `docker compose up` e os testes HTTP continuam. O importador que lia a produção sai do repositório. A ferramenta que importa os dados de produção não faz parte desta decisão.
- Este ADR substitui, no ADR 0001, o que entra no git e a leitura da produção ao nascer o banco. O ADR 0002 permanece: a base segue sem personalização e sem retrato.
