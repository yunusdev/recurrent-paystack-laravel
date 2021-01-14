<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $baseUrl;
    protected $client;

    public function __construct()
    {
        $this->baseUrl = 'https://api.paystack.co';
        $this->client = Http::withToken(config('app.paystack_secret_key'));
    }

    public function verifyTransaction(string $reference)
    {
        $response = $this->client->get($this->baseUrl . "/transaction/verify/" . $reference);
        return json_decode($response, true);
    }

    public function recurrentCharge(array $payload)
    {
        $response = $this->client->post($this->baseUrl . "/transaction/charge_authorization", $payload);
        return json_decode($response, true);
    }

}
