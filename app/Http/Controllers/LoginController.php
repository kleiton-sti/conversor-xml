<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use App\Logging\DatabaseLogger;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Foundation\Inspiring;

class LoginController extends Controller
{
    public function index()
    {

        try {
            if (!Auth::check()) {
                $mensagemInspiradora = Inspiring::quote();
                return view('login.login', compact('mensagemInspiradora'));
            }

            return redirect()->route('home');
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            return redirect()
                ->back()
                ->withErrors('Ocorreu um erro', 'erro');
        }
    }

    public function login(LoginFormRequest $request)
    {
        try {
            if (!Auth::attempt($request->only(['registro', 'password']))) {
                return redirect()
                    ->back()
                    ->withErrors('Credenciais inválidas', 'credenciaisInvalidas');
            }

            DatabaseLogger::log($context = [
                'level_name'=>'LOGIN',
                'user' => Auth::user()->registro,
                'ip'=>$request->ip(),
                'message' => 'Realizou login no sistema',
                'entity_type' => null,
                'entity_id' => 0,
                'context' => '',
                
            ]);

            
            return redirect('home');
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {

            Auth::logout();
            Log::error($e);
            abort(500, $e->getMessage());

        }
    }




    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            Auth::logout();

            DatabaseLogger::log($context = [
                'level_name'=>'LOGOFF',
                'user' => $user->registro,
                'ip'=>$request->ip(),
                'message' => 'Saiu do sistema',
                'entity_type' => null,
                'entity_id' => 0,
                'context' => '',
                
            ]);

            return redirect('login');
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            Log::error($e);
            return redirect()
                ->back()
                ->withErrors('Ocorreu um erro', 'erro');
        }
    }
}
