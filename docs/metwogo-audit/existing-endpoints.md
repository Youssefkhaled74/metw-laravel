# Existing METWOGO Endpoints — Stage A

## Scope

- Mobile routes audited from `routes/metwgo.php:17`.
- Shared legacy representative routes compared from `routes/api.php:82`.
- Dashboard alignment checked from `routes/web.php:462`, `routes/web.php:554`, `routes/web.php:636`, and `routes/web.php:733`.

## Implemented `/api/metwgo` surface

| Method | URI | Controller@method | Notes |
|---|---|---|---|
| POST | `/api/metwgo/auth/login` | `AuthController@login` | Courier login with approval-state gating. |
| POST | `/api/metwgo/auth/forgot-password/send-otp` | `AuthController@sendForgotPasswordOtp` | Password-reset OTP start. |
| POST | `/api/metwgo/auth/otp/verify` | `AuthController@verifyOtp` | OTP verify. |
| POST | `/api/metwgo/auth/otp/resend` | `AuthController@resendOtp` | OTP resend. |
| POST | `/api/metwgo/auth/password/reset` | `AuthController@resetPassword` | Password reset completion. |
| POST | `/api/metwgo/auth/register` | `RegistrationController@register` | All-in-one registration. |
| POST | `/api/metwgo/auth/simple-register` | `RegistrationController@simpleRegister` | Reduced registration path. |
| POST | `/api/metwgo/auth/register/step-1` | `RegistrationController@stepOne` | Registration step. |
| POST | `/api/metwgo/auth/register/step-2` | `RegistrationController@stepTwo` | Registration step. |
| POST | `/api/metwgo/auth/register/step-3` | `RegistrationController@stepThree` | Registration step. |
| POST | `/api/metwgo/auth/register/step-4` | `RegistrationController@stepFour` | Registration step. |
| POST | `/api/metwgo/auth/register/step-5` | `RegistrationController@stepFive` | Registration step. |
| GET | `/api/metwgo/auth/registration/status` | `RegistrationController@registrationStatus` | Approval/progress summary. |
| POST | `/api/metwgo/auth/logout` | `AuthController@logout` | Authenticated. |
| POST | `/api/metwgo/auth/password/change` | `AuthController@changePassword` | Authenticated. |
| GET | `/api/metwgo/warehouses` | `LookupController@warehouses` | Public lookup. |
| GET | `/api/metwgo/rejection-reasons` | `LookupController@rejectionReasons` | Public lookup. |
| GET | `/api/metwgo/lookups/transport-types` | `LookupController@transportTypes` | Public lookup. |
| GET | `/api/metwgo/lookups/governorates` | `LookupController@governorates` | Public lookup. |
| GET | `/api/metwgo/lookups/cities` | `LookupController@cities` | Public lookup. |
| GET | `/api/metwgo/home` | `HomeController@index` | Courier dashboard aggregate. |
| POST | `/api/metwgo/courier/availability` | `HomeController@setAvailability` | Online/offline toggle. |
| GET | `/api/metwgo/orders/incoming` | `OrderController@incoming` | Pending order items only. |
| GET | `/api/metwgo/orders/active` | `OrderController@active` | Current active order. |
| GET | `/api/metwgo/orders/{orderId}` | `OrderController@show` | Order details. |
| POST | `/api/metwgo/orders/{orderId}/start` | `OrderController@start` | Accept order. |
| POST | `/api/metwgo/orders/{orderId}/reject` | `OrderController@reject` | Reject order. |
| GET | `/api/metwgo/profile` | `ProfileController@show` | Profile read. |
| PUT | `/api/metwgo/profile` | `ProfileController@update` | Profile update. |
| PUT | `/api/metwgo/profile/work-info` | `ProfileController@updateWorkInfo` | Work info update. |
| PUT | `/api/metwgo/profile/transport` | `ProfileController@updateTransport` | Transport update. |
| PUT | `/api/metwgo/profile/service-areas` | `ProfileController@updateServiceAreas` | Service-area update. |
| POST | `/api/metwgo/profile/documents` | `ProfileController@uploadDocuments` | Documents update. |
| POST | `/api/metwgo/profile/complete` | `RegistrationController@completeProfile` | Phase-4 style completion. |
| GET | `/api/metwgo/notifications/count` | `NotificationController@count` | Count only. |
| GET | `/api/metwgo/wallet/summary` | `WalletController@summary` | Balance only. |
| POST | `/api/metwgo/wallet/withdrawals` | `WalletController@withdrawals` | Create withdrawal request. |
| GET | `/api/metwgo/shipping-requests/incoming` | `ShippingRequestController@incoming` | Submitted requests only. |
| GET | `/api/metwgo/shipping-requests/active` | `ShippingRequestController@active` | Current active shipping request. |
| GET | `/api/metwgo/shipping-requests/{requestId}` | `ShippingRequestController@show` | Details. |
| POST | `/api/metwgo/shipping-requests/{requestId}/start` | `ShippingRequestController@start` | Accept request. |
| POST | `/api/metwgo/shipping-requests/{requestId}/reject` | `ShippingRequestController@reject` | Reject request. |
| POST | `/api/metwgo/orders/{orderId}/return-request` | `RepReturnController@create` | Create return request. |
| GET | `/api/metwgo/return-reasons` | `RepReturnController@reasons` | Return-reason lookup. |
| GET | `/api/metwgo/admin/metwgo/couriers/pending` | `AdminMetwGoController@pendingCouriers` | Admin review API. |
| PUT | `/api/metwgo/admin/metwgo/couriers/{representative}/approve` | `AdminMetwGoController@approve` | Admin review API. |
| PUT | `/api/metwgo/admin/metwgo/couriers/{representative}/reject` | `AdminMetwGoController@reject` | Admin review API. |
| GET | `/api/metwgo/admin/returns/pending` | `AdminReturnApiController@pendingReturns` | Admin return workflow. |
| GET | `/api/metwgo/admin/returns/complaints` | `AdminReturnApiController@complaints` | Admin return workflow. |
| GET | `/api/metwgo/admin/returns/{orderReturnRequest}` | `AdminReturnApiController@show` | Admin return workflow. |
| PUT | `/api/metwgo/admin/returns/{orderReturnRequest}/status` | `AdminReturnApiController@updateStatus` | Admin return workflow. |
| POST | `/api/metwgo/admin/returns/{orderReturnRequest}/reactivate` | `AdminReturnApiController@reactivate` | Admin return workflow. |

## Form Request coverage observed

- Mobile-specific Form Requests exist for login, OTP, password reset, registration, service areas, transport, withdrawals, documents, and return creation in `app/Http/Requests/Api/MetwGo`.
- Gaps remain where inline `$request->validate()` is still used in state-changing controllers, including `HomeController@setAvailability`, `OrderController@start`, `OrderController@reject`, `ShippingRequestController@start`, and `ShippingRequestController@reject`: `app/Http/Controllers/Api/MetwGo/HomeController.php:73`, `app/Http/Controllers/Api/MetwGo/OrderController.php:93`, `app/Http/Controllers/Api/MetwGo/OrderController.php:160`, `app/Http/Controllers/Api/MetwGo/ShippingRequestController.php:146`.

## Postman coverage

- Existing collection count: **44 requests** from `docs/postman/metwgo-courier-auth-registration.postman_collection.json`.
- The current collection covers auth, registration, profile, home, orders, shipping requests, wallet summary/withdrawal, and basic admin review.
- No dedicated Postman folders currently exist for the brief’s pickup/drop-off, notifications feed, settings, support, delete account, scheduled orders, multi-stop orders, warehouse flow, or full return lifecycle: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:491`.
