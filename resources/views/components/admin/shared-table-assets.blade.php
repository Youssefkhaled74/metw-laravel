@once
    <style>
        .data-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }

        .data-card .table-wrap {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        }

        .search-shell .input-group-text,
        .search-shell .form-control,
        .filter-select-modern {
            border-color: #e5e7eb;
            min-height: 44px;
        }

        .search-shell .input-group-text {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .search-input-modern {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .search-input-modern:focus,
        .filter-select-modern:focus {
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.12);
            border-color: #93c5fd;
        }

        .filter-select-modern {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .rows-counter-badge {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
        }

        .data-table {
            width: 100%;
            table-layout: auto;
        }

        .data-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
            white-space: normal;
        }

        .data-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
            white-space: normal;
            word-break: normal;
        }

        .data-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 0 0 0 9999px rgba(248, 250, 252, 0.35);
            transform: translateY(-1px);
        }

        .sortable-col {
            user-select: none;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sortable-col:hover {
            background: #eef2ff;
            color: #1e3a8a;
        }

        .sortable-col .sort-indicator {
            margin-inline-start: 0.45rem;
            font-size: 0.8rem;
            opacity: 0.75;
        }

        .entity-logo {
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }

        .entity-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1d4ed8;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .entity-name {
            line-height: 1.25;
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .entity-subname {
            line-height: 1.2;
            max-width: 260px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding-inline: 0.85rem;
            border-radius: 999px;
            min-height: 36px;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
            border: 1px solid transparent;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fca5a5;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.42);
            flex: 0 0 auto;
        }

        .status-dot-active {
            color: #16a34a;
        }

        .status-dot-inactive {
            color: #dc2626;
        }

        .count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 32px;
            border-radius: 999px;
            font-weight: 700;
            padding: 0.2rem 0.7rem;
        }

        .packages-pill {
            background: #e0f2fe;
            color: #075985;
        }

        .orders-pill {
            background: #fef3c7;
            color: #92400e;
        }

        .actions-group {
            display: inline-flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 0.45rem;
        }

        .actions-group form {
            display: inline-flex;
            margin: 0;
        }

        .action-icon-btn {
            width: 38px;
            min-width: 38px;
            height: 38px;
            padding: 0;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
            border-radius: 999px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .empty-state {
            background: linear-gradient(180deg, #fafafa 0%, #f8fafc 100%);
            border: 1px dashed #d1d5db;
            border-radius: 18px;
        }

        .empty-icon {
            font-size: 2.25rem;
            color: #94a3b8;
        }

        @media (max-width: 991.98px) {
            .rows-counter-badge {
                margin-inline-start: auto;
            }
        }

        @media (max-width: 767.98px) {
            .data-table thead th,
            .data-table tbody td {
                padding: 0.8rem 0.85rem;
                font-size: 0.9rem;
            }

            .data-table .mobile-hide {
                display: none;
            }

            .status-pill,
            .action-icon-btn {
                width: 100%;
            }

            .actions-group {
                width: 100%;
                flex-wrap: wrap;
            }

            .actions-group .action-icon-btn {
                min-width: 0;
                width: auto;
                flex: 1 1 calc(50% - 0.25rem);
            }

            .search-shell .input-group-text,
            .search-shell .form-control,
            .filter-select-modern {
                min-height: 42px;
            }
        }
    </style>
@endonce

@once
    @section('scripts')
        @parent
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        placement: 'top',
                        fallbackPlacements: [],
                        offset: [0, 10],
                        container: 'body'
                    });
                });
            });
        </script>
    @endsection
@endonce
