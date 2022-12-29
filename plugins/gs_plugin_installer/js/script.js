$(document).ready(function () {

    // Confirm when uninstalling plugins
    $("#uninstall").click(function() { return confirm(i18n(['CONFIRM_UNINSTALL_ALL'])); });
    $("a.cancel").click(function() { return confirm(i18n(['CONFIRM_UNINSTALL'])); });

    // Initialize DataTable
    $('#plugin_table').DataTable({
        "columnDefs": [
            {
                "targets": [1],
                "visible": true,
                "searchable": false // exclude plugin description column from search
            },
            {
                "targets": [2],
                "visible": true,
                "searchable": false // exclude "install" column from search
            },
            {
                "targets": [3],
                "visible": true,
                "searchable": false // checkbox
            }
        ]
    });
});
