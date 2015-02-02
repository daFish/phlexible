Ext.define('Phlexible.mediatype.view.MainController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.mediatype.main',

    onChangeMediaType: function(mediaType) {
        var mimetypes = null;
        if (mediaType) {
            mimetypes = mediaType.get('mimetypes');
        }
        this.getView().getComponent('mimetypes').loadMimetypes(mimetypes);
    },

    loadParams: function () {

    }
});
