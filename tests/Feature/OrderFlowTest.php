<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderFlowTest extends TestCase
{
    // Cuidado: RefreshDatabase limpa o banco de teste a cada execução
    // use RefreshDatabase; 

    /**
     * Testa se a página de listagem de pedidos carrega para um usuário autenticado.
     */
    public function test_orders_page_loads_for_authenticated_user()
    {
        $user = User::first(); // Usar um usuário existente para o ambiente da vestalize
        
        if (!$user) {
            $this->markTestSkipped('Nenhum usuário encontrado para o teste.');
        }

        $response = $this->actingAs($user)->get('/pedidos');

        $response->assertStatus(200);
        $response->assertSee('Listagem de Pedidos');
    }

    /**
     * Testa o acesso ao Wizard de Pedidos.
     */
    public function test_order_wizard_page_is_accessible()
    {
        $user = User::first();
        
        if (!$user) {
            $this->markTestSkipped('Nenhum usuário encontrado para o teste.');
        }

        $response = $this->actingAs($user)->get('/pedidos/wizard/inicio');

        $response->assertStatus(200);
        $response->assertSee('Novo Pedido');
    }
}
