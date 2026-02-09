<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SublimationProductType;
use App\Models\SublimationProductPrice;
use App\Models\SublimationProductAddon;

class SublimationTotalSeeder extends Seeder
{
    /**
     * Seed Sublimação Total pricing and addons based on pricing table.
     * 
     * TABELA DE PREÇOS:
     * Quantidade | Preço Base (R$)
     * 10-29      | 36,00
     * 30-49      | 34,95
     * 50-99      | 33,95
     * 100-299    | 32,30
     * 300-499    | 30,75
     * 500-999    | 29,25
     * 1000+      | 27,85
     */
    public function run(): void
    {
        $this->command->info(' Seeding Sublimação Total prices and addons...');

        // 1. Get or create the Poliéster fabric ID
        $poliesterId = DB::table('product_options')
            ->where('type', 'tecido')
            ->where('name', 'Poliéster')
            ->value('id');

        if (!$poliesterId) {
            $this->command->warn(' Poliéster fabric not found. Creating it...');
            $poliesterId = DB::table('product_options')->insertGetId([
                'type' => 'tecido',
                'name' => 'Poliéster',
                'price' => 0.00,
                'active' => 1,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Create Sublimation Product Type for "Camisa Sublimação Total"
        $sublimationType = SublimationProductType::updateOrCreate(
            ['slug' => 'camisa-sublimacao-total'],
            [
                'tenant_id' => null, // Global type
                'tecido_id' => $poliesterId,
                'name' => 'Camisa Sublimação Total',
                'active' => true,
                'order' => 1,
            ]
        );

        $this->command->info(" Tipo de produto criado: {$sublimationType->name}");

        // 3. Seed quantity-based prices
        $prices = [
            ['quantity_from' => 1,    'quantity_to' => 9,    'price' => 39.00, 'cost' => 25.00],
            ['quantity_from' => 10,   'quantity_to' => 29,   'price' => 36.00, 'cost' => 23.00],
            ['quantity_from' => 30,   'quantity_to' => 49,   'price' => 34.95, 'cost' => 22.00],
            ['quantity_from' => 50,   'quantity_to' => 99,   'price' => 33.95, 'cost' => 21.00],
            ['quantity_from' => 100,  'quantity_to' => 299,  'price' => 32.30, 'cost' => 20.00],
            ['quantity_from' => 300,  'quantity_to' => 499,  'price' => 30.75, 'cost' => 19.00],
            ['quantity_from' => 500,  'quantity_to' => 999,  'price' => 29.25, 'cost' => 18.00],
            ['quantity_from' => 1000, 'quantity_to' => null, 'price' => 27.85, 'cost' => 17.00],
        ];

        foreach ($prices as $priceData) {
            SublimationProductPrice::updateOrCreate(
                [
                    'product_type' => 'camisa-sublimacao-total',
                    'quantity_from' => $priceData['quantity_from'],
                ],
                array_merge($priceData, [
                    'tenant_id' => null,
                    'product_type' => 'camisa-sublimacao-total',
                ])
            );
        }

        $this->command->info(' Preços por quantidade inseridos');

        // 4. Seed addons based on price table from image
        $addons = [
            // Modificadores de modelo (podem ser negativos)
            ['name' => 'Cacharrel', 'price' => -3.00, 'order' => 1],
            ['name' => 'Regata', 'price' => -3.00, 'order' => 2],
            
            // Adicionais de modelo
            ['name' => 'Manga Longa', 'price' => 20.00, 'order' => 3],
            ['name' => 'Polo', 'price' => 20.00, 'order' => 4],
            ['name' => 'Rib Sublimada', 'price' => 3.50, 'order' => 5],
            ['name' => 'Punho Sublimado', 'price' => 3.50, 'order' => 6],
            ['name' => 'Gola V', 'price' => 3.15, 'order' => 7],
            ['name' => 'Gola Padre', 'price' => 5.25, 'order' => 8],
            
            // Adicionais de tecido
            ['name' => 'Aerodry', 'price' => 3.50, 'order' => 9],
            ['name' => 'UV Cache', 'price' => 12.00, 'order' => 10],
            ['name' => 'Crepe Poliéster', 'price' => 10.50, 'order' => 11],
            ['name' => 'Cor Diferenciada', 'price' => 11.67, 'order' => 12],
            ['name' => 'Dryfit', 'price' => 12.00, 'order' => 13],
            ['name' => 'Friso ou Alça', 'price' => 4.00, 'order' => 14],
            ['name' => 'Manga Raglan', 'price' => 7.00, 'order' => 15],
            
            // Adicionais especiais
            ['name' => 'Goleiro', 'price' => 20.00, 'order' => 16],
            ['name' => 'PP Elastano Alure', 'price' => 10.00, 'order' => 17],
            ['name' => 'Manga Longa Dry', 'price' => 38.75, 'order' => 18],
            ['name' => 'Cinza Mesclado', 'price' => 2.00, 'order' => 19],
            
            // Adicionais de personalização
            ['name' => 'Sub Nome/Número', 'price' => 8.00, 'order' => 20],
            ['name' => 'Serigrafia Nome/Número', 'price' => 15.00, 'order' => 21],
            ['name' => 'Material Esportivo', 'price' => 72.00, 'order' => 22],
        ];

        foreach ($addons as $addonData) {
            SublimationProductAddon::updateOrCreate(
                [
                    'product_type' => 'camisa-sublimacao-total',
                    'name' => $addonData['name'],
                ],
                array_merge($addonData, [
                    'tenant_id' => null,
                    'product_id' => null,
                    'product_type' => 'camisa-sublimacao-total',
                    'active' => true,
                ])
            );
        }

        $this->command->info(' Adicionais inseridos: ' . count($addons));

        // 5. Summary table
        $this->command->table(
            ['Faixa de Quantidade', 'Preço (R$)'],
            collect($prices)->map(fn($p) => [
                $p['quantity_to'] ? "{$p['quantity_from']}-{$p['quantity_to']}" : "{$p['quantity_from']}+",
                'R$ ' . number_format($p['price'], 2, ',', '.')
            ])->toArray()
        );

        $this->command->info(' Sublimação Total seeding complete!');
    }
}
