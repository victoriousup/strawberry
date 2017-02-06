@if($pack->exists)

	<div class="dropdown" style="margin-bottom: 25px;">
		<button class="btn btn-default dropdown-toggle btn-outline" type="button" data-toggle="dropdown">
			<i class="fa fa-gear"></i> Actions
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li><a href="admin/jigsaw-daily/packs/{{ $pack->id }}/edit"><i class="fa fa-pencil"></i> Edit</a></li>
			<li><a href="admin/jigsaw-daily/packs/{{ $pack->id }}/preview"><i class="fa fa-search"></i> Preview</a></li>
			<li><a href="admin/jigsaw-daily/organize/{{ $pack->id }}"><i class="fa fa-image"></i> Organize Images</a></li>
			<li><a href="admin/jigsaw-daily/packs/{{ $pack->id }}/promos"><i class="fa fa-bullhorn"></i> Manage Promos</a></li>
			<li><a href="admin/jigsaw-daily/packs/{{ $pack->id }}/store-preview-image"><i class="fa fa-camera"></i> Store Preview Image</a></li>
			<li><a href="admin/jigsaw-daily/packs/{{ $pack->id }}/download"><i class="fa fa-download"></i> Download Images</a></li>


		</ul>
	</div>

@endif
