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
                                        <th>Empresa</th>
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
                                            <td>{{ $history->company }}</td>
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

    <div class="modal fade" id="viewHistory" tabindex="-1" role="dialog" aria-labelledby="viewHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title" id="newUserModalLabel">Visualizar Pagamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Forma de Pagamento</label>
                            <input type="text" class="form-control" name="type_payment" value="" disabled />
                        </div>
                        <div class="form-group col-md-3">
                            <label>Plano</label>
                            <input type="text" class="form-control" name="plan" value="" disabled />
                        </div>
                        <div class="form-group col-md-3">
                            <label>Data da Solicitação</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                </div>
                                <input type="text" class="form-control" name="date_requested" value="" disabled />
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Valor</label>
                            <input type="text" class="form-control" name="amount" value="" disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label>Usuário Solicitante</label>
                            <input type="text" class="form-control" name="user" value="" disabled />
                        </div>
                        <div class="form-group col-md-7">
                            <label>Empresa</label>
                            <input type="text" class="form-control" name="company" value="" disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Situação Atual</label>
                            <input type="text" class="form-control" name="status" value="" disabled />
                        </div>
                        <div class="form-group col-md-8">
                            <label>Situação Atual Detalhada</label>
                            <input type="text" class="form-control" name="status_detail" value="" disabled />
                        </div>
                    </div>
                    <div class="row justify-content-center flex-wrap" type-payment="pix">
                        <img width='250px' src=''/>
                        <br/>
                        <div class="d-flex justify-content-center col-md-12">
                            <div class="form-group col-md-4">
                                <label class="text-center col-md-12">Efetue o pagamento até</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" name="pix_date_of_expiration" value="" disabled />
                                </div>
                            </div>
                        </div>
                        <div class='input-group'>
                            <input type='text' class='form-control' name="pix_copy_paste" value='' readonly>
                            <span class='input-group-btn'>
                                <button type='button' class='btn btn-primary btn-flat copy-input' style='height: 38px'>
                                    <i class='fas fa-copy'></i>
                                </button>
                            </span>
                        </div>
                        <br/>
                        <span class='status_copy'></span>
                    </div>
                    <div class="row" type-payment="credit_card">
                        <div class="form-group col-md-4">
                            <label>Parcelas</label>
                            <input type="text" class="form-control" name="card_installments" value="" disabled />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Bandeira</label>
                            <input type="text" class="form-control" name="card_payment_method_id" value="" disabled />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Valor Total Pago</label>
                            <input type="text" class="form-control" name="card_client_amount" value="" disabled />
                        </div>
                    </div>
                    <div class="row" type-payment="billet">
                        <div class="form-group col-md-12 d-flex justify-content-center flex-wrap">
                            <h3 class="mb-0 col-md-12 text-center">Boleto</h3>
                            <br/>
                            <a href="" class="billet_link_billet btn btn-primary col-md-9 btn-lg" target="_blank" >Visualizar PDF</a>
                        </div>
                        <div class="form-group col-md-12 d-flex justify-content-center flex-wrap">
                            <label class="col-md-12 text-center">Chave Boleto</label>
                            <div class='input-group d-flex justify-content-center'>
                                <input type='text' class='form-control col-md-6' name="billet_barcode" value='' readonly>
                                <span class='input-group-btn'>
                                <button type='button' class='btn btn-primary btn-flat copy-input' style='height: 38px'>
                                    <i class='fas fa-copy'></i>
                                </button>
                            </span>
                            </div>
                            <br/>
                            <span class='status_copy'></span>
                        </div>
                        <div class="d-flex justify-content-center col-md-12">
                            <div class="form-group col-md-4">
                                <label class="text-center col-md-12">Efetue o pagamento até</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" name="billet_date_of_expiration" value="" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row histories-division">
                        <div class="col-md-12"><hr/></div>
                    </div>
                    <div class="row histories-title">
                        <div class="col-md-12 text-center">
                            <h4>Histórico da Transação</h4>
                        </div>
                    </div>
                    <div class="histories row mt-3">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Fechar</button>
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
            const paymentId = $(this).attr('payment-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: `${window.location.origin}/admin/ajax/planos/consultar-pagamento/${paymentId}`,
                dataType: 'json',
                success: response => {
                    console.log(response);
                    const modal = $('#viewHistory');

                    modal.modal('show');
                    modal.find('.modal-body .histories-title, .modal-body .histories-division').hide();

                    let content = '';
                    if (response.history.length) {
                        content += '<ul class="timeline">';
                        $(response.history).each(function (key, value) {
                            content += `<li class="event" data-date="${value.status_date}">
                                            <h3>${value.status}</h3>
                                            <p>${value.status_detail}</p>
                                        </li>`;
                        });
                        content += '</ul>';
                        modal.find('.modal-body .histories-title, .modal-body .histories-division').show();
                    }
                    modal.find('.modal-body .histories').empty().append(content);


                    modal.find('[name="type_payment"]').val(response.payment.type_payment);
                    modal.find('[name="plan"]').val(response.payment.name_plan);
                    modal.find('[name="date_requested"]').val(response.payment.created_at);
                    modal.find('[name="amount"]').val(response.payment.gross_amount);
                    modal.find('[name="user"]').val(response.payment.user_created);
                    modal.find('[name="status"]').val(response.payment.status);
                    modal.find('[name="status_detail"]').val(response.payment.status_detail);
                    modal.find('[name="company"]').val(response.payment.company);
                    modal.find('[type-payment]').hide();

                    const contentModal = modal.find(`[type-payment="${response.payment.type_payment}"]`);

                    contentModal.find('.status_copy').empty();

                    if (response.payment.waiting_payment) {
                        modal.find(`[type-payment="${response.payment.type_payment}"]`).show();
                    } else {
                        modal.find(`[type-payment="${response.payment.type_payment}"]`).hide();
                    }

                    switch (response.payment.type_payment) {
                        case 'pix':
                            contentModal.find('img').prop('src', `data:image/jpeg;base64,${response.payment.base64_key_pix}`);
                            contentModal.find('[name="pix_copy_paste"]').val(response.payment.key_pix);
                            contentModal.find('[name="pix_date_of_expiration"]').val(response.payment.date_of_expiration);
                            break;
                        case 'billet':
                                contentModal.find('.billet_link_billet').prop('href', response.payment.link_billet);
                                contentModal.find('[name="billet_barcode"]').val(response.payment.barcode_billet);
                                contentModal.find('[name="billet_date_of_expiration"]').val(response.payment.date_of_expiration);
                            break;
                        case 'credit_card':
                            contentModal.find('[name="card_installments"]').val(response.payment.installments);
                            contentModal.find('[name="card_payment_method_id"]').val(response.payment.payment_method_id);
                            contentModal.find('[name="card_client_amount"]').val(response.payment.client_amount);
                            break;
                    }

                }, error: e => {
                    console.log(e);
                }
            });
        });

        $(document).on('click', '.copy-input', function() {
            // Seleciona o conteúdo do input
            $(this).closest('.input-group').find('input').select();
            // Copia o conteudo selecionado
            const copy = document.execCommand('copy');
            if (copy) {
                $('.status_copy').addClass('text-success font-weight-bold').html("Código copiado com sucesso!")
            } else {
                $('.status_copy').addClass('text-success font-weight-bold').html("Não foi possível copiar o conteúdo!")
            }
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

        .timeline::before {
            display: none;
        }

        .timeline {
            border-left: 3px solid #727cf5;
            border-bottom-right-radius: 4px;
            border-top-right-radius: 4px;
            background: rgba(114, 124, 245, 0.09);
            margin: 0 auto;
            letter-spacing: 0.2px;
            position: relative;
            line-height: 1.4em;
            font-size: 1.03em;
            padding: 50px;
            list-style: none;
            text-align: left;
            max-width: 40%;
        }

        @media (max-width: 767px) {
            .timeline {
                max-width: 98%;
                padding: 25px;
            }
        }

        .timeline h1 {
            font-weight: 300;
            font-size: 1.4em;
        }

        .timeline h2,
        .timeline h3 {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .timeline .event {
            border-bottom: 1px dashed #e8ebf1;
            padding-bottom: 25px;
            margin-bottom: 25px;
            position: relative;
        }

        @media (max-width: 767px) {
            .timeline .event {
                padding-top: 30px;
            }
        }

        .timeline .event:last-of-type {
            padding-bottom: 0;
            margin-bottom: 0;
            border: none;
        }

        .timeline .event:before,
        .timeline .event:after {
            position: absolute;
            display: block;
            top: 0;
        }

        .timeline .event:before {
            left: -207px;
            content: attr(data-date);
            text-align: right;
            font-weight: bold;
            font-size: 1em;
            min-width: 120px;
        }

        @media (max-width: 767px) {
            .timeline .event:before {
                left: 0px;
                text-align: left;
            }
        }

        .timeline .event:after {
            -webkit-box-shadow: 0 0 0 3px #727cf5;
            box-shadow: 0 0 0 3px #727cf5;
            left: -55.8px;
            background: #fff;
            border-radius: 50%;
            height: 9px;
            width: 9px;
            content: "";
            top: 5px;
        }

        @media (max-width: 767px) {
            .timeline .event:after {
                left: -31.8px;
            }
        }

        .rtl .timeline {
            border-left: 0;
            text-align: right;
            border-bottom-right-radius: 0;
            border-top-right-radius: 0;
            border-bottom-left-radius: 4px;
            border-top-left-radius: 4px;
            border-right: 3px solid #727cf5;
        }

        .rtl .timeline .event::before {
            left: 0;
            right: -170px;
        }

        .rtl .timeline .event::after {
            left: 0;
            right: -55.8px;
        }
    </style>
@endsection
