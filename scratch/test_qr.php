<?php
require __DIR__.'/../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

try {
    $url = 'https://google.com';
    $options = new QROptions([
        'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
        'outputBase64' => false, // Raw SVG
        'svgAddXmlHeader' => false,
        'connectPaths' => true,
    ]);
    $qrcode = new QRCode($options);
    $result = $qrcode->render($url);
    file_put_contents(__DIR__.'/test_qr.svg', $result);
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
