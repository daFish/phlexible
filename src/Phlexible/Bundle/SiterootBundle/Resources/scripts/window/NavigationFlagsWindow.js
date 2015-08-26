Ext.define('Phlexible.siteroot.window.NavigationFlagsWindow', {
    extend: 'Ext.window.Window',

    title: '_NavigationFlagsWindow',
    iconCls: 'p-siteroot-flag-icon',
    width: 400,
    height: 360,
    resizable: false,
    modal: true,
    layout: 'fit',

    flags: 0,
    supports: 0,

    flagsText: '_flags',
    noPrependHomeText: '_noPrependHomeText',
    appendActiveText: '_appendActiveText',
    includeNoNavigationText: '_includeNoNavigationText',
    includeRestrictedText: '_includeRestrictedText',
    includeNotPublishedText: '_includeNotPublishedText',
    includeTypeFullText: '_includeTypeFullText',
    includeTypeStructureText: '_includeTypeStructureText',
    includeTypeLayoutText: '_includeTypeLayoutText',
    includeTypeTeaserText: '_includeTypeTeaserText',
    includeUniqueIdText: '_includeUniqueId',
    storeText: '_storeText',
    cancelText: '_cancelText',

    initComponent: function () {
        this.flags = parseInt(this.record.get('flags'), 10);
        this.supports = parseInt(this.record.get('supports'), 10);

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'form',
                bodyStyle: 'padding: 10px',
                border: false,
                items: [
                    {
                        xtype: 'checkboxgroup',
                        fieldLabel: this.flagsText,
                        hideLabel: true,
                        columns: 1,
                        items: [
                            {
                                name: 'flag_1',
                                boxLabel: this.noPrependHomeText,
                                flag: 1,
                                checked: this.flags & 1,
                                disabled: !(this.supports & 1)
                            },
                            {
                                name: 'flag_2',
                                boxLabel: this.appendActiveText,
                                flag: 2,
                                checked: this.flags & 2,
                                disabled: !(this.supports & 2)
                            },
                            {
                                name: 'flag_4',
                                boxLabel: this.includeNoNavigationText,
                                flag: 4,
                                checked: this.flags & 4,
                                disabled: !(this.supports & 4)
                            },
                            {
                                name: 'flag_8',
                                boxLabel: this.includeRestrictedText,
                                flag: 8,
                                checked: this.flags & 8,
                                disabled: !(this.supports & 8)
                            },
                            {
                                name: 'flag_16',
                                boxLabel: this.includeNotPublishedText,
                                flag: 16,
                                checked: this.flags & 16,
                                disabled: !(this.supports & 16)
                            },
                            {
                                name: 'flag_32',
                                boxLabel: this.includeTypeFullText,
                                flag: 32,
                                checked: this.flags & 32,
                                disabled: !(this.supports & 32)
                            },
                            {
                                name: 'flag_64',
                                boxLabel: this.includeTypeStructureText,
                                flag: 64,
                                checked: this.flags & 64,
                                disabled: !(this.supports & 64)
                            },
                            {
                                name: 'flag_128',
                                boxLabel: this.includeTypeLayoutText,
                                flag: 128,
                                checked: this.flags & 128,
                                disabled: !(this.supports & 128)
                            },
                            {
                                name: 'flag_256',
                                boxLabel: this.includeTypeTeaserText,
                                flag: 256,
                                checked: this.flags & 256,
                                disabled: !(this.supports & 256)
                            },
                            {
                                name: 'flag_512',
                                boxLabel: this.includeUniqueIdText,
                                flag: 512,
                                checked: this.flags & 512,
                                disabled: !(this.supports & 512)
                            }
                        ]
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.storeText,
                handler: function () {
                    var flags = 0;
                    this.getComponent(0).getComponent(0).items.each(function (cb) {
                        if (cb.checked) {
                            flags = flags | cb.flag;
                        }
                    }, this);

                    this.record.set('flags', flags);

                    this.close();
                },
                scope: this
            },
            {
                text: this.cancelText,
                handler: function () {
                    this.close();
                },
                scope: this
            }]
        }];
    }
});
