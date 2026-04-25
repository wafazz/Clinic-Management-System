<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    public function isConfigured(): bool
    {
        return Setting::get('billplz_enabled') === '1'
            && filled(Setting::get('billplz_api_key'))
            && filled(Setting::get('billplz_collection_id'));
    }

    public function isSandbox(): bool
    {
        return Setting::get('billplz_sandbox', '1') === '1';
    }

    private function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://www.billplz-sandbox.com/api/v3'
            : 'https://www.billplz.com/api/v3';
    }

    /**
     * Create a Billplz Bill (checkout link).
     *
     * @return array ['success' => bool, 'url' => string|null, 'bill_id' => string|null, 'response' => mixed]
     */
    public function createBill(array $data): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'url' => null, 'bill_id' => null, 'response' => 'Billplz not configured'];
        }

        $apiKey = Setting::get('billplz_api_key');
        $collectionId = Setting::get('billplz_collection_id');

        try {
            $response = Http::withBasicAuth($apiKey, '')
                ->asForm()
                ->post($this->baseUrl() . '/bills', [
                    'collection_id' => $collectionId,
                    'description' => $data['description'] ?? 'Clinic invoice payment',
                    'email' => $data['email'] ?? 'no-reply@clinic.local',
                    'name' => $data['name'] ?? 'Patient',
                    'amount' => (int) round(((float) $data['amount']) * 100), // sen
                    'callback_url' => route('billplz.callback'),
                    'redirect_url' => $data['redirect_url'] ?? route('billplz.redirect'),
                    'reference_1_label' => 'Invoice',
                    'reference_1' => $data['reference'] ?? '',
                ]);

            $body = $response->json();

            return [
                'success' => $response->successful(),
                'url' => $body['url'] ?? null,
                'bill_id' => $body['id'] ?? null,
                'response' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('Billplz createBill error', ['error' => $e->getMessage()]);
            return ['success' => false, 'url' => null, 'bill_id' => null, 'response' => $e->getMessage()];
        }
    }

    /**
     * Verify Billplz X-Signature on callback.
     */
    public function verifyCallback(array $payload, ?string $signature): bool
    {
        $xSignatureKey = Setting::get('billplz_x_signature');
        if (!$xSignatureKey || !$signature) {
            return false;
        }

        // Sort the payload keys, exclude x_signature
        $data = array_filter($payload, fn($k) => $k !== 'x_signature', ARRAY_FILTER_USE_KEY);
        ksort($data);

        $string = '';
        foreach ($data as $k => $v) {
            $string .= $k . ((string) $v);
        }

        $computed = hash_hmac('sha256', $string, $xSignatureKey);
        return hash_equals($computed, $signature);
    }
}
