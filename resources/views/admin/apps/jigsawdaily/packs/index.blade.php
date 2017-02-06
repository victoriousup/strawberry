@extends('layouts.admin.page', [

	'title' => 'Packs',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/']
])

@section('content')

<a href="admin/jigsaw-daily/packs/create" class="btn btn-primary btn-outline">
	<i class="icon fa-plus"></i>
	New Pack
</a>


{!! BForm::open()->addClass('form-inline')->attribute('style', 'margin-top: 25px')->id('filter') !!}

	{!! BForm::select('Status', 'released')->options(['any' => 'Any', '1' => 'Released', '0' => 'Not Released'])->addClass('filter-control') !!}

	{!! BForm::select('Visibility', 'visible')->options(['any' => 'Any', '1' => 'Visible', '0' => 'Hidden'])->addClass('filter-control') !!}

	{!! BForm::select('iTunes Status', 'itunes_status')->options(array_merge(['any' => 'Any'], \App\Models\Jigdaily\Pack::$iTunesStatusTypes))->addClass('filter-control') !!}

	{!! BForm::text('Pack Name', 'name')->addClass('filter-control') !!}

{!! BForm::close() !!}


<table id="dataTable" class="table table-bordered table-hover" style="margin-top: 25px;">

	<tr>
		<th>Name</th>
		<th>Total Jigsaws</th>
		<th>Release Status</th>
		<th>Visible</th>
		<th>iTunes Status</th>
		<th>Actions</th>
	</tr>

	@foreach($packs as $pack)

		<tr class="filter-element {{ $pack->released ? 'active' : '' }}" data-filter="{!! htmlspecialchars(json_encode($pack)) !!}">
			<td>
				<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/edit/">{{ $pack->name }}</a>
			</td>
			<td>{{ $pack->photos()->count() }}</td>
			<td>

				@if($pack->released)
					<div class="label label-success">Released</div>
				@else
					<div class="label label-default">Not Released</div>
				@endif

			</td>
			<td>

				@if($pack->visible)
					<div class="label label-success">Visible</div>
				@else
					<div class="label label-default">Hidden</div>
				@endif

			</td>
			<td>

				@if($pack->itunes_status == 2)
					<div class="label label-success">Approved</div>
				@else
					<div class="label label-default">{{ \App\Models\Jigdaily\Pack::$iTunesStatusTypes[$pack->itunes_status] }}</div>
				@endif

			</td>
			<td>

				<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/edit" data-toggle="tooltip" title="Edit">
					<i class="fa-wrench"></i></a>

				<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/preview" data-toggle="tooltip" title="Preview">
					<i class="fa-search"></i></a>

				<a href="admin/jigsaw-daily/organize/{{ $pack->id }}" data-toggle="tooltip" title="Organize Images">
					<i class="fa-image"></i></a>

				<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/promos" data-toggle="tooltip" title="Manage Promos">
					<i class="fa-bullhorn"></i></a>

			</td>
		</tr>

	@endforeach

</table>

@endsection


@push('scripts-footer')

<script src="js/displayfilter.js"></script>
<script>

	$(function()
	{
		var filter = new DisplayFilter($('#filter'), $('#dataTable'));
	});

</script>

@endpush