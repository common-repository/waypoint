(function ($) {

    $.fn.wp_media_picker = function (options) {

        var self = this,
            defaults = {
                id: '',
                url: '',
                title: 'Select Image',
                button_text: 'Select Image',
                clear_text: 'Remove Image',
                selector: '.wp-media-picker',
                media_type: ['image'],
                multiple: false,
                library: {
                    order: 'ASC',
                    orderby: 'title',
                    type: ['image']
                }
            };

        // Loop through and override defaults.
        if ('undefined' !== typeof(options)) {
            $(options).each(function (key) {
                defaults[key] = this;
            });
        }

        var $el = $(defaults.selector);

        $($el).each(function () {
            var el = this,
                id = $(el).data('id'),
                url = $(el).data('url'),
                key = $(el).data('key'),
                button_text = $(el).data('buttonText') || defaults.button_text,
                clear_text = $(el).data('clearText') || defaults.clear_text,
                media_type = $(el).data('mediaType') || defaults.media_type,
                title = $(el).data('title') || defaults.title;

            $(el).append('<div class="wp-media-picker"></div>');
            $(el).find('.wp-media-picker').append('<div class="img-preview"></div>');
            $(el).find('.wp-media-picker').append('<input type="hidden" name="' + key + '" value="' + id + '">');
            $(el).find('.wp-media-picker').append('<button type="button" class="button select">' + button_text + '</button>');
            $(el).find('.wp-media-picker').append('<button type="button" class="button clear">' + clear_text + '</button>');

            var select = $(el).find('.select'),
                clear = $(el).find('.clear'),
                preview = $(el).find('.img-preview'),
                input = $(el).find('input[name=' + key + ']');

            if ('undefined' === typeof(url) || url.length === 0) {
                clear.hide();
            } else {
                preview.html('<img src="' + url + '">');
            }


            var lib = defaults.library;
            lib.type = media_type;

            // WP Media Frame.
            var frame = new wp.media.view.MediaFrame.Select({
                title: defaults.title,
                multiple: defaults.multiple,
                library: defaults.library,
                button: {
                    text: defaults.button_text
                }
            });

            // Initialize frame state.
            frame.state();
            frame.lastState();

            frame.on('select', function () {
                var selectionCollection = frame.state().get('selection'),
                    models = selectionCollection.models,
                    image = models[0],
                    imageId = image.attributes.id,
                    imageUrl = 'undefined' === typeof( image.attributes.sizes.medium ) ?
                        image.attributes.sizes.full.url : image.attributes.sizes.medium.url;

                preview.html('<img src="' + imageUrl + '">');
                input.val(imageId);
                input.attr('value', imageId);
                clear.show();
            });

            select.on('click', function () {
                frame.open();
            });

            clear.on('click', function () {
                preview.html('');
                input.val('');
                input.attr('value', '');
                clear.hide();
            });
        });
    };

    $(document).wp_media_picker();

})(jQuery);