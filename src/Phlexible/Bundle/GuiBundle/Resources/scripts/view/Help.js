Ext.define('Phlexible.gui.Help', {
    extend: 'Phlexible.gui.IframePanel',
    alias: 'widget.gui-help',

    title: Phlexible.gui.Strings.help,
    iconCls: Phlexible.Icon.get('book-question'),
    closable: false,

    defaultSrc: 'http://www.test.de',
    src: 'http://www.test.de',
    enableHome: true,
    enableWindow: true
});
