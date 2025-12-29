<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Vestalize</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full grid md:grid-cols-2 bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
        
        <!-- Lado Esquerdo: Info -->
        <div class="p-8 md:p-12 bg-gradient-to-br from-indigo-600 to-violet-700 flex flex-col justify-between text-white">
            <div>
                <h1 class="text-3xl font-bold mb-4">Vestalize</h1>
                <p class="text-indigo-100 text-lg mb-8">A plataforma completa para gestão de confecção e estamparia.</p>
                
                <ul class="space-y-4">
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span>Controle de pedidos e ordens</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span>Gestão de estoque e suprimentos</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span>Kanban de produção em tempo real</span>
                    </li>
                </ul>
            </div>
            
            <div class="mt-12 p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                <p class="text-sm italic">"O Vestalize transformou nossa confecção. O que levava horas agora fazemos em minutos."</p>
                <p class="mt-2 font-semibold text-xs">— Ana Paula, CEO Confecções Têxtil</p>
            </div>
        </div>

        <!-- Lado Direito: Formulário -->
        <div class="p-8 md:p-12">
            <h2 class="text-2xl font-bold mb-2">Comece seu teste grátis</h2>
            <p class="text-slate-400 text-sm mb-8">Não precisa de cartão de crédito para testar.</p>

            <form action="<?php echo e(route('register.public.post')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Dados da Empresa</label>
                    <div class="relative">
                        <i class="fa-solid fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                        <input type="text" name="company_name" required placeholder="Nome da sua Empresa" 
                            class="w-full bg-slate-800 border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Seu Plano</label>
                    <select name="plan_id" required 
                        class="w-full bg-slate-800 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all text-sm appearance-none">
                        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($plan->id); ?>">Plano <?php echo e($plan->name); ?> (R$ <?php echo e(number_format($plan->price, 2, ',', '.')); ?>/mês)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Sua Conta</label>
                        <div class="relative">
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                            <input type="text" name="name" required placeholder="Seu Nome Completo" 
                                class="w-full bg-slate-800 border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                    </div>
                    
                    <div class="col-span-2">
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                            <input type="email" name="email" required placeholder="Seu Melhor E-mail" 
                                class="w-full bg-slate-800 border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                            <input type="password" name="password" required placeholder="Senha" 
                                class="w-full bg-slate-800 border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                    </div>

                    <div>
                        <div class="relative">
                            <i class="fa-solid fa-shield absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                            <input type="password" name="password_confirmation" required placeholder="Confirme" 
                                class="w-full bg-slate-800 border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/20 transition-all mt-4 transform hover:-translate-y-0.5 active:translate-y-0">
                    Começar 7 Dias de Teste Grátis
                </button>

                <p class="text-center text-slate-500 text-xs mt-6">
                    Já tem uma conta? <a href="<?php echo e(route('login')); ?>" class="text-indigo-400 hover:underline">Faça login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/auth/registro.blade.php ENDPATH**/ ?>