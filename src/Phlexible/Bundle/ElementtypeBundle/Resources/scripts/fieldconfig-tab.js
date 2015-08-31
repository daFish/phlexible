Ext.require('Phlexible.element.ElementDataTabHelper');

Phlexible.Storage.set('field', 'tab', function (parentConfig, item, valueStructure, element) {
    var config = {
        xtype: 'panel',
        title: item.labels.fieldLabel[Phlexible.User.getProperty('interfaceLanguage', 'en')],
        layout: 'form',
        cls: 'p-elements-data-tab',
        autoHeight: true,
        autoWidth: true,
        hideMode: 'offsets',
        header: false,
        frame: false,
        border: false,
        collapsible: true,
        collapsed: item.configuration.default_collapsed == 'on',
        titleCollapse: true,
        animCollapse: false,

        isMaster: element.getIsMaster(),
        isDiff: !!element.getDiff(),
        isSortable: (item.configuration.sortable ? true : false),
        //groupId: groupId,
        element: element,

        listeners: {
            activate: function (c) {
                if (c.items && c.element) {
                    c.currentType = c.element.getType();
                    c.currentActive = c.items.indexOf(this.layout.activeItem);
                }
            }
        }
    };

    if (item.children) {
        config.items = Phlexible.element.ElementDataTabHelper.loadItems(item.children, valueStructure, config, element);
    }

    return config;
});

Phlexible.Storage.set('type', 'tab', {
    type: 'tab',
    titles: {
        de: 'Reiter',
        en: 'Tab'
    },
    iconCls: Phlexible.Icon.get('ui-tab'),
    allowedIn: [
        'root',
        'referenceroot'
    ],
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 0,
            suffix: 0,
            help: 1
        }
    }
});
