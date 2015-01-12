Ext.define('Phlexible.gui.util.Dump', {
    /**
     * Return an arbitrary value as a string representation
     *
     * @param {*}     arr            Value
     * @param {Array} skipFunctions  Functions to skip
     * @param {Array} skipObjectKeys Object keys to skip
     */
    dump: function (arr, skipFunctions, skipObjectKeys) {
        if (!skipObjectKeys) {
            skipObjectKeys = [];
        }
        return Phlexible.dumpRep(arr, 0, '', skipFunctions, skipObjectKeys, false);
    },

    /**
     * Return an arbitrary value as a html representation
     *
     * @param {*}     arr            Value
     * @param {Array} skipFunctions  Functions to skip
     * @param {Array} skipObjectKeys Object keys to skip
     */
    dumpHtml: function (arr, skipFunctions, skipObjectKeys) {
        if (!skipObjectKeys) {
            skipObjectKeys = [];
        }
        return Phlexible.dumpRep(arr, 0, '', skipFunctions, skipObjectKeys, true);
    },

    /**
     * Reutrn an arbitrary value as a string representation
     * @param {*}       v              Value
     * @param {Number}  level          Level
     * @param {String}  lastPadding    Last padding
     * @param {Array}   skipFunctions  Functions to skip
     * @param {Array}   skipObjectKeys Object keys to skip
     * @param {Boolean} html           As Html?
     */
    dumpRep: function (v, level, lastPadding, skipFunctions, skipObjectKeys, html) {
        var dump = '';
        if (!level) level = 0;
        if (!lastPadding) last_padding = '';

        // The padding given at the beginning of the line.
        var levelPadding = "";
        for (var j = 1; j < level + 1; j++) {
            levelPadding += '    ';
        }

        switch (typeof(v)) {
            case 'object':
                var sub1 = '', sub2, value;
                for (var key in v) {
                    if (!v.hasOwnProperty(key)) {
                        continue;
                    }
                    if (skipObjectKeys.indexOf(key) === -1) {
                        value = v[key];
                        sub2 = Phlexible.dumpRep(value, level + 1, levelPadding, skipFunctions, skipObjectKeys, html);
                    }
                    else {
                        sub2 = '';
                        if (html) sub2 += '<span style="color: red">';
                        sub2 += '(skipped)';
                        if (html) sub2 += '</span>';
                    }

                    if (sub2 !== false) {
                        sub1 += (sub1 ? ',' : '') + "\n" + levelPadding;
                        if (html) sub1 += '<span style="color: blue;">';
                        sub1 += key + ':';
                        if (html) sub1 += '</span>';
                        sub1 += ' ' + sub2;
                    }
                }
                if (sub1) {
                    if (level) dump += '{';
                    dump += sub1;
                    if (level) dump += "\n" + lastPadding + "}";
                } else {
                    if (html) dump += '<span style="color: red">';
                    dump += '(empty)';
                    if (html) dump += '</span>';
                }
                break;

            case 'function':
                if (skipFunctions) {
                    return false;
                }
                dump = '';
                if (html) dump += '<span style="color: red;">';
                dump += '(function)';
                if (html) dump += '</span>';
                break;

            case 'boolean':
                dump = '';
                if (html) dump += '<span style="color: green;">';
                dump += (v ? 'true' : 'false');
                if (html) dump += '</span>';
                dump += ' ';
                if (html) dump += '<span style="color: red;">';
                dump += '(boolean)';
                if (html) dump += '</span>';
                break;

            case 'number':
                dump = '';
                if (html) dump += '<span style="color: green;">';
                dump += v;
                if (html) dump += '</span>';
                dump += ' ';
                if (html) dump += '<span style="color: red;">';
                dump += '(' + typeof(v) + ')';
                if (html) dump += '</span>';
                break;

            default:
                dump = '';
                if (html) dump += '<span style="color: green;">';
                dump += '"' + v + '"';
                if (html) dump += '</span>';
                dump += ' ';
                if (html) dump += '<span style="color: red;">';
                dump += '(' + typeof(v) + ')';
                if (html) dump += '</span>';
                break;
        }

        //dump += "\n";

        return dump;
    }
});
