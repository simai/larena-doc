# Larena Documentation

Russian documentation for the Larena developer foundation. The site is built
with Docara 2.9.0 and is intended to be served locally at
`https://larena-doc.test`.

## Build

```bash
LARENA_DOC_PHP="${LARENA_DOC_PHP:-/Applications/ServBay/package/php/8.4/8.4.20/bin/php}"
LARENA_DOC_COMPOSER="${LARENA_DOC_COMPOSER:-/Applications/ServBay/package/bin/composer}"
"$LARENA_DOC_PHP" "$LARENA_DOC_COMPOSER" install
"$LARENA_DOC_PHP" vendor/bin/docara doctor --json
"$LARENA_DOC_PHP" vendor/bin/docara build production
"$LARENA_DOC_PHP" vendor/bin/docara verify-static build_production
```

The documentation toolchain requires PHP 8.4.1 or newer. The defaults above
select the verified ServBay PHP 8.4.20 runtime; override the variables when an
equivalent runtime is installed elsewhere.

The generated directory is `build_production`; edit Markdown under
`content/ru` instead of generated files.

## Documentation synchronization

The repository tracks every public page against exact Larena Specs, Root,
Workspace and package revisions. Run the complete local gate before accepting
a package change:

```bash
"$LARENA_DOC_PHP" "$LARENA_DOC_COMPOSER" docs:status
"$LARENA_DOC_PHP" "$LARENA_DOC_COMPOSER" docs:check
```

The machine-readable map is
`contracts/larena-documentation-map.json`; accepted decisions are appended to
`contracts/larena-documentation-decisions.json`. The generated Docara source
contract and `documentation.lock.json` must remain current. See
`docs/maintenance.md` for the update procedure.

## Local site

ServBay should serve `build_production` as the document root for
`larena-doc.test`. After every complete build, verify the exact HTTPS target:

```bash
curl -fsSI https://larena-doc.test/ru/
```

## Sources

The frozen source inventory, coverage and maintenance procedure live under
[`docs/`](docs/). Canonical requirements remain in `larena-specs`; package
implementation and exact technical contracts remain in their package
repositories.
