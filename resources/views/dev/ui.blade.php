@extends('layouts.app')

@section('title', 'Catálogo de Componentes UI')
@section('page-title', 'Design System & UI Components')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    {{-- Page Header --}}
    <x-ui.page-header
        title="Catálogo de Componentes UI"
        description="Referência de design system ZBIZ+ para temas claro e escuro"
        icon="fa-solid fa-palette"
    >
        <x-ui.button variant="secondary">
            <i class="fa-solid fa-download"></i> Exportar
        </x-ui.button>
        <x-ui.button variant="primary">
            <i class="fa-solid fa-plus"></i> Nova Ação
        </x-ui.button>
    </x-ui.page-header>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.stat-card title="Receita Total" value="142.500,00" unit="MT" subtitle="+12% este mês" icon="fa-solid fa-wallet" color="emerald" />
        <x-ui.stat-card title="Vendas Hoje" value="28" unit="talões" subtitle="Ticket médio: 850 MT" icon="fa-solid fa-receipt" color="teal" />
        <x-ui.stat-card title="Avisos Stock" value="3" unit="produtos" subtitle="Abaixo do mínimo" icon="fa-solid fa-triangle-exclamation" color="amber" />
        <x-ui.stat-card title="Dívidas Pendentes" value="18.900,00" unit="MT" subtitle="4 clientes fiados" icon="fa-solid fa-clock-rotate-left" color="rose" />
    </div>

    {{-- Buttons & Badges --}}
    <x-ui.card>
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Botões & Badges</h3>
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <x-ui.button variant="primary">Primário</x-ui.button>
            <x-ui.button variant="secondary">Secundário</x-ui.button>
            <x-ui.button variant="danger">Perigo / Excluir</x-ui.button>
            <x-ui.button variant="ghost">Fantasma</x-ui.button>
            <x-ui.button variant="primary" :loading="true">A carregar...</x-ui.button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge variant="success">Sucesso</x-ui.badge>
            <x-ui.badge variant="warning">Pendente</x-ui.badge>
            <x-ui.badge variant="danger">Atrasado</x-ui.badge>
            <x-ui.badge variant="info">Informativo</x-ui.badge>
            <x-ui.badge variant="neutral">Neutro</x-ui.badge>
            <span class="ms-4 text-xs text-slate-500">Formatação monetária:</span>
            <x-ui.money :amount="12500.5" />
        </div>
    </x-ui.card>

    {{-- Forms & Alerts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Campos de Formulário</h3>
            <div class="space-y-4">
                <div>
                    <x-ui.label for="input-demo" value="Nome do Produto" :required="true" />
                    <x-ui.input id="input-demo" placeholder="Ex: Paracetamol 500mg" />
                </div>
                <div>
                    <x-ui.label for="select-demo" value="Categoria" />
                    <x-ui.select id="select-demo">
                        <option>Medicamentos Gerais</option>
                        <option>Material de Escritório</option>
                    </x-ui.select>
                </div>
                <div>
                    <x-ui.label for="input-err" value="Preço Inválido" />
                    <x-ui.input id="input-err" value="-50" :error="true" />
                    <x-ui.field-error :messages="['O preço não pode ser negativo.']" />
                </div>
            </div>
        </x-ui.card>

        <div class="space-y-4">
            <x-ui.alert variant="success" title="Operação Concluída" :dismissible="true">
                Os dados foram gravados com sucesso na base de dados.
            </x-ui.alert>
            <x-ui.alert variant="warning" title="Atenção">
                O caixa da manhã ainda não foi fechado com contagem cega.
            </x-ui.alert>
            <x-ui.alert variant="danger" title="Erro no Processamento">
                Não foi possível conectar ao terminal de pagamento M-Pesa.
            </x-ui.alert>
            <x-ui.alert variant="info">
                Dica: Pressione F2 para abrir o Ponto de Venda rapidamente.
            </x-ui.alert>
        </div>
    </div>

    {{-- Empty State --}}
    <x-ui.empty-state
        icon="fa-solid fa-box-open"
        title="Nenhum item adicionado"
        description="Comece adicionando novos produtos ou importando o catálogo oficial de medicamentos."
    >
        <x-ui.button variant="primary">
            <i class="fa-solid fa-plus"></i> Adicionar Item
        </x-ui.button>
    </x-ui.empty-state>
</div>
@endsection
