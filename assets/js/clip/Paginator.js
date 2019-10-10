'use strict'

const $ = require('jquery');

module.exports = class Paginator {

    container;
    callback;
    pages = 0;
    currentPage = 1;

    constructor(container, callback) {
        this.container = container;
        this.callback = callback;
    }

    setPages(pages) {
        this.pages = pages;
    }

    setCurrent(current) {
        this.currentPage = current;
    }

    clickHandler(e) {
        callback($(e.currentTarget).data('page'));
    }

    pagesHtml() {
        var html = ''

        var range = 7;

        if (range > this.pages) {
            range = this.pages;
        }

        var delta = Math.ceil(range / 2);

        if (this.currentPage - delta > this.pages - range) {
            var lowerBound = this.pages - range + 1;
            var upperBound = this.pages;
        } else {
            if (this.currentPage - delta < 0) {
                delta = this.currentPage;
            }
            var offset     = this.currentPage - delta;
            var lowerBound = offset + 1;
            var upperBound = offset + range;
        }

        for (var i=lowerBound; i <= upperBound; i++) {
            html += this.pageItemHtml(i);
        }

        return html;
    }

    previousHtml() {
        var html = '';
        var prevPage = this.currentPage - 1;

        if (prevPage > 0) {
            html = this.pageItemHtml(prevPage, '&laquo;');
        }

        return html;
    }

    nextHtml() {
        var html = '';
        var nextPage = this.currentPage + 1;

        if (nextPage <= this.pages) {
            html = this.pageItemHtml(nextPage, '&raquo;');
        }

        return html;
    }

    pageItemHtml(number, label) {
        return '<li class="page-item'+(this.currentPage === number ? ' active':'')+'">'
            + '<a class="page-link" href="#" data-page="' + number + '">' + (typeof label !== 'undefined' ? label : number) + '</a>'
            + '</li>';
    }

    render() {
        var html =
            this.previousHtml()
            + this.pagesHtml()
            + this.nextHtml()
        ;

        $(this.container).html(html);
        $(this.container + ' a').click(this.callback);
    }
}
