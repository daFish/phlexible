Ext.define('Phlexible.gui.handler.Account', {
    extend: 'Phlexible.gui.menuhandle.handle.Menu',

    iconCls: Phlexible.Icon.get('card-address'),
    getText: function () {
        return Phlexible.App.getUser().getDisplayName();
    }
})