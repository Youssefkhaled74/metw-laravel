@extends('layouts.admin')
@section('title', 'طلبات الإرجاع - الموافقات')
@section('page-title', 'طلبات الإرجاع - الموافقات')
@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">طلبات الإرجاع بانتظار الموافقة</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($returns->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted mb-0">لا توجد طلبات إرجاع بانتظار الموافقة.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المندوب</th>
                                <th>السبب</th>
                                <th>التاريخ</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returns as $return)
                            <tr>
                                <td>{{ $return->id }}</td>
                                <td>{{ $return->order?->order_number }}</td>
                                <td>{{ $return->user->name ?? $return->user->username }}</td>
                                <td>{{ $return->representative?->first_name }} {{ $return->representative?->last_name }}</td>
                                <td>{{ $return->reason?->reason_text ?? $return->custom_reason_text }}</td>
                                <td>{{ $return->created_at?->toDateString() }}</td>
                                <td>
                                    <a href="{{ route('admin.return-orders.show', $return->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $returns->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
