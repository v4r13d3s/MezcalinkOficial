@extends('layouts.app')

@section('content')
    <x-hero></x-hero>
    @livewire('featured-mezcales')
    <x-sabias-que></x-sabias-que>
@endsection