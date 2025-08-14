@extends('layouts.app')

@section('content')
    <x-hero></x-hero>
    @livewire('featured-mezcales')
    <x-sabias-que></x-sabias-que>
    <x-proceso-elaboracion></x-proceso-elaboracion>
    <x-conoce-marcas></x-conoce-marcas>
    <x-conoce-agaves></x-conoce-agaves>
@endsection