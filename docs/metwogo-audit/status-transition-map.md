# Status Transition Map — Stage A

## Representative lifecycle

| From | To | Trigger | Evidence | Gap |
|---|---|---|---|---|
| `pending_review` / `pending_approval` | `approved`/`active` surfaced as `approved` | Admin approval | `app/Services/MetwGo/MetwGoCourierService.php:46`, `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:39`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:84` | API and dashboard transitions are duplicated. |
| `pending_approval` | `rejected` | Admin rejection | `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:64`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:102` | Shared action missing. |
| `approved`/`active` | `suspended` | Dashboard suspend | `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:116` | No matching mobile/admin API endpoint. |
| `suspended` | `approved` | Dashboard reactivate | `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:127` | No matching mobile/admin API endpoint. |

## Mobile order-item lifecycle currently exposed

| From | To | Trigger | Evidence | Gap |
|---|---|---|---|---|
| `pending` | `accepted` | Courier accepts order | `app/Http/Controllers/Api/MetwGo/OrderController.php:114` | No lock, no dispatch service, no dashboard sync proof. |
| `pending` | `rejected` | Courier rejects order | `app/Http/Controllers/Api/MetwGo/OrderController.php:167` | Rejection semantics may hide order from all other couriers. |
| `accepted` | `pickup` / `on_way` | Not implemented in mobile API, only visible as queryable active statuses | `app/Services/MetwGo/MetwGoCourierService.php:218` | Missing endpoints for pickup/drop-off stages. |

## Shipment-request lifecycle currently exposed

| From | To | Trigger | Evidence | Gap |
|---|---|---|---|---|
| `submitted` | `assigned` | Courier accepts request | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:109` | No lock, no path workflow reuse. |
| `submitted` | `rejected` | Courier rejects request | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:153` | May close request instead of rotating to other couriers. |
| `assigned` | later execution states | Not exposed in mobile API | No `/pickup`, `/arrive`, `/complete`, `/fail` routes in `routes/metwgo.php:55` | Major Stage B gap. |

## Return lifecycle currently exposed

| From | To | Trigger | Evidence | Gap |
|---|---|---|---|---|
| order delivered? | return request created | Courier creates return request | `app/Http/Controllers/Api/MetwGo/RepReturnController.php:23` | Preconditions depend on request validation; Figma state unknown. |
| pending/admin-review states | `approved` / `completed` | Admin return API | `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:79` | Mobile courier follow-up states not exposed. |
| complaint state | `approved` via reactivate | Admin reactivate | `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:110` | No mobile complaint feed endpoint. |

## Wallet/finance lifecycle currently exposed

| From | To | Trigger | Evidence | Gap |
|---|---|---|---|---|
| wallet balance available | withdrawal `pending` | Courier submits withdrawal | `app/Http/Controllers/Api/MetwGo/WalletController.php:45` | No approval/rejection/list/detail flow in mobile API. |
| return completed | wallet credited | Admin return completion | `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:84`, `app/Services/MetwGo/ReturnService.php:120` | Wallet transaction visibility not exposed to mobile courier. |
