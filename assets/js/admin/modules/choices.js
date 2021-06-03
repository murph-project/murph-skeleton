const Choices = require('choices.js');
const $ = require('jquery');

module.exports = function() {
    $('*[data-jschoice]').each(function(key, item) {
        new Choices(item);
    });
}
