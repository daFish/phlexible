/**
 * User window
 */
Ext.define('Phlexible.user.window.UserWindow', {
    extend: 'Ext.window.Window',

    plain: true,
    iconCls: Phlexible.Icon.get('user'),
    width: 630,
    minWidth: 630,
    height: 420,
    minHeight: 420,
    layout: 'fit',
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
        var xtypes = Phlexible.Storage.getSection('userEditCards'),
            tabs = [],
            mode = this.mode,
            user = this.user;

        Ext.each(xtypes, function(config) {
            config.mode = mode;
            config.user = user;
            config.border = false;

            tabs.push(config);
        });

        this.items = [{
            xtype: 'tabpanel',
            itemId: 'tabs',
            tabPosition: 'left',
            tabRotation: 0,
            tabBar: {
                border: false
            },
            defaults: {
                textAlign: 'left'
            },
            border: false,
            activeTab: 0,
            deferredRender: false,
            items: tabs
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
            items: [
                '->',
            {
                text: this.cancelText,
                handler: this.close,
                scope: this
            },
                '-',
            {
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: this.save,
                scope: this
            },{
                text: this.saveAndNotifyText,
                iconCls: Phlexible.Icon.get('disk--arrow'),
                hidden: this.mode !== 'add',
                handler: this.saveAndNotify,
                scope: this
            }]
        }];
    },

    show: function() {
        var user = this.user;

        this.setTitle(Ext.String.format(this.editUserText, user.get('username')));

        this.callParent();

        if (user.get('expired') || (this.mode === 'edit' && user.get('expiresAt') && user.get('expiresAt') <= Ext.Date.format(new Date(), 'Y-m-d H:i:s'))) {
            this.getDockedComponent('infoTbar').show();
            this.getDockedComponent('infoTbar').getComponent('expiredBtn').show();
        }
        if (!user.get('enabled')) {
            this.getDockedComponent('infoTbar').show();
            this.getDockedComponent('infoTbar').getComponent('disabledBtn').show();
        }
        if (user.get('locked')) {
            this.getDockedComponent('infoTbar').show();
            this.getDockedComponent('infoTbar').getComponent('lockedBtn').show();
        }

        this.getComponent('tabs').items.each(function(panel) {
            panel.loadUser(user);
        });
    },

    saveAndNotify: function() {
        this.doSave(true);
    },

    save: function() {
        this.doSave(false);
    },

    doSave: function(notify) {
        var tabs = this.getComponent('tabs'),
            valid = true;

        tabs.items.each(function(panel) {
            if (!panel.isValid()) {
                valid = false;
            }
        });

        if (!valid) {
            return;
        }

        this.user.set('notify', !!notify);

        tabs.items.each(function(panel) {
            if (!Ext.isFunction(panel.applyToUser)) {
                return;
            }
            panel.applyToUser(this.user);
        });

        if (this.user.phantom) {
            this.user.set('createdAt', new Date());
            this.fireEvent('create', this.user);
        } else {
            this.fireEvent('update', this.user);
        }
    }
});
