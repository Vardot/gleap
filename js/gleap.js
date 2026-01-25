(function (Drupal, drupalSettings) {
  Drupal.behaviors.gleap = {
    attach(context, settings) {
      if (
        typeof drupalSettings.gleap === "undefined" ||
        !drupalSettings.gleap.apiKey
      ) {
        return;
      }

      // Only initialize once.
      if (window.Gleap && window.Gleap.invoked) {
        return;
      }

      const Gleap = (window.Gleap = window.Gleap || []);
      if (!Gleap.invoked) {
        window.GleapActions = [];
        Gleap.invoked = true;
        Gleap.methods = [
          "identify",
          "clearIdentity",
          "attachCustomData",
          "setCustomData",
          "removeCustomData",
          "clearCustomData",
          "registerCustomAction",
          "logEvent",
          "sendSilentCrashReport",
          "startFeedbackFlow",
          "setAppBuildNumber",
          "setAppVersionCode",
          "preFillForm",
          "setApiUrl",
          "setFrameUrl",
          "isOpened",
          "open",
          "close",
          "on",
          "setLanguage",
          "setOfflineMode",
          "initialize",
        ];

        Gleap.f = function (e) {
          return function () {
            const t = Array.prototype.slice.call(arguments);
            window.GleapActions.push({ e, a: t });
          };
        };

        for (let t = 0; t < Gleap.methods.length; t++) {
          const i = Gleap.methods[t];
          Gleap[i] = Gleap.f(i);
        }

        Gleap.load = function () {
          const t = document.getElementsByTagName("head")[0];
          const i = document.createElement("script");
          i.type = "text/javascript";
          i.async = true;
          i.src = "https://js.gleap.io/latest/index.js";
          t.appendChild(i);
        };

        Gleap.load();
        Gleap.initialize(drupalSettings.gleap.apiKey);
      }
    },
  };
})(Drupal, drupalSettings);
