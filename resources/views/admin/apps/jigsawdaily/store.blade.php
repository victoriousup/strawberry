@extends('layouts.admin.page', [

	'title' => 'Store',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/']
])


@push('head')

<style>

	#packs
	{
		margin-left: -40px;
		margin-top: 40px;
	}

	.pack
	{
		width: 200px;
		float: left;
		margin-left: 40px;
		margin-bottom: 20px;
		text-align: center;
		position: relative;
	}

	.pack:nth-child(3n - 5)
	{
		clear: left;
	}

	.pack .image
	{
		width: 200px;
		height: 200px;
		margin-bottom: 8px;
		background-color: #e3e3e3;
	}

	.pack .sticker
	{
		position: absolute;
		top: 0;
		left: 0;
	}

	.pack .overlay
	{
		display: none;
		width: 200px;
		height: 200px;
		position: absolute;
		top: 0;
		left: 0;
		background-color: rgba(0, 0, 0, 0.50);
	}

	.pack .overlay a
	{
		margin-top: 8px;
		width: auto;
	}

	.pack .overlay a:nth-child(-n + 2)
	{
		margin: 15px 0 15px 0;
	}

	.pack:hover .overlay
	{
		display: block;
	}


</style>

@endpush


@section('content')


<a class="btn btn-primary btn-outline btn-save"><i class="fa fa-refresh"></i> Save Store Order</a>

<div id="packs">

	@foreach($packs as $pack)

		<div id="pack_{{ $pack->id }}" class="pack" data-pack="{{ $pack->id }}">

			{{-- Stickers --}}
			@foreach($pack->getStickers() as $sticker)

				<img class="sticker" src="{{ $sticker->getCdnUrl(200) }}" style="width: {{ $sticker->widthScale * 200 }}px; height: {{ $sticker->heightScale * 200 }}px; top: {{ $sticker->yScale * 200 }}px; left: {{ $sticker->xScale * 200 }}px;">

			@endforeach

			{{-- Overlay --}}
			<div class="overlay">
				<a onclick="movePack({{ $pack->id }}, 'up'); return false;" class="btn btn-default"><i class="fa-arrow-up"></i></a>
				<a onclick="movePack({{ $pack->id }}, 'down'); return false;" class="btn btn-default"><i class="fa-arrow-down"></i></a>
				<br>
				<a href="admin/jigsaw-daily/packs/{{ $pack->id }}/edit" class="btn btn-primary"><i class="fa-pencil"></i> Edit Pack</a>
				<a href="admin/jigsaw-daily/organize/{{ $pack->id }}" class="btn btn-primary"><i class="fa-photo"></i> Edit Photos</a>
			</div>

			{{-- Cover image --}}
			@if($pack->cover_id == -1)
				<img class="image" src="">
			@else
				<img class="image" src="{{ $pack->getCover()->getFileUrl(200) }}">
			@endif

			{{ $pack->name }}
		</div>

	@endforeach

</div>

@endsection


@push('scripts-footer')

<script src="vendor/sortable/Sortable.js"></script>
<script>

	// Has the current store order been saved?
	var saved = true;

	$(function()
	{
		// Setup sortable
		var el = document.getElementById('packs');
		var sortable = Sortable.create(el, {

			onMove: function(event)
			{
				saved = false;
			}

		});

		// Save changes
		$('.btn-save').on('click', function()
		{
			$('.btn-save').addClass('disabled');

			var data = [];

			$('.pack').each(function()
			{
				data.push($(this).data('pack'));
			});

			$.post('admin/jigsaw-daily/store/', {packOrder: data}, function(response)
			{
				// Success!
				saved = true;
				alertify.alert("Store order has been saved!");
				$('.btn-save').removeClass('disabled');

			}).fail(function()
			{
				alertify.alert('Unable to save, please try again later.');
				$('.btn-save').removeClass('disabled');
			});

		});


		// Remind the user to save
		$(window).bind('beforeunload', function()
		{
			if(!saved)
			{
				return 'You have not saved your work!';
			}
		});

	});


	/**
	 * Moves a pack location within the store.
	 *
	 * @param packId
	 * @param direction
	 */
	function movePack(packId, direction)
	{
		$element = $('#pack_' + packId);
		$element.remove();

		if(direction == 'up')
		{
			$('#packs').prepend($element);
		}
		else
		{
			$('#packs').append($element);
		}

		saved = false;
	}

</script>

@endpush