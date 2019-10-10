'use strict';

const $ = require('jquery');

module.exports = function ResourceList(resource_url, callback) {

    this.url;

    this.params = {};

    this.callback;

    this.clearParams = function () {
        this.params = {};
    }

    this.setParams = function (params) {
        this.params = params;
    }

    this.getParams = function (params) {
        return this.params;
    }

    this.setCriteria = function (nameOfCriterion, searchOption, searchPhrase) {
        this.setPage(1);
        if (searchPhrase) {
            this.params['criteria[' + nameOfCriterion + '][type]'] = searchOption;
            this.params['criteria[' + nameOfCriterion + '][value]'] = searchPhrase;
        } else {
            delete this.params['criteria[' + nameOfCriterion + '][type]'];
            delete this.params['criteria[' + nameOfCriterion + '][value]'];
        }
    }

    this.setSort = function (nameOfField, direction) {
        this.params['sorting[' + nameOfField + ']'] = direction;
    }

    this.setPage = function(page) {
        this.params['page'] = page;
    }

    this.reload = function (callback) {
        var obj = this;
        $.getJSON(this.url, this.params, function(data) {
            if (typeof(obj.callback) === 'function')
                obj.callback(data);
        })
    }

    this.init = function (url, callback) {
        this.url = url;
        this.callback = callback;
    }

    this.init(resource_url, callback);
}
