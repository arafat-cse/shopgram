<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PendingActionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required',
        ]);

        $login = trim($request->login);
        // Phone: digits only or starts with +/0, no @ symbol
        $field = (preg_match('/^[+0-9][0-9\s\-]{4,}$/', $login)) ? 'phone' : 'email';

        $user = User::where($field, $login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['login' => 'These credentials do not match our records.'])
                ->onlyInput('login');
        }

        if ($user->status === 'blocked') {
            return back()->withErrors(['login' => 'Your account has been blocked. Please contact support.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if (session('pending_action')) {
            $action = session('pending_action');
            $redirectTo = app(PendingActionService::class)->execute($user);
            $msg = match($action) {
                'add_to_cart'      => '✅ Logged in! Product added to your cart.',
                'buy_now'          => '✅ Logged in! Taking you to checkout.',
                'add_to_wishlist'  => '✅ Logged in! Product added to your wishlist.',
                default            => '✅ Login successful!',
            };
            return redirect($redirectTo)->with('success', $msg);
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Sales Executive', 'Inventory Manager', 'Order Manager', 'Customer Support'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'email'    => 'nullable|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->filled('email') ? $request->email : null,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        $user->assignRole('Customer');

        Auth::login($user);

        if (session('pending_action')) {
            $action = session('pending_action');
            $redirectTo = app(PendingActionService::class)->execute($user);
            $msg = match($action) {
                'add_to_cart'      => '🎉 Welcome! Product added to your cart.',
                'buy_now'          => '🎉 Welcome! Taking you to checkout.',
                'add_to_wishlist'  => '🎉 Welcome! Product added to your wishlist.',
                default            => '🎉 Welcome to ShopGram!',
            };
            return redirect($redirectTo)->with('success', $msg);
        }

        return redirect()->route('customer.dashboard')->with('success', 'Welcome to ShopGram!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
