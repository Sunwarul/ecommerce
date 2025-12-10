<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosSessionController extends Controller
{
    public function current()
    {
        $session = PosSession::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest('id')
            ->first();

        return response()->json([
            'session' => $session,
        ]);
    }

    public function open(Request $request)
    {
        $data = $request->validate([
            'branch_id'    => ['nullable', 'exists:branches,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        // close old open session if any
        PosSession::where('user_id', Auth::id())
            ->where('status', 'open')
            ->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);

        $session = PosSession::create([
            'user_id'         => Auth::id(),
            'branch_id'       => $data['branch_id'] ?? null,
            'warehouse_id'    => $data['warehouse_id'] ?? null,
            'opening_balance' => $data['opening_balance'] ?? 0,
            'status'          => 'open',
            'opened_at'       => now(),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
        ]);
    }

    public function close(Request $request)
    {
        $data = $request->validate([
            'session_id'      => ['required', 'exists:pos_sessions,id'],
            'closing_balance' => ['required', 'numeric'],
        ]);

        $session = PosSession::where('id', $data['session_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $session->update([
            'closing_balance' => $data['closing_balance'],
            'status'          => 'closed',
            'closed_at'       => now(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
