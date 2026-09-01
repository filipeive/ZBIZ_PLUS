@extends('layouts.app')

@section('title', 'Modelos e Templates de Documentos')
@section('page-title', 'Modelos de Documentos')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Central de Documentos & Modelos</h2>
            <p class="text-xs text-slate-400">Gere contratos de arrendamento, recibos fiscais e acordos operacionais.</p>
        </div>
        <a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="px-4 py-2.5 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Imprimir Contrato de Renda
        </a>
    </div>

    <!-- Grid de Templates -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Contrato de Renda -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">Contrato de Arrendamento</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-4">Modelo legal de contrato para locação de imóveis e espaços comerciais com cláusulas de reabilitação e prazos de carência.</p>
            </div>
            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Gerar Modelo
                </a>
            </div>
        </div>

        <!-- Recibo de Renda -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">Recibo de Renda / Aluguer</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-4">Comprovativo de liquidação de rendas emitido a partir do registo de despesas operacionais.</p>
            </div>
            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-right"></i> Ver Despesas
                </a>
            </div>
        </div>

        <!-- Folha e Recibo de Vencimento -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">Recibos de Salário</h3>
                <p class="text-xs text-slate-400 leading-relaxed mb-4">Recibo oficial de vencimentos de colaboradores pronto para assinatura e registo no dossiê de pessoal.</p>
            </div>
            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <a href="{{ route('users.employees.payroll') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-right"></i> Aceder à Folha
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
