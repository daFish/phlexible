/**
 * @param {String} path
 * @returns {String}
 */
Phlexible.url = function (path) {
    return Phlexible.baseUrl + path;
};

/**
 * @param {String} path
 * @returns {String}
 */
Phlexible.path = function (path) {
    return Phlexible.basePath + path;
};

/**
 * @param {String} path
 * @returns {String}
 */
Phlexible.bundleAsset = function (path) {
    return Phlexible.bundlePath + path;
};

/**
 * Clone object
 *
 * @param {*} obj
 * @returns {*}
 * @deprecated Use Ext.clone()
 */
Phlexible.clone = Ext.clone;

/**
 * Evaluate string to reference
 *
 * @param {String} s
 * @returns {*}
 */
Phlexible.evalClassString = function (s) {
    var a = s.split('.');
    var n = window;
    for (var i = 0; i < a.length; i++) {
        if (!n[a[i]]) return false;
        n = n[a[i]];
    }

    return n;
};

/**
 * Create inline icon
 *
 * @param {String} iconCls
 * @param {Object} attr
 * @returns {string}
 */
Phlexible.inlineIcon = function (iconCls, attr) {
    if (!attr) attr = {};

    attr = Ext.applyIf(attr, {
        src: Ext.BLANK_IMAGE_URL,
        width: 16,
        height: 16,
        'class': 'p-inline-icon ' + iconCls
    });

    var s = '<img';
    for (var i in attr) {
        if (!attr.hasOwnProperty(i)) {
            continue;
        }
        s += ' ' + i + '="' + attr[i] + '"';
    }

    s += ' />';

    return s;
};
