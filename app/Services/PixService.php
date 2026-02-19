<?php

namespace App\Services;

use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output\Png;

class PixService
{
    /**
     * Chave PIX (CPF sem formatação)
     */
    private string $pixKey = '12350046435';
    
    /**
     * Nome do beneficiário (máx 25 caracteres, sem acentos)
     */
    private string $merchantName = 'VESTALIZE';
    
    /**
     * Cidade do beneficiário (máx 15 caracteres, sem acentos)
     */
    private string $merchantCity = 'MACEIO';
    
    /**
     * Gera o payload PIX no padrão EMV BR Code
     */
    public function generatePayload(float $amount, string $txId = ''): string
    {
        // Formatar valor com 2 casas decimais
        $amount = number_format($amount, 2, '.', '');
        
        // Gerar ID de transação se não fornecido
        if (empty($txId)) {
            $txId = 'PIX' . time() . rand(100, 999);
        }
        
        // Limitar txId a 25 caracteres alfanuméricos
        $txId = preg_replace('/[^a-zA-Z0-9]/', '', $txId);
        $txId = substr($txId, 0, 25);
        
        // Construir payload EMV
        $payload = $this->buildEmvPayload($amount, $txId);
        
        // Adicionar CRC16
        $payload .= '6304';
        $crc = $this->calculateCRC16($payload);
        $payload .= strtoupper($crc);
        
        return $payload;
    }
    
    /**
     * Constrói o payload EMV sem CRC
     */
    private function buildEmvPayload(string $amount, string $txId): string
    {
        $payload = '';
        
        // ID 00 - Payload Format Indicator
        $payload .= $this->formatEmvField('00', '01');
        
        // ID 01 - Point of Initiation Method (12 = dinâmico)
        $payload .= $this->formatEmvField('01', '12');
        
        // ID 26 - Merchant Account Information (PIX)
        $merchantAccount = $this->formatEmvField('00', 'BR.GOV.BCB.PIX');
        $merchantAccount .= $this->formatEmvField('01', $this->pixKey);
        $payload .= $this->formatEmvField('26', $merchantAccount);
        
        // ID 52 - Merchant Category Code
        $payload .= $this->formatEmvField('52', '0000');
        
        // ID 53 - Transaction Currency (986 = BRL)
        $payload .= $this->formatEmvField('53', '986');
        
        // ID 54 - Transaction Amount
        $payload .= $this->formatEmvField('54', $amount);
        
        // ID 58 - Country Code
        $payload .= $this->formatEmvField('58', 'BR');
        
        // ID 59 - Merchant Name
        $payload .= $this->formatEmvField('59', $this->sanitizeName($this->merchantName));
        
        // ID 60 - Merchant City
        $payload .= $this->formatEmvField('60', $this->sanitizeName($this->merchantCity));
        
        // ID 62 - Additional Data Field Template
        $additionalData = $this->formatEmvField('05', $txId);
        $payload .= $this->formatEmvField('62', $additionalData);
        
        return $payload;
    }
    
    /**
     * Formata um campo EMV (ID + Tamanho + Valor)
     */
    private function formatEmvField(string $id, string $value): string
    {
        $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $id . $length . $value;
    }
    
    /**
     * Remove acentos e caracteres especiais
     */
    private function sanitizeName(string $name): string
    {
        $name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        $name = preg_replace('/[^a-zA-Z0-9 ]/', '', $name);
        return strtoupper(substr($name, 0, 25));
    }
    
    /**
     * Calcula CRC16-CCITT
     */
    private function calculateCRC16(string $payload): string
    {
        $polynomial = 0x1021;
        $crc = 0xFFFF;
        
        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        
        return str_pad(dechex($crc), 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Gera o QR Code como imagem Base64
     */
    public function generateQrCodeBase64(string $payload, int $size = 300): string
    {
        $qrCode = new QrCode($payload);
        $qrCode->disableBorder();
        
        $output = new Png();
        $data = $output->output($qrCode, $size);
        
        return 'data:image/png;base64,' . base64_encode($data);
    }
    
    /**
     * Gera payload e QR code completos
     */
    public function generate(float $amount, string $txId = ''): array
    {
        $payload = $this->generatePayload($amount, $txId);
        $qrCodeBase64 = $this->generateQrCodeBase64($payload);
        
        return [
            'payload' => $payload,
            'qrcode' => $qrCodeBase64,
            'amount' => $amount,
            'formatted_amount' => 'R$ ' . number_format($amount, 2, ',', '.'),
            'pix_key' => $this->formatCpf($this->pixKey),
            'merchant_name' => $this->merchantName,
            'merchant_city' => $this->merchantCity,
        ];
    }
    
    /**
     * Formata CPF para exibição
     */
    private function formatCpf(string $cpf): string
    {
        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' . 
                   substr($cpf, 3, 3) . '.' . 
                   substr($cpf, 6, 3) . '-' . 
                   substr($cpf, 9, 2);
        }
        return $cpf;
    }
}
