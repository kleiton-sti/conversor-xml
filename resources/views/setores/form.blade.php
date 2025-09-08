@extends('layout')

@section('conteudo')

<div class="card card-primary">
    <div class="card-header">
        Cadastro de Setor
    </div>
    {{html()->modelForm($setor)->open()}}
    @csrf
    <div class="card-body">
        <div class="form-group">
            <div class="row">
                <div class="col-lg-4">
                    {{html()->label('Setor:', 'setor')->class(['form-label'])->id('form-label-setor')}}
                    {{html()->text('setor')->class(['form-control'])->id('setor')->name('setor')->placeholder('Insira o nome do setor')->required()}}
                    @if($errors->has('setor'))
                    @foreach($errors->get('setor') as $erro)
                    <div class="">
                        <small class="text-danger">
                            {{$erro}}
                        </small>
                    </div>
                    @endforeach
                    @endif
                </div>

                <div class="col-lg-8">
                    {{html()->label('Unidade:', 'unidade_id')->class(['form-label'])->id('form-label-unidade_id')}}
                    {{ html()->select('unidade_id', ['' => 'SELECIONE'] + $unidades->toArray())->required()->class(['form-control']) }}
                    @if($errors->has('unidade_id'))
                    @foreach($errors->get('unidade_id') as $erro)
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