Phlexible.console = Ext.create('Phlexible.gui.util.Console');

Phlexible.Logger = Ext.create('Phlexible.gui.util.Logger');

Phlexible.Handles = Ext.create('Phlexible.gui.util.Handles');

Phlexible.globalKeyMap = new Ext.KeyMap(document);
Phlexible.globalKeyMap.accessKey = function (key, handler, scope) {
    var h = function (keyCode, e) {
        if (Ext.isIE) {
            // IE6 doesn't allow cancellation of the F5 key,
            // so trick it into thinking some other key was pressed (backspace in this case)
            e.browserEvent.keyCode = 8;
        }
        e.preventDefault();
        handler.call(scope || this, keyCode, e);
        e.stopEvent();
        return false;
    };
    this.on(key, h, scope);
};

Phlexible.globalKeyMap.accessKey({key: 'y', alt: true}, function () {
    Phlexible.gui.Actions.show();
});

Phlexible.PluginManager = Ext.create('Phlexible.gui.util.PluginManager');

Phlexible.Router = Ext.create('Phlexible.gui.util.Router');

Phlexible.Icon = Ext.create('Phlexible.gui.util.Icon');

Phlexible.Notify = Ext.create('Phlexible.gui.util.Notify');

Phlexible.Format = Ext.create('Phlexible.gui.util.Format');

Ext.onReady(function () {
    Phlexible.App = Ext.create('Phlexible.gui.util.Application', {
        config: Phlexible.config,
        menu: Phlexible.menu
    });
    Phlexible.App.init();
});
