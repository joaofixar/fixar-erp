Status: ready-for-human
Orbit: ERPFX-63

# 34: O login vira um cartão com a marca da Fixar

**What to build:** O login vira um cartão único no centro, sobre fundo claro quadriculado, no desenho do login do ReUI sem as entradas de terceiros. O cartão tem o logo dourado da Fixar, o título Entrar, o Usuário e a Senha, com "Esqueceu a senha?" ao lado do rótulo e um olho que mostra a senha. O botão é o marinho do logo (`#0a111c`), e o amarelo (`#ffd500`) aparece só no foco. O erro da autenticação aparece no cartão em vez do alert, e o usuário digitado volta preenchido. O rodapé é "© Fixar Industrial" com o ano. Na sidebar, a marca troca o quadrado FX pelo logo branco.

A autenticação, o modal de recuperação de senha e o campo do OAuth continuam como estão.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] Cartão central com o logo dourado, Entrar, Usuário e Senha
- [x] "Esqueceu a senha?" ao lado do rótulo da Senha abre o modal de hoje
- [x] O olho mostra e esconde a senha
- [x] Botão marinho do logo, foco amarelo
- [x] O erro da autenticação aparece no cartão e o usuário digitado volta preenchido
- [x] O modal de senha ganha a mesma paleta
- [x] A marca da sidebar mostra o logo branco
- [x] O percurso cobre o logo, o erro no cartão, o olho e o logo da marca

## Comments

`src/hardness3/outros/login/index.php` e `login.css`; o logo branco em `src/fx/shell.js`.

- Os logos saem dos PNG de 3055×1548 (Dourada e Branca), reduzidos no canvas do Chrome: `login/fixar.png` com 400px de largura (mostrado a 148px) e `src/fx/fixar-branca.png` com 48px de altura (mostrado a 24px). As cores são amostras do próprio logo.
- O PHP de autenticação monta os erros como `alert('...')` no `$js`. A página troca o `window.alert` pela caixa `#login-erro` antes do `$js`, sem mexer no PHP. Com o modal de senha aberto (`is-modal-open`) o alert continua nativo.
- Saem da página a consulta da C004 (nome e logo da empresa), o painel azul com o `logoSistema`, os textos de impacto, a fundação e a cor de fundo configurável. O nome da empresa já aparece na marca depois do login.
- O jQuery 1.8 da página recusa trocar o `type` de input pelo `.attr()`, então o olho troca pela propriedade nativa.
- Os ids que o percurso e o modal usam continuam: `#login-user`, `#login-password`, `#form-login`, `#open-psw`, `#modal-psw`, `#form-psw`.
- Teste: o percurso do desktop.
