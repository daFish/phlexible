Ext.define('Phlexible.mediatype.view.MainController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.mediatype.main',

    onChangeMediaType: function(mediaType) {
        var mimetypes = null;
        if (mediaType) {
            mimetypes = mediaType.get('mimetypes');
        }
        this.getView().getComponent('east').getComponent('mimetypes').loadMimetypes(mimetypes);
        this.loadMimetypes(mimetypes);
    },

    loadParams: function () {

    },

    updateIcons: function(mediatype) {
        var iconPanel = this.getView().getComponent('east').getComponent('icons');

        if (!mediatype) {
            iconPanel.body.update('');
            return;
        }

        var icons = [], desc = [], key = mediatype.get('key');

        if (mediatype.get('icon16')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes16/' + key + '.gif') + '" width="16" height="16" />' +
                '</td>'
            );
            desc.push('<td align="center">16x16</td>');
        }
        if (mediatype.get('icon32')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes32/' + key + '.gif') + '" width="32" height="32" />' +
                '</td>'
            );
            desc.push('<td align="center">32x32</td>');
        }
        if (mediatype.get('icon48')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes48/' + key + '.gif') + '" width="48" height="48" />' +
                '</td>'
            );
            desc.push('<td align="center">48x48</td>');
        }
        if (mediatype.get('icon256')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes256/' + key + '.gif') + '" width="256" height="256" />' +
                '</td>'
            );
            desc.push('<td align="center">256x256</td>');
        }

        if (!icons.length) {
            iconPanel.body.update('');
            return;
        }

        iconPanel.body.update('<table><tr>' + icons.join('') + '</tr><tr>' + desc.join('') + '</tr></table>');
    }
});
