<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DhlTrackingService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.dhl.api_key');
        $this->baseUrl = config('services.dhl.base_url');
    }

    /**
     * Track a shipment using DHL Tracking API
     *
     * @param string $trackingNumber
     * @return array|null
     */
    public function trackShipment($trackingNumber)
    {
        if (empty($this->apiKey)) {
            Log::error('DHL API Key is missing in configuration.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'DHL-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/track/shipments", [
                'trackingNumber' => $trackingNumber,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('DHL API Error: ' . $response->status() . ' - ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('DHL API Exception: ' . $e->getMessage());
            return null;
        }
    }
}
