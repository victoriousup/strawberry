@extends('layouts.admin.page', [

	'title' => $pack->name . ' Promo Builder',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/',
					  'Packs', 'admin/jigsaw-daily/packs/']
])


{{-- Head content --}}
@push('head')
	<link rel="stylesheet" href="apps/jigsaw-daily/css/organizer.css">
	<style>

		#promo-preview
		{
			width: 800px;
			height: 486px;
			position: relative;
			margin-top: 20px;
			margin-bottom: 50px;
		}

		.small-square
		{
			width: 157px;
			height: 157px;
			background-color: #a8a8a8;
			position: absolute;
		}

		.small-square img
		{
			width: 100%;
			height: 100%;
		}

		#square-1
		{
			top: 0;
			left: 0;
		}

		#square-2
		{
			top: 164px;
			left: 0;
		}

		#square-3
		{
			top: 328px;
			left: 0;
		}

		#square-4
		{
			top: 0;
			left: 643px;
		}

		#square-5
		{
			top: 164px;
			left: 643px;
		}

		#square-6
		{
			top: 328px;
			left: 643px;
		}

		#square-7
		{
			top: 0;
			left: 164px;
			width: 472px;
			height: 486px;
		}

		.photo.highlight
		{
			opacity: 0.5;
		}

	</style>
@endpush


@section('content')

	<a class="btn btn-primary btn-outline" onclick="generate()">Generate Promo</a>

	<div id="promo-preview">
		<div id="square-1" class="small-square"></div>
		<div id="square-2" class="small-square"></div>
		<div id="square-3" class="small-square"></div>
		<div id="square-4" class="small-square"></div>
		<div id="square-5" class="small-square"></div>
		<div id="square-6" class="small-square"></div>
		<div id="square-7" class="small-square"></div>
	</div>

	<div id="photos" style="margin-top: 20px;">

		{{-- No photos in this pack --}}
		@if(sizeof($photos) == 0)
			<div style="text-align: center; margin: 60px 0 60px 0;">There are no photos currently in this pack.</div>
		@endif

		@foreach($photos as $photo)

			<div class="photo" data-id="{{ $photo->id }}">
				<img src="{{ $photo->getFileUrl(500) }}">
			</div>

		@endforeach

	</div>

@endsection


@push('scripts-footer')
<script>

	var $photo;

	$(function()
	{
		$('.photo').click(function()
		{
			$photo = $(this);
			$('.photo').removeClass('highlight');
			$photo.addClass('highlight');
		});

		$('.small-square').click(function()
		{
			if($photo)
			{
				// Get photo url
				var photoUrl = $photo.find('img').attr('src');

				$(this).empty();
				$(this).append('<img src="' + photoUrl + '">');
				$(this).data('id', $photo.data('id'));
			}
		})
	});


	function generate()
	{
		var ids = [];
		for(var i = 1; i <= 7; i++)
		{
			ids.push($('#square-' + i).data('id'));
		}

		var idStr = JSON.stringify(ids);
		document.location.href = 'admin/jigsaw-daily/packs/promo-builder-generate?ids=' + idStr;
	}

</script>

@endpush