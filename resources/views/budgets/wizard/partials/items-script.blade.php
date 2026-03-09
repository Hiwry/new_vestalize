<script>
    let currentStep = 1;
    let maxStep = 4;

    let options = {};
    let optionsWithParents = {};
    let selectedPersonalizacoes = [];

    document.addEventListener('DOMContentLoaded', function() {
        loadOptions();
    });

    function openItemModal() {
        document.getElementById('item-modal').classList.remove('hidden');
        resetWizard();
    }

    function closeItemModal() {
        document.getElementById('item-modal').classList.add('hidden');
    }

    function resetWizard() {
        currentStep = 1;
        updateWizardUI();
    }

    function changeStep(direction) {
        if (direction === 1 && !validateStep(currentStep)) return;

        currentStep += direction;
        if (currentStep < 1) currentStep = 1;
        if (currentStep > maxStep) currentStep = maxStep;

        updateWizardUI();
    }

    function updateWizardUI() {
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(`step-${currentStep}`).classList.remove('hidden');
        document.getElementById('current-step-label').textContent = currentStep;

        document.querySelectorAll('.step-indicator').forEach(el => {
            const step = parseInt(el.dataset.step);
            const circle = el.querySelector('div');
            const label = el.querySelector('span');

            if (step === currentStep) {
                circle.className = "w-8 h-8 rounded-full flex items-center justify-center bg-purple-600 text-white font-bold text-sm ring-4 ring-purple-100 dark:ring-purple-900/30";
                circle.style.setProperty('color', 'white', 'important');
                label.className = "text-xs font-medium mt-2 text-purple-600 dark:text-purple-400";
            } else if (step < currentStep) {
                circle.className = "w-8 h-8 rounded-full flex items-center justify-center bg-green-500 text-white font-bold text-sm";
                circle.innerHTML = "";
                circle.style.setProperty('color', 'white', 'important');
                label.className = "text-xs font-medium mt-2 text-green-600";
            } else {
                circle.className = "w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold text-sm";
                circle.innerHTML = step;
                circle.style.color = "";
                circle.style.cssText = "";
                label.className = "text-xs font-medium mt-2 text-gray-500";
            }
        });

        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        const btnSave = document.getElementById('btn-save');

        if (currentStep === 1) {
            btnPrev.classList.add('hidden');
        } else {
            btnPrev.classList.remove('hidden');
        }

        if (currentStep === maxStep) {
            btnNext.classList.add('hidden');
            btnSave.classList.remove('hidden');
        } else {
            btnNext.classList.remove('hidden');
            btnSave.classList.add('hidden');
        }
    }

    function validateStep(step) {
        let isValid = true;
        const errorEl = document.getElementById(`step-${step}-error`);
        if (errorEl) errorEl.classList.add('hidden');

        if (step === 1) {
            if (selectedPersonalizacoes.length === 0) isValid = false;
        } else if (step === 2) {
            const tecido = document.getElementById('tecido').value;
            const cor = document.getElementById('cor').value;
            if (!tecido || !cor) isValid = false;
        } else if (step === 3) {
            const corte = document.getElementById('tipo_corte').value;
            if (!corte) isValid = false;
        }

        if (!isValid && errorEl) {
            errorEl.classList.remove('hidden');
        }

        return isValid;
    }

    function loadOptions() {
        fetch('/api/product-options')
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                options = data;
                return fetch('/api/product-options-with-parents');
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                optionsWithParents = data;
                renderPersonalizacao();
                renderAllDropdowns();
            })
            .catch(error => {
                console.error('Error loading options:', error);
                document.getElementById('personalizacao-options').innerHTML =
                    '<div class="col-span-full text-center text-red-500 py-4">Erro ao carregar opções. Recarregue a página.</div>';
            });
    }

    function getIconStyle(name) {
        const n = name.toLowerCase().trim();
        if (n.includes('local')) return { icon: 'fa-fire', color: 'text-[#7c3aed]', bg: 'bg-purple-100 dark:bg-purple-900/30' };
        if (n.includes('serigrafia')) return { icon: 'fa-fill-drip', color: 'text-purple-600 dark:text-purple-400', bg: 'bg-purple-100 dark:bg-purple-900/30' };
        if (n.includes('dtf')) return { icon: 'fa-print', color: 'text-orange-600 dark:text-orange-400', bg: 'bg-orange-100 dark:bg-orange-900/30' };
        if (n.includes('bordado')) return { icon: 'fa-pen-nib', color: 'text-pink-600 dark:text-pink-400', bg: 'bg-pink-100 dark:bg-pink-900/30' };
        if (n.includes('emborrachado')) return { icon: 'fa-cube', color: 'text-green-600 dark:text-green-400', bg: 'bg-green-100 dark:bg-green-900/30' };
        if (n.includes('lisa')) return { icon: 'fa-star', color: 'text-gray-600 dark:text-gray-400', bg: 'bg-gray-100 dark:bg-gray-700' };
        if (n.includes('total')) return { icon: 'fa-image', color: 'text-white', bg: 'bg-gradient-to-br from-purple-500 to-fuchsia-600 shadow-md' };
        return { icon: 'fa-layer-group', color: 'text-gray-600', bg: 'bg-gray-100' };
    }

    function renderPersonalizacao() {
        const container = document.getElementById('personalizacao-options');
        const items = optionsWithParents.personalizacao || options.personalizacao || [];
        const form = document.getElementById('item-form');

        const existingInputs = form.querySelectorAll('input[name="personalizacao[]"]');
        existingInputs.forEach(input => input.remove());

        selectedPersonalizacoes.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'personalizacao[]';
            input.value = id;
            form.appendChild(input);
        });

        container.innerHTML = items.map(item => {
            const style = getIconStyle(item.name);
            const isSelected = selectedPersonalizacoes.includes(item.id);
            const borderClass = isSelected ? 'border-[#7c3aed] ring-2 ring-[#7c3aed]/20' : 'border-gray-200 dark:border-gray-700';

            return `
            <div onclick="togglePersonalizacao(${item.id})"
                 class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:border-[#7c3aed] transition-all group hover:shadow-lg ${borderClass} bg-white dark:bg-gray-800">

                ${isSelected ? `
                <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-[#7c3aed] text-white flex items-center justify-center">
                    <i class="fa-solid fa-check text-[10px]"></i>
                </div>` : ''}

                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform ${style.bg}">
                    <i class="fa-solid ${style.icon} text-xl ${style.color}"></i>
                </div>

                <span class="text-xs font-bold text-center text-gray-900 dark:text-gray-100 uppercase tracking-wide">${item.name}</span>
            </div>
            `;
        }).join('');
    }

    function togglePersonalizacao(id) {
        const index = selectedPersonalizacoes.indexOf(id);
        if (index > -1) selectedPersonalizacoes.splice(index, 1);
        else selectedPersonalizacoes.push(id);

        renderPersonalizacao();
        renderAllDropdowns();
        updatePrice();
    }

    function renderAllDropdowns() {
        const tecidoId = parseInt(document.getElementById('tecido').value) || null;
        const tipoTecidoId = parseInt(document.getElementById('tipo_tecido').value) || null;
        const tipoCorteId = parseInt(document.getElementById('tipo_corte').value) || null;

        let activeParentIds = [...selectedPersonalizacoes];
        if (tecidoId) activeParentIds.push(tecidoId);
        if (tipoTecidoId) activeParentIds.push(tipoTecidoId);
        if (tipoCorteId) activeParentIds.push(tipoCorteId);

        renderSelect('tecido', optionsWithParents.tecido || [], null, selectedPersonalizacoes);

        const tiposTecido = (options.tipo_tecido || []).filter(t => t.parent_id == tecidoId);
        const tipoContainer = document.getElementById('tipo-tecido-container');
        if (tecidoId && tiposTecido.length > 0) {
            tipoContainer.classList.remove('hidden');
            renderSelect('tipo_tecido', tiposTecido, null, []);
        } else {
            tipoContainer.classList.add('hidden');
        }

        renderSelect('cor', optionsWithParents.cor || [], null, activeParentIds);
        renderSelect('tipo_corte', optionsWithParents.tipo_corte || [], null, activeParentIds);

        const corteParentIds = tipoCorteId ? [tipoCorteId] : [];
        renderSelect('gola', optionsWithParents.gola || [], null, corteParentIds);
        renderSelect('detalhe', optionsWithParents.detalhe || [], null, corteParentIds);

        updatePrice();
    }

    function renderSelect(id, items, selectedValue, parentIdsToCheck) {
        const select = document.getElementById(id);
        if (!select) return;

        let filtered = items;
        if (parentIdsToCheck && parentIdsToCheck.length > 0) {
            filtered = items.filter(item => {
                if (!item.parent_ids || item.parent_ids.length === 0) return true;
                return item.parent_ids.some(pid => parentIdsToCheck.includes(pid));
            });
        }

        const current = selectedValue || select.value;
        const defaultTxt = select.options[0] ? select.options[0].text : 'Selecione...';

        select.innerHTML = `<option value="">${defaultTxt}</option>` +
            filtered.map(i => `<option value="${i.id}" data-price="${i.price}">${i.name} ${i.price > 0 ? '(+R$' + i.price + ')' : ''}</option>`).join('');

        if (current && filtered.find(x => x.id == current)) select.value = current;
    }

    window.loadTiposTecido = function() { renderAllDropdowns(); }
    window.updatePrice = function() {
        const getP = id => {
            const el = document.getElementById(id);
            return el && el.selectedOptions[0] ? parseFloat(el.selectedOptions[0].dataset.price || 0) : 0;
        };
        const total = getP('tipo_corte') + getP('gola') + getP('detalhe');
        document.getElementById('price-total-display').innerText = 'R$ ' + total.toFixed(2).replace('.', ',');
        document.getElementById('unit_price').value = total;
    }

    window.removeItem = function(index) {
        if (!confirm('Remover este item?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("budget.items") }}';
        form.innerHTML = `@csrf <input type="hidden" name="action" value="remove_item"><input type="hidden" name="item_index" value="${index}">`;
        document.body.appendChild(form);
        form.submit();
    }
</script>
