Ext.provide('Phlexible.gui.util.User');

Phlexible.gui.util.User = function (id, username, email, firstname, lastname, properties, roles) {
    this.id = id;
    this.username = username;
    this.email = email;
    this.firstname = firstname;
    this.lastname = lastname;
    this.properties = properties;
    this.roles = roles;
};
Phlexible.gui.util.User.prototype.isGranted = function (role) {
    var isGranted = this.roles.indexOf(role) !== -1;
    if (isGranted) {
        Phlexible.Logger.debug('isGranted(' + role + ') ===', isGranted);
    } else {
        Phlexible.Logger.error('isGranted(' + role + ') ===', isGranted);
    }
});

