'use strict';

const AbstractType = require('./AbstractType');
const $ = require('jquery');

module.exports = class InputType extends AbstractType {
    constructor(selector, callback) {
      super(selector, callback);
    }

    init(selector, callback) {
        var obj = this;
        var timer;

        $(selector).keydown(function(e) {
            clearTimeout(timer);
            timer = setTimeout(function(){
                obj.onChange(callback, $(e.target).val());
            }, 350);
        });
    }
}
