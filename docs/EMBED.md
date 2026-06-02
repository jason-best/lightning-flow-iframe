# Embed on your website

## JavaScript widget (recommended)

```html
<div id="tl-flow-embed"></div>

<script>
  window.tlFlowEmbed = {
    embedUrl: 'https://your-site.force.com/site-prefix/FlowIframeEmbed',
    flow: 'Your_Flow_API_Name',
    endUrl: 'https://yoursite.com/thanks',
    inputVars: ['recordId', 'source'],
    params: { recordId: '001xxx', source: 'homepage' },
    height: '75px',
    heightPadding: 20,
    ease: true,
    easeSpeed: 0.2,
    lazy: true,
    allowedOrigin: 'https://your-site.force.com'
  };
</script>
<script src="https://cdn.jsdelivr.net/gh/jason-best/lightning-flow-iframe@1.0.0/embed/three-levers-flow-embed.js"></script>
```

Define `window.tlFlowEmbed` **before** the widget script.

### CDN

- **jsDelivr (pinned tag):** `https://cdn.jsdelivr.net/gh/jason-best/lightning-flow-iframe@1.0.0/embed/three-levers-flow-embed.js`
- **jsDelivr (latest release):** `https://cdn.jsdelivr.net/gh/jason-best/lightning-flow-iframe@latest/embed/three-levers-flow-embed.js`

### Self-host

Copy [`embed/three-levers-flow-embed.js`](../embed/three-levers-flow-embed.js) to your web server.

## Manual iframe

```html
<iframe
  src="https://your-site.force.com/site-prefix/FlowIframeEmbed?flow=Your_Flow&endUrl=https://yoursite.com/thanks&inputVars=recordId&recordId=001xxx"
  width="100%"
  height="600"
  title="Salesforce Flow">
</iframe>
```

Add a `message` listener for `{ frameHeight: number }` or use the widget script.

## Iframe URL parameters

| Parameter | Required | Description |
|-----------|----------|-------------|
| `flow` | Yes | Flow Developer Name |
| `endUrl` | No | Parent redirect on `FINISHED` |
| `inputVars` | No | Comma-separated allowlist of param names passed as String flow inputs |
| other params | No | Flow inputs only if listed in `inputVars` |

Full reference: [Querystring variables](https://threelevers.com/support/products/lightning-flow-iframe/querystring-variables/)

## Metadata

After load, `window.tlFlowEmbedInfo` includes documentation and support URLs.
