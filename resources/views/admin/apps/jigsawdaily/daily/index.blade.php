@extends('layouts.admin.page', [

	'title' => 'Daily Jigsaws',

	'breadcrumbs' => ['Jigsaw Daily', 'admin/jigsaw-daily/']

])


@push('head')
	<link rel="stylesheet" href="apps/jigsaw-daily/css/daily.css">
@endpush


@section('content')

<h2>
	<a href="admin/jigsaw-daily/daily/{{ $calendar->getMonthStartDate()->subMonth()->format('m/Y/') }}"><i class="icon fa-angle-left"></i></a>
	{{ $calendar->getMonthName() }} {{ $calendar->getYear() }}
	<a href="admin/jigsaw-daily/daily/{{ $calendar->getMonthStartDate()->addMonth()->format('m/Y/') }}"><i class="icon fa-angle-right"></i></a>
</h2>

<div class="calendar">

	@foreach($dates as $date)

		<div class="date">
			<div class="inner">

				{{-- Image selected for date --}}
				@if(sizeof($date->events) > 0)

					<div class="overlay">

						<p>{{ $date->events[0]->photo()->first()->pack()->first()->name }}</p>

						<a href="admin/jigsaw-daily/daily/packs/{{ $date->date->format('Y-m-d') }}/" class="btn btn-primary btn-sm">
							<i class="fa-refresh"></i> Replace
						</a>
					</div>

					<img class="image" src="{{ $date->events[0]->photo()->first()->getFileUrl(200) }}">

				{{-- No image selected yet --}}
				@else

					<a href="admin/jigsaw-daily/daily/packs/{{ $date->date->format('Y-m-d') }}/" class="overlay-select"></a>

				@endif


			</div>
			<div class="day">{{ $date->date->day }}</div>
		</div>

	@endforeach

</div>

@endsection
