Ext.provide('Phlexible.element.view.ElementContentPanel');

Ext.require('Phlexible.element.view.ElementContentTabPanel');
Ext.require('Phlexible.element.ElementDataTabHelper');
//xt.require('Phlexible.element.ElementDataTab');

Phlexible.element.view.ElementContentPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    cls: 'p-elements-content-panel',
    frame: false,
    plain: false,
    autoHeight: false,
    border: false,
    autoScroll: false,
    autoWidth: false,
    deferredRender: false,
    hideMode: 'offsets',
    layout: 'fit',

    currentETID: null,
    currentActive: null,

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            getlock: this.onGetLock,
            islocked: this.onIsLocked,
            removelock: this.onRemoveLock,
            scope: this
        });

        this.on({
            render: function (c) {
                Ext.dd.ScrollManager.register(c.body);
            },
            scope: this
        });

        Phlexible.element.view.ElementContentPanel.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        element.prototypes.clear();
        this.removeAll();

        this.structure = element.getStructure();
        this.valueStructure = element.getValueStructure();

        if (!Ext.isEmpty(element.getStructure())) { // Ext.type(element.data.structure) == 'object') {
            //if(this.isVisible()) {
            this.lateRender();

//            this.enable();
            //}
//        } else {
//            this.disable();
        }
    },

    lateRender: function () {
        if (this.structure) {
            var structureNodes = this.structure,
                valueStructure = this.valueStructure,
                targetPanel = this,
                lazy = false,
                noTitle = true;

//            this.disable();

            structureNodes = Phlexible.element.ElementDataTabHelper.fixStructure(structureNodes, valueStructure, null);

            Phlexible.element.ElementDataTabHelper.importPrototypes(structureNodes, this.element);

            if (structureNodes[0].type === 'tab' && structureNodes.length > 1) {
                targetPanel = {
                    xtype: 'elements-elementcontenttabpanel',
                    items: [],
                    activeTab: 0,
                    listeners: {
                        tabchange: function(tabs, tab) {
                            this.currentTab = tabs.items.indexOf(tab);
                        },
                        scope: this
                    }
                };
                lazy = true;
                noTitle = false;
                if (this.lastTab && this.lastType === this.element.getType()) {
                    targetPanel.activeTab = this.lastTab;
                }
                else if (!Ext.isEmpty(this.element.getDefaultContentTab())) {
                    targetPanel.activeTab = this.element.getDefaultContentTab();
                    this.lastTab = targetPanel.activeTab;
                }
            }

            this.lastType = this.element.getType();

            items = Phlexible.element.ElementDataTabHelper.loadItems(structureNodes, valueStructure, this, this.element);

            /*
             Ext.each(structure, function(item) {
             var config = {
             xtype: 'panel',
             title: noTitle ? null : item.labels.fieldLabel[Phlexible.User.getProperty('interfaceLanguage', 'en')],
             iconCls: noTitle ? null : 'p-element-tab-icon',
             hideMode: 'offsets',
             autoScroll: true,
             items: {
             xtype: 'elements-elementdatatab',
             hideMode: 'offsets',
             element: this.element,
             structure: item.children,
             valueStructure: valueStructure
             },
             listeners: {
             activate: function(c) {
             this.currentETID = this.element.data.properties.et_id;
             this.currentActive = this.items.indexOf(this.layout.activeItem);
             },
             scope: this
             }
             };
             if (lazy) {
             targetPanel.items.push(config);
             } else {
             targetPanel.add(config);
             }
             }, this);
             */

            if (lazy) {
                targetPanel.items = items;
                this.add(targetPanel);
            } else {
                Ext.each(items, function (item) {
                    targetPanel.add(item);
                });
            }

//            for(var i=0; i<structure.length; i++) {
//
//                var formPanel = new Phlexible.element.ElementDataTab({
//                    title: structure[i].name,
//                    element: this.element,
//                    disabled: this.element.locked_by_me ? false : true
//                });
//
//                formPanel.loadData(structure[i].children);
//
//                this.formPanels.push(formPanel);
//                this.tabPanel.add(formPanel);
//            }

            if (this.element.getLockStatus() != 'edit') {
                this.items.each(function (panel) {
                    panel.disable();
                });
            }

            this.doLayout();
//            this.enable();

            this.structure = false;
        }
    },

    syncData: function (c) {
        if (!c.items) return;

        c.items.each(function (i) {
            if (i.isFormField && i.syncValue && typeof i.syncValue == 'function') {
                //Phlexible.console.log('sync item value');
                i.syncValue();
            } else if (!i.isFormField) {
                this.syncData(i);
            }
        }, this);
    },

//    fetchDisabled: function(c) {
//        if(!c.items) return;
//
//        c.items.each(function(i) {
//            if(i.isFormField) {
//                if (i.disabled && i.fieldLabel && i.name && new String(i.getRawValue()).length) {
//                    this.disabledValues[i.name] = i.getRawValue();
//                }
//            } else {
//                this.fetchDisabled(i);
//            }
//        }, this);
//    },

    isValid: function () {
        var valid = true;

        var chk = function (item) {
            if (item.isFormField) {
                if (!item.isValid()) {
                    Phlexible.console.warn('Not valid', item);
                    valid = false;
                    return false;
                }
            } else if (item.items) {
                item.items.each(chk);
            }
            return true;
        };
        this.items.each(chk);

        return valid;
    },

    onGetLock: function () {
        this.items.each(function (panel) {
            panel.enable();
        });
    },

    onIsLocked: function () {
        this.items.each(function (panel) {
            panel.disable();
        });
    },

    onRemoveLock: function () {
        this.items.each(function (panel) {
            panel.disable();
        });
    }
});

Ext.reg('elements-elementcontentpanel', Phlexible.element.view.ElementContentPanel);
