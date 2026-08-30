# ADR 0001: Adoção de Single Database Multi-Tenancy com BelongsToTenant

## Contexto
O ZBIZ+ está sendo desenhado como um SaaS comercial para o mercado moçambicano, onde os custos de hospedagem e a simplicidade operacional são fatores determinantes para a rentabilidade da startup.

## Problema
Como isolar dados entre empresas contratantes com máxima segurança sem explodir custos de servidores ou complexidade de migrações em centenas de bases isoladas?

## Alternativas Consideradas
1. Database per Tenant: Isolamento máximo, mas consumo exorbitante de conexões e RAM.
2. Schema per Tenant (Postgres): Complexo para o ecossistema Laravel padrão.
3. Single Database com `tenant_id` e Global Scope: Eficiência de custos máxima, migrações atómicas e isolamento seguro via software.

## Decisão
Adotar **Single Database com Trait `BelongsToTenant` e Global Scopes forçados**, complementado por testes automatizados de isolamento.

## Consequências
* Redução de mais de 80% nos custos de hospedagem.
* Todo novo Model de negócio deve obrigatoriamente incluir a trait `BelongsToTenant`.
