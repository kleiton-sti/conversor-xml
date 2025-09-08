<?php

use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConversorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home');
});

//ROTAS LOGIN
Route::get('/login',[LoginController::class,'index'])->name('login');
Route::post('/login',[LoginController::class,'login'])->name('post.login');
Route::get('/logout',[LoginController::class,'logout'])->name('logout');



//ROTAS ADMINISTRATIVAS
Route::get('/home',[HomeController::class,'index'])->name('home')->middleware('auth');

Route::get('/usuarios',[UsuarioController::class,'index'])->name('usuarios.index')->middleware('auth')->middleware('can:consultar.usuario');
Route::post('/usuarios', [UsuarioController::class, 'search'])->name('usuarios.search')->middleware('auth')->middleware('can:consultar.usuario');
Route::get('/usuarios/create',[UsuarioController::class,'create'])->name('usuarios.create')->middleware('auth')->middleware('can:gerenciar.usuario');
Route::post('/usuarios/create',[UsuarioController::class,'store'])->name('usuarios.store')->middleware('auth')->middleware('can:gerenciar.usuario');
Route::get('/usuarios/{user}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit')->middleware('auth')->middleware('can:gerenciar.usuario');
Route::post('/usuarios/{user}/edit', [UsuarioController::class, 'update'])->name('usuarios.update')->middleware('auth')->middleware('can:gerenciar.usuario');
Route::get('/usuarios/{user}/destroy', [UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('auth')->middleware('can:gerenciar.usuario');
Route::get('/usuarios/{id}/restore', [UsuarioController::class, 'restore'])->name('usuarios.restore')->middleware('auth')->middleware('can:gerenciar.usuario');

Route::get('/entidades',[EntidadeController::class,'index'])->name('entidades.index')->middleware('auth')->middleware('can:pesquisar_entidade');
Route::post('/entidades', [EntidadeController::class, 'search'])->name('entidades.search')->middleware('auth')->middleware('can:pesquisar_entidade');
Route::get('/entidades/create',[EntidadeController::class,'create'])->name('entidades.create')->middleware('auth')->middleware('can:cadastrar_entidade');
Route::post('/entidades/create',[EntidadeController::class,'store'])->name('entidades.store')->middleware('auth')->middleware('can:cadastrar_entidade');
Route::get('/entidades/{entidade}/edit', [EntidadeController::class, 'edit'])->name('entidades.edit')->middleware('auth')->middleware('can:editar_entidade');
Route::post('/entidades/{entidade}/edit', [EntidadeController::class, 'update'])->name('entidades.update')->middleware('auth')->middleware('can:editar_entidade');
Route::get('/entidades/{entidade}/destroy', [EntidadeController::class, 'destroy'])->name('entidades.destroy')->middleware('auth')->middleware('can:inativar_entidade');
Route::get('/entidades/{id}/restore', [EntidadeController::class, 'restore'])->name('entidades.restore')->middleware('auth')->middleware('can:inativar_entidade');

Route::get('/unidades',[UnidadeController::class,'index'])->name('unidades.index')->middleware('auth')->middleware('can:pesquisar_unidade');
Route::get('/unidades',[UnidadeController::class,'index'])->name('unidades.index')->middleware('auth')->middleware('can:pesquisar_unidade');
Route::post('/unidades', [UnidadeController::class, 'search'])->name('unidades.search')->middleware('auth')->middleware('can:pesquisar_unidade');
Route::get('/unidades/create',[UnidadeController::class,'create'])->name('unidades.create')->middleware('auth')->middleware('can:cadastrar_unidade');
Route::post('/unidades/create',[UnidadeController::class,'store'])->name('unidades.store')->middleware('auth')->middleware('can:cadastrar_unidade');
Route::get('/unidades/{unidade}/edit', [UnidadeController::class, 'edit'])->name('unidades.edit')->middleware('auth')->middleware('can:editar_unidade');
Route::post('/unidades/{unidade}/edit', [UnidadeController::class, 'update'])->name('unidades.update')->middleware('auth')->middleware('can:editar_unidade');
Route::get('/unidades/{unidade}/destroy', [UnidadeController::class, 'destroy'])->name('unidades.destroy')->middleware('auth')->middleware('can:inativar_unidade');
Route::get('/unidades/{id}/restore', [UnidadeController::class, 'restore'])->name('unidades.restore')->middleware('auth')->middleware('can:inativar_unidade');

Route::get('/setores',[SetorController::class,'index'])->name('setores.index')->middleware('auth')->middleware('can:pesquisar.setor');
Route::post('/setores', [SetorController::class, 'search'])->name('setores.search')->middleware('auth')->middleware('can:pesquisar.setor');
Route::get('/setores/create',[SetorController::class,'create'])->name('setores.create')->middleware('auth')->middleware('can:cadastrar.setor');
Route::post('/setores/create',[SetorController::class,'store'])->name('setores.store')->middleware('auth')->middleware('can:cadastrar.setor');
Route::get('/setores/{setor}/edit', [SetorController::class, 'edit'])->name('setores.edit')->middleware('auth')->middleware('can:editar.setor');
Route::post('/setores/{setor}/edit', [SetorController::class, 'update'])->name('setores.update')->middleware('auth')->middleware('can:editar.setor');
Route::get('/setores/{setor}/destroy', [SetorController::class, 'destroy'])->name('setores.destroy')->middleware('auth')->middleware('can:inativar.setor');
Route::get('/setores/{id}/restore', [SetorController::class, 'restore'])->name('setores.restore')->middleware('auth')->middleware('can:inativar.setor');

Route::get('/arquivo', [ConversorController::class,'addClassificados'])->name('arquivo');



