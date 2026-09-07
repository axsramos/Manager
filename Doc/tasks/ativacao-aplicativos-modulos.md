> **Status: IMPLEMENTADO**

## Resultado da implementação

- Criado serviço de catálogo para carregar, validar, filtrar e localizar produtos da loja.
- Criado localizador de pacotes técnicos por aplicativo em `App\Static\Rules\<Aplicativo>`.
- Criado serviço de token para validar `CasTkn` pela convenção `APP_ACTIVATION:<CasRpsCod>:<CasAppCod>` e bloquear a chave após sucesso.
- Criado serviço de checkpoint em `CasPar` para registrar ativação por repositório, aplicativo, versão e hash do pacote.
- Criado orquestrador de ativação para validar sessão, repositório, produto, pacote, direito de uso, vínculo `CasRpa` e aplicador permitido.
- Integrada a ação principal da loja ao endpoint POST `/Manager/AppStore/activate/{slug}`.
- Adicionado campo de chave na página do produto quando `activation_mode = token`.
- Atualizado `AppStore.json` com campos técnicos de pacote, `run_script`, `requires_token` e `success_url`.
- Criado pacote técnico mínimo para o produto gratuito `Manager Insights`.
- Preservado `/Support/Package` e o método legado de `ApplyApplicationSettings::run()`.

# Plano - ativação de aplicativos e módulos

## Objetivo

Implementar o fluxo técnico de ativação de aplicativos descrito na RFC 003, integrando a loja de aplicativos a um serviço de ativação controlado, capaz de validar direito de uso, localizar pacotes técnicos por aplicativo, aplicar configurações de forma idempotente e registrar o resultado por repositório.

Documentos de referência:

- `Doc\RFC\003-ativacao-de-aplicativos-e-modulos.md`;
- `Doc\RFC\004-loja-de-aplicativos.md`;
- `Doc\ADR\004-ativacao-web-controlada-de-aplicativos.md`;
- `Doc\tasks\loja-aplicativos-interface-visual.md`.

Este plano complementa a interface visual da loja. A tela já pode apresentar produtos, mas a ação principal ainda não deve ser considerada ativação real até este plano ser implementado.

## Escopo

- Criar serviço de ativação de aplicativos.
- Integrar a ação principal da loja ao serviço.
- Validar repositório ativo da sessão.
- Validar produto selecionado pelo catálogo e por `CasApp`.
- Validar produto gratuito, contratado, já ativado ou dependente de chave.
- Usar `CasTkn` para chaves individuais de ativação na primeira fase.
- Bloquear token após ativação bem-sucedida.
- Separar `ProductKey` de licença individual.
- Criar localizador de pacotes parametrizado por aplicativo.
- Permitir pacotes em `App\Static\Rules\<Aplicativo>`.
- Reaproveitar `ApplyApplicationSettings` para configurações CAS.
- Permitir aplicadores específicos por produto quando necessário.
- Registrar checkpoints de ativação por repositório e aplicativo.
- Manter `/Support/Package` como ferramenta técnica de suporte.
- Retornar mensagens claras para a interface.

## Fora do escopo

- Criar checkout, pagamento, assinatura recorrente ou integração comercial externa.
- Criar painel administrativo completo de contratos/licenças.
- Criar tabelas novas de licenciamento nesta fase.
- Migrar o catálogo visual para banco de dados.
- Reestruturar toda a autorização CAS.
- Alterar migrações do ContractFlow fora do necessário para integração de ativação.
- Remover `/Support/Package`.
- Publicar versão, gerar build ou alterar changelog.

## Estado atual relevante

- A loja visual foi planejada em `Doc\tasks\loja-aplicativos-interface-visual.md`.
- O catálogo visual usa `App\Static\Menu\AppStore.json`.
- A tela `/Support/Package` lê um código, busca dados de `CasApp` e chama `App\Class\Support\Install`.
- `App\Class\Support\Install` chama `ApplyApplicationSettings` usando o repositório da sessão.
- `ApplyApplicationSettings` aplica `BaseTableData` em tabelas CAS e registra progresso em `CasPar`.
- O trait `DataPackage` está acoplado ao pacote do Manager, usando `Config::getPathRules('Manager')`, `APP_NAME` e `APP_VERSION`.
- O pacote Manager fica em `App\Static\Rules\Manager\Settings_Manager_V1.0.0.json`.
- O pacote ContractFlow fica em `App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json`.
- O ContractFlow também possui `ContractPackageInstaller` e `ApplyContractSettings` para configurações operacionais na base `SAAS`.
- `CasTkn` já possui chave, descrição, expiração e bloqueio.
- `ProductKey` do pacote técnico não deve ser usado como licença individual.

## Estado final esperado

O fluxo deve permitir:

1. usuário autenticado abrir a loja;
2. usuário clicar na ação principal de um produto;
3. sistema identificar repositório ativo da sessão;
4. sistema identificar produto por `slug`, `app_id` ou `product_key`;
5. sistema validar se o produto está publicado e ativo;
6. sistema validar se o produto é gratuito, já contratado, já ativado ou exige chave;
7. sistema solicitar chave quando `activation_mode` exigir token;
8. sistema validar `CasTkn` contra repositório e aplicativo esperados;
9. sistema localizar pacote técnico em `App\Static\Rules\<Aplicativo>`;
10. sistema validar `ProductKey`, versão e `RunScript`;
11. sistema criar ou confirmar vínculo `CasRpa`;
12. sistema aplicar configurações CAS idempotentes;
13. sistema executar aplicador específico do produto quando configurado;
14. sistema registrar checkpoint por repositório, aplicativo, versão e tarefa;
15. sistema bloquear a chave utilizada após sucesso;
16. sistema retornar sucesso, aviso ou erro para a interface;
17. sistema permitir reexecução segura quando a ativação já estiver aplicada.

## Modelo funcional da ativação

### Produto gratuito

Quando `activation_mode = free`, a ação deve seguir direto para ativação. O serviço deve confirmar que o produto está publicado e que existe pacote compatível antes de aplicar configurações.

### Produto pago com token

Quando `activation_mode = token`, a interface deve solicitar chave. O serviço deve validar em `CasTkn`:

- chave existente;
- `CasTknBlq = N`;
- expiração ausente ou futura;
- descrição compatível com repositório e aplicativo;
- token ainda não utilizado.

Convenção inicial para `CasTknDsc`:

```text
APP_ACTIVATION:<CasRpsCod>:<CasAppCod>
```

Após sucesso completo, atualizar:

- `CasTknBlq = S`;
- `CasTknBlqDtt = data/hora atual`.

### Produto contratado previamente

Quando o portal já reconhecer contrato ou vínculo ativo em `CasRpa`, o serviço pode permitir ativação sem token. Nesta fase, isso deve ser tratado como reconhecimento por `CasRpa` com `CasRpaBlq = N`, sem implementar contrato comercial completo.

### Produto já ativado

Quando o checkpoint indicar pacote já aplicado e o vínculo `CasRpa` estiver ativo, a loja deve exibir ação `Abrir` ou `Atualizar`, conforme disponibilidade de pacote mais recente.

## Arquitetura proposta

### Serviços

Criar serviços em `App\Class\Manager` ou subdiretório equivalente:

- `ApplicationActivationService`;
- `ApplicationCatalogService`;
- `ApplicationPackageLocator`;
- `ApplicationActivationTokenService`;
- `ApplicationActivationCheckpointService`.

Responsabilidades:

- `ApplicationCatalogService`: carregar e validar `AppStore.json`.
- `ApplicationPackageLocator`: localizar pacote em `App\Static\Rules\<Aplicativo>`.
- `ApplicationActivationTokenService`: validar e bloquear `CasTkn`.
- `ApplicationActivationCheckpointService`: registrar e consultar status de ativação.
- `ApplicationActivationService`: orquestrar o fluxo completo.

### Controller

Atualizar o controller da loja para:

- receber POST da ação principal;
- exibir modal ou formulário de chave quando necessário;
- chamar `ApplicationActivationService`;
- exibir resultado;
- não conter regra de aplicação de pacote.

### Pacotes técnicos

Padronizar a leitura de pacotes por aplicativo:

```text
App\Static\Rules\<PackageDirectory>\Settings_<ProductName>_V<Version>.json
```

O catálogo pode indicar:

- `product_key`;
- `package_directory`;
- `package_name`;
- `package_version`;
- `run_script`.

Quando esses campos não existirem, o serviço pode derivar valores conservadores do item do catálogo.

### Aplicadores

O serviço deve mapear `RunScript` permitido para aplicadores conhecidos:

- `ApplyApplicationSettings`, para configurações CAS na base `Default`;
- `ApplyContractSettings`, para configurações operacionais do ContractFlow na base `SAAS`;
- outros aplicadores somente quando cadastrados explicitamente.

Não permitir executar classes arbitrárias vindas do JSON.

## Ajustes previstos no catálogo

O arquivo `App\Static\Menu\AppStore.json` deve ganhar dados suficientes para ativação real.

Campos recomendados:

- `package_directory`;
- `package_name`;
- `package_version`;
- `run_script`;
- `activation_mode`;
- `activation_status`;
- `requires_token`;
- `success_url`;

Exemplo conceitual:

```json
{
  "app_id": "CONTRACTFLOW",
  "product_key": "CONTRACTFLOW",
  "package_directory": "Contract",
  "package_name": "Settings_ContractFlow_V1.0.0.json",
  "package_version": "1.0.0",
  "run_script": "ApplyApplicationSettings",
  "activation_mode": "token",
  "success_url": "/Contract/Dashboard"
}
```

## Checkpoints

Na primeira fase, usar `CasPar` para registrar checkpoints CAS, seguindo o padrão existente de `ApplyApplicationSettings`.

Formato sugerido para `CasParCod`:

```text
<CasAppCod>-ACTIVATION
<CasAppCod>-ACTIVATION-TASK-0001
```

Conteúdo sugerido em `CasParTxt`:

```json
{
  "RepositoryId": "<CasRpsCod>",
  "ApplicationId": "<CasAppCod>",
  "ProductKey": "<ProductKey>",
  "Version": "1.0.0",
  "Status": "applied",
  "LastUpdated": "YYYY-MM-DD HH:MM:SS",
  "PackageHash": "<sha256>",
  "RunScript": "ApplyApplicationSettings"
}
```

Para aplicadores operacionais como ContractFlow em `SAAS`, preservar checkpoints próprios, como `CTRPackageCheckpoint`, e referenciar o resultado no checkpoint geral quando possível.

## Plano de execução

### 1. Revisar catálogo da loja

- Acrescentar campos técnicos mínimos no `AppStore.json`.
- Garantir `activation_mode` compatível com a regra desejada.
- Garantir `product_key` alinhado ao pacote técnico.
- Definir diretório de pacote para ContractFlow como `Contract`.

### 2. Criar carregador de catálogo

- Extrair leitura do JSON para serviço dedicado.
- Validar estrutura básica.
- Filtrar produtos publicados.
- Resolver produto por `slug`, `app_id` ou `product_key`.
- Retornar mensagens claras para JSON ausente, inválido ou produto não encontrado.

### 3. Criar localizador de pacote

- Receber dados do produto selecionado.
- Resolver diretório permitido dentro de `App\Static\Rules`.
- Validar que o caminho final permanece dentro da raiz esperada.
- Ler JSON com `JSON_THROW_ON_ERROR`.
- Validar `ProductKey`, `Version` e `RunScript`.
- Calcular hash do conteúdo.

### 4. Adaptar `DataPackage` ou substituir seu uso

- Remover acoplamento obrigatório a `Manager` no fluxo novo.
- Manter compatibilidade com carga inicial do Manager.
- Preferir novo localizador para ativação de loja.
- Evitar busca ampla que aceite pacote errado por acidente.

### 5. Criar serviço de token

- Implementar busca por chave em `CasTkn`.
- Validar bloqueio e expiração.
- Validar convenção `APP_ACTIVATION:<CasRpsCod>:<CasAppCod>`.
- Bloquear token somente após sucesso completo da ativação.
- Não registrar chave completa em logs.

### 6. Criar serviço de checkpoint

- Consultar se aplicativo já foi ativado no repositório.
- Registrar status `applied`, `skipped`, `warning` ou `failed`.
- Guardar versão e hash do pacote.
- Permitir reexecução quando hash for igual.
- Bloquear reexecução automática quando hash divergente exigir decisão de atualização.

### 7. Criar serviço orquestrador

- Criar `ApplicationActivationService`.
- Validar sessão e repositório.
- Validar produto.
- Validar direito de uso.
- Criar ou confirmar `CasRpa`.
- Aplicar configurações CAS com `ApplyApplicationSettings` ou adaptador equivalente.
- Executar aplicador específico quando configurado e permitido.
- Registrar checkpoint final.
- Bloquear token após sucesso.
- Retornar resultado estruturado.

### 8. Integrar com a loja visual

- Atualizar ação principal para POST controlado.
- Exibir campo de chave para produtos com `activation_mode = token`.
- Exibir mensagem de sucesso, aviso ou erro.
- Redirecionar para `success_url` somente quando ativação estiver aplicada.
- Manter modo visual sem executar ativação quando usuário apenas abre detalhes.

### 9. Preservar `/Support/Package`

- Manter rota e comportamento atual durante a transição.
- Avaliar reaproveitamento do novo localizador de pacotes em etapa posterior.
- Não remover ferramenta de suporte neste plano.

### 10. Testar fluxo

- Testar produto gratuito.
- Testar produto pago com token válido.
- Testar token inexistente.
- Testar token bloqueado.
- Testar token expirado.
- Testar token de outro repositório.
- Testar produto já ativado.
- Testar pacote ausente.
- Testar pacote com `ProductKey` divergente.
- Testar pacote com `RunScript` não permitido.
- Testar reexecução idempotente.
- Confirmar que a chave não aparece em logs.

## Critérios de aceite

- A loja executa ativação real por serviço, não por lógica direta na view.
- Produto gratuito pode ser ativado sem chave.
- Produto pago com `activation_mode = token` exige chave válida.
- Chave válida precisa estar vinculada ao repositório e aplicativo esperados.
- Chave utilizada é bloqueada após sucesso.
- `ProductKey` do JSON não é tratado como licença individual.
- Pacote técnico é localizado em `App\Static\Rules\<Aplicativo>`.
- Pacote de aplicativo diferente não é aceito por engano.
- `CasRpa` é criado ou confirmado para o repositório ativado.
- Configurações CAS são aplicadas de forma idempotente.
- Checkpoint de ativação é registrado.
- Reexecução com mesmo pacote não duplica registros.
- Erros são exibidos ao usuário de forma amigável.
- `/Support/Package` continua disponível.

## Riscos e observações

- `CasTkn` atende a primeira fase, mas pode ficar insuficiente para planos, recorrência, múltiplos usuários e auditoria comercial avançada.
- `ApplyApplicationSettings` hoje foi criado em torno do pacote Manager; a ativação multiaplicativo deve evitar depender do trait atual sem parametrização.
- Aplicadores específicos, como `ApplyContractSettings`, exigem cuidado com storage e checkpoints próprios.
- O bloqueio do token deve ocorrer somente após conclusão completa para evitar perda de chave em falha parcial.
- Operações de configuração por HTTP devem ser pequenas, idempotentes e bem validadas.
- A interface deve deixar claro quando uma ação exige chave, quando o produto já está ativo e quando a ativação depende de suporte ou contratação externa.
