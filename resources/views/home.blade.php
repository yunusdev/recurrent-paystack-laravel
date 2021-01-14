@extends('layouts.app')

@section('content')
<div class="container">
    <payment :raw_user="{{auth()->user()}}" raw_wallet_histories="{{$wallet_histories}}" paystack_pb_key="{{env('PAYSTACK_PUBLIC_KEY')}}"></payment>
</div>
@endsection
