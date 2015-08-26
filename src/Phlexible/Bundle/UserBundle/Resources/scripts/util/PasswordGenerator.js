/**
 * Password generator
 */
Ext.define('Phlexible.user.util.PasswordGenerator', {
    vowel: /[aeiouAEIOU]$/,
    consonant: /[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]$/,

    /**
     * Create password
     *
     * @param {Number} length
     * @param {Boolean} memorable
     * @param {RegExp} pattern
     * @param {String} prefix
     */
    create: function (length, memorable, pattern, prefix) {
        var char, n;
        if (length === null) {
            length = 10;
        }
        if (!memorable) {
            memorable = true;
        }
        if (!pattern) {
            pattern = /\w/;
        }
        if (!prefix) {
            prefix = '';
        }
        if (prefix.length >= length) {
            return prefix;
        }
        if (memorable) {
            if (prefix.match(this.consonant)) {
                pattern = this.vowel;
            } else {
                pattern = this.consonant;
            }
        }
        n = (Math.floor(Math.random() * 100) % 94) + 33;
        char = String.fromCharCode(n);
        if (memorable) {
            char = char.toLowerCase();
        }
        if (!char.match(pattern)) {
            return this.create(length, memorable, pattern, prefix);
        }
        return this.create(length, memorable, pattern, "" + prefix + char);
    }
});
