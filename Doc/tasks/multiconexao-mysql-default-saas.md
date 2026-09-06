> **Status: IMPLEMENTADO**

# Plano - conexões MySQL múltiplas para Default e SAAS

## Objetivo

Modificar o projeto para suportar múltiplas conexões de banco de dados, ainda restritas a MySQL, permitindo que a aplicação consulte e grave dados na conexão principal `Default` e em conexões adicionais, inicialmente `SAAS`.

O requisito imediato é preparar uma conexão separada para mini projetos SaaS, como o módulo ContractFlow, sem perder a capacidade atual de operar as tabelas CAS do Manager na conexão `Default`.

## Escopo

- Manter MySQL como único driver de banco suportado pelo projeto.
- Evoluir a configuração para declarar mais de um storage de banco.
- Separar as variáveis de ambiente da conexão `Default` e da conexão `SAAS`.
- Permitir que models, services, migrator e rotinas CLI escolham explicitamente o storage usado.
- Garantir que fluxos existentes continuem usando `Default` por padrão.
- Preparar `SAAS` para armazenar mini projetos independentes e módulos como ContractFlow.
- Validar que uma mesma execução da aplicação possa consultar `Default` e `SAAS`.
- Atualizar documentação técnica com as novas variáveis e exemplos de uso.
- Criar validações automatizadas ou scripts de verificação para configuração, conexão e migração por storage.

## Fora do escopo

- Suportar outros SGBDs além de MySQL.
- Implementar sharding, replicação nativa de banco, leitura/escrita separada ou balanceamento.
- Migrar dados reais entre `Default` e `SAAS`.
- Implementar o módulo ContractFlow nesta task.
- Criar interface visual para gerenciar conexões.
- Criar APIs para consulta cross-storage.
- Permitir conexão arbitrária informada por usuário final em tempo de requisição.

## Estado atual relevante

- `App\Core\EnvironmentVars` define apenas um conjunto de variáveis `DB_*`.
- `App\Core\Config` monta `Config::$DB_STORAGE` com `Default` e `SAAS`, mas ambos usam atualmente os mesmos valores `DB_*`.
- `App\Core\Database` recebe `$storage = 'Default'` no construtor e lê `Config::$DB_STORAGE[$storage]`.
- `App\Core\SimpleMigrator` recebe `$storage = 'Default'`, mas descobre somente metadados `CAS`.
- `migrate.php` já aceita `--storage`, com padrão `Default`.
- Algumas rotinas de console, como limpeza e ativação de contas, já recebem storage no construtor.
- A base `Default` concentra o Manager e as tabelas CAS.
- O ContractFlow e outros mini projetos deverão operar na base `SAAS`.

## Estado final esperado

O projeto deve permitir:

1. configurar `Default` e `SAAS` com bancos MySQL distintos;
2. manter `Default` como storage padrão para código legado;
3. instanciar `Database('Default')` e `Database('SAAS')` na mesma execução;
4. validar de forma clara quando um storage não existe ou está incompleto;
5. executar migrations por storage, sem aplicar metadados de um módulo na base errada;
6. permitir models CAS apontarem para `Default` por padrão;
7. permitir models de mini projetos, como `CTR`, apontarem para `SAAS`;
8. registrar logs de erro e monitoramento identificando o storage usado;
9. documentar as variáveis de ambiente e os comandos operacionais.

## Convenção de configuração

### Variáveis da conexão Default

As variáveis atuais devem continuar representando a conexão principal:

- `DB_CONNECTION=mysql`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET`
- `DB_COLLATION`
- `DB_PREFIX`

Essas variáveis alimentam `Config::$DB_STORAGE['Default']`.

### Variáveis da conexão SAAS

Adicionar variáveis específicas para a conexão `SAAS`:

- `SAAS_DB_CONNECTION=mysql`
- `SAAS_DB_HOST`
- `SAAS_DB_PORT`
- `SAAS_DB_DATABASE`
- `SAAS_DB_USERNAME`
- `SAAS_DB_PASSWORD`
- `SAAS_DB_CHARSET`
- `SAAS_DB_COLLATION`
- `SAAS_DB_PREFIX`

Essas variáveis alimentam `Config::$DB_STORAGE['SAAS']`.

Quando variáveis `SAAS_DB_*` não estiverem definidas em ambiente `local`, a implementação pode usar defaults explícitos seguros para desenvolvimento, desde que fique claro que `SAAS` é um storage separado. Em `staging` e `production`, variáveis obrigatórias da conexão `SAAS` devem ser validadas quando o recurso estiver habilitado ou quando uma operação solicitar esse storage.

## Regras de driver

- `DB_CONNECTION` e `SAAS_DB_CONNECTION` devem aceitar somente `mysql`.
- Qualquer outro valor deve interromper a inicialização ou a operação que tentou usar o storage.
- A validação deve mencionar o storage afetado, por exemplo `Default` ou `SAAS`.
- O projeto não deve introduzir abstrações incompatíveis com os metadados e DDL atuais do MySQL.

## Ajustes em `EnvironmentVars`

- Declarar defaults para `SAAS_DB_*` em `getEnvDefault()`.
- Preencher valores por ambiente em `setEnvLocal()`, `setEnvStaging()` e `setProduction()`.
- Manter `DATABASE_CONNECTION = 'mysql'` como regra global.
- Definir `SAAS_DB_DATABASE` diferente de `DB_DATABASE` nos defaults de desenvolvimento, por exemplo `manager_saas_local`.
- Definir `SAAS_DB_PREFIX` de modo independente de `DB_PREFIX`.
- Atualizar `configure.php`, caso necessário, para gravar as novas variáveis no `.env`.

## Ajustes em `Config`

- Montar `Config::$DB_STORAGE['Default']` a partir de `DB_*`.
- Montar `Config::$DB_STORAGE['SAAS']` a partir de `SAAS_DB_*`.
- Criar validação comum para storage, evitando duplicação de checagens em `Database`, `SimpleMigrator` e services.
- Criar método utilitário, se fizer sentido no padrão do projeto, como `Config::getDbStorage(string $storage): array`.
- Validar driver, host, porta, database, username, charset e collation por storage.
- Garantir que `Default` sempre exista.
- Garantir que `SAAS` exista quando configurado ou quando solicitado por uma operação.
- Não expor senha em mensagens, logs ou dumps de configuração.

## Ajustes em `Database`

- Validar o storage recebido antes de acessar `Config::$DB_STORAGE[$storage]`.
- Manter `Default` como valor padrão do construtor.
- Armazenar o nome do storage na instância para logs.
- Registrar em logs de erro e monitoramento o storage usado.
- Configurar `PDO::ATTR_ERRMODE` de forma consistente com o restante do projeto.
- Evitar redirecionamento HTTP em contextos CLI; erros de conexão em CLI devem lançar exceção ou retornar diagnóstico controlado.
- Preservar compatibilidade com chamadas existentes que usam `new Database()`.

## Uso por models e services

- Models existentes CAS devem continuar usando `Default` por padrão.
- Novos models de mini projetos devem declarar ou receber o storage esperado.
- Services de domínio devem receber storage por construtor ou contexto explícito, evitando dependência implícita de sessão.
- Operações que consultem `Default` e `SAAS` na mesma execução devem instanciar conexões separadas e nomeadas.
- Não executar joins SQL diretos entre bases diferentes como regra de aplicação. Quando dados de `Default` forem necessários em `SAAS`, usar replicação/sincronização mínima documentada, como a réplica reduzida de `CasRps`.

## Migrations por storage

- `migrate.php --storage=Default` deve continuar funcionando para CAS.
- `migrate.php --storage=SAAS` deve conectar na base `SAAS`.
- O migrator deve impedir aplicação acidental de metadados de mini projetos na base `Default` quando houver filtro por módulo.
- A evolução planejada para módulos deve permitir comando equivalente a:

```text
php migrate.php --storage=SAAS --module=CTR
```

- Se o argumento `--module` ainda não existir, esta task deve prever a alteração ou registrar dependência com a task do ContractFlow.
- O log ou saída da migration deve informar storage, database e conjunto de metadata processado.

## Operação com Default e SAAS

Fluxos esperados:

- Autenticação, usuários, repositórios e autorizações continuam consultando `Default`.
- Mini projetos SaaS consultam e gravam suas tabelas na conexão `SAAS`.
- Instalação de mini projeto pode consultar `Default` para obter dados administrativos autorizados e sincronizar referência mínima em `SAAS`.
- ContractFlow deve validar `RepositoryId` na réplica `SAAS.CasRps`, não depender de joins diretos com `Default.CasRps`.
- Logs devem permitir identificar se uma falha ocorreu em `Default` ou `SAAS`.

## Segurança e configuração

- O `.env.example` deve documentar todas as variáveis `SAAS_DB_*` sem credenciais reais.
- Senhas de banco nunca devem ser impressas em logs, erros ou README.
- Validar nomes de storage contra lista configurada; não aceitar storage arbitrário vindo de request sem allowlist.
- Validar que `DB_CONNECTION` e `SAAS_DB_CONNECTION` são `mysql`.
- Em produção, falha de configuração do storage solicitado deve ser erro bloqueante.
- Configurações ausentes de `SAAS` não devem quebrar fluxos que usam apenas `Default`, exceto se a decisão final optar por exigir `SAAS` sempre configurado.

## Validação planejada

### Configuração

- Ambiente local carrega `Default` e `SAAS` em `Config::$DB_STORAGE`.
- `Default` usa `DB_*`.
- `SAAS` usa `SAAS_DB_*`.
- Driver diferente de `mysql` é recusado.
- Storage inexistente gera erro claro.
- `.env.example` contém as variáveis das duas conexões.

### Conexão

- `new Database('Default')` conecta na base configurada em `DB_DATABASE`.
- `new Database('SAAS')` conecta na base configurada em `SAAS_DB_DATABASE`.
- Uma mesma execução consegue instanciar as duas conexões.
- Erro em `SAAS` não mascara nem altera configuração de `Default`.
- Logs de consulta e erro incluem o nome do storage.

### Migration

- `php migrate.php --storage=Default --dry-run` continua gerando SQL CAS para `Default`.
- `php migrate.php --storage=SAAS --dry-run` usa a database `SAAS`.
- Migration com storage inválido falha antes de executar DDL.
- Quando houver suporte a módulo, `--module=CTR` processa apenas metadados do mini projeto correspondente.

### Regressão

- Login e cadastro existentes continuam usando `Default`.
- Rotinas de limpeza e ativação de contas continuam funcionando com `Default`.
- Fluxos já existentes que chamam `new Database()` sem argumento mantêm comportamento anterior.
- Nenhuma consulta existente passa a usar `SAAS` por acidente.

## Critérios de aceite

- O projeto aceita configurar pelo menos dois storages MySQL: `Default` e `SAAS`.
- `Default` permanece compatível com as variáveis `DB_*` existentes.
- `SAAS` possui variáveis próprias `SAAS_DB_*`.
- `Config::$DB_STORAGE` representa corretamente cada conexão.
- `Database` e `SimpleMigrator` validam storage por allowlist e mantêm MySQL como único driver.
- É possível consultar `Default` e `SAAS` na mesma execução da aplicação.
- Migrations e services conseguem receber o storage explicitamente.
- Código legado continua funcionando sem informar storage.
- Documentação técnica e `.env.example` descrevem a configuração multi-conexão.
- Testes ou validações comprovam configuração, conexão, migration e regressão dos fluxos atuais.

## Arquivos previstos para alteração na implementação

- `App\Core\EnvironmentVars.php`.
- `App\Core\Config.php`.
- `App\Core\Database.php`.
- `App\Core\SimpleMigrator.php`.
- `migrate.php`.
- `configure.php`, se necessário para gravar as novas variáveis no `.env`.
- `.env.example`.
- `README.md`.
- Rotinas CLI que validam storage, se precisarem usar a validação comum.
- Models ou base model, caso exista ou seja criada, para aceitar storage explícito.
- Arquivos de teste ou validação compatíveis com a estratégia do projeto.
