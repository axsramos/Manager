# ADR 001 - Arquitetura e Operação do Manager

**Status:** Aprovado  
**Data:** 2026-09-05  
**RFC de referência:** [RFC 001 - Manager: Plataforma de Gestão de Contas](../RFC/001-manager-plataforma-de-gestao-de-contas.md)

## Contexto

O Manager implementa a proposta da RFC 001 como uma aplicação PHP com persistência relacional, controle de acesso e rotinas de manutenção em linha de comando. A aplicação mantém informações de usuários, repositórios, aplicativos, perfis, permissões e parâmetros em tabelas com prefixo configurável.

## Decisão

Manter a arquitetura atual baseada em PHP, MySQL e Apache, com rotas encaminhadas para `index.php`, metadados de entidades em `App/Metadata`, modelos em `App/Models`, regras de negócio em `App/Class` e serviços centrais em `App/Core`.

As operações que alteram a estrutura ou dados operacionais devem ser executadas pela CLI. O Apache bloqueia acesso direto a scripts operacionais, logs, arquivos de ambiente e metadados Git.

## Ambiente técnico

- PHP com extensões `PDO` e `PDO MySQL`.
- MySQL como banco de dados relacional.
- Apache HTTP Server com `mod_rewrite` e suporte a `.htaccess`.
- Composer para instalação das dependências PHP, incluindo `phpmailer/phpmailer`.
- Laragon como ambiente de desenvolvimento local observado neste projeto.
- Arquivo `.env` para parâmetros por ambiente; esse arquivo e suas variações não fazem parte de builds.

## Instalação e operação

Depois de clonar o projeto, gere as variáveis de ambiente, revise as credenciais, execute a migração e realize a carga inicial:

```bash
php configure.php --env=production
php migrate.php --storage=Default
php seed.php --storage=Default
```

Antes da migração, o banco MySQL deve existir e as credenciais em `.env` devem permitir operações DDL e DML. O diretório `Repositories` também precisa permitir escrita.

O comando `seed.php` valida o ambiente, banco, tabelas, diretórios e pacote antes de solicitar dados para as contas `admin` e `support`. A carga cria os registros iniciais de aplicativo, usuários, repositórios, tipos de usuário, acessos e diretórios próprios. A reexecução completa é idempotente; uma carga parcial incompatível encerra com erro.

Os tipos de usuário predefinidos são `ADMINISTRATOR ACCOUNT`, `MANAGER ACCOUNT`, `SUPPORT ACCOUNT` e `USER ACCOUNT`. Esses valores pertencem ao domínio da aplicação e não devem ser removidos.

O pacote de configurações deve ter o nome `Settings_<APP_NAME>_V<APP_VERSION>.json`. Os valores de `APP_VERSION`, `CasAppVer` e `Version` do pacote devem permanecer alinhados.

O processamento de `ApplyApplicationSettings` é a única falha tolerada após a carga principal: os usuários e acessos são preservados, e a aplicação do pacote pode ser repetida pela interface web.

## Rotinas operacionais

### Limpeza de logs

Os logs são mantidos em `Temp/Logs`, separados entre `DB`, `Mail`, `Auth`, `Authorization`, `Runtime`, `Setup`, `Audit` e `Others`. Simule e, depois de revisar o resultado, execute a exclusão:

```bash
php cleanup-logs.php --days=30
php cleanup-logs.php --days=30 --delete
```

O parâmetro `--days` aceita valores entre 1 e 3650. A limpeza ignora links simbólicos e remove somente arquivos `.log` dentro dessa árvore.

### Limpeza de contas não confirmadas

```bash
php cleanup-unconfirmed-accounts.php --days=30
php cleanup-unconfirmed-accounts.php --days=30 --delete
```

A operação remove, em transação, os registros da conta e do repositório correspondente, os tokens de ativação relacionados e registra somente hashes de e-mail em `Temp/Logs/Audit`.

### Ativação de contas verificadas

`ACCOUNT_ACTIVATION_HOURS` define, no `.env`, o prazo de ativação das novas contas. O valor padrão é 168 horas.

```bash
php activate-verified-users.php --limit=500
php activate-verified-users.php --limit=500 --execute
```

O primeiro comando é uma simulação. A execução com `--execute` lê os eventos recentes `register_email_verified` em `Temp/Logs/Auth` e atualiza `CasUsrActDtt` para as contas elegíveis. A saída e os logs não exibem e-mails completos, tokens ou senhas.

## Consequências

- A aplicação pode manter regras de negócio e persistência organizadas por responsabilidade.
- A implantação exige Apache, PHP, MySQL e dependências Composer compatíveis.
- Rotinas administrativas dependem de acesso ao terminal, reduzindo o risco de execução acidental pela web.
- Alterações de versão exigem atualização coordenada dos metadados do aplicativo, pacote, documentação e build.

## Alternativas consideradas

### Scripts operacionais acessíveis por HTTP

Rejeitada por expor operações administrativas e de manutenção ao ambiente web.

### Configuração sem arquivo de ambiente

Rejeitada porque dificultaria separar credenciais e parâmetros entre ambientes.

### Aplicação sem pacote versionado de configurações

Rejeitada porque impediria validar a compatibilidade entre a aplicação e suas configurações iniciais.
