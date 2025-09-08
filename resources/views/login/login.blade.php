@extends('layout-login')

@section('pagina-login')

<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <h1> <b>Laravel</b>Base</h1>
    </div>
    <div class="text-center">
      <p class="login-box-msg"><i>{{$mensagemInspiradora}}</i></p>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Insira suas credenciais de acesso</p>
      {{html()->form('POST')->route('post.login')->open()}}
      @csrf
      <div class="input-group mb-3">
        {{html()->text($name = 'registro')->class(['form-control'])->placeholder('Registro')}}
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-envelope"></span>
          </div>
        </div>
      </div>
      <div class="mensagens-erro">
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

      <div class="input-group mb-3">
        {{html()->password('password')->class(['form-control'])->placeholder('Senha')}}

        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-lock"></span>
          </div>
        </div>
      </div>
      <div class="mensagens-erro">
      @if($errors->credenciaisInvalidas->any())
          <small class="text-danger">
            {{$errors->credenciaisInvalidas->first()}}
          </small>
      @endif
        @if($errors->has('password'))
        @foreach($errors->get('password') as $erro)
        <div class="">
          <small class="text-danger">
            {{$erro}}
          </small>
        </div>
        @endforeach
        @endif
      </div>
      <div class="row">

        <!-- /.col -->
        <div class="col-4">
          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </div>
        <!-- /.col -->
      </div>
      {{html()->form()->close()}}
      

      <!-- <div class="social-auth-links text-center mt-2 mb-3">
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
        </a>
      </div> -->
      <!-- /.social-auth-links -->


    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->


@endsection