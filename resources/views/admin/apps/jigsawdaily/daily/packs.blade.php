@extends('layouts.admin.page', [

	'title' => 'Packs (' . $date->format('F d, Y') . ')',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/', 'Daily Jigsaws', 'admin/jigsaw-daily/daily/' . $date->format('m/Y/')]

])


@push('head')
	<link rel="stylesheet" href="apps/jigsaw-daily/css/daily.css">
@endpush


@section('content')

	@include('admin.apps.jigsawdaily.daily.filter-partial')

	<div id="packs">
		@foreach($packs as $pack)

			<div class="pack filter-element" data-filter="{!! htmlspecialchars(json_encode(['name' => $pack->name, 'daysAgoUsed' => $pack->daysAgoUsed])) !!}">

				@if($pack->daysAgoUsed != null)
					<div class="label label-dark">
						{{ $pack->daysAgoUsed }} days
					</div>
				@endif

				<a href="admin/jigsaw-daily/daily/pack/{{ $pack->id }}/{{ $date->format('Y-m-d') }}/">
					<img src="{{ $pack->getCover()->getFileUrl(200) }}">
				</a>

				<div style="clear: both;">
					{{ $pack->name }}
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