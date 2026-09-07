> **Status: IMPLEMENTADO**

# Plano - complemento CAS do pacote ContractFlow

## Objetivo

Complementar a implementação de `Doc\tasks\ativacao-aplicativos-modulos.md` com as instruções necessárias para que o pacote técnico do ContractFlow cadastre a base CAS mínima do aplicativo durante a ativação pela loja.

O alvo principal é o pacote:

```text
App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json
```

Observação: a solicitação cita `App\Static\Rules\Contract\Settings\_ContractFlow\_V1.0.0.json`, mas o arquivo existente no projeto está em `App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json`. A implementação deve confirmar se será mantido o caminho atual ou se haverá migração de convenção antes de alterar o pacote.

## Documentos e artefatos relacionados

- `Doc\RFC\003-ativacao-de-aplicativos-e-modulos.md`
- `Doc\tasks\ativacao-aplicativos-modulos.md`
- `Doc\ADR\004-ativacao-web-controlada-de-aplicativos.md`
- `App\Static\Rules\Manager\Settings_Manager_V1.0.0.json`
- `App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json`
- `App\Class\Manager\ApplyApplicationSettings.php`
- `App\Class\Manager\ApplicationActivationService.php`
- `App\Class\Contract\ApplyContractSettings.php`

## Escopo

- Adicionar dados CAS básicos do ContractFlow ao pacote técnico.
- Reutilizar a estrutura `BaseTableData` já usada pelo pacote Manager para dados CAS.
- Garantir códigos fixos para tabelas que participam das regras de acesso.
- Não gravar identificadores específicos de um repositório no JSON.
- Garantir comportamento idempotente, sem duplicação de registros.
- Implementar ajuste mínimo em `ApplyApplicationSettings` para processar `CasApp`.
- Manter a aplicação operacional `CTR` já existente em `ApplyContractSettings`.

## Fora do escopo

- Criar pagamento, checkout, assinatura ou contrato comercial.
- Criar tabelas novas de licenciamento.
- Criar dados específicos de clientes dentro do pacote JSON.
- Gerar versão publicada ou build.
- Alterar a experiência visual da loja, salvo mensagens necessárias para ativação.

## Diagnóstico atual

O pacote ContractFlow atual contém:

- `ProductKey = CONTRACTFLOW`
- `ProductName = ContractFlow`
- `Version = 1.0.0`
- `RunScript = ApplyContractSettings`
- dados operacionais em `CTRConcurrencyGroup`

Esse pacote originalmente não continha:

- `CasApp`
- `CasPrg`
- `CasFun`
- `CasFpr`
- `CasMdl`
- `CasMpr`
- `CasMnu`
- `CasMna`
- `CasPfi`
- `CasPfu`
- `CasApf`
- `CasAfu`

O aplicador `ApplyApplicationSettings` já possui lógica idempotente para várias dessas tabelas CAS, mas originalmente não possuía processador explícito para `CasApp`.

A decisão adotada é adicionar suporte controlado a `CasApp` em `ApplyApplicationSettings` e manter `CasRpa` centralizado no `ApplicationActivationService::ensureRepositoryApplication()`, porque o vínculo depende do repositório ativo da sessão e da regra de ativação.

## Convenções de código fixo

Para evitar falha nas regras de permissão, os códigos abaixo devem ser fixos e não podem ser gerados por UUID/random:

- `CasApp.CasAppCod = CONTRACTFLOW`
- `CasMdl.CasMdlCod = CONTRACTFLOW`
- `CasPfi.CasPfiCod = CONTRACTFLOW`
- `CasMnu.CasMnuCod = CONTRACTFLOW`
- `CasMna.CasMnaCod = CONTRACTFLOW_DASHBOARD`
- `CasFun.CasFunCod = AUTHORIZED`
- `CasPrg.CasPrgCod` igual ao identificador do programa

Programas:

- `ContractDashboard`
- `ContractInstall`
- `Contract`
- `ContractItem`
- `ContractLedger`
- `ContractSignatory`
- `ContractConcurrencyGroup`

## Dados CAS esperados no pacote

### CasApp

Adicionar o aplicativo:

```json
{
  "CasAppCod": "CONTRACTFLOW",
  "CasAppDsc": "Contract Flow",
  "CasAppBlq": "N",
  "CasAppTst": "N",
  "CasAppVer": "1.0.0",
  "CasAppKey": "CONTRACTFLOW",
  "CasAppGrp": "CONTRACTFLOW"
}
```

Regra: não duplicar registro. Se já existir, não criar novo registro aleatório.

### CasRpa

O vínculo do aplicativo ao repositório solicitante não deve ser declarado no JSON. Ele continua centralizado em `ApplicationActivationService::ensureRepositoryApplication()`.

Regra: `CasRpsCod` não deve estar no JSON. O serviço de ativação deve preencher com o repositório ativo informado ao processo de ativação.

### CasFun

Garantir a funcionalidade:

```json
{
  "CasFunCod": "AUTHORIZED",
  "CasFunDsc": "Autorizado",
  "CasFunBlq": "N"
}
```

Regra: não duplicar se `AUTHORIZED` já existir no repositório.

### CasPrg

Cadastrar os programas com códigos fixos:

```json
[
  { "CasPrgCod": "ContractDashboard", "CasPrgDsc": "Dashboard ContractFlow", "CasPrgBlq": "N", "CasPrgTst": "N" },
  { "CasPrgCod": "ContractInstall", "CasPrgDsc": "Instalação ContractFlow", "CasPrgBlq": "N", "CasPrgTst": "N" },
  { "CasPrgCod": "Contract", "CasPrgDsc": "Contratos", "CasPrgBlq": "N", "CasPrgTst": "N" },
  { "CasPrgCod": "ContractItem", "CasPrgDsc": "Itens de Contrato", "CasPrgBlq": "N", "CasPrgTst": "N" },
  { "CasPrgCod": "ContractLedger", "CasPrgDsc": "Ledger de Contratos", "CasPrgBlq": "N", "CasPrgTst": "N" },
  { "CasPrgCod": "ContractSignatory", "CasPrgDsc": "Signatários", "CasPrgBlq": "N", "CasPrgTst": "N" },
  { "CasPrgCod": "ContractConcurrencyGroup", "CasPrgDsc": "Grupos de Concorrência", "CasPrgBlq": "N", "CasPrgTst": "N" }
]
```

### CasFpr

Vincular `AUTHORIZED` a cada programa:

```json
[
  { "CasFunCod": "AUTHORIZED", "CasPrgCod": "ContractDashboard" },
  { "CasFunCod": "AUTHORIZED", "CasPrgCod": "ContractInstall" },
  { "CasFunCod": "AUTHORIZED", "CasPrgCod": "Contract" },
  { "CasFunCod": "AUTHORIZED", "CasPrgCod": "ContractItem" },
  { "CasFunCod": "AUTHORIZED", "CasPrgCod": "ContractLedger" },
  { "CasFunCod": "AUTHORIZED", "CasPrgCod": "ContractSignatory" },
  { "CasFunCod": "AUTHORIZED", "CasPrgCod": "ContractConcurrencyGroup" }
]
```

### CasMdl e CasMpr

Criar o módulo:

```json
{
  "CasMdlCod": "CONTRACTFLOW",
  "CasMdlDsc": "Contract Flow",
  "CasMdlBlq": "N"
}
```

Vincular todos os programas ao módulo `CONTRACTFLOW`.

### CasMnu e CasMna

Criar menu:

```json
{
  "CasMnuCod": "CONTRACTFLOW",
  "CasMnuDsc": "CONTRACT FLOW",
  "CasMnuBlq": "N",
  "CasMnuGrp": "CONTRACTFLOW"
}
```

Adicionar ação de menu para o dashboard:

```json
{
  "CasMnuCod": "CONTRACTFLOW",
  "CasMnaCod": "CONTRACTFLOW_DASHBOARD",
  "CasPrgCod": "ContractDashboard",
  "CasMnaDsc": "Dashboard",
  "CasMnaBlq": "N",
  "CasMnaLnk": "/Contract/Dashboard",
  "CasMnaIco": "fas fa-file-contract",
  "CasMnaGrp": "CONTRACTFLOW"
}
```

### CasPfi e CasPfu

Criar o perfil:

```json
{
  "CasPfiCod": "CONTRACTFLOW",
  "CasPfiDsc": "Contract Flow",
  "CasPfiBlq": "N"
}
```

Adicionar perfil aos usuários:

```json
{
  "CasPfiCod": "CONTRACTFLOW",
  "CasUsrCod": ""
}
```

Regra: `CasUsrCod` vazio significa todos os usuários vinculados ao repositório, seguindo a lógica já usada no pacote Manager. Não incluir códigos de usuário no JSON.

### CasApf e CasAfu

Autorizar perfil e usuários para todos os programas:

```json
[
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractDashboard" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractInstall" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "Contract" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractItem" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractLedger" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractSignatory" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractConcurrencyGroup" }
]
```

Autorizar a funcionalidade `AUTHORIZED` para os mesmos programas:

```json
[
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractDashboard", "CasFunCod": "AUTHORIZED" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractInstall", "CasFunCod": "AUTHORIZED" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "Contract", "CasFunCod": "AUTHORIZED" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractItem", "CasFunCod": "AUTHORIZED" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractLedger", "CasFunCod": "AUTHORIZED" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractSignatory", "CasFunCod": "AUTHORIZED" },
  { "CasPfiCod": "CONTRACTFLOW", "CasUsrCod": "", "CasPrgCod": "ContractConcurrencyGroup", "CasFunCod": "AUTHORIZED" }
]
```

## Ajustes necessários no aplicador

### 1. Suportar `CasApp`

Adicionar `processCasApp()` em `ApplyApplicationSettings`.

Regras:

- validar campos obrigatórios de `CasAppModel`;
- respeitar `CasAppCod` informado no JSON;
- não gerar código aleatório quando `CasAppCod` estiver preenchido;
- se existir, não criar duplicado;
- opcionalmente atualizar versão/descrição somente se essa política for definida antes da implementação.

### 2. Manter `CasRpa` no serviço de ativação

Confirmado que `ApplicationActivationService::ensureRepositoryApplication()` continuará responsável por esse vínculo, preenchendo `CasRpsCod` com o repositório ativo da sessão, criando o vínculo quando não existir, desbloqueando quando a ativação permitir e evitando duplicidade.

### 3. Permitir pacote composto

O pacote ContractFlow hoje usa `RunScript = ApplyContractSettings`, que processa apenas dados operacionais `CTR`.

Para aplicar CAS e CTR no mesmo fluxo, escolher uma das opções:

1. **Recomendado:** permitir no catálogo um campo como `run_scripts` com execução sequencial:

```json
"run_scripts": [
  "ApplyApplicationSettings",
  "ApplyContractSettings"
]
```

2. Alternativa: criar `RunScript = ApplyContractFlowPackage`, um aplicador explícito que chama CAS e depois CTR.

3. Alternativa mínima: manter `ApplicationActivationService` aplicando sempre `ApplyApplicationSettings` para `BaseTableData` CAS antes de chamar `ApplyContractSettings` quando o produto for ContractFlow.

A opção recomendada é `run_scripts`, porque preserva a whitelist de aplicadores e evita criar nomes especiais por produto.

## Estrutura sugerida do pacote

O pacote deve separar dados CAS de dados CTR para evitar que `ApplyApplicationSettings` tente processar tabelas operacionais.

Formato recomendado:

```json
{
  "ProductKey": "CONTRACTFLOW",
  "ProductName": "ContractFlow",
  "Version": "1.0.0",
  "RunScript": "ApplyContractSettings",
  "RunScripts": [
    "ApplyApplicationSettings",
    "ApplyContractSettings"
  ],
  "BaseTableData": [
    { "CasApp": [] },
    { "CasFun": [] },
    { "CasMdl": [] },
    { "CasPrg": [] },
    { "CasFpr": [] },
    { "CasMpr": [] },
    { "CasMnu": [] },
    { "CasMna": [] },
    { "CasPfi": [] },
    { "CasPfu": [] },
    { "CasApf": [] },
    { "CasAfu": [] }
  ],
  "OperationalTableData": [
    { "CTRConcurrencyGroup": [] }
  ]
}
```

Se for mantido o nome `BaseTableData` para `CTRConcurrencyGroup`, `ApplyApplicationSettings` deve ignorar tabelas desconhecidas de forma controlada ou receber apenas um subconjunto CAS filtrado pelo orquestrador.

## Plano de execução

### 1. Validar convenção do arquivo

- Confirmar se o caminho permanecerá `App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json`.
- Confirmar se haverá pasta `Settings` ou se a referência com underscores escapados era apenas formatação do texto.

### 2. Definir execução composta

- Atualizar `ApplicationPackageLocator` para aceitar `RunScripts` ou `run_scripts` com whitelist.
- Atualizar `ApplicationActivationService` para executar aplicadores em sequência.
- Manter `RunScript` como compatibilidade com pacotes antigos.

### 3. Adaptar `ApplyApplicationSettings`

- Adicionar `case 'CasApp'`.
- Não adicionar `case 'CasRpa'`; o vínculo permanece no `ApplicationActivationService`.
- Garantir que tabelas desconhecidas retornem aviso rastreável ou sejam filtradas antes da aplicação CAS.
- Manter idempotência por `readRegister()` antes de `createRegister()`.

### 4. Atualizar pacote ContractFlow

- Inserir dados CAS no JSON com códigos fixos.
- Separar dados CAS e CTR conforme decisão da etapa 2.
- Manter `ProductKey`, `ProductName` e `Version` alinhados ao catálogo.

### 5. Atualizar catálogo da loja

- Caso adotado `RunScripts`, alterar o item ContractFlow em `App\Static\Menu\AppStore.json`.
- Garantir que `package_directory = Contract`.
- Garantir que `package_name = Settings_ContractFlow_V1.0.0.json`.
- Garantir que `package_version = 1.0.0`.

### 6. Testar idempotência

- Executar ativação uma vez em ambiente controlado.
- Confirmar criação de `CasApp` e `CasRpa`.
- Confirmar criação dos programas, módulo, menu, perfil e autorizações.
- Executar ativação novamente.
- Confirmar que não há duplicação de registros.
- Confirmar que checkpoints registram aplicação ou reexecução segura.

## Critérios de aceite

- O pacote ContractFlow contém instruções CAS completas para primeiro acesso.
- `CasApp` é cadastrado com código fixo `CONTRACTFLOW`.
- `CasRpa` vincula o aplicativo ao repositório solicitante, sem `CasRpsCod` fixo no JSON.
- Todos os programas ContractFlow são cadastrados com códigos fixos.
- A funcionalidade `AUTHORIZED` existe e está vinculada aos programas.
- O módulo `CONTRACTFLOW` existe e contém os programas.
- O menu `CONTRACTFLOW` exibe o item para `ContractDashboard`.
- O perfil `CONTRACTFLOW` é criado.
- O perfil é vinculado aos usuários do repositório por `CasUsrCod` vazio no pacote.
- `CasApf` e `CasAfu` autorizam o perfil para todos os programas listados.
- Nenhum registro é duplicado em reexecução.
- O pacote não contém IDs personalizados de usuário, repositório ou outro cliente.
- A aplicação operacional `CTRConcurrencyGroup` continua funcionando.

## Riscos e decisões pendentes

- Decisão: `CasRpa` continuará centralizado no `ApplicationActivationService::ensureRepositoryApplication()`, pois o vínculo depende do repositório ativo da sessão e da regra de ativação. O pacote não deve declarar `CasRpsCod` nem processar `CasRpa`.
- Decisão: separar `OperationalTableData` para tabelas operacionais CTR, como `CTRConcurrencyGroup`. `BaseTableData` deve permanecer reservado para configuração CAS do aplicativo/repositório, reduzindo acoplamento entre a instalação operacional no storage `SAAS` e a configuração de acesso, menu e permissões.
- Decisão: o suporte a múltiplos aplicadores deve manter whitelist explícita no código. `RunScripts` pode listar apenas aplicadores previamente permitidos, sem instanciar classes, métodos ou arquivos dinamicamente a partir do JSON.
- Recomendação operacional: antes de aplicar o pacote em produção, validar manualmente a ativação em ambiente ou repositório isolado de homologação. O sistema não bloqueia a ativação em produção por ausência dessa validação prévia; a checagem funciona como controle de implantação.
