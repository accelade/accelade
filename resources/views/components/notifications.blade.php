@php
    $notifications = app('accelade.notify')->flush();
@endphp

@if($notifications->isNotEmpty())
<script data-accelade-notifications>
    (function() {
        // Generate unique ID to prevent duplicate execution
        const scriptId = '{{ md5(json_encode($notifications)) }}';

        // Check if this exact set of notifications was already shown
        if (window.__acceladeNotificationsShown && window.__acceladeNotificationsShown[scriptId]) {
            return;
        }

        // Mark as shown
        window.__acceladeNotificationsShown = window.__acceladeNotificationsShown || {};
        window.__acceladeNotificationsShown[scriptId] = true;

        const notifications = @json($notifications);

        function showNotifications() {
            notifications.forEach(function(n) {
                window.Accelade?.notify?.show(n);
            });
        }

        // Show immediately if Accelade is ready, otherwise wait for DOMContentLoaded
        if (window.Accelade?.notify) {
            showNotifications();
        } else if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', showNotifications, { once: true });
        } else {
            // DOM is ready but Accelade might not be, retry shortly
            setTimeout(function checkAccelade() {
                if (window.Accelade?.notify) {
                    showNotifications();
                } else {
                    setTimeout(checkAccelade, 50);
                }
            }, 10);
        }
    })();
</script>
@endif
