/**
 * Menu bar
 */
Ext.define('Phlexible.gui.menu.MenuBar', {
    extend: 'Ext.Toolbar',
    alias: 'widget.menu-menubar',

    menuRendered: false,

    initComponent: function() {
        if (!this.menu) {
            throw new Error('menu missing');
        }

        this.menu.on({
            populate: this.populateMenu,
            addTrayItem: this.addTrayItem,
            updateTrayItem: this.updateTrayItem,
            scope: this
        });

        this.items = [{
            xtype: 'buttongroup',
            itemId: 'tray',
            hidden: true,
            defaults: {
                scale: 'small'
            }
        }];

        this.callParent(arguments);

        this.populateMenu(this.menu);
    },

    /**
     * @param {Phlexible.gui.util.Menu} menu
     * @param {Object} config
     * @private
     */
    addTrayItem: function(menu, config) {
        this.getComponent('tray').add(config);
        this.getComponent('tray').show();

        config.item = this.getComponent('tray').getComponent(config.itemId);
    },

    /**
     * @param {Phlexible.gui.util.Menu} menu
     * @param {Object} config
     * @private
     */
    updateTrayItem: function(menu, config) {
        var btn = this.getTrayItem(config.itemId);
        if (config.iconCls) {
            btn.setIconCls(config.iconCls);
        }
        if (config.tooltip) {
            btn.setTooltip(config.tooltip);
        }
    },

    /**
     * @param {String} itemId
     * @return {Ext.Component}
     * @private
     */
    getTrayItem: function(itemId) {
        return this.getComponent('tray').getComponent(itemId);
    },

    /**
     * @param {String} itemId
     * @return {Boolean}
     * @private
     */
    hasTrayItem: function(itemId) {
        return this.getComponent('tray').getComponent(itemId);
    },

    /**
     * @param {Phlexible.gui.menu.Menu} menu
     * @private
     */
    populateMenu: function(menu) {
        this.items.each(function(item) {
            if (item.itemId !== 'tray') {
                item.destroy();
            }
        });

        var lastItemWasSeperator = false,
            menuGrp = [],
            toolsGrp = [],
            items = menu.getItems(),
            i = 0;

        Ext.each(items, function(item) {
            this.insert(i, item);
            i += 1;
        }, this);

        this.populateTray(menu);
    },

    /**
     * @param {Phlexible.gui.menu.Menu} menu
     * @private
     */
    populateTray: function(menu) {
        Ext.Object.each(menu.getTrayItems(), function(itemId, config) {
            if (this.hasTrayItem(itemId)) {
                this.updateTrayItem(menu, config);
            } else {
                this.addTrayItem(menu, config);
            }
        }, this);

        this.getComponent('tray').show();
    }
});
