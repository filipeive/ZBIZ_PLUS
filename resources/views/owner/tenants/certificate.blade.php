@extends('layouts.app')

@section('title', 'Certificado de Licença - ' . $tenant->name)
@section('page-title', 'Certificado de Licença de Software')

@php
    $targetPhone = $tenant->users()->first()?->phone ?? $tenant->phone ?? '';
    $rawPhone = preg_replace('/[^0-9]/', '', $targetPhone);
    if (strlen($rawPhone) === 9 && str_starts_with($rawPhone, '8')) {
        $rawPhone = '258' . $rawPhone;
    }
    $smsNotice = "Olá {$tenant->name}, a sua licença do ZBIZ+ (" . ($license->plan?->name ?? 'Plano Empresarial') . ") foi emitida com sucesso!\n\nCódigo de Ativação:\n{$license->key_code}\n\nValidade: " . ($license->expires_at?->format('d/m/Y') ?? 'Vitalício') . "\nAtivar em: " . url('/license/activate');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    copiedKey: false,
    copiedMsg: false,
    copyKey(text) {
        navigator.clipboard.writeText(text);
        this.copiedKey = true;
        setTimeout(() => this.copiedKey = false, 2500);
    },
    copyMessage(text) {
        navigator.clipboard.writeText(text);
        this.copiedMsg = true;
        setTimeout(() => this.copiedMsg = false, 2500);
    }
}">
    <!-- Actions Bar (Hidden on print) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl print:hidden">
        <div class="flex items-center gap-3">
            <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Tenant
            </a>
            <span class="text-xs text-slate-400">Emissão para <strong class="text-white">{{ $tenant->name }}</strong></span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" @click="copyKey('{{ $license->key_code }}')" 
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-2 shadow-md">
                <i class="fa-solid" :class="copiedKey ? 'fa-check' : 'fa-copy'"></i>
                <span x-text="copiedKey ? 'Chave Copiada!' : 'Copiar Chave Serial'"></span>
            </button>
            @if($rawPhone)
                <a href="https://wa.me/{{ $rawPhone }}?text={{ rawurlencode($smsNotice) }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold text-xs transition flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Mandar no WhatsApp</span>
                </a>
            @endif
            <button type="button" @click="copyMessage(`{{ addslashes($smsNotice) }}`)" 
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition flex items-center gap-2 border border-slate-700">
                <i class="fa-solid" :class="copiedMsg ? 'fa-check text-emerald-400' : 'fa-comment-sms'"></i>
                <span x-text="copiedMsg ? 'Mensagem Copiada!' : 'Copiar Mensagem SMS'"></span>
            </button>
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-print"></i> Imprimir PDF
            </button>
        </div>
    </div>

    <!-- Printable Certificate Document Card -->
    <div class="bg-gradient-to-b from-slate-900 via-slate-950 to-slate-900 border-2 border-emerald-500/40 rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden print:border-slate-300 print:bg-white print:text-black print:p-6 print:shadow-none">
        
        <!-- Watermark / Background Accent -->
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none print:hidden"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none print:hidden"></div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-800 print:border-slate-300 pb-8 gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-slate-950 text-2xl font-black shadow-xl print:border print:border-slate-400">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <div class="text-2xl font-black font-heading tracking-tight text-white print:text-black">
                        ZBIZ<span class="text-emerald-400 print:text-emerald-700">+</span>
                    </div>
                    <p class="text-xs uppercase tracking-widest text-slate-400 font-bold print:text-slate-600">Enterprise Cloud & POS Suite · Moçambique</p>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 print:border-slate-400 print:text-black">
                    CERTIFICADO OFICIAL DE LICENÇA
                </span>
                <p class="text-[11px] text-slate-500 font-mono mt-1 print:text-slate-600">Ref: ZB-LIC-{{ str_pad($license->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <!-- Certificate Body -->
        <div class="py-8 space-y-8">
            <div class="text-center space-y-2 max-w-2xl mx-auto">
                <h1 class="text-xl sm:text-2xl font-black font-heading text-white print:text-black">
                    Certificado Oficial de Ativação de Licença de Software
                </h1>
                <p class="text-xs text-slate-400 leading-relaxed print:text-slate-600">
                    Certifica-se que a empresa abaixo identificada está devidamente licenciada para uso operacional da plataforma <strong class="text-white print:text-black">ZBIZ+</strong> conforme os termos e condições do plano contratado.
                </p>
            </div>

            <!-- License Key In Mega Display Box -->
            <div class="p-6 rounded-2xl bg-slate-950 border-2 border-dashed border-emerald-500/40 text-center space-y-3 print:bg-slate-50 print:border-slate-400">
                <span class="text-[10px] uppercase font-black tracking-widest text-emerald-400 print:text-emerald-700 block">
                    CHAVE SERIAL DE LICENÇA (SOFTWARE SERIAL KEY)
                </span>
                <div class="text-2xl sm:text-3xl font-black font-mono tracking-widest text-white print:text-black select-all py-1">
                    {{ $license->key_code }}
                </div>
                <div class="flex items-center justify-center gap-2 print:hidden">
                    <button type="button" @click="copyKey('{{ $license->key_code }}')" 
                            class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid" :class="copiedKey ? 'fa-check' : 'fa-copy'"></i>
                        <span x-text="copiedKey ? 'Copiado!' : 'Copiar Chave'"></span>
                    </button>
                    @if($rawPhone)
                    <a href="https://wa.me/{{ $rawPhone }}?text={{ rawurlencode($smsNotice) }}" target="_blank"
                       class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Enviar no WhatsApp</span>
                    </a>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 print:text-slate-600">
                    Insira este código na tela de ativação (<code class="font-bold">/license/activate</code>) para desbloqueio ou renovação.
                </p>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-900/50 p-6 rounded-2xl border border-slate-800/80 print:bg-slate-50 print:border-slate-300">
                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 print:text-slate-700">Entidade Licenciada</h3>
                    <div class="space-y-1 text-xs">
                        <div class="text-white print:text-black font-bold text-sm">{{ $tenant->name }}</div>
                        <div class="text-slate-400 print:text-slate-600">NUIT: <span class="font-mono font-bold text-slate-200 print:text-black">{{ $tenant->nuit ?? 'N/D' }}</span></div>
                        <div class="text-slate-400 print:text-slate-600">Setor: <span class="capitalize text-slate-200 print:text-black">{{ $tenant->business_type }}</span></div>
                        <div class="text-slate-400 print:text-slate-600">Email: <span class="text-slate-200 print:text-black">{{ $tenant->email ?? '-' }}</span></div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 print:text-slate-700">Especificações do Plano</h3>
                    <div class="space-y-1 text-xs">
                        <div class="text-white print:text-black font-bold text-sm">{{ $license->plan?->name ?? 'Plano Standard' }}</div>
                        <div class="text-slate-400 print:text-slate-600">Modo de Operação: <span class="font-bold uppercase text-slate-200 print:text-black">{{ $license->mode }}</span></div>
                        <div class="text-slate-400 print:text-slate-600">Validade: <span class="font-mono font-bold text-emerald-400 print:text-emerald-700">{{ $license->starts_at?->format('d/m/Y') }} a {{ $license->expires_at?->format('d/m/Y') }}</span></div>
                        <div class="text-slate-400 print:text-slate-600">Estado Atual: <span class="font-bold uppercase text-emerald-400 print:text-emerald-700">{{ $license->status }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Verification & Digital Signature -->
            <div class="border-t border-slate-800 print:border-slate-300 pt-6 space-y-3 text-[11px] text-slate-400 print:text-slate-600">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <span class="text-slate-500 font-bold uppercase text-[9px] block">Assinatura Digital de Autenticidade (SHA-256 HMAC)</span>
                        <span class="font-mono text-[10px] text-slate-300 print:text-black break-all">{{ substr($license->key_hash, 0, 48) }}...</span>
                    </div>
                    <div class="sm:text-right">
                        <span class="text-slate-500 font-bold uppercase text-[9px] block">Emitido Por</span>
                        <span class="text-slate-300 print:text-black font-bold">{{ $license->issuer?->name ?? 'Administração Central ZBIZ+' }} ({{ $license->created_at->format('d/m/Y H:i') }})</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="border-t border-slate-800 print:border-slate-300 pt-8 mt-4 grid grid-cols-2 gap-8 text-center text-xs">
            <div class="space-y-1">
                <div class="border-b border-slate-700 print:border-slate-400 pb-8"></div>
                <div class="font-bold text-white print:text-black mt-2">Dono / Emissor da Plataforma</div>
                <div class="text-[10px] text-slate-500">ZBIZ+ SaaS Moçambique</div>
            </div>
            <div class="space-y-1">
                <div class="border-b border-slate-700 print:border-slate-400 pb-8"></div>
                <div class="font-bold text-white print:text-black mt-2">Representante da Entidade Licenciada</div>
                <div class="text-[10px] text-slate-500">{{ $tenant->name }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
