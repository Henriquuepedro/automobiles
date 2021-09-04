@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
@php( $logout_url = $logout_url ? route($logout_url) : '' )
@php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
@php( $logout_url = $logout_url ? url($logout_url) : '' )
@php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 3 | Lockscreen</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition lockscreen">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
    <div class="lockscreen-logo">
        <h2 class="text-uppercase"><b>{{ $settings->company->fancy }}</b></h2>
    </div>

    <!-- User name -->
    <h3 class="lockscreen-name">Olá {{ \Illuminate\Support\Facades\Auth::user()->name }}</h3>

    <!-- /.lockscreen-item -->
    <div class="help-block text-center">
        Sua conta está temporariamente bloqueada por falta de pagamento. Faça o pagamento o mais breve possível para ter seu acesso liberado novamente.
    </div>
    <div class="help-block text-center mt-3">
        Caso seu pagamento já tenha sido autorizado, recarregue a página.
    </div>
    <div class="text-center mt-3">
        <a class="btn btn-primary col-md-4 btn-flat btn-sm" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-fw fa-power-off"></i> {{ __('adminlte::adminlte.log_out') }}
        </a>
        <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
            @if(config('adminlte.logout_method'))
                {{ method_field(config('adminlte.logout_method')) }}
            @endif
            {{ csrf_field() }}
        </form>
    </div>
    <div class="lockscreen-footer text-center mt-3">
        Copyright &copy; 2020-{{ date('Y') }} <b><a href="http://adminlte.io" class="text-black">{{ $settings->system->name }}</a></b><br>
        All rights reserved.
    </div>
</div>
<!-- /.center -->

<!-- jQuery -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
