@extends('layouts.admin.page', [

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/', 'Packs', 'admin/jigsaw-daily/packs/']
])

@push('head')

<script src="js/dataselect.js"></script>
<style>

	.dataselect
	{
		margin-left: 20px;
		margin-bottom: 20px;
	}

	.dataselect div
	{
		margin-right: 4px;
	}

	#packPreview
	{
		width: 200px;
		height: 200px;
		background-color: #f0f0f0;
		position: absolute;
		top: 50px;
		right: 50px;
	}

	#packPreview img
	{
		width: 100%;
		height: 100%;
	}

	#packPreview .sticker
	{
		position: absolute;
		top: 0px;
		left: 0px;
	}


</style>

@endpush


@section('content')

<!-- Action menu -->
@include('admin.apps.jigsawdaily.packs.action-menu')


{!! BForm::open() !!}
{!! BForm::bind($pack) !!}
<div class="basic-form" style="float: left">

	{!! BForm::text('Name', 'name') !!}

	{!! BForm::hidden('sticker_ids') !!}

	{!! BForm::select('Stickers', '_stickers') !!}

	<!-- List of the currently chosen stickers -->
	<div id="stickerList" class="dataselect"></div>

	<input type="hidden" name="visible" value="0">
	{!! BForm::checkbox('Visible in Store', 'visible')->helpBlock('Packs are only visible in the store once they have also been released.') !!}

	<input type="hidden" name="released" value="0">
	{!! BForm::checkbox('Released', 'released')->id('released') !!}

	{!! BForm::select('Price (USD)', 'price_tier')->options($tiers) !!}

	{!! BForm::select('iTunes Store Status', 'itunes_status')->options(\App\Models\Jigdaily\Pack::$iTunesStatusTypes) !!}

	<input type="hidden" name="featured" value="0">
	{!! BForm::checkbox('Homepage Feature', 'featured') !!}

	{!! BForm::select('Recommended Packs', '_recommended') !!}

	{!! BForm::hidden('recommended_pack_ids') !!}

	<div id="recommendedList" class="dataselect"></div>

	{!! BForm::submit($title)->addClass('btn-default btn-outline') !!}

</div>
{!! BForm::close() !!}


<div id="packPreview">

	@if($pack->getCover() != null)

		<img src="{{ $pack->getCover()->getFileUrl(300) }}">

	@endif

</div>


@endsection



@push('scripts-footer')

<script>

	// Array containing sticker data
	var stickerData = {!! json_encode($stickerData) !!};

	// Array containing pack data
	var packData = {!! json_encode($packData) !!};


	$(function()
	{
		// -----------------------
		// Stickers
		// -----------------------
		var stickerDataSelect = new DataSelect($('select[name=_stickers]'),
				$('input[name=sticker_ids]'),
				$('#stickerList'),
				stickerData);

		stickerDataSelect.updated = function(data)
		{
			// Get the preview size
			var thumbWidth = $('#packPreview').width();
			var thumbHeight = $('#packPreview').height();

			// Remove all existing stickers
			$('#packPreview .sticker').remove();

			// Add current stickers in order
			for(var i = 0; i < data.length; i++)
			{
				var item = data[i];

				var sticker = $('<img class="sticker" src="' + item.img + '">');
				sticker.width(thumbWidth * item.widthScale);
				sticker.height(thumbHeight * item.heightScale);
				sticker.css('left', thumbWidth * item.xScale);
				sticker.css('top', thumbHeight * item.yScale);

				$('#packPreview').append(sticker);
			}
		};

		stickerDataSelect.init();


		// -----------------------
		// Recommended packs
		// -----------------------

		var packDataSelect = new DataSelect($('select[name=_recommended]'),
				$('input[name=recommended_pack_ids]'),
				$('#recommendedList'),
				packData)
				.init();


		//updateReleasedCheckbox();

	});


	function updateReleasedCheckbox()
	{
		$('#released').prop('disabled', $('select[name=itunes_status]').val() != 2);
	}

</script>


@endpush