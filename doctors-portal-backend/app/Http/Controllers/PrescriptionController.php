<?php
namespace App\Http\Controllers;

use App\Services\CareTendService;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    protected CareTendService $careTend;

    public function __construct(CareTendService $careTend)
    {
        $this->careTend = $careTend;
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'fromDate' => 'required|date',
            'toDate'   => 'required|date|after_or_equal:fromDate',
        ]);

        $prescriptions = $this->careTend->getPrescriptions($data['fromDate'], $data['toDate']);

        return response()->json([
            'data' => $prescriptions,
        ]);
    }
}
