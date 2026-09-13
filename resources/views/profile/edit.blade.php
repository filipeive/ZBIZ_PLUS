@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil & Configurações da Conta')

@php
    $theme = tenant_theme();
    $tenant = $tenant ?? current_tenant();
    $subscription = $subscription ?? $tenant?->activeSubscription() ?? $tenant?->currentSubscription;
    $isTrial = $tenant?->isTrial() || $tenant?->status === 'trial' || $subscription?->isTrial() || $subscription?->status === 'trialing';
    $trialDaysLeft = $tenant?->trialDaysRemaining() ?? $subscription?->daysRemaining() ?? 0;
    $trialPercentage = $tenant?->trialPercentage() ?? $subscription?->trialPercentage() ?? 0;
    $planName = $subscription?->plan?->name ?? 'Plano Inicial';
    $latestKey = $tenant?->latestLicenseKey;
@endphp

@section('content')
<div class="space-y-6" x-data="{ 
    showPhotoModal: false, 
    showDeleteModal: false,
    currentPasswordVisible: false,
    newPasswordVisible: false,
    confirmPasswordVisible: false
}">

    <!-- Banner de Subscrição / Período de Avaliação -->
    @if($isTrial)
        <div class="relative overflow-hidden rounded-3xl border {{ $trialDaysLeft <= 3 ? 'border-rose-500/40 bg-rose-950/20' : ($trialDaysLeft <= 7 ? 'border-amber-500/40 bg-amber-950/20' : 'border-emerald-500/40 bg-emerald-950/20') }} p-6 shadow-2xl backdrop-blur-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl {{ $trialDaysLeft <= 3 ? 'bg-rose-500/20 text-rose-400' : ($trialDaysLeft <= 7 ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400') }} flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                        <i class="fa-solid fa-clock-rotate-left {{ $trialDaysLeft <= 3 ? 'animate-pulse' : '' }}"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-black uppercase tracking-wider px-2.5 py-1 rounded-full {{ $trialDaysLeft <= 3 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : ($trialDaysLeft <= 7 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40') }}">
                                {{ $trialDaysLeft > 0 ? 'Período de Avaliação / Teste' : 'Período Experimental Expirado' }}
                            </span>
                            <span class="text-xs font-bold text-slate-300">
                                Plano: <strong class="text-white">{{ $planName }}</strong>
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-white">
                            @if($trialDaysLeft > 0)
                                Restam <span class="{{ $trialDaysLeft <= 3 ? 'text-rose-400' : ($trialDaysLeft <= 7 ? 'text-amber-400' : 'text-emerald-400') }}">{{ $trialDaysLeft }} {{ $trialDaysLeft == 1 ? 'dia' : 'dias' }}</span> de teste gratuito
                            @else
                                O seu período de teste terminou.
                            @endif
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed max-w-2xl">
                            A sua conta para a empresa <strong class="text-slate-200">{{ $tenant?->name }}</strong> está configurada em modo de demonstração.
                            Quando efectuar o pagamento, receberá o código de activação de licença por SMS (<span class="font-mono text-slate-300">ZBIZ-XXXX-XXXX-XXXX-XXXX</span>) para activar a versão completa.
                        </p>
                        @if($tenant?->trial_ends_at || $subscription?->trial_ends_at)
                            <div class="text-[11px] text-slate-400 flex items-center gap-2 pt-1">
                                <i class="fa-solid fa-calendar-check text-slate-500"></i>
                                <span>Término do teste: <strong class="text-slate-200">{{ ($tenant?->trial_ends_at ?? $subscription?->trial_ends_at)->format('d/m/Y \à\s H:i') }}</strong></span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-shrink-0">
                    <a href="{{ route('license.activate') }}" 
                       class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-900/30 flex items-center justify-center gap-2 transition transform active:scale-95">
                        <i class="fa-solid fa-key"></i>
                        <span>Activar Código de Licença</span>
                    </a>
                    <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, gostaria de efetuar o pagamento da licença do ZBIZ+ para a minha empresa ' . ($tenant?->name ?? '')) }}" 
                       target="_blank" 
                       class="px-5 py-3 rounded-2xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold text-xs flex items-center justify-center gap-2 transition">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Pagar / Apoio WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Barra de Progresso do Teste -->
            <div class="mt-4 pt-4 border-t border-slate-800/80">
                <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1.5 font-medium">
                    <span>Progresso do período experimental</span>
                    <span class="font-bold text-slate-300">{{ $trialPercentage }}% decorrido ({{ $trialDaysLeft }} {{ $trialDaysLeft == 1 ? 'dia restante' : 'dias restantes' }})</span>
                </div>
                <div class="w-full bg-slate-800/80 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-500 {{ $trialDaysLeft <= 3 ? 'bg-rose-500' : ($trialDaysLeft <= 7 ? 'bg-amber-500' : 'bg-emerald-500') }}" 
                         style="width: {{ $trialPercentage }}%"></div>
                </div>
            </div>
        </div>
    @elseif($tenant)
        <div class="rounded-3xl border border-emerald-500/30 bg-emerald-950/10 p-6 shadow-xl backdrop-blur-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                Subscrição Activa
                            </span>
                            <span class="text-xs text-slate-400">Plano: <strong class="text-white">{{ $planName }}</strong></span>
                        </div>
                        <h3 class="text-base font-black text-white mt-0.5">Empresa Licenciada & Totalmente Operacional</h3>
                        <p class="text-xs text-slate-400">A sua instalação tem todas as funcionalidades do plano activas.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('license.activate') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-key text-emerald-400"></i>
                        <span>Inserir Nova Chave</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Cartão de Informações do Perfil -->
        <div class="lg:col-span-1">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl text-center">
                <div class="relative inline-block mb-4">
                    <img id="profile-avatar-display" 
                         src="{{ $user->avatar_url }}" 
                         alt="{{ $user->name }}"
                         class="w-28 h-28 rounded-2xl object-cover border-2 border-slate-700 shadow-xl mx-auto">
                    @if($user->is_active)
                        <span class="absolute bottom-0 right-0 w-5 h-5 bg-emerald-500 border-2 border-slate-900 rounded-full" title="Ativo"></span>
                    @else
                        <span class="absolute bottom-0 right-0 w-5 h-5 bg-rose-500 border-2 border-slate-900 rounded-full" title="Inativo"></span>
                    @endif
                </div>

                <h3 class="text-lg font-black font-heading text-white">{{ $user->name }}</h3>
                <p class="text-xs text-slate-400 mb-3">{{ $user->email }}</p>

                <div class="mb-5">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $user->is_admin ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' }}">
                        {{ $user->role_display ?? 'Utilizador' }}
                    </span>
                </div>

                <div class="space-y-2 mb-6">
                    <button type="button" 
                            @click="showPhotoModal = true" 
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-camera {{ $theme['text_accent'] }}"></i>
                        <span>Alterar Fotografia</span>
                    </button>
                    <a href="{{ route('users.show', $user) }}" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-800 text-slate-300 text-xs font-bold border border-slate-800 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user"></i>
                        <span>Ver Ficha Completa</span>
                    </a>
                </div>

                <div class="border-t border-slate-800/80 pt-4 text-left space-y-2 text-xs text-slate-400">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-calendar text-slate-500"></i> Membro desde</span>
                        <span class="font-semibold text-slate-300">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-clock text-slate-500"></i> Último acesso</span>
                        <span class="font-semibold text-slate-300">{{ $user->last_login_display ?? 'Nunca' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-id-badge text-slate-500"></i> Identificador</span>
                        <span class="font-mono text-slate-300">#{{ $user->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Cartão da Empresa & Licença -->
            @if($tenant)
            <div class="mt-6 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl text-left space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300">
                            <i class="fa-solid fa-building text-xs"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-white">Empresa & Licença</h4>
                            <p class="text-[10px] text-slate-400">Informações da subscrição</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $isTrial ? ($trialDaysLeft <= 3 ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30') : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' }}">
                        {{ $isTrial ? 'Trial' : 'Activo' }}
                    </span>
                </div>

                <div class="space-y-2.5 text-xs text-slate-400">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Razão Social / Nome</span>
                        <span class="font-bold text-white text-sm">{{ $tenant->name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Ramo de Actividade</span>
                            <span class="font-semibold text-slate-300">{{ $tenant->business_type_label ?? 'Geral' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">NUIT</span>
                            <span class="font-semibold text-slate-300">{{ $tenant->nuit ?? 'Não registado' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-800/80">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Modalidade</span>
                            <span class="font-mono text-emerald-400 text-xs uppercase font-bold">{{ $tenant->installation_mode ?? 'Cloud' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Plano</span>
                            <span class="font-bold text-slate-200">{{ $planName }}</span>
                        </div>
                    </div>

                    @if($latestKey)
                    <div class="pt-2 border-t border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Chave de Licença Registada</span>
                        <div class="mt-1 flex items-center justify-between p-2 rounded-xl bg-slate-950 border border-slate-800 font-mono text-[11px] text-emerald-400">
                            <span>{{ $latestKey->key_code ?? 'Chave activada' }}</span>
                            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-800/80">
                    <a href="{{ route('license.activate') }}" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-key {{ $theme['text_accent'] }}"></i>
                        <span>Activar Código / Licença</span>
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Formulários de Edição -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Atualizar Informações Básicas -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black font-heading text-white">Dados Pessoais</h4>
                            <p class="text-xs text-slate-400">Actualize o seu nome, email de acesso e preferências.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome Completo *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            @error('name')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email de Acesso *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            @error('email')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(auth()->user()->is_admin && isset($roles))
                            <div>
                                <label for="role_id" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Função / Cargo *</label>
                                <select id="role_id" 
                                        name="role_id" 
                                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Função</label>
                                <input type="text" 
                                       value="{{ $user->role_display }}" 
                                       disabled
                                       class="w-full px-4 py-2.5 bg-slate-950/50 border border-slate-800/80 rounded-xl text-slate-400 text-xs cursor-not-allowed">
                            </div>
                        @endif

                        <div class="flex items-center pt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-xs font-bold text-slate-300">Conta Activa no Sistema</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Alterações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Alterar Senha -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black font-heading text-white">Segurança & Palavra-passe</h4>
                            <p class="text-xs text-slate-400">Recomendamos utilizar uma palavra-passe forte com letras, números e símbolos.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.change-password') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="current_password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Senha Actual *</label>
                            <div class="relative">
                                <input :type="currentPasswordVisible ? 'text' : 'password'" 
                                       id="current_password" 
                                       name="current_password" 
                                       required
                                       class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs pr-10 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <button type="button" 
                                        @click="currentPasswordVisible = !currentPasswordVisible" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                                    <i class="fa-solid" :class="currentPasswordVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nova Senha *</label>
                            <div class="relative">
                                <input :type="newPasswordVisible ? 'text' : 'password'" 
                                       id="password" 
                                       name="password" 
                                       required
                                       class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs pr-10 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <button type="button" 
                                        @click="newPasswordVisible = !newPasswordVisible" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                                    <i class="fa-solid" :class="newPasswordVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Confirmar Senha *</label>
                            <div class="relative">
                                <input :type="confirmPasswordVisible ? 'text' : 'password'" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required
                                       class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs pr-10 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <button type="button" 
                                        @click="confirmPasswordVisible = !confirmPasswordVisible" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                                    <i class="fa-solid" :class="confirmPasswordVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 font-bold text-xs hover:bg-amber-500/30 transition flex items-center gap-2">
                            <i class="fa-solid fa-lock"></i> Actualizar Palavra-passe
                        </button>
                    </div>
                </form>
            </div>

            <!-- Zona de Perigo -->
            <div class="bg-rose-950/20 border border-rose-900/40 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-black font-heading text-rose-400 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> Zona Crítica
                        </h4>
                        <p class="text-xs text-slate-400 mt-1">Ao eliminar a conta, todos os acessos directos serão desvinculados permanentemente.</p>
                    </div>
                    <button type="button" 
                            @click="showDeleteModal = true" 
                            class="px-4 py-2 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold hover:bg-rose-500/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i> Eliminar Conta
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal de Upload de Foto -->
    <div x-cloak x-show="showPhotoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showPhotoModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-camera {{ $theme['text_accent'] }}"></i> Alterar Foto de Perfil
                </h3>
                <button @click="showPhotoModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ route('profile.update-photo') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('patch')

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ficheiro de Imagem (JPG, PNG ou GIF - Máx: 2MB)</label>
                    <input type="file" 
                           name="photo" 
                           accept="image/*" 
                           required
                           class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-700">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showPhotoModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-700">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black">Submeter Foto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Eliminar Conta -->
    <div x-cloak x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showDeleteModal = false" class="bg-slate-900 border border-rose-900/60 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-rose-400 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminação
                </h3>
                <button @click="showDeleteModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Tem a certeza que deseja eliminar a sua conta? Esta acção é irreversível e todos os seus privilégios serão revogados.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="password_delete" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Introduza a sua senha para confirmar</label>
                    <input type="password" 
                           id="password_delete" 
                           name="password" 
                           required 
                           placeholder="Palavra-passe actual"
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-700">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-bold hover:bg-rose-600">Sim, Eliminar Definitivamente</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
