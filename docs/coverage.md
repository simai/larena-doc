# First-edition coverage

| Area | Pages | Audience | Reader outcome | Source basis | Status |
| --- | --- | --- | --- | --- | --- |
| Start | `/ru/start/` | developer | Run the supported developer entry flow | Root install docs | reviewed |
| Concepts | `/ru/concepts/` | all | Understand package ownership and data-to-UI flow | Specs, Root architecture | reviewed |
| CMS work | `/ru/cms/` | administrator | Know which current admin actions are bounded and testable | Root docs and core-12 traceability | reviewed with limits |
| Development | `/ru/development/` | developer | Follow the verified part of the materials-catalog scenario | Root checks and core-12 traceability | reviewed with limits |
| Standards | `/ru/standards/` | developer, maintainer | Find the canonical rule before changing a contract | Specs standards | reviewed |
| Packages | `/ru/packages/` | developer | Locate ownership, entry points, limitations and diagnostics for all twelve packages | package repos and Specs | reviewed |
| Operations | `/ru/operations/` | maintainer | Rebuild and diagnose the local documentation site | Docara 2.9.0 | reviewed |
| Versions | `/ru/versions/` | all | Identify the documented source revisions and maturity limits | locks, Git and evidence | reviewed |

Every public page contains its audience, expected outcome, source basis and
review state in the page body because Docara front matter deliberately accepts
only its published metadata contract.

`contracts/larena-documentation-map.json` provides the machine-readable
equivalent for all 22 pages and 15 source repositories. Twelve package pages
also bind to neutral Docara source entities; their accepted state lives in
`documentation.lock.json`.
