# GHI → VPS migration plan

**Status (2026-11-24): everything except DNS/SSL and old-host data recovery is DONE.**

| Phase | Status |
|---|---|
| A. Repo code changes (deploy.yml → kit) | ✅ pushed (`f8db6eb5`), deploy green |
| B. Hestia domain + alias + DB + mail + new-site.sh | ✅ `globalharmonyinitiative.com` (+www alias), `dev_ghi`/`dev_ghi`, mail domain + `admin@`/`info@`, web root linked |
| C. GitHub secrets (VPS_* + PROD_ENV) | ✅ 11 secrets total, junk cleaned |
| D. First deploy + data import | ✅ release `20261124073906-f8db6eb` live, 25 migrations, ghi-queue online, `ghi_dev` data imported (1 user, 7 stories, 5 causes, 208 media), all pages 200 |
| E. Hosts-file test | ⬜ next |
| F. DNS (A apex + www → 102.212.246.224) | ⬜ waiting for propagation (NS still cloudoon → 54.37.142.31) |
| G. Let's Encrypt + HTTPS redirect + `APP_ENV=production` | ⬜ after DNS |
| H. Old-host DB/images recovery | ⚠️ SSH key rejected by old host; cPanel open on 2083/2086 — needs panel login; local `ghi_dev` imported as baseline |
| I. Kit bugfix (app.name baked as "ghi") | 🔄 fix written in OKJT `laravel-activate.sh`, needs commit + redeploy |
| J. Cleanup old hosting | ⬜ last |

Target: Hestia VPS `102.212.246.224` (Debian 12, Hestia user `dev`), using the
shared deploy-kit in `Juniortambo2628/OKJT` (`deploy-kit/` + the reusable
workflows). Domain: `globalharmonyinitiative.com` (+ `www`), DNS not yet
propagated — everything below except SSL/DNS can be done now.

Stack: **Laravel 13.26 (PHP ≥ 8.3) + Inertia/React + Vite**, Breeze auth,
WebAuthn passkeys, database queue, SQLite locally → MySQL on the VPS.
Single app at the repo root → one `deploy-laravel.yml` job (`app_dir: "."`).

---

## 1. Review of the deploy-kit template

Verified against the actual repo — everything the docs reference exists
(`deploy-kit/actions/ssh/action.yml`, `scripts/*`, `server/new-site.sh`,
`local/set-vps-secrets.ps1`, both reusable workflows). Layout, secrets flow,
rollback and the "lessons learned" list all check out.

Findings that matter for GHI (and for the kit long-term):

| # | Finding | Impact |
|---|---|---|
| R1 | `laravel-activate.sh` runs `artisan route:cache` under `set -e`. GHI has **closure routes** (`/up`, `/dashboard`, `/coming-soon-get-involved` in `routes/web.php`). | ✅ **Verified non-issue**: Laravel 13's `Route::prepareForSerialization()` serializes closures via `SerializableClosure` (confirmed in v13.26.1 source, package in `composer.lock`). First deploy's `route:cache` succeeded. |
| R2 | GHI defines `/up` twice: `bootstrap/app.php` `health: '/up'` **and** `routes/web.php:19`. | ✅ **Verified non-issue**: the framework registers `HealthCheck` first, the web route overwrites it in the collection; both return 200. Deploy verified `/up` → `{"status":"ok"}`. Left as-is. |
| R3 | Kit's default `health_path` is `/`. GHI's homepage reads DB settings → could 500 with an empty DB. | ✅ Passed `health_path: /up`. |
| R4 | README's data-import assumes MySQL source; GHI's local source was believed to be SQLite. | ✅ Resolved: SQLite was empty → imported WAMP MySQL `ghi_dev` instead (§4). |
| R5 | Legacy `public/uploads/images/` exists but no code references `/uploads/`; the ~400 UUID images referenced by `media_assets` are **not in git** — only on the old host. | ⚠️ Old-host recovery still required for images + real submissions (§Phase H). |
| R6 | PHP 8.3 matches Laravel 13 (`php: ^8.3`) ✓; `npm ci` lockfile ✓; `QUEUE_CONNECTION=database` + `jobs` table ✓. | No kit changes needed. |
| **R7** | **Kit bug**: `deploy-laravel.yml` passes `APP_NAME='<app_name>'` into the activate script; phpdotenv never overrides real env vars, so `config:cache` bakes `app.name = "ghi"` over `.env`'s value. | 🔧 **Fix written** in OKJT `laravel-activate.sh` (`PM2_PREFIX` + `unset APP_NAME`) — needs commit + GHI redeploy. Affects every kit project (TFE would show "tfe"). |

Kit gaps noted for later (not blocking): no documented SQLite import path, no
mention of `health_path` choice for first deploys, no `route:cache`/closure lesson.

## 2. GHI configuration decisions

| Input | Value | Why |
|---|---|---|
| `domain` | `globalharmonyinitiative.com` | apex; `www` added as a Hestia **alias** (shares the site + cert) |
| `app_name` | `ghi` | folder under `private/`, PM2 prefix `ghi-queue` |
| `app_dir` / `build_assets` | `.` / `true` | Laravel root; Vite build required (Inertia) |
| `queue` | `true` | `OptimizeImageJob` is dispatched (`QUEUE_CONNECTION=database`) |
| `scheduler` | `false` | no scheduled tasks found (`routes/console.php` only has `inspire`) |
| `seed` | depends on data question | see §4 |
| `health_path` | `/up` | DB-independent (R3) |
| PHP / Node | 8.3 / 24 (defaults) | matches `composer.lock` |

`PROD_ENV` (the `ENV_FILE` secret) is `C:\wamp64\www\GHI\.env.example` with:
`APP_ENV=staging` (until SSL is on — production forces https), a real
`APP_KEY`, real Hestia DB creds (`DB_DATABASE` == `DB_USERNAME` — one row of
`v-list-databases dev`), and real mail creds. Everything else in the example is
already correct (`APP_URL`, `SESSION_DOMAIN=.globalharmonyinitiative.com`,
`SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`, `CACHE_STORE=file`,
`FILESYSTEM_DISK=public`).

## 3. Checklist of what YOU still need to prepare

- [ ] **cPanel login for the old host** (`https://54.37.142.31:2083`, user
      `jhoffkau`) — the GitHub recovery workflow can't get in (stored SSH key
      is rejected; all local keys rejected too). From cPanel: export the
      MySQL DB (phpMyAdmin) and download `GHI-core/storage/app/public` +
      `GHI-shared/storage/app/public` (the ~400 UUID images). Drop them
      anywhere on this PC and I'll import them. *(If you'd rather, unsuspend
      the account or add a new SSH key — cPanel is the fastest.)*
- [ ] **DNS records** (phase F, when propagation allows): apex `A → 102.212.246.224`,
      `www A → 102.212.246.224`, `mail A → 102.212.246.224`, `MX → mail.globalharmonyinitiative.com`,
      plus SPF/DKIM/DMARC (Hestia prints the values: `v-list-dns-domain`).
- [ ] Decide when to **decommission the old hosting** (after data is off it).
- [ ] `gh auth login` was already done ✓ · OKJT is public, no `KIT_TOKEN` needed ✓

## 4. Decisions (answered 2026-11-24)

1. **Data source**: user chose "local SQLite", but investigation showed the
   SQLite file is **schema-only (0 rows)** — the real local data is WAMP MySQL
   `ghi_dev` (fully migrated: 25 migrations, admin user, 7 stories, 5 causes,
   208 media assets). **`ghi_dev` was imported to the VPS as the baseline.**
   It contains NO real contact/newsletter submissions, and the ~400 UUID
   images the DB references exist only on the old host → old-host recovery
   still needed (§Phase H).
2. **Mail**: moved to the VPS — Hestia mail domain `mail.globalharmonyinitiative.com`
   created with accounts `admin@` and `info@` (password = MAIL_PASSWORD in
   PROD_ENV). MX/SPF/DKIM added in the DNS phase.
3. **`www`**: alias → apex. Added as Hestia alias `www.globalharmonyinitiative.com`
   (a redundant bare `www` alias was deleted to match the other sites).

## 5. Execution plan (ordered, per MIGRATION_PROMPT)

### Phase A — repo code changes (my PC, no server needed)
1. Convert the 3 closure routes to invokable controllers (R1) + drop the
   duplicate `/up` (R2).
2. Add `.github/workflows/deploy.yml`:
   a `test` job (composer install → `php artisan key:generate` → `php artisan test`
   → `npm ci` → `npm run build`), then `deploy: needs: test` calling
   `Juniortambo2628/OKJT/.github/workflows/deploy-laravel.yml@main` with the
   inputs from §2 and the five `VPS_*` secrets + `ENV_FILE: secrets.PROD_ENV`.
3. Commit + push to `main`.

### Phase B — server prep (no DNS needed yet)
4. **root (`ssh vps1`)** — Hestia: add web domain `globalharmonyinitiative.com`
   with alias `www`, create the MySQL database:
   ```bash
   v-add-web-domain dev globalharmonyinitiative.com
   v-add-web-domain-alias dev globalharmonyinitiative.com www
   v-add-database dev ghi ghi '<password>'   # db name == user (kit lesson)
   ```
   (or the same in the panel: Web → Add Domain, Databases → Add.)
5. **root (`ssh vps1`)** — one-time site scaffolding (`/root/new-site.sh` is
   already on the server):
   ```bash
   bash /root/new-site.sh laravel globalharmonyinitiative.com ghi
   ```

### Phase C — secrets (my PC, PowerShell)
6. Shared VPS secrets into the GHI repo:
   ```powershell
   cd C:\wamp64\www\OKJT
   .\deploy-kit\local\set-vps-secrets.ps1 -Repos Juniortambo2628/GHI
   ```
7. `PROD_ENV` (staging-flavoured `.env` from §2, `APP_KEY` generated with
   `php artisan key:generate --show`):
   ```powershell
   gh secret set PROD_ENV -R Juniortambo2628/GHI -f C:\wamp64\www\GHI\ghi-prod.env
   ```

### Phase D — data import (order matters: schema first, data second)
8. Merge Phase A → first deploy runs and creates the MySQL schema
   (`migrate --force`) and passes verify against `/up`.
9. Export SQLite data as MySQL `INSERT`s (explicit ids + column names), copy to
   the VPS, load as `dev`:
   ```powershell
   # my PC — a small one-off artisan exporter (I'll add it in Phase A)
   php artisan ghi:export-data > ghi-inserts.sql
   scp ghi-inserts.sql vps1:/tmp/
   ```
   ```bash
   # dev (sudo -u dev bash)
   cd ~/web/globalharmonyinitiative.com/private/ghi
   mariadb -u ghi -p ghi < /tmp/ghi-inserts.sql
   ```
   (Alternative if you prefer: `.dump` the whole SQLite file and hand-fix DDL —
   rejected: SQLite DDL/`sqlite_sequence` don't import cleanly into MySQL.)
10. Legacy `public/uploads/…` only if §4/Q1 shows the DB references them.

### Phase E — test & cutover
11. **my PC** hosts-file test (`C:\Windows\System32\drivers\etc\hosts`, admin):
    `102.212.246.224 globalharmonyinitiative.com www.globalharmonyinitiative.com`
    → browse `http://globalharmonyinitiative.com`, check pages, login, uploads,
    queue job. Watch for **http/https mixups** → if seen, add
    `$middleware->trustProxies(at: '*')` in `bootstrap/app.php` and redeploy.
12. **DNS** (when propagation finishes): `A @ → 102.212.246.224`,
    `A www → 102.212.246.224` (MX unchanged if mail stays put).
13. **root**: Let's Encrypt + force HTTPS:
    ```bash
    v-add-letsencrypt-domain dev globalharmonyinitiative.com www yes
    v-change-web-domain-ssl-redirect dev globalharmonyinitiative.com yes
    ```
14. Flip `APP_ENV` → `production` in `PROD_ENV`, re-run the deploy, verify https.
15. Cleanup: remove the hosts-file entry, decommission old hosting (after mail
    decision), keep the old DB dump as backup.

## 6. Risks / verify list

- First deploy before DNS is fine: `laravel-verify.sh` curls with
  `--resolve` against the server's own IP, and `/up` needs no data.
- SQLite→MySQL import is the only bespoke step — exporter will be written and
  tested before import; row counts checked per table afterwards.
- `route:cache` is the one hard kit/app conflict — fixed in Phase A, and the
  deploy fails *before* switching releases if anything is still wrong (rollback
  = repoint `current`).
- Tests: GHI's phpunit suite (SQLite in-memory) must go green in CI before the
  deploy job runs.
