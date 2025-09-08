<?php

namespace App\Services;

use App\Logging\DatabaseLogger;
use App\Models\Setor;
use App\Models\Unidade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetorService
{

    public function index()
    {
        $setores = Setor::withTrashed()->orderBy('setor')->get();
        // return $setor;
    }

    public function create()
    {
        $setor = new Setor();

        return $setor;
    }

    public function store($nomeSetor,$unidadeId, $ip)
    {
        try {
            DB::beginTransaction();
            
            $setor = Setor::create([
                'setor' => $nomeSetor,
                'unidade_id' =>$unidadeId
            ]);
            
            DatabaseLogger::log($context = [
                'level_name' => 'CREATE',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Cadastrou Setor',
                'entity_type' => 'SETOR',
                'entity_id' => $setor->id,
                'context' => $setor,
            ]);

            DB::commit();
            return $setor;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Setor $setor, array $dataRequest, $ip)
    {
        try {
            DB::beginTransaction();
            $setor->update($dataRequest);

            DatabaseLogger::log($context = [
                'level_name' => 'UPDATE',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Atualizou Setor',
                'entity_type' => 'SETOR',
                'entity_id' => $setor->id,
                'context' => $setor,
            ]);

            DB::commit();

            return $setor;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Setor $setor, $request)
    {
        try {
            DB::beginTransaction();

            if ($setor->users()->exists()) {
                return redirect()->back()->withErrors('Não é possível excluir. Existem usuários vinculados a este setor.');
            }

            $setor->delete();
            $request->session()->flash('flashMsg', "Setor inativado com sucesso");

            DatabaseLogger::log($context = [
                'level_name' => 'DESTROY',
                'user' => Auth::user()->registro,
                'ip' => $request->ip(),
                'message' => 'Inativou Setor',
                'entity_type' => 'Setor',
                'entity_id' => $setor->id,
                'context' => $setor,
            ]);

            DB::commit();

            return $setor;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function restore($id, $request)
    {
        try {
            DB::beginTransaction();

            $setor = Setor::withTrashed()->findOrFail($id);

           
            $setor->restore();
        
            $request->session()->flash('flashMsg', "Setor restaurado com sucesso");

            DatabaseLogger::log($context = [
                'level_name' => 'RESTORE',
                'user' => Auth::user()->registro,
                'ip' => $request->ip(),
                'message' => 'Restaurou Setor',
                'entity_type' => 'Setor',
                'entity_id' => $setor->id,
                'context' => $setor,
            ]);

            DB::commit();

            return $setor;
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
            $setores = Setor::withTrashed();

            

            if ($request->setor) {
                $setores = $setores->filtroSetor($request->setor);
                $filtro = $filtro . "Setor:$request->setor /";
            }

            if ($request->unidade) {
                $setores = $setores->filtroUnidade($request->unidade);
                $filtro = $filtro . "Unidade:$request->unidade /";
            }

            if (!is_null($request->ativo)) {
                $setores = $setores->filtroAtivo($request->ativo);
                $filtro = $filtro . "Ativo:$request->ativo /";
            }

            $setores = $setores->get();
          
            session(['filtrosPesquisa' => $filtro]);

            return $setores;

            DatabaseLogger::log($context = [
                'level_name' => 'SEARCH',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Pesquisou Setor',
                'entity_type' => 'SETOR',
                'entity_id' => '',
                'context' => $setores,
            ]);


            
            return $unidades;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
