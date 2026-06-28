# Database Audit

Date: 2026-06-28

Scope reviewed:
- All migration files under `database/migrations`
- Models under `app/Models`
- `routes/api.php`
- Relevant API/dashboard controllers for users, vendors, warehouses, addresses, products, shipment orders, package addresses, and package details

No application logic changes were made as part of this audit.

## Existing Tables

### Users And Auth

- `users`: customer account table. Includes login fields, phone/email verification, profile image, notification flags, `user_number`, father/last name, birth date, gender, national id, mobile fields, account number, and soft deletes.
- `admins`: admin login table.
- `employees`: employee/admin staff table with role/permission integration.
- `vendors`: vendor auth table with `vendor_number`, contact details, logo, address text, latitude/longitude, active flags, and soft deletes.
- `shipment_companies`: shipment company auth/profile table with `company_number`, contact details, address, logo, price per km, estimated days, and soft deletes.
- `personal_access_tokens`, `password_reset_tokens`, `otp_codes`: auth/session/OTP support.
- Spatie permission tables: roles, permissions, model pivots, role permission pivot.

### Location And Address

- `countries`: normalized country lookup.
- `states`: normalized governorate/state lookup linked to countries.
- `cities`: normalized city lookup linked to states, now also linked to governorates.
- `zones`: normalized zone lookup. Intended to link to cities.
- `governorates`: newly added governorate lookup with `governorate_number`, `name_ar`, and `capital_city_id`.
- `locations`: older generic tree location table with `name`, `type`, `parent_id`, and `path`.
- `shipment_locations`: shipment-company coverage table storing `country`, `state`, `city`, and `zone` as JSON arrays.
- `user_addresses`: ecommerce saved delivery addresses for users.
- `package_addresses`: shipment pickup/dropoff addresses and saved shipment addresses.
- `vendor_branches`: vendor pickup branch addresses.
- `warehouses`: ecommerce/admin warehouse addresses.
- `stores`: missing in the live schema. There is a migration named `create_stores_table`, but it creates `warehouses`.

### Shipment Orders

- `package_types`, `sizes`, `delivery_types`, `consignment_types`: shipment package lookup tables.
- `package_details`: sender/receiver contact and pickup date/time.
- `package_addresses`: pickup/dropoff address records.
- `packages`: shipment package records linked to pickup/dropoff addresses and package details.
- `package_images`: package media.
- `carts`, `cart_items`, `cart_item_routes`: shipment cart and suggested/split route data before checkout.
- `orders`: shipment order header table.
- `order_items`: shipment order package line table.
- `order_item_routes`: shipment route legs with from/to location snapshots, city/state/zone FKs, leg type, company IDs, distance, and cost.
- `package_tracking`: shipment package tracking events.
- `payments`: shipment payment records.
- `reviews`, `favourites`: shipment company reviews/favourites.

### Ecommerce

- `main_categories`, `categories`, translations, brands, product sizes/colors/media/variants/related products.
- `products`: vendor products with branch, brand, category, pricing, dimensions, package fields, deposit fields, return flags, and shipment flags.
- `ecommerce_carts`, `ecommerce_cart_items`: ecommerce cart.
- `ecommerce_orders`: ecommerce order header with user address, warehouse, shipment company, payment fields, OTP, tracking, and delivery dates.
- `ecommerce_order_items`: ecommerce order lines with product, variant, pickup branch, shipment company, shipment price, status, vendor status, and cancellation fields.
- `return_requests`, `return_request_items`, `return_cash_backs`: ecommerce returns.
- `wallets`, `transactions`, `order_payment_records`: wallet and payment history.

### Settings And Support

- `promo_codes`, `notifications`, `jobs`, `failed_jobs`, `settings`, `configs`, `pages`, `bannars`, `cancel_reasons`, `contact_admins`, `whatsapp_templates`, commission and shipment pricing tables.

## Duplicated Address Structures

The project currently has multiple address shapes that overlap but are not interchangeable.

| Area | Table | Shape | Notes |
| --- | --- | --- | --- |
| Ecommerce user addresses | `user_addresses` | `country_id`, `state_id`, `city_id`, `zone_id`, `street_name`, `building`, `floor`, `landmark`, `address_type`, coordinates, `is_default`, `is_village` | Cleanest normalized user address structure. |
| Shipment pickup/dropoff | `package_addresses` | `location`, normalized location FKs, `landmark`, `phone`, `address`, coordinates, `type`, `user_id`, `is_saved` | Used both as immutable package address snapshots and saved shipment addresses. |
| Vendor branches | `vendor_branches` | `state_id`, `city_id`, `zone_id`, `street_main`, `street_sub`, building fields, coordinates | Similar to user addresses but lacks `country_id`, `landmark`, `address_type`, and `is_village`. |
| Warehouses | `warehouses` | `country_id`, `state_id`, `city_id`, `zone_id`, `street_name`, building/floor/landmark/type, coordinates | Very close to `user_addresses`, but separate table and controller. |
| Vendor profile | `vendors` | text `address`, string `latitude`, string `longitude` | Profile-level address duplicates branch location but with weaker typing. |
| Shipment company profile | `shipment_companies` | text `address` | Not normalized, no coordinates. |
| Shipment coverage | `shipment_locations` | JSON arrays of country/state/city/zone IDs | Coverage data duplicates normalized location references, but without FK constraints. |
| Generic location tree | `locations` | `name`, `type`, `parent_id`, `path` | Parallel location model separate from countries/states/cities/zones. |
| Route snapshots | `order_item_routes` | JSON `from_address`/`to_address`, coordinates, city/state/zone FKs | Useful as immutable shipment route snapshots, but overlaps package addresses. |

Key concern: `package_addresses` is doing two jobs: permanent shipment snapshots and user saved shipment addresses. That makes updates/deletes risky because the same table can represent historical order data and user-editable address book data.

## Missing Tables For Representative/Courier Module

There is no dedicated representative/courier module schema yet. Existing shipment companies and order routes can support shipment-company assignment, but not individual courier operations.

Recommended missing tables:

- `couriers` or `representatives`: profile/auth record for individual couriers. Should include name, phone, email, status, shipment company ownership, optional user/auth linkage, and current availability.
- `courier_profiles`: optional extension table for national ID, date of birth, emergency contact, profile image, and employment data if not stored directly on `couriers`.
- `courier_documents`: identity/license/vehicle documents, expiry dates, verification status, and uploaded file paths.
- `courier_vehicles`: vehicle type, plate number, license fields, capacity/weight limits, active status.
- `courier_zones` or `courier_service_areas`: normalized courier coverage/availability by country/state/city/zone.
- `courier_assignments`: assignment of a courier to `order_items`, `order_item_routes`, or `ecommerce_order_items`.
- `courier_assignment_status_events`: immutable status history for assignment lifecycle: assigned, accepted, arrived pickup, picked up, arrived handoff, handed off, out for delivery, delivered, failed, returned, cancelled.
- `courier_location_pings`: optional live tracking table for latitude/longitude/time/order context.
- `courier_shifts`: availability/shift windows and check-in/check-out.
- `courier_cash_collections`: COD/deposit/partial payment collection records linked to orders/order items/payment records.
- `courier_notifications` or reuse Laravel notifications with a dedicated notifiable courier model.

Suggested ownership:

- Shipment delivery courier assignment should primarily link to `order_item_routes` because split shipments already create pickup/dropoff legs.
- Ecommerce delivery courier assignment can link to `ecommerce_order_items` when each item can have its own pickup branch and shipment company, or to `ecommerce_orders` only if the whole order always moves together.

## Risky Migrations Or Naming Problems

- `2025_10_08_080433_create_stores_table.php` creates `warehouses`, not `stores`. The live schema has no `stores` table.
- `2025_09_29_082334_create_zones_table.php` uses `foreignIdFor(City::class)->constrained('states')`, so `zones.city_id` is constrained against `states.id` in the migration source. The column name and relationship expect `cities.id`.
- `2025_10_02_103713_add_columns_to_user_addresses.php` down migration calls `dropConstrainedForeignIdF(Zone::class)`, which is a typo and will fail during rollback.
- `2025_09_01_125905_create_favourites_table.php` and `2025_09_08_145300_create_favourite_table.php` both create `favourites`; this can break fresh migrations unless guarded by environment history.
- `2025_09_01_130600_create_reviews_table.php` and `2025_09_09_141713_create_reviews_table.php` both create `reviews`; same fresh-migration risk.
- `2025_09_01_120307_create_orders_table.php` uses `cascadeOnDelete('set null')` for nullable `cart_id`; the intended behavior should be `nullOnDelete()`.
- `2025_09_09_124316_add_column_to_orders_table.php` checks for misspelled `shipment_compnay_id` before adding `ShipmentCompany::class`, so the condition does not match the intended `shipment_company_id` column.
- `package_addresses` live schema does not include `is_village` or `name`, but `SavedAddressController` writes both. `PackageAddress` fillable also omits `is_village`/`name`.
- `packages` live schema has `size` and `piece`, while `Package` still has a `size()` relation using `size_id`; any code expecting `size_id` can fail after the later package-size refactor.
- Several migrations use `if (Schema::hasTable(...)) return;` inside create migrations (`vendors`, `products`). This hides drift in fresh/partial environments instead of failing loudly.
- Several rollback paths drop columns without guarding all columns or foreign keys consistently.
- Several source files contain mojibake comments/strings, suggesting encoding damage in older Arabic comments/seeders.
- `shipment_locations` stores FK-like IDs inside JSON arrays, so DB constraints cannot protect coverage data from deleted countries/states/cities/zones.
- `vendor_branches.zone_id` is required in the migration, but some controllers/seeders treat zone as nullable. This can cause seed/runtime failures.
- The governorate/city import added `cities.governorate_id` with `cascadeOnDelete`; deleting a governorate would delete cities, which could cascade into addresses/order history risk. Consider `nullOnDelete()` for historical safety before depending on this in production.

## Recommended Migration Order

For a representative/courier module, do the cleanup and new tables in this order:

1. Baseline safety migration
Add only missing indexes/constraints that are safe, and avoid destructive changes. Confirm fresh migration works in a clean database.

2. Fix naming and rollback hazards
Create corrective migrations for `zones.city_id` FK, typo rollback issues, duplicate create guards, and `stores` vs `warehouses` naming. Do not rename production tables until code paths are audited.

3. Stabilize location model
Pick one primary location hierarchy for new work: `countries -> states -> cities -> zones` plus `governorates` if required. Keep `locations` only as legacy coverage data or migrate it explicitly.

4. Introduce a canonical address snapshot pattern
Keep `user_addresses`, `vendor_branches`, and `warehouses` as owner-specific address books. For orders, keep immutable snapshots on order/package route tables. Do not reuse editable saved addresses as historical order addresses.

5. Add courier master tables
Create `couriers`, `courier_profiles`, `courier_documents`, `courier_vehicles`, and `courier_service_areas`.

6. Add assignment tables
Create `courier_assignments` after shipment/ecommerce order item tables are stable. Link shipment assignments to `order_item_routes`; link ecommerce assignments to `ecommerce_order_items` or `ecommerce_orders` based on fulfillment rules.

7. Add status/event tracking
Create `courier_assignment_status_events` and optionally `courier_location_pings`. These should be append-only for auditability.

8. Add payment/collection support
Create `courier_cash_collections` only after payment behavior is finalized for COD, partial payments, deposits, and returns.

9. Add API/admin controllers after schema settles
Only after the above tables exist should application logic be changed for courier login, availability, assignment, status updates, and tracking.

## Suggested Next Step

Before writing courier migrations, run a fresh database migration in a disposable DB and fix the migration failures first. The current migration history has several issues that may be hidden in the existing local database but will matter for CI, staging, and new developer setups.
