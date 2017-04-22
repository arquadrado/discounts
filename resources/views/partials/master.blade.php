<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
	<head>
	
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">

	    <!-- Fonts -->
	    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

	    <link href="/css/app.css" rel="stylesheet" type="text/css">
	    
		@section('title')
			<title>Cloudoki Orders</title>
		@show

		<script>
		    window.Laravel = <?php echo json_encode([
		        'csrfToken' => csrf_token(),
		    ]); ?>
		</script>

</head>
	
	<body>

		@yield('content')

		@section('scripts')
			<script src="/js/app.js"></script>
		@show
	</body>
		
</html>
	

