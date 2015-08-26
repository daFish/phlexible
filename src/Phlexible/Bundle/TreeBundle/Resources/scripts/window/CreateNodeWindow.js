Ext.provide('Phlexible.tree.window.CreateNodeWindow');

Ext.require('Phlexible.gui.util.Dialog');

Phlexible.tree.window.CreateNodeWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.elements.Strings.new_element,
    width: 550,
    minWidth: 550,
    height: 350,
    minHeight: 350,
    iconCls: 'p-element-add-icon',

    textHeader: Phlexible.elements.Strings.new_element_header,
    textDescription: Phlexible.elements.Strings.new_element_description,
    textOk: Phlexible.elements.Strings.save,
    textCancel: Phlexible.elements.Strings.cancel,

    extraCls: 'p-elements-newelement',
    iconClsOk: 'p-element-save-icon',

    getSubmitUrl: function () {
        return Phlexible.Router.generate('tree_create');
    },

    labelWidth: 100,

    getFormItems: function () {
        var items = [
            {
                xtype: 'combo',
                fieldLabel: Phlexible.elements.Strings.type,
                hiddenName: 'type',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('tree_types'),
                    baseParams: this.submitParams,
                    root: 'elementtypes',
                    fields: ['name', 'icon', 'type'],
                    autoLoad: true
                }),
                emptyText: Phlexible.elements.Strings.select_elementtype,
                displayField: 'name',
                valueField: 'name',
                listClass: 'x-combo-list-big',
                editable: false,
                tpl: '<tpl for="."><div class="x-combo-list-item"><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {name}</div></tpl>',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                allowBlank: false,
                listeners: {
                    select: function (cb, record) {
                        if (record.get('type') === 'page') {
                            this.getComponent(0).getComponent(4).enable();
                            this.getComponent(0).getComponent(5).enable();
                        } else {
                            this.getComponent(0).getComponent(4).setValue(0);
                            this.getComponent(0).getComponent(4).disable();
                            this.getComponent(0).getComponent(5).setValue(0);
                            this.getComponent(0).getComponent(5).disable();
                        }
                    },
                    scope: this
                }
            },
            {
                xtype: 'combo',
                fieldLabel: Phlexible.elements.Strings.position,
                hiddenName: 'prevId',
                hidden: this.sortMode != 'free',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('tree_nodes'),
                    baseParams: this.submitParams,
                    fields: Phlexible.tree.model.Node,
                    autoLoad: true,
                    listeners: {
                        load: function () {
                            console.warn('need first array');
                            this.getComponent(0).getComponent(2).setValue('0');
                        },
                        scope: this
                    }
                }),
                value: '0',
                valueField: 'id',
                displayField: 'title',
                listClass: 'x-combo-list-big',
                editable: false,
                tpl: '<tpl for="."><div class="x-combo-list-item"><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                allowBlank: false,
                helpText: Phlexible.elements.Strings.position_help
            },
            {
                xtype: 'iconcombo',
                fieldLabel: Phlexible.elements.Strings.sort_mode,
                hiddenName: 'sort',
                store: new Ext.data.SimpleStore({
                    fields: ['id', 'title', 'icon'],
                    data: [
                        ['free', Phlexible.elements.Strings.free, 'p-element-sort_free-icon'],
                        ['title', Phlexible.elements.Strings.title, 'p-element-sort_alpha_down-icon'],
                        ['createdate', Phlexible.elements.Strings.created, 'p-element-sort_date_down-icon'],
                        ['publishdate', Phlexible.elements.Strings.published, 'p-element-sort_date_down-icon'],
                        ['customdate', Phlexible.elements.Strings.custom_date, 'p-element-sort_date_down-icon']
                    ]
                }),
                displayField: 'title',
                valueField: 'id',
                iconClsField: 'icon',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                value: 'free',
                allowBlank: false
            },
            {
                xtype: 'checkbox',
                fieldLabel: '',
                name: 'navigation',
                labelSeparator: '',
                boxLabel: Phlexible.elements.Strings.in_navigation,
                disabled: true
            }
        ];

        if (Phlexible.Config.get('tree.create.use_multilanguage')) {
            if (Phlexible.Config.get('set.language.frontend').length > 1) {
                items.push({
                    xtype: 'iconcombo',
                    fieldLabel: Phlexible.elements.Strings.masterlanguage_dialog,
                    hiddenName: 'masterlanguage',
                    store: new Ext.data.SimpleStore({
                        fields: ['key', 'title', 'iconCls'],
                        data: Phlexible.Config.get('set.language.frontend')
                    }),
                    value: Phlexible.Config.get('language.frontend'),
                    valueField: 'key',
                    displayField: 'title',
                    iconClsField: 'iconCls',
                    editable: false,
                    mode: 'local',
                    typeAhead: false,
                    emptyText: '_select',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    anchor: '-70',
                    allowBlank: false
                });
            }
            else {
                items.push({
                    xtype: 'textfield',
                    inputType: 'hidden',
                    name: 'masterlanguage',
                    value: Phlexible.Config.get('set.language.frontend')[0][0]
                });
            }
        }
        else {
            items.push({
                xtype: 'textfield',
                inputType: 'hidden',
                name: 'masterlanguage',
                value: this.language
            });
        }

        return items;
    }

//        this.getComponent(0).getComponent(1).setValue(this.elementtype_id);
//        this.getComponent(0).getComponent(2).setValue('0');
});
