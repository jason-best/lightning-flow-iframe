# Installation (Salesforce)

## Option A — Install unlocked package (recommended)

**Version:** `1.1.0-2` (released)  
**Subscriber package version Id:** `04tgL000000GUerQAG`

| Org type | Install URL |
|----------|-------------|
| Production | https://login.salesforce.com/packaging/installPackage.apexp?p0=04tgL000000GUerQAG |
| Sandbox | https://test.salesforce.com/packaging/installPackage.apexp?p0=04tgL000000GUerQAG |

CLI:

```bash
sf package install --package 04tgL000000GUerQAG --target-org <alias>
```

## Option B — Deploy from source

Requires an org with the `three_levers` namespace (scratch org with namespace, or matching packaging org).

```bash
sf org create scratch --definition-file config/project-scratch-def.json --alias flow-embed-scratch --set-default
sf project deploy start --manifest manifest/package.xml --target-org flow-embed-scratch
```

For unpackaged dev without the namespace package, edit `FlowIframeEmbed.page` and change `three_levers:FlowOut` to `c:FlowOut` after deploying the Aura app locally.

## Post-install checklist

1. Create or activate the **Screen Flow** to embed (note its **Developer Name** for `?flow=`).
2. **Setup → Sites** (or Experience Cloud): add the **FlowIframeEmbed** Visualforce page.
3. **Guest User Profile** (public embed): grant access to the VF page and run flows as needed.
4. **Clickjack / CSP**: allow your external parent domain to frame the Site URL.
5. Copy the public Site URL path to **FlowIframeEmbed** — this is your `embedUrl` on the parent site.

See [Salesforce setup](https://threelevers.com/support/products/lightning-flow-iframe/salesforce-setup/) for step-by-step guidance.
