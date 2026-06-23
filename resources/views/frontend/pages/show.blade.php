@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title)
@section('content')
<div class="container py-5">
    <x-breadcrumb :items="[['label' => $page->title]]" />
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-4">{{ $page->title }}</h1>
            <div class="page-content">{!! $page->content !!}</div>
        </div>
    </div>
</div>
@endsection
