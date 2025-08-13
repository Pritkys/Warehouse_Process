<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (!isset($_GET['code'])) {
  http_response_code(400);
  exit('Missing code');
}

$code = $_GET['code'];

$qr = QrCode::create($code)
    ->setSize(300)
    ->setMargin(10);

$writer = new PngWriter();
$result = $writer->write($qr);

header('Content-Type: image/png');
echo $result->getString();
