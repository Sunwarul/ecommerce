@extends('product::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('product.name') !!}</p>
@endsection
