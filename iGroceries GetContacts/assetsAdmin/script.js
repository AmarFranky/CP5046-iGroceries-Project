$(document).ready(function() {
    $('#example').DataTable( {
        "scrollX": true,
         "pagingType": "full_numbers",
        dom: 'Bfrtip',
        buttons: [
            'colvis','copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
} );