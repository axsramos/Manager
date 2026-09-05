# Plano - Ativação de usuários verificados via log

## Objetivo

Criar uma rotina executável por linha de comando para preencher `CasUsrActDtt` em `CasUsr` quando houver evidência recente de verificação de e-mail no log de autenticação.

A rotina deve usar o evento `register_email_verified`, gravado durante a validação do código numérico de cadastro, para marcar como ativas apenas contas recém-criadas dentro do prazo vigente de ativação.

## Estado Atual Relevante

- O prazo de ativação está fixado em código em `App/Class/Auth/AccountActivationService.php` por meio de `AccountActivationService::ACTIVATION_HOURS = 168`.
- A regra atual equivale a 7 dias.
- A verificação do e-mail registra o evento `register_email_verified` em `App/Class/Auth/RegisterEmailVerificationService.php`.
- Esse evento é gravado com `LogToFile::setLog(..., 'register_verification', 'Auth')`.
- Pelo padrão de `App/Traits/LogToFile.php`, os arquivos ficam em `Temp/Logs/Auth/register_verification_YYYYMMDD.log`.
- A linha de log possui o formato `YYYY-MM-DD HH:ii:ss - {json}`.
- O JSON do evento contém `event` e `email_hash`.
- `Config::getPathLogs()` retorna o diretório base de logs (`Temp/Logs`), portanto o caminho de Auth deve ser obtido a partir de `Config`, por exemplo `Config::getPathLogs() . DIRECTORY_SEPARATOR . 'Auth'`.

## Ajuste de Configuração

Remover a dependência de prazo fixo em código para ativação de conta.

Adicionar parâmetro:

```env
ACCOUNT_ACTIVATION_HOURS=168
```

Alterações previstas:

- Adicionar `ACCOUNT_ACTIVATION_HOURS` em `App/Core/EnvironmentVars.php` com padrão `168`.
- Expor `Config::$ACCOUNT_ACTIVATION_HOURS` em `App/Core/Config.php`.
- Validar o valor com inteiro positivo.
- Alterar `AccountActivationService::isAccessExpired()` e geração de tokens para usar `Config::$ACCOUNT_ACTIVATION_HOURS`.
- Manter `AccountActivationService::ACTIVATION_HOURS` apenas se necessário para compatibilidade, delegando o valor real para `Config`.

## Comando Proposto

Criar um script CLI dedicado:

```text
php activate-verified-users.php [--storage=Default] [--execute] [--limit=500]
```

Sem `--execute`, a rotina deve operar em modo pré-visualização (`dry-run`), exibindo quantos usuários seriam ativados sem alterar o banco.

Parâmetros:

- `--storage`: conexão de banco, padrão `Default`.
- `--execute`: confirma atualização real de `CasUsrActDtt`.
- `--limit`: limita a quantidade de usuários processados por execução, útil para operação recorrente.
- `--help`: exibe uso e exemplos.

## Classe de Serviço

Criar uma classe de domínio, por exemplo:

```text
App/Core/VerifiedAccountActivator.php
```

Responsabilidades:

- Carregar `Config`.
- Obter o prazo em horas por `Config::$ACCOUNT_ACTIVATION_HOURS`.
- Obter o diretório Auth com base em `Config::getPathLogs()`.
- Identificar arquivos de log dentro do prazo.
- Ler eventos `register_email_verified`.
- Consultar usuários elegíveis em `CasUsr`.
- Atualizar `CasUsrActDtt` somente quando houver correspondência segura.
- Retornar resumo estruturado para o script CLI.

## Usuários Elegíveis

Selecionar usuários em `CasUsr` com:

- `CasUsrActDtt IS NULL`.
- `CasUsrBlq = 'N'`, salvo se a regra de negócio decidir ativar também contas bloqueadas por ausência de ativação.
- `CasUsrAudIns >= now() - ACCOUNT_ACTIVATION_HOURS`.
- E-mail normalizado formado por `LOWER(TRIM(CasUsrLgn || CasUsrDmn))`.

Para MySQL, a expressão prática pode ser:

```sql
LOWER(CONCAT(CasUsrLgn, CasUsrDmn))
```

O hash esperado deve ser:

```php
hash('sha256', strtolower(trim($email)))
```

Esse cálculo precisa ser idêntico ao usado em `RegisterEmailVerificationService::logEvent()`.

## Leitura de Logs

Ler somente arquivos dentro do prazo limite de ativação.

Regras:

- Usar o diretório `Config::getPathLogs() . DIRECTORY_SEPARATOR . 'Auth'`.
- Considerar apenas arquivos com nome `register_verification_YYYYMMDD.log`.
- Ignorar arquivos cuja data no nome esteja fora da janela `now() - ACCOUNT_ACTIVATION_HOURS`.
- Não navegar em logs antigos acima do prazo de validade.
- Não ler diretórios de log fora de `Auth`.
- Não depender de ordenação por sistema de arquivos; ordenar por data do sufixo quando necessário.

Parsing:

- Para cada linha, separar no primeiro delimitador `" - "`.
- Validar a data/hora do prefixo.
- Ignorar linhas fora da janela.
- Decodificar o JSON após o delimitador.
- Aceitar apenas registros com:
  - `event === 'register_email_verified'`
  - `email_hash` não vazio
- Guardar os hashes em um conjunto (`array` associativo) para busca O(1).

## Atualização de `CasUsrActDtt`

Para cada usuário elegível:

1. Calcular o hash do e-mail normalizado.
2. Verificar se o hash existe nos eventos `register_email_verified` recentes.
3. Em modo `dry-run`, registrar no resumo sem alterar o banco.
4. Em modo `--execute`, atualizar:

```sql
UPDATE CasUsr
SET CasUsrActDtt = :processed_at,
    CasUsrAudUpd = :processed_at,
    CasUsrAudUsr = :audit_user
WHERE CasUsrCod = :user_id
  AND CasUsrActDtt IS NULL
  AND CasUsrAudIns >= :cutoff
```

Usar a data/hora do processamento em `CasUsrActDtt`, conforme solicitado.

## Segurança Operacional

- O comando deve falhar se executado fora de CLI.
- O modo padrão deve ser `dry-run`.
- A execução real deve exigir `--execute`.
- Não imprimir e-mails completos no terminal.
- Não gravar token, senha ou corpo de e-mail em log.
- Usar hash de e-mail nos logs da rotina.
- Usar queries parametrizadas.
- Revalidar `CasUsrActDtt IS NULL` no `UPDATE` para evitar corrida entre processos.

## Logs da Rotina

Registrar resumo em `Temp/Logs/Auth`, usando `Config` para montar o diretório.

Tipos sugeridos:

- `verified_account_activation`
- `verified_account_activation_error`

Payload sugerido:

```json
{
  "operation": "activate_verified_users",
  "mode": "dry-run|execute",
  "activation_hours": 168,
  "cutoff": "YYYY-MM-DD HH:ii:ss",
  "log_files_read": 7,
  "verified_hashes_found": 10,
  "eligible_users": 8,
  "activated_users": 8,
  "skipped_users": 0,
  "account_hashes": []
}
```

`account_hashes` deve conter hashes, não e-mails.

## Tratamento de Erros

- Diretório de log Auth inexistente: retornar sucesso com `0` eventos encontrados, desde que o banco esteja acessível.
- Arquivo de log ilegível: registrar erro e continuar com os demais arquivos, ou falhar conforme parâmetro futuro `--strict`.
- Linha com JSON inválido: ignorar e contabilizar como `invalid_log_lines`.
- Valor inválido em `ACCOUNT_ACTIVATION_HOURS`: falhar na inicialização do `Config`.
- Erro de banco: falhar com código de saída diferente de zero e registrar `verified_account_activation_error`.

## Validação Planejada

- `ACCOUNT_ACTIVATION_HOURS=168` é lido de `.env` e usado pela rotina.
- Usuário com `CasUsrActDtt IS NULL`, criado dentro do prazo e com hash em `register_email_verified` é ativado.
- Usuário criado fora do prazo não é ativado, mesmo com log existente.
- Usuário sem evento `register_email_verified` não é ativado.
- Usuário já ativado não é alterado.
- Logs antigos fora da janela não são lidos.
- Arquivos de outros diretórios de log não são lidos.
- Modo sem `--execute` não altera dados.
- Modo com `--execute` atualiza `CasUsrActDtt` com a data/hora do processamento.

## Critérios de Aceite

- Existe comando CLI para ativar usuários verificados por log.
- O prazo de ativação deixa de estar fixo exclusivamente em código e passa a vir de `Config`.
- A rotina usa `Config` para obter o caminho de logs Auth.
- A rotina lê somente logs dentro do prazo de ativação.
- A rotina identifica `register_email_verified` por `email_hash`.
- A rotina atualiza apenas contas elegíveis e ainda não ativadas.
- O modo padrão é pré-visualização e não altera o banco.
- A execução real exige opção explícita.
- O resumo da execução não expõe e-mails, tokens ou senhas.

## Arquivos Previstos Para Alteração

- `activate-verified-users.php`
- `App/Core/VerifiedAccountActivator.php`
- `App/Core/Config.php`
- `App/Core/EnvironmentVars.php`
- `.env` ou modelo equivalente de configuração
- `App/Class/Auth/AccountActivationService.php`
- `README.md`, se houver seção de comandos operacionais
