/**
 * User window
 */
Ext.define('Phlexible.user.window.UserWindow', {
    extend: 'Ext.Window',

    title: '_user',
    plain: true,
    iconCls: Phlexible.Icon.get('user'),
    width: 630,
    minWidth: 630,
    height: 400,
    minHeight: 400,
    layout: 'border',
    border: true,
    modal: true,

    accountIsExpiredText: '_account_is_expired',
    accountIsDisabledText: '_account_is_disabled',
    saveText: '_save',
    cancelText: '_cancel',
    saveAndNotifyText: '_save_and_notify_text',
    addUserText: '_add_user',
    editUserText: '_edit_user_{0}',
    userText: '_user',

    /**
     * @event save
     */

    /**
     *
     */
    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var xtypes = Phlexible.PluginManager.get('userEditCards'),
            cards = [],
            buttons = [],
            i = 0,
            cls;

        Ext.each(xtypes, function(xtype) {
            cls = Ext.ClassManager.getByAlias('widget.' + xtype);
            console.log(cls.prototype.title, cls.prototype.defaultConfig.iconCls);
            buttons.push({
                text: cls.prototype.title,
                iconCls: cls.prototype.defaultConfig.iconCls,
                margin: '0 0 5 0',
                width: 135,
                textAlign: 'left',
                toggleHandler: function(btn, state) {
                    if (state) {
                        this.getComponent(1).getLayout().setActiveItem('panel-' + xtype);
                    }
                },
                scope: this
            });
            cards.push({
                xtype: xtype,
                itemId: 'panel-' + xtype,
                header: false,
                mode: this.mode
            });
            i += 1;
        }, this);

        buttons[0].pressed = true;

        this.items = [{
            xtype: 'buttongroup',
            region: 'west',
            width: 150,
            columns: 1,
            padding: 5,
            margin: 5,
            defaults: {
                enableToggle: true,
                toggleGroup: 'card'
            },
            items: buttons
        },{
            region: 'center',
            layout: 'card',
            border: false,
            margin: '5 5 5 0',
            items: cards
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            itemId: 'accountExpiredTbar',
            cls: 'p-users-user-expired',
            border: true,
            hidden: true,
            items: [
                '->',
            {
                iconCls: Phlexible.Icon.get('key'),
                text: this.accountIsExpiredText,
                handler: function() {
                    this.getComponent(1).getLayout().setActiveItem('accountPanel');
                },
                scope: this
            }]
        },{
            xtype: 'toolbar',
            dock: 'top',
            itemId: 'accountDisabledTbar',
            cls: 'p-users-user-disabled',
            border: true,
            hidden: true,
            items: [
                '->',
            {
                iconCls: Phlexible.Icon.get('key'),
                text: this.accountIsDisabledText,
                handler: function() {
                    this.getComponent(1).getLayout().setActiveItem('accountPanel');
                },
                scope: this
            }]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.saveAndNotifyText,
                iconCls: Phlexible.Icon.get('disk--arrow'),
                hidden: this.mode !== 'add',
                handler: this.saveAndNotify,
                scope: this
            },{
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: this.save,
                scope: this
            },{
                text: this.cancelText,
                handler: this.close,
                scope: this
            }]
        }];
    },

    show: function(record) {
        this.record = record;
        this.userId = record.get('id');

        if (record.get('username')) {
            this.setTitle(Ext.String.format(this.editUserText, record.get('username')));
        } else {
            this.setTitle(this.addUserText);
        }

        this.callParent();

        var cardPanel = this.getComponent(1);

        cardPanel.items.each(function(panel) {
            panel.loadRecord(record);
        });

        if (record.get('expired') || (record.get('expiresAt') && record.get('expiresAt') <= Ext.Date.format(new Date(), 'Y-m-d H:i:s'))) {
            this.getDockedComponent('accountExpiredTbar').show();
        }
        if (record.get('disabled')) {
            this.getDockedComponent('accountDisabledTbar').show();
        }
    },

    saveAndNotify: function() {
        this.doSave(true);
    },

    save: function() {
        this.doSave(false);
    },

    doSave: function(notify) {
        if (!notify) {
            notify = 0;
        } else {
            notify = 1;
        }

        var cardPanel = this.getComponent(1),
            valid = true,
            params = {},
            url, method;

        cardPanel.items.each(function(panel) {
            if (!panel.isValid()) {
                valid = false;
            }
        });

        if (!valid) {
            return;
        }

        params = {
            notify: notify
        };

        cardPanel.items.each(function(panel) {
            var values = panel.getValues(),
                key;

            for (key in values) {
                params[panel.key + '_' + key] = values[key];
            }
        });

        if (this.userId) {
            params.userId = this.userId;
        }

        if (this.userId) {
            url = Phlexible.Router.generate('phlexible_user_patch', {userId: this.userId});
            method = 'PUT';
        } else {
            url = Phlexible.Router.generate('phlexible_user_create');
            method = 'POST';
        }

        Ext.Ajax.request({
            url: url,
            method: method,
            params: params,
            success: this.onSaveSuccess,
            scope: this
        });
    },

    onSaveSuccess: function(response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.userId = data.userId;
            this.fireEvent('save', this.userId);
            this.close();
        }
    }
});
