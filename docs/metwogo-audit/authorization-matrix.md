# Authorization Matrix — Stage A

## Mobile API guards and observed rules

| Surface | Guard / middleware | Observed authorization rule | Evidence |
|---|---|---|---|
| Public auth + lookups | none | Open access | `routes/metwgo.php:19`, `routes/metwgo.php:42` |
| Courier mobile protected routes | `auth:sanctum` | User must own a representative profile for most operations | `routes/metwgo.php:51`, `app/Services/MetwGo/MetwGoCourierService.php:545` |
| Courier operational routes | `auth:sanctum` + controller checks | Representative must be `approved` and often `online` | `app/Services/MetwGo/MetwGoCourierService.php:60`, `app/Http/Controllers/Api/MetwGo/OrderController.php:27`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:26` |
| Admin courier review API | `auth:sanctum` | No additional policy/guard distinction visible in route or controller | `routes/metwgo.php:101`, `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:19` |
| Admin return API | `auth:sanctum` | No explicit policy/guard distinction visible in route or controller | `routes/metwgo.php:108`, `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:19` |

## Dashboard permissions compared

| Dashboard area | Permission/guard pattern | Evidence | Stage A implication |
|---|---|---|---|
| Admin representatives | `admin` middleware + employee permission checks | `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:16`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:69` | Stronger permission gating than mobile admin API. |
| Admin return requests | `admin` middleware + employee permission checks | `app/Http/Controllers/Dashboard/Admin/ReturnRequestController.php:17`, `app/Http/Controllers/Dashboard/Admin/ReturnRequestController.php:28` | Stronger permission gating than mobile admin API. |
| Shipment return requests | shipment dashboard routes | `routes/web.php:636` | No equivalent mobile shipment-company surface in `/api/metwgo`. |
| Vendor return requests | vendor dashboard routes | `routes/web.php:733` | No equivalent mobile vendor surface in `/api/metwgo`. |
| User wallet adjustment | admin-only dashboard action | `routes/web.php:200`, `app/Http/Controllers/Dashboard/Admin/UserController.php:84` | Mobile wallet flows do not expose this, which is good, but also means no courier transaction history. |

## Authorization mismatches to resolve later

1. Admin mobile review APIs currently appear to rely on `auth:sanctum` alone, while dashboard controllers enforce employee permissions.
2. Courier acceptance flows verify approval/availability but do not show ownership/policy classes for later stages because later stages are not implemented yet.
3. No mobile notification read/list authorization path exists, even though dashboard notification endpoints do exist for admin, shipment, and vendor users: `routes/web.php:258`, `routes/web.php:661`, `routes/web.php:747`.
