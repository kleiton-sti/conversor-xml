<?php

namespace App\Services;

use App\Logging\DatabaseLogger;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuarioService
{

    public function index()
    {
        $unidades = Unidade::withTrashed()->orderBy('unidade')->get();
        return $unidades;
    }

    public function listaUnidades()
    {
        // $unidades = Unidade::get();
        // $unidades = $unidades->pluck('unidade', 'id');
        // return $unidades;

        $unidades = Unidade::with('entidade')->orderBy('unidade')->get();

        $unidades = $unidades->mapWithKeys(function ($unidade) {
            return [
                $unidade->id => $unidade->entidade->entidade . ' > ' . $unidade->unidade
            ];
        });
        return $unidades;
    }

    public function create()
    {
        $usuario = new User();

        return $usuario;
    }

    public function store($nomeUnidade,$entidadeId, $ip)
    {
        try {
            DB::beginTransaction();
            $unidade = Unidade::create([
                'unidade' => $nomeUnidade,
                'entidade_id' =>$entidadeId
            ]);
            
            DatabaseLogger::log($context = [
                'level_name' => 'CREATE',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Cadastrou Unidade',
                'entity_type' => 'UNIDADE',
                'entity_id' => $unidade->id,
                'context' => $unidade,
            ]);

            DB::commit();
            return $unidade;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Unidade $unidade, array $dataRequest, $ip)
    {
        try {
            DB::beginTransaction();

            $unidade->update($dataRequest);

            DatabaseLogger::log($context = [
                'level_name' => 'UPDATE',
                'user' => Auth::user()->registro,
                'ip' => $ip,
                'message' => 'Atualizou Unidade',
                'entity_type' => 'UNIDADE',
                'entity_id' => $unidade->id,
                'context' => $unidade,
            ]);

            DB::commit();

            return $unidade;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Unidade $unidade, $request)
    {
        try {
            DB::beginTransaction();

            if ($unidade->setores()->exists()) {
                return redirect()->back()->withErrors('Não é possível excluir. Existem setores vinculados a esta unidade.');
            }

            $unidade->delete();
            $request->session()->flash('flashMsg', "Unidade inativada com sucesso");

            DatabaseLogger::log($context = [
                'level_name' => 'DESTROY',
                'user' => Auth::user()->registro,
                'ip' => $request->ip(),
                'message' => 'Inativou Unidade',
                'entity_type' => 'UNIDADE',
                'entity_id' => $unidade->id,
                'context' => $unidade,
            ]);

            DB::commit();

            return $unidade;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function restore($id, $request)
    {
        try {
            DB::beginTransaction();

            $unidade = Unidade::withTrashed()->findOrFail($id);

           
            $unidade->restore();
        
            $request->session()->flash('flashMsg', "Unidade restaurada com sucesso");

            DatabaseLogger::log($context = [
                'level_name' => 'RESTORE',
                'user' => Auth::user()->registro,
                'ip' => $request->ip(),
                'message' => 'Restaurou Unidade',
                'entity_type' => 'UNIDADE',
                'entity_id' => $unidade->id,
                'context' => $unidade,
            ]);

            DB::commit();

            return $unidade;
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
            $usuarios = User::withTrashed();

            

            if ($request->nome) {
                $usuarios = $usuarios->filtroNome($request->nome);
                $filtro = $filtro . "Nome:$request->nome /";
            }

            if ($request->setor) {
                $usuarios = $usuarios->filtroSetor($request->setor);
                $filtro = $filtro . "Setor:$request->setor /";
            }

            if ($request->unidade) {
                $usuarios = $usuarios->filtroUnidade($request->unidade);
                $filtro = $filtro . "Unidade:$request->unidade /";
            }

            if ($request->entidade) {
                $usuarios = $usuarios->filtroEntidade($request->entidade);
                $filtro = $filtro . "Entidade:$request->entidade /";
            }

            if (!is_null($request->ativo)) {
                $usuarios = $usuarios->filtroAtivo($request->ativo);
                $filtro = $filtro . "Ativo:$request->ativo /";
            }
            
            $usuarios = $usuarios->get();
          
            session(['filtrosPesquisa' => $filtro]);

           
            DatabaseLogger::log($context = [
                'level_name' => 'SEARCH',
                'user' => Auth::user()->registro,
                'ip' => $request->ip,
                'message' => 'Pesquisou Usuario',
                'entity_type' => 'USUARIO',
                'entity_id' => '0',
                'context' => $usuarios,
            ]);

            return $usuarios;

            
            return $unidades;
        } catch (\Error | \Exception | \ErrorException | \Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
