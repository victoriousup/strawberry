@extends('layouts.admin.base', ['title' => 'Jigsaw Organizer'])


{{-- Head content --}}
@push('head')
	<link rel="stylesheet" href="apps/jigsaw-daily/css/organizer.css">
@endpush


@section('body_content')

	@include('admin.apps.jigsawdaily.organize.preview')

@endsection




@push('scripts-footer')

<script src="apps/jigsaw-daily/js/organizer.js"></script>
<script>

	$(function()
	{
		window.packPreview = new PackPreview(window.opener.organizer, true);

		window.onunload = function()
		{
			window.opener.organizer.breakIn();
		}
	});

</script>

@endpush
