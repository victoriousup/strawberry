<div id="packPreview" class="panel">

	<div class="panel-body">

		{!! BForm::open()->addClass('form-inline') !!}

		{!! BForm::select('Pack', 'pack')->options($packs)->select($packId) !!}

		<a href="" class="btn btn-primary btn-edit"><i class="fa-pencil"></i> Edit</a>
		<a href="" class="btn btn-primary btn-new"><i class="fa-plus"></i> New Pack</a>

		{!! BForm::close() !!}

		<div class="panel-actions">
			<a class="panel-action icon fa-chevron-up increase-size"></a>
			<a class="panel-action icon fa-chevron-down decrease-size"></a>
			<a class="panel-action icon fa-expand break-out"></a>
		</div>

		<div class="clearfix"></div>

		<div class="photos"></div>

	</div>

</div>