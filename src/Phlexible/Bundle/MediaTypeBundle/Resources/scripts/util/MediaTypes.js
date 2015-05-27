Ext.provide('Phlexible.mediatype.util.MediaTypes');

Phlexible.mediatype.util.MediaTypes = Ext.extend(Ext.util.Observable, {
    mediatypes: {},

    has: function(mediaType) {
        return this.mediatypes[mediaType] !== undefined;
    },

    get: function(mediaType) {
        return this.mediatypes[mediaType];
    },

    getClass: function(mediaType) {
        if (this.has(mediaType)) {
            return this.get(mediaType).cls;
        }
        return this.get("_unknown").cls;
    },

    getText: function(mediaType) {
        var language = Phlexible.Config.get("language.backend", "en");
        if (this.has(mediaType)) {
            return this.get(mediaType)[language];
        }
        return this.get("_unknown")[language];
    },

    setData: function(mediatypes) {
        this.mediatypes = mediatypes;
    }
});