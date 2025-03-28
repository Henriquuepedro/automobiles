<div class="modal fade" id="updateUser" tabindex="-1" role="dialog" aria-labelledby="newUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card">
            <form action="{{ route('admin.ajax.user.update') }}" method="post" enctype="multipart/form-data" id="formUpdateUser">
                <div class="modal-header">
                    <h5 class="modal-title" id="newUserModalLabel">Atualizar Usuário</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Nome do Usuário</label>
                            <input type="text" class="form-control" name="name_user">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Endereço de Email</label>
                            <input type="email" class="form-control" name="email_user">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-8">
                            <label>Loja</label>
                            <select class="select2 form-control" multiple name="store_user[]">
                                @foreach ($dataStores as $store)
                                    <option value="{{ $store['id'] }}">{{ $store['store_fancy'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Permissão</label><br>
                            <label><input type="radio" name="permission" value="admin"> Admin</label>
                            <label class="ml-4"><input type="radio" name="permission" value="user"> Usuário</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12 alert alert-info">
                            <p class="cursor-pointer" data-toggle="collapse" data-target="#collapseUpdatePassword" aria-expanded="false" aria-controls="collapseUpdatePassword"><i class="fa fa-key"></i> Alterar senha do usuário</p>
                        </div>
                    </div>
                    <div class="row collapse" id="collapseUpdatePassword">
                        <div class="form-group col-md-6">
                            <label>Senha de Acesso</label>
                            <input type="password" class="form-control" name="password_user">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Confirme a Senha</label>
                            <input type="password" class="form-control" name="password_user_confirmation">
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary col-md-3"><i class="fa fa-save"></i> Atualizar</button>
                </div>
                <input type="hidden" name="user_id">
            </form>
            <div class="overlay dark d-none screen-user-edit">
                <i class="fas fa-3x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>
