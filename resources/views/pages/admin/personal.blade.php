@extends('layouts.admin.main')

@section('title', 'Thiết lập về tôi')
@section('title-content', 'Thông tin cá nhân')

@section('css')
<link rel="stylesheet" href="{{ url('public/assets/css/image-chosen.css') }}">
<link rel="stylesheet" href="{{ url('public/admin/css/personal.css') }}">
@stop

@section('content')
<div class="personal-wrapper">
    <form class="mb-5" action="{{ route('admin.personal.update') }}" method="POST" enctype="multipart/form-data">
        <div class="d-flex flex-column gap-4">
            @if (session('success'))
            <div class="alert alert-success m-0">
                {{ session('success') }}
            </div>
            @endif

            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="full-name" class="form-label">Họ và tên</label>
                <input type="text" class="form-control @if($errors->has('full_name')) is-invalid @endif" id="full-name" name="full_name" value="@if(old('title')){{ old('title') }}@else{{ $user->full_name }}@endif" autocomplete="off">
                @if ($errors->has('full_name'))
                <small class="text-danger">{{ $errors->first('full_name') }}</small>
                @endif
            </div>

            <div class="form-group image-chosen-wrapper">
                <label for="image-chosen-file" class="form-label">Ảnh đại diện</label>
                <input type="file" class="form-control d-none @if($errors->has('avatar')) is-invalid @endif" id="image-chosen-file" name="avatar" accept="image/*">
                <input type="checkbox" class="form-check-input d-none" id="is-remove-image" name="is_remove_avatar" value="Remove avatar">

                <div class="image-chosen-container gap-3">
                    <div class="image-chosen-content-wrapper">
                        <div class="image-chosen-content-container">
                            <img src="{{ url('public/assets/img/user-avatar/user-avatar-default.png') }}" class="image-chosen-content-default">
                            <div class="image-chosen-content">
                                @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->full_name }}">
                                @endif
                            </div>
                            <label for="image-chosen-file" class="image-choose">
                                <div class="icon-box">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button class="btn btn-danger gap-2 remove-image-btn" type="button">
                        <i class="fa-solid fa-trash"></i>
                        <span>Xóa ảnh</span>
                    </button>
                </div>

                @if ($errors->has('avatar'))
                <small class="text-danger">{{ $errors->first('avatar') }}</small>
                @elseif ($errors->any())
                <small class="text-warning">Nếu bạn đã chọn avatar trước đó, vui lòng chọn lại ảnh mới</small>
                @endif
            </div>

            <button type="submit" class="btn btn-success ms-auto gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Cập nhật thông tin</span>
            </button>
        </div>
    </form>
    <hr>
    <form class="my-5" action="{{ route('admin.auth.change-password') }}" method="POST">
        <div class="d-flex flex-column gap-4">
            <h2 class="main-header-title m-0">Đổi mật khẩu</h2>

            @csrf

            @if ($user->hasPassword())
            <div class="form-group">
                <label for="password-old" class="form-label">Mật khẩu cũ</label>
                <input type="password" class="form-control @if($errors->has('password_old')) is-invalid @endif" id="password-old" name="password_old" value="{{ old('password_old') }}">
                @if ($errors->has('password_old'))
                <small class="text-danger">{{ $errors->first('password_old') }}</small>
                @endif
            </div>
            @endif

            <div class="form-group">
                <label for="password-new" class="form-label">Mật khẩu mới</label>
                <input type="password" class="form-control @if($errors->has('password_new')) is-invalid @endif" id="password-new" name="password_new" value="{{ old('password_new') }}">
                @if ($errors->has('password_new'))
                <small class="text-danger">{{ $errors->first('password_new') }}</small>
                @endif
            </div>

            <div class="form-group">
                <label for="password-new-confirm" class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" class="form-control @if($errors->has('password_new_confirm')) is-invalid @endif" id="password-new-confirm" name="password_new_confirm" value="{{ old('password_new_confirm') }}">
                @if ($errors->has('password_new_confirm'))
                <small class="text-danger">{{ $errors->first('password_new_confirm') }}</small>
                @endif
            </div>

            <button type="submit" class="btn btn-success ms-auto gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Xác nhận</span>
            </button>
        </div>
    </form>
</div>
@stop

@section('js')
<script src="{{ url('public/assets/js/image-chosen.js') }}"></script>
@stop