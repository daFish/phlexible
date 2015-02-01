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
    height: 420,
    minHeight: 420,
    layout: 'border',
    border: true,
    modal: true,

    isExpiredText: '_isExpiredText',
    isDisabledText: '_isDisabledText',
    isLockedText: '_isLockedText',
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
            cls;

        Ext.each(xtypes, function(xtype) {
            cls = Ext.ClassManager.getByAlias('widget.' + xtype);
            console.log(cls.prototype.title, cls.prototype.defaultConfig.iconCls);
            buttons.push({
                itemId: xtype,
                text: cls.prototype.title,
                iconCls: cls.prototype.defaultConfig.iconCls,
                margin: '0 0 5 0',
                width: 135,
                textAlign: 'left',
                toggleHandler: function(btn, state) {
                    if (state) {
                        this.getComponent('cards').getLayout().setActiveItem(xtype);
                    }
                },
                scope: this
            });
            cards.push({
                xtype: xtype,
                itemId: xtype,
                header: false,
                mode: this.mode
            });
        }, this);

        buttons[0].pressed = true;

        this.items = [{
            xtype: 'buttongroup',
            region: 'west',
            itemId: 'buttons',
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
            itemId: 'cards',
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
            itemId: 'infoTbar',
            cls: 'p-users-user-expired',
            border: true,
            hidden: true,
            items: [
                '->',
            {
                itemId: 'disabledBtn',
                iconCls: Phlexible.Icon.get('key'),
                text: this.isDisabledText,
                hidden: true,
                handler: function() {
                    this.getComponent('buttons').getComponent('user-edit-account').toggle(true);
                },
                scope: this
            },{
                itemId: 'expiredBtn',
                iconCls: Phlexible.Icon.get('alarm-clock--exclamation'),
                text: this.isExpiredText,
                hidden: true,
                handler: function() {
                    this.getComponent('buttons').getComponent('user-edit-account').toggle(true);
                },
                scope: this
            },{
                itemId: 'lockedBtn',
                iconCls: Phlexible.Icon.get('lock'),
                text: this.isLockedText,
                hidden: true,
                handler: function() {
                    this.getComponent('buttons').getComponent('user-edit-account').toggle(true);
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
            this.getDockedComponent('infoTbar').show();
            this.getDockedComponent('infoTbar').getComponent('expiredBtn').show();
        }
        if (!record.get('enabled')) {
            this.getDockedComponent('infoTbar').show();
            this.getDockedComponent('infoTbar').getComponent('disabledBtn').show();
        }
        if (record.get('locked')) {
            this.getDockedComponent('infoTbar').show();
            this.getDockedComponent('infoTbar').getComponent('lockedBtn').show();
        }
    },

    saveAndNotify: function() {
        this.doSave(true);
    },

    save: function() {
        this.doSave(false);
    },

    doSave: function(notify) {
        var cardPanel = this.getComponent(1),
            valid = true,
            params = {},
            jsonData = {},
            url, method;

        cardPanel.items.each(function(panel) {
            if (!panel.isValid()) {
                valid = false;
            }
        });

        if (!valid) {
            return;
        }

        if (notify) {
            params[notify] = notify;
        }

        cardPanel.items.each(function(panel) {
            var values = panel.getValues(),
                key;

            Ext.Object.each(values, function(key, value)  {
                jsonData[key] = value;
            });
        });

        if (this.userId) {
            /*
            Phlexible.Rest.put('phlexible_user_put_user', {userId: this.userId, notify: notify}, jsonData);
            Phlexible.Rest.put({
                route: 'phlexible_user_put_user',
                params: {userId: this.userId, notify: notify},
                jsonData: jsonData
            });
            */

            url = Phlexible.Router.generate('phlexible_user_put_user', {userId: this.userId});
            method = 'PUT';
        } else {
            /*
            Phlexible.Rest.put('phlexible_user_post_users', {notify: notify}, jsonData);
            Phlexible.Rest.put({
                route: 'phlexible_user_put_user',
                params: {userId: this.userId, notify: notify},
                jsonData: jsonData
            });
            */

            url = Phlexible.Router.generate('phlexible_user_post_users');
            method = 'POST';
        }
        console.log(url);
        console.log(method);
        console.log(jsonData);
        return;

        Ext.Ajax.request({
            url: url,
            method: method,
            params: params,
            jsonData: jsonData,
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
