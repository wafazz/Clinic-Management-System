<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function isConfigured(): bool
    {
        return Setting::get('whatsapp_enabled') === '1'
            && filled(Setting::get('whatsapp_provider'))
            && filled(Setting::get('whatsapp_token'));
    }

    public function send(string $phone, string $message): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => true,
                'simulated' => true,
                'response' => "Simulated send to {$phone}: " . $message,
            ];
        }

        $provider = Setting::get('whatsapp_provider', 'cloud_api');

        return match ($provider) {
            'cloud_api' => $this->sendViaCloudApi($phone, $message),
            'fonnte' => $this->sendViaFonnte($phone, $message),
            'wassenger' => $this->sendViaWassenger($phone, $message),
            default => ['success' => false, 'response' => 'Unknown provider: ' . $provider],
        };
    }

    private function sendViaCloudApi(string $phone, string $message): array
    {
        $token = Setting::get('whatsapp_token');
        $phoneId = Setting::get('whatsapp_phone_id');

        if (!$phoneId) {
            return ['success' => false, 'response' => 'whatsapp_phone_id not set'];
        }

        $url = "https://graph.facebook.com/v20.0/{$phoneId}/messages";

        try {
            $response = Http::withToken($token)->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $this->normalizePhone($phone),
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

            return [
                'success' => $response->successful(),
                'response' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Cloud API error', ['error' => $e->getMessage()]);
            return ['success' => false, 'response' => $e->getMessage()];
        }
    }

    private function sendViaFonnte(string $phone, string $message): array
    {
        $token = Setting::get('whatsapp_token');

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->post('https://api.fonnte.com/send', [
                    'target' => $this->normalizePhone($phone),
                    'message' => $message,
                ]);

            return [
                'success' => $response->successful(),
                'response' => $response->body(),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'response' => $e->getMessage()];
        }
    }

    private function sendViaWassenger(string $phone, string $message): array
    {
        $token = Setting::get('whatsapp_token');

        try {
            $response = Http::withHeaders(['Token' => $token])
                ->post('https://api.wassenger.com/v1/messages', [
                    'phone' => $this->normalizePhone($phone),
                    'message' => $message,
                ]);

            return [
                'success' => $response->successful(),
                'response' => $response->body(),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'response' => $e->getMessage()];
        }
    }

    private function normalizePhone(string $phone): string
    {
        // Strip non-digits
        $digits = preg_replace('/\D/', '', $phone);
        // Convert local 0... to country code 60... (Malaysia)
        if (str_starts_with($digits, '0')) {
            $digits = '60' . substr($digits, 1);
        }
        return $digits;
    }
}
