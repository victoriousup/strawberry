@extends('layouts.admin.page', [

'breadcrumbs' => [  'Jigsaw Daily', 'admin/jigsaw-daily/',
					'Stickers', 'admin/jigsaw-daily/stickers/']
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

{!! BForm::open() !!}
{!! BForm::bind($sticker) !!}
<div class="basic-form">

	{!! BForm::hidden('url')->value($sticker->getCdnUrl(500)) !!}
	{!! BForm::hidden('file') !!}
	{!! BForm::hidden('widthScale') !!}
	{!! BForm::hidden('heightScale') !!}
	{!! BForm::hidden('xScale') !!}
	{!! BForm::hidden('yScale') !!}

	{!! BForm::text('Name', 'name') !!}

	<div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
		<label class="control-label">Image</label>

		<div class="help-block">

			@if($errors->has('file'))
				Please upload a valid sticker image
			@else
				The sticker image should be a JPEG or PNG created based off a 500px by 500px template image.
			@endif

		</div>

	</div>


	<div id="dropzone" class="dropzone">
		<div class="dz-message">
			<i class="icon fa-file-image-o"></i><br>
			Click or drop image here to upload
		</div>
	</div>

	{!! BForm::label('Sticker Preview') !!}
	<div id="stickerPreviewContainer">
		<div id="stickerPreview"></div>
	</div>

	{!! BForm::submit($title)->addClass('btn-default btn-outline') !!}

</div>
{!! BForm::close() !!}

@endsection





@push('scripts-footer')

<script src="vendor/draggabilly/draggabilly.js"></script>
<script>

	// Don't auto-create dropzones (we'll create these manually)
	Dropzone.autoDiscover = false;

	// Determine how much to scale the sticker
	var previewWidth = $('#stickerPreview').width();
	var previewHeight = $('#stickerPreview').height();

	$(function()
	{
		// -------------------------
		// Setup the dropzone
		// -------------------------
		var myDropzone = new Dropzone("div#dropzone", {
			url: "admin/jigsaw-daily/stickers/upload-image",
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
				alert(errorMessage.error);
			}
			else
			{
				alert('The sticker could not be uploaded');
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
			$('input[name=widthScale]').val(response.widthScale);
			$('input[name=heightScale]').val(response.heightScale);

			// Load the uploaded file onto the page
			updateStickerImage();
		});


		// -------------------------
		// Display the current sticker image
		// -------------------------
		updateStickerImage();

	});


	function updateStickerImage()
	{
		// Get the current image to be displayed
		var file = $('input[name=file]').val();
		if(file == '')
		{
			return;
		}

		// Remove any previous sticker
		$('#sticker').remove();

		// Get the new sticker data
		var stickerUrl = $('input[name=url]').val();
		var widthScale = $('input[name=widthScale]').val();
		var heightScale = $('input[name=heightScale]').val();
		var xScale = $('input[name=xScale]').val();
		var yScale = $('input[name=yScale]').val();

		// Add the sticker image
		$('#stickerPreview').append('<img id="sticker" src="' + stickerUrl + '">');
		$('#sticker').width(widthScale * previewWidth);
		$('#sticker').height(heightScale * previewHeight);
		$('#sticker').css('left', xScale * previewWidth);
		$('#sticker').css('top', yScale * previewHeight);

		// Make the sticker draggable
		var $draggable = $('#sticker').draggabilly(
		{
			containment: '#stickerPreviewContainer'
		});

		// Update the sticker position in the hidden form elements
		$draggable.on('dragEnd', function(event, pointer)
		{
			var position = $('#sticker').position();

			$('input[name=xScale]').val(position.left / previewWidth);
			$('input[name=yScale]').val(position.top / previewHeight);
		});
	}

</script>

@endpush
