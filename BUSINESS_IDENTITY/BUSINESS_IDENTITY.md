# MetwGo Business Identity

> Version: 1.0  
> Source: Figma / mobile application screenshots shared during Phase 2 UI/UX review  
> Purpose: A reusable design and business identity reference for improving the web admin dashboard, Blade pages, and future UI/UX work.

---

## 1. Brand Overview

**MetwGo** is a logistics and marketplace platform that connects customers, vendors, delivery captains, shipment companies, and administrators in one operational ecosystem.

The product identity combines:

- **Fast delivery**
- **Marketplace purchasing**
- **Order management**
- **Wallet and earnings**
- **Captain/vendor/admin workflows**
- **Arabic-first user experience**
- **Friendly but professional visual language**

The brand should feel practical, fast, trustworthy, and simple to use.

---

## 2. Brand Personality

MetwGo should feel:

| Trait | Meaning |
|---|---|
| Fast | The product supports quick delivery, order handling, and instant actions. |
| Friendly | Rounded cards, soft backgrounds, and warm orange actions make the product approachable. |
| Trustworthy | Wallet, delivery, and order flows need clear hierarchy and safe confirmation states. |
| Modern | Clean mobile-first UI, gradient accents, cards, subtle shadows, and simple icons. |
| Operational | Dashboards should help users act quickly, not just look decorative. |
| Arabic-first | RTL layout, Arabic labels, and regional formatting should feel native. |

---

## 3. Target Users

### Customers
Use MetwGo to place marketplace orders, request shipping, track orders, and manage delivery/payment.

### Captains / Delivery Agents
Use the app to receive orders, start delivery, complete delivery, and track wallet earnings.

### Vendors / Sellers
Use the platform to manage products, orders, sales, and shipping operations.

### Shipment Companies
Use the platform to manage shipment workflows, delivery requests, and operational performance.

### Admins / Employees
Use the admin dashboard to approve users, manage products, sellers, shipment companies, orders, returns, commissions, notifications, roles, permissions, and settings.

---

## 4. Visual Identity

### 4.1 Core Colors

The mobile UI uses a clear orange and purple identity.

| Token | Color | Usage |
|---|---|---|
| `brand-orange` | `#FF7043` / close to coral orange | Primary CTA buttons, important action highlights, active bottom nav background. |
| `brand-purple` | `#7B00A8` / deep purple | Brand logo, active states, order borders, dashboard accents. |
| `brand-purple-dark` | `#5D008B` | Strong gradients, admin accents, emphasis states. |
| `brand-lavender` | `#D8CEF0` | Soft illustration/login background. |
| `brand-bg-warm` | `#E9E3DA` | Warm onboarding/profile background. |
| `surface-white` | `#FFFFFF` | Main cards and page background. |
| `surface-soft` | `#F7F7F8` | Inputs, subtle sections, empty cards. |
| `text-main` | `#2B2430` | Main Arabic text. |
| `text-muted` | `#9A9A9A` | Secondary labels, hints, timestamps. |
| `success-green` | `#35D36F` | Availability and positive states. |
| `danger-red` | `#FF4B2E` | Alerts, unread notification dot. |

### 4.2 Gradients

The brand relies on orange-to-purple gradients.

Recommended gradients:

```css
--metw-gradient-primary: linear-gradient(135deg, #FF7043 0%, #7B00A8 100%);
--metw-gradient-purple: linear-gradient(135deg, #8A00B8 0%, #5D008B 100%);
--metw-gradient-orange: linear-gradient(135deg, #FF7A45 0%, #FF5A3C 100%);
--metw-gradient-soft: linear-gradient(180deg, #FFFFFF 0%, #F8F5FB 100%);
```

Use gradients for:
- Hero cards
- Wallet cards
- Important status cards
- Selected/active elements
- Admin dashboard highlights

Avoid using too many competing gradients on the same page.

---

## 5. Typography Direction

The application is Arabic-first and uses a soft, rounded UI style.

Recommended typography direction:

- Use a modern Arabic-friendly font such as **Cairo**, **Tajawal**, or **IBM Plex Sans Arabic**.
- Headings should be medium-bold, not overly heavy.
- Body text should be readable and calm.
- Use strong contrast for numbers and key values.
- Keep labels small and muted.

Suggested scale:

| Element | Size | Weight |
|---|---:|---:|
| Page title | 20–24px | 700 |
| Section title | 16–18px | 600–700 |
| Card number/value | 20–28px | 700 |
| Body text | 13–15px | 400–500 |
| Helper text | 11–13px | 400 |
| Buttons | 14–16px | 600 |

---

## 6. UI Shape Language

The mobile screens use soft, rounded, friendly components.

### Border Radius

| Component | Radius |
|---|---:|
| Main page sections | 28–40px |
| Cards | 16–24px |
| Inputs | 12–16px |
| Buttons | 14–22px |
| Icon containers | 10–16px |
| Pills/badges | 999px |

### Shadows

Use subtle shadows only.

```css
--shadow-card: 0 8px 24px rgba(20, 20, 43, 0.06);
--shadow-floating: 0 12px 32px rgba(20, 20, 43, 0.10);
```

Avoid harsh dark shadows.

---

## 7. UI Component Rules

### 7.1 Buttons

Primary buttons should use the orange brand color.

Rules:
- Primary CTA: orange filled button.
- Secondary CTA: white/soft background with light border.
- Destructive actions: red/orange but with clear copy.
- Disabled states should be visibly muted.

Examples:
- `تسجيل الدخول`
- `بدء الطلب`
- `سحب الأرباح`
- `إيقاف الاستقبال`

### 7.2 Cards

Cards should be:
- White or gradient depending on importance.
- Rounded.
- Spacious.
- Focused on one purpose.
- Not overloaded with text.

Admin dashboard cards should prioritize:
- Big number
- Clear label
- Icon
- Optional small status line

### 7.3 Inputs

Inputs should be:
- Soft gray background.
- Rounded.
- Calm borders.
- Clear labels above or aligned for RTL.
- Validation messages close to the field.

### 7.4 Status Badges

Status must be visually clear.

| Status | Visual Direction |
|---|---|
| Available / Active | Green dot or green soft badge |
| Pending | Orange soft badge |
| Completed | Purple/green confirmation |
| Urgent / Alert | Orange/red badge |
| Wallet / Payment | Purple/orange wallet icon |

### 7.5 Bottom Navigation

Mobile app uses pill-like active nav items.

For web/admin equivalents:
- Use clear active sidebar states.
- Use soft background for active links.
- Keep icons consistent and simple.

---

## 8. UX Principles

### 8.1 Action First

Users should quickly understand the next action.

Examples:
- Captains see `بدء الطلب`.
- Wallet page highlights `سحب الأرباح`.
- Success page confirms completion and next wallet result.
- Admin should quickly see pending approvals, orders, and urgent issues.

### 8.2 Clear Hierarchy

Each page should have:
1. Main status or purpose.
2. Primary action.
3. Supporting details.
4. Secondary actions.

### 8.3 Arabic RTL Quality

All Arabic pages must:
- Use correct RTL direction.
- Align labels naturally.
- Keep icons readable.
- Avoid mixed-direction layout issues.
- Format currency and times consistently.

### 8.4 Reduce Visual Noise

The brand uses strong orange and purple, so avoid:
- Too many gradients.
- Too many shadows.
- Too many bright cards on one screen.
- Competing CTA colors.

### 8.5 Trust in Money and Delivery Flows

Wallet, payment, and order completion screens must feel secure and clear.

Always show:
- Amounts clearly.
- Order IDs clearly.
- Status clearly.
- Confirmation messages clearly.
- Next step clearly.

---

## 9. Admin Dashboard Design Direction

The admin dashboard should inherit the MetwGo brand but look more professional than the mobile app.

### 9.1 Admin Feeling

Admin UI should feel:

- Premium
- Operational
- Clean
- Calm
- Fast to scan
- Less playful than mobile
- More data-focused

### 9.2 Admin Colors

Use purple as the admin structural color and orange as the action color.

| Usage | Recommended Color |
|---|---|
| Sidebar / navigation | Deep purple or dark navy-purple |
| Primary buttons | Orange |
| Active state | White/purple or soft purple |
| Dashboard highlights | Purple/orange gradient |
| Background | Very light gray/lavender |
| Tables | White surfaces with subtle borders |

### 9.3 Admin Layout Rules

The admin panel should have:

- Clean sidebar with clear active states.
- Compact but readable topbar.
- Consistent page headers.
- Cards using the same radius and spacing.
- Tables with readable row height.
- Empty states for no data.
- Clear badges for pending/approved/rejected/completed.
- Responsive behavior for tablet and mobile.

### 9.4 Admin Cards

Admin dashboard cards should follow this pattern:

```text
[Icon] Label
Large value
Small helper/status
```

Avoid excessive card colors. Use:
- 1–2 brand gradients for key cards.
- White cards for secondary metrics.
- Orange accents for urgent/required actions.

### 9.5 Admin Tables

Tables should be:
- Clean and scannable.
- Not visually heavy.
- Have clear actions.
- Use badges instead of raw status text.
- Use sticky headers only when useful.
- Be horizontally scrollable on small screens.

### 9.6 Admin Forms

Forms should:
- Use grouped sections.
- Have consistent input spacing.
- Show required fields clearly.
- Place primary action at the bottom/end.
- Use confirmations for destructive actions.

---

## 10. Screenshot-Based Observations

From the shared mobile screens:

### Home / Captain Dashboard
- Header shows greeting, rating, avatar, notification.
- Availability card uses purple gradient and green status dot.
- Order cards use white surface with purple/orange accents.
- Main CTA uses orange.
- Bottom nav active item uses soft orange pill.

### Splash Screen
- Simple white background.
- Logo centered.
- Purple and orange brand colors dominate.

### Registration/Profile Setup
- Large logo.
- Curved white form sheet over warm background.
- Orange section title.
- Soft gray inputs.

### Login
- Soft lavender background.
- 3D package/parachute illustration.
- Rounded white login card.
- Orange title and primary button.

### Wallet
- Purple/orange gradient wallet card.
- Strong balance display.
- Orange withdraw button.
- Analytics section with minimal bar chart.
- Transaction list with icon badges.

### Delivery Success
- Large circular purple/orange success mark.
- Clear success message.
- Earnings card.
- Order details card.
- Informational purple-tinted note.
- Orange primary action.

---

## 11. Do / Don’t

### Do

- Use orange for primary actions.
- Use purple for brand structure and active states.
- Keep cards rounded and spacious.
- Use clean Arabic RTL layouts.
- Use subtle shadows.
- Make order/payment/status information very clear.
- Keep admin pages more professional and less playful than mobile screens.
- Use consistent icon containers.
- Use clear empty/loading/error states.

### Don’t

- Do not use too many gradients on one screen.
- Do not make every card brightly colored.
- Do not use harsh shadows.
- Do not mix unrelated icon styles.
- Do not hide key actions.
- Do not overcrowd dashboard pages.
- Do not break RTL layout.
- Do not change backend logic while doing UI/UX enhancements.
- Do not remove existing routes, variables, permissions, or translations.

---

## 12. Phase 2 UI/UX Checklist

Before changing any Blade/admin page, check:

- [ ] Does the page use MetwGo orange and purple correctly?
- [ ] Is the main action obvious?
- [ ] Is the page readable in Arabic and English?
- [ ] Are cards, buttons, inputs, and badges consistent?
- [ ] Is the layout responsive?
- [ ] Are empty/error/success states clear?
- [ ] Are tables easy to scan?
- [ ] Are destructive actions protected with confirmation?
- [ ] Is the admin page professional and not too noisy?
- [ ] Did we keep all backend logic unchanged?

---

## 13. Implementation Notes for Laravel Blade

When improving Blade UI:

- Keep route names unchanged.
- Keep translation keys unchanged.
- Keep controller variables unchanged.
- Keep permission checks unchanged.
- Avoid editing database logic.
- Prefer CSS classes and Blade layout improvements.
- Add reusable utility classes when possible.
- Use `@stack('styles')`, `@stack('scripts')`, and layout-level design tokens.
- Group page-specific styles with comments.
- Test RTL and LTR.

---

## 14. Suggested CSS Design Tokens

```css
:root {
    --metw-orange: #FF7043;
    --metw-orange-dark: #F45B2E;
    --metw-purple: #7B00A8;
    --metw-purple-dark: #5D008B;
    --metw-lavender: #D8CEF0;
    --metw-bg: #F7F7F8;
    --metw-surface: #FFFFFF;
    --metw-text: #2B2430;
    --metw-muted: #9A9A9A;
    --metw-border: #ECECF1;
    --metw-success: #35D36F;
    --metw-gradient: linear-gradient(135deg, #FF7043 0%, #7B00A8 100%);
    --metw-radius-card: 22px;
    --metw-radius-input: 14px;
    --metw-shadow-card: 0 8px 24px rgba(20, 20, 43, 0.06);
}
```

---

## 15. Final Direction

MetwGo’s UI should always balance:

```text
Friendly mobile simplicity + trustworthy delivery/payment flows + professional admin operations
```

For the admin panel specifically:

```text
Use the MetwGo brand colors, but make the interface calmer, cleaner, more structured, and more data-focused than the mobile app.
```