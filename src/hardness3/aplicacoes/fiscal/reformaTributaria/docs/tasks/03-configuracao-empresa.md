# Task 3 — Configuração da empresa

## Escopo entregue

- Funcionalidade somente leitura em `public/configuracao-empresa.php`.
- Seleção feita exclusivamente nessa funcionalidade, entre empresas ativas vinculadas ao usuário.
- Consulta do campo `C004_Flag_Tipo_Preenchimento_Reforma_Tributaria`, sem uso ou alteração de `Empresa_Atual`.
- Exibição simples dos estados: automático ativado (`1`), desativado/manual (`0`) ou não configurado (outros valores, vazio ou nulo).
- Indicação da manutenção em **Cadastro de Empresas > Impostos > Tipo Preenchimento Reforma Tributária**.
- Nenhuma consulta ou validação de CFOP, NCM, CST, classificação, produtos ou créditos por empresa.

## Guias visuais

- A ação **Ver onde alterar esta configuração** abre um modal próprio com o caminho textual: **Configuração → Empresa → Impostos** e a captura com o campo destacado.
- O atalho de manutenção fica no cabeçalho do modal, à direita e antes do fechar, seguindo o padrão da consulta de configurações fiscais.
- O padrão para imagens de orientação deste painel é **1600 × 1000 px** (proporção 16:10), em PNG. As capturas devem mostrar apenas a área necessária, manter o texto legível e usar uma marcação discreta no campo ou ação relevante.
- A imagem desta funcionalidade está em `public/assets/guia-configuracao-empresa.png`.

## Testes locais

- `php -l` nos arquivos novos e alterados.
- `git diff --check`.

## Ajuste posterior

- A listagem e a leitura da configuração consideram toda empresa ativa vinculada ao usuário em `C019`. O perfil associado à vinculação não é requisito para esta consulta somente leitura; a propriedade da empresa continua validada na consulta individual.
