# Publishing lightning-flow-iframe to GitHub

Run from the repository root (`lightning-flow-iframe`).

## First-time publish

```bash
git init
git add -A
git commit --trailer "Co-authored-by: Cursor <cursoragent@cursor.com>" -m "Initial public release: FlowIframeEmbed source and embed widget v1.0.0"
gh repo create jason-best/lightning-flow-iframe --public --source=. --remote=origin --push
```

If the remote already exists:

```bash
git remote add origin https://github.com/jason-best/lightning-flow-iframe.git
git branch -M main
git push -u origin main
```

## Tag and GitHub Release (embed JS asset)

```bash
git tag -a v1.0.0 -m "Flow embed widget and FlowIframeEmbed metadata v1.0.0"
git push origin v1.0.0
gh release create v1.0.0 embed/three-levers-flow-embed.js \
  --title "v1.0.0" \
  --notes "Initial public release. Install FlowIframeEmbed via 2GP or deploy force-app/main/default. Host embed/three-levers-flow-embed.js from your site or jsDelivr."
```

## Subsequent releases

1. Sync from ThreeLeversDevOrg per `SYNC.md`.
2. Bump `INFO.version` in `embed/three-levers-flow-embed.js` when the widget changes.
3. Commit, tag `vX.Y.Z`, push tag, `gh release create` with the embed file attached (optionally attach a zip of `wordpress/lightning-flow-iframe/` for WordPress uploads).

## CDN (optional)

```html
<script src="https://cdn.jsdelivr.net/gh/jason-best/lightning-flow-iframe@v1.0.0/embed/three-levers-flow-embed.js"></script>
```

Release download URL:

`https://github.com/jason-best/lightning-flow-iframe/releases/download/v1.0.0/three-levers-flow-embed.js`
