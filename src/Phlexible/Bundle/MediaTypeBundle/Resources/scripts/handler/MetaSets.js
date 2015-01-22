Ext.define('Phlexible.mediatype.handler.MediaTypes', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.mediatype.view.MainPanel'],

    text: '_mediatypes',
    iconCls: Phlexible.Icon.get('image-share'),
    name: 'mediatype-main'
});