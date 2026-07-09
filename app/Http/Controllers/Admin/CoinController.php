<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinTransaction;
use App\Models\User;
use App\Services\CoinService;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function __construct(private CoinService $coinService) {}

    public function index(Request $request)
    {
        $query = CoinTransaction::with(['user', 'order'])->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(25)->withQueryString();
        $totalCoinsOutstanding = User::sum('coins_balance');

        return view('admin.coins.index', compact('transactions', 'totalCoinsOutstanding'));
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'amount'     => 'required|integer|not_in:0',
            'reason'     => 'required|string|max:255',
        ]);

        $identifier = trim($data['identifier']);
        $user = is_numeric($identifier)
            ? User::find($identifier)
            : User::where('email', $identifier)->orWhere('phone', $identifier)->first();

        if (!$user) {
            return back()->with('error', "No user found matching \"{$identifier}\".");
        }

        $this->coinService->adjust($user, $data['amount'], $data['reason']);

        return back()->with('success', "Adjusted {$user->name}'s coin balance by {$data['amount']}.");
    }
}
