# Dashboard Integration Map — Stage A

## Representative / courier review integration

| Operation | Mobile/API surface | Dashboard surface | Current finding |
|---|---|---|---|
| List pending couriers | `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:19` | `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:16` + `resources/views/dashboard/admin/representatives/index.blade.php` | Same business domain, different controller logic, no shared approval action. |
| Approve courier | `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:39` | `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:84` | Duplicated status mutation. |
| Reject courier | `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:64` | `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:102` | Duplicated status mutation. |
| Suspend/reactivate courier | No MetwGo admin API route | `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:116`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:127` | Dashboard-only. |

## Order / shipment operational integration

| Operation | Mobile/API surface | Dashboard surface | Current finding |
|---|---|---|---|
| View courier-visible orders | `app/Http/Controllers/Api/MetwGo/OrderController.php:20` | Shipment-company order dashboard: `routes/web.php:560`, `resources/views/dashboard/shipment/orders.blade.php` | Visibility alignment not yet verified automatically. |
| Accept order | `app/Http/Controllers/Api/MetwGo/OrderController.php:89` | Admin/shipment/vendor dashboards depend on order status displays | No explicit dashboard refresh/notification/history side effect in controller. |
| Accept shipping request | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:88` | Admin shipment-request detail UI: `resources/views/dashboard/admin/shipment-requests/show.blade.php` | Mobile controller does not prove downstream dashboard synchronization. |

## Return integration

| Operation | Mobile/API surface | Dashboard surface | Current finding |
|---|---|---|---|
| Create return request | `app/Http/Controllers/Api/MetwGo/RepReturnController.php:23` | Admin returns, shipment returns, vendor returns | Shared return service exists; downstream dashboard traces still need end-to-end verification. |
| Admin return status update | `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:76` | `app/Http/Controllers/Dashboard/Admin/ReturnRequestController.php:122` | API uses shared `ReturnService`; dashboard uses its own direct logic. |
| Vendor seller-status update | none in MetwGo mobile | `app/Http/Controllers/Dashboard/Vendor/ReturnRequestController.php:131` | Dashboard-only. |

## Finance integration

| Operation | Mobile/API surface | Dashboard surface | Current finding |
|---|---|---|---|
| Wallet summary | `app/Http/Controllers/Api/MetwGo/WalletController.php:18` | Admin user wallet adjust UI: `app/Http/Controllers/Dashboard/Admin/UserController.php:84`, `resources/views/dashboard/admin/users.blade.php` | Read vs write split exists, but transaction-history parity is missing. |
| Withdrawal create | `app/Http/Controllers/Api/MetwGo/WalletController.php:33` | No audited dashboard approval controller found in Stage A | Incomplete lifecycle. |

## Summary

- The repo already has rich dashboard surfaces for admin, shipment company, and vendor users.
- The MetwGo mobile API currently covers only a thin courier slice of those workflows.
- Representative review and admin return handling are the clearest places where API and dashboard logic currently diverge.
