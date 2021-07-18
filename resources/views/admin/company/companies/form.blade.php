<div class="tab-pane active" id="company">
    <form action="{{ route('admin.company.update') }}" method="post" enctype="multipart/form-data" id="formCompany">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="company_fancy">Nome Fantasia</label>
                <input type="text" class="form-control" id="company_fancy" name="company_fancy" value="{{ old('company_fancy', $dataCompany->company_fancy) }}">
            </div>
            <div class="form-group col-md-6">
                <label for="company_name">Razão Social</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $dataCompany->company_name) }}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="inputName2">Tipo de Empresa</label>
                <label class="col-md-12"><input type="radio" name="type_company" id="type_pf" value="pf" {{ old('type_company', $dataCompany->type_company) == 'pf' ? 'checked' : '' }}> Pessoa Física</label>
                <label class="col-md-12"><input type="radio" name="type_company" id="type_pj" value="pj" {{ old('type_company', $dataCompany->type_company) == 'pj' ? 'checked' : '' }}> Pessoa Jurídica</label>
            </div>
            <div class="form-group col-md-4">
                <label for="document_primary">CNPJ</label>
                <input type="text" class="form-control" id="document_primary" name="document_primary" value="{{ old('document_primary', $dataCompany->company_document_primary) }}">
            </div>
            <div class="form-group col-md-4">
                <label for="document_secondary">IE</label>
                <input type="text" class="form-control" id="document_secondary" name="document_secondary" value="{{ old('document_secondary', $dataCompany->company_document_secondary) }}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="contact_mail">E-mail de Contato</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $dataCompany->contact_email) }}">
            </div>
            <div class="form-group col-md-4">
                <label for="inputSkills">Telefone Primário</label>
                <input type="text" class="form-control" id="primary_phone" name="primary_phone" value="{{ old('primary_phone', $dataCompany->contact_primary_phone) }}">
            </div>
            <div class="form-group col-md-4">
                <label for="inputSkills">Telefone Secundário</label>
                <input type="text" class="form-control" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $dataCompany->contact_secondary_phone) }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 border-top pt-3 text-right">
                <button type="submit" class="btn btn-success col-md-4"><i class="fa fa-save"></i> Salvar Alterações</button>
            </div>
        </div>
    </form>
</div>
@section('js_form_company')
    <script type="text/javascript" src="{{ asset('admin/dist/js/pages/companies/companies.js') }}"></script>
@endsection
