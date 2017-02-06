@extends('layouts.admin.page', [

	'title' => 'Test Devices',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/']
])

@section('content')

	<a href="admin/jigsaw-daily/test-devices/create/" class="btn btn-primary btn-outline">
		<i class="icon fa-plus"></i>
		Add Device
	</a>

	@if(sizeof($devices) == 0)

		<div style="width: 100%; text-align: center; margin: 50px 0 50px 0;">No devices found.</div>

	@else

		<table id="dataTable" class="table table-bordered" style="margin-top: 25px;">

			<tr>
				<th>Description</th>
				<th>Device Id</th>
				<th>Actions</th>
			</tr>

			@foreach($devices as $device)

				<tr>
					<td>{{ $device->description }}</td>
					<td>{{ $device->device_id }}</td>
					<td>
						<a href="admin/jigsaw-daily/test-devices/device/{{ $device->id }}" data-toggle="tooltip" title="Edit">
							<i class="fa-wrench"></i></a>

						<a href="javascript:deleteDevice({{ $device->id }})" data-toggle="tooltip" title="Delete">
							<i class="fa-times"></i></a>

					</td>
				</tr>

			@endforeach

		</table>

	@endif

@endsection


@push('scripts-footer')

<script>

	function deleteDevice(id)
	{
		alertify.confirm('Are you sure you wish to delete this test device?', function()
		{
			document.location.href = 'admin/jigsaw-daily/test-devices/device/' + id + '/delete';
		});
	}

</script>

@endpush
