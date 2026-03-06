# Instructions for Junie — Musician Storefront App (Angular + Slim, Docker-ready, IONOS-ready)

## 0) Goal
We are building a web app for musicians that allows:
- **Artists** to create profiles and upload songs (for sale as digital downloads).
- **Buyers/Clients** to purchase songs as **guests** or **registered users**, then access their purchases + downloads.
- **Super Admin** to manage everything: artists, songs, pricing rules, payment priorities/health, platform reporting.

**Tech constraints**
- Frontend: **Angular (latest stable/LTS at implementation time)**.
- Backend: **PHP Slim (latest stable)** with a clean REST API.
- Run via **Docker** locally.
- Deployment-ready for **IONOS** using **environment files**.

**Important update**
- **Default storage must be AWS S3** (not local filesystem).
- Include **notification services (email)**.

---

## 1) Roles & access control

### 1.1 Roles
1) **Super Admin**
- Full system control
- Assign artist star ranking (1–5)
- Configure pricing ranges per star level
- Manage payment providers + view health checks
- Configure country→currency mapping
- View platform analytics

2) **Artist**
- Setup/update profile (country of origin required)
- Upload songs (WAV main + teaser required)
- Set song status (draft/published/upcoming/archived)
- View sales charts per song
- Export sales report to PDF

3) **Client/Buyer**
- Buy as **guest** (email required)
- Or buy as **registered user**
- Registered profile shows:
    - Purchased songs + downloads
    - Total spending + breakdown

### 1.2 RBAC enforcement
All Slim endpoints must enforce role ownership and permissions via middleware:
- Artist can only manage their own profile/songs.
- Super admin can manage everything.
- Buyers can only access their own purchases.

---

## 2) Storage (AWS S3 default)

### 2.1 Storage requirements
All media files must be stored in **AWS S3** by default:
- Main song file (WAV)
- Teaser file
- Cover art
- Generated PDFs (optional, recommended)

### 2.2 S3 structure (suggested)
Use private bucket and organized keys:
- `artists/{artistId}/songs/{songId}/main.wav`
- `artists/{artistId}/songs/{songId}/teaser.wav` (or teaser.mp3 if allowed)
- `artists/{artistId}/songs/{songId}/cover.jpg`
- `artists/{artistId}/reports/{reportId}.pdf`

### 2.3 S3 security rules
- Bucket is **private** (no public access).
- Generate **pre-signed URLs** for:
    - Buyer downloads (time-limited)
    - Teaser streaming (time-limited, short expiry)
    - Artist download of their own uploaded assets if needed
- Never expose raw S3 URLs without signing.

### 2.4 S3 environment variables (backend)
Required `.env` keys:
- `STORAGE_DRIVER=s3`
- `AWS_ACCESS_KEY_ID=...`
- `AWS_SECRET_ACCESS_KEY=...`
- `AWS_REGION=...`
- `AWS_S3_BUCKET=...`
- `AWS_S3_ENDPOINT=` (optional for S3-compatible storage)
- `AWS_S3_PATH_STYLE=false|true` (optional)
- `AWS_S3_PUBLIC_BASE_URL=` (optional, if using CloudFront)
- `AWS_S3_SIGNED_URL_TTL_SECONDS=600`

### 2.5 Local development behavior
Local dev can still use S3 by default, but support optional local override:
- `STORAGE_DRIVER=local` (for offline dev)
- Keep S3 as default in `.env.example`, but document override.

---

## 3) Notifications (Email service)

### 3.1 Email use-cases
System must send email notifications for:
- **Guest purchase receipt** + secure download link(s)
- **Registered purchase confirmation**
- **Artist notifications** (optional but recommended):
    - Song approved/published (if moderation exists)
    - Payout/report availability (future)
- **Admin alerts** (optional but recommended):
    - Payment provider health DOWN
    - Webhook failures / repeated errors

### 3.2 Email delivery approach
Backend will send emails via a pluggable provider:
- Recommended: **SMTP** (easy for IONOS)
- Optionally support providers later (SendGrid, SES)

**Implementation requirement**
Create a `NotificationService` abstraction with drivers:
- `smtp`
- `ses` (optional)
- `noop` (testing)

### 3.3 Email templates
Use HTML + text fallback templates:
- Receipt + order summary
- Download links (signed URLs with expiry)
- Password reset (if implemented)
- Account verification (optional)

### 3.4 Email security + download links
- Use signed token links that expire:
    - Guest download: link expires (e.g., 24h) and can be regenerated from email verification flow if needed.
- Do not embed direct S3 object links unless pre-signed.

### 3.5 Email environment variables (backend)
Add to `.env.example`:
- `MAIL_DRIVER=smtp`
- `MAIL_HOST=...`
- `MAIL_PORT=...`
- `MAIL_USERNAME=...`
- `MAIL_PASSWORD=...`
- `MAIL_ENCRYPTION=tls|ssl|none`
- `MAIL_FROM_ADDRESS=...`
- `MAIL_FROM_NAME=...`

Optional:
- `MAIL_REPLY_TO=...`
- `MAIL_DEBUG=true|false`

### 3.6 Event-driven notifications
Implement an internal event system:
- On `ORDER_PAID` → send receipt + generate download entitlements
- On `ORDER_FULFILLED` → ensure download links available
- On `PAYMENT_PROVIDER_DOWN` → notify admin (optional)

---

## 4) Song requirements (validation + metadata)

### 4.1 Song attributes
Each song includes:
- `title`
- `artist_id`
- `status`: `draft | published | upcoming | archived`
- `price` (validated by stars)
- `currency_code` (derived from artist country)
- `main_audio_s3_key` (WAV only)
- `teaser_audio_s3_key` (required)
- `cover_s3_key`
- metadata: genre, duration, release_date, tags, description, etc.

### 4.2 File format restrictions
- Main: **WAV only**
    - Validate MIME + signature
    - Max size configurable via env
- Teaser:
    - Prefer WAV, or allow MP3/AAC if explicitly enabled by env:
        - `TEASER_ALLOWED_FORMATS=wav,mp3`
- Compute duration server-side if possible.

---

## 5) Pricing rules (Stars + currency)

### 5.1 Stars
- Star levels: 1..5
- Super admin assigns star level per artist.

### 5.2 Pricing rules table
Create `star_pricing_rules`:
- `star_level`
- `currency_code`
- `min_price`
- `max_price`

Example for XOF:
- 1 star: 100–250 XOF
- 5 star: 500–1000 XOF
  (intermediate levels configured in admin)

Backend must enforce constraints.

---

## 6) Payments (PayPal + CinetPay + PayDunia)

### 6.1 Priority requirements
- PayPal is always available by default.
- For African countries:
    - health check cinetpay + paydunia
    - prefer cinetpay, fallback paydunia, else paypal

### 6.2 Admin payment health panel
Super admin can view:
- current UP/DOWN/DEGRADED
- last check time + latency
- manual refresh
- history

Store results in `payment_health`.

### 6.3 Webhooks
Implement verified + idempotent webhooks for each provider.

---

## 7) Buyer downloads & entitlements
- Entitlement created on successful payment.
- Download links are signed (either:
    - signed backend token → backend returns signed S3 URL
    - or direct S3 presigned URLs)
- Registered buyers get a library page.
- Guests get receipt email with download links.

---

## 8) Artist analytics + PDF export
- Artist can view sales charts per song
- Export to PDF (backend generates PDF)
- Store PDFs in S3 (recommended) and provide signed download link

---

## 9) Backend architecture (Slim API)

### 9.1 API style
- `/api/v1/...`
- OpenAPI documentation required

### 9.2 Auth
- JWT for admin/artist/buyer
- guest checkout supported

### 9.3 Tables (additions emphasized)
- `songs` includes S3 keys
- `reports` (optional): track generated PDFs stored in S3
- `notifications_log` (optional): store email send status
- `payment_health` for provider monitoring

---

## 10) Docker + env files

### 10.1 Services
- nginx
- backend (php-fpm)
- frontend (build + serve)
- db (mysql/postgres)
- optional: redis
- optional (dev): mailhog for testing email

### 10.2 Required env deliverables
- `.env.example` (complete)
- `.env` (local dev)
- `.env.production.template` (IONOS-ready)

---

## 11) Step-by-step implementation plan (updated)

### Phase 1 — Scaffolding
1) Repo structure + docker compose
2) Env loader + config for S3 + mail

### Phase 2 — DB + auth
3) migrations + seed super admin
4) JWT + RBAC middleware

### Phase 3 — Artist profile + country/currency
5) profile CRUD
6) country→currency mapping admin config

### Phase 4 — Pricing rules + stars
7) pricing rules CRUD (admin)
8) assign stars to artists
9) enforce pricing validation

### Phase 5 — S3 uploads + songs
10) implement S3 uploader service
11) upload main WAV + teaser + cover
12) validate formats
13) song statuses

### Phase 6 — Storefront + browse
14) public browsing/search
15) song detail + teaser playback (signed)

### Phase 7 — Checkout + payments
16) orders + items + entitlements
17) paypal integration
18) cinetpay/paydunia + provider selection
19) webhooks (verified + idempotent)

### Phase 8 — Notifications (email)
20) build NotificationService + templates
21) send receipt emails on paid orders
22) guest download link flow

### Phase 9 — Payment health + admin panel
23) health checks scheduler/endpoint
24) admin UI for health status

### Phase 10 — Analytics + PDF
25) analytics endpoints
26) PDF generation + store in S3
27) signed link download + email (optional)

### Phase 11 — Hardening + IONOS
28) security review
29) production docker compose + nginx config
30) final docs + OpenAPI

---

## 12) Acceptance criteria (updated)
- App runs on docker compose
- S3 is default storage (uploads + downloads via signed URLs)
- Email notifications send receipts + download links
- Guest + registered buyer flows work end-to-end
- Admin panel shows payment API health
- Artist can export sales report to PDF (stored in S3)

---
End of instructions.