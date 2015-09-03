/**
 * User detail view
 */
Ext.define('Phlexible.user.view.users.Details', {
    extend: 'Ext.panel.Panel',

    xtype: 'user.users.details',

    iconCls: Phlexible.Icon.get('card'),
    cls: 'p-user-detail',

    noUserSelectedText: '_no_user_selected',
    usernameText: '_username',
    emailText: '_email',
    isDisabledText: '_is_disabled',
    expiresOnText: '_expires_on',
    commentsText: '_comments',
    rolesText: '_roles',
    groupsText: '_groups',
    propertiesText: '_properties',

    initComponent: function() {
        this.html = this.noUserSelectedText;

        this.initMyTemplateExtraContent();
        this.initMyTemplate();

        this.callParent(arguments);
    },

    initMyTemplateExtraContent: function() {
        this.templateExtraContent = '';
    },

    initMyTemplate: function() {
        this.detailTpl = new Ext.XTemplate(
            '<div style="padding: 5px;">',
            '<tpl if="username">',
                '<div style="height: 65px; text-overflow: ellipsis; white-space: nowrap;">',
                    '<img src="http://www.gravatar.com/avatar/{emailHash}?s=60&d=mm" width="60" height="60" style="float: left; margin-right: 5px; border: 1px solid lightgray" />',
                    '<div style="font-weight: bold; margin-bottom: 10px;"><tpl if="firstname || lastname">{firstname} {lastname}<tpl else>-</tpl></div>',
                    Phlexible.Icon.inline('card-address', {"data-qtip": this.usernameText}) + ' {username}<br />',
                    Phlexible.Icon.inline('mail', {"data-qtip": this.emailText}) + ' <a href="mailto:{email}">{email}</a>',
                '</div>',
                '<tpl if="full">',
                    '<tpl if="disabled">',
                        '<div style="padding-top: 10px;">' + Phlexible.Icon.inline('key') + ' ' + this.isDisabledText + '</div>',
                    '</tpl>',
                    '<tpl if="expiresAt">',
                        '<div style="padding-top: 10px;">' + Phlexible.Icon.inline('alarm-clock') + ' ' + this.expiresOnText + ' {expiresAt}</div>',
                    '</tpl>',
                    '<tpl if="comment">',
                        '<div>',
                            '<div style="padding-top: 10px; padding-bottom: 5px; font-weight: bold;">' + Phlexible.Icon.inline('sticky-note-text') + ' ' + this.commentsText + '</div>',
                            '{comment}',
                        '</div>',
                    '</tpl>',
                    this.templateExtraContent,
                    '<tpl if="roles">',
                        '<div>',
                            '<div style="padding-top: 10px; padding-bottom: 5px; font-weight: bold;">' + Phlexible.Icon.inline('user-business') + ' ' + this.rolesText + '</div>',
                            '<tpl for="roles">',
                                '- {.}<br />',
                            '</tpl>',
                        '</div>',
                    '</tpl>',
                    '<tpl if="groups">',
                        '<div>',
                            '<div style="padding-top: 10px; padding-bottom: 5px; font-weight: bold;">' + Phlexible.Icon.inline('users') + ' ' + this.groupsText + '</div>',
                            '<tpl for="groups">',
                                '- {.}<br />',
                            '</tpl>',
                        '</div>',
                    '</tpl>',
                    '<tpl if="properties">',
                        '<div>',
                            '<div style="padding-top: 10px; padding-bottom: 5px; font-weight: bold;">' + Phlexible.Icon.inline('property') + ' ' + this.propertiesText + '</div>',
                            '<tpl foreach="properties">',
                                '{$} = {.}<br />',
                            '</tpl>',
                        '</div>',
                    '</tpl>',
                '</tpl>',
            '<tpl else>',
                this.noUserSelectedText,
            '</tpl>',
            '</div>'
        );

        delete this.templateExtraContent;
    },

    setUsers: function(users) {
        if (!users) {
            this.clear();
        } else if (Ext.isArray(users)) {
            this.showMulti(users);
        } else {
            this.showSingle(users);
        }
    },

    clear: function() {
        this.setIconCls(Phlexible.Icon.get('card'));

        var html = this.detailTpl.apply({});

        this.body.update(html);

        this.collapse();
    },

    showSingle: function(record) {
        this.setIconCls(Phlexible.Icon.get('card'));

        var data = Ext.apply({full: true}, record.data),
            html = this.detailTpl.apply(data);

        this.body.update(html);

        this.expand();
    },

    showMulti: function(records) {
        this.setIconCls(Phlexible.Icon.get('cards'));

        var html = '';

        Ext.each(records, function(record) {
            html += this.detailTpl.apply(record.data);
        }, this);

        this.body.update(html);

        this.expand();
    }
});
