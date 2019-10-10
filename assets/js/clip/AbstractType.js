'use strict';

module.exports = class AbstractType
{
    constructor(selector, callback) {
        this.init(selector, callback);
    }

    field = 'media-type';

    value;

    callback;

    init(selector) {
    }

    getValue() {
        return this.value;
    }

    onChange(callback, value) {
        if (typeof(callback) === 'function')
        callback(value);
    }
}
//
// module.exports = {
//     AbstractType: AbstractType
// }
