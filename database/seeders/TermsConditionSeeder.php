<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TermsCondition;

class TermsConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsCondition::create([
            'content' => 'TERMOS E CONDIÇÕES DE USO

1. ACEITAÇÃO DOS TERMOS
Ao utilizar nossos serviços, você concorda com estes termos e condições. Se não concordar com qualquer parte destes termos, não deve utilizar nossos serviços.

2. SERVIÇOS OFERECIDOS
Oferecemos serviços de personalização de produtos têxteis, incluindo:
- Serigrafia
- Sublimação
- Bordado
- DTF (Direct to Film)
- Outros métodos de personalização

3. PEDIDOS E PAGAMENTOS
- Todos os pedidos devem ser confirmados antes da produção
- Os preços são válidos conforme tabela vigente
- Pagamentos podem ser feitos à vista ou parcelado conforme acordado
- Em caso de atraso no pagamento, podem ser aplicados juros e multa

4. PRAZOS DE ENTREGA
- Os prazos de entrega são estimativas baseadas na complexidade do pedido
- Prazos podem variar conforme disponibilidade de materiais
- Pedidos de evento têm prioridade e prazos diferenciados

5. QUALIDADE E DEFEITOS
- Garantimos a qualidade de nossos produtos
- Em caso de defeito de fabricação, faremos a reposição sem custo adicional
- Defeitos causados por uso inadequado não são cobertos pela garantia

6. ALTERAÇÕES E CANCELAMENTOS
- Alterações em pedidos confirmados podem gerar custos adicionais
- Cancelamentos após início da produção podem ter cobrança proporcional
- Pedidos de evento têm políticas específicas de cancelamento

7. RESPONSABILIDADES
- O cliente é responsável pela veracidade das informações fornecidas
- Imagens e artes fornecidas devem ter direitos de uso
- Não nos responsabilizamos por problemas decorrentes de informações incorretas

8. PRIVACIDADE
- Respeitamos sua privacidade conforme nossa Política de Privacidade
- Dados pessoais são utilizados apenas para prestação dos serviços
- Não compartilhamos informações com terceiros sem autorização

9. ALTERAÇÕES NOS TERMOS
- Estes termos podem ser alterados a qualquer momento
- Alterações serão comunicadas através de nossos canais oficiais
- O uso continuado dos serviços implica aceitação das alterações

10. CONTATO
Para dúvidas sobre estes termos, entre em contato:
- Telefone: (11) 99999-9999
- Email: contato@empresa.com

Última atualização: ' . date('d/m/Y'),
            'version' => '1.0',
            'active' => true
        ]);
    }
}