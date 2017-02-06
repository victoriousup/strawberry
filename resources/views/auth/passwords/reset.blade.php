@extends('layouts.admin.base', ['title' => 'Reset Password', 'body_class' => 'page-login-v3 layout-full'])


@push('head')
<link rel="stylesheet" href="layouts/admin/base/css/login-v3.css">
@endpush


@section('body_content')

<div class="page vertical-align text-center">
	<div class="page-content vertical-align-middle">
		<div class="panel">
			<div class="panel-body">

				<div class="brand">
					<h2 class="brand-text font-size-18">Reset your password</h2>
				</div>

				<form method="POST" action="admin/password/reset">

					{!! csrf_field() !!}

					<input type="hidden" name="token" value="{{ $token }}">
					<input type="hidden" class="form-control" name="email" placeholder="Email Address" value="{{ $email or old('email') }}" />

					@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					@if ($errors->has('email'))
					<div class="alert alert-danger dark">{{ $errors->first('email') }}</div>
					@endif


					{{-- Password --}}
					<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">

						@if ($errors->has('password'))
						<label class="control-label" for="password">{{ $errors->first('password') }}</label>
						@endif

						<input type="password" class="form-control" name="password" placeholder="Password" />

					</div>


					{{-- Confirm Password --}}
					<div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">

						@if ($errors->has('password_confirmation'))
						<label class="control-label" for="password_confirmation">{{ $errors->first('password_confirmation') }}</label>
						@endif

						<input type="password" class="form-control" name="password_confirmation" placeholder="Password Confirmation" />

					</div>


					<button type="submit" class="btn btn-primary btn-block btn-lg margin-top-40">Reset Password</button>

				</form>

			</div>
		</div>
	</div>
</div>

@endsection
