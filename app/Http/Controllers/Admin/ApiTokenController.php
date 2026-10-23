<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens()->latest()->get();

        return view('admin.api-tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:60',
            'expires' => 'required|in:never,30,90,365',
        ]);

        $expiresAt = $data['expires'] === 'never' ? null : now()->addDays((int) $data['expires']);

        $token = $request->user()->createToken($data['name'], ['*'], $expiresAt);

        
        ActivityLog::record(
            'token.created',
            $request->user()->name . ' created API token "' . $data['name'] . '"'
                . ($expiresAt ? ' (expires ' . $expiresAt->format('M d, Y') . ')' : ' (never expires)')
        );

        
        return redirect()->route('admin.api-tokens.index')
            ->with('new_token', $token->plainTextToken);
    }

    public function destroy(Request $request, int $token)
    {
        
        $row = $request->user()->tokens()->whereKey($token)->first();

        if ($row) {
            $name = $row->name;
            $row->delete();

            
            ActivityLog::record(
                'token.revoked',
                $request->user()->name . ' revoked API token "' . $name . '"'
            );
        }

        return redirect()->route('admin.api-tokens.index');
    }
}