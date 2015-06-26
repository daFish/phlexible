Ext.define('Phlexible.gui.view.Help', {
    extend: 'Phlexible.gui.panel.IFrame',
    alias: 'widget.gui-help',

    iconCls: Phlexible.Icon.get('book-question'),
    closable: false,

    defaultSrc: 'http://www.test.de',
    src: 'http://www.test.de',
    enableHome: true,
    enableWindow: true
});
