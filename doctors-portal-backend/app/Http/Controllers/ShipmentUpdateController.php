<?php

namespace App\Http\Controllers;

use App\Models\ShipmentUpdate;
use App\Services\CareTendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ShipmentUpdateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // Get the authenticated user
        $query = ShipmentUpdate::query();

        // Only include records that belong to the logged-in doctor
        $query->where('user_id', $user->id);

        // If a search term is present, apply filters
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                ->orWhere('prescription_name', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate($request->input('per_page', 20)));
    }




    public function store(Request $request, CareTendService $svc)
    {
        $data = $request->validate([
            'fromDate' => 'required|date',
            'toDate'   => 'required|date|after_or_equal:fromDate',
        ]);

        $saved = $svc->saveShipmentUpdates($data);

        return response()->json(['saved' => $saved->count()]);
    }


    public function create(Request $request)
    {
        $data = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'patient_name'   => 'required|string',
            'prescription_name' => 'required|string',
            'status'         => 'required|string',
            'shipment_id'    => 'required|string',
            'rx_number'    => 'required|string',
            'date_shipped'   => 'nullable|date',
            'delivered_at'   => 'nullable|date',
        ]);

        $shipment = \App\Models\ShipmentUpdate::create($data);

        return response()->json($shipment, 201);
    }


    // ✅ Get prescriptions for a specific doctor (user)
  public function getByUser($userId, Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');

        $query = ShipmentUpdate::with('user')->where('user_id', $userId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', '%' . $search . '%')
                ->orWhere('prescription_name', 'like', '%' . $search . '%');
            });
        }

        $updates = $query->paginate($perPage);

        return response()->json([
            'user_name' => optional($updates->first()?->user)->name,
            'data' => $updates
        ]);
    }




    // ✅ Update a prescription (shipment update)
    public function update(Request $request, $id)
    {
        $update = ShipmentUpdate::findOrFail($id);

        Log::info('This is a debug log for request', ['data' => $request]);


        $data = $request->validate([
            'patient_name'           => 'required|string',
            'prescription_name'      => 'nullable|string',
            'status'                 => 'nullable|string',
            'user_id'                => 'required|exists:users,id',
            'date_of_birth'          => 'nullable|date',
            'insurance'              => 'nullable|string',
            'city'                   => 'nullable|string',
            'arrived_to_office_date' => 'nullable|string', // temporarily string, we'll cast it below
            'source'                 => 'nullable|string',
            'shipment_id'            => 'nullable|string',
            'rx_number'              => 'nullable|string',
            'date_shipped'           => 'nullable|date',
            'delivered_at'           => 'nullable|date',
        ]);

        $update->update($data);

        return response()->json(['message' => 'Update successful', 'data' => $update]);
    }

    public function importFromSheet(Request $request)
    {
        Log::info('✅ Google Sheet request hit the controller.');

        // ✅ Verify the key
        $apiKey = $request->header('X-API-KEY');
        if ($apiKey !== config('services.shipment_sheet.api_key')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ✅ Validate incoming payload
        $data = $request->validate([
            'patient_name'           => 'required|string',
            'prescription_name'      => 'nullable|string',
            'status'                 => 'nullable|string',
            'sheet_identefier'       => 'required|string',
            'date_of_birth'          => 'nullable|date',
            'insurance'              => 'nullable|string',
            'city'                   => 'nullable|string',
            'arrived_to_office_date' => 'nullable|string',
            'arrived_to_office_time' => 'nullable|string',
            'source'                 => 'nullable|string',
            'shipment_id'            => 'nullable|string',
            'rx_number'              => 'nullable|string',
            'date_shipped'           => 'nullable|date',
            'delivered_at'           => 'nullable|date',
        ]);

        // ✅ Clean and normalize the sheet identifier
        $cleanedIdentifier = trim(strtolower($data['sheet_identefier']));

        // ✅ Try to match against user
        $matchingUsers = \App\Models\User::whereRaw('LOWER(TRIM(sheet_identifier)) LIKE ?', [
            '%' . $cleanedIdentifier . '%'
        ])->get();


        if ($matchingUsers->isEmpty()) {
            return response()->json(['message' => 'No matching user found for the provided sheet_identefier'], 404);
        }

        $results = [];


        foreach ($matchingUsers as $user) {
                $payload = $data;
                $payload['user_id'] = $user->id;

                // Combine arrival datetime
                if (!empty($payload['arrived_to_office_date'])) {
                    try {
                        $date = $payload['arrived_to_office_date'];
                        $time = $payload['arrived_to_office_time'] ?? '00:00 AM';
                        $combined = \Carbon\Carbon::parse("{$date} {$time}");
                        $payload['arrived_to_office_date'] = $combined->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        Log::warning("Failed to parse arrival datetime: {$date} {$time}", ['error' => $e->getMessage()]);
                        $payload['arrived_to_office_date'] = $date;
                    }
                }

                unset($payload['arrived_to_office_time']);

                // Check for existing shipment
                $existing = ShipmentUpdate::where('user_id', $payload['user_id'])
                    ->where('patient_name', $payload['patient_name'])
                    ->where('prescription_name', $payload['prescription_name'])
                    ->first();

                if ($existing) {
                    $dirty = collect($payload)->filter(fn($value, $key) => $existing->$key !== $value);
                    if ($dirty->isNotEmpty()) {
                        $existing->update($payload);
                        $message = 'Shipment update modified';
                    } else {
                        $message = 'No changes detected — skipped update';
                    }

                    Log::info("✅ Updating shipment for patient: {$payload['patient_name']} (User ID: {$user->id})");

                    $results[] = [
                        'user_id' => $user->id,
                        'message' => $message,
                        'data'    => $existing
                    ];
                } else {
                    $shipment = ShipmentUpdate::create($payload);
                    Log::info("✅ Creating new shipment for patient: {$payload['patient_name']} (User ID: {$user->id})");

                    $results[] = [
                        'user_id' => $user->id,
                        'message' => 'Shipment update imported',
                        'data'    => $shipment
                    ];
                }
        }

        return response()->json($results);
    }

}
