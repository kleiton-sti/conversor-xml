@extends('layout')

@section('conteudo')

<div class="card card-primary">
    <div class="card-header">
        Cadastro de Unidade
    </div>
    {{html()->modelForm($unidade)->open()}}
    @csrf
    <div class="card-body">
        <div class="form-group">
            <div class="row">
                <div class="col-lg-4">
                    {{html()->label('Unidade:', 'unidade')->class(['form-label'])->id('form-label-unidade')}}
                    {{html()->text('unidade')->class(['form-control'])->id('unidade')->name('unidade')->placeholder('Insira o nome da unidade')->required()}}
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

                <div class="col-lg-4">
                    {{html()->label('Entidade:', 'entidade_id')->class(['form-label'])->id('form-label-entidade_id')}}
                    {{ html()->select('entidade_id', ['' => 'SELECIONE'] + $entidades->toArray())->required()->class(['form-control']) }}
                    @if($errors->has('entidade_id'))
                    @foreach($errors->get('entidade_id') as $erro)
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