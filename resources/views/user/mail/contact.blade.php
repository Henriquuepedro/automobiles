@component('mail::message')
<table style="width: 100%">
    <tr>
        <td>Nome</td>
        <td>{{ $form->name }}</td>
    </tr>
    <tr>
        <td>E-mail</td>
        <td>{{ $form->email }}</td>
    </tr>
    <tr>
        <td>Telefone</td>
        <td>{{ $form->phone }}</td>
    </tr>
    <tr>
        <td>Mensagem</td>
        <td>{{ $form->message }}</td>
    </tr>
</table>
@endcomponent
