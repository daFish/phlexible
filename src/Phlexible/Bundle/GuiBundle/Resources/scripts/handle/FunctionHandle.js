Ext.define('Phlexible.gui.menuhandle.handle.FunctionHandle', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    handle: function () {
        var component = this.getComponent();

        component();
    }
});