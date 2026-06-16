@extends('layouts.admin')

@section('title', __('admin-dashboard.create_new_role') ?? 'Create New Role')
@section('page-title', __('admin-dashboard.create_new_role') ?? 'Create New Role')

@section('content')
    @include('dashboard.admin.roles._form', [
        'title' => __('admin-dashboard.role_details') ?? 'Role Details',
        'subtitle' => __('admin-dashboard.create_new_role') ?? 'Create New Role',
        'formAction' => route('admin.roles.store'),
        'formMethod' => 'POST',
        'submitLabel' => __('admin-dashboard.create_role') ?? 'Create Role',
        'permissions' => $permissions,
        'selectedPermissions' => old('permissions', []),
        'roleName' => old('name'),
    ])
@endsection



