@extends('layouts.admin.page', [

	'title' => $pack->name . ' ' . $title,

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/',
					  'Packs', 'admin/jigsaw-daily/packs/',
					  $pack->name, 'admin/jigsaw-daily/packs/' . $pack->id . '/edit/',
					  'Promos', 'admin/jigsaw-daily/packs/' . $pack->id . '/promos/']
])


@push('head')

<script src="vendor/dropzone/dropzone.js"></script>
<link rel="stylesheet" href="vendor/dropzone/dropzone.css">
<style>

	#stickerPreviewContainer
	{
		position: relative;
		border: 2px dotted #f0f0f0;
		padding: 50px;
		width: 300px;
		margin-bottom: 20px;
	}

	#stickerPreview
	{
		width: 200px;
		height: 200px;
		background-color: #f0f0f0;
		position: relative;
	}

	#sticker
	{
		position: absolute;
		top: 0px;
		left: 0px;
		cursor: move;
	}

</style>

@endpush


@section('content')

	<div class="basic-form">

		{!! BForm::open() !!}
		{!! BForm::bind($promo) !!}
		{!! BForm::hidden('url')->value($promo->getCdnUrl(452)) !!}
		{!! BForm::hidden('file') !!}

		{!! BForm::text('Name', 'name') !!}

		{!! BForm::select('Status', 'status')->options([1 => 'Active', 0 => 'Paused']) !!}

		{!! BForm::select('Type', 'type')->options([0 => 'Both (Daily and Recommended)', 1 => 'Daily Pack', 2 => 'Recommended Pack'])->select(2) !!}

		{!! BForm::select('Currency', 'currency')->options($currencies) !!}

		{!! BForm::select('Price', 'price') !!}

		<div class="form-group">
			<input type="text" name="price_custom" value="{{ $promo->price }}" class="form-control" style="display: none">
		</div>

		<div id="promoPreview"></div>

		<div class="help-block">

			@if($errors->has('file'))
				<span style="color: red;">Please upload a valid promo image</span>
			@else
				Promo image must be 1362px x 828px in JPEG format
			@endif

		</div>

		<div id="dropzone" class="dropzone">
			<div class="dz-message">
				<i class="icon fa-file-image-o"></i><br>
				Click or drop image here to upload
			</div>
		</div>

		{!! BForm::submit($title)->addClass('btn-default btn-outline') !!}

		{!! BForm::close() !!}

	</div>

@endsection


@push('scripts-footer')

<script>

	// Don't auto-create dropzones (we'll create these manually)
	Dropzone.autoDiscover = false;

	// Available prices per currency
	var prices = JSON.parse("{!! addslashes($prices) !!}");

	$(function()
	{
		var $priceSelect = $('select[name=price]');
		var $priceCustom = $('input[name=price_custom]');

		// -------------------------
		// Setup the dropzone
		// -------------------------
		var myDropzone = new Dropzone("div#dropzone", {
			url: "admin/jigsaw-daily/packs/promos/upload",
			headers: {
				'X-CSRF-Token': '{{ csrf_token() }}'
			}
		});


		// -------------------------
		// Listen for error events
		// -------------------------
		myDropzone.on('error', function(file, errorMessage)
		{
			// Display an error message
			if(errorMessage.error)
			{
				alertify.alert(errorMessage.error);
			}
			else
			{
				alertify.alert('The promo could not be uploaded. Please try again later.');
			}

			// Remove the file preview
			myDropzone.removeFile(file);
		});


		// -------------------------
		// Listen for success events
		// -------------------------
		myDropzone.on('success', function(file, response)
		{
			// Remove the file preview
			myDropzone.removeFile(file);

			// Load the image file attributes into the hidden form element
			$('input[name=url]').val(response.url);
			$('input[name=file]').val(response.filename);

			// Load the uploaded file onto the page
			updatePromoImage();
		});


		// -------------------------
		// Display the current sticker image
		// -------------------------
		updatePromoImage();


		// -------------------------
		// Update currency prices
		// -------------------------
		$('select[name=currency]').change(function()
		{
			updatePrices();
		});

		updatePrices();


		// -------------------------
		// Initial price
		// -------------------------
		var initialPrice = $priceCustom.val();
		$priceSelect.val(initialPrice);
		if($priceSelect.val() != initialPrice)
		{
			$priceCustom.show();
			$priceSelect.val('custom');
		}


		// -------------------------
		// Custom price
		// -------------------------
		$priceSelect.change(function()
		{
			var $priceInput = $('input[name=price_custom]');

			if($(this).val() == 'custom')
			{
				$priceInput.show();
			}
			else
			{
				$priceInput.hide();
			}
		});

	});


	function updatePromoImage()
	{
		// Get the current image to be displayed
		var file = $('input[name=file]').val();
		if(file == '')
		{
			return;
		}

		// Remove any previous image
		$('#promoPreview img').remove();

		// Get the new sticker data
		var imageUrl = $('input[name=url]').val();

		// Add the promo image
		$('#promoPreview').append('<img src="' + imageUrl + '">');
	}


	function updatePrices()
	{
		var $priceSelect = $('select[name=price]');
		var $priceInput = $('input[name=price_custom]');
		var currency = $('select[name=currency]').val();

		$priceSelect.find('option').remove();
		$priceSelect.append('<option value="">Any</option>');

		if(currency != '')
		{
			var myPrices = prices[currency];
			for(var i = 0; i < myPrices.length; i++)
			{
				$priceSelect.append('<option value="' + myPrices[i] + '">' + myPrices[i] + ' ' + currency + ' (Tier ' + (i + 1) + ')</option>');
			}
		}

		$priceSelect.append('<option value="custom">Custom</option>');
		$priceInput.hide();
	}

</script>

@endpush