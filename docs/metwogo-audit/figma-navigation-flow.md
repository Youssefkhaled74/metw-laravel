# METWOGO Figma Navigation Flow — Stage A

## Audit status

- The brief requires following prototype connections and alternative paths: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:137`.
- Figma MCP could authenticate the account but could not read the supplied design/prototype key, so exact prototype edges and back-navigation behavior remain blocked.
- The flow below is therefore **backend-informed**, based on route groups in `routes/metwgo.php:17` and request grouping in `docs/postman/metwgo-courier-auth-registration.postman_collection.json`.

## Confirmed backend-informed flow

1. **Auth**
   - Login → home: `routes/metwgo.php:20`, `routes/metwgo.php:52`
   - Forgot password → OTP verify → reset password: `routes/metwgo.php:21`, `routes/metwgo.php:22`, `routes/metwgo.php:24`
2. **Registration**
   - Step 1 personal info → Step 2 work info → Step 3 vehicle → Step 4 service areas → Step 5 documents → registration status: `routes/metwgo.php:28`, `routes/metwgo.php:29`, `routes/metwgo.php:30`, `routes/metwgo.php:31`, `routes/metwgo.php:32`, `routes/metwgo.php:33`
3. **Profile maintenance**
   - Profile root → work info / transport / service areas / documents: `routes/metwgo.php:68`, `routes/metwgo.php:70`, `routes/metwgo.php:71`, `routes/metwgo.php:72`, `routes/metwgo.php:73`
4. **Courier work surface**
   - Home → incoming orders → order details → accept/reject → active order: `routes/metwgo.php:52`, `routes/metwgo.php:56`, `routes/metwgo.php:58`, `routes/metwgo.php:59`, `routes/metwgo.php:60`, `routes/metwgo.php:57`
   - Home → incoming shipping requests → request details → accept/reject → active shipping request: `routes/metwgo.php:82`, `routes/metwgo.php:84`, `routes/metwgo.php:85`, `routes/metwgo.php:86`, `routes/metwgo.php:83`
5. **Finance and support-like endpoints**
   - Home → wallet summary → withdrawal create: `routes/metwgo.php:78`, `routes/metwgo.php:79`
   - Order details → return request create: `routes/metwgo.php:94`
6. **Admin review**
   - Pending couriers → approve/reject: `routes/metwgo.php:102`, `routes/metwgo.php:103`, `routes/metwgo.php:104`
   - Pending returns / complaints → return details → status update / reactivate: `routes/metwgo.php:109`, `routes/metwgo.php:110`, `routes/metwgo.php:111`, `routes/metwgo.php:112`, `routes/metwgo.php:113`

## Missing prototype-confirmed paths

- No MCP-confirmed edges yet for splash → login, login → registration, home tab switches, modal confirmations, back-stack rules, or error-state retries.
- No backend path exists for the brief’s pickup/drop-off progression, so those prototype branches cannot currently map to routes: `C:\Users\redaco\.codex\attachments\15030c51-150b-40be-8b85-ac7871040f61\pasted-text.txt:115`.

## Stage A navigation findings

- The implemented mobile API currently supports **registration**, **profile completion**, **basic courier dispatch claim/reject**, **wallet balance/withdrawal creation**, and **return-request creation**.
- The implemented API does **not** currently expose a full delivery journey after acceptance; both order and shipment request flows stop at “active”.
- The only notification surface confirmed in mobile API is unread count, not a browsable feed or mark-as-read flow: `routes/metwgo.php:75`, `app/Http/Controllers/Api/MetwGo/NotificationController.php:16`.

## Open questions

1. Which Figma frame is the true start node for the prototype?
2. Are order and shipping-request flows separate tabs, cards on one home screen, or parallel prototypes?
3. Which screens own settings, support, delete account, and notifications feed?
