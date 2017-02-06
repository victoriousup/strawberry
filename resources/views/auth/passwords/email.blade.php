@extends('layouts.admin.base', ['title' => 'Forgot Password', 'body_class' => 'page-login-v3 layout-full'])

@push('head')
<link rel="stylesheet" href="layouts/admin/base/css/login-v3.css">
@endpush

<!-- Main Content -->
@section('body_content')

<div class="page vertical-align text-center">
	<div class="page-content vertical-align-middle">
		<div class="panel">
			<div class="panel-body">

				<div class="brand">
					<h2 class="brand-text font-size-18">Forgot your password?</h2>
				</div>

				<form method="POST" action="admin/password/email">

					{!! csrf_field() !!}

					@if (session('status'))
					<div class="alert alert-success dark">
						{{ session('status') }}
					</div>
					@endif

					{{-- Email address --}}
					<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">

						@if ($errors->has('email'))
						<label class="control-label" for="email">{{ $errors->first('email') }}</label>
						@endif

						<input type="email" class="form-control" name="email" placeholder="Email Address" value="{{ old('email') }}" />

					</div>

					<button type="submit" class="btn btn-primary btn-block btn-lg margin-top-40">Reset Password</button>

				</form>

				<p><a href="admin/login/">Login to Account</a></p>

			</div>
		</div>
	</div>
</div>

@endsection
