Ext.define('Phlexible.gui.util.Handles', {
    constructor: function() {
        this.handles = {};
    },

    /**
     *
     * @param {String} key
     * @returns {String|Function}
     */
    get: function(key) {
        return this.handles[key];
    },

    /**
     *
     * @param {String} key
     * @returns {Boolean}
     */
    has: function(key) {
        return this.handles[key] !== undefined;
    },
    /**
     *
     * @param {String} key
     * @param {String|Function} name
     */
    add: function(key, name) {
        this.handles[key] = name;
    }
});

