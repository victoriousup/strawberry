@extends('layouts.admin.base', ['title' => 'Login', 'body_class' => 'page-login-v3 layout-full'])

@push('head')
<link rel="stylesheet" href="layouts/admin/base/css/login-v3.css">
@endpush

@section('body_content')

<div class="page vertical-align text-center">
	<div class="page-content vertical-align-middle">
		<div class="panel">
			<div class="panel-body">

				<div class="brand">
					<img class="brand-img" src="layouts/admin/base/images/logo-blue.png">
					<h2 class="brand-text font-size-18">Digital Strawberry Admin</h2>
				</div>

				<form method="POST" action="{{ url('/admin/login') }}">

					{!! csrf_field() !!}

					{{-- Email address --}}
					<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">

						@if ($errors->has('email'))
						<label class="control-label" for="email">{{ $errors->first('email') }}</label>
						@endif

						<input type="email" class="form-control" name="email" placeholder="Email Address" />

					</div>


					{{-- Password --}}
					<div class="form-group  {{ $errors->has('password') ? ' has-error' : '' }}">

						@if ($errors->has('password'))
						<label class="control-label" for="email">{{ $errors->first('password') }}</label>
						@endif

						<input type="password" class="form-control" name="password" placeholder="Password" />

					</div>

					<div class="form-group clearfix">
						<div class="checkbox-custom checkbox-inline checkbox-primary checkbox-lg pull-left">
							<input type="checkbox" id="inputCheckbox" name="remember" checked>
							<label for="inputCheckbox">Remember me</label>
						</div>
						<a class="pull-right" href="admin/password/reset/">Forgot password?</a>
					</div>

					<button type="submit" class="btn btn-primary btn-block btn-lg margin-top-40">Sign in</button>

				</form>
			</div>
		</div>
	</div>
</div>

@endsection



@push('scripts-footer')

<script>

	$(function()
	{
		$("input:text:visible:first").focus();
	});
</script>

@endpush