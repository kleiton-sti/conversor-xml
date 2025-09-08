@extends('layout')


@section('conteudo')
<div class="layout-alerta">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if (Session::has('flashMsg'))
                <div class="alert alert-info">
                    <p>{{ Session::get('flashMsg') }}</p>
                </div>
                @endif
                @if($errors->any())
                <div class="alert alert-danger">
                    {{$errors->first()}}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                Pesquisa de Setores
            </h3>
        </div>
        <!-- /.card-heading -->
        <div class="card-body">
            {{html()->form('POST',route('setores.search') )->open()}}
            @csrf
            <!-- row -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        {{html()->label('Setor:', 'setor')->class(['form-label'])->id('form-label-setor')}}
                        {{html()->text('setor')->class(['form-control'])->id('setor')->name('setor')->placeholder('Insira o nome do Setor')}}
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-group">
                        {{html()->label('Unidade:', 'unidade')->class(['form-label'])->id('form-label-unidade')}}
                        {{html()->text('unidade')->class(['form-control'])->id('unidade')->name('unidade')->placeholder('Insira o nome da unidade')}}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {{html()->label('Ativo?', 'ativo')->class(['form-label'])->id('form-label-ativo')}}
                        {{html()->select('ativo')->class(['form-control'])->id('ativo')->name('ativo')->options([
                            ''=>'SELECIONE',
                            '1'=>'SIM',
                            '0'=>'NAO'
                            ])}}
                    </div>
                </div>
            </div>
            <button class="btn btn-primary layout-botao" type="submit">Pesquisar</button>
            {{ html()->form()->close()}}
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    @if(session()->has('filtrosPesquisa'))
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <p>Filtros utilizados: {{session('filtrosPesquisa')}} </p>
            </div>
        </div>
    </div>
    @endif
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        Resultados da Pesquisa
                    </h3>
                </div>
                <!-- /.panel-heading -->
                <div class="card-body">
                    <div class="table-responsive">

                        <table id="tabelaRegistrosPesquisa" class="table table-bordered table-striped dataTable table-hover dtr-inline" aria-describedby="example1_info">
                            <thead>
                                <tr>
                                    <th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Entidades: ative para utilizar ordenação da coluna">Setor</th>
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Ações: ative para utilizar ordenação da coluna">Unidade</th>
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Ações: ative para utilizar ordenação da coluna">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                @foreach($setores as $s)
                                <tr>
                                    <td class="align-middle lista-entidades__nome">{{$s->setor}}</td>
                                    <td class="align-middle lista-entidades__nome">{{$s->unidade->entidade->entidade}}->{{$s->unidade->unidade}}</td>
                                    <td>
                                        @if($s->trashed())
                                        <a data-toggle="modal" data-target="#modalConfirmacao" data-url="{{ route('setores.restore', $s->id) }}" class="btn btn-warning lista-entidades__item--editar" href="{{ route('setores.restore', $s->id) }}">Restaurar</a>
                                        @else
                                        <a class="btn btn-info lista-entidades__item--editar" href="{{route('setores.edit', $s->id)}}">Editar</a>
                                        <a data-toggle="modal" data-target="#modalConfirmacao" data-url="{{ route('setores.destroy', $s->id) }}"
                                            class="btn btn-danger lista-entidades__item--remover" href="{{route('setores.destroy', $s->id)}}">Remover</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th rowspan="1" colspan="1">Setor</th>
                                    <th rowspan="1" colspan="1">Unidade</th>
                                    <th rowspan="1" colspan="1">Ações</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-6 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->






@endsection