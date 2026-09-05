> **Status: IMPLEMENTADO**

# Plano — carga inicial do sistema por linha de comando

## Objetivo

Disponibilizar um comando manual, executado somente depois da configuração do ambiente e da migration, para criar os dados mínimos de funcionamento do Manager: aplicativo, contas `admin` e `support`, repositórios físicos e lógicos, tipos de usuário e respectivos relacionamentos.

O fluxo técnico documentado deverá ser, obrigatoriamente, nesta ordem:

1. executar `php configure.php --env=<ambiente>`;
2. revisar o `.env`, criar a base de dados e validar que suas credenciais permitem conexão;
3. executar `php migrate.php --storage=Default`;
4. executar o comando de carga inicial `php seed.php --storage=Default`.

Cada etapa continuará manual e independente. O comando de carga não deverá executar `configure.php` nem `migrate.php` implicitamente.

## Escopo

- Criar os registros iniciais em `CasApp`, `CasUsr`, `CasRps`, `CasTus`, `CasRpa` e `CasRpu`.
- Garantir em `CasTus` o domínio de tipos de usuário predefinidos, sem impedir o cadastro de tipos adicionais e dinâmicos.
- Solicitar interativamente os dados e as credenciais de admin e support, com confirmação das duas senhas e sem exibi-las no terminal ou em logs.
- Criar a conta de suporte usando `CreateUserRepositoryAccount` e vinculá-la ao repositório admin por `CreateUserSupportClass`, sem manter IDs ou credenciais fixos dentro das classes.
- Sobrescrever `SupportUser.json` com os dados persistidos da conta support.
- Criar um repositório próprio para `admin` e outro para `support`, incluindo os diretórios correspondentes em `Repositories/<CasRpsCod>`.
- Vincular o aplicativo Manager aos repositórios iniciais.
- Preservar a regra de criação de contas futuras: o proprietário usa `MANAGER ACCOUNT` no repositório próprio, enquanto o suporte usa `SUPPORT ACCOUNT` nos repositórios dos demais usuários.
- Atualizar a seção técnica do `README.md` com os comandos, pré-requisitos, ordem e resultados esperados.
- Prever validações automatizadas ou repetíveis do fluxo e de sua reexecução.
- Ajustar o `ProductKey` do pacote Manager para `APP_KEY` e executar `ApplyApplicationSettings` como única etapa tolerante a falha.

## Fora do escopo

- Executar automaticamente configuração, migration ou carga durante uma requisição web.
- Criar ou alterar a base de dados informada no `.env`.
- Reestruturar todo o mecanismo de autenticação ou substituir o algoritmo de senha da aplicação neste trabalho.
- Popular tabelas além das necessárias para a carga descrita, salvo dependência comprovada durante a implementação.
- Alterar neste trabalho as rotinas genéricas de exclusão de `CasTus`; a carga inicial não removerá dados, e a não remoção dos quatro valores predefinidos fica documentada como regra obrigatória para essas rotinas.

## Estado atual relevante

- `configure.php` gera o `.env`, inclusive `APP_KEY` e `APP_TOKEN`; `APP_VERSION` já aparece no `.env` atual, mas ainda não é declarada por `EnvironmentVars` nem exposta por `Config`.
- `migrate.php` instancia `SimpleMigrator`, testa a conexão ao abrir o PDO e cria o esquema CAS a partir dos metadados.
- `RegisterClass` usa o hash MD5 do e-mail normalizado como ID do usuário e do repositório; o login depende desse mesmo cálculo para localizar o repositório principal.
- `CreateUserRepositoryAccount` contém IDs base64 fixos para `USER ACCOUNT` e `ADMIN ACCOUNT`, cria ambos os tipos e associa o proprietário ao último tipo instanciado.
- `CreateUserSupportClass` contém um ID base64 fixo para `SUPPORT ACCOUNT` e também aceita um `CasUsrCod` fixo vindo de `SupportUser.json`.
- A criação do diretório físico ocorre hoje em `RegisterClass`, depois das gravações no banco, portanto não cobre uma carga feita diretamente por CLI.
- `Config::getTypeUsersAdminLocal()` e comentários de controle de menu ainda reconhecem `ADMIN ACCOUNT`; esses consumidores precisam acompanhar a nomenclatura final.
- `Config::getTypeUsersAdminPortal()` retorna atualmente `ADMINISTRATOR PORTAL`, não existindo ainda um método equivalente para `USER ACCOUNT`.
- `SupportUser.json` é decodificado como array, mas `CreateUserSupportClass::createAccount()` tipa o item como `object`; a refatoração deverá uniformizar esse contrato.
- `CasTusModel` não preenche hoje `CasTusGrp`; a regra solicitada exige `CasTusGrp = CasTusCod`.
- `CasRpaModel` tenta preencher `CasRpaGrp` a partir de `CasRpaCod`, campo que não existe. Como a PK de `CasRpa` (`CasRpsCod`, `CasAppCod`) é composta somente por FKs, o default correto será aleatório.
- O arquivo existente do pacote é `App/Static/Rules/Manager/Settings_Manager_V1.0.0.json`. `DataPackage` procura hoje qualquer `Settings_*.json`, e `loadFile()` só aceita o conteúdo quando `ProductKey` coincide exatamente com `CasApp.CasAppCod` e `Version` coincide com `CasApp.CasAppVer`.
- No fluxo web, `App\Class\Support\Install` precede `ApplyApplicationSettings`, obtém o repositório da sessão autenticada e encaminha o ID do aplicativo. Esse adaptador depende de `AuthSession` e não deve ser usado como entrada do comando CLI.

## Estado final esperado

Considerando IDs calculados ou gerados em tempo de execução:

| Repositório | Usuário | Tipo no repositório | Finalidade |
| --- | --- | --- | --- |
| repositório do admin | admin | `ADMINISTRATOR ACCOUNT` | perfil exclusivo da conta administrativa inicial |
| repositório do support | support | `MANAGER ACCOUNT` | suporte como proprietário e gestor do próprio repositório |
| repositório do admin | support | `SUPPORT ACCOUNT` | acesso de suporte ao repositório administrativo |
| repositório de usuário comum | próprio usuário | `MANAGER ACCOUNT` | perfil proprietário padrão de novas contas |
| repositório de usuário comum | support | `SUPPORT ACCOUNT` | acesso padrão do suporte ao repositório do cliente |

Não deve existir uma segunda associação do usuário `support` ao próprio repositório com `SUPPORT ACCOUNT`, pois a chave de `CasRpu` permite apenas uma relação por par repositório/usuário e, nesse caso, prevalece `MANAGER ACCOUNT`.

## Domínio protegido de tipos de usuário

Os seguintes valores de `CasTusDsc` formam o domínio predefinido da aplicação:

- `ADMINISTRATOR ACCOUNT`;
- `MANAGER ACCOUNT`;
- `SUPPORT ACCOUNT`;
- `USER ACCOUNT`.

A existência de um tipo no domínio não concede esse papel automaticamente. A autorização efetiva continua sendo definida pela associação em `CasRpu`: admin recebe `ADMINISTRATOR ACCOUNT` no próprio repositório, proprietários comuns e support recebem `MANAGER ACCOUNT` no repositório próprio, e support recebe `SUPPORT ACCOUNT` nos repositórios dos demais usuários. `USER ACCOUNT` permanece disponível para associações de usuários comuns que não sejam proprietários.

`CasTus` continuará aceitando outros valores cadastrados dinamicamente. Entretanto, os quatro valores predefinidos não poderão ser removidos nem renomeados de forma que deixem de existir no domínio. A carga proposta não executará exclusões; esta proteção fica registrada como regra funcional a ser respeitada por qualquer rotina atual ou futura de manutenção de tipos de usuário.

Como `CasTus` é vinculada a `CasRps`, a implementação deverá garantir o domínio predefinido no contexto de cada repositório criado pela rotina comum. A presença dos quatro tipos não altera a regra que restringe `ADMINISTRATOR ACCOUNT` à associação do admin inicial.

## Regra de preenchimento dos campos `Grp`

- Por padrão, qualquer campo cujo nome termine em `Grp` herda o valor da chave primária do registro.
- Em tabelas com chave primária composta, `Grp` herda o componente da chave que não seja também chave estrangeira.
- Quando todos os componentes da chave primária composta também forem chaves estrangeiras, não existe valor elegível para herança; nesse caso, `Grp` deve receber um valor aleatório gerado no momento da criação.
- O valor aleatório de `Grp` deve respeitar o tamanho/metadado do campo, usar o gerador comum de identificadores do projeto e permanecer inalterado em reexecuções idempotentes. Ele não deve copiar arbitrariamente uma das FKs.
- Em `CasRpa`, ambas as partes da PK são FKs; portanto, `CasRpaGrp` deve ser gerado aleatoriamente quando não for informado.
- Para `CasTus`, `CasRpsCod` é a parte estrangeira da chave composta e `CasTusCod` é a parte própria; portanto, `CasTusGrp` deve receber exatamente `CasTusCod`.
- No refinamento do tipo proprietário padrão, `CasTusDsc` recebe `MANAGER ACCOUNT`, enquanto `CasTusGrp` recebe seu `CasTusCod`; descrição e grupo não devem ser tratados como o mesmo valor.
- A implementação deve aplicar essa herança como default no model ou em uma rotina comum quando `Grp` não for informado. Na carga inicial, nenhum valor divergente deve ser fornecido explicitamente; o tratamento de valores explícitos em outros cadastros permanece fora deste plano.
- Os valores predefinidos de `CasTusDsc` continuam sendo os quatro domínios documentados; cada registro possui seu próprio `CasTusCod`, que também será seu `CasTusGrp`.

## Desenho do comando de carga

### Decisão de reaproveitamento de `CreateUserRepositoryAccount`

`CreateUserRepositoryAccount` deve ser reaproveitada como a rotina central para criar uma conta com repositório próprio. A nova `CreateInitialData` não deverá duplicar diretamente a criação de `CasUsr`, `CasRps`, tipo proprietário e `CasRpu`; ela deverá preparar os contextos, chamar `CreateUserRepositoryAccount` uma vez para admin e uma vez para support e coordenar somente as dependências externas à rotina.

| Comportamento atual | Reaproveitamento | Modificação necessária |
| --- | --- | --- |
| `createUser()` separa login/domínio e cria `CasUsr` | Manter a responsabilidade dentro da classe | Validar o e-mail, receber o ID explicitamente, tratar conta existente compatível e rejeitar conflito de e-mail/ID |
| `createRepository()` cria `CasRps` para a conta | Manter a responsabilidade dentro da classe | Receber o ID explicitamente, validar repositório existente e garantir também o diretório físico |
| `run()` cria tipos de usuário | Manter a criação dos tipos dentro da classe | Trocar IDs fixos por definições recebidas, garantir os quatro tipos predefinidos no repositório e associar ao proprietário somente o tipo aplicável |
| `run()` associa proprietário/repositório em `CasRpu` | Manter a associação dentro da classe | Usar o ID de tipo recebido, verificar conteúdo quando a relação já existir e não depender do último model instanciado |
| `run()` delega o vínculo de suporte | Manter a delegação para contas comuns | Receber o contexto de suporte, permitir adiar/desabilitar a associação e aplicar a proteção contra vínculo de suporte consigo mesmo |
| A classe não cria `CasApp`/`CasRpa` | Não incorporar essas tabelas | Deixar aplicativo e relação aplicativo/repositório sob responsabilidade de `CreateInitialData` |

Na carga inicial, o vínculo do suporte com o repositório admin deve ser adiado até a conta support existir. Assim, a primeira chamada cria admin/repositório/tipo administrativo sem tentar criar o suporte; a segunda cria support/repositório/tipo manager sem criar um vínculo de suporte consigo mesmo; por fim, `CreateUserSupportClass` relaciona o support já existente ao repositório admin.

### 1. Criar o ponto de entrada CLI

- Adicionar `seed.php` na raiz, seguindo o padrão de bootstrap de `configure.php` e `migrate.php` (`vendor/autoload.php`, validação de `PHP_SAPI`, mensagens em `STDOUT`/`STDERR` e códigos de saída).
- Aceitar `--storage` com padrão `Default` e `--help`.
- Inicializar `Config` e validar antes de qualquer prompt:
  - storage solicitado existente;
  - conexão disponível;
  - tabelas `CasApp`, `CasUsr`, `CasRps`, `CasTus`, `CasRpa` e `CasRpu` existentes;
  - `APP_KEY`, `APP_NAME` e `APP_VERSION` preenchidos;
  - diretório-base `Repositories` existente e gravável.
- Interromper com orientação explícita para executar a migration se o esquema ainda não existir.
- Delegar a regra de negócio a uma classe de orquestração, proposta como `App\Class\Manager\CreateInitialData`, mantendo o arquivo CLI restrito a entrada, saída e códigos de retorno.

### 2. Coletar e validar as credenciais iniciais

- Solicitar separadamente nome, sobrenome, e-mail, senha e confirmação da senha para admin e support.
- Não usar `SupportUser.json` como fonte de credencial de entrada; o arquivo passa a ser uma saída regenerada a partir da conta support confirmada no banco.
- Normalizar os dois e-mails da mesma forma usada por `RegisterClass`/`LoginClass`, validar sua composição em login e domínio e impedir que admin e support usem a mesma conta.
- Aplicar às duas senhas pelo menos as regras já expostas por `Config::getRulesPassword()`.
- Ler ambas as senhas sem eco quando o terminal oferecer essa capacidade; caso não seja possível, falhar com instrução segura ou usar uma alternativa explícita que não grave senhas no histórico.
- Nunca incluir senha em texto puro, confirmação ou hash em logs, exceções ou resumo final.
- Calcular os IDs de usuário e repositório das duas contas de modo compatível com o login atual. Os demais IDs devem ser resolvidos ou gerados pela orquestração e passados às classes, em vez de declarados dentro delas.

### 3. Resolver IDs e regenerar `SupportUser.json`

- Derivar ou gerar em tempo de execução `CasUsrCod` e `CasRpsCod` de support, garantindo compatibilidade com o login e com a regra usuário/repositório próprio.
- Remover a dependência funcional do `CasUsrCod` e da credencial atualmente fixados no JSON; os valores resolvidos devem ser fornecidos aos métodos `run()`.
- Gerar ou recuperar os IDs de `CasTus` para os quatro valores predefinidos e propagá-los explicitamente: administrador especial, proprietário/manager, suporte e usuário comum.
- Para cada tipo, definir `CasTusGrp = CasTusCod`; para o proprietário padrão, definir `CasTusDsc = 'MANAGER ACCOUNT'`.
- Ao reexecutar, localizar os registros por suas chaves e identidades conhecidas antes de gerar novos IDs, evitando duplicação de tipos ou associações.
- Depois de confirmada a persistência de support em `CasUsr`, reler o registro do banco e sobrescrever `App/Static/Rules/Manager/SupportUser.json`.
- Gerar o JSON como uma lista contendo o registro support e os elementos correspondentes aos campos de `CasUsrModel::FIELDS`, mantendo a estrutura baseada na tabela. O campo de senha deve conter somente o valor já transformado e persistido em `CasUsrPwd`, nunca a senha digitada.
- Escrever JSON válido e formatado, validar a serialização antes da substituição e usar gravação protegida contra conteúdo parcial. Falha ao gerar ou sobrescrever o arquivo é falha do processo principal, não uma falha tolerada.

### 4. Refatorar `CreateUserRepositoryAccount`

- Preservar a classe e seus métodos de criação de usuário, repositório, tipo proprietário e associação como base comum do fluxo CLI e do registro web; não reimplementar essas quatro gravações em `CreateInitialData`.
- Substituir o contrato baseado em chaves implícitas `Repository` e `USR_ID` por um contrato explícito que receba os identificadores do repositório, do proprietário, do tipo proprietário e, quando aplicável, do usuário/tipo de suporte. Pode ser um array de contexto validado ou parâmetros tipados, mas os campos obrigatórios devem estar documentados.
- Receber as definições do domínio predefinido (`CasTusCod`, `CasTusDsc`, link e bloqueio), derivando `CasTusGrp` obrigatoriamente de `CasTusCod`, e permitindo garantir no repositório:
  - `ADMINISTRATOR ACCOUNT`;
  - `MANAGER ACCOUNT`;
  - `SUPPORT ACCOUNT`;
  - `USER ACCOUNT`.
- Receber separadamente o ID do papel que será associado ao proprietário, permitindo atribuir:
  - `ADMINISTRATOR ACCOUNT` somente para o admin inicial;
  - `MANAGER ACCOUNT` para support em seu próprio repositório e para contas comuns.
- Remover os IDs base64 fixos e a criação indiscriminada de `USER ACCOUNT`/`ADMIN ACCOUNT`.
- Garantir idempotentemente os quatro tipos predefinidos no repositório e associar ao proprietário em `CasRpu` somente o tipo adequado.
- Para contas comuns e para support em seu repositório próprio, criar/validar o tipo proprietário com `CasTusDsc = 'MANAGER ACCOUNT'` e `CasTusGrp = CasTusCod`.
- Acionar `CreateUserSupportClass` somente quando o proprietário não for o próprio suporte e quando o contexto de suporte tiver sido fornecido.
- Permitir que o orquestrador adie o vínculo de suporte na criação do admin, pois o usuário support ainda não existe nesse ponto da carga inicial.
- Retornar um resultado estruturado por etapa, em vez de descartar os retornos dos models em variáveis locais, para que o CLI possa falhar com diagnóstico preciso.
- Incorporar ou chamar uma rotina comum para criar `Repositories/<CasRpsCod>`, fazendo a regra física valer tanto no CLI quanto no registro web.
- Atualizar `RegisterClass` para montar e passar os IDs e o papel `MANAGER ACCOUNT` ao novo contrato, preservando a regra para futuras contas.

### 5. Refatorar `CreateUserSupportClass`

- Alterar `run()` para receber explicitamente, no mínimo, `repositoryId`, `supportUserId` e `supportTypeId`.
- Alterar `createDefaultTypeUser()` para receber `supportTypeId`, removendo o ID base64 fixo.
- Usar o `supportUserId` recebido ao buscar e associar a conta support já criada por `CreateUserRepositoryAccount`; a classe não deve depender do JSON para obter a credencial.
- Se o JSON continuar sendo lido para compatibilidade, tratá-lo somente como espelho do registro persistido, uniformizar seus itens como `array` e validar que seu conteúdo coincide com `CasUsr`.
- Tornar a criação de `CasTus` e `CasRpu` idempotente: registro idêntico é sucesso; conflito de conteúdo deve produzir erro explícito e não sobrescrever silenciosamente o papel existente.
- Impedir que a chamada transforme a associação `MANAGER ACCOUNT` do suporte em seu repositório próprio.
- Retornar um resultado estruturado com os estados `created`, `existing` ou `failed` para cada registro.

### 6. Orquestrar as gravações na ordem das dependências

A classe de carga inicial deverá executar:

1. preflight do ambiente, esquema e estado atual;
2. criação ou validação do aplicativo em `CasApp`, atribuindo `CasAppCod = Config::$APP_KEY`, `CasAppGrp = Config::$APP_KEY`, `CasAppDsc = Config::$APP_NAME` e `CasAppVer = Config::$APP_VERSION`;
3. chamada de `CreateUserRepositoryAccount` para criar/validar usuário admin, repositório admin, os quatro tipos predefinidos com `CasTusGrp = CasTusCod`, relação admin/`ADMINISTRATOR ACCOUNT` e diretório, com o vínculo de suporte adiado;
4. chamada de `CreateUserRepositoryAccount` para criar/validar usuário support, repositório support, os quatro tipos predefinidos com `CasTusGrp = CasTusCod`, relação support/`MANAGER ACCOUNT` e diretório, com a autoproteção que impede `SUPPORT ACCOUNT` no próprio repositório;
5. releitura do registro support em `CasUsr` e sobrescrita validada de `SupportUser.json` com os elementos persistidos da tabela;
6. chamada de `CreateUserSupportClass` com os IDs resolvidos para criar/validar `SUPPORT ACCOUNT` no repositório admin e a relação admin/support em `CasRpu`;
7. criação das relações de `CasRpa` entre o aplicativo identificado por `APP_KEY` e os repositórios admin e support, deixando `CasRpaGrp` ser gerado aleatoriamente pela regra de PK composta somente por FKs;
8. localização determinística do pacote `Settings_<APP_NAME>_V<APP_VERSION>.json` e atualização obrigatória de `ProductKey` para `APP_KEY` e `Version` para `APP_VERSION`, preservando e validando os demais elementos do JSON;
9. confirmação do checkpoint principal: usuários, aplicativo, tipos, relações, JSON de suporte, pacote com `ProductKey` correto e diretórios válidos;
10. execução CLI pós-checkpoint de `ApplyApplicationSettings::run($repositoryId, Config::$APP_KEY)` para os repositórios admin e support, registrando separadamente o resultado de cada repositório;
11. verificação pós-carga e impressão de resumo sem dados sensíveis, distinguindo conclusão integral de conclusão com aviso na aplicação de configurações.

As operações deverão respeitar as FKs: `CasApp`, `CasUsr` e `CasRps` antes de `CasTus`; `CasTus` antes de `CasRpu`; `CasApp` e `CasRps` antes de `CasRpa`.

### 7. Ajustar e aplicar o pacote Manager

- Declarar `APP_VERSION` em `EnvironmentVars` com o valor canônico `1.0.0`, expô-la como `Config::$APP_VERSION` e incluí-la na validação de variáveis obrigatórias. `configure.php` deverá gravar exatamente `APP_VERSION=1.0.0` no `.env` junto das demais variáveis do ambiente.
- Manter uma única identidade para o aplicativo:
  - `CasApp.CasAppCod = Config::$APP_KEY`;
  - `CasApp.CasAppDsc = Config::$APP_NAME`;
  - `CasApp.CasAppVer = Config::$APP_VERSION`;
  - `CasApp.CasAppGrp = CasApp.CasAppCod`;
  - `ProductKey = Config::$APP_KEY` no JSON do pacote.
- Localizar o pacote somente no diretório `App/Static/Rules/Manager` e compor seu nome exato como `Settings_` + `APP_NAME` + `_V` + `APP_VERSION` + `.json`.
- O caractere `V` pertence somente ao padrão físico do nome: com `APP_NAME=Manager` e `APP_VERSION=1.0.0`, o arquivo esperado é `Settings_Manager_V1.0.0.json`. Não acrescentar `V` ao valor armazenado em `APP_VERSION`.
- Tratar `APP_NAME` e `APP_VERSION` como componentes de nome de arquivo: rejeitar separadores, segmentos relativos ou caracteres inválidos e confirmar que o caminho resolvido permanece dentro de `App/Static/Rules/Manager`.
- Substituir a busca ampla de qualquer `Settings_*.json` em `DataPackage` pela resolução determinística do arquivo esperado, ou ao menos priorizar e exigir correspondência exata com `Settings_<APP_NAME>_V<APP_VERSION>.json`.
- Carregar o JSON localizado, alterar `ProductKey` para `Config::$APP_KEY` e `Version` para `Config::$APP_VERSION`, preservar os demais elementos e sobrescrever o arquivo com JSON válido.
- Validar que o elemento `Version` do JSON e `CasApp.CasAppVer` contenham exatamente `Config::$APP_VERSION` (`1.0.0`, sem o prefixo `V`), pois `DataPackage::loadFile()` exige produto e versão compatíveis.
- Manter em `RegisterClass` o uso de `Config::$APP_KEY` para `CasRpa` e `ApplyApplicationSettings`, acrescentando validação de que o `CasApp` correspondente existe e coincide com o pacote localizado.
- No CLI, chamar `ApplyApplicationSettings` diretamente, pois `Install` exige usuário/repositório em `AuthSession`.
- No browser, preservar `App\Class\Support\Install::run($appId, 'ApplyApplicationSettings')` como fachada anterior a `ApplyApplicationSettings`; esse será também o caminho interativo para repetir a aplicação após um aviso no seed.
- Tratar falha de leitura do pacote, incompatibilidade residual, exceção ou falha de gravação ocorrida dentro de `ApplyApplicationSettings` como aviso pós-checkpoint. Não remover nem reverter usuários, repositórios, aplicativo, tipos ou relações já confirmados.
- Refatorar `ApplyApplicationSettings::run()` ou envolver sua execução para obter resultado observável por repositório (`applied` ou `warning`), pois atualmente o retorno `void` e o pacote vazio podem ocultar a falha.
- Continuar tentando a aplicação no outro repositório quando uma chamada falhar, acumulando os avisos no resumo.
- Informar que a aplicação das configurações pode ser repetida posteriormente pela tela interativa com os acessos já criados.
- Restringir a tolerância de falha exclusivamente a essa etapa. Falhas anteriores, inclusive banco, criação de domínio, geração de `SupportUser.json` ou edição obrigatória de `ProductKey`, devem encerrar o comando como falha e produzir diagnóstico para reexecução idempotente.
- Quando houver transação compartilhada para o núcleo, confirmá-la antes de chamar `ApplyApplicationSettings`; nunca incluir a aplicação do pacote em uma transação capaz de desfazer o checkpoint principal.

### 8. Definir política de reexecução e falha parcial

- A execução deve ser idempotente: uma segunda chamada com os mesmos dados não cria duplicatas e informa que a carga já existe.
- Antes de gravar, classificar o banco como vazio, carga completa ou carga parcial/conflitante.
- Em carga completa, validar as relações e encerrar com sucesso sem solicitar nova senha.
- Em carga parcial, criar somente itens comprovadamente ausentes quando os registros existentes forem compatíveis; diante de IDs, e-mail, papel ou aplicativo conflitantes, abortar com a lista das divergências.
- Não adicionar uma opção destrutiva que apague ou recrie contas automaticamente.
- Se a infraestrutura de conexão atual não permitir uma única transação compartilhada entre os models, compensar com preflight, operações idempotentes e verificação pós-carga; não simular atomicidade inexistente.
- Criar o diretório somente depois de o ID do repositório estar validado e restringir o caminho final ao diretório-base `Repositories`.

### 9. Atualizar consumidores dos papéis

- Substituir o perfil proprietário comum `ADMIN`/`ADMIN ACCOUNT` por `MANAGER`/`MANAGER ACCOUNT` nos pontos que representam contas comuns.
- Preservar uma lista separada para o perfil administrativo especial, sem concedê-lo a novas contas.
- Revisar em `Config.php` os métodos de domínio para que retornem exatamente:
  - `getTypeUsersAdminPortal()`: `['ADMINISTRATOR ACCOUNT']`;
  - `getTypeUsersSupportPortal()`: `['SUPPORT ACCOUNT']`;
  - `getTypeUsersAdminLocal()`: `['MANAGER ACCOUNT']`.
- Criar, seguindo o mesmo padrão, um método para o domínio ainda ausente, proposto como `getTypeUsersUserLocal()`, retornando `['USER ACCOUNT']`.
- Revisar `Config::getTypeUsersRestrictAccess()` para compor somente os domínios que realmente representam acesso restrito; a inclusão de `USER ACCOUNT` não deve ser automática apenas por existir o novo método.
- Revisar `SideMenuControls`, comparações de sessão e comentários para que usem os nomes canônicos e não dependam de `ADMIN ACCOUNT` ou `ADMINISTRATOR PORTAL`.
- Documentar junto aos métodos de `Config` que os quatro valores são predefinidos, podem coexistir com tipos dinâmicos e não podem ser removidos do domínio.
- Validar que admin, manager e suporte mantenham somente os menus e permissões esperados em cada repositório.

### 10. Documentar o procedimento técnico no README

- Criar uma seção `Configuração técnica inicial` no `README.md`.
- Listar pré-requisitos: PHP/Composer instalados, dependências disponíveis, base MySQL criada, usuário com permissão de DDL/DML e diretório `Repositories` gravável.
- Documentar os comandos na ordem manual obrigatória, incluindo exemplos para os ambientes suportados e `--storage=Default`.
- Explicar que o `.env` deve ser revisado e a conexão validada antes da migration.
- Informar que `seed.php` solicita no terminal as credenciais de admin e support, cria as contas, aplicativo, tipos, relações e diretórios e sobrescreve `SupportUser.json`.
- Documentar que o nome do pacote é composto como `Settings_<APP_NAME>_V<APP_VERSION>.json`, mantendo `APP_VERSION=1.0.0`, que `CasAppCod` e `ProductKey` recebem `APP_KEY` e que a aplicação ocorre depois do checkpoint principal.
- Diferenciar os caminhos: seed chama `ApplyApplicationSettings` diretamente no CLI; a repetição interativa no browser passa por `App\Class\Support\Install`.
- Explicar que somente uma falha de `ApplyApplicationSettings` gera conclusão com aviso sem desfazer os acessos; a operação pode ser repetida pela tela interativa.
- Documentar comportamento de reexecução, códigos de saída e diagnóstico para migration ausente, carga parcial e conflito.
- Não publicar no README credenciais reais, hashes, IDs gerados ou conteúdo sensível de `SupportUser.json`.

## Validação planejada

### Cenários de linha de comando

- `seed.php` rejeita execução fora de CLI.
- `--help` não abre conexão nem solicita senha.
- storage inválido, `.env` incompleto, banco inacessível ou migration ausente encerram antes do prompt e sem gravar dados.
- ausência de `APP_NAME`, `APP_VERSION` ou `APP_KEY`, nome de pacote inseguro ou arquivo exato inexistente são diagnosticados antes da aplicação.
- senha inválida ou confirmação divergente não cria registros.
- credenciais iguais para admin e support são recusadas.
- falha ao sobrescrever `SupportUser.json` ou ao ajustar `ProductKey` encerra o processo principal como erro.
- uma instalação vazia termina com código zero e resumo completo.
- a segunda execução termina com código zero, sem duplicar nem alterar IDs ou senha.
- estado parcial compatível é completado; estado conflitante é recusado com diagnóstico.
- falha em `ApplyApplicationSettings` preserva o checkpoint principal, tenta o outro repositório e termina como sucesso com aviso recuperável.
- registro com PK composta somente por FKs recebe `Grp` aleatório na criação e mantém o mesmo valor na reexecução.

### Invariantes de dados

- Existe exatamente um aplicativo inicial com `CasAppCod = APP_KEY`, `CasAppDsc = APP_NAME`, `CasAppVer = APP_VERSION` e `CasAppGrp = CasAppCod`.
- `APP_VERSION`, `CasAppVer` e o elemento `Version` do pacote valem `1.0.0`; o prefixo `V` aparece apenas no nome do arquivo.
- Cada repositório criado possui exatamente um registro para cada tipo predefinido: `ADMINISTRATOR ACCOUNT`, `MANAGER ACCOUNT`, `SUPPORT ACCOUNT` e `USER ACCOUNT`.
- Cada registro `CasTus` possui `CasTusGrp = CasTusCod`; o tipo proprietário padrão possui `CasTusDsc = 'MANAGER ACCOUNT'`.
- Admin e support possuem, cada um, um `CasUsr`, um `CasRps` com o mesmo identificador esperado pelo login e um diretório próprio.
- O repositório admin possui as relações admin/administrador e support/suporte.
- O repositório support possui apenas a relação support/manager para esse usuário.
- Ambos os repositórios estão relacionados ao aplicativo em `CasRpa`.
- Um cadastro comum posterior cria usuário/repositório próprio com `MANAGER ACCOUNT` e adiciona support com `SUPPORT ACCOUNT`.
- FKs de `CasTus`, `CasRpu` e `CasRpa` permanecem válidas.
- `SupportUser.json` contém uma lista válida formada pelos campos persistidos de support em `CasUsr`, sem senha em texto puro.
- O arquivo localizado exatamente por `Settings_<APP_NAME>_V<APP_VERSION>.json` possui `ProductKey = APP_KEY` e `Version = APP_VERSION`.

### Regressão funcional

- Login do admin abre seu repositório com o perfil administrativo especial.
- Login do support lista seu repositório e o repositório admin; ao alternar, o perfil muda de manager para support conforme `CasRpu`.
- Login de usuário comum abre seu repositório como manager.
- Menus e restrições que consultam `PROFILE` reconhecem a nova nomenclatura.
- A documentação e os testes da carga confirmam que nenhum dos quatro tipos predefinidos é removido ou renomeado; a eventual validação das telas/rotinas genéricas de exclusão deverá respeitar a regra documentada, sem ampliar o escopo desta carga inicial.
- Nenhuma senha ou hash aparece na saída do comando e nos logs de erro.
- No CLI, `ApplyApplicationSettings` recebe `APP_KEY` diretamente; no browser, recebe o mesmo ID por meio de `Install`. Sua falha não remove dados do checkpoint principal.

## Critérios de aceite

- A instalação pode ser concluída manualmente com os três comandos, na ordem documentada: configurar, migrar e carregar dados.
- O comando solicita as credenciais de admin e support e cria todos os registros e diretórios previstos sem IDs ou credenciais fixos nas duas classes refatoradas.
- `CreateUserRepositoryAccount` e `CreateUserSupportClass` recebem os IDs necessários de seus chamadores.
- `CreateInitialData` reutiliza `CreateUserRepositoryAccount` para admin e support e não contém uma implementação paralela para criar `CasUsr`, `CasRps`, `CasTus` proprietário ou `CasRpu` proprietário.
- A matriz admin/support/usuário comum corresponde às regras deste documento.
- Os quatro tipos predefinidos existem por repositório, sem que sua simples existência conceda associações indevidas, e permanecem documentados como não removíveis.
- Novas contas usam `MANAGER ACCOUNT`, e `ADMINISTRATOR ACCOUNT` fica restrito ao admin inicial.
- A reexecução é segura e não gera registros duplicados ou mudança silenciosa de papéis.
- Os campos `Grp` respeitam a herança da chave definida, incluindo `CasTusGrp = CasTusCod`.
- Tabelas com chave primária composta exclusivamente por FKs recebem `Grp` aleatório e estável após a criação.
- `SupportUser.json` é sobrescrito a partir do registro support confirmado em `CasUsr`.
- `CasAppCod` e `ProductKey` correspondem a `APP_KEY`, `APP_VERSION` permanece `1.0.0`, o pacote é localizado como `Settings_<APP_NAME>_V<APP_VERSION>.json` e `ApplyApplicationSettings` é executada após o checkpoint como único ponto de falha tolerada.
- O `README.md` contém o procedimento técnico completo e não sugere automação do fluxo manual.
- Os cenários de validação passam em uma base descartável criada após a migration.

## Arquivos previstos para alteração na implementação

- `seed.php` (novo ponto de entrada CLI).
- `App/Class/Manager/CreateInitialData.php` (nova orquestração da carga).
- `App/Class/Manager/CreateUserRepositoryAccount.php`.
- `App/Class/Manager/CreateUserSupportClass.php`.
- `App/Class/Manager/ApplyApplicationSettings.php` e/ou seu adaptador de resultado.
- `App/Class/Support/Install.php`, para preservar/documentar a precedência no fluxo web e propagar resultado observável se necessário.
- `App/Class/Auth/RegisterClass.php`.
- `App/Core/Config.php`, incluindo `APP_VERSION`, e consumidores que comparem nomes de perfil.
- `App/Core/EnvironmentVars.php`, para declarar `APP_VERSION` em todos os ambientes.
- `App/Models/CAS/CasTusModel.php` e models/rotina comum responsáveis pelo default de campos `Grp`.
- `App/Traits/DataPackage.php`, caso necessário para tornar a falha de carga observável.
- `App/Static/Rules/Manager/SupportUser.json` (sobrescrito pela carga).
- `App/Static/Rules/Manager/Settings_<APP_NAME>_V<APP_VERSION>.json` (`ProductKey` e `Version` alinhados pela carga; para os valores atuais, `Settings_Manager_V1.0.0.json`).
- `README.md`.
- Arquivos de teste/validação compatíveis com a estratégia de testes escolhida para o projeto.
