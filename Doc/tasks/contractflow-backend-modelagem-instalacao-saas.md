> **Status: PLANEJADO**

# Plano - backend do ContractFlow com modelagem e instalação SaaS

## Objetivo

Implementar o backend do módulo ContractFlow para gestão dinâmica de contratos, itens, franquias, consumo e aceite legal, conforme:

- `Doc/RFC/002-contractflow-gestao-dinamica-de-contratos-e-franquias.md`;
- `Doc/ADR/002-particao-vertical-line-item-pricing-e-ledger-do-contractflow.md`;
- `Doc/ADR/003-modelagem-de-dados-do-contractflow.md`.

O trabalho deve entregar a base técnica do módulo de contratos sem interface visual e sem endpoints de API nesta tarefa. A implementação deve tratar modelagem de dados, migrations, models, metadata, classes de domínio, funções reutilizáveis e fluxo de instalação por tenant na base `SAAS`.

## Escopo

- Criar a estrutura backend do módulo usando `Contract` para a camada de aplicação e `CTR` para models e metadata.
- Criar migrations para a base `SAAS`, não para `Default`.
- Criar a réplica reduzida de `CasRps` na base `SAAS`, contendo somente `CasRpsCod` e `CasRpsDsc`.
- Criar as tabelas `CTRConcurrencyGroup`, `CTRContract`, `CTRContractDetail`, `CTRContractItem`, `CTRContractItemDetail`, `CTRContractItemConsumption` e `CTRContractSignatory`.
- Adicionar `RepositoryId` nas tabelas operacionais do módulo, relacionado a `CasRps.CasRpsCod` da base `SAAS`.
- Garantir que todas as novas tabelas físicas do módulo ContractFlow usem o prefixo `CTR`, exceto a réplica reduzida `CasRps` na conexão `SAAS`.
- Implementar models e metadata em `App\Models\CTR` e `App\Metadata\CTR`.
- Criar classes de domínio em diretório próprio do módulo, preservando separação entre ContractFlow e Manager.
- Criar funções reutilizáveis para contratos, itens, saldos, ledger, instalação de pacote e resolução de dados predefinidos.
- Implementar fluxo backend de instalação do módulo por repositório/tenant, com comportamento semelhante ao fluxo de pacote usado por `App\Controllers\Support\Package.php`.
- Criar pacote de configurações predefinidas do ContractFlow em `App\Static\Rules\Contract`, com localização por hash e aplicação idempotente no repositório informado.
- Prever funções que possam ser consumidas futuramente por frontend e APIs, sem criar telas nem controllers de API nesta tarefa.
- Criar validações automatizadas ou scripts de verificação compatíveis com o padrão do projeto.

## Fora do escopo

- Criar telas em `App\Views\SBAdmin\Contract`.
- Criar controllers web para navegação visual do módulo, exceto classes backend estritamente necessárias à instalação ou execução por linha de comando.
- Criar endpoints em `App\Controllers\API\V1\Contract`.
- Implementar integração com WhatsApp, QR Code, apps mobile, parceiros externos ou gateway de pagamento.
- Implementar assinatura digital avançada ou guarda jurídica externa de documentos.
- Criar rotinas de cobrança financeira, emissão fiscal ou conciliação bancária.
- Publicar versão, atualizar changelog ou gerar build.

## Estado atual relevante

- O projeto já possui documentação aprovada para o ContractFlow em RFC 002, ADR 002 e ADR 003.
- O Manager utiliza separação vertical por responsabilidade, com exemplos em `App\Controllers\Manager`, `App\Controllers\Support`, `App\Views\SBAdmin\Manager` e `App\Views\SBAdmin\Support`.
- O mecanismo atual de pacotes do Manager usa `App\Controllers\Support\Package.php`, `App\Class\Support\Install`, `App\Class\Manager\ApplyApplicationSettings` e o trait `App\Traits\DataPackage`.
- O pacote atual do Manager está em `App\Static\Rules\Manager\Settings_Manager_V1.0.0.json`.
- `migrate.php` aceita `--storage`, e `App\Core\SimpleMigrator` já utiliza metadados para criar estruturas.
- A base `Default` concentra as estruturas CAS atuais do Manager. O ContractFlow deve operar em formato SaaS na base `SAAS`.
- A base `SAAS` poderá compor vários mini projetos; por isso, tabelas novas do módulo Contract devem usar prefixo físico `CTR` para evitar colisão com nomes genéricos como `Contract`.
- A instalação do módulo precisa ser acionada por tenant/repositório, aplicando dados predefinidos que compõem o estado inicial da aplicação.

## Estado final esperado

O backend do ContractFlow deve permitir:

1. executar migration do módulo contra `SAAS`;
2. criar ou validar a réplica reduzida de `CasRps` em `SAAS`;
3. criar as tabelas relacionais do ContractFlow com constraints e índices previstos nos ADRs;
4. instanciar models e metadata `CTR` para todas as tabelas do módulo;
5. instalar o módulo em um repositório/tenant a partir de um hash de pacote;
6. localizar deterministicamente o pacote de configurações predefinidas do ContractFlow;
7. aplicar dados iniciais de forma idempotente no tenant informado;
8. disponibilizar classes de backend reaproveitáveis por controllers web e APIs futuras;
9. registrar resultados observáveis de instalação, validação e falha parcial sem depender de interface visual.

## Estrutura de diretórios prevista

- `App\Class\Contract` para orquestrações e serviços de domínio do ContractFlow.
- `App\Models\CTR` para models das tabelas do módulo.
- `App\Metadata\CTR` para metadados usados por migrations e models.
- `App\Static\Rules\Contract` para regras estáticas e pacote de instalação do ContractFlow.
- `App\Static\Rules\Contract\Settings` ou subdiretório equivalente para separar pacotes versionados, se o padrão atual permitir essa organização sem quebrar `DataPackage`.
- `Doc\tasks` para documentação de planejamento e rastreabilidade.

Não criar nesta tarefa:

- `App\Controllers\Contract`;
- `App\Controllers\API\V1\Contract`;
- `App\Views\SBAdmin\Contract`.

Esses diretórios podem ser previstos nos documentos de arquitetura, mas a task atual deve se limitar ao backend sem interface.

## Modelagem de dados

### Storage

- A migration deve apontar para `SAAS`.
- Nenhuma migration do ContractFlow deve criar tabelas na conexão `Default`, exceto se uma etapa futura documentar explicitamente sincronização entre bases.
- O comando de migration deve permitir executar somente o conjunto de metadata `CTR`, sem recriar estruturas CAS do Manager indevidamente.
- Todas as tabelas novas do módulo ContractFlow devem usar prefixo `CTR`. A regra não se estende às tabelas `CAS`, nem à réplica reduzida de `CasRps` em `SAAS`.
- Nomes genéricos como `Contract` devem ser evitados para reduzir risco de colisão com outros mini projetos. O módulo Contract deve usar `CTRContract`; um módulo futuro de locações, por exemplo, poderá usar `LCAContract`.

### Réplica reduzida de `CasRps`

Criar em `SAAS`:

```sql
CREATE TABLE CasRps (
    CasRpsCod INT NOT NULL PRIMARY KEY,
    CasRpsDsc VARCHAR(255) NOT NULL
);
```

Regras:

- A tabela é uma referência mínima de repositório/tenant.
- A origem autorizada continua sendo a estrutura administrativa do Manager.
- A sincronização deve inserir ou atualizar somente `CasRpsCod` e `CasRpsDsc`.
- A instalação do ContractFlow deve validar a existência do `CasRpsCod` correspondente antes de aplicar dados do módulo.

### Tabelas do ContractFlow

Criar metadata e model para:

- `CTRConcurrencyGroup`;
- `CTRContract`;
- `CTRContractDetail`;
- `CTRContractItem`;
- `CTRContractItemDetail`;
- `CTRContractItemConsumption`;
- `CTRContractSignatory`.

As tabelas operacionais com dados próprios do módulo devem possuir `RepositoryId`:

- `CTRConcurrencyGroup.RepositoryId`;
- `CTRContract.RepositoryId`;
- `CTRContractItem.RepositoryId`;
- `CTRContractItemConsumption.RepositoryId`;
- `CTRContractSignatory.RepositoryId`.

As tabelas de detalhe 1:1 não precisam repetir `RepositoryId`:

- `CTRContractDetail`;
- `CTRContractItemDetail`.

O escopo dessas tabelas deve ser derivado por join com `CTRContract` ou `CTRContractItem`.

### Restrições obrigatórias

- `CTRContract.Id` deve usar UUID v4 em `CHAR(36)`.
- Valores monetários devem ser persistidos em centavos usando `BIGINT`.
- `CTRContractItem.LineTotal` deve ser `GENERATED ALWAYS AS (...) STORED`.
- `CTRContractItemConsumption` deve operar como append-only.
- `CTRContractItemConsumption.CurrentBalance` deve possuir `CHECK (CurrentBalance >= 0)`.
- Relações de detalhe e composição podem usar `ON DELETE CASCADE` conforme ADR 003.
- Índices mínimos devem seguir o ADR 003, incluindo `RepositoryId` nas buscas de alta frequência.

## Classes e funções previstas

### Serviços de domínio

Criar classes em `App\Class\Contract` com responsabilidades pequenas e reaproveitáveis. Nomes finais podem seguir o padrão do projeto, mas a implementação deve cobrir:

- resolução e validação de tenant/repositório na base `SAAS`;
- sincronização mínima de `CasRps` para `SAAS`;
- criação e consulta de grupos de concorrência;
- criação, ativação, supersessão, cancelamento e expiração de contratos;
- criação e manutenção de itens de contrato;
- cálculo e validação de saldos de franquia;
- registro append-only de consumo e estorno;
- registro de signatários e hash de aceite;
- montagem de resultado estruturado para uso por frontend ou APIs futuras.

### Funções genéricas e reutilizáveis

Sempre que possível, criar funções ou helpers genéricos para:

- executar operações idempotentes de insert/read/update controlado;
- validar existência de repositório em storage configurável;
- resolver caminho seguro de pacote dentro de `App\Static\Rules`;
- validar hash de pacote;
- carregar JSON de pacote com validação de schema mínimo;
- aplicar lista de dados predefinidos com checkpoint por task;
- retornar resultado padronizado por etapa, com estados `created`, `existing`, `updated`, `skipped`, `applied`, `warning` e `failed`;
- proteger ledger append-only contra alterações destrutivas por caminhos operacionais.

Essas funções não devem depender de `AuthSession`, HTML, formulário ou controller web. A dependência de sessão deve ficar restrita a adaptadores futuros.

## Fluxo de instalação do módulo no tenant

O módulo será usado em formato SaaS. Cada repositório/tenant deve ativar a aplicação ContractFlow em seu próprio contexto.

O fluxo deve ser semelhante ao aplicado pelo Manager em `App\Controllers\Support\Package.php`, mas implementado como backend reutilizável:

1. receber `RepositoryId` e hash do pacote;
2. validar se `RepositoryId` existe na réplica `CasRps` da base `SAAS`;
3. localizar o pacote estático correspondente ao hash informado;
4. validar `ProductKey`, `ProductName`, `Version`, `RunScript` e estrutura mínima do JSON;
5. confirmar se o pacote pertence ao ContractFlow;
6. verificar se a aplicação já foi aplicada para o tenant;
7. aplicar dados predefinidos de forma idempotente;
8. registrar checkpoints de tarefas aplicadas;
9. retornar resumo estruturado sem depender de tela;
10. permitir reexecução segura sem duplicar registros.

### Localização por hash

O instalador deve partir de um hash recebido e localizar o arquivo de configurações predefinidas.

Regras:

- O hash não deve ser interpretado como caminho de arquivo.
- O caminho resolvido deve permanecer dentro de `App\Static\Rules\Contract`.
- O pacote deve declarar metadados suficientes para validar produto e versão.
- O backend deve falhar com erro claro se nenhum pacote corresponder ao hash.
- A comparação de hash deve usar conteúdo canônico do pacote ou manifesto explícito, evitando depender de nome de arquivo informado pelo usuário.

### Pacote estático do ContractFlow

Criar pacote inicial do ContractFlow em padrão compatível com o mecanismo de pacote do projeto.

Nome sugerido:

```text
App/Static/Rules/Contract/Settings_ContractFlow_V1.0.0.json
```

Conteúdo mínimo previsto:

- `ProductKey`;
- `ProductName`;
- `Version`;
- `Description`;
- `RunScript`;
- `Packages`;
- `BaseTableData` ou seção equivalente para dados iniciais do módulo.

Dados iniciais esperados:

- grupos de concorrência base, se houver domínio mínimo aprovado;
- parâmetros operacionais do ContractFlow;
- status canônicos de contrato;
- tipos canônicos de item;
- ciclos de cobrança aceitos;
- configurações de ledger e instalação;
- checkpoints de pacote por tenant.

Quando o domínio mínimo ainda não estiver definido, o pacote deve criar apenas parâmetros técnicos indispensáveis e deixar dados comerciais para pacote posterior.

## Idempotência e checkpoints

- A instalação deve ser idempotente por `RepositoryId`, `ProductKey`, `Version` e task do pacote.
- Uma task já aplicada com o mesmo conteúdo deve ser tratada como `existing` ou `skipped`.
- Uma task aplicada com conteúdo divergente deve gerar `failed` ou `warning`, conforme criticidade definida.
- Falhas antes do checkpoint principal devem encerrar a instalação como erro.
- Falhas em tasks independentes devem permitir diagnóstico claro e reexecução.
- O instalador não deve apagar dados do tenant automaticamente.
- Não criar opção destrutiva para resetar o módulo nesta task.

## Migrations

### Ajustes no migrator

Avaliar e implementar, se necessário:

- suporte a executar metadata por namespace ou grupo, como `CAS` e `CTR`;
- suporte a storage `SAAS`;
- geração de colunas calculadas `GENERATED ALWAYS AS (...) STORED`;
- geração de `CHECK Constraint`;
- geração de índices com ordenação `DESC` quando suportado;
- preservação de charset `utf8mb4` e collation `utf8mb4_0900_ai_ci`;
- relatórios de dry-run com storage, tabelas e índices que seriam criados.

### Comando esperado

O fluxo técnico esperado deve permitir algo equivalente a:

```text
php migrate.php --storage=SAAS --module=CTR
```

Se o projeto optar por outro nome de argumento, documentar a decisão na própria implementação e manter a separação entre migrations `CAS` e `CTR`.

## Regras transacionais

- Criação de contrato e itens deve ocorrer em transação sempre que o storage/model permitir.
- Ativação de contrato e supersessão de contrato anterior devem ser atômicas.
- Consumo de franquia deve travar ou validar concorrência do item antes de inserir no ledger.
- O saldo não deve ser calculado apenas por leitura eventual sem proteção de concorrência.
- `CurrentBalance` deve refletir o saldo após o evento inserido.
- Estorno deve ser novo registro no ledger, não atualização do consumo original.
- Rotinas administrativas não devem executar `UPDATE` ou `DELETE` em `CTRContractItemConsumption` fora de migração corretiva documentada.

## Validação planejada

### Migration

- Migration com `--storage=SAAS` cria somente as tabelas necessárias do ContractFlow e a réplica reduzida de `CasRps`.
- Migration não altera a base `Default`.
- Todas as foreign keys são criadas com sucesso.
- `CTRContractItem.LineTotal` é coluna calculada armazenada.
- `CHK_CTRBalance_Not_Negative` impede saldo negativo.
- Índices do ADR 003 existem após a migration.
- Reexecutar a migration não falha nem recria objetos existentes indevidamente.

### Models e metadata

- Cada tabela do ContractFlow possui metadata e model correspondente em `CTR`.
- Campos obrigatórios, tipos, chaves primárias e foreign keys refletem o ADR 003.
- Models usam o storage `SAAS` quando operam no módulo.
- Tabelas de detalhe são acessadas por relacionamento 1:1.
- Campos monetários aceitam somente valores inteiros em centavos.

### Instalação por tenant

- Instalação rejeita `RepositoryId` inexistente em `SAAS.CasRps`.
- Instalação localiza pacote pelo hash correto.
- Hash inválido não revela caminhos internos sensíveis.
- Pacote de outro produto ou versão incompatível é recusado.
- Primeira instalação aplica dados predefinidos e checkpoints.
- Segunda instalação com o mesmo hash não duplica registros.
- Instalação parcial compatível pode ser completada.
- Instalação parcial conflitante retorna erro estruturado.

### Regras de contrato

- Criação de contrato `Draft` grava dados quentes em `CTRContract` e payload pesado em `CTRContractDetail` somente quando informado.
- Ativação respeita `CTRConcurrencyGroup.AllowMultipleActive`.
- Upgrade ou downgrade supersede contrato anterior quando múltiplos ativos não são permitidos.
- Item billable calcula `LineTotal` em centavos.
- Item não billable mantém `LineTotal = 0`.
- Consumo reduz saldo e grava evento append-only.
- Estorno grava novo evento com quantidade negativa.
- Tentativa de saldo negativo falha no backend e no banco.
- Registro de signatário grava aceite sem depender de interface visual.

## Critérios de aceite

- Existe uma task implementável para backend do ContractFlow sem incluir interface visual nem APIs.
- Migrations do módulo podem ser executadas contra `SAAS`.
- A réplica reduzida de `CasRps` existe em `SAAS`.
- As tabelas do ADR 003 são criadas com prefixo `CTR`, `RepositoryId`, constraints e índices definidos.
- Models e metadata `CTR` estão disponíveis para todas as tabelas do módulo.
- Classes de domínio em `App\Class\Contract` expõem funções reutilizáveis por frontend e APIs futuras.
- O fluxo de instalação por tenant recebe `RepositoryId` e hash, localiza o pacote estático e aplica dados predefinidos de forma idempotente.
- O pacote estático do ContractFlow fica separado em `App\Static\Rules\Contract`.
- O backend não depende de `AuthSession`, HTML ou controller visual para executar instalação e regras de domínio.
- Testes ou validações comprovam migration, instalação, idempotência, ledger append-only e regras básicas de concorrência.

## Arquivos previstos para alteração na implementação

- `migrate.php`, se necessário para suportar `--module=CTR` ou filtro equivalente.
- `App\Core\SimpleMigrator.php`, se necessário para metadata por módulo, generated columns, checks e índices avançados.
- `App\Metadata\CTR\CasRpsMD.php` ou metadata equivalente para réplica reduzida.
- `App\Metadata\CTR\CTRConcurrencyGroupMD.php`.
- `App\Metadata\CTR\CTRContractMD.php`.
- `App\Metadata\CTR\CTRContractDetailMD.php`.
- `App\Metadata\CTR\CTRContractItemMD.php`.
- `App\Metadata\CTR\CTRContractItemDetailMD.php`.
- `App\Metadata\CTR\CTRContractItemConsumptionMD.php`.
- `App\Metadata\CTR\CTRContractSignatoryMD.php`.
- `App\Models\CTR\CasRpsModel.php` ou model equivalente para réplica reduzida.
- `App\Models\CTR\CTRConcurrencyGroupModel.php`.
- `App\Models\CTR\CTRContractModel.php`.
- `App\Models\CTR\CTRContractDetailModel.php`.
- `App\Models\CTR\CTRContractItemModel.php`.
- `App\Models\CTR\CTRContractItemDetailModel.php`.
- `App\Models\CTR\CTRContractItemConsumptionModel.php`.
- `App\Models\CTR\CTRContractSignatoryModel.php`.
- `App\Class\Contract\TenantRepositorySync.php` ou nome equivalente.
- `App\Class\Contract\ContractService.php` ou nome equivalente.
- `App\Class\Contract\ContractItemService.php` ou nome equivalente.
- `App\Class\Contract\ContractLedgerService.php` ou nome equivalente.
- `App\Class\Contract\ContractSignatoryService.php` ou nome equivalente.
- `App\Class\Contract\ContractPackageInstaller.php` ou nome equivalente.
- `App\Class\Contract\ApplyContractSettings.php` ou nome equivalente.
- `App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json`.
- Arquivos de teste ou validação compatíveis com a estratégia do projeto.
