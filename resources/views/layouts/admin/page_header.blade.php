@if(isset($title) || isset($breadcrumbs))

<div class="page-header">

	<ol class="breadcrumb">
		<li><a href="admin/">Home</a></li>

		{{-- Cycle through each of the breadcrumbs --}}
		@if(isset($breadcrumbs))

			@for($i = 0; $i < sizeof($breadcrumbs) - 1; $i += 2)

				<li><a href="{{ $breadcrumbs[$i + 1] }}">{{ $breadcrumbs[$i] }}</a></li>

			@endfor

		@endif

		<li class="active">{{ $title }}</li>
	</ol>


	{{-- Page title --}}
	@if(isset($title))
	<h1 class="page-title">{{ $title }}</h1>
	@endif

</div>

@endif