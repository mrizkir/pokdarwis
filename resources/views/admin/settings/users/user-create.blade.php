@extends('layouts.app-backend')

@section('page-title','Users')

@section('page-header')
<div class="admin-page-header d-flex align-items-center justify-content-between">
  <div>
    <h1>Users</h1>
    <small class="text-muted">Mengelola user admin</small>
  </div>
  <div class="header-btn">
    <a href="{{ route('settings-users-superadmin.index') }}" class="round-btn">Close</a>
  </div>
</div>
@endsection

@section('main')
{!! html()->form('post', route('settings-users-superadmin.store'))->open() !!}
  @csrf

  <div class="card shadow-sm border-0">
    <div class="card-body">

      {{-- NAME --}}
      {!! html()->label('name', 'Nama User')->class('form-label') !!}
      {!! html()->text('name')->class('form-control')->value(old('name')) !!}
      @error('name') <div class="text-danger small">{{ $message }}</div> @enderror

      {{-- EMAIL --}}
      <div class="mt-3">
        {!! html()->label('email', 'Email')->class('form-label') !!}
        {!! html()->email('email')->class('form-control')->value(old('email')) !!}
        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      {{-- PASSWORD --}}
      <div class="mt-3">
        {!! html()->label('password', 'Password')->class('form-label') !!}
        {!! html()->password('password')->class('form-control') !!}
        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      {{-- ROLE --}}
      <div class="mt-3">
        {!! html()->label('role', 'Role')->class('form-label') !!}
        {!! html()->select('role', [
              'admin'     => 'admin',
              'pokdarwis' => 'pokdarwis',
            ])
            ->class('form-select')
            ->value(old('role','admin')) !!}
        @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

    </div>
    <div class="card-footer text-end">
      {!! html()->submit('Simpan')->class('btn btn-primary') !!}
    </div>
  </div>
{!! html()->form()->close() !!}
@endsection

@section('page-breadcrumb')
<nav class="admin-breadcrumb" aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Settings</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings-users-superadmin.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
</nav>
@endsection
