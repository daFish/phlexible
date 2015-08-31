/**
 * Object Storage
 */
Ext.define('Phlexible.gui.util.Storage', {
    constructor: function() {
        this.sections = {};
    },

    /**
     * @param {String} section
     * @param {String} key
     * @param {Mixed} value
     */
    set: function(section, key, value) {
        if (this.containsSection(section) && !Ext.isObject(this.sections[section])) {
            throw new Error("Section " + section + " is of type array.");
        }
        if (!this.containsSection(section)) {
            this.sections[section] = {};
        }
        this.sections[section][key] = value;

        Phlexible.Logger.debug("PluginManager " + section + "/" + key + ": " + typeof value);
    },

    /**
     * @param {String} section
     * @param {Mixed} value
     */
    append: function(section, value) {
        if (this.containsSection(section) && !Ext.isArray(this.sections[section])) {
            throw new Error("Section " + section + " is of type array.");
        }
        if (!this.containsSection(section)) {
            this.sections[section] = [];
        }
        this.sections[section].push(value);
    },

    /**
     * @param {String} section
     * @param {Mixed} value
     */
    prepend: function(section, value) {
        if (this.containsSection(section) && !Ext.isArray(this.sections[section])) {
            throw new Error("Section " + section + " is of type array.");
        }
        if (!this.containsSection(section)) {
            this.sections[section] = [];
        }
        this.sections[section].unshift(value);
    },

    /**
     * @param {String} section
     * @param {String} key
     * @return {Mixed}
     */
    get: function(section, key) {
        if (this.containsSection(section) && !Ext.isObject(this.sections[section])) {
            throw new Error("Section " + section + " is of type array.");
        }
        return this.sections[section][key];
    },

    /**
     * @param {String} section
     * @param {String} key
     * @return {Boolean}
     */
    contains: function(section, key) {
        return this.containsSection(section) && this.sections[section][key] !== undefined;
    },

    /**
     * @param {String} section
     * @return {Array}|{Object}
     */
    getSection: function(section) {
        if (!this.containsSection(section)) {
            return null;
        }

        return this.sections[section];
    },

    /**
     * @param {String} section
     * @return {Boolean}
     */
    containsSection: function(section) {
        return this.sections[section] !== undefined;
    },

    /**
     * @param {String} section
     * @param {String} value
     * @return {Boolean}
     */
    remove: function(section, value) {
        if (!this.containsSection(section)) {
            return;
        }

        var index = Ext.Array.indexOf(this.sections[section], value);

        if (index >= 0) {
            this.sections[section].splice(index, 1);
        }
    },

    /**
     * @param {String} section
     * @param {Function} callback
     */
    each: function(section, callback) {
        if (!this.containsSection(section)) {
            return;
        }
        if (Ext.isArray(this.sections[section])) {
            Ext.each(this.sections[section], callback);
        } else if (Ext.isObject(this.sections[section])) {
            Ext.Object.each(this.sections[section], callback);
        }
    }
});
