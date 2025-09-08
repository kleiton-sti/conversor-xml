<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    static function logErro($msgAuxiliar = null,$erro)
    {
        Log::error($msgAuxiliar."->".$erro);
    }



    // static function logCadastroEntidade($usuarioLogado, $entidade, $ipOrigem, $acao, $tituloLog)
    // {

    //     $log = [
    //         'USUARIO' => $usuarioLogado->id,
    //         'NOME' => $usuarioLogado->nome,
    //         'LOCAL' => $usuarioLogado->local->local,
    //         'SETOR' => $usuarioLogado->local->setor->setor,
    //         'ENTIDADE' => $usuarioLogado->local->setor->entidade->entidade,
    //         'ACAO' => $acao,
    //         'IP_ORIGEM' => $ipOrigem,
    //         'DADOS_PERSISTIDOS' => $entidade->toArray()
    //     ];


    //     //first parameter passed to Monolog\Logger sets the logging channel name
    //     $municipeLog = new Logger($tituloLog);
    //     $municipeLog->pushHandler(new StreamHandler(storage_path('logs/access-log/' . $entidade->id . '.log')), Logger::INFO);
    //     $municipeLog->info('log', $log);
    // }


    // static function logCadastroEntidadeFilha($usuarioLogado, $entidade, $ipOrigem, $acao, $tituloLog, $idIndividuo)
    // {

    //     $log = [
    //         'USUARIO' => $usuarioLogado->id,
    //         'NOME' => $usuarioLogado->nome,
    //         'LOCAL' => $usuarioLogado->local->local,
    //         'SETOR' => $usuarioLogado->local->setor->setor,
    //         'ENTIDADE' => $usuarioLogado->local->setor->entidade->entidade,
    //         'ACAO' => $acao,
    //         'IP_ORIGEM' => $ipOrigem,
    //         'DADOS_PERSISTIDOS' => $entidade->toArray()
    //     ];

    //     //first parameter passed to Monolog\Logger sets the logging channel name
    //     $municipeLog = new Logger($tituloLog);
    //     $municipeLog->pushHandler(new StreamHandler(storage_path('logs/access-log/' . $idIndividuo . '.log')), Logger::INFO);
    //     $municipeLog->info('log', $log);
    // }

    // static function logAlteracaoFamilia($usuarioLogado, $ipOrigem, $acao, $tituloLog, $idIndividuo)
    // {

    //     $log = [
    //         'USUARIO' => $usuarioLogado->id,
    //         'NOME' => $usuarioLogado->nome,
    //         'LOCAL' => $usuarioLogado->local->local,
    //         'SETOR' => $usuarioLogado->local->setor->setor,
    //         'ENTIDADE' => $usuarioLogado->local->setor->entidade->entidade,
    //         'IP_ORIGEM' => $ipOrigem,
    //         'ACAO' => $acao,
    //     ];

    //     //first parameter passed to Monolog\Logger sets the logging channel name
    //     $municipeLog = new Logger($tituloLog);
    //     $municipeLog->pushHandler(new StreamHandler(storage_path('logs/access-log/' . $idIndividuo . '.log')), Logger::INFO);
    //     $municipeLog->info('log', $log);
    // }

    // static function logAtividadeUsuario($usuarioLogado, $ipOrigem, $acao, $tituloLog)
    // {
    //     $log = [
    //         'USUARIO' => $usuarioLogado->id,
    //         'NOME' => $usuarioLogado->nome,
    //         'LOCAL' => $usuarioLogado->local->local,
    //         'SETOR' => $usuarioLogado->local->setor->setor,
    //         'ENTIDADE' => $usuarioLogado->local->setor->entidade->entidade,
    //         'IP_ORIGEM' => $ipOrigem,
    //         'ACAO' => $acao,
    //     ];

    //     //first parameter passed to Monolog\Logger sets the logging channel name
    //     $municipeLog = new Logger($tituloLog);
    //     $municipeLog->pushHandler(new StreamHandler(storage_path('logs/user-log/' . $usuarioLogado->id . '.log')), Logger::INFO);
    //     $municipeLog->info('log', $log);
    // }

    // static function logVisualizacaoIndividuo($usuarioLogado,$acao, $tituloLog, $individuo)
    // {
    //     $log = [
    //         'USUARIO' => $usuarioLogado->id,
    //         'NOME' => $usuarioLogado->nome,
    //         'LOCAL' => $usuarioLogado->local->local,
    //         'SETOR' => $usuarioLogado->local->setor->setor,
    //         'ENTIDADE' => $usuarioLogado->local->setor->entidade->entidade,
    //         'ACAO' => $acao
    //     ];

    //     //first parameter passed to Monolog\Logger sets the logging channel name
    //     $municipeLog = new Logger($tituloLog);
    //     $municipeLog->pushHandler(new StreamHandler(storage_path('logs/access-log/' . $individuo->id . '.log')), Logger::INFO);
    //     $municipeLog->info('log', $log);
    // }
}
