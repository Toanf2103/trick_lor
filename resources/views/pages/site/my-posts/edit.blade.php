@extends('layouts.site.header-only')

@section('title', 'Cập nhật bài đăng - Trick loR')

@section('css')
<link rel="stylesheet" href="{{ url('public/assets/css/prism.css') }}">
<link rel="stylesheet" href="{{ url('public/assets/css/virtual-select.min.css') }}">
<link rel="stylesheet" href="{{ url('public/site/css/post-detail.css') }}">

<link rel="stylesheet" href="{{ url('public/assets/css/image-chosen.css') }}">
<link rel="stylesheet" href="{{ url('public/site/css/my-posts/create.css') }}">

<script src="{{ url('public/assets/js/virtual-select.min.js') }}"></script>
<script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@stop

@section('content')
<livewire:site.my-posts.edit :allCategories="$listCategories" :postId="$postId" />
@stop

@section('js')
<script src="{{ url('public/assets/js/prism.js') }}"></script>
@stop