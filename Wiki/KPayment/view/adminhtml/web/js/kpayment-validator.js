require([
        'jquery',
        'mage/translate',
        'jquery/validate'
    ],
    function($) {
        "use strict";
        $.extend(true, $, {
            mage: {
                /**
                 * Check if string is empty with trim
                 * @param {string} value
                 */
                isEmpty: function(value) {
                    return (value === '' || value === undefined || (value == null) || (value.length === 0) || /^\s+$/.test(value));
                },
                /**
                 * Checks if {value} is between numbers {from} and {to}
                 * @param {string} value
                 * @param {string} from
                 * @param {string} to
                 * @returns {boolean}
                 */
                isBetween: function(value, from, to) {
                    return ($.mage.isEmpty(from) || value >= $.mage.parseNumber(from)) &&
                        ($.mage.isEmpty(to) || value <= $.mage.parseNumber(to));
                },
                /**
                 * Check if string is empty no trim
                 * @param {string} value
                 */
                isEmptyNoTrim: function(value) {
                    return (value === '' || (value == null) || (value.length === 0));
                },
            }
        });

        var isEnabledPlgin = false;

        var rules = {
            "kbank-merchant-length": [
                function (v) {
                    return (v.length === 15);
                },
                $.mage.__('Merchant ID must have length of 15')
            ],
            "kbank-terminal-length":[
                function (v) {
                    return (v.length === 8);
                },
                $.mage.__('Terminal ID must have length of 8')
            ],
            "kbank-md5-hash": [
                function (v) {
                    return (/^[A-Za-z0-9=~!@#$%&]{32}$/.test(v));
                },
                $.mage.__('MD5 hash value is invalid')
            ],
            "kbank-enable-plugin": [
                function(value) {
                    isEnabledPlgin = parseNumber(value);
                    return !$.mage.isEmpty(value);
                }
            ],
            "kbank-required-entry": [
                function(value) {
                    if (!isEnabledPlgin)
                        return true;

                    return !$.mage.isEmpty(value);
                },
                $.mage.__('This is a required field.')
            ],
            "kbank-validate-digits": [
                function(v) {
                    if (!isEnabledPlgin)
                        return true;

                    return $.mage.isEmptyNoTrim(v) || !/[^\d]/.test(v);
                },
                $.mage.__('Please enter a valid number in this field.')
            ],
            "kbank-expiry": [
                function(v, elm) {
                    if (!isEnabledPlgin)
                        return true;

                    var test = isEnabledPlgin;

                    if ($.mage.isEmpty(v)) {
                        return true;
                    }

                    var numValue = parseNumber(v);
                    if (isNaN(numValue)) {
                        return false;
                    }

                    var reRange = /^digits-range-(-?\d+)?-(-?\d+)?$/,
                        result = true;

                    $w(elm.className).each(function(name) {
                        var m = reRange.exec(name);
                        if (m) {
                            result = result && (m[1] == null || m[1] == '' || numValue >= parseNumber(m[1])) && (m[2] == null || m[2] == '' || numValue <= parseNumber(m[2]));
                        }
                    });

                    return result;
                },
                $.mage.__('Please enter 123 payment expiry in between 8 - 720 hours only.'),
                true
            ]
        };

        $.each(rules, function(i, rule) {
            rule.unshift(i);
            $.validator.addMethod.apply($.validator, rule);
        });
    }
);