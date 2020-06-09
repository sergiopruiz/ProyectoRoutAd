$(document).ready(function () {
    let $tabletId = window.location.pathname;
    $('#tablaServicios').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": Routing.generate('servicio_datatables'),
            "type": "POST",
            "data": $tabletId
        },
        "columns": [
            {data: "id"},
            {data: "fecha"},
            {data: "latitudOrigen"},
            {data: "longitudOrigen"},
            {data: "latitudDestino"},
            {data: "longitudDestino"},
            {data: "duracionRuta"},
        ],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguente",
                "sPrevious": "Anterior"
            },
            "sProcessing": "Procesando..",
        }
    });
});