/**
 * Three Levers Flow Embed â€” parent-page widget for FlowIframeEmbed (Salesforce Site).
 *
 * Configure window.tlFlowEmbed before loading this script.
 *
 * Documentation:
 *   https://threelevers.com/support/products/lightning-flow-iframe/javascript/
 *
 * Product index:
 *   https://threelevers.com/support/products/lightning-flow-iframe/
 *
 * Support:
 *   https://threelevers.com/support/contact-support/
 *
 * Contact:
 *   https://threelevers.com/contact/
 *
 * @version 1.0.0
 * @copyright Three Levers
 */
(function () {
  'use strict';

  var IFRAME_ID = 'tl-flow-iframe';
  var DEFAULT_CONTAINER = '#tl-flow-embed';
  var RESERVED_PARAMS = ['flow', 'endUrl', 'inputVars'];

  var INFO = {
    name: 'Three Levers Flow Embed',
    version: '1.0.0',
    documentation:
      'https://threelevers.com/support/products/lightning-flow-iframe/javascript/',
    productIndex: 'https://threelevers.com/support/products/lightning-flow-iframe/',
    productSupport: 'https://threelevers.com/support/contact-support/',
    contact: 'https://threelevers.com/contact/',
    website: 'https://threelevers.com',
    repository: 'https://github.com/threelevers/lightning-flow-iframe',
    relatedDocs: {
      querystringVariables:
        'https://threelevers.com/support/products/lightning-flow-iframe/querystring-variables/',
      finishUrl:
        'https://threelevers.com/support/products/lightning-flow-iframe/finish-url/',
      salesforceSetup:
        'https://threelevers.com/support/products/lightning-flow-iframe/salesforce-setup/',
      wordpress: 'https://threelevers.com/support/products/lightning-flow-iframe/wordpress/'
    }
  };

  if (typeof window !== 'undefined') {
    window.tlFlowEmbedInfo = INFO;
  }

  function logError(message) {
    if (typeof console !== 'undefined' && console.error) {
      console.error(
        '[tlFlowEmbed] ' +
          message +
          ' Documentation: ' +
          INFO.documentation +
          ' Support: ' +
          INFO.productSupport
      );
    }
  }

  function resolveContainer(selector) {
    if (!selector) {
      return document.querySelector(DEFAULT_CONTAINER);
    }
    if (typeof selector === 'string') {
      if (selector.charAt(0) === '#' || selector.charAt(0) === '.') {
        return document.querySelector(selector);
      }
      return document.getElementById(selector);
    }
    if (selector.nodeType === 1) {
      return selector;
    }
    return null;
  }

  function normalizeInputVars(inputVars) {
    if (!inputVars) {
      return [];
    }
    if (Array.isArray(inputVars)) {
      return inputVars.map(function (k) {
        return String(k).trim();
      }).filter(Boolean);
    }
    if (typeof inputVars === 'string') {
      return inputVars.split(',').map(function (k) {
        return k.trim();
      }).filter(Boolean);
    }
    return [];
  }

  function buildIframeSrc(config) {
    var base = String(config.embedUrl || '').trim();
    if (!base) {
      return '';
    }

    var url;
    try {
      url = new URL(base);
    } catch (e) {
      logError('embedUrl is not a valid URL: ' + base);
      return '';
    }

    var params = new URLSearchParams(url.search);
    params.set('flow', String(config.flow).trim());

    var endUrl = config.endUrl;
    if (endUrl != null && String(endUrl).trim().length > 0) {
      params.set('endUrl', String(endUrl).trim());
    }

    var allowedKeys = normalizeInputVars(config.inputVars);
    if (allowedKeys.length > 0) {
      params.set('inputVars', allowedKeys.join(','));
    }

    var flowParams = config.params && typeof config.params === 'object' ? config.params : {};
    Object.keys(flowParams).forEach(function (key) {
      if (RESERVED_PARAMS.indexOf(key) !== -1) {
        return;
      }
      if (allowedKeys.length > 0 && allowedKeys.indexOf(key) === -1) {
        return;
      }
      if (allowedKeys.length === 0) {
        return;
      }
      var value = flowParams[key];
      if (value != null && String(value).length > 0) {
        params.set(key, String(value));
      }
    });

    url.search = params.toString();
    return url.toString();
  }

  function buildIframeStyle(config) {
    var height = config.height != null ? String(config.height) : '75px';
    var style = 'height:' + height + ';width:100%;border:0;display:block;';
    if (config.ease === true || config.ease === 'true') {
      var speed = config.easeSpeed != null ? Number(config.easeSpeed) : 0.2;
      if (isNaN(speed) || speed < 0) {
        speed = 0.2;
      }
      style += 'transition:height ' + speed + 's ease;';
    }
    return style;
  }

  function mountIframe(config) {
    var container = resolveContainer(config.container);
    if (!container) {
      logError('Container not found. Set container to a valid selector or add <div id="tl-flow-embed"></div>.');
      return null;
    }

    var src = buildIframeSrc(config);
    if (!src) {
      return null;
    }

    var iframe = document.createElement('iframe');
    iframe.id = IFRAME_ID;
    iframe.setAttribute('src', src);
    iframe.setAttribute('width', '100%');
    iframe.setAttribute('scrolling', 'no');
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('title', config.title || 'Salesforce Flow');
    iframe.setAttribute('style', buildIframeStyle(config));

    if (config.lazy === true || config.lazy === 'true') {
      iframe.setAttribute('loading', 'lazy');
    }

    container.appendChild(iframe);
    return iframe;
  }

  function attachResizeListener(config, iframe) {
    var padding = config.heightPadding != null ? Number(config.heightPadding) : 20;
    if (isNaN(padding)) {
      padding = 20;
    }

    var allowedOrigin = config.allowedOrigin != null ? String(config.allowedOrigin).trim() : '';

    window.addEventListener('message', function (event) {
      if (!event.data || typeof event.data.frameHeight !== 'number') {
        return;
      }
      if (allowedOrigin && event.origin !== allowedOrigin) {
        return;
      }
      if (event.source !== iframe.contentWindow) {
        return;
      }
      iframe.style.height = event.data.frameHeight + padding + 'px';
    });
  }

  function init() {
    var config = window.tlFlowEmbed;
    if (!config || typeof config !== 'object') {
      logError('window.tlFlowEmbed is not defined. Set it before loading three-levers-flow-embed.js.');
      return;
    }

    if (!config.embedUrl || !String(config.embedUrl).trim()) {
      logError('embedUrl is required.');
      return;
    }
    if (!config.flow || !String(config.flow).trim()) {
      logError('flow is required (Flow API Developer Name).');
      return;
    }

    var iframe = mountIframe(config);
    if (iframe) {
      attachResizeListener(config, iframe);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
