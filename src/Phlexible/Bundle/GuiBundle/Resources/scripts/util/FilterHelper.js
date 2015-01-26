/**
 * Notification helper
 */
Ext.define('Phlexible.gui.util.FilterHelper', {
    constructor: function(config) {
        config = config || {};

        this.values = {};
        if (config.keys && Ext.isArray(config.keys)) {
            for (var i = 0; i < config.keys.length; i++) {
                this.values[config.keys[i]] = null;
            }
        }
    },

    /**
     * Set value
     *
     * @param {String} key
     * @param {String} value
     * @return {Phlexible.gui.util.FilterHelper}
     */
    set: function(key, value){
        this.values[key] = value;

        return this;
    },

    /**
     * Return value for given key.
     * Default value is returned when key is not set
     * null is returned when neither key exists nor a defaultValue is provided
     *
     * @param {String} key
     * @param {String} defaultValue
     * @return {String}
     */
    get: function(key, defaultValue) {
        if (this.values[key] === undefined) {
            if (defaultValue === undefined) {
                return null;
            }

            return defaultValue;
        }

        return this.values[key];
    },

    /**
     * Is value set for this key?
     *
     * @param {String} key
     * @returns {Boolean}
     */
    has: function(key) {
        return this.values[key] !== undefined;
    },

    /**
     * Return keys
     *
     * @returns {Array}
     */
    getKeys: function() {
        return Object.keys(this.values);
    },

    applyValues: function(values) {
        var key;

        for (key in this.values) {
            if (values[key]) {
                this.values[key] = values[key];
            } else {
                this.values[key] = null;
            }
        };
    },

    getSetValues: function() {
        var values = {}, key;

        for (key in this.values) {
            if (this.values[key]) {
                values[key] = this.values[key];
            }
        };

        return values;
    },

    getUnsetValues: function() {
        var values = {}, key;

        for (key in this.values) {
            if (!this.values[key]) {
                values[key] = null;
            }
        };

        return values;
    },

    getResetValues: function() {
        var values = {}, key;

        for (key in this.values) {
            values[key] = null;
        };

        return values;
    },

    /**
     * Is at least one value set?
     *
     * @return {Boolean}
     */
    hasValue: function() {
        for (var key in this.values) {
            if (this.values[key]) {
                return true;
            }
        }

        return false;
    }
});
