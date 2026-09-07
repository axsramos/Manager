# ADR 004 - Ativação Web Controlada de Aplicativos

**Status:** Aprovado  
**Data:** 2026-09-07  
**RFC de referência:** [RFC 003 - Ativação de Aplicativos e Módulos](../RFC/003-ativacao-de-aplicativos-e-modulos.md)

## Contexto

O ADR 001 define que operações que alteram estrutura ou dados operacionais devem ser executadas pela CLI. Essa regra reduz risco em rotinas amplas, como carga inicial, migração, manutenção e limpeza.

A RFC 003 propõe uma loja de aplicativos onde o usuário administrador ativa produtos contratados ou gratuitos pelo próprio portal. Essa ativação não instala código novo, mas aplica configurações existentes: vínculo entre repositório e aplicativo, menus, programas, funcionalidades, perfis, permissões e parâmetros.

Sem uma decisão específica, a ativação web ficaria em conflito com a orientação geral de manter operações operacionais fora de HTTP.

## Decisão

Permitir ativação web de aplicativos desde que a operação seja controlada por um serviço de aplicação dedicado, com escopo limitado ao repositório ativo da sessão e ao aplicativo solicitado.

Essa ativação web não substitui migrações, cargas iniciais, scripts de manutenção ou rotinas administrativas amplas. Ela deve executar apenas configuração funcional previamente empacotada e versionada em `App\Static\Rules\<Aplicativo>`.

O fluxo deve ser orquestrado por um serviço como `ApplicationActivationService`, chamado pela loja de aplicativos. O serviço deve:

- validar usuário autenticado e repositório ativo;
- validar o produto no catálogo e/ou em `CasApp`;
- validar o direito de uso por gratuidade, contrato, vínculo existente ou token;
- carregar pacote técnico centralizado e versionado;
- aplicar configurações de forma idempotente;
- registrar checkpoints;
- bloquear o token de ativação após uso bem-sucedido, quando houver;
- retornar resultado observável para a interface.

Na primeira fase, chaves individuais de ativação devem usar `CasTkn` com convenção de descrição vinculando repositório e aplicativo. O `ProductKey` do JSON identifica o pacote/produto, mas não é licença individual.

## Ambiente técnico

- Aplicação PHP do Manager.
- Banco `Default` para tabelas CAS.
- Base `SAAS` quando um aplicador específico do produto exigir configuração operacional própria.
- Pacotes técnicos em `App\Static\Rules`.
- Catálogo visual em `App\Static\Menu\AppStore.json`.

## Consequências

- O usuário administrador poderá ativar produtos sem intervenção manual de suporte.
- A ativação passa a ser uma operação HTTP sensível e precisa de validações explícitas de sessão, repositório, produto e direito de uso.
- O serviço de ativação deve ser idempotente para permitir reexecução controlada.
- O `/Support/Package` pode continuar existindo como ferramenta técnica de suporte, sem ser a experiência principal.
- A separação entre catálogo visual e pacote técnico evita misturar marketing com regras de configuração.

## Alternativas consideradas

### Manter ativação apenas por CLI ou suporte

Rejeitada porque impede a experiência de loja de aplicativos proposta na RFC 003 e mantém dependência operacional para cada cliente.

### Executar diretamente os aplicadores atuais a partir da tela

Rejeitada porque acopla a interface ao detalhe técnico dos pacotes e dificulta validar direito de uso, token, checkpoint e retorno padronizado.

### Criar tabelas completas de licenciamento nesta etapa

Adiada. Pode ser a melhor solução no longo prazo, mas `CasTkn` atende a primeira fase com menor impacto e já possui chave, expiração e bloqueio.

### Copiar pacotes para `Repositories`

Rejeitada por duplicar artefatos técnicos e aumentar risco de divergência entre repositórios.
