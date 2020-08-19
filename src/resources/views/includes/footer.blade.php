<script>
    window.config = @json(\InWeb\Admin\App\Admin::jsonVariables(request()));
</script>

@stack('js')

<script src="{{ mix('/js/app.js', 'admin-assets') }}"></script>

<!-- Build Admin Instance -->
<script>
    window.App = new CreateApp(config)
</script>

<!-- Tool Scripts -->
@foreach (\InWeb\Admin\App\Admin::availableScripts(request()) as $name => $path)
    @if (\Str::startsWith($path, ['http://', 'https://']))
        <script src="{!! $path !!}"></script>
    @else
        <script src="{{ route('admin::script', $name) }}"></script>
    @endif
@endforeach

<script>
    window.App.start()
</script>
