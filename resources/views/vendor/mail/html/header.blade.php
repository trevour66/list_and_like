@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <!-- <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo"> -->
            <img src="https://listandlike.com/images/Logo.png" alt="" class="h-10">
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>