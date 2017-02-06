@extends('layouts.admin.page', [

	'title' => 'Players',

	'breadcrumbs' => [  'Jigsaw Daily', 'admin/jigsaw-daily/']

])
@section('content')


	<form class="form-inline" method="GET" action="admin/jigsaw-daily/players/device/">
		<div class="form-group">
			<input type="text" class="form-control" name="id" placeholder="Device Id">
		</div>
		<button type="submit" class="btn btn-default btn-outline">Search</button>
	</form>


	@if($error)

		<div style="margin-top: 20px;" class="alert alert-danger dark alert-icon">
			<i class="icon fa fa-exclamation-circle"></i>
			Device id not found.
		</div>

	@endif



@endsection
