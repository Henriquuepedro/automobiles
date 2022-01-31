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
    <div class="container py-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pagamentos Solicitados</h3><br/>
                        <small>Solicitações já realizadas</small>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Plano</th>
                                        <th>Tipo do Pagamento</th>
                                        <th>Valor do Plano</th>
                                        <th>Valor Pago</th>
                                        <th>Solicitado Por</th>
                                        <th>Solicitado Em</th>
                                        <th>Situação</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($histories as $history)
                                        <tr>
                                            <td>{{ $history->plan }}</td>
                                            <td>{{ \App\Http\Controllers\Controller::getTypePaymentMP($history->type_payment) }}</td>
                                            <td>R$ {{ number_format($history->gross_amount, 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($history->client_amount, 2, ',', '.') }}</td>
                                            <td>{{ $history->user }}</td>
                                            <td>{{ date('d/m/Y H:i', strtotime($history->created_at)) }}</td>
                                            <td>{{ \App\Http\Controllers\Controller::getStatusMP($history->status) }}</td>
                                            <td><button type="button" class="btn btn-flat btn-sm btn-primary" payment-id="{{ $history->id }}"><i class="fa fa-eye"></i></button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
    <script>
        $(document).on('click', '[payment-id]', function(){
            Swal.fire({
                icon: 'warning',
                title: 'Em construção',
                html: '<i class="fa fa-spin fa-spinner"></i>'
            });
        });
    </script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}"/>
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
