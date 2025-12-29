<?php
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

echo "<h1>Teste de DOMPDF com GD</h1>";

// Verificar GD
echo "<h2>1. Verificação da Extensão GD</h2>";
if (extension_loaded('gd')) {
    echo "<p style='color: green'>✅ GD está carregada</p>";
} else {
    echo "<p style='color: red'>❌ GD NÃO está carregada</p>";
    die("GD não está disponível!");
}

// Verificar função específica que o DOMPDF usa
echo "<h2>2. Verificação de funções GD</h2>";
$functions = ['imagecreatetruecolor', 'imagecreatefromjpeg', 'imagecreatefrompng', 'imagecreatefromgif'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green'>✅ Função $func existe</p>";
    } else {
        echo "<p style='color: red'>❌ Função $func NÃO existe</p>";
    }
}

// Teste simples de PDF sem imagem
echo "<h2>3. Teste de PDF Simples (sem imagem)</h2>";
try {
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml('<html><body><h1>Teste</h1><p>Este é um teste simples.</p></body></html>');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    echo "<p style='color: green'>✅ PDF simples gerado com sucesso!</p>";
} catch (Exception $e) {
    echo "<p style='color: red'>❌ Erro: " . $e->getMessage() . "</p>";
}

// Teste de PDF com imagem base64
echo "<h2>4. Teste de PDF com imagem base64</h2>";
try {
    // Criar uma imagem simples em base64
    $img = imagecreatetruecolor(100, 100);
    $white = imagecolorallocate($img, 255, 255, 255);
    $red = imagecolorallocate($img, 255, 0, 0);
    imagefilledrectangle($img, 0, 0, 100, 100, $white);
    imagefilledrectangle($img, 20, 20, 80, 80, $red);
    
    ob_start();
    imagepng($img);
    $imageData = ob_get_clean();
    imagedestroy($img);
    
    $base64 = base64_encode($imageData);
    $dataUrl = 'data:image/png;base64,' . $base64;
    
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isImageEnabled', true);
    
    $dompdf = new Dompdf($options);
    $html = '<html><body><h1>Teste com Imagem</h1><img src="' . $dataUrl . '" width="100" height="100"></body></html>';
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    echo "<p style='color: green'>✅ PDF com imagem base64 gerado com sucesso!</p>";
} catch (Exception $e) {
    echo "<p style='color: red'>❌ Erro: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Conclusão</h2>";
echo "<p>Se todos os testes passaram, o problema pode estar em uma imagem específica do pedido.</p>";
