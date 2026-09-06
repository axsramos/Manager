# ADR 003 - Modelagem de Dados do ContractFlow

**Status:** Aprovado  
**Data:** 2026-09-06  
**RFC de referência:** [RFC 002 - ContractFlow: Gestão Dinâmica de Contratos e Franquias](../RFC/002-contractflow-gestao-dinamica-de-contratos-e-franquias.md)  
**ADR complementar:** [ADR 002 - Partição Vertical, Line-Item Pricing e Ledger do ContractFlow](002-particao-vertical-line-item-pricing-e-ledger-do-contractflow.md)

## Contexto

O ADR 002 definiu os pilares arquiteturais do ContractFlow: partição vertical entre dados quentes e frios, precificação por linha de item, ledger append-only para consumo e governança por grupo de concorrência.

Este ADR detalha a modelagem relacional decorrente dessa decisão, incluindo as tabelas, campos, relacionamentos, índices e premissas de armazenamento que devem orientar a implementação das migrations e dos metadados em `App\Metadata\CTR`.

O módulo deve persistir seus dados na base `SAAS`, mantendo isolamento operacional em relação à conexão `Default`. Para preservar a associação com repositórios sem carregar a estrutura administrativa completa da base principal, a base `SAAS` deve conter uma réplica reduzida de `CasRps`.

## Decisão

Adotar a modelagem de dados abaixo como baseline relacional do ContractFlow.

As migrations do módulo devem executar contra o storage `SAAS`. Tabelas operacionais devem possuir `RepositoryId` como vínculo lógico com `CasRpsCod`, permitindo segregação por repositório e facilitando migração entre bases.

Todas as novas tabelas físicas do módulo ContractFlow devem usar o prefixo `CTR`. Essa regra não se aplica às tabelas `CAS` existentes nem à réplica reduzida de `CasRps` criada em `SAAS`. A base `SAAS` poderá concentrar vários mini projetos; por isso, nomes genéricos como `Contract` devem ser evitados. O módulo Contract deve usar nomes específicos, como `CTRContract`. Um módulo futuro de locações, por exemplo, poderá usar outro prefixo, como `LCAContract`.

## Tabela de referência de repositórios

Criar na base `SAAS` uma réplica reduzida da tabela `CasRps`, contendo somente as chaves necessárias para relacionamento e identificação:

```sql
CREATE TABLE CasRps (
    CasRpsCod INT NOT NULL PRIMARY KEY,
    CasRpsDsc VARCHAR(255) NOT NULL
);
```

Essa tabela deve ser mantida como referência mínima do repositório. Ela não substitui a estrutura completa da conexão `Default` e não deve receber campos administrativos que pertençam ao Manager.

## Estrutura core e governança

`CTRConcurrencyGroup` agrupa contratos que competem entre si, como planos, locações ou pacotes mutuamente exclusivos dentro de um mesmo domínio.

```sql
CREATE TABLE CTRConcurrencyGroup (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    RepositoryId INT NOT NULL,
    Name VARCHAR(100) NOT NULL,
    AllowMultipleActive TINYINT(1) NOT NULL DEFAULT 0,

    CONSTRAINT FK_CTRConcurrencyGroup_Repository
        FOREIGN KEY (RepositoryId) REFERENCES CasRps(CasRpsCod)
);
```

`CTRContract` armazena dados transacionais compactos do contrato. A tabela representa dados quentes usados em validação de status, concorrência, vigência e linhagem de upgrade ou downgrade.

```sql
CREATE TABLE CTRContract (
    Id CHAR(36) PRIMARY KEY,
    RepositoryId INT NOT NULL,
    UserId INT NOT NULL,
    ParentContractId CHAR(36) NULL,
    ConcurrencyGroupId INT NOT NULL,
    Status VARCHAR(20) NOT NULL DEFAULT 'Draft',
    BillingCycle VARCHAR(20) NULL,
    StartDate DATETIME NULL,
    EndDate DATETIME NULL,
    ContractHash VARCHAR(256) NULL,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT FK_CTRContract_Repository
        FOREIGN KEY (RepositoryId) REFERENCES CasRps(CasRpsCod),
    CONSTRAINT FK_CTRContract_Parent
        FOREIGN KEY (ParentContractId) REFERENCES CTRContract(Id),
    CONSTRAINT FK_CTRContract_Group
        FOREIGN KEY (ConcurrencyGroupId) REFERENCES CTRConcurrencyGroup(Id)
);
```

`CTRContractDetail` armazena dados frios do contrato, como minuta renderizada, caminho de template e termos específicos.

```sql
CREATE TABLE CTRContractDetail (
    ContractId CHAR(36) PRIMARY KEY,
    TemplatePath VARCHAR(255) NULL,
    TemplateContent LONGTEXT NULL,
    CustomTerms TEXT NULL,

    CONSTRAINT FK_CTRContractDetail_Contract
        FOREIGN KEY (ContractId) REFERENCES CTRContract(Id) ON DELETE CASCADE
);
```

## Itens do contrato e precificação

`CTRContractItem` armazena dados quentes de itens contratados, incluindo métricas, preços em centavos, franquias, indicadores de apresentação e coluna calculada fisicamente.

```sql
CREATE TABLE CTRContractItem (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    RepositoryId INT NOT NULL,
    ContractId CHAR(36) NOT NULL,
    ServiceCode VARCHAR(50) NULL,
    Description VARCHAR(255) NOT NULL,
    CollaboratorReference VARCHAR(100) NULL,
    IsIncluded TINYINT(1) NOT NULL DEFAULT 1,
    DisplayOrder INT NOT NULL DEFAULT 0,
    ItemType VARCHAR(20) NOT NULL DEFAULT 'Base',
    ValidUntil DATETIME NULL,
    IsBillable TINYINT(1) NOT NULL DEFAULT 0,
    Quantity INT NULL,
    UnitPrice BIGINT NULL,
    DiscountValue BIGINT NULL,
    ReplacementValue BIGINT NULL DEFAULT 0,
    UsageLimit INT NULL,
    LineTotal BIGINT GENERATED ALWAYS AS (
        CASE
            WHEN IsBillable = 1 THEN (IFNULL(Quantity, 1) * IFNULL(UnitPrice, 0)) - IFNULL(DiscountValue, 0)
            ELSE 0
        END
    ) STORED,

    CONSTRAINT FK_CTRContractItem_Repository
        FOREIGN KEY (RepositoryId) REFERENCES CasRps(CasRpsCod),
    CONSTRAINT FK_CTRContractItem_Contract
        FOREIGN KEY (ContractId) REFERENCES CTRContract(Id) ON DELETE CASCADE
);
```

`CTRContractItemDetail` armazena atributos pesados ou variáveis do item, como JSON de variante e observações específicas.

```sql
CREATE TABLE CTRContractItemDetail (
    ContractItemId INT PRIMARY KEY,
    ItemAttributes JSON NULL,
    CustomNotes TEXT NULL,

    CONSTRAINT FK_CTRItemDetail_ContractItem
        FOREIGN KEY (ContractItemId) REFERENCES CTRContractItem(Id) ON DELETE CASCADE
);
```

## Consumo e aceite legal

`CTRContractItemConsumption` registra o extrato imutável de consumo de franquias. A tabela deve ser tratada pela aplicação como append-only.

```sql
CREATE TABLE CTRContractItemConsumption (
    Id BIGINT AUTO_INCREMENT PRIMARY KEY,
    RepositoryId INT NOT NULL,
    ContractItemId INT NOT NULL,
    PreviousBalance INT NOT NULL,
    ConsumedQuantity INT NOT NULL,
    CurrentBalance INT NOT NULL,
    ExternalReference VARCHAR(100) NULL,
    Description VARCHAR(255) NULL,
    ConsumedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT FK_CTRConsumption_Repository
        FOREIGN KEY (RepositoryId) REFERENCES CasRps(CasRpsCod),
    CONSTRAINT FK_CTRConsumption_ContractItem
        FOREIGN KEY (ContractItemId) REFERENCES CTRContractItem(Id),
    CONSTRAINT CHK_CTRBalance_Not_Negative
        CHECK (CurrentBalance >= 0)
);
```

`CTRContractSignatory` registra o aceite legal e os dados mínimos de auditoria associados ao contrato.

```sql
CREATE TABLE CTRContractSignatory (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    RepositoryId INT NOT NULL,
    ContractId CHAR(36) NOT NULL,
    UserId INT NOT NULL,
    Role VARCHAR(50) NOT NULL,
    SignedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    IpAddress VARCHAR(45) NOT NULL,
    DeviceFingerprint VARCHAR(255) NULL,

    CONSTRAINT FK_CTRSignatory_Repository
        FOREIGN KEY (RepositoryId) REFERENCES CasRps(CasRpsCod),
    CONSTRAINT FK_CTRSignatory_Contract
        FOREIGN KEY (ContractId) REFERENCES CTRContract(Id) ON DELETE CASCADE
);
```

## Índices

Os índices mínimos devem priorizar validação de contratos ativos, rastreabilidade de supersessão, busca de itens por código ou colaborador e renderização do histórico de consumo.

```sql
CREATE INDEX IX_CTRContract_User_Status_Group
ON CTRContract (RepositoryId, UserId, Status, ConcurrencyGroupId);

CREATE INDEX IX_CTRContract_Parent
ON CTRContract (ParentContractId);

CREATE INDEX IX_CTRContractItem_Search
ON CTRContractItem (RepositoryId, ContractId, ServiceCode, CollaboratorReference);

CREATE INDEX IX_CTRContractItem_Collaborator
ON CTRContractItem (RepositoryId, CollaboratorReference, ContractId);

CREATE INDEX IX_CTRConsumption_History
ON CTRContractItemConsumption (ContractItemId, ConsumedAt DESC);
```

## Regras de integridade

- Todas as novas tabelas do módulo ContractFlow devem usar prefixo `CTR`, exceto a réplica `CasRps` em `SAAS`.
- `CTRContract.Id` deve usar UUID v4 em `CHAR(36)`, permitindo geração descentralizada pela aplicação.
- Chaves de tabelas de alto volume devem usar `INT` ou `BIGINT` sequenciais.
- `RepositoryId` deve existir nas tabelas operacionais com dados próprios do módulo.
- Tabelas de detalhe 1:1 não precisam repetir `RepositoryId`, pois herdam o escopo pelo relacionamento com a tabela principal.
- Valores monetários devem ser persistidos em centavos usando `BIGINT`.
- `CTRContractItem.LineTotal` deve ser calculado pelo banco e armazenado fisicamente.
- `CTRContractItemConsumption` deve aceitar estornos por `ConsumedQuantity` negativo, desde que `CurrentBalance` permaneça maior ou igual a zero.
- Fluxos operacionais não devem atualizar nem excluir registros de `CTRContractItemConsumption`.
- Exclusão em cascata deve ficar restrita às tabelas dependentes de detalhe, itens e signatários conforme a relação de composição definida.

## Ambiente técnico

- Storage alvo: `SAAS`.
- SGBD: MySQL 8.0+.
- Engine: InnoDB.
- Charset: `utf8mb4`.
- Collation: `utf8mb4_0900_ai_ci`.
- Namespace de models: `App\Models\CTR`.
- Namespace de metadata: `App\Metadata\CTR`.
- Prefixo físico das novas tabelas do módulo ContractFlow: `CTR`.

## Consequências

### Positivas

- A modelagem separa dados de consulta frequente dos payloads pesados, mantendo tabelas transacionais compactas.
- O prefixo `CTR` evita colisão com tabelas genéricas ou tabelas de outros mini projetos na mesma base `SAAS`.
- `RepositoryId` permite segmentação por repositório e reduz o acoplamento com a conexão `Default`.
- A réplica reduzida de `CasRps` viabiliza migração para bases distintas sem carregar a estrutura administrativa completa.
- A precificação por linha facilita pacotes, addons, itens gratuitos, indenizações e franquias.
- O ledger append-only melhora rastreabilidade, auditoria e conciliação.

### Negativas e riscos

- A réplica de `CasRps` precisa de rotina confiável de sincronização a partir da origem autorizada.
- A aplicação precisa garantir consistência entre `RepositoryId` do contrato, grupo de concorrência, itens, consumo e signatários.
- Consultas completas de contrato exigem joins com tabelas de detalhe.
- O modelo append-only exige controles na camada de aplicação e nas rotinas administrativas para evitar manutenção destrutiva.

## Alternativas consideradas

### Manter `CasRps` somente na conexão `Default`

Rejeitada porque obrigaria o módulo a depender da estrutura administrativa da base principal e dificultaria migrações para bases independentes.

### Criar tabelas sem prefixo, como `Contract` e `ContractItem`

Rejeitada porque a base `SAAS` pode concentrar vários mini projetos. Nomes genéricos aumentam risco de colisão com módulos futuros que também precisem de conceitos contratuais.

### Repetir `RepositoryId` também nas tabelas de detalhe

Rejeitada para o baseline inicial porque `CTRContractDetail` e `CTRContractItemDetail` são extensões 1:1 das tabelas principais. O escopo por repositório deve ser resolvido pelo relacionamento com `CTRContract` ou `CTRContractItem`.

### Calcular totais apenas na aplicação

Rejeitada porque espalharia regra financeira em múltiplos fluxos e aumentaria risco de divergência entre telas, APIs e rotinas de processamento.
