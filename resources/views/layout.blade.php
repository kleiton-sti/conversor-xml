<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel - Base</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href={{asset("/css/adminlte.css")}}>

    <link rel="stylesheet" href="{{asset("/css/datatables-bs4/dataTables.bootstrap4.css")}}">



</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src={{asset("/img/brasao.png")}} alt="Brasão Prefeitura Caraguatatuba" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href={{route('home')}} class="nav-link">Início</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href={{route('logout')}} class="nav-link">Sair</a>
                </li>

            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Navbar Search -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>



                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href={{route('home')}} class="brand-link">
                <img src={{asset("/img/brasao.png")}} alt="brasão Prefeitura Caraguatatuba" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Laravel - Base</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <p><a href="" class="d-block">{{Auth::user()->nome}} - {{Auth::user()->registro}}</a></p>
                        <p><a href="" class="d-block">{{Auth::user()->setor->unidade->entidade->entidade}}</a></p>
                        <p><a href="" class="d-block">{{Auth::user()->setor->setor}}</a></p>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fa-solid fa-wrench"></i>
                                <p>
                                    Configurações
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-solid fa-building-columns nav-icon"></i>
                                        <p>
                                            Entidades
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('pesquisar_entidade')
                                        <li class="nav-item">
                                            <a href={{route("entidades.index")}} class="nav-link">
                                                <i class="far fa-solid fa-list nav-icon"></i>
                                                <p>Listar</p>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('cadastrar_entidade')
                                        <li class="nav-item">
                                            <a href={{route("entidades.create")}} class="nav-link">
                                                <i class="far fa-solid fa-plus nav-icon"></i>
                                                <p>Criar</p>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-solid fa-building nav-icon"></i>
                                        <p>
                                            Unidades
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('pesquisar_unidade')
                                        <li class="nav-item">
                                            <a href={{route("unidades.index")}} class="nav-link">
                                                <i class="far fa-solid fa-list nav-icon"></i>
                                                <p>Listar</p>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('cadastrar_unidade')
                                        <li class="nav-item">
                                            <a href={{route("unidades.create")}} class="nav-link">
                                                <i class="far fa-solid fa-plus nav-icon"></i>
                                                <p>Criar</p>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-solid fa-people-roof nav-icon"></i>
                                        <p>
                                            Setores
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('pesquisar.setor')
                                        <li class="nav-item">
                                            <a href={{route("setores.index")}} class="nav-link">
                                                <i class="far fa-solid fa-list nav-icon"></i>
                                                <p>Listar</p>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('cadastrar.setor')
                                        <li class="nav-item">
                                            <a href={{route("setores.create")}} class="nav-link">
                                                <i class="far fa-solid fa-plus nav-icon"></i>
                                                <p>Criar</p>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-solid fa-user-tie nav-icon"></i>
                                        <p>
                                            Usuários
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('consultar.usuario')
                                        <li class="nav-item">
                                            <a href={{route("usuarios.index")}} class="nav-link">
                                                <i class="far fa-solid fa-list nav-icon"></i>
                                                <p>Listar</p>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('gerenciar.usuario')
                                        <li class="nav-item">
                                            <a href={{route("usuarios.create")}} class="nav-link">
                                                <i class="far fa-solid fa-plus nav-icon"></i>
                                                <p>Criar</p>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                            </ul>
                        </li>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    @yield('conteudo')
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Desenvolvido por: Secretaria de Tecnologia da Informação.</strong>
            <div class="float-right d-none d-sm-inline-block">
                <b>Versão:</b> 1.0.0
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    @include('modal')

    <!-- jQuery -->
    <script src={{asset("/js/jquery/jquery.min.js")}}></script>
    <!-- jQuery UI 1.11.4 -->
    <script src={{asset("/js/jquery-ui/jquery-ui.min.js")}}></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src={{asset("/js/bootstrap/bootstrap.bundle.min.js")}}></script>
    <!-- AdminLTE App -->
    <script src={{asset("/js/adminlte/adminlte.js")}}></script>
    <!-- DataTables JavaScript -->
    <script src={{asset("/js/datatables/jquery.dataTables.min.js")}}></script>
    <script src={{asset("/js/datatables/dataTables.bootstrap4.min.js")}}></script>
    <script src={{asset("/js/dataTables.js")}}></script>
    <script src={{asset("/js/modal-confirmacao.js")}}></script>
</body>

</html>