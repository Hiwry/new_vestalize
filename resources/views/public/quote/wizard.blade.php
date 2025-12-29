<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $settings->primary_color }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .step-active { @apply border-indigo-600 text-indigo-600; }
        .step-inactive { @apply border-gray-200 text-gray-400; }
        /* Dynamic Primary Color */
        :root {
            --primary-color: {{ $settings->primary_color }};
        }
        .text-primary { color: var(--primary-color); }
        .bg-primary { background-color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .ring-primary { --tw-ring-color: var(--primary-color); }
        .hover\:bg-primary-dark:hover { filter: brightness(0.9); background-color: var(--primary-color); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-900 flex flex-col items-center py-10">

    <!-- Header -->
    <div class="text-center mb-10 px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $settings->title }}</h1>
        <p class="text-gray-500">{{ $settings->description ?? 'Preencha os dados abaixo e receba seu orçamento via WhatsApp' }}</p>
    </div>

    <!-- Wizard Container -->
    <div x-data="quoteWizard()" class="w-full max-w-3xl px-4">
        
        <!-- Progress Steps -->
        <div class="flex justify-center items-center mb-10 space-x-4 md:space-x-8">
            <!-- Step 1: Produto -->
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-colors duration-300"
                         :class="step >= 1 ? 'border-primary text-primary bg-indigo-50' : 'border-gray-300 text-gray-400'">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <span class="text-xs font-medium mt-2" :class="step >= 1 ? 'text-primary' : 'text-gray-400'">Produto</span>
                </div>
                <div class="h-0.5 w-12 md:w-20 bg-gray-200 mx-2 mb-6" :class="step > 1 ? 'bg-primary' : ''"></div>
            </div>

            <!-- Step 2: Quantidade -->
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-colors duration-300"
                         :class="step >= 2 ? 'border-primary text-primary bg-indigo-50' : 'border-gray-300 text-gray-400'">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium mt-2" :class="step >= 2 ? 'text-primary' : 'text-gray-400'">Quantidade</span>
                </div>
                <div class="h-0.5 w-12 md:w-20 bg-gray-200 mx-2 mb-6" :class="step > 2 ? 'bg-primary' : ''"></div>
            </div>

            <!-- Step 3: Logo -->
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-colors duration-300"
                         :class="step >= 3 ? 'border-primary text-primary bg-indigo-50' : 'border-gray-300 text-gray-400'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-xs font-medium mt-2" :class="step >= 3 ? 'text-primary' : 'text-gray-400'">Logo</span>
                </div>
                <div class="h-0.5 w-12 md:w-20 bg-gray-200 mx-2 mb-6" :class="step > 3 ? 'bg-primary' : ''"></div>
            </div>

            <!-- Step 4: Contato -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-colors duration-300"
                     :class="step >= 4 ? 'border-primary text-primary bg-indigo-50' : 'border-gray-300 text-gray-400'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-xs font-medium mt-2" :class="step >= 4 ? 'text-primary' : 'text-gray-400'">Contato</span>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-10 relative overflow-hidden">
            
            <!-- Loading Overlay -->
            <div x-show="loading" class="absolute inset-0 bg-white/90 z-50 flex flex-col items-center justify-center backdrop-blur-sm" x-transition>
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-indigo-600"></div>
                <p class="mt-4 text-gray-600 font-medium">Processando seu orçamento...</p>
            </div>

            <!-- STEP 1: SELECT PRODUCT -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <h2 class="text-xl font-bold text-center mb-8">1. Qual produto você deseja?</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($settings->products_json as $index => $product)
                    <div @click="selectProduct({{ $index }})" 
                         class="cursor-pointer border-2 rounded-xl p-6 flex flex-col items-center text-center transition-all duration-200 hover:shadow-md hover:border-primary group"
                         :class="selectedProductIndex === {{ $index }} ? 'border-primary bg-indigo-50/10 ring-1 ring-primary' : 'border-gray-200'">
                         
                        <div class="w-16 h-16 mb-4 text-gray-600 group-hover:text-primary transition-colors">
                            @if(($product['icon'] ?? 't-shirt') == 't-shirt')
                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            @elseif(($product['icon'] ?? '') == 'polo')
                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            @else
                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            @endif
                        </div>
                        
                        <h3 class="font-bold text-gray-900 mb-2">{{ $product['name'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $product['description'] }}</p>

                        <!-- Checked Icon -->
                        <div x-show="selectedProductIndex === {{ $index }}" class="absolute top-2 right-2 text-primary">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- STEP 2: QUANTITY -->
            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <h2 class="text-xl font-bold text-center mb-8">2. Qual a quantidade?</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="opt in quantityOptions">
                        <div @click="selectQuantity(opt.value)" 
                             class="cursor-pointer border-2 rounded-xl p-6 flex flex-col items-center justify-center text-center transition-all duration-200 hover:shadow-md hover:border-primary"
                             :class="quantity === opt.value ? 'border-primary bg-indigo-50/10 ring-1 ring-primary' : 'border-gray-200'">
                            <span class="text-2xl font-bold mb-1" x-text="opt.label"></span>
                            <span class="text-xs text-gray-500" x-text="opt.desc"></span>
                        </div>
                    </template>
                    
                    <!-- Outra Quantidade Input -->
                    <div @click="selectQuantity('other')" 
                         class="cursor-pointer border-2 rounded-xl p-6 flex flex-col items-center justify-center text-center transition-all duration-200 hover:shadow-md hover:border-primary relative"
                         :class="quantity === 'other' ? 'border-primary bg-indigo-50/10 ring-1 ring-primary' : 'border-gray-200'">
                        <span class="text-lg font-bold mb-1">Outra quantidade</span>
                        <div x-show="quantity === 'other'" class="mt-2 w-full" @click.stop>
                            <input type="number" x-model="quantityOther" placeholder="Digite a quantidade" class="w-full text-center border-b-2 border-primary focus:outline-none bg-transparent py-1">
                        </div>
                        <span x-show="quantity !== 'other'" class="text-xs text-gray-500">Especifique sua necessidade</span>
                    </div>
                </div>
            </div>

            <!-- STEP 3: LOGO -->
            <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <h2 class="text-xl font-bold text-center mb-8">3. Upload da Logo (Opcional)</h2>

                <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:bg-gray-50 transition-colors relative"
                     :class="logoFile ? 'border-primary bg-indigo-50/10' : ''">
                    
                    <input type="file" x-ref="logoInput" @change="handleLogoUpload" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    
                    <div x-show="!logoFile">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Clique para selecionar ou arraste o arquivo</p>
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, PDF até 10MB</p>
                    </div>

                    <div x-show="logoFile" class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium text-gray-900" x-text="logoFile ? logoFile.name : ''"></p>
                        <button type="button" @click.stop.prevent="logoFile = null; $refs.logoInput.value = ''" class="mt-2 text-xs text-red-600 hover:text-red-800 underline">Remover arquivo</button>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">Você também pode enviar a logo posteriormente via WhatsApp</p>
                </div>
            </div>

            <!-- STEP 4: CONTACT -->
            <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <h2 class="text-xl font-bold text-center mb-6">4. Seus Dados de Contato</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seu Nome</label>
                        <input type="text" x-model="contact.name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-3 border" placeholder="Nome completo">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                        <input type="tel" x-model="contact.phone" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-3 border" placeholder="(00) 00000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Empresa (Opcional)</label>
                        <input type="text" x-model="contact.company" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-3 border" placeholder="Nome da sua empresa">
                    </div>
                </div>
            </div>

            <!-- SUCCESS -->
            <div x-show="step === 5" x-cloak class="text-center py-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Orçamento Gerado!</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">Seu PDF foi gerado com sucesso. Clique abaixo para enviar ao nosso time comercial e iniciar seu atendimento.</p>

                <div class="flex flex-col space-y-3 max-w-sm mx-auto">
                    <a :href="whatsappUrl" target="_blank" class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 md:text-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.015-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487 2.182.941 2.623.755 3.565.709.941-.046 2.08-.85 2.376-1.67.298-.819.298-1.522.209-1.67z"/></svg>
                        Enviar no WhatsApp
                    </a>
                    
                    <a :href="pdfUrl" download class="block w-full text-center py-2 text-sm text-gray-500 hover:text-gray-900 underline">
                        Baixar PDF apenas
                    </a>
                </div>
                
                <button @click="reset()" class="mt-8 text-sm text-gray-400 hover:text-gray-600">
                    Iniciar novo orçamento
                </button>
            </div>

            <!-- Navigation Buttons -->
            <div x-show="step < 5" class="mt-10 flex justify-between pt-6 border-t border-gray-100">
                <button x-show="step > 1" @click="step--" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-medium hover:bg-gray-50 transition">
                    &larr; Anterior
                </button>
                <div x-show="step === 1" class="flex-1"></div> <!-- Spacer for step 1 -->
                
                <button @click="nextStep()" 
                        class="px-8 py-2 bg-gray-900 text-white font-medium rounded-md hover:bg-gray-800 transition shadow-md hover:shadow-lg flex items-center ml-auto"
                        :disabled="!canProceed"
                        :class="!canProceed ? 'opacity-50 cursor-not-allowed' : ''">
                    <span x-text="step === 4 ? 'Finalizar e Gerar PDF' : 'Próximo'"></span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('quoteWizard', () => ({
                step: 1,
                loading: false,
                selectedProductIndex: null,
                quantity: null,
                quantityOther: '',
                logoFile: null,
                contact: { name: '', phone: '', company: '' },
                pdfUrl: '#',
                whatsappUrl: '#',
                
                quantityOptions: [
                    { value: '25', label: '25 peças', desc: 'Ideal para equipes pequenas' },
                    { value: '50', label: '50 peças', desc: 'Ideal para eventos médios' },
                    { value: '100', label: '100 peças', desc: 'Ideal para grandes empresas' },
                ],

                selectProduct(index) {
                    this.selectedProductIndex = index;
                },

                selectQuantity(value) {
                    this.quantity = value;
                    if (value !== 'other') this.quantityOther = '';
                },

                handleLogoUpload(e) {
                    if (e.target.files.length > 0) {
                        this.logoFile = e.target.files[0];
                    }
                },

                get canProceed() {
                    if (this.step === 1) return this.selectedProductIndex !== null;
                    if (this.step === 2) return this.quantity && (this.quantity !== 'other' || this.quantityOther);
                    if (this.step === 3) return true; // Logo optional
                    if (this.step === 4) return this.contact.name && this.contact.phone;
                    return false;
                },

                async nextStep() {
                    if (this.step === 4) {
                        await this.submitForm();
                    } else {
                        this.step++;
                    }
                },

                async submitForm() {
                    this.loading = true;
                    
                    const formData = new FormData();
                    formData.append('product_index', this.selectedProductIndex);
                    formData.append('quantity', this.quantity);
                    formData.append('quantity_other', this.quantityOther);
                    if (this.logoFile) formData.append('logo_upload', this.logoFile);
                    formData.append('has_logo', this.logoFile ? '1' : '0');
                    formData.append('contact_name', this.contact.name);
                    formData.append('contact_phone', this.contact.phone);
                    formData.append('contact_company', this.contact.company);
                    formData.append('step', 'finish');

                    try {
                        // Using fetch to post to current URL (the PublicQuoteController submit)
                        const response = await fetch(window.location.href, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            this.pdfUrl = result.pdf_url;
                            this.whatsappUrl = result.whatsapp_url;
                            this.step = 5;
                        } else {
                            alert('Ocorreu um erro. Tente novamente.');
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Erro ao processar solicitação.');
                    } finally {
                        this.loading = false;
                    }
                },
                
                reset() {
                    this.step = 1;
                    this.selectedProductIndex = null;
                    this.quantity = null;
                    this.quantityOther = '';
                    this.logoFile = null;
                    this.contact = { name: '', phone: '', company: '' };
                }
            }));
        });
    </script>
</body>
</html>
