# METWOGO Figma Screen Inventory — Stage A

## Audit status

- **Blocked at source**: the Stage A brief requires Figma MCP inspection of the design and prototype at `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:67` and `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:137`.
- **Observed blocker on August 6, 2026**: `mcp__codex_apps__figma._whoami` succeeded, but `mcp__codex_apps__figma._use_figma`, `_get_libraries`, and `_get_screenshot` all rejected the provided file key `eizUeoukhY7S4o3MiBOCDA` with `INVALID_ARGUMENT`.
- **Consequence**: node-level frame inspection, prototype-link traversal, and exact node IDs are not yet available from MCP, so this inventory is **provisional** and derived from the brief plus implemented mobile/Postman surfaces.

## Provisional screen inventory

| Screen | Figma node ID | Role | Purpose | Backend evidence | Current audit note |
|---|---|---|---|---|---|
| Splash | `UNRESOLVED` | Courier | App boot / session handoff | Required by brief only: `...pasted-text.txt:99` | No confirmed API dependency yet. |
| Login | `UNRESOLVED` | Courier | Authenticate courier | `routes/metwgo.php:20`, `app/Http/Controllers/Api/MetwGo/AuthController.php:25` | Implemented. |
| Forgot password / send OTP | `UNRESOLVED` | Courier | Start password reset | `routes/metwgo.php:21`, `app/Http/Controllers/Api/MetwGo/AuthController.php:94` | Implemented. |
| OTP verification | `UNRESOLVED` | Courier | Verify OTP for password reset / registration | `routes/metwgo.php:22`, `app/Http/Controllers/Api/MetwGo/AuthController.php:126` | Implemented, but Figma state mapping blocked. |
| OTP resend | `UNRESOLVED` | Courier | Resend verification code | `routes/metwgo.php:23`, `app/Http/Controllers/Api/MetwGo/AuthController.php:165` | Implemented. |
| Reset password | `UNRESOLVED` | Courier | Set new password | `routes/metwgo.php:24`, `app/Http/Controllers/Api/MetwGo/AuthController.php:205` | Implemented. |
| Registration step 1 | `UNRESOLVED` | Courier | Personal data | `routes/metwgo.php:28`, `app/Http/Controllers/Api/MetwGo/RegistrationController.php:161` | Implemented. |
| Registration step 2 | `UNRESOLVED` | Courier | Work info / warehouse / work types | `routes/metwgo.php:29`, `app/Http/Controllers/Api/MetwGo/RegistrationController.php:203` | Implemented. |
| Registration step 3 | `UNRESOLVED` | Courier | Vehicle / transport | `routes/metwgo.php:30`, `app/Http/Controllers/Api/MetwGo/RegistrationController.php:238` | Implemented. |
| Registration step 4 | `UNRESOLVED` | Courier | Service areas | `routes/metwgo.php:31`, `app/Http/Controllers/Api/MetwGo/RegistrationController.php:277` | Implemented. |
| Registration step 5 | `UNRESOLVED` | Courier | Document uploads | `routes/metwgo.php:32`, `app/Http/Controllers/Api/MetwGo/RegistrationController.php:318` | Implemented. |
| Registration status | `UNRESOLVED` | Courier | Review/approval state | `routes/metwgo.php:33`, `app/Http/Controllers/Api/MetwGo/RegistrationController.php:533` | Implemented. |
| Profile | `UNRESOLVED` | Courier | Read/update profile | `routes/metwgo.php:68`, `routes/metwgo.php:69`, `app/Http/Controllers/Api/MetwGo/ProfileController.php:28` | Implemented. |
| Work info | `UNRESOLVED` | Courier | Update account type / work types | `routes/metwgo.php:70`, `app/Http/Controllers/Api/MetwGo/ProfileController.php:77` | Implemented. |
| Transport | `UNRESOLVED` | Courier | Update vehicle/transport | `routes/metwgo.php:71`, `app/Http/Controllers/Api/MetwGo/ProfileController.php:109` | Implemented. |
| Service areas | `UNRESOLVED` | Courier | Update cities/governorates | `routes/metwgo.php:72`, `app/Http/Controllers/Api/MetwGo/ProfileController.php:143` | Implemented. |
| Upload documents | `UNRESOLVED` | Courier | Update verification docs | `routes/metwgo.php:73`, `app/Http/Controllers/Api/MetwGo/ProfileController.php:180` | Implemented. |
| Home dashboard | `UNRESOLVED` | Courier | Courier summary / incoming / active | `routes/metwgo.php:52`, `app/Http/Controllers/Api/MetwGo/HomeController.php:16` | Implemented. |
| Availability toggle | `UNRESOLVED` | Courier | Online/offline state | `routes/metwgo.php:53`, `app/Http/Controllers/Api/MetwGo/HomeController.php:69` | Implemented. |
| Incoming orders list | `UNRESOLVED` | Courier | Browse pending ecommerce order items | `routes/metwgo.php:56`, `app/Http/Controllers/Api/MetwGo/OrderController.php:20` | Implemented only for `pending` → accept/reject. |
| Order details | `UNRESOLVED` | Courier | Inspect order item details | `routes/metwgo.php:58`, `app/Http/Controllers/Api/MetwGo/OrderController.php:65` | Implemented. |
| Accept order | `UNRESOLVED` | Courier | Claim order item | `routes/metwgo.php:59`, `app/Http/Controllers/Api/MetwGo/OrderController.php:89` | Implemented but incomplete for locking/history/notifications. |
| Reject order | `UNRESOLVED` | Courier | Reject order item | `routes/metwgo.php:60`, `app/Http/Controllers/Api/MetwGo/OrderController.php:153` | Implemented but directly mutates order status to `rejected`. |
| Active order | `UNRESOLVED` | Courier | Current assigned order | `routes/metwgo.php:57`, `app/Http/Controllers/Api/MetwGo/OrderController.php:203` | Implemented. |
| Incoming shipping requests | `UNRESOLVED` | Courier | Browse shipment requests | `routes/metwgo.php:82`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:19` | Implemented only for `submitted` → assign/reject. |
| Shipping request details | `UNRESOLVED` | Courier | Inspect shipment request | `routes/metwgo.php:84`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:64` | Implemented. |
| Accept shipping request | `UNRESOLVED` | Courier | Claim shipment request | `routes/metwgo.php:85`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:88` | Implemented but incomplete for locking/history/notifications. |
| Reject shipping request | `UNRESOLVED` | Courier | Reject shipment request | `routes/metwgo.php:86`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:139` | Implemented but directly mutates request status to `rejected`. |
| Active shipping request | `UNRESOLVED` | Courier | Current assigned shipment request | `routes/metwgo.php:83`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:189` | Implemented. |
| Notifications badge | `UNRESOLVED` | Courier | Unread count only | `routes/metwgo.php:76`, `app/Http/Controllers/Api/MetwGo/NotificationController.php:16` | Count exists; list/read states missing. |
| Wallet summary | `UNRESOLVED` | Courier | Read wallet balance | `routes/metwgo.php:78`, `app/Http/Controllers/Api/MetwGo/WalletController.php:18` | Implemented. |
| Withdrawals | `UNRESOLVED` | Courier | Create withdrawal request | `routes/metwgo.php:79`, `app/Http/Controllers/Api/MetwGo/WalletController.php:33` | Create-only; no listing or admin lifecycle in mobile API. |
| Return flow entry | `UNRESOLVED` | Courier | Create return request for order | `routes/metwgo.php:94`, `app/Http/Controllers/Api/MetwGo/RepReturnController.php:23` | Implemented for create only. |
| Return reasons | `UNRESOLVED` | Courier | Load reason picker | `routes/metwgo.php:98`, `app/Http/Controllers/Api/MetwGo/RepReturnController.php:70` | Implemented. |
| Pending courier approvals | `UNRESOLVED` | Admin | Review new couriers | `routes/metwgo.php:102`, `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:19` | Implemented. |
| Approve courier | `UNRESOLVED` | Admin | Approve courier account | `routes/metwgo.php:103`, `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:39` | Implemented but duplicates dashboard logic. |
| Reject courier | `UNRESOLVED` | Admin | Reject courier account | `routes/metwgo.php:104`, `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:64` | Implemented but duplicates dashboard logic. |

## Screens explicitly required by the brief but not confirmed in current APIs

- No direct mobile API evidence yet for Splash, Settings, Support, Delete account, Notifications list/read-all, Transactions history, Pickup navigation, Arrival at pickup, Pickup confirmation, Ongoing pickup, Drop-off navigation, Arrival at drop-off, Drop-off confirmation, Delivery proof, Delivery success, Failed delivery, Cancellation, Warehouse receive/release flow, Scheduled-order details, or Multi-stop-order details: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:97`.

## Open questions

1. Is the provided Figma file key still valid for the connected account, or is the design now private/branched under a different key?
2. Are the prototype-only states (loading, empty, success, offline, confirmations, bottom sheets) represented in Figma frames that MCP can access once the key issue is fixed?
3. Are pickup/drop-off/warehouse flows intentionally deferred to Stage B, or are they already designed but missing from the backend?
