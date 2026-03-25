@extends('layout')

@section('conteudo')

<div class="row mt-3">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-code mr-2"></i>
                    Gerador de XML — Lista de Classificação
                </h3>
            </div>

            <div class="card-body">

                {{-- Mensagem de erro --}}
                @if ($errors->has('erro'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $errors->first('erro') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Erros de validação --}}
                @if ($errors->any() && !$errors->has('erro'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle mr-1"></i>Atenção:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                <p class="text-muted mb-4">
                    Envie o <strong>XML base</strong> (modelo de estrutura) e a <strong>planilha</strong> com os dados.
                    O sistema irá gerar o XML preenchido automaticamente para download.
                </p>

                <form action="{{ route('conversor.processar') }}" method="POST" enctype="multipart/form-data" id="formConversor">
                    @csrf

                    <div class="row">
                        {{-- Upload XML Base --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="xml_base">
                                    <i class="fas fa-file-code text-primary mr-1"></i>
                                    XML Base (modelo de estrutura)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input
                                            type="file"
                                            class="custom-file-input @error('xml_base') is-invalid @enderror"
                                            id="xml_base"
                                            name="xml_base"
                                            accept=".xml"
                                        >
                                        <label class="custom-file-label" for="xml_base" id="label_xml_base">
                                            Selecionar arquivo .xml
                                        </label>
                                    </div>
                                </div>
                                @error('xml_base')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="text-muted">Arquivo XML com a estrutura/modelo a ser seguido.</small>
                            </div>
                        </div>

                        {{-- Upload Planilha --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="planilha">
                                    <i class="fas fa-file-excel text-success mr-1"></i>
                                    Planilha com os dados
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input
                                            type="file"
                                            class="custom-file-input @error('planilha') is-invalid @enderror"
                                            id="planilha"
                                            name="planilha"
                                            accept=".xlsx,.xls,.ods"
                                        >
                                        <label class="custom-file-label" for="planilha" id="label_planilha">
                                            Selecionar arquivo .xlsx / .xls / .ods
                                        </label>
                                    </div>
                                </div>
                                @error('planilha')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <small class="text-muted">
                                    A <strong>primeira linha</strong> deve ser o cabeçalho (será ignorada).
                                    Os dados começam na linha 2.
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Tabela de mapeamento --}}
                    <div class="card card-secondary card-outline mt-3 mb-4">
                        <div class="card-header p-2">
                            <a data-toggle="collapse" href="#tabelaMapeamento" class="text-dark">
                                <i class="fas fa-table mr-1"></i>
                                <strong>Ver mapeamento esperado da planilha</strong>
                                <i class="fas fa-chevron-down float-right mt-1"></i>
                            </a>
                        </div>
                        <div class="collapse" id="tabelaMapeamento">
                            <div class="card-body p-0">
                                <table class="table table-sm table-bordered table-striped mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Coluna</th>
                                            <th>Campo XML</th>
                                            <th>Observação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td><strong>A</strong></td><td>AnoExercicio</td><td>Ex: 2025</td></tr>
                                        <tr><td><strong>B</strong></td><td>TipoDocumento</td><td>Ex: Lista de Classificação</td></tr>
                                        <tr><td><strong>C</strong></td><td>Entidade</td><td>Código da entidade</td></tr>
                                        <tr><td><strong>D</strong></td><td>Município</td><td>Código do município</td></tr>
                                        <tr><td><strong>F</strong></td><td>TipoProcessoSelecao</td><td>Ex: 3</td></tr>
                                        <tr><td><strong>G</strong></td><td>numeroProcessoSelecao</td><td></td></tr>
                                        <tr><td><strong>H</strong></td><td>anoProcessoSelecao</td><td></td></tr>
                                        <tr><td><strong>I</strong></td><td>CodigoEntidadePrevista</td><td></td></tr>
                                        <tr><td><strong>J</strong></td><td>CodigoMunicipioEntidadePrevista</td><td></td></tr>
                                        <tr><td><strong>K</strong></td><td>codigoFuncao</td><td></td></tr>
                                        <tr><td><strong>L</strong></td><td>dataPublicacaoListaClassificacao</td><td>Formato: dd/mm/aaaa ou data Excel</td></tr>
                                        <tr><td><strong>M</strong></td><td>dataAtoHomologacaoConcurso</td><td>Formato: dd/mm/aaaa ou data Excel</td></tr>
                                        <tr><td><strong>N</strong></td><td>dataValidadeInicial</td><td>Formato: dd/mm/aaaa ou data Excel</td></tr>
                                        <tr><td><strong>O</strong></td><td>dataPublicacaoHomologacao</td><td>Formato: dd/mm/aaaa (opcional)</td></tr>
                                        <tr><td><strong>Q</strong></td><td>CPF Responsável Homologação</td><td>Apenas números</td></tr>
                                        <tr><td><strong>R</strong></td><td>codigoCargoResponsavelHomologacao</td><td></td></tr>
                                        <tr class="table-info"><td><strong>T</strong></td><td>CPF Classificado</td><td>Repetir por linha</td></tr>
                                        <tr class="table-info"><td><strong>U</strong></td><td>Nome Classificado</td><td>Repetir por linha</td></tr>
                                        <tr class="table-info"><td><strong>V</strong></td><td>Ordem de Classificação</td><td>Repetir por linha</td></tr>
                                    </tbody>
                                </table>
                                <p class="p-2 mb-0 text-muted small">
                                    <i class="fas fa-info-circle"></i>
                                    As colunas em azul (<strong>T, U, V</strong>) se repetem para cada classificado.
                                    As demais colunas utilizam os valores da primeira linha de dados.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" id="btnGerar">
                                <i class="fas fa-cogs mr-1"></i>
                                Gerar XML
                            </button>
                            <button type="reset" class="btn btn-secondary ml-2" onclick="resetLabels()">
                                <i class="fas fa-undo mr-1"></i>
                                Limpar
                            </button>
                        </div>
                    </div>

                </form>

            </div>{{-- /.card-body --}}
        </div>{{-- /.card --}}
    </div>
</div>

<script>
    document.getElementById('xml_base').addEventListener('change', function () {
        var label = document.getElementById('label_xml_base');
        label.textContent = this.files.length ? this.files[0].name : 'Selecionar arquivo .xml';
    });

    document.getElementById('planilha').addEventListener('change', function () {
        var label = document.getElementById('label_planilha');
        label.textContent = this.files.length ? this.files[0].name : 'Selecionar arquivo .xlsx / .xls / .ods';
    });

    function resetLabels() {
        document.getElementById('label_xml_base').textContent = 'Selecionar arquivo .xml';
        document.getElementById('label_planilha').textContent  = 'Selecionar arquivo .xlsx / .xls / .ods';
    }

    document.getElementById('formConversor').addEventListener('submit', function () {
        var btn = document.getElementById('btnGerar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Gerando XML...';
    });
</script>

@endsection
