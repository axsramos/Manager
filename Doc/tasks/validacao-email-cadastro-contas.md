# Plano - validacao de e-mail no cadastro de novas contas

## Objetivo

Incluir no cadastro de novas contas em `/Auth/Register` uma etapa inicial de validacao de e-mail com envio de link de ativacao, usando `CasTkn` para administrar tokens de uso unico.

O cadastro deve continuar permitindo a criacao da conta antes da confirmacao do e-mail, mas o acesso deve ser bloqueado se a ativacao nao ocorrer dentro do prazo definido, inicialmente 24 horas.

## Escopo

- Alterar o fluxo atual de `/Auth/Register` para iniciar pela coleta do e-mail.
- Enviar e-mail de ativacao com link de uso unico.
- Criar ou ajustar tela anterior ao formulario atual de cadastro.
- Manter o formulario atual para dados adicionais: nome, sobrenome, senha e confirmacao de senha.
- Adicionar sinalizacao de ativacao em `CasUsr`.
- Utilizar `CasTkn` para gerar, validar, expirar e bloquear tokens de ativacao.
- Bloquear acesso de contas nao ativadas apos o prazo limite de ativacao.
- Prever rotina segura para limpeza de cadastros nao confirmados apos 30 dias.
- Adicionar validacao na recuperacao de senha para aceitar somente contas ativadas e nao bloqueadas.

## Fora do escopo

- Implementar login social com Google.
- Integrar com API OAuth do Google.
- Exigir confirmacao do e-mail antes de criar a conta.
- Alterar a regra de senha atual, exceto quando necessario para encaixar no novo fluxo.

## Estado atual relevante

- `App/Controllers/Auth/Register.php` exibe e processa diretamente o formulario completo de cadastro.
- `App/Class/Auth/RegisterClass.php` cria a conta e executa login ao final do cadastro.
- `App/Core/ServiceMail.php` ja possui envio de e-mail transacional com templates HTML e `AltBody`.
- `App/Static/Template/email_register.html` e `email_recovery.html` usam placeholders parametrizados.
- `App/Models/CAS/CasUsrModel.php` representa a tabela `CasUsr`.
- `App/Metadata/CAS/CasUsrMD.php` ainda nao possui campo de ativacao de conta.
- `App/Models/CAS/CasTknModel.php` representa a tabela `CasTkn`.
- `App/Metadata/CAS/CasTknMD.php` ja possui `CasTknKey`, `CasTknKeyExp`, `CasTknBlq` e `CasTknBlqDtt`.
- `App/Class/Auth/RecoveryClass.php` atualmente valida apenas existencia, bloqueio e token de recuperacao.

## Estado final esperado

O fluxo de cadastro deve ficar dividido em etapas:

1. Usuario acessa `/Auth/Register`.
2. Tela inicial solicita apenas o e-mail.
3. Tela inicial exibe tambem espaco opcional para botao "Continuar com Google", sem implementar a funcionalidade.
4. Ao avancar, a aplicacao valida formato e disponibilidade do e-mail.
5. A aplicacao gera token de ativacao em `CasTkn` com expiracao de 24 horas.
6. A aplicacao envia e-mail com link de ativacao.
7. Usuario segue para o formulario de dados adicionais.
8. Conta e criada mesmo que o e-mail ainda nao tenha sido ativado.
9. Se a conta nao for ativada em ate 24 horas, o login deve ser bloqueado.
10. Ao acessar o link de ativacao, o token deve ser consumido uma unica vez e a conta deve receber data/hora de ativacao.
11. Contas nao ativadas por mais de 30 dias podem ser removidas por rotina segura de limpeza.

## Alteracoes de dados

Adicionar campo em `CasUsr`:

| Campo | Tipo sugerido | Nulo | Regra |
| --- | --- | --- | --- |
| `CasUsrActDtt` | `datetime` | Sim | `NULL` indica conta ainda nao ativada; valor preenchido indica data/hora de ativacao. |

Atualizar tambem:

- `App/Metadata/CAS/CasUsrMD.php`
- `App/Models/CAS/CasUsrModel.php`, se necessario para queries especificas
- scripts ou rotinas de criacao/atualizacao da estrutura MySQL
- JSONs ou pacotes de carga que espelham campos de `CasUsr`, quando aplicavel

Observacao: o campo de bloqueio existente `CasUsrBlq` deve continuar indicando bloqueio operacional da conta. A ausencia de ativacao deve ser avaliada por `CasUsrActDtt IS NULL` combinada com o prazo do token ou com a data de criacao da conta.

## Uso de `CasTkn`

Criar tokens de ativacao usando `CasTkn` com uma identificacao propria, separada dos tokens de recuperacao de senha.

Padrao sugerido:

- `CasTknCod`: identificador deterministico ou UUID seguro para o token de ativacao.
- `CasTknDsc`: prefixo funcional, por exemplo `ACCOUNT_ACTIVATION {email_normalizado}`.
- `CasTknKey`: chave aleatoria compativel com URL.
- `CasTknKeyExp`: data/hora atual + 24 horas.
- `CasTknBlq`: `N` enquanto token estiver disponivel.
- `CasTknBlqDtt`: preenchido quando o token for usado, expirado ou invalidado.

Regras:

- O link deve conter somente `CasTknKey`, nao o e-mail em texto aberto.
- O token deve ser de uso unico.
- Ao ativar com sucesso, gravar `CasUsrActDtt` e bloquear o token usado.
- Se o token estiver expirado, nao ativar a conta com aquele token.
- Se um novo token for gerado, tokens anteriores da mesma finalidade/conta devem ser bloqueados para evitar multiplas URLs ativas.
- Nao registrar o token completo em logs.

## Geracao do link

Criar rota para receber ativacao, por exemplo:

```text
/Auth/Register/Activate/{token}
```

O link enviado por e-mail deve ser montado com `Config::$APP_URL`, por exemplo:

```text
{APP_URL}/Auth/Register/Activate/{CasTknKey}
```

O token precisa ser seguro para URL. Reaproveitar o criterio ja adotado para chaves compativeis com URL no projeto, evitando caracteres como barras, acentos, espacos ou simbolos que exijam escaping especial.

## Tela inicial de cadastro

Criar nova tela ou alterar a tela atual para etapa inicial:

- Campo `inputEmail`.
- Botao `Avancar`.
- Area prevista para botao opcional de login social com Google.
- Mensagem informando que sera enviado um e-mail de ativacao.
- Sem solicitar nome, sobrenome ou senha nesta etapa.

Ao avancar:

- validar preenchimento e formato do e-mail;
- verificar se ja existe conta com o e-mail informado;
- gerar token de ativacao;
- enviar e-mail de ativacao;
- preservar o e-mail para a etapa seguinte;
- encaminhar para formulario com nome, sobrenome, senha e confirmacao.

## Formulario de dados adicionais

O formulario atual deve ser reaproveitado ou ajustado para receber o e-mail ja validado na etapa inicial.

Regras:

- O e-mail nao deve poder ser trocado silenciosamente na segunda etapa.
- Se o e-mail for exibido, preferir campo somente leitura.
- A conta deve ser criada com `CasUsrActDtt = NULL`.
- O envio do e-mail de boas-vindas atual deve ser reavaliado para nao conflitar com o e-mail de ativacao.
- Se permanecer o e-mail de boas-vindas, sua mensagem deve deixar claro quando a conta ainda depende de ativacao.

## Template de e-mail

Criar novo template:

```text
App/Static/Template/email_activation.html
```

O template deve seguir o padrao dos templates atuais:

- HTML conservador para clientes de e-mail.
- Estrutura baseada em `table`.
- Estilos inline.
- Sem JavaScript, formularios, iframe, fontes externas ou dependencias remotas.
- Conteudo parametrizado via `ServiceMail`.
- `AltBody` em texto simples.

Conteudo obrigatorio:

- nome da aplicacao;
- e-mail da conta ou saudacao generica;
- link de ativacao;
- aviso de que o link e de uso unico;
- aviso de expiracao em 24 horas;
- orientacao de que a conta podera ter o acesso bloqueado se nao for confirmada dentro do prazo;
- contato de suporte parametrizado.

## ServiceMail

Adicionar metodo especifico, por exemplo:

```php
sendMailActivation(string $name, string $email, string $activationUrl, string $expiresAt): bool
```

O metodo deve:

- carregar `email_activation.html`;
- preencher placeholders com dados escapados;
- definir assunto parametrizado, por exemplo `Ative sua conta - {APP_NAME}`;
- registrar logs estruturados em `Temp/Logs/Mail`;
- retornar `false` em qualquer falha sem quebrar a aplicacao;
- nao gravar token completo nem corpo do e-mail em log.

## Ativacao da conta

Ao acessar o link:

- localizar token em `CasTkn` por `CasTknKey`;
- verificar se `CasTknBlq = 'N'`;
- verificar se `CasTknKeyExp` ainda esta vigente;
- localizar a conta associada;
- se a conta existir e nao estiver ativada, gravar `CasUsrActDtt = now()`;
- bloquear o token usado com `CasTknBlq = 'S'` e `CasTknBlqDtt = now()`;
- exibir tela de sucesso.

Se o token estiver expirado:

- exibir tela de falha por expiracao;
- se a conta ainda existir em `CasUsr` e nao estiver ativada, permitir gerar novo token e reenviar o e-mail;
- se a conta ja tiver sido removida pela limpeza, orientar novo cadastro.

Se o token ja tiver sido usado:

- exibir mensagem informando que o link ja foi utilizado ou esta invalido;
- nao ativar novamente;
- nao gerar novo token automaticamente sem validar a conta associada.

## Bloqueio de acesso

Adicionar validacao no login para impedir acesso quando:

- `CasUsrActDtt IS NULL`;
- a conta foi criada ha mais de 24 horas; ou
- o token de ativacao vigente expirou.

O bloqueio pode ser logico, sem necessariamente alterar `CasUsrBlq`, desde que a mensagem e a regra sejam claras. Se optar por alterar `CasUsrBlq = 'S'`, documentar a diferenca entre bloqueio por falta de ativacao e bloqueio administrativo.

## Recuperacao de senha

Adicionar validacao em `App/Class/Auth/RecoveryClass.php`:

- conta precisa existir;
- conta nao pode estar bloqueada;
- conta precisa estar ativada (`CasUsrActDtt IS NOT NULL`);
- se a conta estiver pendente de ativacao, nao gerar token de recuperacao de senha.

Mensagem esperada:

- informar que a conta ainda precisa ser ativada;
- quando aplicavel, orientar o reenvio do e-mail de ativacao.

## Limpeza de cadastros nao confirmados

Prever comando seguro de console para remover ou bloquear cadastros nao ativados ha mais de 30 dias.

Sugestao:

```text
php console.php auth:cleanup-unconfirmed-accounts --days=30
```

Regras:

- operar apenas sobre contas com `CasUsrActDtt IS NULL`;
- considerar `CasUsrAudIns` ou campo equivalente de criacao;
- nao remover contas ativadas;
- invalidar tokens de ativacao vinculados;
- registrar resumo em `Temp/Logs/Auth` ou `Temp/Logs/Audit`;
- suportar modo de pre-visualizacao, por exemplo `--dry-run`;
- exigir confirmacao explicita quando executado sem `--dry-run`, caso o padrao dos comandos do projeto permita.

## Logs

Registrar eventos estruturados para:

- token de ativacao criado;
- e-mail de ativacao enviado;
- falha no envio do e-mail;
- token usado com sucesso;
- tentativa com token expirado;
- tentativa com token ja utilizado;
- reenvio de token;
- conta bloqueada por falta de ativacao;
- limpeza de cadastros nao confirmados.

Nao registrar:

- senha;
- token completo;
- corpo do e-mail;
- dados pessoais alem do minimo necessario.

Usar e-mail mascarado e hash normalizado quando precisar correlacionar eventos.

## Mensagens e telas

Prever mensagens para:

- e-mail enviado para ativacao;
- falha ao enviar e-mail;
- conta criada com ativacao pendente;
- ativacao concluida com sucesso;
- link expirado;
- link invalido;
- link ja utilizado;
- conta removida por falta de confirmacao;
- recuperacao de senha bloqueada porque a conta ainda nao foi ativada.

## Validacao planejada

### Cadastro

- Cadastro com e-mail valido envia token e avanca para dados adicionais.
- Cadastro com e-mail invalido nao cria token.
- Cadastro com e-mail ja existente nao cria token duplicado indevido.
- Conta criada fica com `CasUsrActDtt = NULL`.
- Usuario com conta pendente dentro de 24 horas recebe tratamento esperado.
- Usuario com conta pendente apos 24 horas nao consegue acessar.

### Ativacao

- Link valido ativa a conta e bloqueia token.
- Reuso do mesmo link nao ativa novamente.
- Link expirado nao ativa a conta.
- Link expirado permite reenvio se a conta ainda existir e estiver pendente.
- Link invalido exibe falha controlada.

### Recuperacao

- Conta ativada e nao bloqueada gera token de recuperacao.
- Conta bloqueada nao gera token de recuperacao.
- Conta nao ativada nao gera token de recuperacao.

### Limpeza

- `--dry-run` lista contas elegiveis sem remover.
- Execucao real remove ou bloqueia apenas contas nao ativadas ha mais de 30 dias.
- Tokens de ativacao vinculados sao invalidados.
- Contas ativadas nao sao afetadas.

## Criterios de aceite

- `/Auth/Register` inicia pela etapa de e-mail.
- Existe previsao visual para login social Google sem implementar OAuth.
- E-mail de ativacao e enviado com link de uso unico.
- Conta e criada mesmo sem ativacao imediata.
- `CasUsr` possui data/hora de ativacao.
- Conta nao ativada em ate 24 horas fica impedida de acessar.
- Link de ativacao valido grava ativacao e invalida token.
- Link expirado nao ativa a conta.
- Link expirado permite novo envio quando a conta ainda existe.
- Recuperacao de senha aceita somente contas ativadas e nao bloqueadas.
- Existe rotina segura para limpeza de cadastros nao confirmados apos 30 dias.
- Logs permitem auditar criacao, envio, ativacao, expiracao, bloqueio e limpeza sem expor senha ou token completo.

## Arquivos previstos para alteracao na implementacao

- `App/Controllers/Auth/Register.php`
- `App/Class/Auth/RegisterClass.php`
- `App/Class/Auth/RecoveryClass.php`
- `App/Core/ServiceMail.php`
- `App/Models/CAS/CasUsrModel.php`
- `App/Models/CAS/CasTknModel.php`
- `App/Metadata/CAS/CasUsrMD.php`
- `App/Static/Template/email_activation.html`
- views de cadastro e ativacao em `App/Views/SBAdmin`
- mensagens em `App/Shared/MessageDictionary.php`
- rotinas de banco/pacotes de schema MySQL
- rotina de console para limpeza
- documentacao tecnica no `README.md`, se o comando de limpeza for criado
