> **Status: IMPLEMENTADO**

# Plano - interface visual SBAdmin do ContractFlow

## Objetivo

Implementar a interface visual do módulo ContractFlow para gestão de contratos, itens, consumo de franquias, signatários e instalação por tenant, seguindo o padrão já adotado no projeto em `App\Views\SBAdmin\Manager`.

Documentos de referência:

- `Doc\RFC\002-contractflow-gestao-dinamica-de-contratos-e-franquias.md`;
- `Doc\ADR\002-particao-vertical-line-item-pricing-e-ledger-do-contractflow.md`;
- `Doc\ADR\003-modelagem-de-dados-do-contractflow.md`;
- `Doc\tasks\contractflow-backend-modelagem-instalacao-saas.md`.

## Decisão de diretório

Usar `App\Views\SBAdmin\Contract` para as views do módulo.

Justificativa:

- A RFC 002 define `Contract` para camada de aplicação e interface.
- `CTR` deve permanecer reservado para tabelas físicas, metadata e models, como `App\Models\CTR` e `App\Metadata\CTR`.
- `App\Views\SBAdmin\CTRContract` misturaria o prefixo físico de banco com a responsabilidade visual da aplicação.
- A escolha mantém simetria com `App\Controllers\Contract` e com o padrão já usado por `Manager` e `Support`.

## Escopo

- Criar views SBAdmin em `App\Views\SBAdmin\Contract`.
- Criar controllers web em `App\Controllers\Contract` apenas para navegação visual e adaptação de request/session para os serviços backend já criados.
- Reutilizar classes de domínio existentes em `App\Class\Contract`.
- Reutilizar models e metadata de `App\Models\CTR` e `App\Metadata\CTR`.
- Implementar telas administrativas do ContractFlow sem criar endpoints em `App\Controllers\API\V1\Contract`.
- Respeitar o storage `SAAS` para dados do módulo.
- Respeitar o isolamento por `RepositoryId`.
- Usar os partials existentes do SBAdmin, como `Head.php`, `TopMenu.php`, `SideMenu.php`, `Breadcrumb.php`, `AlertMessage.php`, `Tabs.php`, `FormButtonControls.php` e `BodyScripts.php`.
- Usar estrutura semelhante às views de `App\Views\SBAdmin\Manager`, com arquivo base, lista e formulário por entidade quando aplicável.

## Fora do escopo

- Criar APIs REST ou controllers em `App\Controllers\API\V1\Contract`.
- Alterar a modelagem de dados definida no ADR 003.
- Alterar regras transacionais do backend já implementado.
- Criar integrações com WhatsApp, QR Code, apps mobile, parceiros externos, gateway de pagamento ou assinatura digital externa.
- Criar build versionado, changelog ou publicação de versão.
- Criar redesign global do SBAdmin.

## Estado atual relevante

- O backend do ContractFlow já possui metadata, models, migrations e services.
- A migration do módulo deve ser executada contra `SAAS` com `php migrate.php --storage=SAAS --module=CTR`.
- O pacote inicial do ContractFlow está em `App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json`.
- A instalação por tenant é feita por hash e usa `CTRPackageCheckpoint`.
- O padrão visual do Manager usa uma view base por funcionalidade e arquivos complementares de lista/formulário, por exemplo:
  - `RepositorioView.php`;
  - `RepositorioViewList.php`;
  - `RepositorioViewForm.php`.
- As views do Manager recebem dados por `$data['FormData']` e metadados de tela por `$data['FormDesign']`.

## Estado final esperado

A interface visual deve permitir:

1. selecionar ou validar o tenant/repositório operacional;
2. visualizar painel resumido do ContractFlow;
3. listar contratos por tenant, status, usuário, grupo de concorrência e vigência;
4. criar contrato em modo `Draft`;
5. consultar detalhes quentes e frios de um contrato;
6. editar dados permitidos enquanto o contrato estiver em `Draft`;
7. ativar contrato respeitando regras de concorrência;
8. cancelar ou expirar contrato por ação administrativa permitida;
9. listar e manter itens de contrato;
10. visualizar `LineTotal` calculado pelo banco sem recalcular na view;
11. registrar consumo ou estorno de franquia por ação visual controlada;
12. visualizar extrato append-only de consumo;
13. registrar e consultar signatários;
14. executar instalação do ContractFlow por hash para um `RepositoryId`;
15. visualizar checkpoints de instalação por tenant.

## Arquitetura visual

### Controllers

Criar controllers em `App\Controllers\Contract` como adaptadores finos entre HTTP/session e serviços de domínio:

- `Dashboard.php`;
- `Install.php`;
- `Contract.php`;
- `ContractItem.php`;
- `ContractLedger.php`;
- `ContractSignatory.php`;
- `ConcurrencyGroup.php`.

Responsabilidades dos controllers:

- Resolver `RepositoryId` ativo a partir da sessão, rota, parâmetro ou seleção do usuário.
- Validar permissões usando o mecanismo existente do Manager.
- Chamar services em `App\Class\Contract`.
- Montar `$data['FormData']`, `$data['FormDesign']`, tabs, breadcrumbs e mensagens.
- Renderizar views em `App\Views\SBAdmin\Contract`.
- Não conter regra financeira, regra de saldo ou regra de supersessão.

### Views

Criar views em `App\Views\SBAdmin\Contract`.

Arquivos base previstos:

- `DashboardView.php`;
- `InstallView.php`;
- `InstallViewForm.php`;
- `InstallViewCheckpointList.php`;
- `ContractView.php`;
- `ContractViewList.php`;
- `ContractViewForm.php`;
- `ContractViewDetail.php`;
- `ContractItemView.php`;
- `ContractItemViewList.php`;
- `ContractItemViewForm.php`;
- `ContractLedgerView.php`;
- `ContractLedgerViewList.php`;
- `ContractLedgerViewForm.php`;
- `ContractSignatoryView.php`;
- `ContractSignatoryViewList.php`;
- `ConcurrencyGroupView.php`;
- `ConcurrencyGroupViewList.php`;
- `ConcurrencyGroupViewForm.php`.

O arquivo base de cada funcionalidade deve seguir a composição usada pelo Manager:

- incluir partial `Head.php`;
- incluir `TopMenu.php`;
- incluir `SideMenu.php`;
- renderizar `Breadcrumb.php`;
- renderizar `AlertMessage.php`;
- usar `Tabs.php` quando houver lista/formulário/detalhes;
- carregar conteúdo por `FormDesign['Tabs']['LoadFile']`;
- incluir `Footer.php`;
- incluir `BodyScripts.php`.

## Telas planejadas

### Dashboard

Tela inicial operacional do ContractFlow.

Conteúdo mínimo:

- total de contratos ativos do tenant;
- total de contratos em `Draft`;
- contratos próximos do vencimento;
- itens com franquia ativa;
- consumos recentes;
- instalações ou checkpoints recentes;
- atalhos para contratos, grupos, instalação e extrato.

### Instalação do módulo

Tela administrativa para ativar o ContractFlow em um tenant.

Campos e ações:

- `RepositoryId`;
- descrição do repositório quando encontrada em `SAAS.CasRps`;
- hash do pacote;
- botão para validar pacote;
- botão para aplicar pacote;
- resumo do pacote encontrado: `ProductKey`, `ProductName`, `Version`, `Description`;
- lista de checkpoints em `CTRPackageCheckpoint`.

Regras:

- não aceitar hash como caminho de arquivo;
- exibir erro amigável quando o pacote não existir;
- exibir estado `applied`, `existing`, `skipped`, `warning` ou `failed`;
- permitir reexecução idempotente sem duplicar dados.

### Contratos

Tela principal para ciclo de vida de contratos.

Lista:

- `Id`;
- `RepositoryId`;
- `UserId`;
- `Status`;
- `BillingCycle`;
- `StartDate`;
- `EndDate`;
- `ConcurrencyGroupId`;
- `CreatedAt`;
- indicador de contrato pai quando existir.

Filtros:

- tenant/repositório;
- status;
- usuário;
- grupo de concorrência;
- vigência;
- contrato pai.

Formulário:

- dados quentes de `CTRContract`;
- dados frios de `CTRContractDetail`, carregados sob demanda;
- ações condicionais por status.

Ações:

- criar `Draft`;
- salvar alterações permitidas em `Draft`;
- ativar;
- cancelar;
- expirar;
- abrir itens;
- abrir extrato;
- abrir signatários.

### Itens do contrato

Tela para manutenção de `CTRContractItem` e `CTRContractItemDetail`.

Lista:

- descrição;
- código de serviço;
- referência do colaborador;
- tipo do item;
- quantidade;
- preço unitário em centavos formatado como moeda;
- desconto formatado;
- `LineTotal` somente leitura;
- limite de uso;
- validade;
- item incluso ou addon.

Formulário:

- dados quentes do item;
- JSON de atributos em campo controlado para `CTRContractItemDetail.ItemAttributes`;
- observações em `CustomNotes`.

Regras:

- não recalcular `LineTotal` no PHP da view;
- exibir `LineTotal` retornado do banco;
- proteger campos conforme status do contrato;
- permitir itens não faturáveis com `LineTotal = 0`.

### Ledger de consumo

Tela para visualizar e registrar consumo de franquia.

Lista:

- item;
- saldo anterior;
- quantidade consumida ou estornada;
- saldo atual;
- referência externa;
- descrição;
- data do consumo.

Formulário de ação:

- selecionar item com `UsageLimit`;
- informar quantidade;
- informar referência externa;
- informar descrição;
- ação de consumo;
- ação de estorno.

Regras:

- registros existentes não podem ser editados ou removidos pela interface;
- estorno deve criar novo registro com quantidade negativa;
- tentativa de saldo negativo deve apresentar erro retornado pelo backend;
- lista deve ordenar por `ConsumedAt DESC`.

### Signatários

Tela para consulta e registro de aceite legal.

Lista:

- contrato;
- usuário;
- papel;
- data de assinatura;
- IP;
- fingerprint do dispositivo.

Formulário:

- usuário;
- papel;
- IP;
- fingerprint;
- hash do contrato quando aplicável.

Regras:

- não implementar assinatura digital externa nesta task;
- registrar aceite mínimo usando `ContractSignatoryService`;
- exibir histórico sem permitir exclusão visual.

### Grupos de concorrência

Tela para parametrizar `CTRConcurrencyGroup`.

Lista:

- tenant;
- nome;
- permite múltiplos ativos.

Formulário:

- `Name`;
- `AllowMultipleActive`.

Regras:

- mudanças devem afetar somente contratos futuros ou transições futuras;
- não recalcular histórico de contratos já supersedidos;
- alertar quando alteração puder impactar ativação de contratos.

## FormDesign e metadados de tela

Cada controller deve montar `FormDesign` de forma compatível com o padrão atual:

- `FormDesign['Fields']` com `ShortLabel`, `LongLabel`, `TextPlaceholder` e `TextHelp`;
- `FormDesign['Hidden']` para campos técnicos;
- `FormDesign['Tabs']` com itens e `LoadFile`;
- `FormDesign['TransMode']` para `Insert`, `Update`, `Delete`, `View` ou equivalente;
- `FormDesign['FormDisable']` para telas somente leitura;
- links de ação compatíveis com o roteamento existente.

Sempre que possível, criar helpers privados ou classes auxiliares para reduzir duplicação de `FormDesign` entre controllers do módulo.

## Regras de UX

- Seguir o estilo visual atual do SBAdmin usado no Manager.
- Usar tabelas responsivas para listas.
- Usar cards somente como contêineres já previstos pelo padrão SBAdmin.
- Usar badges para status de contrato: `Draft`, `Active`, `Superseded`, `Expired`, `Canceled`.
- Usar badges ou ícones para indicar item incluso, addon, faturável e vencido.
- Usar campos monetários formatados para exibição, mantendo centavos inteiros no backend.
- Exibir dados frios somente em abas ou seções de detalhe, evitando carregar minutas grandes em listas.
- Exibir mensagens de erro e sucesso pelo partial existente `AlertMessage.php`.
- Não inserir textos longos explicando funcionalidades dentro da interface.

## Regras de segurança e consistência

- Toda ação deve validar `RepositoryId`.
- Nenhuma tela pode consultar ou alterar dados de outro tenant.
- Controllers devem validar permissões antes de executar ações sensíveis.
- A interface não deve executar `UPDATE` ou `DELETE` em `CTRContractItemConsumption`.
- Ativação de contrato deve usar `ContractService`, preservando transação e supersessão.
- Consumo e estorno devem usar `ContractLedgerService`, preservando o ledger append-only.
- Instalação por hash deve usar `ContractPackageInstaller`.
- Views devem escapar saídas dinâmicas para reduzir risco de XSS.
- Campos `LONGTEXT` e `JSON` devem ser tratados com cuidado para não quebrar layout nem carregar payload pesado sem necessidade.

## Integração com menu e permissões

Planejar inclusão do módulo ContractFlow no menu administrativo, respeitando o mecanismo já usado por Manager.

Itens sugeridos:

- ContractFlow;
- Dashboard;
- Instalação;
- Contratos;
- Grupos de concorrência;
- Extrato de consumo;
- Signatários.

Permissões sugeridas:

- visualizar dashboard;
- instalar módulo por tenant;
- visualizar contratos;
- criar contratos;
- alterar contratos em draft;
- ativar contratos;
- cancelar contratos;
- manter itens;
- registrar consumo;
- registrar estorno;
- visualizar ledger;
- registrar signatários;
- parametrizar grupos de concorrência.

Se o projeto exigir carga por pacote estático para menus e permissões, criar um pacote ContractFlow complementar ou ampliar o pacote existente sem misturar regras de Manager.

## Validação planejada

### Estrutura

- Diretório `App\Views\SBAdmin\Contract` criado.
- Controllers em `App\Controllers\Contract` criados apenas para interface visual.
- Nenhum endpoint em `App\Controllers\API\V1\Contract` criado nesta task.
- Views seguem o padrão de composição do SBAdmin usado pelo Manager.

### Navegação

- Dashboard renderiza sem erro.
- Listas carregam com tenant válido.
- Formulários de contrato, item, ledger, signatário, instalação e grupo renderizam com dados vazios e dados existentes.
- Tabs carregam o arquivo correto em `FormDesign['Tabs']['LoadFile']`.
- Breadcrumb e mensagens usam partials existentes.

### Regras funcionais

- Criar contrato `Draft` pela tela chama o backend correto.
- Ativar contrato aplica regra de concorrência.
- Item faturável exibe `LineTotal` gerado pelo banco.
- Consumo grava novo evento no ledger.
- Estorno grava novo evento negativo.
- Ledger não oferece ação visual de editar ou excluir registros.
- Instalação por hash aplica ou reaproveita checkpoints sem duplicação.

### Isolamento SaaS

- Todas as consultas do módulo usam `SAAS`.
- Toda listagem filtra por `RepositoryId`.
- A interface recusa operações sem tenant/repositório válido.
- Dados de outro tenant não aparecem em listas ou detalhes.

### Qualidade

- `php -l` sem erros nos controllers e views PHP criados.
- Teste manual mínimo das rotas principais.
- Quando houver harness de teste no projeto, criar validações automatizadas para controllers ou services adaptados.

## Critérios de aceite

- Existe interface visual SBAdmin para o módulo ContractFlow em `App\Views\SBAdmin\Contract`.
- A escolha de `Contract` para views está documentada e justificada.
- Controllers web ficam em `App\Controllers\Contract`.
- A interface permite gerenciar contratos, itens, ledger, signatários, grupos de concorrência e instalação por hash.
- A interface usa services backend já criados em `App\Class\Contract`.
- O ledger é visualmente append-only.
- As telas respeitam `RepositoryId` e storage `SAAS`.
- Nenhuma API é criada nesta task.
- O padrão visual e estrutural segue as referências em `App\Views\SBAdmin\Manager`.

## Implementação realizada

- Views criadas em `App\Views\SBAdmin\Contract`, mantendo `CTR` restrito aos artefatos físicos do módulo.
- Controllers web criados em `App\Controllers\Contract` para dashboard, instalação, contratos, itens, ledger, signatários e grupos de concorrência.
- `ContractReadService` centraliza consultas de leitura do storage `SAAS`; as operações de escrita continuam nos services do domínio.
- Foram incluídos `ContractService::updateDraft`, `ContractItemService::updateItem` e `ConcurrencyGroupService` para evitar SQL de negócio nos controllers.
- O ledger possui somente listagem, consumo e estorno. Não há fluxo visual para alteração ou exclusão de lançamentos.
- As permissões dos programas `ContractDashboard`, `ContractInstall`, `Contract`, `ContractItem`, `ContractLedger`, `ContractSignatory` e `ContractConcurrencyGroup` devem ser cadastradas no mecanismo CAS do repositório antes da disponibilização a usuários finais. Essa configuração pertence à conexão `Default`; os dados CTR continuam isolados na conexão `SAAS`.

## Arquivos previstos para criação ou alteração

- `App\Controllers\Contract\Dashboard.php`.
- `App\Controllers\Contract\Install.php`.
- `App\Controllers\Contract\Contract.php`.
- `App\Controllers\Contract\ContractItem.php`.
- `App\Controllers\Contract\ContractLedger.php`.
- `App\Controllers\Contract\ContractSignatory.php`.
- `App\Controllers\Contract\ConcurrencyGroup.php`.
- `App\Views\SBAdmin\Contract\DashboardView.php`.
- `App\Views\SBAdmin\Contract\InstallView.php`.
- `App\Views\SBAdmin\Contract\InstallViewForm.php`.
- `App\Views\SBAdmin\Contract\InstallViewCheckpointList.php`.
- `App\Views\SBAdmin\Contract\ContractView.php`.
- `App\Views\SBAdmin\Contract\ContractViewList.php`.
- `App\Views\SBAdmin\Contract\ContractViewForm.php`.
- `App\Views\SBAdmin\Contract\ContractViewDetail.php`.
- `App\Views\SBAdmin\Contract\ContractItemView.php`.
- `App\Views\SBAdmin\Contract\ContractItemViewList.php`.
- `App\Views\SBAdmin\Contract\ContractItemViewForm.php`.
- `App\Views\SBAdmin\Contract\ContractLedgerView.php`.
- `App\Views\SBAdmin\Contract\ContractLedgerViewList.php`.
- `App\Views\SBAdmin\Contract\ContractLedgerViewForm.php`.
- `App\Views\SBAdmin\Contract\ContractSignatoryView.php`.
- `App\Views\SBAdmin\Contract\ContractSignatoryViewList.php`.
- `App\Views\SBAdmin\Contract\ConcurrencyGroupView.php`.
- `App\Views\SBAdmin\Contract\ConcurrencyGroupViewList.php`.
- `App\Views\SBAdmin\Contract\ConcurrencyGroupViewForm.php`.
- Arquivos de rota, menu, permissão ou pacote estático, se o padrão do projeto exigir registro explícito.
