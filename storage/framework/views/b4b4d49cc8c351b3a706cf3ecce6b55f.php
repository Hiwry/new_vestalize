<?php $__env->startComponent('mail::message'); ?>
# Recuperação de Senha

Olá <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Recebemos uma solicitação de recuperação de senha para sua conta.

## Suas Novas Credenciais

**Email:** <?php echo new \Illuminate\Support\EncodedHtmlString($user->email); ?>


<?php if($storeCode): ?>
**Código da Loja:** <?php echo new \Illuminate\Support\EncodedHtmlString($storeCode); ?>

<?php endif; ?>

**Nova Senha:** <?php echo new \Illuminate\Support\EncodedHtmlString($newPassword); ?>


<?php $__env->startComponent('mail::button', ['url' => route('login')]); ?>
Acessar Sistema
<?php echo $__env->renderComponent(); ?>

**Importante:** Por questões de segurança, recomendamos que você altere esta senha assim que fizer login.

Se você não solicitou esta recuperação de senha, entre em contato conosco imediatamente.

Atenciosamente,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/emails/password-reset.blade.php ENDPATH**/ ?>