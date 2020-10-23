<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Actas pleno</title>

    <!-- Bootstrap, datatables CSS -->    
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.min.css') }}">

    <style>
      tfoot input {
          width: 100%;
          padding: 3px;
          box-sizing: border-box;
        }
        
        .bottom-left {
        position: absolute;
        bottom: -20px;
        left: 16px;
        }
    </style>
  </head>

  <body>
    <div class="container-fluid">
    <div class="mb-1" style="background-image: url('/images/background-menu.png');">
        <div class='container'>
            <div class="row">
                <div class="col">
                    <div class="bottom-left mb-1"><strong><p class="text-white">ACTAS DE PLENO</p></strong></div>
                </div>
                <div class="col">
                    <img src="{{url('/images/logoasamblea.png')}}" class="float-right my-2 mr-1" alt="Logo asamblea" height="70px">
                </div>
              </div>
            </div>
        </div>
    </div>
    </div>

    <div class='container'>
      <table id="example" class="table data-table hover compact order-column mt-1">
        <thead class="thead-dark">
            <tr>
                <th col width="100px">Año</th>
                <th col width="100px">Mes</th>
                <th>Acta</th>
                <th col width="25px">Acción</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Año</th>
                <th>Mes</th>
                <th>Acta</th>
                <th hidden></th>
            </tr>
        </tfoot>
      </table>
    </div>

    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
        
          // Setup - add a text input to each footer cell
          $('#example tfoot th').each( function () {
              var title = $(this).text();
              $(this).html( '<input type="text" placeholder="Filtrar por '+title+'" />' );
          } );
      
          // DataTable
          var table = $('#example').DataTable({
                "responsive": true,
                "serverSide": true,
                "order": [[ 0, "desc" ]],
                "ajax": "{{ url('api/actas_pleno') }}",
                "columns": [
                    {data: 'anno'},
                    {data: 'mes'},
                    {data: 'acta'},
                    {data: 'btn', orderable: false, searchable: false}
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'dt-center'
                    },
                    {
                        targets: -1,
                        className: 'dt-center'
                    }
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