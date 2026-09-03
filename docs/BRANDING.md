# Branding

Brand is tenant configuration, not code.

| Field | Where | Purpose |
|---|---|---|
| `brand_name` | `tenants.brand_name` | UI title and report header |
| `support_email` | `tenants.support_email` | Contact copy |
| `primary_color` | `tenants.primary_color` | `--brand` CSS variable and export accents |
| `logo_path` | `tenants.logo_path` | Sidebar logo URL |
| `settings` JSON | `tenants.settings` | Default scoring (`min_responses`, positive band, scale) |

Change these under **Settings** (`/settings`) or when onboarding (`/tenants/onboard`).

Do not hardcode company names, support inboxes, or client subdomains. The legacy Ixia values are not used.
