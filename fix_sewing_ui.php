<?php
$file = 'c:/xampp/htdocs/vestalize/resources/views/orders/wizard/sewing.blade.php';
$content = file_get_contents($file);

// Background replacements
$content = str_replace(
    'bg-gray-50 dark:bg-slate-800/50 rounded-xl',
    'bg-gray-50/50 dark:bg-slate-900/40 rounded-xl',
    $content
);
$content = str_replace(
    'bg-gray-50 dark:bg-slate-800/50 rounded-2xl',
    'bg-gray-50/50 dark:bg-slate-900/40 rounded-2xl',
    $content
);
$content = str_replace(
    'bg-white dark:bg-slate-900 border border-red-200',
    'bg-red-50/10 dark:bg-red-900/10 border border-red-200',
    $content
);
$content = str_replace(
    'p-4 bg-white dark:bg-slate-900 border border-purple-200',
    'p-4 bg-purple-50/20 dark:bg-purple-900/20 border border-purple-200',
    $content
);

// Encoding/Accentuation fixes
$replacements = [
    'ObservaÃ§Ãµes' => 'Observações',
    'observaÃ§Ã£o' => 'observação',
    'produÃ§Ã£o' => 'produção',
    'PeÃ§as' => 'Peças',
    'UnitÃ¡rio' => 'Unitário',
    'ConferÃªncia' => 'Conferência',
    'acrÃ©scimo' => 'acréscimo',
    'Personaliza&ccedil;&atilde;o' => 'Personalização',
    'AtenÃ§Ã£o' => 'Atenção'
];

foreach ($replacements as $broken => $fixed) {
    $content = str_replace($broken, $fixed, $content);
}

file_put_contents($file, $content);
echo "Refinement complete.\n";
