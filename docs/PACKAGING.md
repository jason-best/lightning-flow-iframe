# Packaging (Three Levers maintainers)

Public consumers install the published unlocked package. **2GP version creation** runs from the private **ThreeLeversDevOrg** monorepo (`force-app/package-flow-iframe/`), not from anonymous clones of this repo.

Workflow:

1. Develop in `ThreeLeversDevOrg` → `force-app/main/default` and sync to `force-app/package-flow-iframe/` (or run `scripts/sync-package-flow-iframe.ps1`).
2. `sf package version create --package "FlowIframeEmbed" ...`
3. Sync source to this public repo per [SYNC.md](../SYNC.md).
4. Update `README.md` install `04t…` id and tag GitHub release.

Namespace: `three_levers`  
Package name: **FlowIframeEmbed**
