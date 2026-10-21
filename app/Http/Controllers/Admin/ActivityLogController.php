<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $type   = $request->query('type', 'all');   
        $search = trim((string) $request->query('q', ''));

        $logs = ActivityLog::with('user')
            ->when($type !== 'all', fn ($q) => $q->where('action', 'like', $type . '.%'))
            ->when($search !== '', fn ($q) => $q->where('description', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.activity-log', compact('logs', 'type', 'search'));
    }
}