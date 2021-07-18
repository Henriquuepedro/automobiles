<div class="tab-pane" id="users">
    <div class="row">
        <div class="col-md-12 form-group d-flex justify-content-end">
            <button class="btn btn-primary col-md-4" data-toggle="modal" data-target="#newUser" id="registerNewUser">Novo Usuário</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 form-group">
            <table id="tableUsers"  class="table table-bordered table-striped col-md-12">
                <thead>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Ações</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@section('js_form_user')
    <script type="text/javascript" src="{{ asset('admin/dist/js/pages/users/users.js') }}"></script>
@endsection
