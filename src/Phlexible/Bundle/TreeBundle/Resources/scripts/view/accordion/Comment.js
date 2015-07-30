Ext.provide('Phlexible.tree.view.accordion.Comment');

Phlexible.tree.view.accordion.Comment = Ext.extend(Ext.FormPanel, {
    title: Phlexible.elements.Strings.comment,
    tabTip: Phlexible.elements.Strings.comment,
    cls: 'p-tree-comment',
    iconCls: 'p-element-comment-icon',
    height: 200,
    autoHeight: true,

    key: 'comment',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.items = [
            {
                xtype: 'textarea',
                hideLabel: true,
                anchor: '100%',
                height: 200
            }
        ];

        Phlexible.tree.view.accordion.Comment.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        this.getComponent(0).setValue(element.getComment());
    },

    getData: function () {
        return this.getComponent(0).getValue();
    }
});

Ext.reg('tree-accordion-comment', Phlexible.tree.view.accordion.Comment);
