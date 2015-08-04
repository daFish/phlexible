Ext.provide('Phlexible.elementtypes.Format');

Phlexible.elementtypes.Format = {
    title: function (name, meta, record) {
        return '<img src="' + Phlexible.bundleAsset('/phlexibletree/node-icons/' + record.get('icon')) + '" width="18" height="18" border="0" alt="' + name + '" /> ' + name;
    },

    status: function (status, meta, record) {
        return '';
    }
};
