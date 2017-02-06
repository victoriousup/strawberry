@extends('layouts.admin.page', [

	'title' => $pack->name,

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/',
					  'Packs', 'admin/jigsaw-daily/packs/']
])


{{-- Head content --}}
@push('head')
	<link rel="stylesheet" href="apps/jigsaw-daily/css/organizer.css">
@endpush


@section('content')

	<!-- Action menu -->
	@include('admin.apps.jigsawdaily.packs.action-menu')

	<div id="photos" style="margin-top: 20px;">

		{{-- No photos in this pack --}}
		@if(sizeof($photos) == 0)
			<div style="text-align: center; margin: 60px 0 60px 0;">There are no photos currently in this pack.</div>
		@endif

		@foreach($photos as $photo)

			<div class="photo">
				<a href="{{ $photo->getFullFileUrl(20) }}">
				
				{{-- Pack cover --}}
				@if($photo->id == $pack->cover_id)
					<div class="ribbon ribbon-primary ribbon-badge"><span class="ribbon-inner">Cover</span></div>
				@endif

					<img src="{{ $photo->getFileUrl(300) }}">
				</a>
				{{ $photo->name }}

			</div>

		@endforeach

	</div>


@endsection