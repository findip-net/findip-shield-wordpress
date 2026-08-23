(function () {
  'use strict';

  var settings = window.findipShieldSettings || {};
  var shield = window.FindIP;
  var sentPostConsentPageEvents = false;

  if (!shield || !settings.siteKey) {
    return;
  }

  if (settings.consentRequired) {
    shield.setConsent(false);
  }

  shield.init({
    siteKey: settings.siteKey,
    privacyMode: settings.privacyMode || 'strict',
    autoTrack: settings.autoTrack !== false,
    autoDetectForms: settings.autoDetectForms !== false,
    consentRequired: settings.consentRequired === true,
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

  if (!settings.woocommerce) {
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
