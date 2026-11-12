@extends('layouts.app')

@section('title', 'Senhas Temporárias: ' . $user->name)
@section('page-title', 'Histórico de Senhas Temporárias')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Bar -->
    <div class="flex items-center justify-between bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-2xl object-cover border border-border-default">
            <div>
                <h2 class="text-lg font-black font-heading text-content-main">{{ $user->name }}</h2>
                <p class="text-xs text-content-dimmed">Histórico de senhas descartáveis geradas para este utilizador.</p>
            </div>
        </div>
        <a href="{{ route('users.show', $user) }}" class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-2 transition" title="Voltar ao Perfil">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="hidden sm:inline">Voltar ao Perfil</span>
        </a>
    </div>

    <!-- Table Container -->
    <div class="bg-surface-card border border-border-default rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border-default text-content-dimmed uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Estado</th>
                        <th class="pb-3">Gerada Em</th>
                        <th class="pb-3">Expiração</th>
                        <th class="pb-3">Gerada Por</th>
                        <th class="pb-3">Utilizada Em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-default/60">
                    @forelse($temporaryPasswords as $temp)
                        <tr class="hover:bg-surface-hover/30 transition">
                            <td class="py-3">
                                @if($temp->used)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/30">Usada</span>
                                @elseif($temp->isExpired())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">Expirada</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Ativa</span>
                                @endif
                            </td>
                            <td class="py-3 font-mono text-content-dimmed">{{ $temp->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 font-mono text-content-dimmed">{{ $temp->expires_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 text-content-main">{{ $temp->createdBy->name ?? 'Sistema' }}</td>
                            <td class="py-3 text-content-dimmed">{{ $temp->used_at ? $temp->used_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-content-dimmed">Nenhuma senha temporária registada para este utilizador.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($temporaryPasswords->hasPages())
            <div class="mt-6 pt-4 border-t border-border-default">
                {{ $temporaryPasswords->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
