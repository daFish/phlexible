Ext.define('Phlexible.elementtypes.window.NewElementtypeWindow', {
    extend: 'Phlexible.gui.util.Dialog',

    width: 400,
    height: 230,
    minWidth: 400,
    minHeight: 230,
    iconCls: 'p-elementtype-elementtype_add-icon',

    textHeader: '_textHeader',
    textDescription: '_textDescription',
    textOk: '_textOk',
    textCancel: '_textCancel',

    extraCls: 'p-elementtypes-newelementtype',
    iconClsOk: 'p-elementtype-elementtype_save-icon',

    getSubmitUrl: function () {
        return Phlexible.Router.generate('elementtypes_list_create');
    },

    type: Phlexible.elementtypes.TYPE_FULL,

    getFormItems: function () {
        if (this.type == Phlexible.elementtypes.TYPE_REFERENCE) {
            this.type = Phlexible.elementtypes.TYPE_FULL;
        }

        return [
            {
                anchor: '-70',
                fieldLabel: Phlexible.elementtypes.Strings.title,
                name: 'title',
                msgTarget: 'under'
            },
            {
                xtype: 'iconcombo',
                fieldLabel: Phlexible.elementtypes.Strings.type,
                hiddenName: 'type',
                anchor: '-70',
                //                width: 183,
                //                listWidth: 200,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'value', 'icon'],
                    data: [
                        [Phlexible.elementtypes.TYPE_FULL, Phlexible.elementtypes.Strings.full_element, 'p-elementtype-type_full-icon'],
                        [Phlexible.elementtypes.TYPE_STRUCTURE, Phlexible.elementtypes.Strings.structure_element, 'p-elementtype-type_structure-icon'],
                        [Phlexible.elementtypes.TYPE_LAYOUTAREA, Phlexible.elementtypes.Strings.layout_element, 'p-elementtype-type_layoutarea-icon'],
                        [Phlexible.elementtypes.TYPE_PART, Phlexible.elementtypes.Strings.part_element, 'p-elementtype-type_part-icon']
                    ]
                }),
                value: this.type,
                displayField: 'value',
                valueField: 'key',
                iconClsField: 'icon',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                allowEmpty: false
            }
        ];
    }

});
