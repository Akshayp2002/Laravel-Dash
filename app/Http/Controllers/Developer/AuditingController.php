<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use Illuminate\Http\Request;

class AuditingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Audit::orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('user_id', 'like', "%$search%")
                    ->orWhere('event', 'like', "%$search%")
                    ->orWhere('old_values', 'like', "%$search%")
                    ->orWhere('new_values', 'like', "%$search%")
                    ->orWhere('url', 'like', "%$search%")
                    ->orWhere('ip_address', 'like', "%$search%");
            });
        }

        // Use cursor pagination for large datasets (instead of regular paginate)
        $audits = $query->paginate(10);

        $totalCount = Audit::count();

        return view('developer.audit.auditing', compact('audits', 'totalCount', 'search'));
    }
}
