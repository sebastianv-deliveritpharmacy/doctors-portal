<?php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\ShipmentUpdate;

class CareTendService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.caretend.base_uri'),
            'headers'  => [
                'Authorization' => 'Bearer ' . config('services.caretend.api_key'),
                'Accept'        => 'application/json',
            ],
        ]);
    }

    /**
     * Fetch prescriptions with patient info within a date range.
     *
     * @param string $fromDate Format: 'YYYY-MM-DD'
     * @param string $toDate   Format: 'YYYY-MM-DD'
     * @return array
     */
    public function getPrescriptions(string $fromDate, string $toDate): array
    {
        try {
            $response = $this->client->get('prescriptions', [
                'query' => [
                    'fromDate' => $fromDate,
                    'toDate'   => $toDate,
                    'include'  => 'patient,patient.status', // if supported
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['prescriptions'] ?? [];
        } catch (\Exception $e) {
            Log::error('CareTend API error: '.$e->getMessage());
            return [];
        }
    }


    /**
     * Call searchDeliveryTicketShipmentUpdatesV3 endpoint.
     *
     * @param array $filters – e.g. ['fromDate' => '2025-06-01', 'toDate' => '2025-06-15']
     * @return array
     */
    public function getShipmentUpdates(array $filters = []): array
    {
        try {
            $response = $this->client->post('deliverytickets/search', [
                'json' => $filters,
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['updates'] ?? [];
        } catch (\Exception $e) {
            Log::error('CareTend shipment updates error: ' . $e->getMessage());
            return [];
        }
    }




    public function saveShipmentUpdates(array $filters = []): Collection
    {
        $updates = $this->getShipmentUpdates($filters);

        return collect($updates)->map(function ($u) {
            return ShipmentUpdate::updateOrCreate(
                ['shipment_id' => $u['trackingNumber'] ?? $u['patientOrderId']],
                [
                    'patient_name'      => trim(($u['patientFirstName'] ?? '') . ' ' . ($u['patientLastName'] ?? '')),
                    'rx_number'         => $u['rxNumber'] ?? null,
                    'prescription_name' => $u['ndc'] ?? null,  // change if API returns actual name
                    'status'            => $u['rxShippingStatus'] ?? null,
                    'date_shipped'      => isset($u['dateShipped']) ? Carbon::parse($u['dateShipped']) : null,
                ]
            );
        });
    }

}
