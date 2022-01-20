{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Planos', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Planos')

@section('content')
    <div class="container py-3">
        <header>
            <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
                <h1 class="display-4 fw-normal">Planos Mensais</h1>
                <p class="fs-5 text-muted">Quickly build an effective pricing table for your potential customers with this Bootstrap example. It’s built with default Bootstrap components and utilities with little customization.</p>
            </div>
        </header>

        <main>
            <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm border-secondary">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Básico</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">R$ 45,90<small class="text-muted fw-light">/mês</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>10 users included</li>
                                <li>2 GB of storage</li>
                                <li>Email support</li>
                                <li>Help center access</li>
                            </ul>
                            <a href="{{ route('admin.plan.confirm', ['id' => $plans['basic']]) }}" class="w-100 btn btn-lg btn-outline-primary">Assinar</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm border-primary">
                        <div class="card-header py-3 text-white bg-primary border-primary">
                            <h4 class="my-0 fw-normal">Intermediário</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">R$ 59,90<small class="text-muted fw-light">/mês</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>20 users included</li>
                                <li>10 GB of storage</li>
                                <li>Priority email support</li>
                                <li>Help center access</li>
                            </ul>
                            <a href="{{ route('admin.plan.confirm', ['id' => $plans['intermediary']]) }}" class="w-100 btn btn-lg btn-primary">Assinar</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm border-secondary">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Avançado</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">R$ 99,90<small class="text-muted fw-light">/mês</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>30 users included</li>
                                <li>15 GB of storage</li>
                                <li>Phone and email support</li>
                                <li>Help center access</li>
                            </ul>
                            <a href="{{ route('admin.plan.confirm', ['id' => $plans['advanced']]) }}" class="w-100 btn btn-lg btn-outline-primary">Assinar</a>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="display-6 text-center mb-4">Compare plans</h2>

            <div class="table-responsive">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th style="width: 34%;"></th>
                            <th style="width: 22%;">Báscio</th>
                            <th style="width: 22%;">Intermediário</th>
                            <th style="width: 22%;">Avançado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" class="text-start">Public</th>
                            <td><i class="fa fa-check"></i></td>
                            <td><i class="fa fa-check"></i></td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Private</th>
                            <td></td>
                            <td><i class="fa fa-check"></i></td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Permissions</th>
                            <td><i class="fa fa-check"></i></td>
                            <td><i class="fa fa-check"></i></td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Sharing</th>
                            <td></td>
                            <td><i class="fa fa-check"></i></td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Unlimited members</th>
                            <td></td>
                            <td><i class="fa fa-check"></i></td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-start">Extra security</th>
                            <td></td>
                            <td></td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                    </tbody>
                </table>
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
