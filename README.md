# Manager

Sistema de gestão de contas de usuários e aplicativos.

<img src="https://img.shields.io/badge/license-MIT-green"><img/>
<img src="https://img.shields.io/badge/version-1.0.0-blue"><img/>
<img src="https://img.shields.io/badge/build-260905180409-orange"><img/>

Consulte o [Changelog](./Doc/changelog.md), as [novidades da versão atual](./Doc/VERSION/1.0.0/whatsnew.md), o [documento de produto](./Doc/RFC/001-manager-plataforma-de-gestao-de-contas.md) e as [especificações técnicas](./Doc/ADR/001-arquitetura-e-operacao-do-manager.md).

## Primeiros passos

Clone o repositório e acesse o diretório do projeto:

```bash
git clone <URL_DO_REPOSITORIO> Manager
cd Manager
```

As instruções de configuração do ambiente, instalação, carga inicial e rotinas operacionais estão no [ADR técnico](./Doc/ADR/001-arquitetura-e-operacao-do-manager.md).

## Conexões de banco

O projeto usa somente MySQL. A conexão principal `Default` continua usando as variáveis `DB_*` e atende o Manager, autenticação, usuários, repositórios e tabelas CAS.

Mini projetos SaaS usam conexões separadas. A conexão inicial é `SAAS`, configurada por `SAAS_DB_*`, permitindo que a aplicação consulte `Default` e `SAAS` na mesma execução sem misturar schemas.

Exemplos:

```bash
php migrate.php --storage=Default --module=CAS
php migrate.php --storage=SAAS --module=CTR
```

Quando `--module` não é informado, o padrão continua sendo `CAS`.

## Visão geral

O Manager centraliza a gestão do ciclo de vida de contas de usuários e dos aplicativos associados a cada cliente. A plataforma apoia organizações que precisam administrar acessos, responsabilidades e serviços de forma consistente, com visão centralizada e regras adequadas a cada contexto de uso.

## Principais funcionalidades

- Gestão centralizada de contas e aplicativos para múltiplos clientes.
- Controle de acesso por perfis e permissões.
- Acompanhamento do ciclo de vida das contas, incluindo confirmação de e-mail, recuperação de acesso e bloqueios.
- Registro de atividades para auditoria e acompanhamento operacional.
- Recursos de manutenção para preservar a continuidade do serviço.
