# Engagement Platform

CakePHP 5 application: a **generic admin kit** (tenants, users, roles, org trees, branding) plus **engagement / survey / 360 / pulse / reporting** plugins.

The original CakePHP 2 Ixia admin panel lives in [`legacy/`](legacy/) as a behaviour reference only. Domain rules are recorded in [`docs/DOMAIN.md`](docs/DOMAIN.md).

## Run with Docker

```bash
docker compose up -d --build
docker compose exec app composer dump-autoload -o
docker compose exec app bin/cake migrations migrate
docker compose exec app bin/cake seed_demo
```

Open [http://localhost:8765](http://localhost:8765)

- Admin: `admin@example.com` / `ChangeMe!23`
- Or create a tenant at `/tenants/onboard`

## First slice

Create survey → add questions → invite → `/take/{token}` → analytics (`/analytics`) → report preview/PDF/Word/PPT (`/reports`).

## Layout

| Path | Role |
|---|---|
| `plugins/AdminKit` | Tenants, auth, users, roles, settings, CRUD base, tenant scope |
| `plugins/Organisation` | Nested-set organisation tree |
| `plugins/Survey` | Surveys, questions, invitations, take-survey, responses |
| `plugins/Analytics` | Percent-positive, eNPS, demonstrated vs importance, engagement index, anonymity floor |
| `plugins/Reporting` | Template pages/elements, HTML/PDF/DOCX/PPTX |
| `plugins/Feedback360` | 360 cycles and rater assignments |
| `plugins/ActionPlanning` | Plans and tasks |
| `plugins/Pulse` | Short-cycle surveys (`kind = pulse`) |

## Tests

Calculator tests do not need a database:

```bash
vendor/bin/phpunit -c phpunit.unit.xml
```

Full app tests (inside Docker, after migrate):

```bash
docker compose exec app vendor/bin/phpunit
```

## Docs

- [Domain contract](docs/DOMAIN.md)
- [Branding](docs/BRANDING.md)
- [Extending](docs/EXTENDING.md)
- [Missing legacy runtime](docs/MISSING_RUNTIME.md)
# multi-tenant-survey-engine
