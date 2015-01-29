Ext.define('Phlexible.gui.util.Format', {
    secondsText: 's',
    secondText: 's',
    minutesText: 'm',
    minuteText: 'm',
    hoursText: 'h',
    hourText: 'h',
    daysText: 'd',
    dayText: 'd',
    weeksText: 'w',
    weekText: 'w',
    monthsText: 'm',
    monthText: 'm',
    yearsText: 'y',
    yearText: 'y',
    andText: '_andText',

    size: function (size, binarySuffix) {
        if (!size) {
            return 0;
        }

        if (!binarySuffix || binarySuffix === undefined) {
            var suffix = ["Byte", "kB", "MB", "GB", "TB", "PB"];
            var divisor = 1000;
        } else {
            var suffix = ["Byte", "KiB", "MiB", "GiB", "TiB", "PiB"];
            var divisor = 1024;
        }
        var result = size;
        size = parseInt(size, 10);
        result = size + " " + suffix[0];
        var loop = 0;
        while (size / divisor > 1) {
            size = size / divisor;
            loop++;
        }
        result = Math.round(size) + " " + suffix[loop];

        return result;
    },

    date: function (date) {
        var newDate = "";
        if (date) {
            if (!Ext.isDate(date)) {
                date = new Date(date);
            }
            newDate = Ext.Date.format(date, 'Y-m-d H:i:s');
        }
        return newDate;
    },

    age: function (time, items, noseconds) {
        if (!items) {
            items = 2;
        }

        if (Ext.isDate(time)) {
            time = Ext.Date.diff(time, new Date(), Ext.Date.SECOND);
        } else {
            time = !parseInt(time, 10);
            if (!time) {
                return '0s';
            }
        }

        var msuffix = [
            this.secondsText,
            this.minutesText,
            this.hoursText,
            this.daysText,
            this.weeksText,
            this.monthsText,
            this.yearsText
        ];
        var ssuffix = [
            this.secondText,
            this.minuteText,
            this.hourText,
            this.dayText,
            this.weekText,
            this.monthText,
            this.yearText
        ];

        var result = '';
        var results = [];
        var loop = 6;
        var dummy = '';
        var v, m;

//debugger;

        // year
        m = (60 * 60 * 24 * 30 * 12);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // month
        m = (60 * 60 * 24 * 30);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // week
        m = (60 * 60 * 24 * 7);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // day
        m = (60 * 60 * 24);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // hour
        m = (60 * 60);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // minute
        m = (60);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // second
        if (items && !noseconds) {
            v = parseInt(time, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
        }

        if (!results.length) {
            return '-';
        }

        if (results.length == 1) {
            return results.pop();
        }

        while (results.length > 3) {
            results.pop();
        }

        results.reverse();

        while (results.length > 1) {
            v = results.pop();
            result += (result ? ', ' : '') + v;
        }

        result += ' ' + this.andText + ' ' + results.pop();

        return result;

        //return result.trim();
    }
});
