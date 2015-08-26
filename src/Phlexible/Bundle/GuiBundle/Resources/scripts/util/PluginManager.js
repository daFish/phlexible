/**
 * Plugin manager
 */
Ext.define('Phlexible.gui.util.PluginManager', {
    constructor: function() {
        this.sections = {};
    },

    /**
     * @deprecated
     */
    addObject: function(section, key, value) {
        this.set(section, key, value);
    },

    set: function(section, key, value) {
        if (!this.sections[section]) {
            this.sections[section] = {};
        }
        this.sections[section][key] = value;

        Phlexible.Logger.notice("PluginManager " + section + "/" + key + ": " + typeof value);
    },

    append: function(section, value) {
        if (!this.sections[section]) {
            this.sections[section] = [];
        }
        this.sections[section].push(value);
    },

    prepend: function(section, value) {
        if (!this.sections[section]) {
            this.sections[section] = [];
        }
        this.sections[section].unshift(value);
    },

    get: function(section) {
        return this.sections[section];
    },

    contains: function(section, key) {
        return this.sections[section] !== undefined && this.sections[section][key] !== undefined;
    },

    remove: function(section, value) {
        if (!this.sections[section]) {
            return;
        }

        var index = Ext.Array.indexOf(this.sections[section], value);

        if (index >= 0) {
            this.sections[section].splice(index, 1);
        }
    },

    each: function(section, callback) {
        Ext.each(this.sections[section], callback);
    }
});
