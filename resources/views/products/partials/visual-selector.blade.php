@php
    $currentIcon = old('icon', $product->icon ?? ($isPharmacy ?? false ? 'fa-solid fa-pills' : 'fa-solid fa-box'));
    $currentImageUrl = isset($product) ? $product->image_url : null;
    $presetIcons = [
        ['class' => 'fa-solid fa-box',                'label' => 'Caixa / Geral'],
        ['class' => 'fa-solid fa-pills',              'label' => 'Comprimidos'],
        ['class' => 'fa-solid fa-capsules',           'label' => 'Cápsulas'],
        ['class' => 'fa-solid fa-prescription-bottle-medical', 'label' => 'Xarope / Frasco'],
        ['class' => 'fa-solid fa-syringe',            'label' => 'Injetável'],
        ['class' => 'fa-solid fa-kit-medical',        'label' => 'Primeiros Socorros'],
        ['class' => 'fa-solid fa-bottle-droplet',     'label' => 'Líquido / Óleo'],
        ['class' => 'fa-solid fa-basket-shopping',    'label' => 'Mercearia'],
        ['class' => 'fa-solid fa-apple-whole',        'label' => 'Frescos / Fruta'],
        ['class' => 'fa-solid fa-wine-bottle',        'label' => 'Bebidas'],
        ['class' => 'fa-solid fa-utensils',           'label' => 'Restaurante'],
        ['class' => 'fa-solid fa-burger',             'label' => 'Lanche / Fast-Food'],
        ['class' => 'fa-solid fa-mug-hot',            'label' => 'Cafetaria'],
        ['class' => 'fa-solid fa-shirt',              'label' => 'Vestuário / Moda'],
        ['class' => 'fa-solid fa-mobile-screen',      'label' => 'Eletrónica'],
        ['class' => 'fa-solid fa-print',              'label' => 'Reprografia'],
        ['class' => 'fa-solid fa-screwdriver-wrench', 'label' => 'Serviço Técnico'],
        ['class' => 'fa-solid fa-tag',                'label' => 'Promocional'],
    ];
@endphp

<div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 space-y-4"
     x-data="{
         selectedIcon: @js($currentIcon),
         previewUrl: @js($currentImageUrl),
         removeImage: false,
         compressing: false,
         compressionInfo: '',
         errorMsg: '',
         async handleImageUpload(event) {
             const file = event.target.files?.[0];
             if (!file) return;
             if (!file.type.startsWith('image/')) {
                 this.errorMsg = 'Selecione um ficheiro de imagem válido (JPG, PNG ou WebP).';
                 return;
             }
             this.errorMsg = '';
             this.compressing = true;
             this.removeImage = false;
             const originalKB = (file.size / 1024).toFixed(1);

             try {
                 const result = await this.compressImageToMax128KB(file, 128 * 1024);
                 this.previewUrl = result.dataUrl;
                 this.$refs.compressedInput.value = result.dataUrl;
                 const finalKB = (result.byteSize / 1024).toFixed(1);

                 // Atualiza também o input[type=file] com o ficheiro leve (<= 128 KB)
                 if (window.DataTransfer && result.blob) {
                     const ext = result.mimeType === 'image/webp' ? 'webp' : 'jpg';
                     const compressedFile = new File([result.blob], 'product_opt.' + ext, { type: result.mimeType });
                     const dt = new DataTransfer();
                     dt.items.add(compressedFile);
                     event.target.files = dt.files;
                 }

                 this.compressionInfo = `Otimizada automaticamente: ${originalKB} KB → ${finalKB} KB (≤ 128 KB)`;
             } catch (err) {
                 console.error('Erro na compressão automática:', err);
                 this.errorMsg = 'Não foi possível otimizar a imagem selecionada.';
             } finally {
                 this.compressing = false;
             }
         },
         clearSelectedImage() {
             this.previewUrl = null;
             this.removeImage = true;
             this.compressionInfo = '';
             if (this.$refs.compressedInput) this.$refs.compressedInput.value = '';
             if (this.$refs.fileInput) this.$refs.fileInput.value = '';
         },
         compressImageToMax128KB(file, maxBytes) {
             return new Promise((resolve, reject) => {
                 const reader = new FileReader();
                 reader.onerror = () => reject(new Error('Falha ao ler ficheiro'));
                 reader.onload = (e) => {
                     const img = new Image();
                     img.onerror = () => reject(new Error('Falha ao descodificar imagem'));
                     img.onload = () => {
                         let maxDim = 640;
                         let qualitySteps = [0.82, 0.70, 0.58, 0.45, 0.35, 0.25];
                         const canvas = document.createElement('canvas');
                         const ctx = canvas.getContext('2d');

                         const renderAndEncode = (dim, quality, mimeType) => {
                             let w = img.width;
                             let h = img.height;
                             if (w > dim || h > dim) {
                                 if (w > h) {
                                     h = Math.round((h * dim) / w);
                                     w = dim;
                                 } else {
                                     w = Math.round((w * dim) / h);
                                     h = dim;
                                 }
                             }
                             canvas.width = w;
                             canvas.height = h;
                             ctx.clearRect(0, 0, w, h);
                             ctx.drawImage(img, 0, 0, w, h);
                             return canvas.toDataURL(mimeType, quality);
                         };

                         const dataUrlByteSize = (dataUrl) => {
                             const base64 = dataUrl.split(',')[1] || '';
                             return Math.ceil((base64.length * 3) / 4);
                         };

                         // Tenta WebP primeiro (com fallback para JPEG)
                         let testDataUrl = renderAndEncode(maxDim, 0.80, 'image/webp');
                         let mimeType = testDataUrl.startsWith('data:image/webp') ? 'image/webp' : 'image/jpeg';

                         let bestDataUrl = '';
                         let bestSize = Infinity;

                         for (let dim of [640, 480, 360]) {
                             for (let q of qualitySteps) {
                                 const candidate = renderAndEncode(dim, q, mimeType);
                                 const size = dataUrlByteSize(candidate);
                                 bestDataUrl = candidate;
                                 bestSize = size;
                                 if (size <= maxBytes) {
                                     break;
                                 }
                             }
                             if (bestSize <= maxBytes) break;
                         }

                         canvas.toBlob((blob) => {
                             resolve({
                                 dataUrl: bestDataUrl,
                                 byteSize: bestSize,
                                 mimeType: mimeType,
                                 blob: blob
                             });
                         }, mimeType, 0.72);
                     };
                     img.src = e.target.result;
                 };
                 reader.readAsDataURL(file);
             });
         }
     }">

    <input type="hidden" name="icon" :value="selectedIcon">
    <input type="hidden" name="compressed_image_data" x-ref="compressedInput">
    <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-image text-sky-500"></i>
                <span>Identidade Visual do Artigo (Ícone ou Fotografia ≤ 128 KB)</span>
            </h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                Escolha um ícone representativo ou carregue uma foto — imagens grandes são otimizadas automaticamente para até 128 KB.
            </p>
        </div>
        <span x-show="compressionInfo" x-cloak
              class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold">
            <i class="fa-solid fa-bolt"></i>
            <span x-text="compressionInfo"></span>
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        {{-- Coluna Esquerda: Preview + Upload de Imagem com Compressor Invisível --}}
        <div class="lg:col-span-5 flex items-center gap-4 p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="relative w-20 h-20 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                <template x-if="previewUrl">
                    <img :src="previewUrl" alt="Preview do Produto" class="w-full h-full object-cover">
                </template>
                <template x-if="!previewUrl">
                    <i :class="selectedIcon" class="text-2xl text-sky-600 dark:text-sky-400"></i>
                </template>

                <div x-show="compressing" x-cloak class="absolute inset-0 bg-slate-950/70 flex items-center justify-center text-white text-xs">
                    <i class="fa-solid fa-spinner animate-spin"></i>
                </div>
            </div>

            <div class="flex-1 min-w-0 space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <label class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs cursor-pointer transition shadow-sm">
                        <i class="fa-solid fa-camera"></i>
                        <span x-text="previewUrl ? 'Trocar Imagem' : 'Carregar Imagem'"></span>
                        <input type="file"
                               name="image"
                               x-ref="fileInput"
                               accept="image/*"
                               @change="handleImageUpload($event)"
                               class="hidden">
                    </label>

                    <button type="button"
                            x-show="previewUrl"
                            x-cloak
                            @click="clearSelectedImage()"
                            class="px-2.5 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 text-xs font-bold transition"
                            title="Remover fotografia e usar ícone">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>

                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">
                    Suporta fotos de telemóvel ou câmara (JPG, PNG, WebP). Conversor invisível integrado (máx. <strong>128 KB</strong>).
                </p>

                <p x-show="errorMsg" x-cloak x-text="errorMsg" class="text-[11px] font-bold text-rose-600 dark:text-rose-400"></p>
            </div>
        </div>

        {{-- Coluna Direita: Grelha de Ícones Rápidos --}}
        <div class="lg:col-span-7 space-y-2">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                Ou escolha um Ícone Rápido do Catálogo:
            </label>
            <div class="grid grid-cols-6 sm:grid-cols-9 gap-2">
                @foreach($presetIcons as $preset)
                    <button type="button"
                            @click="selectedIcon = '{{ $preset['class'] }}'"
                            :class="selectedIcon === '{{ $preset['class'] }}'
                                ? 'bg-sky-600 text-white border-sky-600 ring-2 ring-sky-500/30 shadow-sm'
                                : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-sky-500/50'"
                            class="h-10 rounded-xl border flex items-center justify-center text-sm transition cursor-pointer"
                            title="{{ $preset['label'] }}">
                        <i class="{{ $preset['class'] }}"></i>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
