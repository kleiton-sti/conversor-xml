@extends('layout')

@section('conteudo')

<div class="card card-primary">
    <div class="card-header">
        Cadastro de Usuário
    </div>
    {{html()->modelForm($usuario)->open()}}
    @csrf
    <div class="card-body">
        <div class="form-group">
            <div class="row">
                <div class="col-lg-4">
                    {{html()->label('Nome:', 'nome')->class(['form-label'])->id('form-label-nome')}}
                    {{html()->text('nome')->class(['form-control'])->id('nome')->name('nome')->placeholder('Insira o nome do usuário')->required()}}
                    @if($errors->has('nome'))
                    @foreach($errors->get('nome') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="col-lg-4">
                    {{html()->label('CPF:', 'cpf')->class(['form-label'])->id('form-label-cpf')}}
                    {{html()->text('cpf')->class(['form-control'])->id('cpf')->name('cpf')->placeholder('Insira o cpf do usuário')->required()}}
                    @if($errors->has('cpf'))
                    @foreach($errors->get('cpf') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="col-lg-4">
                    {{html()->label('Registro:', 'registro')->class(['form-label'])->id('form-label-registro')}}
                    {{html()->text('registro')->class(['form-control'])->id('registro')->name('registro')->placeholder('Insira o registro do usuário')->required()}}
                    @if($errors->has('registro'))
                    @foreach($errors->get('registro') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    {{html()->label('Email:', 'email')->class(['form-label'])->id('form-label-email')}}
                    {{html()->email('email')->class(['form-control'])->id('email')->name('email')->placeholder('Insira o email do usuário')->required()}}
                    @if($errors->has('nome'))
                    @foreach($errors->get('nome') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="col-lg-8">
                    {{html()->label('Entidade:', 'entidade')->class(['form-label'])->id('form-label-entidade')}}
                    {{ html()->select('entidade', ['' => 'SELECIONE'])->required()->class(['form-control']) }}
                    @if($errors->has('entidade'))
                    @foreach($errors->get('entidade') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    {{html()->label('Unidade:', 'unidade')->class(['form-label'])->id('form-label-unidade')}}
                    {{ html()->select('unidade', ['' => 'SELECIONE'])->required()->class(['form-control']) }}
                    @if($errors->has('unidade'))
                    @foreach($errors->get('unidade') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="col-lg-6">
                    {{html()->label('Setor:', 'setor_id')->class(['form-label'])->id('form-label-setor_id')}}
                    {{ html()->select('setor_id', ['' => 'SELECIONE'])->required()->class(['form-control']) }}
                    @if($errors->has('setor_id'))
                    @foreach($errors->get('setor_id') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    {{html()->label('Grupo:', 'grupo_id')->class(['form-label'])->id('form-label-grupo_id')}}
                    {{ html()->select('grupo_id', ['' => 'SELECIONE'])->required()->class(['form-control']) }}
                    @if($errors->has('grupo_id'))
                    @foreach($errors->get('grupo_id') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- /.card-body -->

    <div class="card-footer">
        {{html()->button('Salvar', 'submit')->class('btn btn-primary')}}
    </div>
    {{ html()->closeModelForm()}}
</div>








@endsection