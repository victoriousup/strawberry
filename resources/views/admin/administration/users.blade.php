@extends('layouts.admin.page', [

	'title' => 'Users',

	'breadcrumbs' => [

		'Administration', 'admin/'

	]
])

@section('content')

<a href="admin/users/add" class="btn btn-primary btn-outline">
	<i class="icon fa-plus"></i>
	Add New User
</a>

<table class="table table-bordered" style="margin-top: 25px;">
	<tr>
		<th>Name</th>
		<th>Email</th>
		<th>User Type</th>
		<th>Last Login</th>
		<th>Login Location</th>
	</tr>

	@foreach($users as $user)

		<tr>
			<td><a href="admin/users/user/{{ $user->id }}/edit">{{ $user->name }}</a></td>
			<td>{{ $user->email }}</td>
			<td><div class="label {{ !$user->user_type ? 'label-success' : 'label-default' }}">{{ !$user->user_type ? 'Admin' : 'User' }}</div></td>
			<td>{{ $user->lastLogin() == null ? 'Never' : $user->lastLogin()->date->diffForHumans() }}</td>
			<td>

				@if($user->lastLogin() != null && $user->lastLogin()->country != '')

					{{ $user->lastLogin()->city}}, {{ $user->lastLogin()->country_code }}

				@endif

			</td>
		</tr>

	@endforeach

</table>

@endsection
