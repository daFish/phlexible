Ext.namespace('Phlexible.fields');

Phlexible.fields.Registry = Ext.create('Phlexible.elementtype.util.Registry');
Phlexible.fields.Prototypes = Ext.create('Phlexible.elementtype.util.Prototypes');
Phlexible.fields.FieldTypes = Ext.create('Phlexible.elementtype.util.Types');
Phlexible.fields.FieldHelper = Ext.create('Phlexible.elementtype.util.Helper');

Phlexible.elementtype.Format = {
    title: function (name, md, elementtype) {
        return '<img src="' + Phlexible.bundleAsset('/phlexibleelementtype/elementtypes/' + elementtype.get('icon')) + '" width="18" height="18" border="0" alt="' + name + '" style="vertical-align: middle;" /> ' + name;
    },

    status: function (status, meta, record) {
        return '';
    }
};

Phlexible.elementtype.FieldMap = {
    field: {
        type: '',
        working_title: '',
        comment: '',
        image: ''
    },
    validation: {},
    configuration: {},
    labels: {
        fieldLabel: {
            de: '',
            en: ''
        },
        boxLabel: {
            de: '',
            en: ''
        },
        prefix: {
            de: '',
            en: ''
        },
        suffix: {
            de: '',
            en: ''
        },
        contextHelp: {
            de: '',
            en: ''
        }
    },
    options: {
    }
};

Ext.define('Phlexible.element.ElementDataTabHelper', {});
Ext.define('Phlexible.element.Element', {});
Ext.define('Phlexible.element.ElementContentPanel', {extend: 'Ext.panel.Panel', xtype: 'elements-elementcontentpanel'});
