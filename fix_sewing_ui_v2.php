<?php
$file = 'c:/xampp/htdocs/vestalize/resources/views/orders/wizard/sewing.blade.php';
$content = file_get_contents($file);

// Background replacement for Step 10 upload box (account for different class order)
$content = str_replace(
    'rounded-xl bg-gray-50 dark:bg-slate-800/50',
    'rounded-xl bg-gray-50/50 dark:bg-slate-900/40',
    $content
);

file_put_contents($file, $content);
echo "Final UI refinement complete.\n";
