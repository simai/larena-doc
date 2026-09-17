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
bindings. Root candidate `0e0340139566dff7d07f56bdd207e194e69e20a0` and Layout
`157f60b9be1bc350f91998fddd643288f0089936` prove the two-page editor,
snapshot history, atomic multi-page Setting stage and registered `main` region
inheritance with replace, empty and reset modes.
The exact pair is `ui-ddb249279ff4-smart-dd973536c66f`. Current program coverage
and remaining requirements are projected from Specs
`specs/interface-program/current-execution.json`. Stable package source entries
above are not silently promoted to these candidates.

The exact 25-package artifact catalog for the region candidate has SHA-256
`99eebded463a932827b6986fd5dae2993540ee2c69d80345287d5c73e3080a09`.
SQLite focused acceptance passed 25 tests and 398 assertions; sequential Root
regression passed 1228 tests with 4 skipped and 26,597 assertions; isolated
MySQL 8.2 acceptance passed 21 tests and 385 assertions. Fresh application
installation and protected Chrome review remain release gates.
