<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request with admin verification
     */
    public function register(Request $request)
    {
        // Verificar senha administrativa
        if (!$this->verifyAdminPassword($request->admin_password ?? '')) {
            return back()->withErrors([
                'admin_password' => 'Senha administrativa incorreta.'
            ])->withInput($request->except(['password', 'password_confirmation', 'admin_password']));
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        return redirect()->route('login')->with('success', 
            'Conta criada com sucesso! Aguarde a ativação pelo administrador antes de fazer login.'
        );
    }

    /**
     * Verify admin password
     */
    protected function verifyAdminPassword($password)
    {
        // Em produção, você pode armazenar isso em config ou banco de dados
        $adminPassword = config('auth.admin_registration_password', 'admin2024!');
        return $password === $adminPassword;
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'admin_verified' => ['required', 'in:1'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'role.required' => 'A função é obrigatória.',
            'role.in' => 'Função inválida selecionada.',
            'admin_verified.required' => 'Verificação administrativa obrigatória.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => false, // Conta inativa por padrão, admin deve ativar
        ]);
    }

    /**
     * AJAX endpoint para verificar senha administrativa
     */
    public function verifyAdminPasswordAjax(Request $request)
    {
        $password = $request->input('admin_password');
        
        if ($this->verifyAdminPassword($password)) {
            return response()->json([
                'success' => true,
                'message' => 'Senha administrativa verificada com sucesso.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Senha administrativa incorreta.'
        ], 400);
    }
}