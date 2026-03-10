<div class="pdv-catalog-head">
    <div>
        <span>Itens disponiveis</span>
        <h3 id="pdv-catalog-title">{{ $currentTypeLabel }}</h3>
    </div>
    <p class="pdv-catalog-note">Clique em adicionar para abrir a configuracao do item e enviar direto para o carrinho.</p>
</div>

<div class="pdv-catalog-body">
    <div id="products-grid-container">
        @include('pdv.partials.grid')
    </div>
</div>
