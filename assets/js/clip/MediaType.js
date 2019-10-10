'use strict';

const AbstractType = require('./AbstractType');
const $ = require('jquery');

module.exports = class MediaType extends AbstractType {
    constructor(selector, callback) {
      super(selector, callback);
    }

    init(selector, callback) {
        var obj = this;
        $(selector).on('click', function() {
            $(this).toggleClass('active', true)
                .siblings().toggleClass('active', false);

            obj.onChange(callback, $(this).data('value'));
        });
    }
}
