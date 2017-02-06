@extends('layouts.admin.page', [

	'title' => 'Bulk Uploader',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/',
					  'Packs', 'admin/jigsaw-daily/packs/',
					  $pack->name, 'admin/jigsaw-daily/packs/' . $pack->id . '/edit/',
					  'Promos', 'admin/jigsaw-daily/packs/' . $pack->id . '/promos/']
])


@push('head')

<script src="vendor/dropzone/dropzone.js"></script>
<link rel="stylesheet" href="vendor/dropzone/dropzone.css">
<style>

	#promo-preview img
	{
		padding-bottom: 15px;
	}

</style>

@endpush


@section('content')

	<div class="basic-form">

		{!! BForm::open() !!}
		<input type="hidden" name="promos" value="">

		{!! BForm::text('Name', 'name') !!}

		{!! BForm::select('Type', 'type')->options([0 => 'Both (Daily and Recommended)', 1 => 'Daily Pack', 2 => 'Recommended Pack'])->select(1) !!}

		{!! BForm::select('Tier', 'tier')->options([1 => 'Tier 1']) !!}

		<div id="promo-preview"></div>

		<div id="dropzone" class="dropzone">
			<div class="dz-message">
				<i class="icon fa-file-image-o"></i><br>
				Click or drop image here to upload
			</div>
		</div>

		{!! BForm::submit('Upload')->addClass('btn-default btn-outline') !!}

		{!! BForm::close() !!}

	</div>

@endsection


@push('scripts-footer')

<script>

	var promos = [];

	// Don't auto-create dropzones (we'll create these manually)
	Dropzone.autoDiscover = false;

	$(function()
	{
		// -------------------------
		// Setup the dropzone
		// -------------------------
		var myDropzone = new Dropzone("div#dropzone", {
			url: "admin/jigsaw-daily/packs/promos/upload",
			headers: {
				'X-CSRF-Token': '{{ csrf_token() }}'
			},
			uploadMultiple: false,
			parallelUploads: 1
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
			addPromoImage(response.url, response.filename, response.orgName);
		});

	});


	function addPromoImage(url, filename, orgName)
	{
		$('#promo-preview').append('<img src="' + url + '">');
		promos.push({url: url, filename: filename, orgName: orgName});
		$('input[name=promos]').val(JSON.stringify(promos));
	}


</script>

@endpush