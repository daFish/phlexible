/**
 * Last logins portlet
 */
Ext.define('Phlexible.user.portlet.LastLogins', {
    extend: 'Portal.view.Portlet',
    alias: 'widget.users-online-portlet',

    title: '_last_logins',
    bodyStyle: 'padding: 5px',
    iconCls: Phlexible.Icon.get('users'),
    extraCls: 'last-logins-portlet',

    imageUrl: '/bundles/phlexibleuser/images/portlet-users.png',

    noLastLoginsText: '_no_last_logins_text',
    userText: '_user',
    loggedInText: '_logged_in',
    loggedOutText: '_logged_out',

    initComponent: function() {
        this.store = new Ext.data.SimpleStore({
            model: 'Phlexible.user.model.LastLogin',
            idProperty: 'userId',
            sorters: {
                property: 'username',
                username: 'ASC'
            }
        });

        var data = this.item.data;
        if(data) {
            Ext.each(data, function(item) {
                this.add(Ext.create('Phlexible.user.model.LastLogin', item));
            }, this.store);
        }

        this.items = [{
            xtype: 'dataview',
            itemSelector: 'div.portlet-online',
            style: 'overflow: auto',
            singleSelect: true,
            emptyText: this.noLastLoginsText,
            deferEmptyText: false,
            autoHeight: true,
            store: this.store,
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                '<div id="portal_online_{userId}" class="p-last-logins-portlet">',
                '<div class="image"><img src="http://www.gravatar.com/avatar/{emailHash}?s=40&d=mm" width="40" height="40" /></div>',
                '<div class="name">{name}</div>',
                '<div class="age">Logged in {[Phlexible.Format.age(values.loginSeconds)]} ago.</div>',
                '<div style="clear: both;"></div>',
                '</div>',
                '</tpl>'
            )
        }];

        this.callParent(arguments);
    },

    updateData: function(data){
        var onlineMap = [];
        var i, r;

        for(i=0; i<data.length; i++) {
            var row = data[i];
            onlineMap.push(row.userId);
            r = this.store.getById(row.userId);
            if(r) {
                r.set('login_seconds', row.login_seconds);
            } else {
                this.store.add(Ext.create('Phlexible.user.model.LastLogin', row, row.userId));

                Phlexible.msg('Online', this.userText + ' "' + row.username + '" ' + this.loggedInText);
                Ext.fly('portal_online_' + row.userId).frame('#8db2e3', 1);
            }
        }

        for(i=0; i<this.store.getCount(); i++) {
            r = this.store.getAt(i);
            if(onlineMap.indexOf(r.id) == -1) {
                Phlexible.msg('Online', this.userText + ' "' + r.get('username') + '" ' + this.loggedOutText);
                this.store.remove(r);
            }
        }

        this.store.sort('username', 'ASC');
    }
});
