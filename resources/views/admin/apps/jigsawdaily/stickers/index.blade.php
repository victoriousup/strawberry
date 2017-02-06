@extends('layouts.admin.page', [

	'title' => 'Stickers',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/']
])

@section('content')

<a href="admin/jigsaw-daily/stickers/create" class="btn btn-primary btn-outline">
	<i class="icon fa-plus"></i>
	New Sticker
</a>


<table class="table table-bordered table-hover" style="margin-top: 25px;">
	<tr>
		<th>Name</th>
		<th>Preview</th>
	</tr>

	@foreach($stickers as $sticker)

		<tr>
			<td><a href="admin/jigsaw-daily/stickers/{{ $sticker->id }}/edit">{{ $sticker->name }}</a></td>
			<td><img src="{{ $sticker->getCdnUrl() }}" style="max-width: 200px"></td>
		</tr>

	@endforeach

</table>

@endsection
