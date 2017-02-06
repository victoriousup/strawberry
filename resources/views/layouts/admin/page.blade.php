@extends('layouts.admin.app')

@section('page')

{{-- Page title and breadcrumbs --}}
@include('layouts.admin.page_header')


<div class="page-content">
	<div id="mainPanel" class="panel">
		<div class="panel-body">

			@if(Session::has('alert-success'))
				<div class="alert alert-success dark">{{ Session::get('alert-success') }}</div>
			@endif

			@yield('content')

		</div>
	</div>

	@yield('panel')

</div>

@endsection