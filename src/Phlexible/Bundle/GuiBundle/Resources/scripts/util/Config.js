/**
 * Icon helper
 */
Ext.define('Phlexible.gui.util.Config', {
    extend: 'Ext.util.Observable',

    /**
     * @cfg {Object} values Config values
     */
    values: {},

    /**
     * Fired when a value is set
     *
     * @event set
     * @param {String} key
     * @param {String} value
     * @param {Phlexible.gui.util.Config} config
     */

    /**
     * Fired when config is initialized
     *
     * @event init
     * @param {Phlexible.gui.util.Config} config
     */

    /**
     * @constructor
     * @param {Object} values
     */
    constructor: function(values) {
        this.callParent();

        this.values = values;
        this.fireEvent('init', this);
    },

    /**
     * Return CSS rule for the given icon
     *
     * @param {String} key
     * @param {String} defaultValue
     * @return {String}
     */
    get: function(key, defaultValue) {
        if (this.has(key)) {
            return this.values[key];
        }

        if (defaultValue) {
            return defaultValue;
        }

        return null;
    },

    /**
     * Set value
     *
     * @param {String} key
     * @param {String} value
     * @return {Phlexible.gui.util.Config}
     */
    set: function(key, value) {
        this.values[key] = value;

        this.fireEvent('set', key, value, this);
    },

    /**
     * Is a rule for the given icon defined?
     *
     * @param {String} key
     * @return {Boolean}
     */
    has: function(key) {
        return !!this.values[key];
    },

    /**
     * Unset value
     *
     * @param {String} key
     */
    unset: function(key) {
        if (this.has(key)) {
            delete (this.values[key]);
        }
    }
});

