$(document).ready(function () {
    let id, fila;

    let tablaAnuncios = $('#tablaAnuncios').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": Routing.generate('anuncio_datatables'),
            "type": "POST"
        },
        "columns": [
            {data: "id"},
            {data: "video"},
            {data: "duracion"},
            {data: "activo"},
            {
                "defaultContent":
                    "<button class='btn btn-outline-info btnVer'><i class=\"fa fa-eye\"></i></button>" +
                    "<button class='btn btn-outline-warning mx-1 btnEditar'><i class='fa fa-edit'></i></button>" +
                    "<button class='btn btn-outline-danger btnBorrar'><i class='fa fa-trash'></i></button>"
            }
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
        },
        rowCallback: function (row, data) {
            if (data.activo) {
                $("td:eq(3)", row).html('<i class="fas fa-check text-success"></i>')
            } else {
                $("td:eq(3)", row).html('<i class="fas fa-times text-danger"></i>')
            }
        }
    });

    // #############################################################################################

    $(document).on("click", ".btnBorrar", function () {
        fila = $(this);
        id = parseInt($(this).closest('tr').find('td:eq(0)').text());

        Swal.fire({
            title: 'Borrar anuncio?',
            text: "El borrado sera definitivo",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Borrar'
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                        title: 'Confirmación de borrado',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    }
                )
                $.ajax({
                    type: "POST",
                    url: Routing.generate('anuncio_delete',{ id: id }),
                success: function () {
                    tablaAnuncios.row(fila.parents('tr')).remove().draw();
                }
            });
            }
        })
    });

    $(document).on("click", ".btnVer", function () {
        fila = $(this);
        let id = parseInt($(this).closest('tr').find('td:eq(0)').text());
        $.ajax({
            type: "GET",
            success: function () {
                 $(location).attr('href',Routing.generate('anuncio_show',{ id: id }));
            }
        });
    });

    $(document).on("click", ".btnEditar", function () {
        fila = $(this);
        let id = parseInt($(this).closest('tr').find('td:eq(0)').text());
        $.ajax({
                type: "GET",
                success: function () {
                    $(location).attr('href',Routing.generate('anuncio_edit',{ id: id }));
                }
            })
    });
});