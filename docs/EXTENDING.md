# Extending the platform

## Add a question nature

1. Use the nature string on `questions.nature` (`standard`, `demonstrated`, `importance`, `enps`, or your own).
2. If it needs scoring, add a class in `plugins/Analytics/src/Service/` implementing `CalculatorInterface`.
3. Register it in `ScoreService::$calculators`.
4. Cover it with a unit test in `tests/TestCase/Calculator/`.

## Add a report widget

1. Store an element on a report page with `type` and JSON `config_json`.
2. Handle the type in `Reporting\Service\ExportService::elementText()`.
3. Built-in types: `static` (copy) and `score` (`config.calculator` = calculator name).

## Add a plugin

1. Create `plugins/YourPlugin/src/YourPluginPlugin.php`.
2. Add PSR-4 autoload in the root `composer.json`.
3. Enable it in `config/plugins.php`.
4. Keep controllers thin; put rules in services.

## Tenant onboarding

`AdminKit\Service\TenantOnboardingService` creates tenant, roles, admin user, organisation, and root node. Call it from `/tenants/onboard` or `bin/cake seed_demo`.
