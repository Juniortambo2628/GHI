# GHI → VPS migration plan

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
| R1 | `laravel-activate.sh` runs `artisan route:cache` under `set -e`. GHI has **closure routes** (`/up`, `/dashboard`, `/coming-soon-get-involved` in `routes/web.php`). Laravel refuses to cache closure routes → deploy aborts *before* the switch (safe, but the deploy fails). | **Must fix in GHI** (convert the 3 routes to controllers/invokables). Candidate lesson for `MIGRATION_PROMPT.md`. |
| R2 | GHI defines `/up` twice: `bootstrap/app.php` `health: '/up'` **and** `routes/web.php:19`. Duplicate route names can also break `route:cache`. | Remove the `routes/web.php` copy (keep the framework health route). |
| R3 | Kit's default `health_path` is `/`. GHI's homepage reads DB settings and writes page_views → with an **empty DB** the first verify could 500. | Pass `health_path: /up` (JSON, DB-independent). |
| R4 | README's data-import step assumes a MySQL source (`mysqldump`). GHI's local source is **SQLite** (`database/database.sqlite`, `config` default `sqlite`, no local `.env`). | Needs a SQLite→MySQL export step (below). Note rsync already excludes `/database/*.sqlite`. |
| R5 | Legacy content sits in `public/uploads/images/` but **no code references `/uploads/`** (app writes go to the `public` disk → `storage/app/public`, which the kit links as `public/storage`). | Confirm DB rows don't point at `uploads/…`; if they don't, leave it out of scope. |
| R6 | PHP: kit default `8.3` matches Laravel 13 (`php: ^8.3`) ✓. `npm ci` works (lockfile present) ✓. `QUEUE_CONNECTION=database` + `jobs` migration ✓. | No kit changes needed. |

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

## 3. What I need from you before writing files / running steps

- [ ] **Shell access fixed** — every `pwsh` spawn from this session fails with
      `Access is denied` on `C:\Users\kevin\AppData\Local\Microsoft\WindowsApps\pwsh.exe`,
      so I currently can't run `git`, tests, `ssh`, or `sqlite3`. (Files/reads work.)
- [ ] Answers to the three questions in §4 (data, mail, www).
- [ ] `gh auth login` done on this PC (needed by `set-vps-secrets.ps1`).
- [ ] Confirm the OKJT repo is public, otherwise GHI needs a `KIT_TOKEN`
      (fine-grained PAT with read access to OKJT) for the kit checkout.
- [ ] Push the GHI changes (workflow + route fixes) to `main` — deploys trigger from GitHub.

## 4. Open questions (blocking)

1. **Source of truth for data**: does `database/database.sqlite` hold the real
   content (contact submissions, stories, admins…), is there live data on old
   hosting (cPanel?) to export, or is this a fresh launch seeded with
   `GhiContentSeeder`?
2. **Mail**: `.env.example` points at `mail.globalharmonyinitiative.com`.
   Keep mail on the current host (only DNS changes), use an external SMTP, or
   set up Hestia mail later?
3. **`www`**: alias/redirect to apex (recommended — `SESSION_DOMAIN` already
   covers both), or treat it as separate?

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
