<?php
i18n_gallery_register('maps', 'Google Maps', 
  'Displays geolocated images in a Google Map.',
  'i18n_gallery_maps_edit', 'i18n_gallery_maps_header', 'i18n_gallery_maps_content');

function i18n_gallery_maps_edit() {
?>
  <p>
    <a href="#" class="setloc"><?php i18n('i18n_gallery/SET_LOCATIONS'); ?></a>
  </p>
  <script type="text/javascript">
    var current = 0;
    var mapsWindow = null;
    var select
    function openMapsWindow() {
      mapsWindow = window.open('../plugins/i18n_gallery/browser/map.html', 'maps', 
          'width=600,height=400,left=100,top=100,scrollbars=no,status=0,toolbar=0,location=0');
    }
    function showLocation() {
      if (!mapsWindow || mapsWindow.closed) openMapsWindow();
      
    }
    $(function() {
      $('#post-type').click(function(e) {
        var val = $(e.target).val();
        if (val == 'maps') $('#editgallery').addClass('geo'); else $('#editgallery').removeClass('geo');
      });
      $('.type_maps a.setloc').click(function(e) {
        openMapsWindow();
        return false;
      });
      $('#editgallery span.geo').live('click', showLocation);
    });
  </script>
<?php
}

function i18n_gallery_maps_header() {

}

function i18n_gallery_maps_content() {

}
