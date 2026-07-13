@extends('layouts.admin')
@section('title', 'شكاوى الإرجاع')
@section('page-title', 'شكاوى الإرجاع')
@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">شكاوى الإرجاع</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($returns->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted mb-0">لا توجد شكاوى إرجاع.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>سبب الرفض</th>
                                <th>عدد الشكاوى</th>
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
                                <td>{{ $return->rejection_reason }}</td>
                                <td>{{ $return->complaints->count() }}</td>
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
