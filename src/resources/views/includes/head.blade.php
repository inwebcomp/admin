<link rel="stylesheet" href="{{ asset('admin-assets/fonts/FontAwesome/css/all.css') }}" />

<link rel="stylesheet" href="{{ mix('/css/app.css', 'admin-assets') }}" />

<!-- Tool Styles -->
@foreach(\InWeb\Admin\App\Admin::availableStyles(request()) as $name => $path)
    <link rel="stylesheet" href="{{ route('admin::style', $name) }}">
@endforeach

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
		'csrfToken' => csrf_token(),
	]) !!};
</script>

<link rel="shortcut icon" type="image/png" href="{{ asset('admin-assets/img/site/logo.png') }}"/>

@include('admin::includes.meta')

@stack('css')