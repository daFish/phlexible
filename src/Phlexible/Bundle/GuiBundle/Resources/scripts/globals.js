Ext.Ajax.setDefaultHeaders({apikey: Phlexible.apikey});

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

Phlexible.Storage = Ext.create('Phlexible.gui.util.Storage');

Phlexible.Icon = Ext.create('Phlexible.gui.util.Icon');

Phlexible.Notify = Ext.create('Phlexible.gui.util.Notify');

Phlexible.Format = Ext.create('Phlexible.gui.util.Format');

Phlexible.Storage.append('portlet', {
    id: 'load-portlet',
    image: '/bundles/phlexibledashboard/images/portlet-plain.png',
    xtype: 'gui-load-portlet',
    iconCls: 'system-monitor'
});

