<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;

class PixService
{
    protected $merchantName;
    protected $city;
    protected $gi;
    protected $isUniquePayment;

    public function __construct()
    {
        $this->merchantName = config('pix.merchant_name', 'VETESSENCE CLINICA VETERINARIA');
        $this->city = config('pix.city', 'SAO PAULO');
        $this->gi = config('pix.gi', 'br.gov.bcb.pix');
        $this->isUniquePayment = config('pix.is_unique_payment', false);
    }

    public function getPayloadFormat()
    {
        return '01';
    }

    public function getMerchantCategoryCode()
    {
        return '0000';
    }

    public function getTransactionCurrency()
    {
        return '986';
    }

    public function getCountryCode()
    {
        return 'BR';
    }

    public function buildPayload($value, $txid = '')
    {
        $payload = $this->buildMerchantAccountInformation($txid);
        $payload .= $this->buildMerchantCategoryCode();
        $payload .= $this->buildTransactionCurrency();
        $payload .= $this->buildCountryCode();
        $payload .= $this->buildMerchantName();
        $payload .= $this->buildCity();
        
        if ($value > 0) {
            $payload .= $this->buildTransactionAmount($value);
        }
        
        return $this->buildCompletePayload($payload);
    }

    protected function buildMerchantAccountInformation($txid)
    {
        $gui = $this->gi;
        $key = config('pix.pix_key', 'seuemail@exemplo.com');
        $url = config('pix.url', '');
        
        $mai = $gui . '01' . $key;
        
        if (!empty($url)) {
            $mai .= '02' . $url;
        }
        
        if (!empty($txid)) {
            $txid = str_pad(substr($txid, 0, 25), 25, ' ', STR_PAD_RIGHT);
            $mai .= '05' . $txid;
        }
        
        return '00' . str_pad($mai, strlen($mai), ' ', STR_PAD_RIGHT);
    }

    protected function buildMerchantCategoryCode()
    {
        return '52' . $this->getMerchantCategoryCode();
    }

    protected function buildTransactionCurrency()
    {
        return '53' . $this->getTransactionCurrency();
    }

    protected function buildCountryCode()
    {
        return '58' . $this->getCountryCode();
    }

    protected function buildMerchantName()
    {
        $name = str_pad(substr($this->merchantName, 0, 25), 25, ' ', STR_PAD_RIGHT);
        return '59' . $name;
    }

    protected function buildCity()
    {
        $city = str_pad(substr($this->city, 0, 15), 15, ' ', STR_PAD_RIGHT);
        return '60' . $city;
    }

    protected function buildTransactionAmount($value)
    {
        $amount = number_format($value, 2, '.', '');
        return '54' . $amount;
    }

    protected function buildCompletePayload($payload)
    {
        return '000201' . $payload . '6304';
    }

    public function getCRC16($payload)
    {
        $payload .= '6304';
        $crc = 0xFFFF;
        
        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= ord($payload[$i]);
            for ($j = 8; $j != 0; $j--) {
                if (($crc & 1) != 0) {
                    $crc = ($crc >> 1) ^ 0x1021;
                } else {
                    $crc = $crc >> 1;
                }
            }
        }
        
        return strtoupper(dechex($crc));
    }

    public function generatePayload($value, $txid = '')
    {
        $payload = $this->buildPayload($value, $txid);
        $crc = $this->getCRC16($payload);
        return $payload . $crc;
    }

    public function generateQRCode($value, $txid = '', $size = 300)
    {
        $payload = $this->generatePayload($value, $txid);
        
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($payload)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin(10)
            ->build();

        return [
            'payload' => $payload,
            'qrcode_base64' => $result->getDataUri(),
        ];
    }

    public function generateQRCodeFile($value, $txid = '', $size = 300, $filename = null)
    {
        $result = $this->generateQRCode($value, $txid, $size);
        
        if ($filename) {
            $result['qrcode']->saveToFile($filename);
        }
        
        return $result;
    }
}
