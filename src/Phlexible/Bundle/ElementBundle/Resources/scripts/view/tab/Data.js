Ext.provide('Phlexible.element.view.tab.Data');

Ext.require('Phlexible.element.view.ElementContentPanel');
Ext.require('Phlexible.tree.window.PublishSlaveWindow');
Ext.require('Phlexible.tree.model.Version');

Phlexible.element.view.tab.Data = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.content,
    iconCls: 'p-element-tab_data-icon',
    cls: 'p-elements-data-panel',
    autoScroll: false,
    autoWidth: false,
    layout: 'fit',
    hideMode: 'offsets',

    currentETID: null,
    currentActive: null,

    accordionCollapsed: false,

    initComponent: function () {
        this.addEvents(
            'save'
        );

        this.element.on({
            load: this.onLoadElement,
            getlock: this.onGetLock,
            islocked: this.onIsLocked,
            removelock: this.onRemoveLock,
            scope: this
        });

        this.compareElement = new Phlexible.element.Element({
            siterootId: this.element.getSiterootId(),
            language: this.element.getLanguage()
        });

        this.items = [
            {
                xtype: 'form',
                region: 'center',
                layout: 'border',
                hideMode: 'offsets',
                border: false,
                items: [{
                    xtype: 'elements-elementcontentpanel',
                    region: 'center',
                    element: this.element,
                    listeners: {
                        tabchange: function (tabPanel, panel) {
                            var index = tabPanel.items.items.indexOf(panel);
                            var targetPanel = this.getComponent(1).getComponent(1).getComponent(index);
                            if (targetPanel) {
                                this.getComponent(1).getComponent(1).setActiveTab(targetPanel);
                            }
                        },
                        scope: this
                    }
                },{
                    xtype: 'elements-elementcontentpanel',
                    region: 'east',
                    width: 400,
                    split: true,
                    hidden: true,
                    element: this.compareElement,
                    listeners: {
                        tabchange: function (tabPanel, panel) {
                            var index = tabPanel.items.items.indexOf(panel);
                            var targetPanel = this.getComponent(1).getComponent(1).getComponent(index);
                            if (targetPanel) {
                                this.getComponent(1).getComponent(1).setActiveTab(targetPanel);
                            }
                        },
                        scope: this
                    }
                }]
            }
        ];

        this.tbar = [
            {
                // 0
                text: this.strings.save,
                iconCls: 'p-element-save-icon',
                handler: this.onSave,
                scope: this
            },
            {
                // 1
                text: this.strings.publish_element,
                iconCls: 'p-element-publish-icon',
                disabled: true,
                handler: this.onPublish,
                scope: this
            },
            '->',
            {
                // 3
                xtype: 'tbtext',
                text: this.strings.diff.show,
                hidden: true
            },
            ' ',
            {
                // 5
                xtype: 'iconcombo',
                hidden: true,
                width: 140,
                value: this.element.getLanguage(),
                emptyText: this.strings.diff.language,
                store: new Ext.data.JsonStore({
                    fields: ['langKey', 'text', 'iconCls'],
                    data: this.element.getLanguages()
                }),
                valueField: 'langKey',
                displayField: 'text',
                iconClsField: 'iconCls',
                mode: 'local',
                forceSelection: true,
                typeAhead: false,
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },
            ' ',
            {
                // 7
                xtype: 'combo',
                hidden: true,
                width: 80,
                listWidth: 200,
                emptyText: this.strings.diff.from_version,
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class="x-combo-list-item">',
                    '<tpl if="values.isPublished"><b></tpl>',
                    '<tpl if="values.wasPublished"><i></tpl>',
                    '{version} [{createdAt:date("Y-m-d H:i:s")}]',
                    '<tpl if="values.wasPublished"></i></tpl>',
                    '<tpl if="values.isPublished"></b></tpl>',
                    '</div>',
                    '</tpl>'
                ),
                //value: (parseInt(this.element.version, 10) - 1),
                store: new Ext.data.JsonStore({
                    fields: Phlexible.tree.model.Version,
                    id: 0,
                    data: []
                }),
                displayField: 'version',
                mode: 'local',
                forceSelection: true,
                typeAhead: false,
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },
            {
                // 8
                text: this.strings.diff.compare_to,
                enableToggle: true,
                hidden: true,
                toggleHandler: function (btn, state) {
                    this.getTopToolbar().items.items[this.btnIndex.compareVersion].setDisabled(!state);
                },
                scope: this
            },
            {
                // 9
                xtype: 'combo',
                width: 80,
                hidden: true,
                disabled: true,
                listWidth: 200,
                emptyText: this.strings.diff.to_version,
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class="x-combo-list-item">',
                    '<tpl if="values.isPublished"><b></tpl>',
                    '<tpl if="values.wasPublished"><i></tpl>',
                    '{version} [{createdAt:date("Y-m-d H:i:s")}]',
                    '<tpl if="values.wasPublished"></i></tpl>',
                    '<tpl if="values.isPublished"></b></tpl>',
                    '</div>',
                    '</tpl>'
                ),
                //value: (parseInt(this.element.version, 10) - 1),
                store: new Ext.data.JsonStore({
                    fields: Phlexible.tree.model.Version,
                    id: 0,
                    data: []
                }),
                displayField: 'version',
                mode: 'local',
                forceSelection: true,
                typeAhead: false,
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },
            ' ',
            {
                // 11
                text: this.strings.diff.load,
                hidden: true,
                handler: this.loadDiff,
                scope: this
            },
            '-',
            {
                // 13
                text: this.strings.diff.compare,
                iconCls: 'p-element-diff-icon',
                enableToggle: true,
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.showDiff();
                    } else {
                        this.hideDiff();

                        if (this.element.getDiff() && this.element.getDiff().enabled) {
                            this.element.reload();
                        }
                    }
                },
                scope: this,
                listeners: {
                    render: function () {
                        var tb = this.getTopToolbar();
                        tb.items.items[this.btnIndex.show].hide();
                        tb.items.items[this.btnIndex.sep].hide();
                    },
                    scope: this
                }
            }
        ];

        this.btnIndex = {
            save: 0,
            publish: 1,
            show: 3,
            language: 5,
            version: 7,
            compare: 8,
            compareVersion: 9,
            load: 11,
            sep: 12,
            enable: 13
        };

        this.keys = [
            {
                key: 's',
                alt: true,
                stopEvent: true,
                fn: this.onInternalSave,
                scope: this
            }
        ];

        /*
         this.getContentPanel().on({
         render: {
         fn: function(c) {
         c.body.on({
         scroll: {
         fn: function(e) {
         Phlexible.console.log(e);

         this.getComponent(1).getComponent(1).el.scrollTo('top', 100, false);
         },
         scope: this
         }
         });
         },
         scope: this
         }
         })
         */

        this.diffParams = (this.element.startParams && this.element.startParams.diff)
            ? this.element.startParams.diff
            : {};

        Phlexible.element.view.tab.Data.superclass.initComponent.call(this);
    },

    getFormPanel: function () {
        return this.getComponent(0);
    },

    getContentPanel: function () {
        return this.getFormPanel().getComponent(0);
    },

    getCompareContentPanel: function () {
        return this.getFormPanel().getComponent(1);
    },

    getAccordionPanel: function () {
        return this.getSidebarPanel().getComponent(1);
    },

    onLoadElement: function (element) {
        // load diff data
        var tb = this.getTopToolbar(),
            toVersion;

        if (this.diffParams.version) {
            toVersion = this.diffParams.version;
        } else {
            toVersion = element.getVersion() > 1 ? element.getVersion() - 1 : element.getVersion();
        }

        tb.items.items[this.btnIndex.version].store.loadData(element.getVersions());
        tb.items.items[this.btnIndex.compareVersion].store.loadData(element.getVersions());
        tb.items.items[this.btnIndex.version].setValue(toVersion);
        tb.items.items[this.btnIndex.compareVersion].setValue(element.getVersion());

        if (this.diffParams.enabled) {
            tb.items.items[this.btnIndex.compare].toggle(true);
            tb.items.items[this.btnIndex.enable].toggle(true);
        }

        if (tb.items.items[this.btnIndex.compare].pressed) {
            this.loadDiff();
        }

        this.diffParams = {};
    },

    loadDiff: function () {
        var tb = this.getTopToolbar(),
            languageField = tb.items.items[this.btnIndex.language],
            versionField = tb.items.items[this.btnIndex.version],
            compareVersionField = tb.items.items[this.btnIndex.compareVersion],
            diffLanguage = languageField.getValue(),
            version = versionField.getValue(),
            compareVersion = null;

        if (!version) {
            version = this.element.getVersion();
            versionField.setValue(version);
        }

        if (tb.items.items[this.btnIndex.compare].pressed && compareVersionField.getValue()) {
            compareVersion = compareVersionField.getValue();
        }

        this.compareElement.reload({
            id: this.element.getNodeId(),
            version: version,
            language: diffLanguage,
            lock: 0,
            diff: 1,
            compareVersion: compareVersion
        });
    },

    toggleDiff: function (diff) {
        var tb = this.getTopToolbar();

        if (diff.enabled) {
            tb.items.items[this.btnIndex.compare].toggle(true, true);
            tb.items.items[this.btnIndex.language].setValue(diff.language);
            tb.items.items[this.btnIndex.version].setValue(diff.version);
            tb.items.items[this.btnIndex.compareVersion].setValue(diff.compareVersion);

            this.showDiff();
        } else {
            tb.items.items[this.btnIndex.compare].toggle(false, true);
            this.hideDiff();
        }
    },

    showDiff: function () {
        this.isDiffMode = true;
        var tb = this.getTopToolbar();

        tb.items.items[this.btnIndex.enable].toggle(true);

        tb.items.items[this.btnIndex.show].show();
        tb.items.items[this.btnIndex.language].show();
        tb.items.items[this.btnIndex.version].show();
        tb.items.items[this.btnIndex.compare].show();
        tb.items.items[this.btnIndex.compareVersion].show();
        tb.items.items[this.btnIndex.sep].show();
        tb.items.items[this.btnIndex.load].show();

        this.getCompareContentPanel().show();
        this.getCompareContentPanel().setWidth(this.getInnerWidth() / 2);
        this.doLayout();

        if (this.accordionCollapsed) {
            //this.getComponent(1).expand();
        } else {
            //this.getComponent(1).setWidth(this.getInnerWidth() / 2);
            //this.getComponent(1).ownerCt.doLayout();
        }
    },

    hideDiff: function () {
        this.isDiffMode = false;
        var tb = this.getTopToolbar();

        tb.items.items[this.btnIndex.enable].toggle(false);

        tb.items.items[this.btnIndex.show].hide();
        tb.items.items[this.btnIndex.language].hide();
        tb.items.items[this.btnIndex.version].hide();
        tb.items.items[this.btnIndex.compare].hide();
        tb.items.items[this.btnIndex.compareVersion].hide();
        tb.items.items[this.btnIndex.sep].hide();
        tb.items.items[this.btnIndex.load].hide();

        this.getCompareContentPanel().hide();
        if (this.accordionCollapsed) {
            //this.getComponent(1).collapse();
        }
        this.doLayout();
    },

    onReset: function () {
        this.element.reload();
    },

    onSave: function () {
        if (!this.getContentPanel().isValid()) {
            //errors.push('Required fields are missing.');
            return;
        }

        /*
        if (this.element.fireEvent('beforeSave', this.element) === false) {
            errors.push('Save cancelled.');
            return;
        }
        */


        this.getContentPanel().syncData(this.getContentPanel());

        var data = {
                values: {},
                structures: []
            };

        this.recurseFields(this.getFormPanel(), data);

        console.log(data);
    },

    recurseFields: function(p, data) {
        p.items.each(function(c) {
            if (c.xtype === 'group' && (c.isOptional || c.isRepeatable)) {
                console.log('GROUP', c.xtype, c.dsId);
                var x = {
                    id: c.dataId,
                    values: {},
                    structures: []
                };
                data.structures.push(x);
                this.recurseFields(c, x);
            } else if (c.isFormField) {
                console.log('FIELD', c.xtype, c.dsId, c.getValue());
                data.values[c.dsId] = c.getValue();
            } else if (c.items) {
                this.recurseFields(c, data);
            }
        }, this);
    },

    onGetLock: function () {
        //Phlexible.console.debug('ElementDataPanel: GET LOCK');

        var tb = this.getTopToolbar();

        /*
         var x = function() {
         this.getTopToolbar().items.items[this.btnIndex.save].enable();
         };
         x.defer(5000, this);

         tb.items.items[this.btnIndex.save].disable();
         tb.items.items[this.btnIndex.minor].enable();
         tb.items.items[this.btnIndex.reset].enable();
         */
        tb.items.items[this.btnIndex.enable].enable();
    },

    onIsLocked: function () {
        //Phlexible.console.debug('ElementDataPanel: IS LOCKED');

        var tb = this.getTopToolbar();

        /*
         tb.items.items[this.btnIndex.save].disable();
         tb.items.items[this.btnIndex.minor].disable();
         tb.items.items[this.btnIndex.reset].disable();
         */
        tb.items.items[this.btnIndex.enable].disable();
        this.hideDiff();
    },

    onRemoveLock: function () {
        //Phlexible.console.debug('ElementDataPanel: DROP LOCK');

        var tb = this.getTopToolbar();

        /*
         tb.items.items[this.btnIndex.save].disable();
         tb.items.items[this.btnIndex.minor].disable();
         tb.items.items[this.btnIndex.reset].disable();
         */
        tb.items.items[this.btnIndex.enable].disable();
        this.hideDiff();
    }
});

Ext.reg('element-tab-data', Phlexible.element.view.tab.Data);
