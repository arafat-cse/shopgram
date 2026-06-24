<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::with('user')->orderByDesc('created_at');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->action) {
            $query->where('action', $request->action);
        }
        if ($request->model_type) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs       = $query->paginate(30);
        $actions    = AdminActivityLog::distinct()->pluck('action')->sort()->values();
        $modelTypes = AdminActivityLog::distinct()->whereNotNull('model_type')->pluck('model_type')->sort()->values();

        return view('admin.activity-logs.index', compact('logs', 'actions', 'modelTypes'));
    }
}
