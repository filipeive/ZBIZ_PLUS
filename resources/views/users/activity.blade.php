@extends('layouts.app')

@section('title', 'Atividades: ' . $user->name)
@section('page-title', 'Histórico de Atividades do Utilizador')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-2xl object-cover border border-slate-700">
            <div>
                <h2 class="text-lg font-black font-heading text-white">{{ $user->name }}</h2>
                <p class="text-xs text-slate-400">Registo completo de logs, acessos e auditoria de ações.</p>
            </div>
        </div>
        <a href="{{ route('users.show', $user) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Perfil
        </a>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data & Hora</th>
                        <th class="pb-3">Ação</th>
                        <th class="pb-3">Descrição do Evento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 text-slate-400 font-mono whitespace-nowrap">
                                {{ $activity->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 border border-slate-700 text-emerald-400 uppercase">
                                    {{ $activity->action }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-200">
                                {{ $activity->description ?? 'Sem descrição' }}
                                @if($activity->model_type && $activity->model_id)
                                    <span class="text-[10px] text-slate-500 font-mono block">#{{ class_basename($activity->model_type) }} ID: {{ $activity->model_id }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-500">Nenhum registo de atividade encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
