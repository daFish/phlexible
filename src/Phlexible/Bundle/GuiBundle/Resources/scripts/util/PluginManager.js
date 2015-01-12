/**
 * Plugin manager
 */
Ext.define('Phlexible.gui.util.PluginManager', {
    plugins: {},

    addObject: function(plugin, key, value) {
        if (!this.plugins[plugin]) {
            this.plugins[plugin] = {};
        }
        this.plugins[plugin][key] = value;
    },

    append: function(plugin, value) {
        if (!this.plugins[plugin]) {
            this.plugins[plugin] = [];
        }
        this.plugins[plugin].push(value);
    },

    prepend: function(plugin, value) {
        if (!this.plugins[plugin]) {
            this.plugins[plugin] = [];
        }
        this.plugins[plugin].unshift(value);
    },

    get: function(plugin) {
        return this.plugins[plugin];
    }
});
