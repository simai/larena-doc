# Larena documentation source map

Observed on 2026-09-30. This file records the factual basis of the first Russian
edition; it does not change ownership of the sources.

| Source | Revision | Use |
| --- | --- | --- |
| `simai/docara` | tag `v2.11.0`, commit `907f361af33425b935cf379fdc65596e8d7ac9f0` | Site compiler and schemas |
| `simai/larena-specs` | `30c908f24d72aa437f8354b8de772f629aad89d9` | Canonical package identities, requirements and standards; later backup-only changes were reviewed as outside the 12-package first edition |
| `simai/larena-workspace` | `614c823837d27664ddb6e8c52b5277239ee8d6fa` | Development assembly and package locations |
| `simai/larena` | `f0d5821fad633c28228f2b9110bd30d1cb5323c9` | Executable entry app, installation and integrated checks |

## Package revisions

| Package | Revision | Primary human source |
| --- | --- | --- |
| `larena/core` | `707790e8a5817182bc07618353d2ab7fa09ff069` | package README, module manifest and Specs |
| `larena/setting` | `e0b8c3c44f2bbe8e5dfb201e876b41b06a810d22` | package README, module manifest and Specs |
| `larena/lang` | `b668a2ff3d4d39958938a40ce7a57771d554f101` | package README, module manifest and Specs |
| `larena/auth` | `640c6c2f6c791fed1590b39f3920e65b511c7ccb` | package README, developer docs and Specs |
| `larena/access` | `d8fcd4ceae598991f93f05d5b439e76b3c9f9f4a` | package README, developer docs and Specs |
| `larena/property` | `d0053051ad9616cc24c460ffea249028c881649d` | package README, module manifest and Specs |
| `larena/storage` | `17e9df2274bb027b08308c0522ff6c42bd4e47ee` | package README, developer docs and Specs |
| `larena/filesystem` | `996093e547237f740af8c2cb2a0fca96cabd4c96` | package README, module manifest and Specs |
| `larena/dataview` | `a838384e9dc3b143116a9c60dff3eb952feaac9b` | package README, module manifest and Specs |
| `larena/layout` | `5c098f51e497944f50ee8ae719d91077b949e0ed` | package README, module manifest and Specs |
| `larena/ui` | `2b45c2ac1eb99ac962a60e13fdf6b4bd978982f0` | package README, module manifest and Specs |
| `larena/admin` | `3f6fcd87f0c148c487cf4d7792acb50399cb20a7` | package README, module manifest and Specs |

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
