# Docara release and Larena documentation upgrade

Status: complete

## Outcome

Publish the already integrated Docara `origin/main` changes as the next stable
release, upgrade Larena documentation through Docara's transactional project
workflow, and prevent future builds from silently treating an older stable tag
as the current upstream state.

## Scope

- Prepare the Docara release from a clean worktree based on the exact current
  `origin/main`; preserve the dirty local Docara checkout.
- Update release metadata, run the repository release checks, push the verified
  commit to `main`, create the stable tag and let the existing GitHub release
  workflow publish immutable artifacts.
- Upgrade `/Users/rim/Documents/GitHub/larena-doc` to the released version.
- Add a deterministic freshness check that compares the installed Docara source
  revision with the latest stable tag and reports any commits present on
  upstream `main` after that tag.
- Rebuild and verify the Larena documentation site, then commit and push its
  reviewed changes to `main`.

## Boundaries

- Do not copy or commit any changes from the dirty Docara checkout.
- Do not force-push, rewrite history, delete tags or expose credentials.
- Do not change Larena package behavior or documentation content unrelated to
  the engine upgrade.
- Treat the release commit, stable tag, GitHub release and Larena project
  upgrade as separately verified states.

## Rollback and stop conditions

- Before publication, discard only the isolated release worktree if a release
  check fails.
- Push the release commit with an exact remote-head lease. Stop if `origin/main`
  changes after candidate preparation.
- Do not move or replace an existing tag.
- The Larena project upgrade must retain Docara's transactional rollback state;
  stop before commit if build, static verification or documentation checks fail.

## Acceptance

- A new stable Docara tag resolves to the exact verified release commit.
- GitHub reports the immutable release and Composer can resolve the new stable
  version.
- Larena Doc pins that exact version and records its source revision.
- The freshness command detects a deliberately stale stable/main relationship
  and passes for the released current state.
- Documentation impact tests, Docara validation, production build,
  `verify-static` and HTTPS smoke pass.
- Both repositories' intended `main` refs match their remotes, and unrelated
  local Docara work remains untouched.

## Simplicity review

The smallest complete correction is one normal Docara patch release, one
transactional consumer upgrade and one explicit freshness check. The existing
package tracking and release workflows remain the owners of their current
responsibilities.

## Evidence

- Docara `v2.9.1` resolves to release commit
  `72b475b06ad3acf5c07f9a9f85d123c5620021fd`; the GitHub release was published
  on 2026-09-14. Two clean package builds produced the same archive SHA-256:
  `669cc9637da03985800b56bf0b71a92050594be723b1bb837b1a104fa599e26a`.
- The release publication marker is Docara main commit
  `8fdbdc79aa1afc0e7b660ecbb47e6dfc1177cf95`. The local dirty Docara checkout
  was not used as a release source and was not modified by this work.
- Larena Doc pins Docara `2.9.1` at the exact tagged source revision. Engine and
  Framework projections validate against that runtime.
- The freshness contract test passes 7 assertions. Live upstream status is
  `current`: installed `2.9.1`, latest stable `v2.9.1`, and no substantive
  commits remain after the tag. The publication-only release marker is ignored.
- The documentation impact gate passes with 15 sources, 22 pages, zero stale
  sources and 12 current package bindings. Specs revision
  `5209e5f25158b2127eff3a21f51a716ca68f01f9` was reviewed; its backup package,
  generated dependency graph and project-management changes do not alter the
  documented behavior of the 12-package first edition. The decision and exact
  changed paths are recorded in the immutable decision ledger.
- `doctor` passes all five registry checks. Project validation reports zero
  errors, zero undeclared pages, 24 passed checks and 22 advisory editorial
  reviews. Production build creates 22 authored pages; static verification
  checks 44 HTML files and 1,928 local references with no broken references.
- HTTPS returns 200 for `/`, `/ru/` and `/ru/packages/storage/`. Browser smoke
  confirms the nested page, breadcrumbs, one-result `права доступа` search,
  mobile navigation at 390 by 844 pixels, and zero console warnings or errors.
- Local rollback state for the engine update remains under
  `.docara/rollbacks/20260914125930-95348354ea57` and is excluded from Git.

## Follow-up defects

- Docara 2.9.1 rejects `upgrade --to=2.9.1 --dry-run` as an action conflict
  because absent Symfony options are compared with `false` instead of `null`.
- The transactional upgrade candidate verifies the old project-owned Framework
  projections before synchronizing them, which causes
  `FRAMEWORK_RUNTIME_PROJECTION_MISMATCH` for this 2.9.0 to 2.9.1 transition.

The Larena project was upgraded with the documented low-level fallback: exact
Composer resolution, hash-bound engine update, then synchronization of the
package-owned Framework projection files. The resulting project passed every
acceptance gate above. The two transactional-upgrade defects belong to a later
Docara patch and do not make the installed Larena build stale.
