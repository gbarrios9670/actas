<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <style>
      tfoot input {
          width: 100%;
          padding: 3px;
          box-sizing: border-box;
    }
    </style>

    <title>Actas de Comisiones</title>
  </head>
  <body>
    <div class='container'>
      <div class="alert alert-success mt-2" role="alert">
            <strong>ACTAS DE COMISIONES</strong>
      </div>
      <table id="example" class="display table mt-1 compact">
        <thead class="thead-dark">
            <tr>
                <th col width="100px">Año</th>
                <th col width="100px">Mes</th>
                <th>Comisión</th>
                <th width="25px">Accion</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Año</th>
                <th>Mes</th>
                <th>Comisión</th>
                <th hidden></th>
            </tr>
        </tfoot>
      </table>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
        
          // Setup - add a text input to each footer cell
          $('#example tfoot th').each( function () {
              var title = $(this).text();
              $(this).html( '<input type="text" placeholder="Filtrar por '+title+'" />' );
          } );
      
          // DataTable
          var table = $('#example').DataTable({
                "serverSide": true,
                "order": [[ 0, "desc" ]],
                "ajax": "{{ url('api/actas_comisiones') }}",
                "columns": [
                    {data: 'anno'},
                    {data: 'mes'},
                    {data: 'comision'},
                    {data: 'btn', orderable: false, searchable: false}
                ],
                "language": {
                    "decimal":        "",
                    "emptyTable":     "No hay datos disponibles para esta tabla",
                    "info":           "&nbsp;&nbsp; Total de _MAX_ registros",
                    "infoEmpty":      "",
                    "infoFiltered":   "",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search":         "Buscar:",
                    "zeroRecords":    "No se encontró ningun registro con ese filtro",
                    "paginate": {
                        "first":      "Primer",
                        "last":       "Último",
                        "next":       "Próximo",
                        "previous":   "Anterior"
                    },
                    "aria": {
                        "sortAscending":  ": active para ordenar ascendentemente",
                        "sortDescending": ": active para ordenar descendentemente"
                    }
                },
              
              initComplete: function () {
                  // Apply the search
                  this.api().columns().every( function () {
                      var that = this;
      
                      $( 'input', this.footer() ).on( 'keyup change clear', function () {
                          if ( that.search() !== this.value ) {
                              that
                                  .search( this.value )
                                  .draw();
                          }
                      } );
                  } );
              }
          });
      } );
    </script>
  </body>
</html>