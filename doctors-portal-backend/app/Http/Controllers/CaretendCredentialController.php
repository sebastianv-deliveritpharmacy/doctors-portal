<?php

namespace App\Http\Controllers;

use App\Models\CaretendCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaretendCredentialController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $credential = $user->caretendCredential;

        return response()->json($credential);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'client_id' => 'required|string|max:255',
            'secret' => 'required|string|max:1000',
        ]);

        $existing = $user->caretendCredential;

        if ($existing) {
            return response()->json(['message' => 'Credential already exists. Use update instead.'], 400);
        }

        $credential = $user->caretendCredential()->create([
            'client_id' => $request->client_id,
            'secret' => $request->secret,
        ]);

        return response()->json(['message' => 'Credential saved', 'data' => $credential]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'client_id' => 'required|string|max:255',
            'secret' => 'required|string|max:1000',
        ]);

        $credential = $user->caretendCredential;

        if (!$credential) {
            return response()->json(['message' => 'Credential not found. Use store to create it.'], 404);
        }

        $credential->update([
            'client_id' => $request->client_id,
            'secret' => $request->secret,
        ]);

        return response()->json(['message' => 'Credential updated', 'data' => $credential]);
    }

    public function destroy()
    {
        $user = Auth::user();

        $credential = $user->caretendCredential;

        if ($credential) {
            $credential->delete();
            return response()->json(['message' => 'Credential deleted']);
        }

        return response()->json(['message' => 'No credential to delete'], 404);
    }
}
