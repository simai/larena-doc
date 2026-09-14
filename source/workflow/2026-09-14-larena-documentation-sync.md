# Larena documentation synchronization

Status: complete

## Outcome

Public Larena documentation is checked against the exact Specs, Root, Workspace
and package revisions. A public-contract change identifies affected pages and
blocks acceptance until maintainers record either `documentation_updated` or
`documentation_not_affected` with evidence and a reason.

## Source-of-truth boundaries

- Requirements and normative decisions remain in `larena-specs`.
- Package contracts and maintainer documents remain in package repositories.
- Internal developer-documentation coverage remains owned by Larena Root's
  existing `validate-developer-documentation-delta.php` gate.
- Public reader guidance, the impact map and its acceptance decisions live in
  `larena-doc`.
- Docara consumes a generated public-contract projection; it does not import or
  rewrite source text.

## Reuse review

- Extend the existing Root documentation-delta gate instead of adding a second
  competing QA entry point.
- Use Docara's supported `documentation_tracking` contract and lock file for
  source-entity-to-route verification.
- Add only the missing cross-repository impact and decision layer in
  `larena-doc`.

## Delivery batches

1. Add a machine-readable map for pages, source repositories, public-contract
   paths, verified revisions and decisions.
2. Add one CLI for impact, status, acceptance recording, stale reports and the
   deterministic Docara source projection.
3. Enable Docara tracking and accept the initial 12 package-page bindings.
4. Connect the existing Root gate to the public documentation checker when the
   sibling documentation repository is available, with a strict CI mode.
5. Update maintainer guidance and verify negative/positive paths, Docara build,
   static output and Root QA integration.

## Acceptance

- All 12 package pages have explicit Specs and package contract sources.
- A simulated public-contract change returns affected pages and a failing gate.
- A matching `documentation_updated` or `documentation_not_affected` decision
  makes the same change acceptable; missing reason or evidence fails.
- Internal-only paths do not mark unrelated pages stale.
- Docara reports all 12 tracked package entities as current.
- Root `docs:delta:check` includes the public-documentation result and passes on
  the frozen assembly.
- No source text is copied automatically between repositories.

## Simplicity review

Primary outcome: a package author receives one precise list of public pages to
review before a contract change can be accepted.

Primary scenario: change a Specs or package public-contract file, run the Root
documentation check, update the indicated pages, record one justified decision,
then rerun the same check.

The design reuses the two existing engines and introduces one map plus one CLI.
Generated projection and reports stay derived. The protected complexity is the
separation between normative Specs, package-owned technical facts and curated
public explanations.

## Evidence

- Machine map: 22 pages, 15 source repositories and 12 Docara package entities.
- Impact contract test: 12 assertions passed, including negative acceptance and
  internal-only change cases.
- Public synchronization check: passed with no stale source or page.
- Docara tracking: 12 current, zero new, changed, missing, unverified or orphan.
- Root developer-documentation gate: 132 of 132 internal records plus passing
  public synchronization result.
- Docara validation, 22-page production build and static verification passed;
  44 HTML files and 1,974 local references had zero broken links.
- HTTPS smoke: `/ru/` and the updated operations guidance returned from
  `https://larena-doc.test`.
