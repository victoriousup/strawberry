@extends('layouts.admin.page', [

	'title' => $pack->name . ' (' . $date->format('F d, Y') . ')',

	'breadcrumbs' => [  'Jigsaw Daily', 'admin/jigsaw-daily/',
						'Daily Jigsaws', 'admin/jigsaw-daily/daily/' . $date->format('m/Y/'),
						'Packs', 'admin/jigsaw-daily/daily/packs/' . $date->format('Y-m-d')]

])


@push('head')
	<link rel="stylesheet" href="apps/jigsaw-daily/css/daily.css">
@endpush


@section('content')

	@include('admin.apps.jigsawdaily.daily.filter-partial')

	<div id="packs">
		@foreach($photos as $photo)

			<div class="pack filter-element" data-filter="{!! htmlspecialchars(json_encode(['name' => $photo->name, 'daysAgoUsed' => $photo->daysAgoUsed])) !!}">

				@if($photo->daysAgoUsed != null)
					<div class="label label-dark">
						{{ $photo->daysAgoUsed }} days
					</div>
				@endif

				<a href="admin/jigsaw-daily/daily/set/{{ $photo->id }}/{{ $date->format('Y-m-d') }}/">
					<img src="{{ $photo->getFileUrl(200) }}">
				</a>

				<div style="clear: both;">
					{{ $photo->name }}
				</div>

			</div>

		@endforeach
	</div>


@endsection


@push('scripts-footer')

<script src="js/displayfilter.js"></script>
<script>

	$(function()
	{
		var filter = new DisplayFilter($('#filter'), $('#packs'));
	});

</script>

@endpush