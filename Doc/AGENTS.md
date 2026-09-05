# Documentação

## RFC

- Crie RFCs em `Doc/RFC` com prefixo numérico sequencial de três dígitos, por exemplo `001-nome-da-iniciativa.md`.
- Inicie todo RFC com `Status: Rascunho`, `Status: Em revisão`, `Status: Aprovado`, `Status: Rejeitado` ou `Status: Substituído`.
- Escreva para pessoas de negócio, sem excesso de termos técnicos e sem detalhamento extenso de implementação.
- Estruture o documento com as seções `Cenário`, `Problema identificado`, `Proposta de solução`, `Alternativas consideradas` e `Decisão`.
- Use um diagrama de contexto inspirado no [C4 Model](https://c4model.com/diagrams), em Mermaid, somente quando ele melhorar a compreensão do cenário ou das relações envolvidas.

## ADR

- Crie ADRs em `Doc/ADR` com prefixo numérico sequencial de três dígitos, por exemplo `001-decisao-de-arquitetura.md`.
- Inicie todo ADR com status e referência explícita ao RFC relacionado.
- Registre contexto técnico, decisão, consequências, alternativas e informações de ambiente relevantes à decisão.
- Um ADR novo para uma decisão aprovada deve iniciar com `Status: Aprovado`.

## Tasks

- Mantenha tasks em `Doc/tasks` quando sua criação for útil para planejamento, rastreabilidade ou execução.
- Tasks podem ser criadas e desenvolvidas sem RFC ou ADR prévios. Não bloqueie uma demanda pela ausência desses documentos.
