<?php
// Alternative QR Code generation using Endroid QR Code library
// This is optional - you can use this instead of TCPDF's built-in QR if needed

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Generate QR Code as PNG image
 * @param string $data The data to encode
 * @param int $size Size of the QR code
 * @return string Base64 encoded PNG image
 */
function generateQRCodePNG($data, $size = 200) {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($data)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
        ->size($size)
        ->margin(10)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->build();

    return base64_encode($result->getString());
}

/**
 * Generate QR Code and save as file
 * @param string $data The data to encode
 * @param string $filename File path to save
 * @param int $size Size of the QR code
 */
function saveQRCodeFile($data, $filename, $size = 200) {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($data)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
        ->size($size)
        ->margin(10)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->build();

    $result->saveToFile($filename);
}

// Example usage:
// $qrBase64 = generateQRCodePNG('https://example.com');
// saveQRCodeFile('https://example.com', 'qrcode.png');
?>
