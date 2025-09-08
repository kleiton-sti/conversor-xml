<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnidadesFormRequest;
use App\Models\Unidade;
use App\Services\EntidadeService;
use App\Services\UnidadeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UnidadeController extends Controller
{
    function __construct(UnidadeService $unidadeService, EntidadeService $entidadeService)
    {
        $this->unidadeService = $unidadeService;
        $this->entidadeService = $entidadeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            //$unidades = $this->unidadeService->index();
            $unidades = collect();
            $flashMsg = $request->session()->get('flashMsg');
            session()->forget('filtrosPesquisa');
            return view('unidades.index', [
                'unidades' => $unidades,
                'flashMsg' => $flashMsg
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Unidades", $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro("Erro ao executar o método index do Controller de Unidades", $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {

            $unidade = $this->unidadeService->create();
            $entidades = $this->entidadeService->listaEntidades();



            return view('unidades.form', [
                'unidade' => $unidade,
                'entidades' => $entidades
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
    public function store(UnidadesFormRequest $request)
    {
        try {
            //CRIA A UNIDADE
            $unidade = $this->unidadeService->store(
                $request->unidade,
                $request->entidade_id,
                $request->ip()
            );

            $request->session()->flash('flashMsg', "Unidade cadastrada com sucesso");


            return redirect()->route('unidades.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método store do Controller de Unidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unidade $unidade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unidade $unidade)
    {
        try {

            $entidades = $this->entidadeService->listaEntidades();

            return view('unidades.form', [
                'unidade' => $unidade,
                'entidades' => $entidades
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método edit do Controller de Unidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnidadesFormRequest $request, Unidade $unidade)
    {
        try {

            $unidade = $this->unidadeService->update(
                $unidade,
                $request->validated(),
                $request->ip()
            );

            $request->session()->flash('flashMsg', "Unidade atualizada com sucesso");

            return redirect()->route('unidades.index');
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
    public function destroy(Request $request, Unidade $unidade)
    {

        try {

            $unidade = $this->unidadeService->destroy(
                $unidade,
                $request
            );

            return redirect()->route('unidades.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método destroy do Controller de unidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }


    public function restore($id, Request $request)
    {
        try {
            $unidade = $this->unidadeService->restore(
                $id,
                $request
            );

            return redirect()->route('unidades.index');
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método restore do Controller de unidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }





    public function search(Request $request)
    {
        try {
            $unidades = $this->unidadeService->search($request);

            $flashMsg = $request->session()->get('flashMsg');

            $filtros = session('filtrosPesquisa');

            return view('unidades.index', [
                'unidades' => $unidades,
                'flashMsg' => $flashMsg
            ]);
        } catch (ModelNotFoundException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (NotFoundHttpException $e) {
            LogController::logErro('Erro ao executar o método search do Controller de unidades', $e->getMessage());
            abort(404, "RECURSO NAO ENCONTRADO");
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            LogController::logErro('Erro ao executar o método search do Controller de unidades', $e->getMessage());
            abort(500, $e->getMessage());
        }
    }
}
