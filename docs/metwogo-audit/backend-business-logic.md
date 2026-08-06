# Backend Business Logic Audit — Stage A

## Shared services already in play

| Shared class | Current role | Evidence |
|---|---|---|
| `App\Services\MetwGo\MetwGoCourierService` | Courier lookup, approval mapping, availability metadata, incoming/active query builders, profile formatting, shipping-request formatting, OTP cooldowns | `app/Services/MetwGo/MetwGoCourierService.php:25` |
| `App\Services\MetwGo\ReturnService` | Return-request creation, refund calculation, wallet credit, status logs, notifications | `app/Services/MetwGo/ReturnService.php:16` |
| `App\Services\CourierSystem\CourierAssignmentService` | Existing courier-system assignment workflow outside MetwGo surface | `app/Services/CourierSystem/CourierAssignmentService.php:1` |
| `App\Services\CourierSystem\CourierDispatchService` | Existing dispatch orchestration outside MetwGo surface | `app/Services/CourierSystem/CourierDispatchService.php:1` |
| `App\Services\CourierSystem\CourierMatchService` | Existing matching logic outside MetwGo surface | `app/Services/CourierSystem/CourierMatchService.php:1` |
| `App\Services\CourierSystem\CourierRequestWorkflowService` | Existing request/path workflow with richer path confirmation and execution stages | `app/Services/CourierSystem/CourierRequestWorkflowService.php:25` |

## Strong findings

### 1. MetwGo mobile order acceptance bypasses richer courier-system workflow

- `OrderController@start` updates the `order_items` row directly and sets metadata on the parent order: `app/Http/Controllers/Api/MetwGo/OrderController.php:114`.
- It does **not** call the existing courier-system services that already manage dispatch/path workflow: `app/Services/CourierSystem/CourierAssignmentService.php:1`, `app/Services/CourierSystem/CourierRequestWorkflowService.php:34`.
- There is no transaction or lock around claim logic in `OrderController@start`: `app/Http/Controllers/Api/MetwGo/OrderController.php:114`.

### 2. MetwGo shipping-request acceptance also bypasses richer workflow

- `ShippingRequestController@start` directly sets `representative_id`, `status`, and `accepted_at`: `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:109`.
- The repo already contains a path-based request workflow service with path evaluation, client selection, advance payment, and execution stages: `app/Services/CourierSystem/CourierRequestWorkflowService.php:34`.
- The mobile API does not currently reuse that richer shared logic.

### 3. Admin representative approval logic is duplicated between API and dashboard

- API approval/rejection: `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:39`, `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:64`
- Dashboard approval/rejection/suspend/reactivate: `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:84`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:102`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:116`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:127`
- Both mutate representative status directly instead of delegating to a shared action/service, which conflicts with the brief’s no-duplicate-logic rule: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:50`.

### 4. Wallet withdrawal creation is incomplete as a business flow

- Mobile withdrawal only creates a `wallet_withdrawals` record if current balance is sufficient: `app/Http/Controllers/Api/MetwGo/WalletController.php:33`.
- There is no visible debit/hold transaction, no notification, no admin approval path in the mobile API, and no list/readback endpoint for courier withdrawals.

### 5. Return flow is more mature than courier delivery flow

- Mobile return creation uses `ReturnService`: `app/Http/Controllers/Api/MetwGo/RepReturnController.php:23`.
- Admin return updates also use `ReturnService`, including refund calculation and wallet credit: `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:76`, `app/Services/MetwGo/ReturnService.php:96`, `app/Services/MetwGo/ReturnService.php:120`.
- This is the clearest current example of shared business logic spanning creation + admin handling.

## Missing shared-logic alignments for Stage B

1. Courier acceptance/rejection actions should be extracted and reused across mobile API and dashboard/admin workflow.
2. Availability should move from metadata mutation toward a dedicated action if dashboards also need to manage it.
3. Withdrawal creation and approval need one shared lifecycle service with transaction records.
4. Notification list/read flows should align with existing dashboard notification behavior in `routes/web.php:258`, `routes/web.php:661`, and `routes/web.php:747`.
