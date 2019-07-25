jQuery(function($){
    $('.array-field-group').on('click', '.array-field-remove', function (e) {
        $(this).closest('.array-field-wrapper').remove();
    });
    $('.array-field-group').on('click', '.array-field-add', function (e) {
        var $this = $(this);
        var $wrapper = $this.closest('.array-field-wrapper');
        var $newWrapper = $wrapper.clone();

        var index = $wrapper.data('index');
        var newIndex = index + 1;

        var id = $wrapper.data('id');
        var escapedId = id.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '-' + index;
        var reqexId = new RegExp('^' + escapedId);
        var newId = id + '-' + newIndex;

        var name = $wrapper.data('name');
        var escapedName = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\[' + index + '\\]';
        var reqexName = new RegExp('^' + escapedName);
        var newName = name + '[' + newIndex + ']';

        $newWrapper.attr('data-index', newIndex);
        $newWrapper.find('[name^="' + escapedName + '"]').each(function (e) {
            $(this).attr('name', $(this).attr('name').replace(reqexName, newName));
        });
        $newWrapper.find('[id^="' + escapedId + '"]').each(function (e) {
            $(this).attr('id', $(this).attr('id').replace(reqexId, newId));
        });

        $this.removeClass('btn-success array-field-add').addClass('btn-danger array-field-remove');
        $this.html('&ndash;');

        $wrapper.after($newWrapper);

        var init = $wrapper.data('init');
        if (init && window[init]) {
            window[init]($newWrapper, newId, newName, newIndex);
        }
    });
});