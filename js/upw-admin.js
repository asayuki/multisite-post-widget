jQuery(document).ready(function($) {

    
    $('#widgets-right').on('click', '.upw-tab-item', function (event)
    {
     
        event.preventDefault();
        var tabwrapper = $(this).closest('.upw-tabs');
        tabwrapper.find('.upw-tab-item').removeClass('active');
        $(this).addClass('active');
        tabwrapper.find('.upw-tab').addClass('upw-hide');
        tabwrapper.find('.' + $(this).data('toggle')).removeClass('upw-hide');
      });

});


jQuery(document).ready(function ($) {
    window.upwAdmin = function () {
        
        $('.upw-tab-item').on('click', function (event) {
            
            event.preventDefault();
            var tabwrapper = $(this).closest('form');
            if (!tabwrapper.length)
                tabwrapper = $(this).closest('.panel-dialog');
            tabwrapper.find('.upw-tab-item').removeClass('active');
            $(this).addClass('active');
            tabwrapper.find('.upw-tab').addClass('upw-hide');
            var toggleclass = $(this).data('toggle');
            tabwrapper.find('.' + toggleclass).removeClass('upw-hide');
        });
    }
});
