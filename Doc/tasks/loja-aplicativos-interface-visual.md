> **Status: IMPLEMENTADO**

# Plano - interface visual da loja de aplicativos

## Objetivo

Implementar a interface visual da loja de aplicativos do Manager, conforme a RFC 004, permitindo que o usuário administrador descubra produtos disponíveis, alterne entre visualização em cards e lista, abra uma página de detalhes do produto e veja uma ação principal de ativação no card e no topo da página do produto.

Documentos de referência:

- `Doc\RFC\003-ativacao-de-aplicativos-e-modulos.md`;
- `Doc\RFC\004-loja-de-aplicativos.md`.

Este plano cobre somente a construção da experiência visual, leitura do catálogo e navegação. As rotinas de configuração, aplicação de pacotes, criação de permissões e validação real de licença mencionadas na RFC 003 ficam fora deste trabalho.

## Escopo

- Criar a tela de catálogo da loja de aplicativos.
- Criar alternância visual entre modo cards e modo lista.
- Criar estrutura JSON em `App\Static\Menu` para alimentar a loja.
- Criar uma página genérica de detalhes de produto, carregada por `slug` ou identificador equivalente.
- Criar sections específicas por produto para compor o conteúdo comercial da página de detalhes.
- Reutilizar o componente visual de ação principal no card e no topo da página do produto.
- Exibir estados visuais simulados ou derivados do catálogo, como `Ativar`, `Informar chave`, `Atualizar`, `Abrir` e `Indisponível`.
- Prever localização de imagens e assets por produto e por tema visual.
- Usar SBAdmin como tema padrão.
- Permitir que o catálogo declare outro tema, como `Martex`, quando os assets estiverem disponíveis.
- Manter textos exibidos ao usuário em pt-BR.

## Fora do escopo

- Implementar `ApplicationActivationService`.
- Validar token real em `CasTkn`.
- Bloquear chave de ativação após uso.
- Criar ou alterar vínculos em `CasRpa`.
- Executar `ApplyApplicationSettings`, `ApplyContractSettings` ou qualquer rotina de pacote.
- Inserir ou atualizar permissões em `CasApf`, `CasAfu`, `CasPfu` ou tabelas correlatas.
- Criar fluxo comercial, checkout, assinatura, pagamento ou integração externa.
- Criar painel administrativo para editar o catálogo.
- Migrar o catálogo JSON para banco de dados.
- Publicar versão, gerar build ou alterar changelog.

## Estado atual relevante

- A tela `/Support/Package` existe, mas é técnica e voltada a pacote/chave.
- A RFC 003 recomenda uma loja separada da tela `/Support/Package`.
- A RFC 004 define a loja como catálogo visual baseado em JSON em `App\Static\Menu`.
- O projeto já usa `App\Static\Menu\SideMenu.json` para estrutura estática de navegação.
- O portal usa SBAdmin como base visual predominante.
- Existem views SBAdmin organizadas por domínio em `App\Views\SBAdmin\Manager`, `App\Views\SBAdmin\Support` e `App\Views\SBAdmin\Contract`.
- O ContractFlow já possui pacote técnico em `App\Static\Rules\Contract\Settings_ContractFlow_V1.0.0.json`, mas sua aplicação técnica não deve ser implementada neste plano.

## Estado final esperado

A interface deve permitir:

1. acessar a loja por uma rota do Manager;
2. carregar os produtos publicados a partir de um JSON em `App\Static\Menu`;
3. ordenar os produtos pelo campo `order`;
4. ocultar produtos com status não publicável, quando definido no catálogo;
5. visualizar produtos em cards responsivos;
6. alternar para visualização em lista;
7. ver título, descrição, tag, valor, prazo, imagem, categoria e ação principal;
8. abrir uma página genérica de detalhes do produto;
9. renderizar no topo da página do produto o mesmo objetivo de ação exibido no card;
10. carregar uma section específica do produto no corpo da página;
11. exibir fallback visual quando imagem, section ou tema específico não existir;
12. manter `/Support/Package` sem alterações funcionais.

## Estrutura proposta do catálogo

Criar um arquivo como:

- `App\Static\Menu\AppStore.json`

Estrutura conceitual:

```json
{
  "version": "1.0.0",
  "items": [
    {
      "app_id": "CONTRACTFLOW",
      "product_key": "CONTRACTFLOW",
      "slug": "contractflow",
      "title": "ContractFlow",
      "summary": "Gestão dinâmica de contratos, franquias e consumo.",
      "description": "Controle contratos, itens, saldos e consumo por repositório.",
      "tag": "Pago",
      "badge": "Novo",
      "category": "Contratos",
      "price": "Sob consulta",
      "term": "Mensal",
      "order": 10,
      "status": "published",
      "theme": "SBAdmin",
      "cover_image": "/SBAdmin/images/products/contractflow/cover.jpg",
      "icon": "fas fa-file-contract",
      "assets_path": "/SBAdmin/images/products/contractflow/",
      "details_view": "App/Views/SBAdmin/AppStore/Products/ContractFlowSection.php",
      "activation_mode": "token",
      "launch_url": "/Contract/Dashboard",
      "learn_more_url": "/Manager/AppStore/Product/contractflow",
      "features": [
        "Contratos por repositório",
        "Controle de franquias",
        "Ledger de consumo"
      ],
      "audience": "Equipes operacionais e financeiras"
    }
  ]
}
```

Campos mínimos obrigatórios:

- `title`;
- `description`;
- `tag`;
- `price`;
- `term`;
- `order`.

Campos recomendados:

- `app_id`;
- `product_key`;
- `slug`;
- `summary`;
- `badge`;
- `category`;
- `status`;
- `theme`;
- `cover_image`;
- `icon`;
- `assets_path`;
- `details_view`;
- `activation_mode`;
- `launch_url`;
- `learn_more_url`;
- `features`;
- `audience`.

## Rotas e navegação

Rotas sugeridas:

- `/Manager/AppStore`
- `/Manager/AppStore/Product/<slug>`

Controller sugerido:

- `App\Controllers\Manager\AppStore.php`

Responsabilidades do controller:

- carregar o JSON do catálogo;
- filtrar itens publicáveis;
- ordenar por `order` e `sort_group`, quando existir;
- resolver produto por `slug`;
- montar `FormDesign`, breadcrumbs, scripts e dados para a view;
- renderizar a loja e a página genérica de produto;
- não executar ativação real neste plano.

## Views previstas

Diretório padrão:

- `App\Views\SBAdmin\AppStore`

Arquivos sugeridos:

- `AppStoreView.php`;
- `AppStoreCatalog.php`;
- `AppStoreProduct.php`;
- `Partials\AppStoreAction.php`;
- `Partials\AppStoreCard.php`;
- `Partials\AppStoreList.php`;
- `Products\ContractFlowSection.php`;
- `Products\DefaultProductSection.php`.

Responsabilidades:

- `AppStoreView.php`: composição base com partials SBAdmin.
- `AppStoreCatalog.php`: tela de catálogo, filtros simples e alternância cards/lista.
- `AppStoreProduct.php`: página genérica de produto.
- `AppStoreAction.php`: componente visual da ação principal.
- `AppStoreCard.php`: renderização de cada card.
- `AppStoreList.php`: renderização em tabela/lista.
- `Products\*Section.php`: conteúdo comercial específico de cada produto.

## Diretrizes visuais

- A primeira tela deve ser a loja, não uma página explicativa.
- A visualização em cards deve priorizar descoberta visual e escaneabilidade.
- A visualização em lista deve priorizar comparação rápida.
- Cards devem conter imagem real ou asset próprio do produto, evitando placeholders genéricos quando houver imagem disponível.
- Tags comerciais devem ser visíveis e curtas.
- O botão principal deve ser destacado, mas sem competir com navegação global.
- A página de produto deve manter a ação principal no topo.
- O topo da página de produto deve mostrar título, resumo, valor, prazo, tag e botão de ação.
- O conteúdo comercial específico deve ficar abaixo do topo, em section própria.
- Quando `theme` não for reconhecido, usar SBAdmin.
- Quando `cover_image` não existir, usar fallback consistente do produto ou da loja.
- A interface deve funcionar em desktop e mobile, sem sobreposição de textos ou botões.

## Estados visuais da ação principal

Neste plano, os estados podem ser simulados ou derivados do catálogo. A integração real fica para a etapa de ativação.

Estados previstos:

| Estado | Texto sugerido | Uso visual |
| --- | --- | --- |
| `free` | `Ativar` | Produto gratuito disponível |
| `token` | `Informar chave` | Produto pago que exige chave |
| `contract` | `Ativar` | Produto pago já reconhecido como contratado |
| `trial` | `Iniciar teste` | Avaliação temporária |
| `active` | `Abrir` | Produto já ativado |
| `update` | `Atualizar` | Reaplicação ou atualização disponível |
| `unavailable` | `Indisponível` | Produto bloqueado, oculto ou incompatível |
| `external` | `Solicitar contratação` | Fluxo comercial fora do portal |

## Plano de execução

### 1. Criar catálogo estático

- Criar `App\Static\Menu\AppStore.json`.
- Definir schema inicial informal com `version` e `items`.
- Criar ao menos um item para `ContractFlow`.
- Criar um item exemplo gratuito, se útil para validar variação de tag e ação.
- Garantir `order` numérico para ordenação.

### 2. Criar controller da loja

- Criar `App\Controllers\Manager\AppStore.php`.
- Proteger a rota com `validateAccess` quando o programa estiver cadastrado.
- Enquanto a autorização final não existir, seguir o padrão de acesso adotado para telas Manager.
- Implementar método `index()` para catálogo.
- Implementar método `product($slug)` para página genérica.
- Tratar JSON ausente ou inválido com mensagem amigável.

### 3. Criar views de catálogo

- Criar view base SBAdmin.
- Criar layout de catálogo com cabeçalho compacto.
- Criar alternância cards/lista.
- Renderizar cards ordenados.
- Renderizar lista comparativa.
- Exibir estado vazio quando não houver produtos publicados.

### 4. Criar componente visual de ação

- Criar partial reutilizável para botão principal.
- Mapear `activation_mode` e estado visual para texto, cor e link.
- No primeiro momento, ação real pode apontar para detalhe, modal visual ou botão desabilitado conforme o estado.
- Evitar executar rotinas de configuração.

### 5. Criar página genérica de produto

- Carregar produto por `slug`.
- Exibir topo com imagem/banner, título, resumo, preço, prazo, tag e ação.
- Incluir `details_view` quando existir.
- Usar `DefaultProductSection.php` quando a section específica não existir.
- Preservar layout responsivo.

### 6. Criar section inicial do ContractFlow

- Criar `Products\ContractFlowSection.php`.
- Apresentar benefícios, recursos e casos de uso do ContractFlow.
- Usar texto comercial curto, com foco em decisão.
- Prever imagens pelo caminho declarado no catálogo.

### 7. Integrar navegação

- Avaliar inclusão da loja no menu lateral em `App\Static\Menu\SideMenu.json`.
- Definir rótulo como `Loja de Aplicativos` ou `Aplicativos`.
- Garantir que a rota da página de produto seja acessível a partir de cada card e da lista.

### 8. Validar experiência visual

- Validar renderização em desktop.
- Validar renderização em mobile.
- Conferir alternância cards/lista.
- Conferir fallback de imagem.
- Conferir fallback de section.
- Conferir textos em pt-BR.
- Conferir que nenhum botão executa aplicação técnica de pacote.

## Critérios de aceite

- A loja carrega produtos a partir de `App\Static\Menu\AppStore.json`.
- Os produtos são ordenados por `order`.
- A tela exibe cards responsivos com informações comerciais mínimas.
- A tela permite alternar para visualização em lista.
- Cada produto publicado possui link para uma página de detalhes.
- A página de detalhes usa uma estrutura genérica e carrega section específica quando configurada.
- O componente visual de ação aparece no card e no topo da página do produto.
- O ContractFlow possui card e página de detalhes inicial.
- Produtos sem imagem ou section específica possuem fallback visual.
- Nenhuma rotina de ativação/configuração da RFC 003 é executada.

## Riscos e observações

- O catálogo estático facilita a primeira entrega, mas exige alteração de arquivo para publicar ou editar produtos.
- A ausência de ativação real pode exigir textos claros para não prometer conclusão técnica ao usuário.
- A inclusão no menu lateral depende dos cadastros de programa e autorização; se esses cadastros forem deixados para etapa futura, a rota pode existir antes de aparecer no menu.
- Temas externos como `Martex` devem ser tratados como opcionais. A loja deve continuar funcional com SBAdmin.
- O JSON de catálogo não deve carregar regras técnicas de pacote para evitar acoplamento com a RFC 003.

## ADR

Não é necessário criar ADR para este plano. A decisão de produto e arquitetura de alto nível já está coberta pelas RFCs 003 e 004. Este documento apenas organiza a execução da interface visual, sem decidir persistência nova, contrato de ativação ou arquitetura definitiva de licenças.
