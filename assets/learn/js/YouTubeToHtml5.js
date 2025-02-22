'use strict';

function _typeof(a) {
    "@babel/helpers - typeof";
    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(a) {
        return typeof a
    } : function(a) {
        return a && "function" == typeof Symbol && a.constructor === Symbol && a !== Symbol.prototype ? "symbol" : typeof a
    }, _typeof(a)
}

function YouTubeToHtml5() {
    var a = 0 < arguments.length && void 0 !== arguments[0] ? arguments[0] : {};
    for (var b in this.hooks = {}, this.options = {}, this.defaultOptions) this.options[b] = b in a ? a[b] : this.defaultOptions[b];
    this.options.autoload && this.load()
}
YouTubeToHtml5.prototype.defaultOptions = {
    selector: "video[data-yt2html5]",
    attribute: "data-yt2html5",
    formats: "*",
    autoload: !0,
    withAudio: !1
}, YouTubeToHtml5.prototype.globalHooks = {}, YouTubeToHtml5.prototype.getHooks = function(a, b) {
    var c = [];
    if (a in this.globalHooks) {
        var d = this.globalHooks[a];
        d = d.filter(function(a) {
            return a.name === b
        }), d = d.sort(function(c, a) {
            return c.priority - a.priority
        }), c = c.concat(d)
    }
    if (a in this.hooks) {
        var e = this.hooks[a];
        e = e.filter(function(a) {
            return a.name === b
        }), e = e.sort(function(c, a) {
            return c.priority - a.priority
        }), c = c.concat(e)
    }
    return c
}, YouTubeToHtml5.prototype.addHook = function(a, b) {
    a in this.globalHooks || (this.globalHooks[a] = []), a in this.hooks || (this.hooks[a] = []), "global" in b && b.global ? this.globalHooks[a].push(b) : this.hooks[a].push(b)
}, YouTubeToHtml5.prototype.addAction = function(a, b) {
    var c = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : 10,
        d = !!(3 < arguments.length && arguments[3] !== void 0) && arguments[3];
    this.addHook("actions", {
        name: a,
        callback: b,
        priority: c,
        global: d
    })
}, YouTubeToHtml5.prototype.doAction = function(a) {
    for (var b = this.getHooks("actions", a), c = arguments.length, d = Array(1 < c ? c - 1 : 0), e = 1; e < c; e++) d[e - 1] = arguments[e];
    for (var f = 0; f < b.length; f++) {
        var g;
        (g = b[f]).callback.apply(g, d)
    }
}, YouTubeToHtml5.prototype.addFilter = function(a, b) {
    var c = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : 10,
        d = !!(3 < arguments.length && arguments[3] !== void 0) && arguments[3];
    this.addHook("filters", {
        name: a,
        callback: b,
        priority: c,
        global: d
    })
}, YouTubeToHtml5.prototype.applyFilters = function(a, b) {
    for (var c = this.getHooks("filters", a), d = arguments.length, e = Array(2 < d ? d - 2 : 0), f = 2; f < d; f++) e[f - 2] = arguments[f];
    for (var g = 0; g < c.length; g++) {
        var h;
        b = (h = c[g]).callback.apply(h, [b].concat(e))
    }
    return b
}, YouTubeToHtml5.prototype.itagMap = {
    18: "360p",
    22: "720p",
    37: "1080p",
    38: "3072p",
    82: "360p3d",
    83: "480p3d",
    84: "720p3d",
    85: "1080p3d",
    133: "240pna",
    134: "360pna",
    135: "480pna",
    136: "720pna",
    137: "1080pna",
    264: "1440pna",
    298: "720p60",
    299: "1080p60na",
    160: "144pna",
    139: "48kbps",
    140: "128kbps",
    141: "256kbps"
}, YouTubeToHtml5.prototype.urlToId = function(a) {
    var b = a.match(/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|(?:(?:youtube-nocookie\.com\/|youtube\.com\/)(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)))([a-zA-Z0-9\-_]*)/);
    return Array.isArray(b) && b[1] ? b[1] : a
}, YouTubeToHtml5.prototype.fetch = function(a) {
    return new Promise(function(b, c) {
        var d = new XMLHttpRequest;
        d.open("GET", a, !0), d.onreadystatechange = function() {
            4 === this.readyState && (200 <= this.status && 400 > this.status ? b(this.responseText) : c(this))
        }, d.send(), d = null
    })
}, YouTubeToHtml5.prototype.getAllowedFormats = function() {
    var a = [];
    return Array.isArray(this.options.formats) ? a = this.options.formats : this.itagMap[this.options.formats] ? a = [this.options.formats] : "*" === this.options.formats && (a = Object.values(this.itagMap).sort()), a
}, YouTubeToHtml5.prototype.getElements = function(a) {
    var b = null;
    return a && (NodeList.prototype.isPrototypeOf(a) || HTMLCollection.prototype.isPrototypeOf(a) ? b = a : "object" === _typeof(a) && "nodeType" in a && a.nodeType ? b = [a] : b = document.querySelectorAll(this.options.selector)), b = Array.from(b || ""), this.applyFilters("elements", b)
}, YouTubeToHtml5.prototype.youtubeDataApiEndpoint = function(a) {
    var b = "https://yt2html5.com/?id=".concat(a);
    return this.applyFilters("api.endpoint", b, a, null)
}, YouTubeToHtml5.prototype.parseUriString = function(a) {
    return a.split("&").reduce(function(a, b) {
        var c = b.split("=").map(function(a) {
            return decodeURIComponent(a.replace("+", " "))
        });
        return a[c[0]] = c[1], a
    }, {})
}, YouTubeToHtml5.prototype.canPlayType = function(a) {
    var b = null;
    b = /^audio/i.test(a) ? document.createElement("audio") : document.createElement("video");
    var c = b && "function" == typeof b.canPlayType ? b.canPlayType(a) : "unknown";
    return c ? c : "no"
}, YouTubeToHtml5.prototype.parseYoutubeMeta = function(a) {
    var b = this,
        c = [],
        d = [];
    if ("string" == typeof a) try {
        a = JSON.parse(a)
    } catch (a) {
        return null
    }
    var e = a.data || {};
    return e = this.applyFilters("api.response", e, a), e.hasOwnProperty("url_encoded_fmt_stream_map") && (c = c.concat(e.url_encoded_fmt_stream_map.split(",").map(function(a) {
        return b.parseUriString(a)
    }))), e.player_response.streamingData && e.player_response.streamingData.formats && (c = c.concat(e.player_response.streamingData.formats)), e.hasOwnProperty("adaptive_fmts") && (c = c.concat(e.adaptive_fmts.split(",").map(function(a) {
        return b.parseUriString(a)
    }))), e.player_response.streamingData && e.player_response.streamingData.adaptiveFormats && (c = c.concat(e.player_response.streamingData.adaptiveFormats)), c.forEach(function(a) {
        if (a && "itag" in a && b.itagMap[a.itag]) {
            var c = {
                _raw: a,
                itag: a.itag,
                url: null,
                label: null,
                type: "unknown",
                mime: "unknown",
                hasAudio: !1,
                browserSupport: "unknown"
            };
            if ("url" in a && a.url ? c.url = a.url : "signatureCipher" in a, "audioQuality" in a && a.audioQuality && (c.hasAudio = !0), c.label = "qualityLabel" in a && a.qualityLabel ? a.qualityLabel : b.itagMap[a.itag], "mimeType" in a) {
                var e = a.mimeType.match(/^(audio|video)(?:\/([^;]+);)?/i);
                e[1] && (c.type = e[1]), e[2] && (c.mime = e[2]), c.browserSupport = b.canPlayType("".concat(c.type, "/").concat(c.mime))
            }
            c.url && d.push(c)
        }
    }), d = this.applyFilters("api.results", d, e), d
}, YouTubeToHtml5.prototype.load = function() {
    var a = this,
        b = this.getElements(this.options.selector);
    b && b.length && b.forEach(function(b) {
        a.loadSingle(b)
    })
}, YouTubeToHtml5.prototype.loadSingle = function(a) {
    var b = this,
        c = 1 < arguments.length && arguments[1] !== void 0 ? arguments[1] : null,
        d = c || this.options.attribute;
    if (a.getAttribute(d)) {
        var e = this.urlToId(a.getAttribute(d)),
            f = this.youtubeDataApiEndpoint(e);
        this.doAction("api.before", a), this.fetch(f).then(function(c) {
            if (c) {
                var d = b.parseYoutubeMeta(c);
                if (d && Array.isArray(d)) {
                    d = d.filter(function(b) {
                        return b.type === a.tagName.toLowerCase()
                    }), d.sort(function(c, a) {
                        var b = {
                            unknown: -1,
                            no: -1,
                            maybe: 0,
                            probably: 1
                        };
                        return b[c.browserSupport] + b[a.browserSupport]
                    }), b.options.withAudio && (d = d.filter(function(a) {
                        return a.hasAudio
                    }));
                    for (var f, g = b.getAllowedFormats(), h = null, j = null, k = function(a) {
                            var c = g[a],
                                e = d.filter(function(a) {
                                    return b.itagMap[a.itag] === c
                                });
                            if (e && e.length) return h = e.shift(), j = c, "break"
                        }, l = 0; l < g.length && (f = k(l), "break" !== f); l++);
                    h = b.applyFilters("video.stream", h, a, j, d);
                    var m = {
                        src: "",
                        type: ""
                    };
                    h && "url" in h && h.url && (m.src = h.url), h.type && "unknown" !== h.type && h.mime && "unknown" !== h.mime && (m.type = "".concat(h.type, "/").concat(h.mime)), m.src = b.applyFilters("video.source", m.src, h, a, j, d), m.src && "function" == typeof m.src.toString && m.src.toString().length ? (a.src = m.src, m.type && m.type.length && (a.type = m.type)) : console.warn("YouTubeToHtml5 unable to load video for ID: ".concat(e))
                }
            }
        })["finally"](function(c) {
            b.doAction("api.after", a, c)
        })
    }
}, "object" === ("undefined" == typeof module ? "undefined" : _typeof(module)) && "object" === _typeof(module.exports) && (module.exports = YouTubeToHtml5);