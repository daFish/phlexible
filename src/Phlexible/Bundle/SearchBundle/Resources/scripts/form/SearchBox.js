Ext.define('Phlexible.search.form.SearchBox', {
    extend: 'Ext.form.ComboBox',
    requires: ['Phlexible.search.model.Result'],

    xtype: 'searchbox',

    displayField: 'title',
    cls: 'p-searchbox',
    typeAhead: false,
    loadingText: '_loadingText',
    width: 150,
    growWidth: false,
    listWidth: 500,
    maxHeight: 500,
    pageSize: 8,
    minChars: 2,
    typeAheadDelay: 500,
    //hideTrigger: true,
    triggerClass: 'x-form-search-trigger',
    itemSelector: 'div.search-item',
    initComponent: function () {
        this.origWidth = this.width;

        this.initMyStore();
        this.initMyTemplate();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.search.model.Result',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_search_get_results'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'results',
                    totalProperty: 'totalCount'
                }
            }
        });
    },

    initMyTemplate: function() {
        // Custom rendering Template
        this.tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="search-item">',
            '<div class="search-result-image">',
            '<img src="{image}" alt="{title}" />',
            '</div>',
            '<div class="search-result-text">',
            '<h3><span>{date:date("Y-m-d H:i:s"}<br />by {author}</span>{title}</h3>',
            '{component}<br />&nbsp;',
            '</div>',
            '<div class="x-clear"">',
            '</div>',
            '</div>',
            '</tpl>'
        );
    },

    initMyListeners: function() {
        this.on({
            focus: function (c) {
                if (this.growWidth) {
                    this.setWidth(this.growWidth);
                }
            },
            blur: function (c) {
                if (this.growWidth) {
                    this.setWidth(this.origWidth);
                }
            },
            beforeselect: function (combo, record) {
                var handlerData = record.get('handler');

                if (handlerData && handlerData.handler) {
                    var handler = Phlexible.Handles.get(handlerData.handler)();

                    if (handlerData.parameters) {
                        handler.setParameters(handlerData.parameters);
                    }

                    handler.handle();
                }

                return false;
            },
            render: function () {
                Phlexible.globalKeyMap.accessKey({key: 'f', alt: true}, this.focus, this);
            },
            scope: this
        });
    }
});
