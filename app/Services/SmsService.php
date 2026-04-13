<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $provider;
    private string $senderName;

    public function __construct()
    {
        $this->provider = config('sms.provider', 'log');
        $this->senderName = config('sms.sender_name', 'MIZAN');
    }

    public function send(string $phone, string $message): array
    {
        $phone = $this->formatPhone($phone);

        return match ($this->provider) {
            'taqnyat' => $this->sendViaTaqnyat($phone, $message),
            'unifonic' => $this->sendViaUnifonicSms($phone, $message),
            'msegat' => $this->sendViaMsegat($phone, $message),
            default => $this->sendViaLog($phone, $message),
        };
    }

    /**
     * Send bulk SMS to multiple numbers
     */
    public function sendBulk(array $recipients, string $message): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($recipients as $phone) {
            $result = $this->send($phone, $message);
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "{$phone}: {$result['message']}";
            }
        }

        return $results;
    }

    /**
     * Taqnyat SMS Gateway (taqnyat.sa)
     */
    private function sendViaTaqnyat(string $phone, string $message): array
    {
        $config = config('sms.providers.taqnyat');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $config['api_key'],
            ])->post($config['api_url'], [
                'recipients' => [$phone],
                'body' => $message,
                'sender' => $this->senderName,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['statusCode'] ?? 0) == 201) {
                Log::info("SMS sent via Taqnyat to {$phone}");
                return ['success' => true, 'message' => 'تم الإرسال', 'provider' => 'taqnyat'];
            }

            Log::warning("Taqnyat SMS failed to {$phone}: " . json_encode($data));
            return ['success' => false, 'message' => $data['message'] ?? 'فشل الإرسال', 'provider' => 'taqnyat'];
        } catch (\Exception $e) {
            Log::error("Taqnyat SMS error: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage(), 'provider' => 'taqnyat'];
        }
    }

    /**
     * Unifonic SMS Gateway (unifonic.com)
     */
    private function sendViaUnifonicSms(string $phone, string $message): array
    {
        $config = config('sms.providers.unifonic');

        try {
            $response = Http::asForm()->post($config['api_url'], [
                'AppSid' => $config['app_sid'],
                'Recipient' => $phone,
                'Body' => $message,
                'SenderID' => $this->senderName,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                Log::info("SMS sent via Unifonic to {$phone}");
                return ['success' => true, 'message' => 'تم الإرسال', 'provider' => 'unifonic'];
            }

            Log::warning("Unifonic SMS failed to {$phone}: " . json_encode($data));
            return ['success' => false, 'message' => $data['message'] ?? 'فشل الإرسال', 'provider' => 'unifonic'];
        } catch (\Exception $e) {
            Log::error("Unifonic SMS error: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage(), 'provider' => 'unifonic'];
        }
    }

    /**
     * Msegat SMS Gateway (msegat.com)
     */
    private function sendViaMsegat(string $phone, string $message): array
    {
        $config = config('sms.providers.msegat');

        try {
            $response = Http::post($config['api_url'], [
                'apiKey' => $config['api_key'],
                'userName' => $config['username'],
                'numbers' => $phone,
                'userSender' => $this->senderName,
                'msg' => $message,
                'msgEncoding' => 'UTF8',
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['code'] ?? '') == '1') {
                Log::info("SMS sent via Msegat to {$phone}");
                return ['success' => true, 'message' => 'تم الإرسال', 'provider' => 'msegat'];
            }

            Log::warning("Msegat SMS failed to {$phone}: " . json_encode($data));
            return ['success' => false, 'message' => $data['message'] ?? 'فشل الإرسال', 'provider' => 'msegat'];
        } catch (\Exception $e) {
            Log::error("Msegat SMS error: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage(), 'provider' => 'msegat'];
        }
    }

    /**
     * Log-only mode for development/testing
     */
    private function sendViaLog(string $phone, string $message): array
    {
        Log::info("SMS [LOG MODE] to {$phone}: {$message}");
        return ['success' => true, 'message' => 'تم التسجيل في السجل (وضع التطوير)', 'provider' => 'log'];
    }

    /**
     * Format phone number to international format (Saudi +966)
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // 05xxxxxxxx → 9665xxxxxxxx
        if (str_starts_with($phone, '05')) {
            return '966' . substr($phone, 1);
        }

        // 5xxxxxxxx → 9665xxxxxxxx
        if (str_starts_with($phone, '5') && strlen($phone) == 9) {
            return '966' . $phone;
        }

        // +9665 → 9665
        if (str_starts_with($phone, '+')) {
            return ltrim($phone, '+');
        }

        return $phone;
    }
}
