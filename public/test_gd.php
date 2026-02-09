<?php
echo "<h1>Verificação da Extensão GD</h1>";

if (extension_loaded('gd')) {
    echo "<p style='color: green; font-weight: bold;'> Extensão GD está HABILITADA!</p>";
    echo "<h2>Informações do GD:</h2>";
    echo "<pre>";
    print_r(gd_info());
    echo "</pre>";
} else {
    echo "<p style='color: red; font-weight: bold;'> Extensão GD NÃO está habilitada!</p>";
}

echo "<h2>PHP Info:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>php.ini carregado:</strong> " . php_ini_loaded_file() . "</p>";

echo "<h2>Extensões carregadas:</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";
