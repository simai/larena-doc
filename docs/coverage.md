# First-edition coverage

| Area | Pages | Audience | Reader outcome | Source basis | Status |
| --- | --- | --- | --- | --- | --- |
| Start | `/ru/start/` | developer | Run the supported developer entry flow | Root install docs | reviewed |
| Concepts | `/ru/concepts/` | all | Understand package ownership and data-to-UI flow | Specs, Root architecture | reviewed |
| Site owner's guide | `/ru/cms/` and its nine skill pages (`install`, `users-roles`, `storage`, `pages`, `menus-languages`, `files`, `seo`, `backup`, `sitepack`) | site owner, administrator, editor | Install the site, give access and run content, languages, files, SEO, backups and site transfer | Root install scripts and routes; Admin, Auth, Access, Storage, Layout, Setting, File Manager, Filesystem and Backup code | reviewed against code 2026-11-18 |
| Development | `/ru/development/` | developer | Follow the verified part of the materials-catalog scenario | Root checks and core-12 traceability | reviewed with limits |
| Standards | `/ru/standards/` | developer, maintainer | Find the canonical rule before changing a contract | Specs standards | reviewed |
| Packages | `/ru/packages/` | developer | Locate ownership, entry points, limitations and diagnostics for all twelve packages | package repos and Specs | reviewed |
| Operations | `/ru/operations/` | maintainer | Rebuild and diagnose the local documentation site | Docara 2.9.0 | reviewed |
| Versions | `/ru/versions/` | all | Identify the documented source revisions and maturity limits | locks, Git and evidence | reviewed |

Every public page contains its audience, expected outcome, source basis and
review state in the page body because Docara front matter deliberately accepts
only its published metadata contract.

`contracts/larena-documentation-map.json` provides the machine-readable
equivalent for 35 pages and 17 source repositories (the owner's guide pages
also track `larena/file-manager` and `larena/backup`; the older developer page
`/ru/cms/login-composition/` is not mapped yet). Twelve package pages
also bind to neutral Docara source entities; their accepted state lives in
`documentation.lock.json`.
