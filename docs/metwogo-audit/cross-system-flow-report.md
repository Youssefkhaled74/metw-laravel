# Cross-System Flow Report — Stage A

## Flow verification summary

| Flow | Stage A result | Evidence | Notes |
|---|---|---|---|
| Order created by vendor/admin/shipment company | `PARTIAL` | Dashboard order routes in `routes/web.php:560` | Creation exists outside MetwGo mobile slice; not fully traced to courier mobile entry yet. |
| Order becomes available to representatives | `PARTIAL` | `app/Services/MetwGo/MetwGoCourierService.php:232` | Query exists, but dispatch/matching reuse is unclear. |
| Representative views order | `READY` | `app/Http/Controllers/Api/MetwGo/OrderController.php:20`, `app/Http/Controllers/Api/MetwGo/OrderController.php:65` | Read surface exists. |
| Representative accepts order | `INCOMPLETE` | `app/Http/Controllers/Api/MetwGo/OrderController.php:89` | No locking / no downstream proof. |
| Representative rejects order | `INCOMPLETE` | `app/Http/Controllers/Api/MetwGo/OrderController.php:153` | Rejecting sets order `rejected`, which may be too final. |
| Pickup begins | `MISSING` | No route in `routes/metwgo.php:55` | Required by brief. |
| Arrive at pickup | `MISSING` | No route in `routes/metwgo.php:55` | Required by brief. |
| Pickup confirmed | `MISSING` | No route in `routes/metwgo.php:55` | Required by brief. |
| Delivery begins / drop-off | `MISSING` | No route in `routes/metwgo.php:55` | Required by brief. |
| Delivery succeeds / fails | `MISSING` | No route in `routes/metwgo.php:55` | Required by brief. |
| Shipping request visible | `READY` | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:19` | Read surface exists. |
| Shipping request accepted | `INCOMPLETE` | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:88` | No path workflow reuse / no locking. |
| Shipping request rejected | `INCOMPLETE` | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:139` | Same finality concern as orders. |
| Return request created | `READY` | `app/Http/Controllers/Api/MetwGo/RepReturnController.php:23` | Shared service exists. |
| Wallet transaction created | `PARTIAL` | `app/Services/MetwGo/ReturnService.php:120`, `app/Http/Controllers/Dashboard/Admin/UserController.php:117` | Return flow and admin wallet adjustment create transactions; courier withdrawal does not. |
| Commission calculated | `UNVERIFIED` | No MetwGo mobile flow evidence in Stage A | Needs deeper Stage B/DB audit. |
| Withdrawal requested | `READY` | `app/Http/Controllers/Api/MetwGo/WalletController.php:33` | Create-only. |
| Withdrawal approved/rejected | `MISSING` | No audited mobile endpoint | Required by brief. |
| Notifications sent and marked read | `INCOMPLETE` | Count only in `app/Http/Controllers/Api/MetwGo/NotificationController.php:16`; dashboard read endpoints in `routes/web.php:258` | Mobile list/read endpoints missing. |

## High-risk cross-system gaps

1. **Assignment concurrency risk**: both acceptance endpoints rely on plain `whereNull('representative_id')` / `firstOrFail()` without explicit transaction + row locking: `app/Http/Controllers/Api/MetwGo/OrderController.php:114`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:109`.
2. **Dashboard sync risk**: acceptance flows do not explicitly create history entries, emit notifications, or invoke shared workflow services already present in the repo.
3. **Status semantics risk**: courier rejection currently changes the core order/request status to `rejected`, which may conflict with a marketplace-style “declined by one courier but still available to others” model.

## Stage A conclusion

- Stage A confirms a **working thin-slice courier API**, not a complete mobile-to-dashboard workflow.
- The largest uncovered flows are post-acceptance delivery steps and notification/finance lifecycle completion.
