(function () {
  'use strict';

  var settings = window.findipShieldSettings || {};
  var shield = window.FindIP;
  var sentPostConsentPageEvents = false;

  // wp_localize_script casts booleans to strings ("1" / ""), so every flag
  // must be normalized before comparison.
  function flag(value, fallback) {
    if (value === true || value === '1' || value === 1) {
      return true;
    }

    if (value === false || value === '' || value === '0' || value === 0) {
      return false;
    }

    return fallback;
  }

  var consentRequired = flag(settings.consentRequired, false);
  var autoTrack = flag(settings.autoTrack, true);
  var autoDetectForms = flag(settings.autoDetectForms, true);
  var woocommerceEnabled = flag(settings.woocommerce, false);

  if (!shield || !settings.siteKey) {
    return;
  }

  if (consentRequired) {
    shield.setConsent(false);
  }

  shield.init({
    siteKey: settings.siteKey,
    privacyMode: settings.privacyMode || 'strict',
    autoTrack: autoTrack,
    autoDetectForms: autoDetectForms,
    consentRequired: consentRequired,
    noConsentMode: settings.noConsentMode || 'strict',
    integration: settings.integration || 'wordpress',
    pushToDataLayer: true
  });

  document.addEventListener('findip:consent', function (event) {
    var detail = event.detail || {};
    var granted = detail.granted === true;

    shield.setConsent(granted);

    if (granted && settings.noConsentMode === 'disabled' && !sentPostConsentPageEvents) {
      sentPostConsentPageEvents = true;
      shield.track('session_start', {
        custom: { integration: 'wordpress' }
      });
      shield.track('page_view', {
        custom: { integration: 'wordpress' }
      });
    }
  });

  if (!woocommerceEnabled) {
    return;
  }

  if (settings.pageEvent === 'order_received') {
    shield.track('custom', {
      custom: {
        integration: 'woocommerce',
        event_type: 'order_received'
      }
    });
  } else if (settings.pageEvent === 'cart_view') {
    shield.track('custom', {
      custom: {
        integration: 'woocommerce',
        event_type: 'cart_view'
      }
    });
  }

  if (window.jQuery) {
    window.jQuery(document.body).on('checkout_error', function () {
      shield.track('payment_failed', {
        custom: { integration: 'woocommerce' }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var checkoutEvents = window.wc && window.wc.blocksCheckoutEvents;

    if (!checkoutEvents) {
      return;
    }

    checkoutEvents.onCheckoutSuccess(function () {
      shield.track('custom', {
        custom: {
          integration: 'woocommerce',
          event_type: 'checkout_success'
        }
      });
      return true;
    });

    checkoutEvents.onCheckoutFail(function () {
      shield.track('payment_failed', {
        custom: { integration: 'woocommerce' }
      });
      return true;
    });
  });
})();
