# Maintainer sync (Three Levers internal)

The canonical development monorepo is private. When shipping a release, sync into this public repository:

| Private (ThreeLeversDevOrg) | Public (this repo) |
|-----------------------------|-------------------|
| `force-app/flow-iframe-embed/` | `force-app/main/default/` |
| `embed/three-levers-flow-embed.js` | `embed/three-levers-flow-embed.js` |

After sync:

1. Bump `INFO.version` in `embed/three-levers-flow-embed.js` if the embed script changed.
2. Update install URLs in `README.md` when a new package version is published.
3. Commit, tag (`vX.Y.Z`), and create a GitHub Release (attach `embed/three-levers-flow-embed.js`).
