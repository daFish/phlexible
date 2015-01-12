/**
 * PHP info window
 */
Ext.define('Phlexible.gui.PhpInfoWindow', {
    extend: 'Ext.window.Window',
    requires: ['Ext.window.Window', 'Phlexible.gui.IframePanel'],

    title: '_php_info',
    iconCls: 'p-gui-php-icon',
    width: 940,
    height: 600,
    constrain: true,
    modal: true,
    maximizable: true,
    layout: 'border',

    modulesText: '_modules',

    initComponent: function() {
        this.items = [{
            xtype: 'buttongroup',
            columns: 1,
            region: 'west',
            title: this.modulesText,
            plain: true,
            autoScroll: true,
            collapsible: true,
            width: 140,
            margin: 5,
            padding: 5,
            items: []
        },{
            xtype: 'gui-iframepanel',
            region: 'center',
            src: Phlexible.Router.generate('gui_status_php'),
            enableReload: true,
            enableWindow: true,
            listeners: {
                load: function(iframe) {
                    var doc = iframe.getDoc(),
                        h2a = doc.getElementsByTagName('h2');

                    Ext.each(h2a, function(h2) {
                        var a, anchor, name;
                        if (!h2.childNodes || h2.childNodes[0].tagName !== 'A') {
                            return;
                        }
                        a = h2.childNodes[0];
                        if (!a.attributes || a.attributes[0].name !== 'name') {
                            return;
                        }
                        anchor = a.attributes[0].textContent;
                        if (!a.childNodes || a.childNodes[0].nodeType !== 3) {
                            return;
                        }
                        name = a.childNodes[0].textContent;

                        this.getComponent(0).add({
                            xtype: 'button',
                            text: name,
                            width: 125,
                            handler: function() {
                                this.getComponent(1).getFrame().src = this.getComponent(1).src + '#' + anchor;
                            },
                            scope: this
                        });
                    }, this);
                },
                scope: this
            }
        }];

        this.callParent(arguments);
    }
});
