(function($){
    var body = $('body');

    (function(){
        $('.post-type select').live('change', function(e){
            var that = $(this),
                list_cats = that.closest('div.sb-widget').find('p.post-cat');
            if('category' == that.val()) {
                list_cats.delay(10).fadeIn();
            } else {
                list_cats.delay(10).fadeOut();
            }
        });
    })();

    (function(){
        $('.post-cat option').live('click', function(e){
            var that = $(this),
                taxonomy = that.attr('data-taxonomy'),
                input_taxonomy = that.closest('div.sb-post-widget').find('input.taxonomy');
            input_taxonomy.val(taxonomy);
        });
    })();

    (function(){
        function sb_post_widget_switch_only_thumbnail(selector, value) {
            var widget_container = selector.closest('div.sb-widget');
            if(value) {
                widget_container.find('p.show-excerpt').fadeOut();
                widget_container.find('p.excerpt-length').fadeOut();
                widget_container.find('p.title-length').fadeOut();
                widget_container.find('fieldset.post-info').fadeOut();
            } else {
                widget_container.find('p.show-excerpt').fadeIn();
                if(widget_container.find('p.show-excerpt input').is(':checked')) {
                    widget_container.find('p.excerpt-length').fadeIn();
                }
                widget_container.find('p.title-length').fadeIn();
                widget_container.find('fieldset.post-info').fadeIn();
            }
        }
        $('.only-thumbnail input').live('click', function(e){
            var that = $(this);
            sb_post_widget_switch_only_thumbnail(that, that.is(':checked'));
        });
        $('.show-excerpt input').live('click', function(e){
            var that = $(this),
                widget_container = that.closest('div.sb-widget'),
                excerpt_length = widget_container.find('p.excerpt-length');
            if(that.is(':checked')) {
                excerpt_length.fadeIn();
            } else {
                excerpt_length.fadeOut();
            }
        })
    })();

})(jQuery);