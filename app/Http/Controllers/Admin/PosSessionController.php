<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosSessionController extends Controller
{
    public function current()
    {
        $session = PosSession::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest('id')
            ->first();

        return response()->json($session);
    }

    public function open(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'opening_cash' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($data) {
            $alreadyOpen = PosSession::where('user_id', Auth::id())
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if ($alreadyOpen) {
                return back()->withErrors([
                    'session' => 'You already have an open POS session.',
                ]);
            }

            PosSession::create([
                'user_id' => Auth::id(),
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'],
                'opening_cash' => $data['opening_cash'] ?? 0,
                'note' => $data['note'] ?? null,
                'status' => 'open',
                'opened_at' => now(),
            ]);

            return back()->with('success', 'POS session opened.');
        });
    }

    public function close(Request $request)
    {
        $data = $request->validate([
            'closing_cash' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($data) {
            $session = PosSession::where('user_id', Auth::id())
                ->where('status', 'open')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (!$session) {
                return back()->withErrors([
                    'session' => 'No open POS session found.',
                ]);
            }

            $session->update([
                'closing_cash' => $data['closing_cash'] ?? 0,
                'note' => $data['note'] ?? $session->note,
                'status' => 'closed',
                'closed_at' => now(),
            ]);

            return back()->with('success', 'POS session closed.');
        });
    }
}
