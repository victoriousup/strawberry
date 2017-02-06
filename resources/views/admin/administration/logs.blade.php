@extends('layouts.admin.page', [

	'title' => 'Access Logs',

	'breadcrumbs' => [

		'Administration', 'admin/'

	]
])

@section('content')


<table class="table table-bordered" style="margin-top: 25px;">
	<tr>
		<th>Date</th>
		<th>Event</th>
		<td>Email</td>
		<th>IP Address</th>
		<th>Location</th>
	</tr>

	@foreach($logs as $log)

		<tr>
			<td>{{ $log->date->diffForHumans() }}</td>
			<td>
				@if($log->event == 0)

					<div class="label label-warning">Failed Login</div>

				@elseif($log->event == 1)

					<div class="label label-success">Successful Login</div>

				@elseif($log->event == 2)

					<div class=""label label-danger">Banned IP</div>

				@endif

			</td>
			<td>{{ $log->email }}</td>
			<td>{{ $log->ip }}</td>
			<td>

				@if($log->country != '')

					{{$log->city}}, {{$log->country_code}}

				@endif

			</td>
		</tr>


	@endforeach

</table>

@endsection
