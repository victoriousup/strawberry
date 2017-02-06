@extends('layouts.admin.page', [

	'title' => 'Daily Revenue for ' . $date->format('D F jS, Y'),

	'breadcrumbs' => [  'Jigsaw Daily', 'admin/jigsaw-daily/']

])


{{-- Head content --}}
@push('head')
<link rel="stylesheet" href="vendor/datepicker/bootstrap-datepicker.min.css">
@endpush


@section('content')

	{!! BForm::open()->get()->addClass('form-inline')->id('filter') !!}

	{!! BForm::select('Platform', 'platform')->options(['-1' => 'All', '0' => 'Apple', '1' => 'Android'])->select($platform) !!}

	<div class="form-group">
		<div class="input-group">
			<input type="text" class="form-control" name="date" value="{{ $date->format('m/d/Y') }}">
			<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
		</div>
	</div>

	<button type="submit" class="btn btn-default btn-outline">View Report</button>

	{!! BForm::close() !!}


	<div class="col-md-6">
		<div class="widget">
			<div class="widget-content padding-30">
				<div class="counter counter-lg">
					<div class="counter-label text-uppercase">Daily Revenue</div>
					<div class="counter-number-group">
						<span class="counter-icon margin-right-10 green-600">
							<i class="fa fa-bar-chart"></i>
						</span>
						<span class="counter-number">

							${{ number_format($revenue, 2) }}

						</span>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="col-md-6">
		<div class="widget">
			<div class="widget-content padding-30">
				<div class="counter counter-lg">
					<div class="counter-label text-uppercase">Total Transactions</div>
					<div class="counter-number-group">
						<span class="counter-icon margin-right-10 blue-600">
							<i class="fa fa-shopping-cart"></i>
						</span>
						<span class="counter-number">{{ $transactions }}</span>
					</div>
				</div>
			</div>
		</div>
	</div>


	<table class="table table-bordered" style="margin-top: 25px;">

		<tr>
			<th>Pack</th>
			<th>Transactions</th>
			<th>Average Price</th>
			<th>Revenue</th>
		</tr>

		@foreach($packs as $pack)

			<tr>
				<td>{{$pack->pack_name}}</td>
				<td>{{ $pack->transactions }}</td>
				<td>${{ number_format($pack->revenue / $pack->transactions, 2) }}</td>
				<td>${{ number_format($pack->revenue, 2) }}</td>
			</tr>

		@endforeach

	</table>


@endsection


@push('scripts-footer')

<script src="vendor/datepicker/bootstrap-datepicker.js"></script>
<script>

	$(function()
	{

		$('input[name=date]').datepicker();

	});

</script>

@endpush
