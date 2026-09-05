# Instruções Globais

- Textos exibidos ao usuário devem usar ortografia pt-BR com acentuação correta.
- Preserve sem tradução palavras de outros idiomas, nomes de produtos, nomes de classes, rotas, chaves de configuração e termos técnicos quando forem usados como identificadores.
- Mensagens de log, códigos internos, nomes de arquivos, placeholders e valores persistidos que funcionam como identificadores técnicos podem permanecer sem acentuação quando isso reduzir risco de incompatibilidade.

## Versionamento e Entregas

- Aplique [Semantic Versioning 2.0.0](https://semver.org/) a cada versão publicada: `MAJOR` para incompatibilidades, `MINOR` para funcionalidades compatíveis e `PATCH` para correções compatíveis.
- Trate uma versão publicada como imutável. Qualquer alteração posterior exige uma nova versão.
- Ao gerar uma versão, atualize de forma consistente `APP_VERSION` nos ambientes declarados por `App/Core/EnvironmentVars.php`, o pacote `Settings_<APP_NAME>_V<APP_VERSION>.json`, o campo `Version` do pacote, os indicadores de versão e build do `README.md`, `Doc/changelog.md` e `Doc/VERSION/<versão>/whatsnew.md`.
- Mantenha `Doc/changelog.md` no formato [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), com `Não publicado` no topo, a versão mais recente antes das anteriores e categorias como `Adicionado`, `Alterado`, `Corrigido`, `Descontinuado`, `Removido` e `Segurança` quando aplicáveis.
- Cada entrada de versão em `Doc/changelog.md` deve apontar para `Doc/VERSION/<versão>/whatsnew.md`.
- O arquivo `whatsnew.md` deve explicar benefícios, funcionalidades e correções em linguagem comercial. Inclua `## Detalhes técnicos` somente quando isso for necessário para implantação, integração, segurança ou suporte.
- Gere um único identificador de build no formato `YYMMDDHHIISS` no início da entrega. Use-o no indicador de build do README e no diretório `Build/<identificador>/`.
- O snapshot em `Build/<identificador>/` deve conter a cópia distribuível do projeto e uma cópia de `whatsnew.md` na raiz do build. Nunca inclua `.git`, `Build`, `.env*`, credenciais, logs ou outros dados transitórios.
- Valide os arquivos alterados e os metadados da versão antes de gerar o snapshot. Não gere uma versão ou build quando a solicitação não incluir uma entrega versionada.
