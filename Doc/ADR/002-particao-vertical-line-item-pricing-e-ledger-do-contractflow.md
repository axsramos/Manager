# ADR 002 - Partição Vertical, Line-Item Pricing e Ledger do ContractFlow

**Status:** Aprovado  
**Data:** 2026-09-06  
**RFC de referência:** [RFC 002 - ContractFlow: Gestão Dinâmica de Contratos e Franquias](../RFC/002-contractflow-gestao-dinamica-de-contratos-e-franquias.md)

## Contexto

O ContractFlow precisa atender requisições de baixa latência em canais interativos, como chatbots de WhatsApp, apps mobile e terminais de validação, mantendo consistência transacional estrita para contratos, itens, saldos e aceites.

As consultas de alta frequência tratam de verificação de status, validação de concorrência, elegibilidade de consumo e atualização de saldos. Em contrapartida, dados volumosos, como minutas contratuais em HTML, termos em texto longo e atributos JSON de produtos ou variantes, têm baixa taxa de leitura e escrita.

Estruturar essas informações em tabelas monolíticas aumentaria o tamanho das páginas consultadas com maior frequência, causando pressão desnecessária no buffer pool do MySQL e degradação de I/O em cenários concorrentes.

## Decisão

Adotar uma arquitetura relacional no MySQL 8.0+ baseada em quatro decisões principais.

### Partição vertical entre dados quentes e frios

As tabelas de dados quentes devem conter atributos compactos, usados em validações, filtros e transações frequentes:

- `CTRContract`
- `CTRContractItem`
- `CTRContractItemConsumption`
- `CTRContractSignatory`
- `CTRConcurrencyGroup`

As tabelas de dados frios devem armazenar payloads pesados e ser consultadas apenas sob demanda:

- `CTRContractDetail`
- `CTRContractItemDetail`

`CTRContractDetail` deve manter relação 1:1 com `CTRContract`. `CTRContractItemDetail` deve manter relação 1:1 com `CTRContractItem`. Relações de extensão podem usar `ON DELETE CASCADE` porque dependem integralmente do registro principal.

### Precificação por item e valores monetários inteiros

O contrato deve ser precificado por linha de item. Valores monetários devem ser armazenados em centavos usando `BIGINT`, evitando erro de arredondamento por ponto flutuante.

`CTRContractItem.LineTotal` deve ser uma coluna calculada fisicamente com `GENERATED ALWAYS AS (...) STORED`, gravando no disco o resultado consolidado da linha no momento de escrita.

### Ledger append-only para consumo

`CTRContractItemConsumption` deve operar como histórico imutável de consumo de franquias, exclusivamente por `INSERT`. A tabela deve registrar saldo anterior, quantidade consumida ou estornada, saldo atual, referência externa, descrição e data do consumo.

A consistência mínima do saldo deve ser protegida no banco por `CHECK (CurrentBalance >= 0)`. A camada de aplicação não deve executar `UPDATE` ou `DELETE` nessa tabela em fluxos operacionais.

### Governança por grupo de concorrência

`CTRConcurrencyGroup` deve agrupar contratos que competem entre si dentro de um mesmo domínio comercial. Quando `AllowMultipleActive = 0`, a aplicação deve tratar transições de upgrade e downgrade garantindo a supersessão de contratos anteriores.

O ciclo esperado de status é:

- `Draft`
- `Active`
- `Superseded`
- `Expired`
- `Canceled`

## Organização do módulo

O ContractFlow deve seguir a partição vertical já usada por aplicações como Manager e Support.

- Controllers web em `App\Controllers\Contract`.
- Views baseadas no SBAdmin em `App\Views\SBAdmin\Contract`.
- Models do domínio em `App\Models\CTR`.
- Metadata do domínio em `App\Metadata\CTR`.
- Controllers de API em `App\Controllers\API\V1\Contract`.
- Regras estáticas em `App\Static\Rules\Contract`.

## Modelagem de dados

A migration do ContractFlow deve apontar para o storage `SAAS`, não para `Default`.

As tabelas operacionais do módulo devem possuir `RepositoryId`, relacionado à chave `CasRpsCod`. Esse vínculo identifica o repositório de origem ou propriedade dos dados sem depender da carga completa das estruturas pertencentes à conexão `Default`.

Todas as novas tabelas físicas do módulo ContractFlow devem usar o prefixo `CTR`. A regra não se aplica às tabelas `CAS` já existentes nem à réplica reduzida de `CasRps` criada em `SAAS`. O objetivo é evitar colisão com tabelas genéricas na mesma base, como `Contract`, permitindo que outros mini projetos usem prefixos próprios no futuro, por exemplo `LCAContract` para contratos de locação.

Na base `SAAS`, deve existir uma réplica reduzida de `CasRps` contendo somente:

- `CasRpsCod`
- `CasRpsDsc`

Essa réplica tem finalidade de referência e migração entre bases distintas. Ela não deve carregar toda a estrutura administrativa da conexão `Default`.

### Tabelas principais

`CTRConcurrencyGroup` deve conter:

- `Id`
- `RepositoryId`
- `Name`
- `AllowMultipleActive`

`CTRContract` deve conter:

- `Id`
- `RepositoryId`
- `UserId`
- `ParentContractId`
- `ConcurrencyGroupId`
- `Status`
- `BillingCycle`
- `StartDate`
- `EndDate`
- `ContractHash`
- `CreatedAt`

`CTRContractDetail` deve conter:

- `ContractId`
- `TemplatePath`
- `TemplateContent`
- `CustomTerms`

`CTRContractItem` deve conter:

- `Id`
- `RepositoryId`
- `ContractId`
- `ServiceCode`
- `Description`
- `CollaboratorReference`
- `IsIncluded`
- `DisplayOrder`
- `ItemType`
- `ValidUntil`
- `IsBillable`
- `Quantity`
- `UnitPrice`
- `DiscountValue`
- `ReplacementValue`
- `UsageLimit`
- `LineTotal`

`CTRContractItemDetail` deve conter:

- `ContractItemId`
- `ItemAttributes`
- `CustomNotes`

`CTRContractItemConsumption` deve conter:

- `Id`
- `RepositoryId`
- `ContractItemId`
- `PreviousBalance`
- `ConsumedQuantity`
- `CurrentBalance`
- `ExternalReference`
- `Description`
- `ConsumedAt`

`CTRContractSignatory` deve conter:

- `Id`
- `RepositoryId`
- `ContractId`
- `UserId`
- `Role`
- `SignedAt`
- `IpAddress`
- `DeviceFingerprint`

### Restrições e índices

As foreign keys internas devem preservar integridade entre contrato, detalhe, itens, consumo e signatários.

Os índices mínimos de alta frequência são:

- `IX_CTRContract_User_Status_Group` em `CTRContract (RepositoryId, UserId, Status, ConcurrencyGroupId)`.
- `IX_CTRContract_Parent` em `CTRContract (ParentContractId)`.
- `IX_CTRContractItem_Search` em `CTRContractItem (RepositoryId, ContractId, ServiceCode, CollaboratorReference)`.
- `IX_CTRContractItem_Collaborator` em `CTRContractItem (RepositoryId, CollaboratorReference, ContractId)`.
- `IX_CTRConsumption_History` em `CTRContractItemConsumption (ContractItemId, ConsumedAt DESC)`.

## Ambiente técnico

- SGBD: MySQL 8.0+.
- Engine: InnoDB.
- Charset: `utf8mb4`.
- Collation: `utf8mb4_0900_ai_ci`.
- Storage alvo da migration: `SAAS`.
- Prefixo das tabelas do módulo ContractFlow: `CTR`, exceto `CasRps` em `SAAS`.
- Chaves de contrato: UUID v4 em `CHAR(36)`.
- Chaves de itens e transações de alto volume: `INT` ou `BIGINT` sequenciais.
- Integridade: uso estrito de foreign keys, com `CASCADE` restrito às tabelas de extensão e dependência direta.

## Consequências

### Positivas

- Consultas de validação trabalham sobre tabelas compactas, reduzindo I/O e pressão de memória.
- A precificação por item permite pacotes, addons, gratuidades condicionais e indenizações em um mesmo contrato.
- O armazenamento monetário em centavos melhora a precisão financeira.
- O ledger append-only aumenta rastreabilidade, auditoria e conciliação de repasses.
- `RepositoryId` prepara o módulo para operação e migração entre bases distintas.
- A réplica reduzida de `CasRps` evita dependência estrutural completa da base `Default`.

### Negativas e riscos

- Telas ou APIs que precisam de documentos completos exigem joins explícitos nas tabelas de detalhe.
- O padrão append-only exige disciplina da camada de aplicação e revisão cuidadosa de rotinas administrativas.
- A réplica reduzida de `CasRps` exige sincronização controlada das chaves e descrições necessárias ao módulo.
- Regras de supersessão por concorrência precisam ser transacionais para evitar duplicidade de contratos ativos.

## Alternativas consideradas

### Banco de dados orientado a documentos

Rejeitado pela necessidade de consistência ACID forte em cálculos financeiros, consumo de franquias e transições concorrentes de contrato.

### Modelo relacional monolítico

Rejeitado porque misturaria campos de validação frequente com payloads pesados, aumentando o churn do buffer pool e comprometendo o tempo de resposta sob carga.

### Reutilização direta da estrutura completa de `CasRps` em `Default`

Rejeitada porque acoplaria o módulo à estrutura administrativa da base `Default`, dificultando migração, replicação e operação em bases distintas.
