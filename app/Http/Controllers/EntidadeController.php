<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Services\EntidadeService;
use Illuminate\Http\Request;
use App\Http\Controllers\LogController;
use App\Http\Requests\EntidadesFormRequest;

class EntidadeController extends Controller
{

    function __construct(EntidadeService $entidadeService)
    {
        $this->entidadeService = $entidadeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $entidades = $this->entidadeService->index();
            $flashMsg = $request->session()->get('flashMsg');
            session()->forget('filtrosPesquisa');
            return view('entidades.index', [
                'entidades' => $entidades,
                'flashMsg' => $flashMsg
            ]);

        } catch (ModelNotFoundException $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Entidades", $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Entidades", $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            
            $entidade = $this->entidadeService->create();

            return view('entidades.form', [
                'entidade' => $entidade,
            ]);

        } catch (ModelNotFoundException $e) {
            LogController::logErro("Erro ao executar o método create do Controller de Entidades", $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro("Erro ao executar o método create do Controller de Entidades", $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EntidadesFormRequest $request)
    {
        try {
            //CRIA A ENTIDADE
            $entidade = $this->entidadeService->store(
                $request->entidade,
                $request->ip()
            );

            $request->session()->flash('flashMsg', "Entidade cadastrada com sucesso");


            return redirect()->route('entidades.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Entidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Entidade $entidade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entidade $entidade)
    {
        try {

            //$entidade = $this->entidadesService->buscaEntidadePorId($idEntidade);

            return view('entidades.form', [
                'entidade' => $entidade,
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Entidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EntidadesFormRequest $request, Entidade $entidade)
    {
        try {

            $entidade = $this->entidadeService->update(
                $entidade,
                $request->validated(),
                $request->ip()
            );

            $request->session()->flash('flashMsg', "Entidade atualizada com sucesso");

            return redirect()->route('entidades.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método update do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método update do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método update do Controller de Entidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Entidade $entidade)
    {

        try {

            $entidade = $this->entidadeService->destroy(
                $entidade,
                $request
            );

            return redirect()->route('entidades.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de Entidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    
    public function restore($id, Request $request)
    {
        try {
            $entidade = $this->entidadeService->restore(
                $id,
                $request
            );
            
            return redirect()->route('entidades.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de Entidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }
    
    
    
    
    
    public function search(Request $request)
    {
        try {

            $entidades = $this->entidadeService->search($request);

            $flashMsg = $request->session()->get('flashMsg');

            $filtros = session('filtrosPesquisa');

            return view('entidades.index', [
                'entidades' => $entidades,
                'flashMsg' => $flashMsg
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Entidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método search do Controller de Entidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }
}
