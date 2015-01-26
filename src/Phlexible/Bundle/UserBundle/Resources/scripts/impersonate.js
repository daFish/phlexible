Ext.onReady(function() {
    if (!Phlexible.App.getUser().isImpersonated()) {
        return;
    }

    var btn = Phlexible.App.getTray().add('impersonate', Ext.create('Ext.button.Button', {
        cls: 'x-btn-icon',
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
                    html: 'Switch back to "' + Phlexible.App.getUser().getImpersonated().username + '"',
                    anchor: 'bottom',
                    defaultAlign: 'tr-br?'
                });
            }
        }
    }));
});
