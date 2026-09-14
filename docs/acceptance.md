# First-edition acceptance

Verified on 2026-09-14 against the revisions in `docs/source-map.md`.

## Documentation synchronization

- The impact map covers 22 public pages, Larena Specs, Root, Workspace and all
  12 foundation package repositories.
- The isolated contract test passes 12 assertions, including a stale public
  change, incomplete acceptance rejection, accepted update and an internal-only
  change that does not mark a page stale.
- Docara source tracking reports 12 `current` package entities and no other
  editorial state.
- Larena Root's existing developer-documentation delta validator passes with
  132 of 132 internal records and an embedded passing public-documentation
  result.

## Docara and static output

- `composer.lock` resolves `simai/docara` 2.9.1 at the published tag commit.
- ServBay PHP 8.4.20 satisfies the project's explicit PHP 8.4.1 minimum; the system PHP 8.2 runtime is intentionally rejected by Composer.
- `"$LARENA_DOC_PHP" vendor/bin/docara doctor --json` passed.
- `"$LARENA_DOC_PHP" vendor/bin/docara validate project --json` reported 0 errors, 0 undeclared pages and 24 passed checks. The 22 public pages remain visible in the authoring report for human editorial review.
- `"$LARENA_DOC_PHP" vendor/bin/docara build production` built 22 authored pages.
- `"$LARENA_DOC_PHP" vendor/bin/docara verify-static build_production` checked 44 HTML outputs and 1,928 local references with no broken references.

The 44 HTML outputs contain the canonical Russian routes and compatibility redirects for the configured legacy unprefixed URLs.

## HTTPS and browser checks

- `https://larena-doc.test/` returns HTTP 200 and routes to the Russian edition.
- `https://larena-doc.test/ru/packages/storage/` returns HTTP 200 and renders with the expected title, breadcrumbs and content.
- Desktop navigation, article table of contents, reader preferences and dark theme work.
- At 390 by 844 pixels, the mobile navigation opens and exposes every first-edition section.
- Keyboard focus reaches the header controls and modal controls.
- Browser console: 0 errors and 0 warnings on the tested home and nested package pages.

## Search checks

| Query | Results |
| --- | ---: |
| `создать поле` | 1 |
| `права доступа` | 1 |
| `настройки` | 3 |
| `список записей` | 2 |
| `larena/storage` | 9 |
| `dataview` | 6 |

## Local serving and rollback

ServBay resolves `/Users/rim/Sites/larena-doc.test` to the generated `build_production` directory in this repository. The previous empty document root is preserved at `/Users/rim/Sites/.larena-doc-backups/20260914-verified-build-switch`.

To roll back the document-root switch, remove the symlink and restore that saved directory at `/Users/rim/Sites/larena-doc.test`.
