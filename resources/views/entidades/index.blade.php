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
                Pesquisa de Entidades
            </h3>
        </div>
        <!-- /.card-heading -->
        <div class="card-body">
            {{html()->form('POST',route('entidades.search'))->open()}}
            @csrf
            <!-- row -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        {{html()->label('Entidade:', 'entidade')->class(['form-label'])->id('form-label-entidade')}}
                        {{html()->text('entidade')->class(['form-control'])->id('entidade')->name('entidade')->placeholder('Insira o nome da entidade')}}
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
                                    <th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Entidades: ative para utilizar ordenação da coluna">Entidades</th>
                                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Ações: ative para utilizar ordenação da coluna">Ações</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($entidades as $e)
                                <tr>
                                    <td class="align-middle lista-entidades__nome">{{$e->entidade}}</td>
                                    <td>
                                        @if($e->trashed())
                                        <a data-toggle="modal" data-target="#modalConfirmacao" data-url="{{ route('entidades.restore', $e->id) }}" class="btn btn-warning lista-entidades__item--editar" href="{{ route('entidades.restore', $e->id) }}">Restaurar</a>
                                        @else
                                        <a class="btn btn-info lista-entidades__item--editar" href="{{route('entidades.edit', $e->id)}}">Editar</a>
                                        <a data-toggle="modal" data-target="#modalConfirmacao" data-url="{{ route('entidades.destroy', $e->id) }}"
                                        class="btn btn-danger lista-entidades__item--remover" href="{{route('entidades.destroy', $e->id)}}">Remover</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th rowspan="1" colspan="1">Entidades</th>
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