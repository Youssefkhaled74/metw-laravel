# Missing Work Report — Stage A

## Executive summary

- Stage A confirms that the repo already contains a **partial METWOGO courier API** plus related admin return/review APIs.
- The current implementation does **not** yet satisfy the Stage A brief’s requirement to trace every Figma action across backend, dashboards, Postman, and tests: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:252`.
- The biggest blockers are:
  1. **Figma MCP file access failure** for the supplied design/prototype key.
  2. **Missing post-accept delivery lifecycle endpoints**.
  3. **Duplicate dashboard/API logic** for courier approval and return handling.
  4. **Sparse MetwGo-specific test coverage**.

## Missing / incomplete work by area

### Figma audit blockers

- Exact frame inventory, node IDs, prototype edges, state screens, and back-navigation are blocked because the supplied Figma key could not be opened through MCP, despite successful account authentication.

### Mobile API gaps against the brief

- Missing courier delivery lifecycle endpoints for pickup, arrival, pickup confirmation, drop-off, delivery proof, success, failure, cancellation, and return follow-up: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:115`.
- Missing notification feed/read endpoints; only unread count exists: `routes/metwgo.php:75`.
- Missing wallet transaction history and withdrawal lifecycle endpoints.
- Missing support, settings, delete-account, and warehouse-flow endpoints.
- Missing explicit scheduled-order and multi-stop-order mobile surfaces.

### Shared business logic gaps

- Courier approval/rejection is duplicated between API and dashboard controllers: `app/Http/Controllers/Api/Admin/AdminMetwGoController.php:39`, `app/Http/Controllers/Dashboard/Admin/RepresentativeController.php:84`.
- Admin return API uses `ReturnService`, while dashboard admin return handling still performs direct mutations: `app/Http/Controllers/Api/Admin/AdminReturnApiController.php:76`, `app/Http/Controllers/Dashboard/Admin/ReturnRequestController.php:122`.
- Mobile order/shipping-request acceptance bypasses the richer courier-system workflow services already present in the repo.

### Postman gaps

- Existing collection contains 44 requests but does not cover all Stage A brief folders or flows.
- No current request coverage for return-request creation, notification feed/read, transaction history, pickup/drop-off lifecycle, settings, support, or delete-account flows.

### Test gaps

- No MetwGo-focused feature suite was found for the implemented `/api/metwgo` endpoints.
- Existing representative feature coverage targets `/api/v1/representatives/...`, not the MetwGo mobile routes: `tests/Feature/RepresentativeModuleFeatureTest.php:68`.

## Open questions

1. Is the supplied Figma file key stale, private, or branch-specific?
2. Should courier order/shipping-request rejection mean “declined by this courier” or “globally rejected”?
3. Which existing dashboard/controller/services are the intended source of truth for post-accept delivery stages?
4. Is there already a withdrawal approval module elsewhere in the repo that should be reused in Stage B?
