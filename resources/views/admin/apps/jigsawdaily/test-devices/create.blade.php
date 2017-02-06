@extends('layouts.admin.page', [

	'title' => $title,

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/',
					  'Test Devices', 'admin/jigsaw-daily/test-devices/']
])

@section('content')

	{!! BForm::open() !!}
	{!! BForm::bind($device) !!}
	<div class="basic-form">

		{!! BForm::text('Device id', 'device_id') !!}

		{!! BForm::text('Description', 'description') !!}

		{!! BForm::submit($title)->addClass('btn-default btn-outline') !!}

	</div>
	{!! BForm::close() !!}



@endsection
