@extends('layouts.admin.page', ['title' => 'Edit Profile'])

@section('content')

{!! BForm::open() !!}
{!! BForm::bind($user) !!}
<div class="basic-form">

	{!! BForm::text('Full Name', 'name') !!}

	{!! BForm::email('Email Address', 'email') !!}

	{!! BForm::password('Password', 'password')->helpBlock('(Optional) Enter a new password if you wish to change it') !!}

	{!! BForm::password('Confirm Password', 'password_confirm') !!}

	{!! BForm::submit('Edit Profile')->addClass('btn-default btn-outline') !!}

</div>
{!! BForm::close() !!}


@endsection