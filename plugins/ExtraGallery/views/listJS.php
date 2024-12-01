$( document ).ready(function() {
    var $maincontent = $('#maincontent'),
        $list = $('#list'),
        ajaxUrl = '<?php echo EG_AJAXURL; ?>?nonce=<?php echo get_nonce("ajax", "ExtraGallery"); ?>';
       
    $list.on('click', 'button.delete', function(){
        var $button = $(this),
            $tr = $button.closest('tr'),
            name = $tr.find('a.name').text(),
            agree = confirm('<?php i18n(EG_ID.'/LIST_JS_DELETE') ?>' );
            
           
            
        if (agree){
            $button.css('visibility', 'hidden').attr('disabled', 'disabled');
            
            $.ajax({
                url: ajaxUrl,
                data: {
                    mode        : 'gallery-delete',
                    instance    : '<?php echo $this->instanceNum; ?>',
                    name        : name                 
                },
                dataType: 'json'
            })
            .done(function(data){
                if (data == 1)
                    $tr.remove();
                else
                    alert('Cannot delete!');
            })
            .fail(function(){
                alert('Ajax call failed!');
            });
        }
    });
       

});