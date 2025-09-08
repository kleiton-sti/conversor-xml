@extends('layout')

@section('conteudo')

<div class="card card-primary">
    <div class="card-header">
        Cadastro de Entidade
    </div>
    {{html()->modelForm($entidade)->open()}}
    @csrf
    <div class="card-body">
        <div class="form-group">
            <div class="row">
                <div class="col-lg-4">
                    {{html()->label('Entidade:', 'entidade')->class(['form-label'])->id('form-label-entidade')}}
                    {{html()->text('entidade')->class(['form-control'])->id('entidade')->name('entidade')->placeholder('Insira o nome da entidade')}}
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
        </div>
    </div>


    <!-- /.card-body -->

    <div class="card-footer">
        {{html()->button('Salvar', 'submit')->class('btn btn-primary')}}
    </div>
    {{ html()->closeModelForm()}}
</div>








@endsection