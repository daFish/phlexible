Ext.onReady(function() {
    return;
    if (!Phlexible.User.isImpersonated()) {
        return;
    }

    var btn = Phlexible.Menu.addTrayItem('impersonate', {
        iconCls: Phlexible.Icon.get('user-thief'),
        handler: function() {
            document.location.href = Phlexible.Router.generate('gui_index', {"_switch_user": "_exit"});
        },
        listeners: {
            render: function(c) {
                Ext.create('Ext.tip.ToolTip', {
                    target: c.el,
                    dismissDelay: 0,
                    autoHide: true,
                    html: 'Switch back to "' + Phlexible.User.getImpersonated().username + '"',
                    anchor: 'bottom',
                    defaultAlign: 'tr-br?'
                });
            }
        }
    });
});
