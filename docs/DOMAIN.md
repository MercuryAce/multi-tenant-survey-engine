# Domain contract (from legacy CakePHP 2 app)

This document is the data and scoring contract extracted from `legacy/config/Schema/schema.php` and the original models. The CakePHP 5 app implements these bounded contexts as plugins. Ixia-specific names, emails, and client hardcoding are **not** part of the contract.

## Bounded contexts

| Context | Plugin | Legacy tables (representative) |
|---|---|---|
| Tenancy, users, roles, menus, settings, theming | AdminKit | `users`, `roles`, `user_roles`, `menus`, `menu_items`, `controller_actions`, `roles_menu_items`, `roles_modules`, `stylesheets`, `audit` |
| Organisation hierarchy | Organisation | `organisations`, `organisation_levels`, `ol_groups`, `ol_group_members` |
| Surveys and responses | Survey | `surveys`, `questions`, `question_sets`, `question_extra_datas`, `access_tickets`, `survey_answers`, `user_surveys` |
| Scoring and dashboards | Analytics | computed from answers (legacy: `survey_answers_final*`, `pre_calculations`, `surveys_completed`) |
| Report templates and export | Reporting | `report_types`, `report_pages`, `report_elements`, `report_text`, `widgets` |
| 360 feedback | Feedback360 | `associated_users`, `associated_user_types` |
| Follow-up work | ActionPlanning | `action_plans`, `action_plan_tasks`, `action_plan_commitments` |

## Scoring and anonymity (legacy constants → config)

These were hardcoded. They are now tenant/survey settings with the same **defaults**:

| Setting | Legacy source | Default |
|---|---|---|
| Likert scale | implied 1–5 | `1..5` |
| Percent-positive band | `Model/Survey.php` `value IN (4,5)` | `{4, 5}` |
| Anonymity floor | `OrganisationLevel::$min_responses = 4`; `surveys.min_responses` default 5 in schema | `4` |
| Question natures | magic IDs 1–4 | `demonstrated`, `importance`, `standard`, `enps` |
| Super-admin role | `Role.id == 1` | role slug `super-admin` |

### Calculators

- **Percent positive:** share of scored answers whose numeric value is in the positive band, as a percentage.
- **eNPS:** for `enps` questions, `(promoters − detractors) / n * 100` using standard bands (0–6 detractor, 7–8 passive, 9–10 promoter) when the scale is 0–10; on a 1–5 scale treat 1–3 detractor, 4 passive, 5 promoter unless overridden.
- **Demonstrated vs importance:** pair questions of natures `demonstrated` and `importance` (same competency) and report the gap (importance − demonstrated).
- **Engagement index:** mean percent-positive across `standard` and `demonstrated` items in a question set, subject to the anonymity floor.

Do **not** recreate per-survey tables `survey_{id}_answers_final`. Store raw answers once; compute aggregates in queued jobs.

## Nested-set organisation tree

`organisation_levels` used nested sets (`lft`, `rght`, `parent_id`, `depth`) plus `quota` and `current_respondents`. Visibility groups (`ol_groups` / `ol_group_members`) restrict which nodes a user can see.

## Multi-tenancy

Legacy: subdomain `cp{client}` selected a whole MySQL database. Target: **one database + `tenant_id`** on tenant-owned rows.


