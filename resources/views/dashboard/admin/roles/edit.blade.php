@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_role') ?? 'Edit Role')
@section('page-title', __('admin-dashboard.edit_role') ?? 'Edit Role')

@section('content')
    @include('dashboard.admin.roles._form', [
        'title' => __('admin-dashboard.edit_role_details') ?? 'Edit Role Details',
        'subtitle' => __('admin-dashboard.edit_role') ?? 'Edit Role',
        'formAction' => route('admin.roles.update', $role->id),
        'formMethod' => 'PATCH',
        'submitLabel' => __('admin-dashboard.update_role') ?? 'Update Role',
        'permissions' => $permissions,
        'selectedPermissions' => old('permissions', $role->permissions->pluck('name')->toArray()),
        'roleName' => old('name', $role->name),
    ])
@endsection



