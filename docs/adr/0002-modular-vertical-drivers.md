# ADR 0002: Arquitetura de Módulos Verticais Configuráveis por Driver

## Contexto
O sistema precisa atender setores distintos (Farmácia, Retalho, Gráficas, Serviços) sem duplicar código ou poluir controllers com múltiplos `if ($type == 'farmacia')`.

## Decisão
Implementar o padrão **BusinessProfileDriver** onde cada vertical registra sua terminologia, campos dinâmicos e regras de validação consumidos pela interface e serviços.

## Consequências
* Código limpo e desacoplado.
* Facilidade para adicionar novos verticais (ex: Restauração, Oficinas) no futuro.
