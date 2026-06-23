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
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status === 'blocked') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been blocked. Please contact support.']);
            }

            if (session('pending_action')) {
                $redirectTo = app(PendingActionService::class)->execute($user);
                return redirect($redirectTo)->with('success', 'Login successful. Continuing your action.');
            }

            if ($user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Sales Executive', 'Inventory Manager', 'Order Manager', 'Customer Support'])) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('customer.dashboard');
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        $user->assignRole('Customer');

        Auth::login($user);

        if (session('pending_action')) {
            $redirectTo = app(PendingActionService::class)->execute($user);
            return redirect($redirectTo)->with('success', 'Registration successful. Continuing your action.');
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
