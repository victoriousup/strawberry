@extends('layouts.admin.page', [

	'title' => 'Jigsaw Organizer',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/']

])


{{-- Head content --}}
@push('head')
	<link rel="stylesheet" href="apps/jigsaw-daily/css/organizer.css">
@endpush


@section('content')

	{!! BForm::open()->addClass('form-inline')->id('filter') !!}

		{!! BForm::select('Filter', 'category')->options($categories) !!}

		{!! BForm::select('Subcategory', 'subcategory')->hideLabel() !!}

		<div class="pull-right">
			{!! BForm::text('Search', 'search')->placeholder('Photo Search')->hideLabel()->addClass('filter-control') !!}
			<a id="searchBtn" class="btn btn-default btn-outline">Search</a>
		</div>

	{!! BForm::close() !!}

	<div class="clearfix"></div>

	<div id="photos"></div>

	<div id="loading">Loading...</div>

@endsection




@section('panel')

	@include('admin.apps.jigsawdaily.organize.preview')

@endsection




@push('scripts-footer')

<script src="js/infinitescroll.js"></script>
<script src="apps/jigsaw-daily/js/organizer.js"></script>
<script>

	var organizer;

	$(function()
	{
		organizer = new Organizer();
	});

</script>

@endpush
