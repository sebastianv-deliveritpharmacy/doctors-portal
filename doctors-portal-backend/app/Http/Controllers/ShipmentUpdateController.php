<?php

namespace App\Http\Controllers;

use App\Models\ShipmentUpdate;
use App\Services\CareTendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;


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

        $data['arrived_to_office_date'] = $this->parseArrivalDateTimeToUtc(
        $data['arrived_to_office_date'] ?? null,
        $request->input('arrived_to_office_time') ?? null,
        'America/Chicago'
    );

    if (!empty($data['date_shipped'])) {
        $data['date_shipped'] = $this->parseArrivalDateTimeToUtc($data['date_shipped'], null, 'America/Chicago');
    }
    if (!empty($data['delivered_at'])) {
        $data['delivered_at'] = $this->parseArrivalDateTimeToUtc($data['delivered_at'], null, 'America/Chicago');
    }


        $update->update($data);

        return response()->json(['message' => 'Update successful', 'data' => $update]);
    }

    // public function importFromSheet(Request $request)
    // {
    //     Log::info('✅ Google Sheet request hit the controller.');

    //     // ✅ Verify the key
    //     $apiKey = $request->header('X-API-KEY');
    //     if ($apiKey !== config('services.shipment_sheet.api_key')) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     // ✅ Validate incoming payload
    //     $data = $request->validate([
    //         'patient_name'           => 'required|string',
    //         'prescription_name'      => 'nullable|string',
    //         'status'                 => 'nullable|string',
    //         'sheet_identifier'       => 'required|string',
    //         'date_of_birth'          => 'nullable|date',
    //         'insurance'              => 'nullable|string',
    //         'city'                   => 'nullable|string',
    //         'arrived_to_office_date' => 'nullable|string',
    //         'arrived_to_office_time' => 'nullable|string',
    //         'source'                 => 'nullable|string',
    //         'shipment_id'            => 'nullable|string',
    //         'rx_number'              => 'nullable|string',
    //         'date_shipped'           => 'nullable|date',
    //         'delivered_at'           => 'nullable|date',
    //     ]);

    //     // ✅ Clean and normalize the sheet identifier
    //     $cleanedIdentifier = trim(strtolower($data['sheet_identifier']));

    //     // ✅ Try to match against user
    //     $matchingUsers = \App\Models\User::whereRaw('LOWER(TRIM(sheet_identifier)) LIKE ?', [
    //         '%' . $cleanedIdentifier . '%'
    //     ])->get();


    //     if ($matchingUsers->isEmpty()) {
    //         return response()->json(['message' => 'No matching user found for the provided sheet_identifier'], 404);
    //     }

    //     $results = [];


    //     foreach ($matchingUsers as $user) {
    //             $payload = $data;
    //             $payload['user_id'] = $user->id;

    //             // Combine arrival datetime
    //             if (!empty($payload['arrived_to_office_date'])) {
    //                 try {
    //                     $date = $payload['arrived_to_office_date'];
    //                     $time = $payload['arrived_to_office_time'] ?? '00:00 AM';
    //                     $combined = \Carbon\Carbon::parse("{$date} {$time}");
    //                     $payload['arrived_to_office_date'] = $combined->format('Y-m-d H:i:s');
    //                 } catch (\Exception $e) {
    //                     Log::warning("Failed to parse arrival datetime: {$date} {$time}", ['error' => $e->getMessage()]);
    //                     $payload['arrived_to_office_date'] = $date;
    //                 }
    //             }

    //             unset($payload['arrived_to_office_time']);

    //             // Check for existing shipment
    //             $existing = ShipmentUpdate::where('user_id', $payload['user_id'])
    //                 ->where('patient_name', $payload['patient_name'])
    //                 ->where('prescription_name', $payload['prescription_name'])
    //                 ->first();

    //             if ($existing) {
    //                 $dirty = collect($payload)->filter(fn($value, $key) => $existing->$key !== $value);
    //                 if ($dirty->isNotEmpty()) {
    //                     $existing->update($payload);
    //                     $message = 'Shipment update modified';
    //                 } else {
    //                     $message = 'No changes detected — skipped update';
    //                 }

    //                 Log::info("✅ Updating shipment for patient: {$payload['patient_name']} (User ID: {$user->id})");

    //                 $results[] = [
    //                     'user_id' => $user->id,
    //                     'message' => $message,
    //                     'data'    => $existing
    //                 ];
    //             } else {
    //                 $shipment = ShipmentUpdate::create($payload);
    //                 Log::info("✅ Creating new shipment for patient: {$payload['patient_name']} (User ID: {$user->id})");

    //                 $results[] = [
    //                     'user_id' => $user->id,
    //                     'message' => 'Shipment update imported',
    //                     'data'    => $shipment
    //                 ];
    //             }
    //     }

    //     return response()->json($results);
    // }

    /**
     * Normalize a name string:
     * - lowercase
     * - strip titles (dr/dr.)
     * - remove punctuation to spaces
     * - collapse multiple spaces
     */
    private function normalizeName(string $s): string
    {
        $raw = $s;

        $s = mb_strtolower($s, 'UTF-8');

        // strip "dr" / "dr." as whole words
        $s = preg_replace('/\bdr\.?\b/u', '', $s);

        // replace non letters/numbers with spaces
        $s = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $s);

        // collapse spaces
        $s = trim(preg_replace('/\s+/u', ' ', $s));

        return $s;
    }

    /**
     * If raw had a comma format (e.g., "ELKHALILI, ABDELNASIR"),
     * flip the first two tokens of the normalized string to FIRST LAST.
     */
    private function flipIfCommaFormat(string $raw, string $normalized): string
    {
        if (strpos($raw, ',') === false) {
            return $normalized;
        }

        $parts = explode(' ', $normalized);
        if (count($parts) >= 2) {
            $last  = array_shift($parts);
            $first = array_shift($parts);
            // Rebuild: FIRST LAST [rest...]
            return trim($first . ' ' . $last . (count($parts) ? ' ' . implode(' ', $parts) : ''));
        }

        return $normalized;
    }

    /**
     * Heuristic last name key: last token of the normalized full name.
     */
    private function lastNameKey(string $normalizedFull): ?string
    {
        $tokens = array_values(array_filter(explode(' ', $normalizedFull)));
        if (empty($tokens)) {
            return null;
        }
        return end($tokens);
    }

    /**
     * Normalize an identifier (as stored on users.sheet_identifier) similarly,
     * so LIKE matches are resilient to punctuation/casing/titles.
     */
    private function normalizeIdentifierForMatch(?string $s): string
    {
        $s = $s ?? '';
        $s = mb_strtolower($s, 'UTF-8');
        // strip DR/DR.
        $s = preg_replace('/\bdr\.?\b/u', '', $s);
        // punctuation -> spaces
        $s = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $s);
        // collapse spaces
        $s = trim(preg_replace('/\s+/u', ' ', $s));
        return $s;
    }

    /**
     * Parse local date + optional time safely, then return a UTC 'Y-m-d H:i:s' string.
     * - If time is empty/null → startOfDay in the source TZ.
     * - Handles 12h/24h; normalizes edge cases like "00:15 AM" -> "12:15 AM".
     * - Interprets input in America/Chicago and converts to UTC.
     */
    private function parseArrivalDateTimeToUtc(?string $date, ?string $time, string $sourceTz = 'America/Chicago'): ?string
    {
        if (empty($date)) {
            return null;
        }

        $date = trim($date);
        $time = is_null($time) ? '' : trim($time);

        try {
            if ($time === '') {
                return Carbon::parse($date, $sourceTz)
                    ->startOfDay()
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');
            }

            $candidates = [
                'Y-m-d h:i A', // 01:05 PM
                'Y-m-d g:i A', // 1:05 PM
                'Y-m-d H:i',   // 13:05
                'Y-m-d H:i:s', // 13:05:59
                'Y-m-d h:iA',  // 01:05PM
                'Y-m-d g:iA',
                'Y-m-d H.i',   // 13.05
            ];

            foreach ($candidates as $fmt) {
                try {
                    $dt = Carbon::createFromFormat($fmt, "{$date} {$time}", $sourceTz);
                    if ($dt !== false) {
                        return $dt->setTimezone('UTC')->format('Y-m-d H:i:s');
                    }
                } catch (\Throwable $e) {
                    // keep trying
                }
            }

            // Normalize "00:mm AM" to "12:mm AM"
            $normalizedTime = preg_replace('/^00:(\d{2})\s*AM$/i', '12:$1 AM', $time);

            return Carbon::parse("{$date} {$normalizedTime}", $sourceTz)
                ->setTimezone('UTC')
                ->format('Y-m-d H:i:s');

        } catch (\Throwable $e) {
            try {
                return Carbon::parse($date, $sourceTz)
                    ->startOfDay()
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }


    /**
     * POST /api/import-from-sheet
     */
    public function importFromSheet(Request $request)
{
    Log::info('🚀 [STEP 1] importFromSheet() hit', [
        'headers' => $request->headers->all(),
        'payload' => $request->all(),
    ]);

    try {

        // -------------------------------------------------
        // STEP 2: API KEY VALIDATION
        // -------------------------------------------------
        $apiKey = $request->header('X-API-KEY');
        Log::info('🔑 [STEP 2] API Key received', ['key' => $apiKey]);

        if ($apiKey !== config('services.shipment_sheet.api_key')) {
            Log::warning('⛔ [STEP 2 FAILED] Invalid API Key');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // -------------------------------------------------
        // STEP 3: REQUEST VALIDATION
        // -------------------------------------------------
        Log::info('🧪 [STEP 3] Validating payload');

        $data = $request->validate([
            'patient_name'           => 'required|string',
            'prescription_name'      => 'nullable|string',
            'status'                 => 'nullable|string',
            'sheet_identifier'       => 'required|string',
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

        Log::info('✅ [STEP 3 PASSED] Payload validated', $data);

        // -------------------------------------------------
        // STEP 4: IDENTIFIER NORMALIZATION
        // -------------------------------------------------
        $rawIdentifier = $data['sheet_identifier'];
        Log::info('🧠 [STEP 4] Normalizing identifier', ['raw' => $rawIdentifier]);

        $norm        = $this->normalizeName($rawIdentifier);
        $normFlipped = $this->flipIfCommaFormat($rawIdentifier, $norm);
        $lastKey     = $this->lastNameKey($normFlipped);

        Log::info('🧠 [STEP 4 RESULT]', compact('norm', 'normFlipped', 'lastKey'));

        if (!$lastKey) {
            Log::warning('❌ [STEP 4 FAILED] Unable to derive lastKey');
            return response()->json([
                'message' => 'Unable to derive last name key from sheet_identifier'
            ], 422);
        }

        // -------------------------------------------------
        // STEP 5: FETCH USER CANDIDATES
        // -------------------------------------------------
        $likeLast   = '%' . $lastKey . '%';
        $likeShort1 = '%' . mb_substr($lastKey, 0, max(2, (int) floor(mb_strlen($lastKey)/2))) . '%';

        Log::info('🔍 [STEP 5] Searching candidate users', [
            'likeLast'   => $likeLast,
            'likeShort1' => $likeShort1,
        ]);

        $candidates = User::query()
            ->whereNotNull('sheet_identifier')
            ->where(function ($q) use ($likeLast, $likeShort1) {
                $q->whereRaw('LOWER(sheet_identifier) LIKE ?', [$likeLast])
                  ->orWhereRaw('LOWER(sheet_identifier) LIKE ?', [$likeShort1]);
            })
            ->get();

        Log::info('👥 [STEP 5 RESULT] Candidate users found', [
            'count' => $candidates->count(),
            'ids'   => $candidates->pluck('id'),
        ]);

        // -------------------------------------------------
        // STEP 6: PHP NORMALIZED MATCH
        // -------------------------------------------------
        $matchingUsers = $candidates->filter(function (User $u) use ($lastKey, $normFlipped) {
            $userNorm = $this->normalizeIdentifierForMatch($u->sheet_identifier);

            if (str_contains($userNorm, $lastKey)) return true;
            if (str_contains($userNorm, $normFlipped)) return true;

            $tokens = explode(' ', $normFlipped);
            if (count($tokens) >= 2) {
                $alt = trim(end($tokens) . ' ' . $tokens[0]);
                if (str_contains($userNorm, $alt)) return true;
            }

            return false;
        })->values();

        Log::info('✅ [STEP 6 RESULT] Matching users', [
            'count' => $matchingUsers->count(),
            'ids'   => $matchingUsers->pluck('id'),
        ]);

        if ($matchingUsers->isEmpty()) {
            Log::warning('❌ [STEP 6 FAILED] No matching user found');
            return response()->json([
                'message' => 'No matching user found for the provided sheet_identifier'
            ], 404);
        }

        // -------------------------------------------------
        // STEP 7: PROCESS EACH USER
        // -------------------------------------------------
        $results = [];

        foreach ($matchingUsers as $user) {
            Log::info('👤 [STEP 7] Processing user', ['user_id' => $user->id]);

            $payload = $data;
            $payload['user_id'] = $user->id;

            // -------------------------------------------------
            // STEP 8: DATE PARSING
            // -------------------------------------------------
            Log::info('🕒 [STEP 8] Parsing arrival datetime', [
                'date' => $payload['arrived_to_office_date'] ?? null,
                'time' => $payload['arrived_to_office_time'] ?? null,
            ]);

            $payload['arrived_to_office_date'] = $this->parseArrivalDateTimeToUtc(
                $payload['arrived_to_office_date'] ?? null,
                $payload['arrived_to_office_time'] ?? null,
                'America/Chicago'
            );
            unset($payload['arrived_to_office_time']);

            if (!empty($payload['date_shipped'])) {
                $payload['date_shipped'] = $this->parseArrivalDateTimeToUtc(
                    $payload['date_shipped'], null, 'America/Chicago'
                );
            }

            if (!empty($payload['delivered_at'])) {
                $payload['delivered_at'] = $this->parseArrivalDateTimeToUtc(
                    $payload['delivered_at'], null, 'America/Chicago'
                );
            }

            Log::info('📦 [STEP 8 RESULT] Parsed dates', [
                'arrived_to_office_date' => $payload['arrived_to_office_date'],
                'date_shipped'           => $payload['date_shipped'] ?? null,
                'delivered_at'           => $payload['delivered_at'] ?? null,
            ]);

            // -------------------------------------------------
            // STEP 9: IDEMPOTENCY CHECK
            // -------------------------------------------------
            Log::info('🔎 [STEP 9] Checking existing shipment');

            $existing = ShipmentUpdate::where('user_id', $payload['user_id'])
                ->where('patient_name', $payload['patient_name'])
                ->where(function ($q) use ($payload) {
                    if (!empty($payload['rx_number'])) {
                        $q->where('rx_number', $payload['rx_number']);
                    } else {
                        $q->where('prescription_name', $payload['prescription_name']);
                    }
                })
                ->first();

            if ($existing) {
                Log::info('♻️ [STEP 9] Existing shipment found', ['id' => $existing->id]);

                $dirty = collect($payload)->filter(fn ($v, $k) => $existing->$k !== $v);
                if ($dirty->isNotEmpty()) {
                    $existing->update($payload);
                    $message = 'Shipment update modified';
                } else {
                    $message = 'No changes detected — skipped update';
                }

                $results[] = [
                    'user_id' => $user->id,
                    'message' => $message,
                    'data'    => $existing->fresh(),
                ];
            } else {
                Log::info('🆕 [STEP 9] Creating new shipment');

                $shipment = ShipmentUpdate::create($payload);

                $results[] = [
                    'user_id' => $user->id,
                    'message' => 'Shipment update imported',
                    'data'    => $shipment,
                ];
            }
        }

        Log::info('🎉 [FINAL] importFromSheet completed successfully');

        return response()->json($results);

    } catch (\Throwable $e) {
        Log::error('🔥 [FATAL ERROR] importFromSheet crashed', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'Internal server error'
        ], 500);
    }
}


}
