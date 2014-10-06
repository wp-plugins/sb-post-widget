(function($){
    var body = $('body');

    (function(){
        body.delegate('.post-type select', 'change', function(){
            var that = $(this),
                listCats = that.parent().parent().find('p.post-cat');
            if('category' == that.val()) {
                listCats.delay(10).fadeIn();
            } else {
                listCats.delay(10).fadeOut();
            }
        });
    })();

    (function(){
        body.delegate('.post-cat option', 'click', function(){
            var that = $(this),
                taxonomy = that.attr('data-taxonomy'),
                inputTaxonomy = that.closest('div.sb-post-widget').find('input.taxonomy');
            inputTaxonomy.val(taxonomy);
        });
    })();

    (function(){
        function onlyThumbnailCheck(selector, value) {
            var postWidget = selector.parent().parent();
            if(true == value) {
                postWidget.find('p.show-excerpt').fadeOut();
                postWidget.find('p.excerpt-length').fadeOut();
                postWidget.find('p.title-length').fadeOut();
                postWidget.find('fieldset.post-info').fadeOut();
            } else {
                postWidget.find('p.show-excerpt').fadeIn();
                if(postWidget.find('p.show-excerpt input').is(':checked')) {
                    postWidget.find('p.excerpt-length').fadeIn();
                }
                postWidget.find('p.title-length').fadeIn();
                postWidget.find('fieldset.post-info').fadeIn();
            }
        }
        body.delegate('input.sb-checkbox', 'click', function(){
            var that = $(this),
                parentClass = that.parent().attr('class');

            switch(parentClass) {
                case 'only-thumbnail':
                    if(that.is(':checked')) {
                        onlyThumbnailCheck(that, true);
                    } else {
                        onlyThumbnailCheck(that, false);
                    }
                    break;
                case 'show-excerpt':
                    if(that.is(':checked')) {
                        that.parent().parent().find('p.excerpt-length').fadeIn();
                    } else {
                        that.parent().parent().find('p.excerpt-length').fadeOut();
                    }
                    break;
            }
        });
    })();

})(jQuery);