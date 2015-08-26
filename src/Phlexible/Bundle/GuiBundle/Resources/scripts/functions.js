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
