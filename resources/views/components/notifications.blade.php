@php
    $notifications = app('accelade.notify')->flush();
@endphp

@if($notifications->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifications = @json($notifications);
        notifications.forEach(function(n) {
            window.Accelade?.notify?.show(n);
        });
    });
</script>
@endif
