Ext.define('Phlexible.elementtypes.window.NewElementtypeWindow', {
    extend: 'Phlexible.gui.util.Dialog',

    width: 400,
    height: 230,
    minWidth: 400,
    minHeight: 230,
    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),

    textHeader: '_textHeader',
    textDescription: '_textDescription',
    textOk: '_textOk',
    textCancel: '_textCancel',

    extraCls: 'p-elementtypes-newelementtype',
    iconClsOk: 'p-elementtype-elementtype_save-icon',

    getSubmitUrl: function () {
        return Phlexible.Router.generate('elementtypes_list_create');
    },

    type: Phlexible.elementtype.TYPE_FULL,


    titleText: '_titleText',
    typeText: '_typeText',
    fullText: '_fullText',
    structureText: '_structureText',
    layoutText: '_layoutText',
    partText: '_partText',

    getFormItems: function () {
        if (this.type == Phlexible.elementtype.TYPE_REFERENCE) {
            this.type = Phlexible.elementtype.TYPE_FULL;
        }

        return [
            {
                anchor: '-70',
                fieldLabel: this.titleText,
                name: 'title',
                msgTarget: 'under'
            },
            {
                xtype: 'iconcombo',
                fieldLabel: this.typeText,
                hiddenName: 'type',
                anchor: '-70',
                //                width: 183,
                //                listWidth: 200,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'value', 'icon'],
                    data: [
                        [Phlexible.elementtype.TYPE_FULL, this.fullText, 'p-elementtype-type_full-icon'],
                        [Phlexible.elementtype.TYPE_STRUCTURE, this.structureText, 'p-elementtype-type_structure-icon'],
                        [Phlexible.elementtype.TYPE_LAYOUTAREA, this.layoutText, 'p-elementtype-type_layoutarea-icon'],
                        [Phlexible.elementtype.TYPE_PART, this.partText, 'p-elementtype-type_part-icon']
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
                allowEmpty: false
            }
        ];
    }

});
