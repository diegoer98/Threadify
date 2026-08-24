<?php
/**
 * Threadify Expansion — Cart Module Source (dormant)
 *
 * Ported verbatim from the live homepage's cart badge/drawer script
 * (window.ThreadifyCart, localStorage key threadify_cart_v1) so it isn't
 * lost, but rendered INERT wherever it's used -- wrapped by the caller in
 * <script type="text/plain"> instead of a real <script> tag -- per Diego's
 * 2026-08-24 call to defer cart UI until the new page layout is settled.
 * Kept as one shared source (rather than duplicated per page) so /shop/
 * and the future homepage template stay in sync and reactivation is a
 * one-line change in both places.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function tse_cart_module_source() {
    return <<<'JS'
/* Threadify shared cart — used by every page (homepage, order builder, product detail,
   fundraisers, events). Cart lives in localStorage so it persists across pages and visits.

   Public API: window.ThreadifyCart = { add, remove, setQty, clear, items, count, open, close }

   Design: this is a lightweight "quick add while browsing" cart, not the full order flow.
   The full guided flow (colors, quantities, embroidery placement, logo upload, quote submission)
   still lives in threadify-builder.html. "Continue to quote" in the cart drawer hands cart items
   for one industry off to that page (via sessionStorage) and lets the existing builder logic
   (chooseColor/lineSet) take it from there — no duplicated checkout logic. */

(function () {
  "use strict";

  var STORAGE_KEY = "threadify_cart_v1";
  var IMPORT_KEY = "threadify_cart_import";
  var IMGBASE = "Sanmar%20Data/SanMar%20Images/SDL/";

  function readCart() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      var arr = raw ? JSON.parse(raw) : [];
      return Array.isArray(arr) ? arr : [];
    } catch (e) { return []; }
  }
  function writeCart(items) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(items)); } catch (e) {}
  }
  function lineKey(style, colorIdx) { return style + "::" + colorIdx; }

  var cart = readCart();

  function count() {
    var n = 0;
    for (var i = 0; i < cart.length; i++) n += cart[i].qty;
    return n;
  }
  function items() { return cart.slice(); }

  function add(entry) {
    var qty = Math.max(1, parseInt(entry.qty, 10) || 1);
    var existing = null;
    for (var i = 0; i < cart.length; i++) {
      if (cart[i].style === entry.style && cart[i].colorIdx === entry.colorIdx) { existing = cart[i]; break; }
    }
    if (existing) {
      existing.qty += qty;
    } else {
      cart.push({
        style: entry.style,
        colorIdx: entry.colorIdx,
        colorName: entry.colorName || "",
        colorPhoto: entry.colorPhoto || "",
        brand: entry.brand || "",
        title: entry.title || "",
        price: typeof entry.price === "number" ? entry.price : null,
        industryKey: entry.industryKey || "",
        qty: qty
      });
    }
    writeCart(cart);
    render();
  }
  function remove(style, colorIdx) {
    cart = cart.filter(function (it) { return !(it.style === style && it.colorIdx === colorIdx); });
    writeCart(cart);
    render();
  }
  function setQty(style, colorIdx, qty) {
    var q = parseInt(qty, 10);
    if (isNaN(q) || q <= 0) { remove(style, colorIdx); return; }
    for (var i = 0; i < cart.length; i++) {
      if (cart[i].style === style && cart[i].colorIdx === colorIdx) { cart[i].qty = q; break; }
    }
    writeCart(cart);
    render();
  }
  function clear() {
    cart = [];
    writeCart(cart);
    render();
  }

  /* ---------- UI ---------- */
  var els = {};
  var isOpen = false;

  function injectStyle() {
    if (document.getElementById("tf-cart-style")) return;
    var css = ""
      + ".tf-cart-btn{display:none;align-items:center;justify-content:center;position:relative;width:40px;height:40px;border-radius:var(--radius,6px);border:1px solid rgba(26,26,24,0.18);background:transparent;cursor:pointer;flex:0 0 auto;transition:.15s ease;margin-right:2px;}"
      + ".tf-cart-btn:hover{border-color:var(--brass,#B8922A);background:var(--cream,#F5F0E8);}"
      + ".tf-cart-btn.tf-show{display:inline-flex;}"
      + ".tf-cart-btn svg{width:20px;height:20px;stroke:var(--forest,#2E4A35);fill:none;stroke-width:1.8;}"
      + ".tf-cart-badge{position:absolute;top:-6px;right:-6px;min-width:18px;height:18px;padding:0 4px;border-radius:100px;background:var(--brass,#B8922A);color:var(--ink,#1A1A18);font-family:'Inter',sans-serif;font-size:0.68rem;font-weight:700;display:flex;align-items:center;justify-content:center;line-height:1;}"
      + ".tf-cart-backdrop{position:fixed;inset:0;background:rgba(26,26,24,0.45);opacity:0;pointer-events:none;transition:opacity .2s ease;z-index:200;}"
      + ".tf-cart-backdrop.tf-open{opacity:1;pointer-events:auto;}"
      + ".tf-cart-drawer{position:fixed;top:0;right:0;bottom:0;width:400px;max-width:92vw;background:var(--cream,#F5F0E8);box-shadow:-8px 0 28px rgba(26,26,24,0.18);z-index:201;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .28s ease;font-family:'Inter',sans-serif;color:var(--ink,#1A1A18);}"
      + ".tf-cart-drawer.tf-open{transform:translateX(0);}"
      + ".tf-cart-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid rgba(26,26,24,0.12);flex:0 0 auto;}"
      + ".tf-cart-head h2{font-family:'Bebas Neue',sans-serif;font-weight:400;font-size:1.5rem;letter-spacing:0.02em;color:var(--forest,#2E4A35);margin:0;}"
      + ".tf-cart-close{background:none;border:none;font-size:1.4rem;line-height:1;cursor:pointer;color:var(--stone,#6B6B60);padding:4px 8px;}"
      + ".tf-cart-close:hover{color:var(--brass,#B8922A);}"
      + ".tf-cart-list{flex:1 1 auto;overflow-y:auto;padding:14px 20px;}"
      + ".tf-cart-empty{padding:40px 8px;text-align:center;color:var(--stone,#6B6B60);font-size:0.92rem;}"
      + ".tf-cart-line{display:flex;gap:12px;padding:14px 0;border-bottom:1px solid rgba(26,26,24,0.08);}"
      + ".tf-cart-line:last-child{border-bottom:none;}"
      + ".tf-cart-thumb{width:56px;height:56px;flex:0 0 auto;border:1px solid rgba(26,26,24,0.12);border-radius:5px;background:#fff;overflow:hidden;}"
      + ".tf-cart-thumb img{width:100%;height:100%;object-fit:contain;}"
      + ".tf-cart-info{flex:1;min-width:0;}"
      + ".tf-cart-brand{font-size:0.68rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--brass,#B8922A);font-weight:600;}"
      + ".tf-cart-title{font-size:0.9rem;font-weight:600;margin-top:1px;}"
      + ".tf-cart-color{font-size:0.8rem;color:var(--stone,#6B6B60);margin-top:2px;}"
      + ".tf-cart-row2{display:flex;align-items:center;justify-content:space-between;margin-top:8px;gap:8px;}"
      + ".tf-cart-qty{display:flex;align-items:center;gap:5px;}"
      + ".tf-cart-qty button{width:22px;height:22px;border-radius:4px;border:1px solid var(--forest,#2E4A35);background:transparent;color:var(--forest,#2E4A35);cursor:pointer;font-size:0.85rem;line-height:1;padding:0;}"
      + ".tf-cart-qty button:hover{background:var(--forest,#2E4A35);color:var(--cream,#F5F0E8);}"
      + ".tf-cart-qty span{min-width:20px;text-align:center;font-size:0.85rem;}"
      + ".tf-cart-remove{background:none;border:none;color:var(--stone,#6B6B60);font-size:0.76rem;text-decoration:underline;cursor:pointer;padding:0;}"
      + ".tf-cart-remove:hover{color:var(--brass,#B8922A);}"
      + ".tf-cart-foot{flex:0 0 auto;padding:16px 20px 20px;border-top:1px solid rgba(26,26,24,0.12);background:var(--parchment,#EDE8DC);}"
      + ".tf-cart-total{display:flex;justify-content:space-between;font-size:0.92rem;margin-bottom:12px;}"
      + ".tf-cart-total strong{color:var(--forest,#2E4A35);}"
      + ".tf-cart-note{font-size:0.76rem;color:var(--stone,#6B6B60);margin-top:8px;}"
      + ".tf-cart-cta{display:block;width:100%;text-align:center;font-weight:600;font-size:0.92rem;padding:13px 20px;border-radius:var(--radius,6px);border:1px solid var(--forest,#2E4A35);background:var(--forest,#2E4A35);color:var(--cream,#F5F0E8);cursor:pointer;transition:.18s ease;}"
      + ".tf-cart-cta:hover{background:var(--brass,#B8922A);border-color:var(--brass,#B8922A);color:var(--ink,#1A1A18);}"
      + ".tf-cart-clear{display:block;text-align:center;margin-top:10px;font-size:0.78rem;color:var(--stone,#6B6B60);text-decoration:underline;cursor:pointer;background:none;border:none;width:100%;}"
      + ".tf-cart-clear:hover{color:var(--brass,#B8922A);}"
      + ".tf-added-flash{outline:2px solid var(--brass,#B8922A) !important;}"
      + "@media (max-width:480px){.tf-cart-drawer{width:100vw;max-width:100vw;}}";
    var style = document.createElement("style");
    style.id = "tf-cart-style";
    style.textContent = css;
    document.head.appendChild(style);
  }

  function svgBag() {
    return '<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8V6a6 6 0 0 1 12 0v2"/><path d="M4 8h16l-1.2 12.2a2 2 0 0 1-2 1.8H7.2a2 2 0 0 1-2-1.8L4 8z"/></svg>';
  }

  function buildUi() {
    var mount = document.querySelector(".nav-cta") || document.querySelector(".nav-inner");
    if (!mount) return false;

    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "tf-cart-btn";
    btn.id = "tfCartBtn";
    btn.setAttribute("aria-label", "View cart");
    btn.innerHTML = svgBag() + '<span class="tf-cart-badge" id="tfCartBadge" style="display:none;">0</span>';
    mount.insertBefore(btn, mount.firstChild);

    var backdrop = document.createElement("div");
    backdrop.className = "tf-cart-backdrop";
    backdrop.id = "tfCartBackdrop";

    var drawer = document.createElement("div");
    drawer.className = "tf-cart-drawer";
    drawer.id = "tfCartDrawer";
    drawer.innerHTML =
      '<div class="tf-cart-head"><h2>Your cart</h2><button type="button" class="tf-cart-close" id="tfCartClose" aria-label="Close cart">&times;</button></div>' +
      '<div class="tf-cart-list" id="tfCartList"></div>' +
      '<div class="tf-cart-foot" id="tfCartFoot"></div>';

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    els.btn = btn; els.badge = document.getElementById("tfCartBadge");
    els.backdrop = backdrop; els.drawer = drawer;
    els.list = document.getElementById("tfCartList"); els.foot = document.getElementById("tfCartFoot");

    btn.addEventListener("click", toggle);
    backdrop.addEventListener("click", close);
    document.getElementById("tfCartClose").addEventListener("click", close);
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") close(); });

    return true;
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"]/g, function (c) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", "\"": "&quot;" }[c]; });
  }
  function thumbSrc(it) {
    if(!it.colorPhoto) return ""; return it.colorPhoto.indexOf("http")===0 ? it.colorPhoto : (IMGBASE + "COLOR_PRODUCT_IMAGE/" + encodeURIComponent(it.colorPhoto));
  }

  function primaryIndustry() {
    var tally = {};
    cart.forEach(function (it) {
      var k = it.industryKey || "";
      if (!k) return;
      tally[k] = (tally[k] || 0) + 1;
    });
    var best = null, bestN = 0;
    for (var k in tally) { if (tally[k] > bestN) { best = k; bestN = tally[k]; } }
    return best;
  }

  function renderList() {
    if (!cart.length) {
      els.list.innerHTML = '<div class="tf-cart-empty">Your cart is empty.<br>Browse an industry and add a garment to get started.</div>';
      return;
    }
    els.list.innerHTML = cart.map(function (it) {
      var priceHtml = it.price != null ? ("$" + it.price.toFixed(2) + "+") : "";
      return '<div class="tf-cart-line">' +
        '<div class="tf-cart-thumb"><img src="' + thumbSrc(it) + '" alt="" loading="lazy" onerror="this.style.opacity=0"/></div>' +
        '<div class="tf-cart-info">' +
          '<div class="tf-cart-brand">' + escapeHtml(it.brand) + '</div>' +
          '<div class="tf-cart-title">' + escapeHtml(it.title) + '</div>' +
          '<div class="tf-cart-color">' + escapeHtml(it.colorName) + (priceHtml ? " · " + priceHtml : "") + '</div>' +
          '<div class="tf-cart-row2">' +
            '<div class="tf-cart-qty">' +
              '<button type="button" aria-label="Fewer" data-act="dec" data-style="' + escapeHtml(it.style) + '" data-idx="' + it.colorIdx + '">&minus;</button>' +
              '<span>' + it.qty + '</span>' +
              '<button type="button" aria-label="More" data-act="inc" data-style="' + escapeHtml(it.style) + '" data-idx="' + it.colorIdx + '">+</button>' +
            '</div>' +
            '<button type="button" class="tf-cart-remove" data-act="rm" data-style="' + escapeHtml(it.style) + '" data-idx="' + it.colorIdx + '">remove</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    }).join("");

    Array.prototype.forEach.call(els.list.querySelectorAll("button[data-act]"), function (b) {
      b.addEventListener("click", function () {
        var style = b.getAttribute("data-style"), idx = parseInt(b.getAttribute("data-idx"), 10);
        var act = b.getAttribute("data-act");
        var current = null;
        for (var i = 0; i < cart.length; i++) { if (cart[i].style === style && cart[i].colorIdx === idx) { current = cart[i]; break; } }
        if (!current) return;
        if (act === "inc") setQty(style, idx, current.qty + 1);
        else if (act === "dec") setQty(style, idx, current.qty - 1);
        else if (act === "rm") remove(style, idx);
      });
    });
  }

  function renderFoot() {
    if (!cart.length) { els.foot.innerHTML = ""; return; }
    var totalPieces = count();
    var mainKey = primaryIndustry();
    var mainName = (mainKey && window.THREADIFY_CATALOG && window.THREADIFY_CATALOG[mainKey]) ? window.THREADIFY_CATALOG[mainKey].name : null;
    var otherCount = mainKey ? cart.filter(function (it) { return it.industryKey !== mainKey; }).length : 0;
    var noteHtml = "";
    if (mainName) {
      noteHtml = otherCount
        ? ('<div class="tf-cart-note">Continuing with your ' + escapeHtml(mainName) + ' items. ' + otherCount + ' item(s) from other industries will stay in your cart.</div>')
        : ('<div class="tf-cart-note">You\'ll continue in the ' + escapeHtml(mainName) + ' order builder to set embroidery placement and request your quote.</div>');
    } else {
      noteHtml = '<div class="tf-cart-note">Open an item from an industry page to continue to a quote.</div>';
    }
    els.foot.innerHTML =
      '<div class="tf-cart-total">Total pieces <strong>' + totalPieces + '</strong></div>' +
      '<button type="button" class="tf-cart-cta" id="tfCartGo"' + (mainKey ? '' : ' disabled') + '>Continue to quote &rarr;</button>' +
      noteHtml +
      '<button type="button" class="tf-cart-clear" id="tfCartClear">Clear cart</button>';
    var goBtn = document.getElementById("tfCartGo");
    if (goBtn) goBtn.addEventListener("click", goToQuote);
    var clearBtn = document.getElementById("tfCartClear");
    if (clearBtn) clearBtn.addEventListener("click", clear);
  }

  function goToQuote() {
    var mainKey = primaryIndustry();
    if (!mainKey) return;
    var selected = cart.filter(function (it) { return it.industryKey === mainKey; });
    try {
      sessionStorage.setItem(IMPORT_KEY, JSON.stringify(selected.map(function (it) {
        return { style: it.style, colorIdx: it.colorIdx, qty: it.qty };
      })));
    } catch (e) {}
    window.location.href = "/order-builder/?industry=" + encodeURIComponent(mainKey);
  }

  function render() {
    var n = count();
    if (els.badge) {
      els.badge.textContent = n;
      els.badge.style.display = n > 0 ? "flex" : "none";
    }
    if (els.btn) {
      els.btn.classList.toggle("tf-show", n > 0);
    }
    if (els.list) renderList();
    if (els.foot) renderFoot();
  }

  function open() {
    if (!els.drawer) return;
    isOpen = true;
    els.drawer.classList.add("tf-open");
    els.backdrop.classList.add("tf-open");
    document.body.style.overflow = "hidden";
  }
  function close() {
    if (!els.drawer) return;
    isOpen = false;
    els.drawer.classList.remove("tf-open");
    els.backdrop.classList.remove("tf-open");
    document.body.style.overflow = "";
  }
  function toggle() { if (isOpen) close(); else open(); }

  function flashAdded(elId) {
    var el = document.getElementById(elId);
    if (!el) { open(); return; }
    el.classList.add("tf-added-flash");
    setTimeout(function () { el.classList.remove("tf-added-flash"); }, 900);
  }

  function resync() {
    cart = readCart();
    render();
  }

  function init() {
    injectStyle();
    if (!buildUi()) return;
    render();

    /* Why this is needed: the cart icon/badge is only as fresh as the `cart` variable, which is read
       from localStorage once when this script first runs. Two real situations can leave it stale after
       that, which is what made the icon look like it "disappears when changing pages":
       1. Back/forward navigation can restore a page from the browser's bfcache (a frozen snapshot of
          the page from before you left it) instead of re-running this script fresh — so the badge shows
          whatever it was BEFORE an item was added on another page. `pageshow` fires in both the normal
          load case and the bfcache-restore case, so re-reading localStorage there covers it.
       2. If two tabs are open at once (e.g. a product page opened in a new tab from the builder), adding
          to the cart in one tab doesn't touch the other tab's in-memory `cart` variable. The `storage`
          event fires in OTHER tabs of the same site whenever localStorage changes, so listening for it
          keeps every open tab's icon in sync with each other. */
    window.addEventListener("pageshow", resync);
    window.addEventListener("storage", function (e) {
      if (!e.key || e.key === STORAGE_KEY) resync();
    });
    document.addEventListener("visibilitychange", function () {
      if (document.visibilityState === "visible") resync();
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  window.ThreadifyCart = {
    add: add, remove: remove, setQty: setQty, clear: clear,
    items: items, count: count, open: open, close: close, toggle: toggle,
    flashAdded: flashAdded
  };
})();

JS;
}
