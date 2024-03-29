@extends('layouts.user')
@section('content')
<div class="content pl-32 pr-8 mt-4 pb-20" id="content-full">
	<client-create-loan-account 
		name="{{$client->full_name}}" 
		client_id="{{$client->client_id}}" 
		businesses = "{{$client->businesses}}"
		household_income = "{{$client->household_income}}">
	</client-create-loan-account>
</div>
@endsection