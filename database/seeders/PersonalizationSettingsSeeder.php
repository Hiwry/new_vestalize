<?php

namespace Database\Seeders;

use App\Models\PersonalizationSetting;
use App\Models\PersonalizationSpecialOption;
use Illuminate\Database\Seeder;

class PersonalizationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info(' Criando configurações de personalização...');

        // Configurações por tipo de personalização
        $settings = [
            [
                'personalization_type' => 'DTF',
                'display_name' => 'DTF - Digital Transfer Film',
                'description' => 'Impressão digital de alta qualidade transferida para tecido',
                'charge_by_color' => false,
                'color_price_per_unit' => 0,
                'min_colors' => 1,
                'max_colors' => null,
                'discount_2nd_application' => 50,
                'discount_3rd_application' => 60,
                'discount_4th_plus_application' => 70,
                'has_sizes' => true,
                'has_locations' => true,
                'has_special_options' => true,
                'active' => true,
                'order' => 1,
            ],
            [
                'personalization_type' => 'SERIGRAFIA',
                'display_name' => 'Serigrafia',
                'description' => 'Impressão em tela com tintas de alta durabilidade',
                'charge_by_color' => true,
                'color_price_per_unit' => 2.00,
                'min_colors' => 1,
                'max_colors' => 8,
                'discount_2nd_application' => 50,
                'discount_3rd_application' => 60,
                'discount_4th_plus_application' => 70,
                'has_sizes' => true,
                'has_locations' => true,
                'has_special_options' => true,
                'active' => true,
                'order' => 2,
            ],
            [
                'personalization_type' => 'BORDADO',
                'display_name' => 'Bordado',
                'description' => 'Bordado industrial de alta qualidade',
                'charge_by_color' => true,
                'color_price_per_unit' => 3.00,
                'min_colors' => 1,
                'max_colors' => 6,
                'discount_2nd_application' => 40,
                'discount_3rd_application' => 50,
                'discount_4th_plus_application' => 60,
                'has_sizes' => true,
                'has_locations' => true,
                'has_special_options' => false,
                'active' => true,
                'order' => 3,
            ],
            [
                'personalization_type' => 'EMBORRACHADO',
                'display_name' => 'Emborrachado',
                'description' => 'Aplicação de alto relevo emborrachado',
                'charge_by_color' => true,
                'color_price_per_unit' => 2.50,
                'min_colors' => 1,
                'max_colors' => 4,
                'discount_2nd_application' => 50,
                'discount_3rd_application' => 60,
                'discount_4th_plus_application' => 70,
                'has_sizes' => true,
                'has_locations' => true,
                'has_special_options' => true,
                'active' => true,
                'order' => 4,
            ],
            [
                'personalization_type' => 'SUB. LOCAL',
                'display_name' => 'Sublimação Local',
                'description' => 'Sublimação em área específica da peça',
                'charge_by_color' => false,
                'color_price_per_unit' => 0,
                'min_colors' => 1,
                'max_colors' => null,
                'discount_2nd_application' => 50,
                'discount_3rd_application' => 60,
                'discount_4th_plus_application' => 70,
                'has_sizes' => true,
                'has_locations' => true,
                'has_special_options' => false,
                'active' => true,
                'order' => 5,
            ],
            [
                'personalization_type' => 'SUB. TOTAL',
                'display_name' => 'Sublimação Total',
                'description' => 'Sublimação completa da peça (cacharrel)',
                'charge_by_color' => false,
                'color_price_per_unit' => 0,
                'min_colors' => 1,
                'max_colors' => null,
                'discount_2nd_application' => 0,
                'discount_3rd_application' => 0,
                'discount_4th_plus_application' => 0,
                'has_sizes' => false,
                'has_locations' => false,
                'has_special_options' => true,
                'active' => true,
                'order' => 6,
            ],
        ];

        foreach ($settings as $setting) {
            PersonalizationSetting::updateOrCreate(
                ['personalization_type' => $setting['personalization_type']],
                $setting
            );
        }

        $this->command->info(' Configurações criadas!');

        // Opções especiais (adicionais)
        $this->command->info(' Criando opções especiais...');

        $specialOptions = [
            // Serigrafia
            ['personalization_type' => 'SERIGRAFIA', 'name' => 'Dourado', 'charge_type' => 'percentage', 'charge_value' => 50, 'description' => 'Tinta dourada metalizada', 'order' => 1],
            ['personalization_type' => 'SERIGRAFIA', 'name' => 'Prata', 'charge_type' => 'percentage', 'charge_value' => 50, 'description' => 'Tinta prateada metalizada', 'order' => 2],
            ['personalization_type' => 'SERIGRAFIA', 'name' => 'Neon', 'charge_type' => 'fixed', 'charge_value' => 2.00, 'description' => 'Tinta fluorescente', 'order' => 3],
            ['personalization_type' => 'SERIGRAFIA', 'name' => 'Glitter', 'charge_type' => 'percentage', 'charge_value' => 30, 'description' => 'Acabamento com glitter', 'order' => 4],
            
            // DTF
            ['personalization_type' => 'DTF', 'name' => 'Holográfico', 'charge_type' => 'percentage', 'charge_value' => 40, 'description' => 'Efeito holográfico', 'order' => 1],
            ['personalization_type' => 'DTF', 'name' => 'Glitter', 'charge_type' => 'percentage', 'charge_value' => 30, 'description' => 'Acabamento com glitter', 'order' => 2],
            ['personalization_type' => 'DTF', 'name' => 'Metálico', 'charge_type' => 'percentage', 'charge_value' => 35, 'description' => 'Efeito metalizado', 'order' => 3],
            
            // Emborrachado
            ['personalization_type' => 'EMBORRACHADO', 'name' => 'Alto Relevo Extra', 'charge_type' => 'percentage', 'charge_value' => 25, 'description' => 'Maior volume de relevo', 'order' => 1],
            ['personalization_type' => 'EMBORRACHADO', 'name' => 'Dourado', 'charge_type' => 'percentage', 'charge_value' => 50, 'description' => 'Acabamento dourado', 'order' => 2],
            
            // Sublimação Total
            ['personalization_type' => 'SUB. TOTAL', 'name' => 'Manga Longa', 'charge_type' => 'fixed', 'charge_value' => 5.00, 'description' => 'Adicional para manga longa', 'order' => 1],
            ['personalization_type' => 'SUB. TOTAL', 'name' => 'Capuz', 'charge_type' => 'fixed', 'charge_value' => 8.00, 'description' => 'Adicional para capuz', 'order' => 2],
        ];

        foreach ($specialOptions as $option) {
            PersonalizationSpecialOption::updateOrCreate(
                [
                    'personalization_type' => $option['personalization_type'],
                    'name' => $option['name'],
                ],
                $option
            );
        }

        $this->command->info(' Opções especiais criadas!');
    }
}
