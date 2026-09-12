@extends('layouts.app')

@section('title', 'Mesas')
@section('page-title', 'Mesas do Restaurante')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-400">Operação de sala</p>
                <h2 class="mt-2 text-2xl font-black font-heading text-white">Mapa de mesas</h2>
                <p class="mt-1 text-sm text-slate-400">Acompanhe a ocupação da filial ativa.</p>
            </div>
            <form method="POST" action="{{ route('restaurant.tables.store') }}" class="flex flex-wrap items-end gap-2 rounded-2xl border border-slate-800 bg-slate-900/80 p-3">
                @csrf
                <label class="text-xs text-slate-400">Nome<input name="name" required placeholder="Mesa 01" class="mt-1 w-28 rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-white"></label>
                <label class="text-xs text-slate-400">Lugares<input name="capacity" type="number" min="1" max="100" value="2" required class="mt-1 w-20 rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-white"></label>
                <button class="rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-slate-950 hover:bg-orange-400">Adicionar</button>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse($tables as $table)
                @php
                    $status = [
                        'free' => ['label' => 'Livre', 'class' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300'],
                        'occupied' => ['label' => 'Ocupada', 'class' => 'border-rose-500/40 bg-rose-500/10 text-rose-300'],
                        'reserved' => ['label' => 'Reservada', 'class' => 'border-amber-500/40 bg-amber-500/10 text-amber-300'],
                        'cleaning' => ['label' => 'Limpeza', 'class' => 'border-sky-500/40 bg-sky-500/10 text-sky-300'],
                    ][$table->status] ?? ['label' => $table->status, 'class' => 'border-slate-700 bg-slate-800 text-slate-300'];
                @endphp
                <article class="rounded-2xl border {{ $status['class'] }} p-5 shadow-xl shadow-slate-950/20">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-2xl font-black text-white">{{ $table->name }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $table->capacity }} lugares</p>
                        </div>
                        <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-wider">{{ $status['label'] }}</span>
                    </div>
                    <form method="POST" action="{{ route('restaurant.tables.status', $table) }}" class="mt-6">
                        @csrf
                        @method('PATCH')
                        <label class="sr-only" for="status-{{ $table->id }}">Estado</label>
                        <select id="status-{{ $table->id }}" name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-white">
                            @foreach(['free' => 'Livre', 'occupied' => 'Ocupada', 'reserved' => 'Reservada', 'cleaning' => 'Limpeza'] as $value => $label)
                                <option value="{{ $value }}" @selected($table->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </article>
            @empty
                <div class="sm:col-span-2 xl:col-span-4 rounded-2xl border border-dashed border-slate-700 p-10 text-center text-sm text-slate-400">Ainda não existem mesas nesta filial.</div>
            @endforelse
        </div>
    </div>
@endsection