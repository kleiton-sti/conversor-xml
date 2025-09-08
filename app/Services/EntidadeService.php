<?php

namespace App\Services;

use App\Logging\DatabaseLogger;
use App\Models\Entidade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntidadeService
{

    public function index()
    {
        $entidades = Entidade::withTrashed()->orderBy('entidade')->get();
        return $entidades;
    }

    public function listaEntidades()
    {
        $entidades = Entidade::get();
        $entidades = $entidades->pluck('entidade', 'id');
        return $entidades;
    }

    public function create()
    {
        $entidade = new Entidade();

        return $entidade;
    }

    public function store($entidade, $ip)
    {
        try {
            DB::beginTransaction();

            $entidade = Entidade::create([
                'entidade' => $entidade
            ]);

            DatabaseLogger::log($context = [
                'level_name' => 'CREATE',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Cadastrou Entidade',
                'entity_type' => 'ENTIDADE',
                'entity_id' => $entidade->id,
                'context' => $entidade,
            ]);

            DB::commit();
            return $entidade;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Entidade $entidade, array $dataRequest, $ip)
    {
        try {
            DB::beginTransaction();

            // $entidade->entidade = $nomeEntidade;
            $entidade->update($dataRequest);
            //$entidade->save();

            DatabaseLogger::log($context = [
                'level_name' => 'UPDATE',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Atualizou Entidade',
                'entity_type' => 'ENTIDADE',
                'entity_id' => $entidade->id,
                'context' => $entidade,
            ]);

            DB::commit();

            return $entidade;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Entidade $entidade, $request)
    {
        try {
            DB::beginTransaction();

            if ($entidade->unidades()->exists()) {
                return redirect()->back()->withErrors('Não é possível excluir. Existem unidades vinculadas a esta entidade.');
            }

            // $entidade->entidade = $nomeEntidade;
            $entidade->delete();
            //$entidade->save();
            $request->session()->flash('flashMsg', "Entidade inativada com sucesso");

            DatabaseLogger::log($context = [
                'level_name' => 'DESTROY',
                'user' => Auth::user()->registro,
                'ip' => $request->ip(),
                'message' => 'Inativou Entidade',
                'entity_type' => 'ENTIDADE',
                'entity_id' => $entidade->id,
                'context' => $entidade,
            ]);

            DB::commit();

            return $entidade;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function restore($id, $request)
    {
        try {
            DB::beginTransaction();

            $entidade = Entidade::withTrashed()->findOrFail($id);

            // $entidade->entidade = $nomeEntidade;
            $entidade->restore();
            //$entidade->save();
            $request->session()->flash('flashMsg', "Entidade restaurada com sucesso");

            DatabaseLogger::log($context = [
                'level_name' => 'RESTORE',
                'user' => Auth::user()->registro,
                'ip' => $request->ip(),
                'message' => 'Restaurou Entidade',
                'entity_type' => 'ENTIDADE',
                'entity_id' => $entidade->id,
                'context' => $entidade,
            ]);

            DB::commit();

            return $entidade;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function search($request)
    {
        try {

            session()->forget('filtrosPesquisa');
            $filtro = null;
            $entidades = Entidade::withTrashed();

            // dd($request);

            if ($request->entidade) {
                $entidades = $entidades->entidade($request->entidade);
                $filtro = $filtro . "Entidade:$request->entidade /";
            }

            
            $entidades = $entidades->get();
            session(['filtrosPesquisa' => $filtro]);

            // dd($entidades);

            return $entidades;

            DatabaseLogger::log($context = [
                'level_name' => 'SEARCH',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Pesquisou Entidade',
                'entity_type' => 'ENTIDADE',
                'entity_id' => '',
                'context' => $entidades,
            ]);



            return $entidades;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
