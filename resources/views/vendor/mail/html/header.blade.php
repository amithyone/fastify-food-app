@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('favicon.png') }}" class="logo" alt="Fastify Logo" style="height: 50px; width: auto;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
