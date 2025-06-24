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
        $query = ShipmentUpdate::query();

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

        $query = ShipmentUpdate::where('user_id', $userId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', '%' . $search . '%')
                ->orWhere('prescription_name', 'like', '%' . $search . '%');
            });
        }

        $updates = $query->paginate($perPage);

        return response()->json($updates);
    }



    // ✅ Update a prescription (shipment update)
    public function update(Request $request, $id)
    {
        $update = ShipmentUpdate::findOrFail($id);

        Log::info('This is a debug log for request', ['data' => $request]);


        $data = $request->validate([
            'status'     => 'nullable|string',
            'notes'      => 'nullable|string',
            'user_id'  => 'nullable|exists:users,id',
            "patient_name" => "nullable|string",
            "prescription_name" => "nullable|string",
            "rx_number" => "nullable|string",
            'date_shipped'   => 'nullable|date',
            'delivered_at'   => 'nullable|date',
            // Add any other fields you want to allow updating
        ]);

        $update->update($data);

        return response()->json(['message' => 'Update successful', 'data' => $update]);
    }
}
