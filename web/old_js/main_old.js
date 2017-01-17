$(document).ready(function (){
    initSortableCards();
    initAutoComplete();
});

function initSortableCards () {
    var card = '.tile',
        column = '.js-sortable',
        columnTitle = '.stage-header',
        placeholderClass = 'tile-placeholder',
        activeClass = 'tile--active';

    $(column).sortable({
        items: card,
        cancel: columnTitle,
        connectWith: column,
        placeholder: placeholderClass,

        zIndex: 2,
        revert: 150,
        forcePlaceholderSize: true,

        start: function(event, ui) {
            ui.item.addClass(activeClass);
        },
        stop: function(event, ui) {
            window.setTimeout(function(){
                ui.item.removeClass(activeClass);
            }, 100);
        }
    });
}

function initAutoComplete () {
    var searchField = '#search',
        highlightClass = 'highlight--bold',
        stubNames = [
            'Nick Write', 'Doyle Oliver', 'Jake Jensen', 'Tonya Austin',
            'Noel Lambert', 'Nick Hernandez', 'Edmund Walsh', 'Wilbert Taylor',
            'Josephine Grant', 'Jody Mcguire'
        ];

    $(searchField)
        .autocomplete({
            source: stubNames
        })
        .data("uiAutocomplete")._renderItem = highlightMatched;


    function highlightMatched (ul, item) {
        var matcher = new RegExp( "(" + this.term + ")", "gi" ),
            template = "<span class='" + highlightClass + "'>$1</span>",
            label = item.label.replace( matcher, template );

        return $( "<li>" ).append( "<a>" + label + "</a>" ).appendTo( ul );
    }
}