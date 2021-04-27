<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>SMS MANGMENT APP</title>
	<link href="{{ asset('admin/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script type="javascript"  src="https://code.jquery.com/jquery-3.5.1.js" > </script>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" rel="stylesheet" />

</head>
    <body >
    
        @auth
        <div class="alert alert-danger" role="alert">
			<marquee scrollamount="4" direction="right" scrolldelay="100" behavior="alternate">The portal is under maintenance, you may find some data missing, it will be updated shortly. We apologize for the inconvenience.</marquee>
		</div>
	    <div class="wrapper" >
            @include('dashboard.includes.sidenav')
            <div class="main">
            @include('dashboard.includes.navigation')
        @endauth
               {{ $slot }}
            @auth
            @include('dashboard.includes.footer')
            </div>
        </div>
        @endauth
        @include('dashboard.includes.script')

    </body>
</html>
