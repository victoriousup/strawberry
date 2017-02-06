@extends('layouts.admin.page', [

	'breadcrumbs' => [  'Administration', 'admin/',
						'Users', 'admin/users']
])

@inject('permissions', 'App\Utils\Permissions')

@section('content')

{!! BForm::open() !!}
{!! BForm::bind($user) !!}
<div class="basic-form">

	{!! BForm::text('Full Name', 'name') !!}

	{!! BForm::email('Email Address', 'email') !!}

	{{-- Initial password --}}
	@if(!$user->exists)

		{!! BForm::password('Default Password', 'password')->helpBlock('(Optional) Enter an initial default password for the user. A random password will be chosen if one is not entered.') !!}

	@endif

	{!! BForm::select('User Type', 'user_type')->options([0 => 'Admin', 1 => 'User'])->helpBlock('Admin users have access to the full administration system, including adding new users') !!}

	<div id="permissions">

		<label>Permissions</label>

		<!-- Main permissions -->
		@foreach($permissions::getPermissions() as $permission)

			{!! BForm::checkbox($permission->name, 'permission_' . $permission->id)->defaultCheckedState($user->hasPermission($permission->id))->addClass('mainPermission') !!}

			<!-- Sub-permissions -->
			<div class="subpermission_block" style="margin-left: 25px" data-id="{{ $permission->id }}">

				@foreach($permission->sub as $sub)

					{!! BForm::checkbox($sub->name, 'permission_' . $sub->id)->defaultCheckedState($user->hasPermission($sub->id))->addClass('subPermission') !!}

				@endforeach

			</div>

		@endforeach

	</div>

	{!! BForm::submit($title)->addClass('btn-default btn-outline') !!}

</div>
{!! BForm::close() !!}


@endsection


@push('scripts-footer')

<script>

	$(function()
	{
		// Update permissions when the user type is changed
		$('select[name=user_type]').change(function()
		{
			updatePermissions();
		});


		// Remove sub-permissions when main permission is unchecked
		$('.mainPermission').change(function()
		{
			updateSubpermissions();

			// Do nothing if checked
			if($(this).is(':checked'))
			{
				return;
			}

			// Get the permission name
			var permissionName = ($(this).attr('name')).replace('permission_', '');

			// Uncheck sub-elements
			$('input[name*="permission_' + permissionName + ':"').each(function()
			{
				$(this).attr('checked', false);
			});

		});

		// Make sure everything is set up correctly on first page load
		updatePermissions();
		updateSubpermissions();
	});


	function updatePermissions()
	{
		// Get the user type
		var userType = $('select[name=user_type]').val();

		// Hide the permissions if the user is admin
		if(userType == 0)
		{
			$('#permissions').hide();
		}
		else
		{
			$('#permissions').show();
		}
	}


	function updateSubpermissions()
	{
		$('.subpermission_block').each(function()
		{
			var id = $(this).data('id');

			if($('input[name=permission_' + id + ']').is(':checked'))
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
	}




</script>

@endpush