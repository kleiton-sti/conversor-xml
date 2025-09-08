@extends('layout')

@section('conteudo')

<div class="container-fluid">


    <div class="card">
        <div class="card-body row">
            <div class="col-12 text-center d-flex align-items-center justify-content-center">
                <div class="">
                    <h2>Erro <strong>500</strong></h2>
                    <p class="lead mb-5">{{$exception->getMessage()?:'Server Error'}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->



@endsection