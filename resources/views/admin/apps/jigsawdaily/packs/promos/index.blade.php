@extends('layouts.admin.page', [

	'title' => $pack->name . ' Promos',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/',
					  'Packs', 'admin/jigsaw-daily/packs/',
					  $pack->name, 'admin/jigsaw-daily/packs/' . $pack->id . '/edit/']
])


@section('content')

	<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/promos/create" class="btn btn-primary btn-outline">
		<i class="icon fa-plus"></i>
		New Promo
	</a>

	<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/promos/bulk" class="btn btn-primary btn-outline">
		<i class="icon fa-flash"></i>
		Bulk Uploader
	</a>

	<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/promo-builder" class="btn btn-primary btn-outline">
		<i class="icon fa-bullhorn"></i>
		Promo Builder
	</a>

	@if(sizeof($promos) == 0)

		<div style="text-align: center; margin: 60px 0 60px 0;">There are no promos for this pack.</div>

	@else

		<table class="table table-bordered table-hover" style="margin-top: 25px;">
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Status</th>
				<th>Preview</th>
				<th>Currency</th>
				<th>Price</th>
			</tr>

			@foreach($promos as $promo)

				<tr>
					<td><a href="admin/jigsaw-daily/packs/{{ $pack->id }}/promos/{{ $promo->id }}/edit">{{ $promo->name }}</a></td>
					<td>

						@if($promo->type == 0)

							<div class="label label-default">Both</div>

						@elseif($promo->type == 1)

							<div class="label label-success">Daily</div>

						@else

							<div class="label label-info">Recommended</div>

						@endif


					</td>
					<td>

						@if($promo->status)

							<div class="label label-success">Active</div>

						@else

							<div class="label label-warning">Paused</div>

						@endif

					</td>
					<td>

						<img style="width: 250px;" src="{{ $promo->getCdnUrl(452) }}">

					</td>
					<td>

						@if($promo->currency == '')
							Any
						@else
							{{ $promo->currency }}
						@endif

					</td>
					<td>

						@if($promo->price == null)

							Any

						@else

							{{ number_format($promo->price, 2) }}

						@endif

					</td>
				</tr>

			@endforeach

		</table>

	@endif



@endsection