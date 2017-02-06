<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="bootstrap admin template">
	<meta name="author" content="">
	<title>{{ isset($title) ? $title . ' - Digital Strawberry Admin' : 'Digital Strawberry Admin' }}</title>
	<base href="/">
	<link rel="shortcut icon" href="../../assets/images/favicon.ico">


	<!-- Stylesheets -->
	<link rel="stylesheet" href="layouts/admin/global/css/bootstrap.min.css">
	<link rel="stylesheet" href="layouts/admin/global/css/bootstrap-extend.min.css">
	<link rel="stylesheet" href="layouts/admin/base/css/site.css">


	<!-- Plugins -->
	<link rel="stylesheet" href="vendor/animsition/animsition.css">


	<!-- Fonts -->
	<link rel="stylesheet" href="fonts/web-icons/web-icons.min.css">
	<link rel="stylesheet" href="fonts/font-awesome/font-awesome.min.css">
	<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>


	<!-- Scripts -->
	<script src="vendor/modernizr/modernizr.js"></script>
	<script src="vendor/breakpoints/breakpoints.js"></script>
	<script>
		Breakpoints();
	</script>

	@stack('head')

	<link rel="stylesheet" href="layouts/admin/base/css/mods.css">

</head>
<body class="{{ $body_class or '' }}">


{{-- Page content --}}
@yield('body_content')


<!-- Core  -->
<script src="vendor/jquery/jquery.js"></script>
<script src="vendor/bootstrap/bootstrap.js"></script>
<script src="vendor/animsition/animsition.js"></script>
<script src="vendor/asscroll/jquery-asScroll.js"></script>

<!-- Scripts -->
<script src="layouts/admin/global/js/core.js"></script>
<script src="layouts/admin/base/js/site.js"></script>
<script src="layouts/admin/base/js/sections/menu.js"></script>
<script src="layouts/admin/base/js/sections/menubar.js"></script>
<script src="layouts/admin/base/js/sections/gridmenu.js"></script>
<script src="layouts/admin/global/js/configs/config-colors.js"></script>
<script src="layouts/admin/global/js/components/animsition.js"></script>
<script src="vendor/alertify/js/alertify.js"></script>
<script src="js/admin.js"></script>

<!-- Custom -->
<script src="js/csrf.js"></script>

<script>
	(function(document, window, $) {
		'use strict';
		var Site = window.Site;
		$(document).ready(function() {
			Site.run();
		});
	})(document, window, jQuery);
</script>

@stack('scripts-footer')

</body>
</html>