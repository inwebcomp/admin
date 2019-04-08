<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
	<head>
		@include('admin::includes.head')
	</head>
	<body>
		<div id="app">
            @yield('content')
		</div>

		@include('admin::includes.footer')
	</body>
</html>
