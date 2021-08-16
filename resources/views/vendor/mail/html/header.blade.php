<tr>
<td class="header">
<a href="{{ $settings->baseUrl }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ $settings->logotipo }}" class="logo" alt="Laravel Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
