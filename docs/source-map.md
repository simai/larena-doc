# Larena documentation source map

Observed on 2026-09-14. This file records the factual basis of the first Russian
edition; it does not change ownership of the sources.

| Source | Revision | Use |
| --- | --- | --- |
| `simai/docara` | tag `v2.9.1`, commit `72b475b06ad3acf5c07f9a9f85d123c5620021fd` | Site compiler and schemas |
| `simai/larena-specs` | `5209e5f25158b2127eff3a21f51a716ca68f01f9` | Canonical package identities, requirements and standards; later backup-only changes were reviewed as outside the 12-package first edition |
| `simai/larena-workspace` | `749cd11e1844cff40a7c768002737cb8c85aaaf7` | Development assembly and package locations |
| `simai/larena` | `98dd55f2ea4bc8428907d386d7949633d6b8115e` | Executable entry app, installation and integrated checks |

## Package revisions

| Package | Revision | Primary human source |
| --- | --- | --- |
| `larena/core` | `47123f2e79fd8852c91af7eafb4b03754425ac4a` | package README, module manifest and Specs |
| `larena/setting` | `5cf41d20252be9cefa72f1730a505b25e45b481b` | package README, module manifest and Specs |
| `larena/lang` | `b668a2ff3d4d39958938a40ce7a57771d554f101` | package README, module manifest and Specs |
| `larena/auth` | `9a2c93842e1a9e949f6e6f82499eec5cd883d47c` | package README, developer docs and Specs |
| `larena/access` | `0c504fc86a02cd499664a8233d24c54a6854b788` | package README, developer docs and Specs |
| `larena/property` | `b0253f5f2546ea8bcb8e52b47f31f9f71ae627c7` | package README, module manifest and Specs |
| `larena/storage` | `b361f6e16ed8b5e18d78b47b9d9fa6620b588a16` | package README, developer docs and Specs |
| `larena/filesystem` | `996093e547237f740af8c2cb2a0fca96cabd4c96` | package README, module manifest and Specs |
| `larena/dataview` | `685919570a7a57654528c4577bd119e436189e23` | package README, module manifest and Specs |
| `larena/layout` | `30c4ab5f452584d7c95d6186ed4d4b9ab999a5f5` | package README, module manifest and Specs |
| `larena/ui` | `7506dac223bfe981144e2f40d42ef3826b5d58b8` | package README, module manifest and Specs |
| `larena/admin` | `e9c2969b80f279e38e700ccf2fa013580b2383a0` | package README, module manifest and Specs |

## Precedence

1. Package identity and intended behavior: current `larena-specs` graph and
   curated standards.
2. Actual public behavior: current package code and its developer documentation.
3. Integrated behavior: exact Root lock, tests and fresh runtime evidence.
4. This site: curated explanation and navigation.

When these sources disagree, the page is marked for update instead of choosing
the most convenient statement.

## Declarative interface planning supplement — 2026-09-16

`/ru/standards/declarative-interface-program/` explains the local Specs planning package in `docs/architecture/frontend-interface-program/` and `specs/interface-program/`. It is target architecture, not an adoption receipt. The planning source is committed in Specs at `d252c9693386c50eb46b76b102fe4ba89cddac9f`. Existing implementation locks are not promoted by this planning addition.

## Published interface candidates — 2026-09-17

This supplement describes feature-branch evidence, not accepted main runtime
bindings. Root evidence carrier `98d3dea22f2c593dae2c49228d7980d83d978148`,
implementation `3c0e0946cf0a467e34f6e54ad34e4721d325d6c3` and Layout
`157f60b9be1bc350f91998fddd643288f0089936` prove the two-page editor,
snapshot history, atomic multi-page Setting stage and registered `main` region
inheritance with replace, empty and reset modes.
The exact pair is `ui-ddb249279ff4-smart-dd973536c66f`. Current program coverage
and remaining requirements are projected from Specs
`specs/interface-program/current-execution.json`. Stable package source entries
above are not silently promoted to these candidates.

The exact 25-package artifact catalog for the region candidate has SHA-256
`99eebded463a932827b6986fd5dae2993540ee2c69d80345287d5c73e3080a09`.
SQLite focused acceptance passed 21 tests and 383 assertions; Root regression
passed 1228 tests with 4 skipped and 26,595 assertions; isolated MySQL 8.2
acceptance passed 21 tests and 383 assertions. A committed Git archive installed
all 25 packages from exact artifacts with no path repositories or vendor
symlinks. Chrome 153 accepted both protected targets, region inherit/reset,
atomic required-empty refusal, light/dark and 1440×1000 / 390×844 layouts.
RU/EN and RTL localization, main/GitHub publication and live adoption remain
open. Current execution is bound to Specs `d1def357`.

## Localized instance editor acceptance — 2026-09-18

Root evidence `d7c6785ddfce168003bc3c0753a9c466cdf7a0d0`, implementation
`4391890e8ed479de0695df4159d3e25a63eb68b4`, Admin
`a7a38415dc54de72e467a5502293665a4ba4e1c3` and unchanged Layout
`157f60b9be1bc350f91998fddd643288f0089936` supersede the earlier localization
limitation for English and Russian. Chrome accepted matching language metadata,
wide and narrow layouts, light and dark themes, nested instance field isolation,
two publications, rollback and post-restart readback. The focused suite passed
22 tests and 400 assertions; the exact full Root suite passed 1233 tests with
1229 passed, 4 skipped and 26,612 assertions. RTL is structurally supported by
Admin tests, but no translated RTL locale is published or visually claimed.
The sanitized artifact catalog SHA-256 is
`f60ef9a3618a91aa43d343a08e2b30544557bf16fbfbcf029248e4cad0a79aca`.
Current execution is bound to Specs `b15e507b9f2b71f26db30408b2a33de5591027e8`.
