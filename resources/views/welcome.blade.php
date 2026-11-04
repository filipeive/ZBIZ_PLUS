<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="ZBIZ+">
<title>ZBIZ+ | Sistema de gestão para comércio, farmácias e serviços em Moçambique</title>
<meta name="description" content="Frente de caixa, stock, fiados, M-Pesa e e-Mola, IVA Modelo A e lotes FEFO num só sistema. Funciona no computador e no telemóvel, com ou sem internet.">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  [x-cloak] { display: none !important; }
  body { font-variant-numeric: tabular-nums; }
  details > summary { list-style: none; }
  details > summary::-webkit-details-marker { display: none; }
  :focus-visible {
    outline: 2px solid #166534;
    outline-offset: 2px;
  }
</style>
</head>
<body class="font-sans text-ink bg-white antialiased"
x-data="{
  sector: 'retail',
  receiptMode: 'retail',
  cycle: 'monthly',
  mobileMenu: false,
  deferredPrompt: null,
  init() {
    window.addEventListener('beforeinstallprompt', e => {
      e.preventDefault();
      this.deferredPrompt = e;
    });
  },
  price(base) {
    const f = this.cycle === 'annual' ? 0.8 : (this.cycle === 'semiannual' ? 0.9 : 1);
    return String(Math.round(base * f)).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }
}">

{{-- Barra de topo discreta: Multiplataforma (Computador + Android + iOS) --}}
<div class="bg-surface border-b border-line text-xs text-muted">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-2 flex flex-wrap items-center justify-between gap-2">
    <div class="flex items-center gap-2">
      <span class="inline-block h-2 w-2 rounded-full bg-brand"></span>
      <span>Sistema de gestão em <strong class="text-ink font-semibold">Moçambique</strong> · Computador, Android e iPhone</span>
    </div>
    <div class="flex items-center gap-4">
      <a href="#aplicacao" class="inline-flex items-center gap-1.5 font-medium text-ink hover:text-brand transition-colors">
        <svg class="w-3.5 h-3.5 text-brand" viewBox="0 0 24 24" fill="currentColor"><path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4483-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993.0001.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993 0 .5511-.4483.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.2439 13.8533 7.8508 12 7.8508s-3.5902.3931-5.1367 1.0989L4.841 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3435-4.1021-2.6892-7.5743-6.1185-9.4396"/></svg>
        <span>App Android (APK)</span>
      </a>
      <span class="text-line">|</span>
      <a href="#aplicacao" class="inline-flex items-center gap-1.5 font-medium text-ink hover:text-brand transition-colors">
        <svg class="w-3.5 h-3.5 text-ink" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.33c.64-.78 1.08-1.86.96-2.94-.93.04-2.06.62-2.72 1.4-.58.68-1.1 1.79-.96 2.84 1.04.08 2.08-.52 2.72-1.3"/></svg>
        <span>iPhone &amp; iPad</span>
      </a>
      <span class="hidden sm:inline text-line">|</span>
      <a href="https://wa.me/258862134230" target="_blank" rel="noopener noreferrer" class="hidden sm:inline font-medium hover:text-brand transition-colors">
        WhatsApp: (+258) 86 213 4230
      </a>
    </div>
  </div>
</div>

{{-- Cabeçalho --}}
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-line">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
    {{-- Logo oficial --}}
    <a href="/" class="group flex items-center gap-2.5 shrink-0 rounded-lg focus-visible:outline-none">
      <span class="relative flex h-9 w-9 items-center justify-center rounded-lg bg-ink shadow-sm ring-1 ring-ink/10 overflow-hidden transition group-hover:ring-brand/40">
        <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="h-7 w-7 object-contain">
      </span>
      <div class="flex flex-col">
        <span class="text-[1.3rem] font-bold tracking-tight leading-none text-ink">
          ZBIZ<span class="text-brand">+</span>
        </span>
        <span class="text-[10px] text-muted font-medium tracking-wide mt-0.5 hidden sm:block">Gestão &amp; POS</span>
      </div>
    </a>

    <nav class="hidden lg:flex items-center gap-0.5 text-[13.5px] font-medium text-muted whitespace-nowrap" aria-label="Principal">
      <a href="#funcionalidades" class="px-2.5 py-2 rounded-md hover:text-ink hover:bg-surface transition-colors">Funcionalidades</a>
      <a href="#segmentos" class="px-2.5 py-2 rounded-md hover:text-ink hover:bg-surface transition-colors">Segmentos</a>
      <a href="#aplicacao" class="px-2.5 py-2 rounded-md hover:text-ink hover:bg-surface transition-colors flex items-center gap-1.5">
        <span>Aplicação</span>
        <span class="rounded bg-brand-soft px-1.5 py-0.5 text-[10px] font-semibold text-brand">Android &amp; iOS</span>
      </a>
      <a href="#planos" class="px-2.5 py-2 rounded-md hover:text-ink hover:bg-surface transition-colors">Planos e Preços</a>
      <a href="#experimente-gratis" class="px-2.5 py-2 rounded-md hover:text-ink hover:bg-surface transition-colors">Experimente Grátis</a>
      <a href="#perguntas" class="px-2.5 py-2 rounded-md hover:text-ink hover:bg-surface transition-colors">Perguntas</a>
    </nav>

    <div class="flex items-center gap-2 text-sm whitespace-nowrap shrink-0">
      @auth
        <a href="{{ route('dashboard.index') }}"
           class="inline-flex items-center justify-center bg-brand hover:bg-brand-dark text-white font-semibold px-4 py-2 rounded-lg shadow-sm transition-colors">
          Abrir painel
        </a>
      @else
        <a href="{{ route('login') }}"
           class="inline-flex px-3 py-2 font-medium text-ink hover:text-brand rounded-lg hover:bg-surface transition-colors">
          Entrar
        </a>
        <a href="{{ route('register') }}"
           class="inline-flex items-center justify-center bg-brand hover:bg-brand-dark text-white font-semibold px-4 py-2 rounded-lg shadow-sm transition-colors">
          Experimente grátis
        </a>
      @endauth

      {{-- Botão Menu Mobile --}}
      <button type="button" @click="mobileMenu = !mobileMenu" aria-label="Abrir menu"
              class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg border border-line text-ink hover:bg-surface">
        <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        <svg x-cloak x-show="mobileMenu" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
  </div>

  {{-- Gaveta de Navegação Mobile --}}
  <div x-cloak x-show="mobileMenu" @click.outside="mobileMenu = false"
       class="lg:hidden border-t border-line bg-white px-4 py-4 space-y-1 text-sm">
    <a href="#funcionalidades" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-medium text-ink hover:bg-surface">Funcionalidades</a>
    <a href="#segmentos" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-medium text-ink hover:bg-surface">Segmentos de negócio</a>
    <a href="#aplicacao" @click="mobileMenu = false" class="flex items-center justify-between px-3 py-2 rounded-lg font-medium text-brand bg-brand-soft/60">
      <span>Aplicação Android (APK) e iPhone</span>
      <span class="text-xs font-semibold">Instalar →</span>
    </a>
    <a href="#planos" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-medium text-ink hover:bg-surface">Planos e preços</a>
    <a href="#experimente-gratis" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-medium text-ink hover:bg-surface">Experimente grátis</a>
    <a href="#perguntas" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-medium text-ink hover:bg-surface">Perguntas frequentes</a>
  </div>
</header>

{{-- Hero --}}
<section class="relative overflow-hidden border-b border-line bg-gradient-to-b from-surface/60 via-white to-white">
  <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-brand/5 via-transparent to-transparent pointer-events-none"></div>
  <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-14 sm:py-20 lg:py-24 grid lg:grid-cols-12 gap-12 lg:gap-12 items-center">
    <div class="lg:col-span-7">
      <div class="inline-flex items-center gap-2 rounded-full border border-line bg-white px-3.5 py-1 text-xs font-medium text-muted mb-6 shadow-sm">
        <span class="h-2 w-2 rounded-full bg-brand"></span>
        Feito para Moçambique · M-Pesa · e-Mola · IVA Modelo A · ANARME
      </div>
      <h1 class="text-[2.25rem] sm:text-5xl font-bold tracking-tight leading-[1.12] text-ink">
        O sistema de gestão simples para vender, controlar o stock e fechar a caixa.
      </h1>
      <p class="mt-5 text-lg text-muted max-w-xl leading-relaxed">
        Controle vendas, stock multi-filial, fiados, lotes <strong class="text-ink font-semibold">FEFO</strong> e faturas num único lugar. Receba em <strong class="text-ink font-semibold">M-Pesa, e-Mola, POS e numerário</strong> e apure o IVA Modelo A — no computador e no telemóvel, mesmo sem internet.
      </p>

      <div class="mt-8 flex flex-col sm:flex-row gap-3">
        <a href="{{ route('register') }}"
           class="inline-flex items-center justify-center bg-brand hover:bg-brand-dark text-white font-semibold px-6 py-3.5 rounded-xl shadow-sm transition-all hover:shadow-md">
          Experimente grátis agora
        </a>
        <a href="#planos"
           class="inline-flex items-center justify-center border border-line hover:border-ink/30 bg-white text-ink font-semibold px-6 py-3.5 rounded-xl transition-colors">
          Ver planos e preços
        </a>
      </div>

      <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs sm:text-sm text-muted">
        <span class="inline-flex items-center gap-1.5">
          <svg class="w-4 h-4 text-brand shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Sem cartão de crédito
        </span>
        <span class="inline-flex items-center gap-1.5">
          <svg class="w-4 h-4 text-brand shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Credenciais por SMS (+258)
        </span>
        <span class="inline-flex items-center gap-1.5">
          <svg class="w-4 h-4 text-brand shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Funciona offline
        </span>
      </div>

      {{-- Botões diretos da Aplicação Mobile (Android e iOS) sempre visíveis no Hero --}}
      <div class="mt-8 pt-6 border-t border-line">
        <div class="text-xs font-semibold uppercase tracking-wider text-muted mb-3">
          Disponível também como aplicação para telemóvel e tablet:
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
          <a href="{{ asset('downloads/zbiz-plus.apk') }}" download
             class="inline-flex items-center gap-2.5 rounded-xl border border-line bg-white hover:border-brand/50 hover:bg-brand-soft/30 px-4 py-2.5 text-left shadow-sm transition-all">
            <svg class="w-5 h-5 text-brand shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4483-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993.0001.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993 0 .5511-.4483.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.2439 13.8533 7.8508 12 7.8508s-3.5902.3931-5.1367 1.0989L4.841 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3435-4.1021-2.6892-7.5743-6.1185-9.4396"/></svg>
            <div>
              <span class="block text-[10px] leading-none text-muted">Descarregar APK</span>
              <span class="block text-xs font-bold text-ink mt-0.5">App Android</span>
            </div>
          </a>

          <a href="#aplicacao"
             class="inline-flex items-center gap-2.5 rounded-xl border border-line bg-white hover:border-ink/40 hover:bg-surface px-4 py-2.5 text-left shadow-sm transition-all">
            <svg class="w-5 h-5 text-ink shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.33c.64-.78 1.08-1.86.96-2.94-.93.04-2.06.62-2.72 1.4-.58.68-1.1 1.79-.96 2.84 1.04.08 2.08-.52 2.72-1.3"/></svg>
            <div>
              <span class="block text-[10px] leading-none text-muted">Instalar no Safari</span>
              <span class="block text-xs font-bold text-ink mt-0.5">iPhone &amp; iPad</span>
            </div>
          </a>

          <button type="button" x-cloak x-show="deferredPrompt"
                  @click="deferredPrompt.prompt(); deferredPrompt = null"
                  class="inline-flex items-center gap-2 rounded-xl border border-brand/30 bg-brand-soft px-4 py-2.5 text-xs font-semibold text-brand transition-colors">
            Instalar no navegador
          </button>
        </div>
      </div>
    </div>

    {{-- Coluna Direita: Talão Interativo (Comércio / Farmácia FEFO / Fecho Cego Z) --}}
    <div class="lg:col-span-5 flex flex-col items-center lg:items-end">
      {{-- Seletor rápido de exemplo no topo do talão --}}
      <div class="mb-3 inline-flex p-1 rounded-lg bg-surface border border-line text-xs font-medium">
        <button type="button" @click="receiptMode = 'retail'"
                :class="receiptMode === 'retail' ? 'bg-white text-ink shadow-sm font-semibold' : 'text-muted hover:text-ink'"
                class="px-3 py-1.5 rounded-md transition-all">
          Venda Loja
        </button>
        <button type="button" @click="receiptMode = 'pharmacy'"
                :class="receiptMode === 'pharmacy' ? 'bg-white text-ink shadow-sm font-semibold' : 'text-muted hover:text-ink'"
                class="px-3 py-1.5 rounded-md transition-all">
          Farmácia FEFO
        </button>
        <button type="button" @click="receiptMode = 'shift'"
                :class="receiptMode === 'shift' ? 'bg-white text-ink shadow-sm font-semibold' : 'text-muted hover:text-ink'"
                class="px-3 py-1.5 rounded-md transition-all">
          Fecho Cego Z
        </button>
      </div>

      <div class="relative w-full max-w-[330px]">
        <div class="absolute -inset-3 bg-brand/5 rounded-2xl blur-xl"></div>

        {{-- Modo 1: Talão Loja / Ferragens --}}
        <div x-show="receiptMode === 'retail'" class="relative bg-white border border-line rounded-xl shadow-receipt px-6 py-7 font-mono text-[13px] leading-6" aria-label="Exemplo de talão de venda">
          <div class="text-center">
            <div class="font-semibold tracking-wide text-ink">FERRAGENS ZAMBÉZIA</div>
            <div class="text-muted text-[11px] mt-0.5">NUIT: 400987654 · Talão #1094</div>
          </div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="space-y-1.5">
            <div class="flex justify-between gap-4"><span class="truncate">2 × Cimento 50 kg</span><span class="shrink-0">1.240,00</span></div>
            <div class="flex justify-between gap-4"><span class="truncate">1 × Pregos 3" 1 kg</span><span class="shrink-0">110,00</span></div>
            <div class="flex justify-between gap-4"><span class="truncate">1 × Fita métrica 5 m</span><span class="shrink-0">150,00</span></div>
          </div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="flex justify-between text-muted text-[12px]"><span>IVA Incluído (16%)</span><span>206,90 MT</span></div>
          <div class="flex justify-between font-semibold text-[15px] mt-1"><span>Total</span><span>1.500,00 MT</span></div>
          <div class="flex justify-between text-muted text-[12px] mt-1"><span>Pago em</span><span class="font-semibold text-brand">M-Pesa</span></div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="flex items-center justify-between text-[11px] text-muted">
            <span>Stock abatido</span>
            <span class="text-brand font-medium">● Sincronizado</span>
          </div>
        </div>

        {{-- Modo 2: Talão Farmácia (FEFO & Art. 9º CIVA) --}}
        <div x-cloak x-show="receiptMode === 'pharmacy'" class="relative bg-white border border-line rounded-xl shadow-receipt px-6 py-7 font-mono text-[13px] leading-6" aria-label="Exemplo de talão de farmácia">
          <div class="text-center">
            <div class="font-semibold tracking-wide text-ink">FARMÁCIA CENTRAL</div>
            <div class="text-muted text-[11px] mt-0.5">ANARME · Dispensação FEFO #2041</div>
          </div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="space-y-2">
            <div>
              <div class="flex justify-between gap-4"><span class="truncate">2 × Amoxicilina 500mg</span><span class="shrink-0">360,00</span></div>
              <div class="text-[11px] text-brand">Lote: MZ-0927 · Val: 11/2027 (FEFO)</div>
            </div>
            <div>
              <div class="flex justify-between gap-4"><span class="truncate">1 × Paracetamol 500mg</span><span class="shrink-0">120,00</span></div>
              <div class="text-[11px] text-muted">Lote: AN-1128 · Isento Art. 9.º CIVA</div>
            </div>
          </div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="flex justify-between text-muted text-[12px]"><span>Enquadramento</span><span>Isento IVA (0%)</span></div>
          <div class="flex justify-between font-semibold text-[15px] mt-1"><span>Total</span><span>480,00 MT</span></div>
          <div class="flex justify-between text-muted text-[12px] mt-1"><span>Pago em</span><span class="font-semibold text-brand">e-Mola / POS</span></div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="flex items-center justify-between text-[11px] text-muted">
            <span>Lotes verificados</span>
            <span class="text-brand font-medium">● Sem expirados</span>
          </div>
        </div>

        {{-- Modo 3: Talão Z de Fecho Cego de Caixa --}}
        <div x-cloak x-show="receiptMode === 'shift'" class="relative bg-white border border-line rounded-xl shadow-receipt px-6 py-7 font-mono text-[13px] leading-6" aria-label="Exemplo de fecho cego de caixa">
          <div class="text-center">
            <div class="font-semibold tracking-wide text-ink">TALÃO Z · FECHO CEGO</div>
            <div class="text-muted text-[11px] mt-0.5">Turno #104 · Operador: Caixa 01</div>
          </div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="space-y-1.5 text-[12px]">
            <div class="flex justify-between"><span>Numerário contado:</span><span>24.850,00</span></div>
            <div class="flex justify-between"><span>M-Pesa conciliado:</span><span>18.400,00</span></div>
            <div class="flex justify-between"><span>e-Mola / POS:</span><span>9.200,00</span></div>
          </div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="flex justify-between font-semibold text-[14px]"><span>Total Turno</span><span>52.450,00 MT</span></div>
          <div class="flex justify-between text-brand text-[12px] font-semibold mt-1"><span>Diferença de Caixa</span><span>0,00 MT (Certo)</span></div>
          <div class="my-4 border-t border-dashed border-line"></div>
          <div class="text-center text-muted text-[11px]">Conferência cega auditada</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Faixa de Indicadores Operacionais (Estilo VHSYS) --}}
<section class="border-b border-line bg-surface/50 py-8">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-center sm:text-left">
    <div>
      <div class="text-2xl font-bold text-ink font-mono">80mm &amp; A4</div>
      <div class="text-xs text-muted mt-0.5">Talões térmicos, faturas e cotações em PDF</div>
    </div>
    <div>
      <div class="text-2xl font-bold text-brand font-mono">100% Offline</div>
      <div class="text-xs text-muted mt-0.5">Continua a vender mesmo quando a internet cai</div>
    </div>
    <div>
      <div class="text-2xl font-bold text-ink font-mono">CIVA 16%</div>
      <div class="text-xs text-muted mt-0.5">Isenções Art. 9.º e mapa Modelo A para a AT</div>
    </div>
    <div>
      <div class="text-2xl font-bold text-ink font-mono">Web + Mobile</div>
      <div class="text-xs text-muted mt-0.5">Computador, APK Android e iPhone/iPad</div>
    </div>
  </div>
</section>

{{-- Funcionalidades (Pilares estilo VHSYS) --}}
<section id="funcionalidades" class="py-20 sm:py-24">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="max-w-2xl">
      <span class="text-xs font-semibold uppercase tracking-wider text-brand">Gestão Integrada</span>
      <h2 class="mt-2 text-3xl sm:text-[2rem] font-bold tracking-tight">Do balcão ao imposto, os dados ficam ligados</h2>
      <p class="mt-3 text-muted leading-relaxed">Cada venda actualiza o stock, a caixa e o mapa de IVA automaticamente. Não há segunda digitação nem folhas de cálculo soltas.</p>
    </div>

    <dl class="mt-14 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-surface/60 border border-line rounded-xl p-6 hover:border-brand/40 transition-colors">
        <dt class="font-semibold text-lg flex items-center gap-2.5 text-ink">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-soft text-brand text-sm font-bold font-mono">01</span>
          Frente de caixa (POS)
        </dt>
        <dd class="mt-3 text-sm text-muted leading-relaxed">Venda com leitor de código de barras ou com a câmara do telemóvel. Imprima talões de 80 mm ou 58 mm e faturas A4, e converta cotações em vendas com um clique.</dd>
      </div>

      <div class="bg-surface/60 border border-line rounded-xl p-6 hover:border-brand/40 transition-colors">
        <dt class="font-semibold text-lg flex items-center gap-2.5 text-ink">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-soft text-brand text-sm font-bold font-mono">02</span>
          Stock, compras e filiais
        </dt>
        <dd class="mt-3 text-sm text-muted leading-relaxed">Veja o stock de cada loja ou armazém, registe compras a fornecedores, transfira mercadoria entre filiais e receba avisos de stock mínimo. O Kardex guarda todas as movimentações.</dd>
      </div>

      <div class="bg-surface/60 border border-line rounded-xl p-6 hover:border-brand/40 transition-colors">
        <dt class="font-semibold text-lg flex items-center gap-2.5 text-ink">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-soft text-brand text-sm font-bold font-mono">03</span>
          Caixa e fecho cego Z
        </dt>
        <dd class="mt-3 text-sm text-muted leading-relaxed">Numerário, M-Pesa, e-Mola e POS na mesma venda. No fecho cego, o operador conta a gaveta sem ver o valor esperado, e o sistema mostra a diferença no talão Z.</dd>
      </div>

      <div class="bg-surface/60 border border-line rounded-xl p-6 hover:border-brand/40 transition-colors">
        <dt class="font-semibold text-lg flex items-center gap-2.5 text-ink">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-soft text-brand text-sm font-bold font-mono">04</span>
          Clientes, cotações e fiados
        </dt>
        <dd class="mt-3 text-sm text-muted leading-relaxed">Defina limite de crédito por cliente, registe pagamentos parciais ou totais com recibo de amortização e acompanhe a conta corrente de cada devedor.</dd>
      </div>

      <div class="bg-surface/60 border border-line rounded-xl p-6 hover:border-brand/40 transition-colors">
        <dt class="font-semibold text-lg flex items-center gap-2.5 text-ink">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-soft text-brand text-sm font-bold font-mono">05</span>
          Lotes, validades e FEFO
        </dt>
        <dd class="mt-3 text-sm text-muted leading-relaxed">O lote que vence primeiro sai primeiro (FEFO). Alertas preventivos aos 30, 60 e 90 dias e bloqueio de lotes expirados para farmácias e produtos perecíveis.</dd>
      </div>

      <div class="bg-surface/60 border border-line rounded-xl p-6 hover:border-brand/40 transition-colors">
        <dt class="font-semibold text-lg flex items-center gap-2.5 text-ink">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-soft text-brand text-sm font-bold font-mono">06</span>
          IVA Modelo A e Backups
        </dt>
        <dd class="mt-3 text-sm text-muted leading-relaxed">Taxa de 16% e isenções do artigo 9.º do CIVA, com o mapa Modelo A em PDF pronto para a AT. Cada ação fica registada por utilizador com cópias de segurança na nuvem.</dd>
      </div>
    </dl>
  </div>
</section>

{{-- Segmentos (Estilo vhsys.com.br/segmentos/) --}}
<section id="segmentos" class="py-20 sm:py-24 bg-surface border-y border-line">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="max-w-2xl">
      <span class="text-xs font-semibold uppercase tracking-wider text-brand">Segmentos Atendidos</span>
      <h2 class="mt-2 text-3xl sm:text-[2rem] font-bold tracking-tight">Feito para o seu tipo de negócio</h2>
      <p class="mt-2 text-muted">Escolha o seu ramo para ver como o sistema se adapta à sua operação.</p>
    </div>

    <div class="mt-8 flex flex-wrap gap-2" role="tablist">
      <button type="button" role="tab" @click="sector = 'retail'" :aria-selected="sector === 'retail'"
        :class="sector === 'retail' ? 'bg-ink text-white shadow-sm' : 'bg-white text-ink border border-line hover:border-ink/40'"
        class="px-4 py-2.5 rounded-lg text-sm font-medium transition-all">
        Comércio, mercearias e ferragens
      </button>
      <button type="button" role="tab" @click="sector = 'pharmacy'" :aria-selected="sector === 'pharmacy'"
        :class="sector === 'pharmacy' ? 'bg-ink text-white shadow-sm' : 'bg-white text-ink border border-line hover:border-ink/40'"
        class="px-4 py-2.5 rounded-lg text-sm font-medium transition-all">
        Farmácias e clínicas
      </button>
      <button type="button" role="tab" @click="sector = 'restaurant'" :aria-selected="sector === 'restaurant'"
        :class="sector === 'restaurant' ? 'bg-ink text-white shadow-sm' : 'bg-white text-ink border border-line hover:border-ink/40'"
        class="px-4 py-2.5 rounded-lg text-sm font-medium transition-all">
        Restaurantes, bares e cafés
      </button>
      <button type="button" role="tab" @click="sector = 'services'" :aria-selected="sector === 'services'"
        :class="sector === 'services' ? 'bg-ink text-white shadow-sm' : 'bg-white text-ink border border-line hover:border-ink/40'"
        class="px-4 py-2.5 rounded-lg text-sm font-medium transition-all">
        Serviços, gráficas e oficinas
      </button>
    </div>

    <div class="mt-10 grid lg:grid-cols-12 gap-8 items-start">
      <div class="lg:col-span-7 bg-white border border-line rounded-xl p-6 sm:p-8 shadow-soft">
        <div x-show="sector === 'retail'">
          <span class="inline-block text-xs font-semibold text-brand bg-brand-soft px-2.5 py-1 rounded-md mb-3">Retalho &amp; Grossistas</span>
          <h3 class="text-xl font-bold text-ink">Lojas, supermercados, ferragens e distribuidoras</h3>
          <p class="mt-3 text-muted leading-relaxed">Um produto com código de barras vende-se em segundos no balcão. Quem compra a crédito fica registado com limite, e a mercadoria passa do armazém para a loja com rasto de cada transferência.</p>
          <ul class="mt-5 space-y-2.5 text-sm text-ink">
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Fiados com limite de crédito e recibos de amortização</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Stock por loja, alertas de rutura e transferências entre filiais</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Fecho cego de caixa no fim do turno (Talão Z)</li>
          </ul>
        </div>

        <div x-cloak x-show="sector === 'pharmacy'">
          <span class="inline-block text-xs font-semibold text-brand bg-brand-soft px-2.5 py-1 rounded-md mb-3">Saúde &amp; ANARME</span>
          <h3 class="text-xl font-bold text-ink">Farmácias, drogarias e clínicas</h3>
          <p class="mt-3 text-muted leading-relaxed">O sistema escolhe na venda o lote com a validade mais próxima (FEFO) e impede a saída de lotes expirados. Vem com um catálogo inicial de 63 medicamentos essenciais e aplica a isenção de IVA do artigo 9.º.</p>
          <ul class="mt-5 space-y-2.5 text-sm text-ink">
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Lotes e validades com alertas aos 30, 60 e 90 dias</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Registo de receitas médicas e controlo de psicotrópicos</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Mapa de IVA Modelo A com isenções do artigo 9.º do CIVA</li>
          </ul>
        </div>

        <div x-cloak x-show="sector === 'restaurant'">
          <span class="inline-block text-xs font-semibold text-brand bg-brand-soft px-2.5 py-1 rounded-md mb-3">Restauração &amp; Lazer</span>
          <h3 class="text-xl font-bold text-ink">Restaurantes, bares, pastelarias e take-away</h3>
          <p class="mt-3 text-muted leading-relaxed">Controle o mapa de mesas livres e ocupadas em tempo real, registe pedidos pelo telemóvel e envie diretamente para o ecrã da cozinha (KDS), sem papel perdido entre o salão e o balcão.</p>
          <ul class="mt-5 space-y-2.5 text-sm text-ink">
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Mapa de mesas e comandas eletrónicas no telemóvel</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Monitor de preparação na cozinha (KDS) em tempo real</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Pagamento rápido em M-Pesa, e-Mola, POS e numerário</li>
          </ul>
        </div>

        <div x-cloak x-show="sector === 'services'">
          <span class="inline-block text-xs font-semibold text-brand bg-brand-soft px-2.5 py-1 rounded-md mb-3">Serviços &amp; Produção</span>
          <h3 class="text-xl font-bold text-ink">Prestadores de serviços, gráficas, reprografias e oficinas</h3>
          <p class="mt-3 text-muted leading-relaxed">Envie a cotação em PDF, converta-a em fatura quando o cliente aceita e acompanhe o que falta receber. Nas gráficas e reprografias, abate automaticamente papel, tintas e consumíveis.</p>
          <ul class="mt-5 space-y-2.5 text-sm text-ink">
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Cotações em PDF com conversão direta em venda</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Conta corrente por cliente e pagamentos parciais</li>
            <li class="flex items-start gap-2.5"><span class="text-brand font-bold">✓</span>Despesas operacionais e folha de salários no mesmo sistema</li>
          </ul>
        </div>
      </div>

      <div class="lg:col-span-5">
        <div class="bg-white border border-line rounded-xl p-6 sm:p-7 shadow-soft space-y-4">
          <div class="text-xs font-semibold uppercase tracking-wider text-muted">Comece hoje</div>
          <h4 class="text-lg font-bold text-ink">Ambiente pré-configurado para a sua área</h4>
          <p class="text-sm text-muted leading-relaxed">
            Ao criar a sua conta, o ZBIZ+ ativa automaticamente as categorias, unidades e relatórios recomendados para o seu segmento.
          </p>
          <a :href="'{{ route('register') }}?sector=' + sector"
             class="inline-flex w-full items-center justify-center bg-brand hover:bg-brand-dark text-white font-semibold px-5 py-3.5 rounded-lg text-sm shadow-sm transition-colors">
            Experimente grátis neste segmento
          </a>
          <p class="text-xs text-muted leading-relaxed pt-2 border-t border-line">
            Também atendemos <strong class="text-ink">autopeças, informática, salões, papelarias e moda</strong>. Fale connosco pelo
            <a class="font-semibold text-brand hover:underline" href="https://wa.me/258862134230" target="_blank" rel="noopener noreferrer">WhatsApp (+258 86 213 4230)</a>.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Aplicação (Android APK, iPhone/iPad e Computador) --}}
<section id="aplicacao" class="py-20 sm:py-24">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">
    <div class="lg:col-span-5">
      <span class="text-xs font-semibold uppercase tracking-wider text-brand">Mobilidade Total</span>
      <h2 class="mt-2 text-3xl sm:text-[2rem] font-bold tracking-tight">No computador e no telemóvel (Android e iOS)</h2>
      <p class="mt-4 text-muted leading-relaxed">
        No <strong class="text-ink">Android</strong>, descarregue o instalador APK e abra a aplicação como qualquer outra. No <strong class="text-ink">iPhone / iPad</strong> e no computador, instala-se diretamente a partir do navegador.
      </p>
      <p class="mt-3 text-muted leading-relaxed">
        No telemóvel, a câmara lê códigos de barras e o dono acompanha as vendas de cada filial em tempo real. Se a internet falhar, as vendas continuam e sincronizam quando ela voltar.
      </p>

      <div class="mt-7 flex flex-wrap gap-3">
        <a href="{{ asset('downloads/zbiz-plus.apk') }}" download
           class="inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-semibold px-5 py-3 rounded-xl text-sm shadow-sm transition-colors">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4483-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993.0001.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993 0 .5511-.4483.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.2439 13.8533 7.8508 12 7.8508s-3.5902.3931-5.1367 1.0989L4.841 5.4467a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3435-4.1021-2.6892-7.5743-6.1185-9.4396"/></svg>
          <span>Descarregar para Android (APK)</span>
        </a>
        <button type="button" x-cloak x-show="deferredPrompt"
          @click="deferredPrompt.prompt(); deferredPrompt = null"
          class="inline-flex items-center justify-center border border-line hover:border-ink/30 font-semibold px-5 py-3 rounded-xl text-sm transition-colors">
          Instalar pelo navegador
        </button>
      </div>
    </div>

    <div class="lg:col-span-7 grid sm:grid-cols-2 gap-6">
      <div class="bg-surface border border-line rounded-xl p-6 shadow-soft">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-ink">Android (Telemóvel e Tablet)</h3>
          <span class="text-xs font-semibold text-brand bg-brand-soft px-2.5 py-0.5 rounded-md">APK Oficial</span>
        </div>
        <ol class="mt-4 space-y-3 text-sm text-muted leading-relaxed">
          <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand text-xs font-bold">1</span>Descarregue o ficheiro APK no botão ao lado.</li>
          <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand text-xs font-bold">2</span>Abra o ficheiro e, se o Android pedir, permita a instalação desta origem.</li>
          <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand text-xs font-bold">3</span>Toque em Instalar e abra o ZBIZ+ no seu ecrã principal.</li>
        </ol>
      </div>

      <div class="bg-surface border border-line rounded-xl p-6 shadow-soft">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-ink">iPhone e iPad (Apple iOS)</h3>
          <span class="text-xs font-semibold text-ink bg-white border border-line px-2.5 py-0.5 rounded-md">WebApp iOS</span>
        </div>
        <ol class="mt-4 space-y-3 text-sm text-muted leading-relaxed">
          <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand text-xs font-bold">1</span>Abra o ZBIZ+ no navegador Safari do seu iPhone ou iPad.</li>
          <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand text-xs font-bold">2</span>Toque no ícone Partilhar na barra inferior.</li>
          <li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-soft text-brand text-xs font-bold">3</span>Escolha “Adicionar ao ecrã principal” para usar em ecrã inteiro.</li>
        </ol>
      </div>
    </div>
  </div>
</section>

{{-- Planos (Estilo vhsys.com.br/planos-e-precos/) --}}
<section id="planos" class="py-20 sm:py-24 bg-surface border-y border-line">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
      <div>
        <span class="text-xs font-semibold uppercase tracking-wider text-brand">Transparência Total</span>
        <h2 class="mt-2 text-3xl sm:text-[2rem] font-bold tracking-tight">Planos e preços em Meticais</h2>
        <p class="mt-2 text-muted max-w-md">Todos os planos incluem a aplicação Android e iOS, actualizações automáticas e suporte técnico local.</p>
      </div>
      <div class="inline-flex p-1 bg-white border border-line rounded-lg text-sm font-medium self-start shadow-sm" role="group" aria-label="Período de pagamento">
        <button type="button" @click="cycle = 'monthly'"
          :class="cycle === 'monthly' ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'"
          class="px-4 py-2 rounded-md transition-all">Mensal</button>
        <button type="button" @click="cycle = 'semiannual'"
          :class="cycle === 'semiannual' ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'"
          class="px-4 py-2 rounded-md transition-all">Semestral · −10%</button>
        <button type="button" @click="cycle = 'annual'"
          :class="cycle === 'annual' ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'"
          class="px-4 py-2 rounded-md transition-all">Anual · −20%</button>
      </div>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-5 items-stretch">
      {{-- Starter --}}
      <div class="bg-white border border-line rounded-xl p-6 flex flex-col shadow-soft hover:shadow-card transition-shadow">
        <h3 class="font-bold text-lg text-ink">ZBIZ Starter</h3>
        <p class="mt-1 text-sm text-muted">Bancas, mercearias e lojas individuais.</p>
        <p class="mt-5">
          <span class="text-3xl font-bold tracking-tight text-ink" x-text="price(1250)">1.250</span>
          <span class="text-sm text-muted">MT/mês</span>
        </p>
        <ul class="mt-5 mb-6 space-y-2.5 text-sm text-muted flex-1">
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> 1 loja e até 2 utilizadores</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Frente de caixa e talão 80 mm</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Fecho cego de caixa</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Até 500 produtos</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> App Android e iOS incluída</li>
        </ul>
        <a href="{{ route('register', ['plan' => 'starter']) }}"
           class="block text-center border border-line hover:border-ink/40 font-semibold py-2.5 rounded-lg text-sm transition-colors">
          Experimente grátis
        </a>
      </div>

      {{-- Pro --}}
      <div class="bg-white border border-line rounded-xl p-6 flex flex-col shadow-soft hover:shadow-card transition-shadow">
        <h3 class="font-bold text-lg text-ink">ZBIZ Pro</h3>
        <p class="mt-1 text-sm text-muted">Comércio, gráficas e serviços em crescimento.</p>
        <p class="mt-5">
          <span class="text-3xl font-bold tracking-tight text-ink" x-text="price(2950)">2.950</span>
          <span class="text-sm text-muted">MT/mês</span>
        </p>
        <ul class="mt-5 mb-6 space-y-2.5 text-sm text-muted flex-1">
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Até 2 lojas e 5 utilizadores</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Fiados, cotações e clientes</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Fornecedores, compras e despesas</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Mapa de IVA Modelo A</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> App Android e iOS incluída</li>
        </ul>
        <a href="{{ route('register', ['plan' => 'pro']) }}"
           class="block text-center border border-line hover:border-ink/40 font-semibold py-2.5 rounded-lg text-sm transition-colors">
          Experimente grátis
        </a>
      </div>

      {{-- Pharmacy+ (destaque) --}}
      <div class="relative bg-white border-2 border-brand rounded-xl p-6 flex flex-col shadow-card">
        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
          <span class="inline-flex items-center rounded-full bg-brand px-3 py-0.5 text-[11px] font-semibold text-white tracking-wide">
            RECOMENDADO
          </span>
        </div>
        <h3 class="font-bold text-lg text-ink">Pharmacy+</h3>
        <p class="mt-1 text-sm text-muted">Farmácias, clínicas e drogarias.</p>
        <p class="mt-5">
          <span class="text-3xl font-bold tracking-tight text-brand" x-text="price(4500)">4.500</span>
          <span class="text-sm text-muted">MT/mês</span>
        </p>
        <ul class="mt-5 mb-6 space-y-2.5 text-sm text-muted flex-1">
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Até 2 filiais</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Lotes, validades e FEFO</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Receitas e psicotrópicos</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Isenções do artigo 9.º e Modelo A</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> App Android e iOS incluída</li>
        </ul>
        <a href="{{ route('register', ['plan' => 'pharmacy_plus', 'sector' => 'pharmacy']) }}"
           class="block text-center bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
          Experimente grátis
        </a>
      </div>

      {{-- Business --}}
      <div class="bg-white border border-line rounded-xl p-6 flex flex-col shadow-soft hover:shadow-card transition-shadow">
        <h3 class="font-bold text-lg text-ink">ZBIZ Business</h3>
        <p class="mt-1 text-sm text-muted">Redes comerciais e distribuidoras.</p>
        <p class="mt-5">
          <span class="text-3xl font-bold tracking-tight text-ink" x-text="price(5500)">5.500</span>
          <span class="text-sm text-muted">MT/mês</span>
        </p>
        <ul class="mt-5 mb-6 space-y-2.5 text-sm text-muted flex-1">
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Até 5 filiais e 15 utilizadores</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Transferências entre filiais</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Cópias de segurança na nuvem</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> Artigos e faturação sem limite</li>
          <li class="flex gap-2"><span class="text-brand font-bold">✓</span> App Android e iOS incluída</li>
        </ul>
        <a href="{{ route('register', ['plan' => 'business']) }}"
           class="block text-center border border-line hover:border-ink/40 font-semibold py-2.5 rounded-lg text-sm transition-colors">
          Experimente grátis
        </a>
      </div>
    </div>

    {{-- Tabela comparativa --}}
    <div class="mt-12 overflow-x-auto bg-white border border-line rounded-xl shadow-soft">
      <table class="w-full text-sm text-left">
        <thead>
          <tr class="border-b border-line bg-surface/60">
            <th class="py-3.5 px-5 font-semibold text-ink">Comparação de recursos incluídos</th>
            <th class="py-3.5 px-4 font-semibold text-center text-ink">Starter</th>
            <th class="py-3.5 px-4 font-semibold text-center text-ink">Pro</th>
            <th class="py-3.5 px-4 font-semibold text-center text-brand">Pharmacy+</th>
            <th class="py-3.5 px-4 font-semibold text-center text-ink">Business</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-line">
          <tr class="hover:bg-surface/40 transition-colors">
            <td class="py-3.5 px-5">Aplicação Android (APK), iPhone/iPad e Computador</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
          </tr>
          <tr class="hover:bg-surface/40 transition-colors">
            <td class="py-3.5 px-5">Frente de caixa POS offline, fecho cego e pagamentos móveis</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
          </tr>
          <tr class="hover:bg-surface/40 transition-colors">
            <td class="py-3.5 px-5">Fiados, cotações em PDF, compras e fornecedores</td>
            <td class="text-center text-muted">—</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
          </tr>
          <tr class="hover:bg-surface/40 transition-colors">
            <td class="py-3.5 px-5">Mapa de IVA Modelo A (AT Moçambique)</td>
            <td class="text-center text-muted">—</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
          </tr>
          <tr class="hover:bg-surface/40 transition-colors">
            <td class="py-3.5 px-5">Lotes, FEFO, catálogo ANARME e alertas de validade</td>
            <td class="text-center text-muted">—</td>
            <td class="text-center text-muted">—</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
          </tr>
          <tr class="hover:bg-surface/40 transition-colors">
            <td class="py-3.5 px-5">Transferências entre filiais e cópias de segurança na nuvem</td>
            <td class="text-center text-muted">—</td>
            <td class="text-center text-muted">—</td>
            <td class="text-center text-brand font-medium">Sim</td>
            <td class="text-center text-brand font-medium">Sim</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

{{-- Experimente grátis (Estilo vhsys.com.br/experimente-gratis/) --}}
<section id="experimente-gratis" class="py-20 sm:py-24">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 grid lg:grid-cols-12 gap-12 items-start">
    <div class="lg:col-span-5">
      <span class="text-xs font-semibold uppercase tracking-wider text-brand">Experimente Grátis</span>
      <h2 class="mt-2 text-3xl sm:text-[2rem] font-bold tracking-tight">Teste com os dados da sua empresa</h2>
      <p class="mt-4 text-muted leading-relaxed max-w-md">
        Crie a conta de teste em um minuto. Enviamos as credenciais por SMS e e-mail, com categorias já configuradas para o seu segmento. Se preferir ajuda na instalação, a nossa equipa responde no WhatsApp.
      </p>
      <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, quero experimentar o sistema ZBIZ+ na minha empresa.') }}"
         target="_blank" rel="noopener noreferrer"
         class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand hover:underline">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.526 5.85L.057 23.5a.75.75 0 00.92.92l5.65-1.47A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.72 9.72 0 01-4.95-1.35l-.35-.21-3.68.96.98-3.58-.23-.37A9.72 9.72 0 012.25 12 9.75 9.75 0 0112 2.25 9.75 9.75 0 0121.75 12 9.75 9.75 0 0112 21.75z"/></svg>
        Falar no WhatsApp: (+258) 86 213 4230
      </a>
    </div>
    <form method="GET" action="{{ route('register') }}"
          class="lg:col-span-7 bg-surface border border-line rounded-xl p-6 sm:p-8 space-y-5 shadow-soft">
      <div>
        <label for="f-sector" class="block text-sm font-medium mb-1.5">Segmento da empresa</label>
        <select id="f-sector" name="sector" x-model="sector"
                class="w-full px-3.5 py-2.5 rounded-lg bg-white border border-line text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/20 transition">
          <option value="retail">Comércio, mercearia ou ferragens</option>
          <option value="pharmacy">Farmácia, clínica ou drogaria</option>
          <option value="restaurant">Restaurante, bar ou pastelaria</option>
          <option value="services">Serviços, gráfica ou oficina</option>
        </select>
      </div>
      <div>
        <label for="f-plan" class="block text-sm font-medium mb-1.5">Plano a testar</label>
        <select id="f-plan" name="plan"
                class="w-full px-3.5 py-2.5 rounded-lg bg-white border border-line text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/20 transition">
          <option value="pro" :selected="sector !== 'pharmacy'">ZBIZ Pro</option>
          <option value="pharmacy_plus" :selected="sector === 'pharmacy'">Pharmacy+</option>
          <option value="starter">ZBIZ Starter</option>
          <option value="business">ZBIZ Business</option>
        </select>
      </div>
      <button type="submit"
              class="w-full bg-brand hover:bg-brand-dark text-white font-semibold py-3.5 rounded-lg text-sm shadow-sm transition-colors">
        Criar conta de teste grátis
      </button>
    </form>
  </div>
</section>

{{-- Perguntas --}}
<section id="perguntas" class="py-20 sm:py-24 bg-surface border-t border-line">
  <div class="max-w-3xl mx-auto px-4 sm:px-6">
    <h2 class="text-3xl sm:text-[2rem] font-bold tracking-tight">Perguntas frequentes</h2>
    <div class="mt-10 divide-y divide-line border-y border-line">
      <details class="group py-5">
        <summary class="flex justify-between gap-4 cursor-pointer font-medium text-ink hover:text-brand transition-colors">
          Funciona sem internet?
          <span class="text-muted group-open:rotate-45 transition-transform text-xl leading-none">+</span>
        </summary>
        <p class="mt-3 text-muted leading-relaxed pr-8">Sim. A frente de caixa continua a vender e os dados sincronizam quando a ligação regressa.</p>
      </details>
      <details class="group py-5">
        <summary class="flex justify-between gap-4 cursor-pointer font-medium text-ink hover:text-brand transition-colors">
          Como funciona a aplicação no telemóvel Android e no iPhone?
          <span class="text-muted group-open:rotate-45 transition-transform text-xl leading-none">+</span>
        </summary>
        <p class="mt-3 text-muted leading-relaxed pr-8">No Android pode descarregar diretamente o instalador APK nesta página. No iPhone ou iPad, basta abrir no Safari e escolher "Adicionar ao ecrã principal".</p>
      </details>
      <details class="group py-5">
        <summary class="flex justify-between gap-4 cursor-pointer font-medium text-ink hover:text-brand transition-colors">
          Preciso de comprar um leitor de código de barras?
          <span class="text-muted group-open:rotate-45 transition-transform text-xl leading-none">+</span>
        </summary>
        <p class="mt-3 text-muted leading-relaxed pr-8">Não é obrigatório. Pode usar a câmara do telemóvel. Se vender muito, um leitor USB acelera o balcão e funciona com o sistema.</p>
      </details>
      <details class="group py-5">
        <summary class="flex justify-between gap-4 cursor-pointer font-medium text-ink hover:text-brand transition-colors">
          Posso mudar de plano mais tarde?
          <span class="text-muted group-open:rotate-45 transition-transform text-xl leading-none">+</span>
        </summary>
        <p class="mt-3 text-muted leading-relaxed pr-8">Sim. Os dados e o histórico mantêm-se quando passa para um plano com mais filiais ou módulos.</p>
      </details>
      <details class="group py-5">
        <summary class="flex justify-between gap-4 cursor-pointer font-medium text-ink hover:text-brand transition-colors">
          Quem presta o suporte?
          <span class="text-muted group-open:rotate-45 transition-transform text-xl leading-none">+</span>
        </summary>
        <p class="mt-3 text-muted leading-relaxed pr-8">A Fdsmultiservices, a equipa que desenvolve o sistema, por WhatsApp e e-mail.</p>
      </details>
    </div>
  </div>
</section>

{{-- Rodapé --}}
<footer class="border-t border-line py-12 text-sm text-muted bg-white">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
    <div class="flex items-center gap-3">
      <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-ink overflow-hidden">
        <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="h-6 w-6 object-contain">
      </span>
      <div>
        <span class="font-bold text-ink">ZBIZ<span class="text-brand">+</span></span>
        <span class="ml-2 text-muted">· Desenvolvido por Fdsmultiservices</span>
      </div>
    </div>
    <div class="flex flex-wrap gap-x-6 gap-y-2">
      <a href="{{ asset('downloads/zbiz-plus.apk') }}" download class="font-medium text-brand hover:underline">App Android (APK)</a>
      <a href="https://wa.me/258862134230" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">(+258) 86 213 4230</a>
      <a href="mailto:fdsmultiservices@gmail.com" class="hover:text-ink transition-colors">fdsmultiservices@gmail.com</a>
    </div>
    <div class="text-muted">&copy; {{ date('Y') }} ZBIZ+</div>
  </div>
</footer>

<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('{{ asset("sw.js") }}').catch(() => {});
    });
  }
</script>
</body>
</html>
