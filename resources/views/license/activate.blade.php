@extends('layouts.app')

@section('title', 'Activar Licença')
@section('page-title', 'Activação de Licença do Sistema')

@php
    $theme = tenant_theme();
    $tenant = current_tenant();
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-base flex-shrink-0"></i>
            <div>
                <strong class="font-bold block">Erro na activação:</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        
        <!-- Header -->
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-key"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white">Activar Licença do ZBIZ+</h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Insira o código serial oficial (<span class="font-mono text-teal-300">ZBIZ-XXXX-XXXX-...</span>) emitido na Nuvem. O sistema valida com o servidor central, ativa a licença e <strong class="text-slate-200">configura automaticamente a sincronização em nuvem</strong>.
                </p>
            </div>
        </div>

        @if($tenant)
        <div class="mb-6 p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-[10px] uppercase font-bold text-slate-400">Empresa Activa:</span>
                <div class="text-xs font-bold text-white">{{ $tenant->name }}</div>
            </div>
            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full {{ $tenant->isTrial() ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' }}">
                {{ $tenant->isTrial() ? 'Plano de Teste (' . $tenant->trialDaysRemaining() . 'd)' : 'Estado: ' . ucfirst($tenant->status) }}
            </span>
        </div>
        @endif

        <form method="POST" action="{{ route('license.activate.store') }}" class="space-y-5" 
              x-data="{
                  key: '{{ old('license_key') }}',
                  formatKey(val) {
                      if (!val || val.includes('.')) {
                          this.key = val ? val.trim() : '';
                          return;
                      }
                      let cleaned = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
                      if (cleaned.startsWith('ZBIZ')) {
                          cleaned = cleaned.substring(4);
                      }
                      cleaned = cleaned.substring(0, 16);
                      let parts = ['ZBIZ'];
                      for (let i = 0; i < cleaned.length; i += 4) {
                          let chunk = cleaned.substring(i, i + 4);
                          if (chunk.length > 0) {
                              parts.push(chunk);
                          }
                      }
                      this.key = parts.join('-');
                  },
                  async pasteKey() {
                      try {
                          const text = await navigator.clipboard.readText();
                          if (text) {
                              this.formatKey(text);
                          }
                      } catch (err) {
                          // Clipboard API restrita ou sem permissão
                      }
                  }
              }">
            @csrf

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="license_key" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Código de Activação ou Token de Licença *
                    </label>
                    <button type="button" @click="pasteKey()" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1 cursor-pointer">
                        <i class="fa-regular fa-paste"></i> Colar da Área de Transferência
                    </button>
                </div>
                <div class="relative">
                    <input type="text" 
                           id="license_key"
                           name="license_key" 
                           x-model="key"
                           @input="formatKey($event.target.value)"
                           required 
                           autofocus
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono text-sm tracking-wider placeholder:text-slate-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition" 
                           placeholder="ex: ZBIZ-ABCD-EFGH-1234-5678">
                </div>
                @error('license_key')
                    <p class="text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-2">
                    <i class="fa-solid fa-circle-info text-emerald-400 mr-1"></i>
                    O código serial possui o formato <span class="font-mono text-slate-300">ZBIZ-XXXX-XXXX-XXXX-XXXX</span> e é formatado automaticamente enquanto digita ou cola.
                </p>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t border-slate-800">
                <a href="{{ route('profile.edit') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold text-center transition">
                    Voltar ao Perfil
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs font-bold text-white shadow-lg flex items-center justify-center gap-2 transition hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Confirmar e Activar Licença</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Ajuda & Pagamento -->
    <div class="bg-slate-900/60 border border-slate-800/80 rounded-3xl p-6 text-center space-y-3 backdrop-blur-xl">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-300">Ainda não recebeu a sua chave ou precisa pagar?</h4>
        <p class="text-xs text-slate-400 max-w-md mx-auto">
            Contacte a equipa da <strong class="text-slate-200">Fdsmultiservices</strong> para efectuar o pagamento via M-Pesa, e-Mola ou transferência bancária e receber a sua licença de imediato por SMS.
        </p>
        <div class="pt-2">
            <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, gostaria de obter a chave de activação da licença para o ZBIZ+ da empresa ' . ($tenant?->name ?? '')) }}" 
               target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Falar no WhatsApp: (+258) 86 213 4230</span>
            </a>
        </div>
    </div>

</div>
@endsection
