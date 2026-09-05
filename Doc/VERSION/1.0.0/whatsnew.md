# Novidades da versão 1.0.0

## Uma gestão mais centralizada e confiável

O Manager reúne em uma única plataforma a administração de contas, acessos e aplicativos dos clientes. A versão 1.0.0 estabelece uma base estável para acompanhar responsabilidades, manter os serviços organizados e reduzir atividades manuais de gestão.

## O que há de novo

- Gestão centralizada de contas, perfis, permissões e aplicativos.
- Confirmação de e-mail no cadastro, com ativação de conta e recuperação de acesso mais seguras.
- Organização do ciclo de vida das contas, incluindo bloqueios e limpeza de registros não confirmados.
- Rotinas de manutenção que apoiam a continuidade do atendimento e o acompanhamento operacional.

## Correções e proteção

- Contas já ativadas passam a ser reconhecidas corretamente durante a recuperação de acesso.
- Arquivos de configuração, logs e rotinas de administração recebem proteção contra acesso direto pela web.

## Detalhes técnicos

- A validação de propriedades no `CasUsrModel` foi ajustada para que `empty()` reconheça valores carregados pelo modelo.
- Scripts operacionais são restritos à linha de comando e a configuração Apache bloqueia diretórios e arquivos sensíveis.
