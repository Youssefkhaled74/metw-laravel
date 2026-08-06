# METWOGO Figma Statuses — Stage A

## Audit status

- Exact Figma state labels are blocked until the file key issue is resolved.
- This file captures **implemented backend statuses that the Figma design must reconcile with**.

## Courier account and availability statuses

| Domain | Statuses seen | Evidence |
|---|---|---|
| Courier approval state returned to mobile | `approved`, `pending_approval`, `rejected`, `suspended`, `incomplete` | `app/Services/MetwGo/MetwGoCourierService.php:46` |
| Underlying representative statuses | `approved`, `active`, `pending_review`, `pending_approval`, `rejected`, `suspended`, `inactive` | `app/Services/MetwGo/MetwGoCourierService.php:48`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:25` |
| Availability state | `online`, `offline` | `app/Services/MetwGo/MetwGoCourierService.php:173`, `app/Services/MetwGo/MetwGoCourierService.php:197` |

## Ecommerce order-item statuses currently used in mobile courier APIs

| Flow point | Statuses seen | Evidence |
|---|---|---|
| Incoming list filter | `pending` only | `app/Services/MetwGo/MetwGoCourierService.php:232` |
| Active order query | `accepted`, `pickup`, `on_way` | `app/Services/MetwGo/MetwGoCourierService.php:218` |
| Accept/start mutation | `pending` → `accepted` | `app/Http/Controllers/Api/MetwGo/OrderController.php:114`, `app/Http/Controllers/Api/MetwGo/OrderController.php:123` |
| Reject mutation | `pending` → `rejected` | `app/Http/Controllers/Api/MetwGo/OrderController.php:167`, `app/Http/Controllers/Api/MetwGo/OrderController.php:173` |

## Shipment-request statuses currently used in mobile courier APIs

| Flow point | Statuses seen | Evidence |
|---|---|---|
| Incoming list filter | `submitted` | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:109` |
| Active request query | active request exists, but later journey not exposed | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:189` |
| Accept/start mutation | `submitted` → `assigned` | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:109`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:116` |
| Reject mutation | `submitted` → `rejected` | `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:153`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:159` |

## Return and finance statuses visible to Stage A

| Domain | Statuses seen | Evidence |
|---|---|---|
| Wallet withdrawal | `pending` | `app/Http/Controllers/Api/MetwGo/WalletController.php:45` |
| Admin return API input | `approved`, `completed` | `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:79` |
| Dashboard return statuses | `requested`, `approved`, `pickup`, `processing`, `refunded`, `rejected`, `cancelled`, `completed` | `app/Http/Controllers/Dashboard/Vendor/ReturnRequestController.php:22` |

## Stage A status gaps against the brief

- The brief expects pickup, drop-off, proof, cancellation, return, warehouse, success, failure, empty, loading, and offline states to be traced: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:87`.
- Current mobile APIs expose only a subset of those operational statuses.
- Figma must be re-read once MCP access is restored to verify whether the design is aligned with these exact backend status names.
