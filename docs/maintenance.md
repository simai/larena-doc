# Documentation maintenance

## Ownership

- Canonical product requirements and standards remain in `larena-specs`.
- Exact package contracts and maintainer notes remain in package repositories.
- Russian explanations, learning paths and navigation live in `larena-doc`.
- Generated `build_*` files are never edited as source.

## Update procedure

1. Commit the Specs or package public-contract change, then run
   `composer docs:status` in `larena-doc`. The report identifies the exact
   source paths and affected public pages.
2. Review the canonical Specs and package-owned technical documentation.
   Update only the reported pages whose reader outcome changed.
3. Record one explicit source-revision decision. For an actual documentation
   update, list every affected page:

   ```bash
   php scripts/documentation-impact.php accept \
     --source=package.storage \
     --decision=documentation_updated \
     --reason="Updated the storage contract and matching public guides." \
     --page=concepts \
     --page=development \
     --page=development.materials-catalog \
     --page=operations \
     --page=packages.storage
   ```

   If the public-looking source change does not alter the documented behavior,
   record the reason instead:

   ```bash
   php scripts/documentation-impact.php accept \
     --source=package.storage \
     --decision=documentation_not_affected \
     --reason="The signature is unchanged; only package-owned wording changed."
   ```

   Acceptance is refused while mapped source files are uncommitted. A
   `documentation_updated` decision is also refused unless every affected page
   is named. The ledger stores the revision range, changed paths, reason and
   hashes of updated pages.
4. Run `php vendor/bin/docara documentation status --json`. For each `changed`
   package entity, create and apply Docara's hash-bound acceptance plan:

   ```bash
   php vendor/bin/docara documentation accept \
     --source=larena --key=larena.package.storage \
     --route=/ru/packages/storage/ --review=ai_verified --dry-run --json
   php vendor/bin/docara documentation accept --apply=<plan-sha256> --json
   ```

5. Run `composer docs:check`. It verifies the impact map, decision state,
   generated source projection, the 12 Docara bindings, the isolated contract
   tests and Docara release freshness. The freshness gate compares the exact
   version and source revision in `composer.lock` with the latest stable tag,
   then rejects substantive commits left on upstream `main` after that tag.
   The publication-only `.github/release-request.json` marker is ignored.

   Use `composer docs:engine:status` for an informational report. Override the
   default Composer source URL only for a controlled local mirror:

   ```bash
   DOCARA_UPSTREAM_REPOSITORY=/path/to/docara composer docs:engine:status
   ```
6. Run `"$LARENA_DOC_PHP" vendor/bin/docara validate project --json` with PHP
   8.4.1 or newer and review authoring reports.
7. Run a complete production build and `verify-static`, serve those exact
   bytes and check representative desktop, mobile, keyboard, search and
   direct-route scenarios.
8. Update `docs/source-map.md`, `docs/coverage.md` and `docs/gaps.md` when
   their human-readable summaries changed.

Root's existing `composer docs:delta:check` remains the common QA entry point.
It validates internal developer-documentation coverage and calls this public
gate when a sibling `larena-doc` checkout exists. Set `LARENA_DOC_ROOT` for a
different checkout and `LARENA_PUBLIC_DOCS_REQUIRED=1` when CI must fail if the
public documentation repository is absent.

The checker generates only a neutral contract projection for Docara. It never
copies prose from Specs, README files or code into Markdown.

Adding, deleting or moving a route always requires a complete build. A later
English locale should mirror the stable translation keys and may use different
physical paths only when both pages declare the same key.
