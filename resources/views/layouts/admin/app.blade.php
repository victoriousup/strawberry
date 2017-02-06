@extends('layouts.admin.base', ['body_class' => 'site-menubar-native'])

{{-- Inject needed classes --}}
@inject('cloudStorageHelper', 'App\Utils\CloudStorageHelper')
@inject('cdnHelper', 'App\Utils\CDNHelper')


{{-- Header Scripts --}}
@push('head')
<script>

	// Base cdn url
	var cdnUrl = "{{ $cdnHelper::getBaseUrl() }}";

	// CSRF token
	var token = "{{ csrf_token() }}";

</script>
@endpush


@section('body_content')


{{-- Top navigation --}}
@include('layouts.admin.topnav')


{{-- Left navigation --}}
@include('layouts.admin.leftnav')


{{-- Page --}}
<div class="page animsition">
	@yield('page')
</div>


{{-- Footer --}}
<footer class="site-footer">
	<div class="site-footer-legal">© 2016 Digital Strawberry LLC</div>
</footer>



@endsection