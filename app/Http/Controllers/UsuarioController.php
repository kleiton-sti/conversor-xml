<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UsuarioService;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{

    function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $usuarios = collect();
            $usuarios = User::withTrashed()->get();
            $flashMsg = $request->session()->get('flashMsg');
            session()->forget('filtrosPesquisa');
            return view('usuarios.index', [
                'usuarios' => $usuarios,
                'flashMsg' => $flashMsg
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Usuarios", $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Usuarios", $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {

            $usuario = $this->usuarioService->create();
            $entidades = null;
            $unidades = null;
            $setores = null;

            return view('usuarios.form', [
                'usuario' => $usuario,
                'entidades' => $entidades,
                'unidades' => $unidades,
                'setores' => $setores
            ]);
            
        } catch (ModelNotFoundException $e) {
            LogController::logErro("Erro ao executar o método create do Controller de Usuarios", $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro("Erro ao executar o método create do Controller de Usuarios", $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
        try {
            $usuarios = $this->usuarioService->search($request);

            $flashMsg = $request->session()->get('flashMsg');

            $filtros = session('filtrosPesquisa');

            return view('usuarios.index', [
                'usuarios' => $usuarios,
                'flashMsg' => $flashMsg
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Usuarios', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Usuarios', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Usuarios', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }
}
