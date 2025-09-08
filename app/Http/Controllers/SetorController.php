<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetoresFormRequest;
use App\Models\Setor;
use App\Services\SetorService;
use App\Services\UnidadeService;
use Illuminate\Http\Request;

class SetorController extends Controller
{

    function __construct(SetorService $setorService, UnidadeService $unidadeService)
    {
        $this->setorService = $setorService;
        $this->unidadeService = $unidadeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $setores = collect();
            $setores = Setor::withTrashed()->get();
            $flashMsg = $request->session()->get('flashMsg');
            session()->forget('filtrosPesquisa');
            return view('setores.index', [
                'setores' => $setores,
                'flashMsg' => $flashMsg
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Setores", $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Setores", $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {

            $setor = $this->setorService->create();
            $unidades = $this->unidadeService->listaUnidades();



            return view('setores.form', [
                'setor' => $setor,
                'unidades' => $unidades
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro("Erro ao executar o método create do Controller de Unidades", $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro("Erro ao executar o método create do Controller de Unidades", $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SetoresFormRequest $request)
    {
        try {
            //CRIA O SETOR
            $setor = $this->setorService->store(
                $request->setor,
                $request->unidade_id,
                $request->ip()
            );

            $request->session()->flash('flashMsg', "Setor cadastrado com sucesso");

            return redirect()->route('setores.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Setores', $e->getMessage());
            abort(500, $e->getMessage());
        }
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
    public function edit(Setor $setor)
    {
        try {

            $unidades = $this->unidadeService->listaUnidades();

            return view('setores.form', [
                'setor' => $setor,
                'unidades' => $unidades
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Setores', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SetoresFormRequest $request, Setor $setor)
    {
        try {

            $setor = $this->setorService->update(
                $setor,
                $request->validated(),
                $request->ip()
            );

            $request->session()->flash('flashMsg', "Setor atualizada com sucesso");

            return redirect()->route('setores.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método update do Controller de Unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método update do Controller de Unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método update do Controller de unidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Setor $setor)
    {
        try {

            $setor = $this->setorService->destroy(
                $setor,
                $request
            );

            return redirect()->route('setores.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de Setores', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    public function restore($id, Request $request)
    {
        try {
            $setor = $this->setorService->restore(
                $id,
                $request
            );

            return redirect()->route('setores.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de Setores', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $setores = $this->setorService->search($request);

            $flashMsg = $request->session()->get('flashMsg');

            $filtros = session('filtrosPesquisa');

            return view('setores.index', [
                'setores' => $setores,
                'flashMsg' => $flashMsg
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Setores', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Setores', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }
}
