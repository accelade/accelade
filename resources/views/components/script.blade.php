{{--
    Accelade Script Component

    Use this component to define custom JavaScript functions that have access to:
    - state: The reactive state object
    - actions: Built-in actions (increment, decrement, set, toggle, reset)
    - $set(key, value): Set a state value
    - $get(key): Get a state value
    - $toggle(key): Toggle a boolean state value
    - $navigate(url, options): Navigate to a URL using SPA
    - $watch(key, callback): Watch a state property (Vue only)

    The script should return an object with your custom methods:

    Example:
    <accelade:script>
        return {
            customIncrement() {
                $set('count', $get('count') + 5);
            },
            handleSubmit(event) {
                event.preventDefault();
                $navigate('/success');
            }
        };
    </accelade:script>
--}}

@php
    $framework = config('accelade.framework', 'vanilla');
@endphp

@if($framework === 'vanilla')
<script type="text/accelade" a-script>
{{ $slot }}
</script>
@elseif($framework === 'vue')
<script type="text/accelade" v-script>
{{ $slot }}
</script>
@else
<script type="text/accelade" state-script>
{{ $slot }}
</script>
@endif
