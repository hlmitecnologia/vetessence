<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;

class PixService
{
    protected string $pixKey;
    protected string $merchantName;
    protected string $city;
    protected string $gi;
    protected string $url;
    protected bool $isUniquePayment;

    public function __construct(?PaymentGateway $gateway = null)
    {
        $gateway = $gateway ?? PaymentGateway::where('provider', 'pix')->where('is_active', true)->first();

        $this->gi = 'br.gov.bcb.pix';

        $branch = null;
        if ($gateway && $gateway->branch_id) {
            $branch = $gateway->branch;
        }

        if ($branch) {
            $this->pixKey = $gateway->public_key ?? config('pix.pix_key', '');
            $this->merchantName = $branch->name ?? config('pix.merchant_name', '');
            $this->city = $branch->city ?? config('pix.city', 'SAO PAULO');
            $this->url = ($gateway->config['url'] ?? '') ?: config('pix.url', '');
            $this->isUniquePayment = $gateway->config['is_unique_payment'] ?? config('pix.is_unique_payment', false);
        } elseif ($gateway) {
            $this->pixKey = $gateway->public_key ?? config('pix.pix_key', '');
            $this->merchantName = config('pix.merchant_name', '');
            $this->city = config('pix.city', 'SAO PAULO');
            $this->url = ($gateway->config['url'] ?? '') ?: config('pix.url', '');
            $this->isUniquePayment = $gateway->config['is_unique_payment'] ?? config('pix.is_unique_payment', false);
        } else {
            $this->pixKey = config('pix.pix_key', '');
            $this->merchantName = config('pix.merchant_name', '');
            $this->city = config('pix.city', 'SAO PAULO');
            $this->url = config('pix.url', '');
            $this->isUniquePayment = config('pix.is_unique_payment', false);
        }
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
        $payload = $this->buildMerchantAccountInformation();
        $payload .= $this->tlv('52', '04', $this->getMerchantCategoryCode());
        $payload .= $this->tlv('53', '03', $this->getTransactionCurrency());
        $payload .= $this->tlv('58', '02', $this->getCountryCode());
        $payload .= $this->tlv('59', strlen(substr($this->merchantName, 0, 25)), substr($this->merchantName, 0, 25));
        $payload .= $this->tlv('60', strlen(substr($this->city, 0, 15)), substr($this->city, 0, 15));

        if ($value > 0) {
            $amount = number_format($value, 2, '.', '');
            $payload .= $this->tlv('54', strlen($amount), $amount);
        }

        if (!empty($txid)) {
            $txidClean = substr($txid, 0, 25);
            $payload .= $this->tlv('62', strlen($txidClean) + 4, '05' . $this->tlvLen(strlen($txidClean)) . $txidClean);
        }

        return '000201' . $payload . '6304';
    }

    protected function buildMerchantAccountInformation()
    {
        $mai = $this->tlv('00', strlen($this->gi), $this->gi);
        $mai .= $this->tlv('01', strlen($this->pixKey), $this->pixKey);

        if (!empty($this->url)) {
            $mai .= $this->tlv('02', strlen($this->url), $this->url);
        }

        return $this->tlv('26', strlen($mai), $mai);
    }

    protected function tlv(string $tag, int $len, string $value): string
    {
        return $tag . $this->tlvLen($len) . $value;
    }

    protected function tlvLen(int $len): string
    {
        if ($len < 10) {
            return '0' . $len;
        }
        return (string) $len;
    }

    protected function buildCompletePayload($payload)
    {
        return '000201' . $payload . '6304';
    }

    public function getCRC16($payload)
    {
        $crc = 0xFFFF;
        
        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= ord($payload[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
                $crc &= 0xFFFF;
            }
        }
        
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
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

    public function generateQRCodeFromPayload(string $payload, int $size = 300): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($payload)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin(10)
            ->build();

        return $result->getDataUri();
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
