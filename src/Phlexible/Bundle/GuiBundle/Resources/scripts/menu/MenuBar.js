/**
 * Menu bar
 */
Ext.define('Phlexible.gui.menu.MenuBar', {
    extend: 'Ext.Toolbar',
    alias: 'widget.menu-menubar',

    menuRendered: false,
    trayRendered: false,

    initComponent: function() {
        if (!this.menu) {
            throw Exception('menu missing');
        }
        if (!this.tray) {
            throw Exception('tray missing');
        }

        this.menu.on({
            populate: this.populateMenu,
            scope: this
        });

        this.tray.on({
            addItem: this.addTrayItem,
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

        this.populateMenu(this.menu.getItems());
    },

    getTray: function() {
        return this.tray;
    },

    getMenu: function() {
        return this.menu;
    },

    reloadMenu: function() {

    },

    clearMenu: function() {

    },

    addTrayItem: function(config) {
        if (!this.trayRendered) {
            return;
        }

        if (!config.itemId) {
            throw new Error('config.itemId missing');
        }

        Phlexible.Logger.debug('Menu.addTrayItem()', config);

        this.getComponent('tray').add(config);
        this.getComponent('tray').show();

        config.item = this.getComponent('tray').getComponent(config.itemId);
    },

    populateTray: function() {
        if (this.trayRendered) {
            return;
        }

        //Phlexible.console.info('menuBar.populateTray()');

        this.getComponent('tray').add(this.tray.items);
        /*
        Ext.each(this.tray.items, function(item) {
            this.getComponent('tray').add(item);
        }, this);
        */

        this.trayRendered = true;

        this.getComponent('tray').show();
    },

    populateMenu: function(menu) {
        this.clearMenu();

        //Phlexible.console.info('menuBar.populateMenu()');

        var lastItemWasSeperator = false,
            menuGrp = [],
            toolsGrp = [],
            items = this.menu.getItems(),
            i = 0;

        Ext.each(items, function(item) {
            this.insert(i, item);
            i += 1;
        }, this);

        this.populateTray();
    }
});
