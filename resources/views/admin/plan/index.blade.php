{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Planos', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Planos')

@section('content')
    <div class="container py-3">
        <header>
            <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
                <h1 class="display-4 fw-normal">Planos</h1>
                <p class="fs-5 text-muted"></p>
            </div>
        </header>

        <main>
            <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                @foreach($plans as $plan)
                <div class="col">
                    <div class="card mb-{{ 12 / count($plans) }} rounded-3 shadow-sm {{ $plan->primary ? 'border-primary' : 'border-secondary' }}">
                        <div class="card-header py-3 {{ $plan->primary ? 'text-white bg-primary border-primary' : '' }}">
                            <h4 class="my-0 fw-normal">{{ $plan->name }}</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">R$ {{ number_format(($plan->amount / $plan->qty_months), 2, ',', '.') }}<small class="text-muted fw-light">/mês</small></h1>
                            {!! $plan->description !!}
                            <a href="{{ route('admin.plan.confirm', ['type' => $plan->type, 'id' => $plan->code]) }}" class="w-100 btn btn-lg {{ $plan->primary ? 'btn-primary' : 'btn-outline-primary' }}">Assinar</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </main>
    </div>
@stop
@section('js')
    <script src="https://sdk.mercadopago.com/js/v2"></script>
@endsection
@section('css')
    <style>
        .shadow-sm .card-header:not(.bg-primary) {
            background-color: #dde;
            border: 1px solid #dde;
        }

        .pricing-card-title {
            font-size: 2.5rem !important;
            width: 100%;
            margin-bottom: 1rem;
        }

        ul.list-unstyled li {
            font-size: 1.2rem !important;
        }
    </style>
@endsection
