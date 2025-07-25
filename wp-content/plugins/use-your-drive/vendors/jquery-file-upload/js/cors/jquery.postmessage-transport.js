/*
 * jQuery postMessage Transport Plugin
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * © Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global define, require */

(function (factory) {
  'use strict';
  if (typeof define === 'function' && define.amd) {
    // Register as an anonymous AMD module:
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    // Node/CommonJS:
    factory(require('jquery'));
  } else {
    // Browser globals:
    factory(window.jQuery);
  }
})(function ($) {
  'use strict';

  var counter = 0,
    names = [
      'accepts',
      'cache',
      'contents',
      'contentType',
      'crossDomain',
      'data',
      'dataType',
      'headers',
      'ifModified',
      'mimeType',
      'password',
      'processData',
      'timeout',
      'traditional',
      'type',
      'url',
      'username'
    ],
    convert = function (p) {
      return p;
    };

  $.ajaxSetup({
    converters: {
      'postmessage text': convert,
      'postmessage json': convert,
      'postmessage html': convert
    }
  });

  $.ajaxTransport('postmessage', function (options) {
    if (options.postMessage && window.postMessage) {
      var iframe,
        loc = $('<a></a>').prop('href', options.postMessage)[0],
        target = loc.protocol + '//' + loc.host,
        xhrUpload = options.xhr().upload;
      // IE always includes the port for the host property of a link
      // element, but not in the location.host or origin property for the
      // default http port 80 and https port 443, so we strip it:
      if (/^(http:\/\/.+:80)|(https:\/\/.+:443)$/.test(target)) {
        target = target.replace(/:(80|443)$/, '');
      }
      return {
        send: function (_, completeCallback) {
          counter += 1;
          var message = {
              id: 'postmessage-transport-' + counter
            },
            eventName = 'message.' + message.id;
          iframe = $(
            '<iframe style="display:none;" src="' +
              options.postMessage +
              '" name="' +
              message.id +
              '"></iframe>'
          )
            .on('load', function () {
              $.each(names, function (i, name) {
                message[name] = options[name];
              });
              message.dataType = message.dataType.replace('postmessage ', '');
              $(window).on(eventName, function (event) {
                var e = event.originalEvent;
                var data = e.data;
                var ev;
                if (e.origin === target && data.id === message.id) {
                  if (data.type === 'progress') {
                    ev = document.createEvent('Event');
                    ev.initEvent(data.type, false, true);
                    $.extend(ev, data);
                    xhrUpload.dispatchEvent(ev);
                  } else {
                    completeCallback(
                      data.status,
                      data.statusText,
                      { postmessage: data.result },
                      data.headers
                    );
                    iframe.remove();
                    $(window).off(eventName);
                  }
                }
              });
              iframe[0].contentWindow.postMessage(message, target);
            })
            .appendTo(document.body);
        },
        abort: function () {
          if (iframe) {
            iframe.remove();
          }
        }
      };
    }
  });
});
