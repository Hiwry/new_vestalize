<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos e Condições - Vestalize</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen py-10 px-4">

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <!-- Header -->
        <div class="bg-indigo-600 p-8 text-center">
            <h1 class="text-3xl font-bold text-white mb-2">Termos e Condições de Uso</h1>
            <p class="text-indigo-100">Última atualização: {{ date('d/m/Y') }}</p>
        </div>

        <!-- Content -->
        <div class="p-8 md:p-12 space-y-8 text-gray-600 dark:text-gray-300 leading-relaxed text-sm md:text-base">
            
            <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">1. Aceitação dos Termos</h2>
                <p>Ao acessar e criar uma conta no Vestalize ("Plataforma"), você concorda em cumprir estes Termos e Condições de Uso. Se você não concordar com qualquer parte destes termos, você não poderá usar nossos serviços.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">2. Descrição do Serviço</h2>
                <p>O Vestalize é uma plataforma SaaS (Software as a Service) destinada à gestão de confecções, estamparias e negócios têxteis. Oferecemos ferramentas para controle de pedidos, estoque, financeiro e produção.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">3. Assinatura e Pagamentos</h2>
                <p>O serviço é oferecido mediante assinatura mensal. Os pagamentos são processados via Mercado Pago ou PIX. O não pagamento da mensalidade poderá resultar na suspensão temporária ou no cancelamento do acesso à conta.</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>O período de teste gratuito é de 7 dias.</li>
                    <li>Após o período de teste, a cobrança será iniciada automaticamente caso os dados de pagamento tenham sido fornecidos.</li>
                    <li>Não há reembolso para períodos parciais de uso.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">4. Responsabilidades do Usuário</h2>
                <p>O usuário é inteiramente responsável pela segurança de suas credenciais de acesso e por todas as atividades que ocorram sob sua conta. Você concorda em:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Fornecer informações verdadeiras e atualizadas.</li>
                    <li>Não utilizar a plataforma para fins ilegais.</li>
                    <li>Não tentar violar a segurança do sistema.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">5. Propriedade Intelectual</h2>
                <p>Todo o conteúdo, design, código e software da plataforma são de propriedade exclusiva do Vestalize e estão protegidos pelas leis de direitos autorais e propriedade intelectual.</p>
            </section>

             <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">6. Limitação de Responsabilidade</h2>
                <p>A plataforma é fornecida "como está". Não garantimos que o serviço será ininterrupto ou livre de erros. Em nenhuma circunstância seremos responsáveis por danos indiretos, incidentais ou consequentes decorrentes do uso do serviço.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">7. Disposições Finais</h2>
                <p>Reservamo-nos o direito de modificar estes termos a qualquer momento. Alterações significativas serão notificadas aos usuários. O uso continuado da plataforma após as alterações constitui aceitação dos novos termos.</p>
            </section>

        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-900/50 p-6 text-center border-t border-gray-100 dark:border-gray-700">
            <a href="javascript:window.close()" class="inline-block bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold py-3 px-8 rounded-lg hover:opacity-90 transition-opacity">
                Fechar Janela
            </a>
        </div>
    </div>

</body>
</html>
