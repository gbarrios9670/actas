<?php

namespace App\Http\Controllers;
use DB, Storage;
use App\Actascomi;
use App\Actaspleno;
use Illuminate\Http\File;
use Jenssegers\Date\Date;
use App\Imports\ActascomisImport;
use App\Imports\ActasplenoImport;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function import_txt()
    {
        // limpia la tabla registros
        DB::table('actasplenos')->truncate();
        DB::table('actascomis')->truncate();

        /********************************************************
         * Carga los datos de actas del pleno desde archivo txt
        ********************************************************/
        $input = file(storage_path('ACT_PLEN.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $filedata= array();
        foreach ($input as $key => $line) {
            // $filedata[] = explode("\t",$line);
            $filedata[] = str_replace("|", "", explode("\t",$line)) ;
            // var_dump($filedata[$key][0]);
            
            $pleno = new Actaspleno;

            $pleno->anno = $filedata[$key][0];
            $pleno->mes = Self::getMonthName($filedata[$key][1]);
            $pleno->acta = $filedata[$key][2];
            $pleno->dire_web = $filedata[$key][3];

            $pleno->save();
        }
    
        /************************************************************
         * Carga los datos de actas de comisiones desde archivo txt
        ************************************************************/
        $input = file(storage_path('ACT_COMI.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $filedata= array();
        foreach ($input as $key => $line) {
            // $filedata[] = explode("\t",$line);
            $filedata[$key] = str_replace("|", "", explode("\t",$line)) ;
            // dd($filedata[$key]);
            
            $comis = new Actascomi;

            $comis->anno = $filedata[$key][0];
            $comis->mes = Self::getMonthName($filedata[$key][1]);
            $comis->dia = Self::getDay($filedata[$key][2]);
            $comis->comision = Self::getComisionName($filedata[$key][3]);
            $comis->dire_web = $filedata[$key][4];
            
            $comis->save();
        }

        return "Datos cargados con éxito...";     
    }

    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function cargar_datos()
    {

        /********************************************************
         * Carga los datos de actas del pleno
        ********************************************************/

        // limpia la tabla registros
        DB::table('actasplenos')->truncate();
        
        // importa los datos de excel a la tabla registros
        Excel::import(new ActasplenoImport, 'actas_pleno.xls');
        
        $data = Actaspleno::where('id', '>',1 )->get();
        // dd($data->toArray());
        
        // actualiza del campo mes numerico por en nombre del mes
        foreach ($data as $i) {
            $i->mes = Self::getMonthName($i->mes);
            $i->save();
        }
        
        // elimina el primer registro de encabezados
        $row1 = Actaspleno::find(1);
        $row1->delete();
        
        // dd($data->toArray());
    
        /********************************************************
         * Carga los datos de actas del pleno
        ********************************************************/
        // limpia la tabla registros
        DB::table('actascomis')->truncate();
        
        // importa los datos de excel a la tabla registros
        Excel::import(new ActascomisImport, 'actas_comi.xls');
        
        $data = Actascomi::where('id', '>',1 )->get();
        // dd($data->toArray());
        
        // actualiza del campo mes numerico por en nombre del mes
        foreach ($data as $i) {
            $i->mes = Self::getMonthName($i->mes);
            $i->comision = Self::getComisionName($i->comision);
            $i->save();
        }
        
        // elimina el primer registro de encabezados
        $row1 = Actascomi::find(1);
        $row1->delete();
        
        return "Datos cargados con éxito...";     
    }
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function actaspleno()
    {
        $data = Actaspleno::query();
        // dd($data;
        
        return datatables()->eloquent($data)
            ->addColumn('btn', 'accion_pleno')
            ->rawColumns(['btn'])
            ->toJson();
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function actascomis()
    {
        $data = Actascomi::query();
        // dd($data;
        
        return datatables()->eloquent($data)
            ->addColumn('btn', 'accion_comi')
            ->rawColumns(['btn'])
            ->toJson();
    }


    /*************************************************************************************
     * en una nueva pestaña del navegador, despliega pdf almacenados en el disco local
     ************************************************************************************/
    public function ver_pdf($path)
    {
        $filepath = env('ROOT_PATH_TO_PDFS').$path;
        // dd($filepath);

        $file = Storage::disk('public')->get("$filepath");
            
        $header = [
            'Content-Type' => 'application/pdf',
        ];
        return response($file, 200, $header);
    }


    /***************************************************************************************
     * en una nueva pestaña del navegador, despliega pdf atraves del dominio asamblea.gob.pa 
     ***************************************************************************************/
    public function showAsambleaPdf($id, $comi_pleno)
    {
        // dd($id, $comi_pleno);

        if ($comi_pleno == 1) {
            $dato = Actascomi::where('id', $id)->first();   //se trata de un acta de comision
        } else {
            $dato = Actaspleno::where('id', $id)->first();  //se trata de un acta de pleno 
        }
        
        // modifica la direccion contenida en la columna dire_web
        $dire_web = substr($dato->dire_web, 2);     //elimina los dos primeros puntos
        $dire_web = str_replace(".PDF", ".pdf", $dire_web);    //cambia de mayuscula a minuscula la extension del pdf
        
        return redirect()->away("https://www.asamblea.gob.pa".$dire_web);
    }


    /*************************************************************************************
     * determina si el pdf existe en el servidor local
     ************************************************************************************/
    public function existe_pdf($filepath)
    {

        $existe = Storage::disk('public')->exists("$filepath");

        if ($existe) {
            return true;
        }
        return false;
    }


   /*************************************************************************************
     * determina el nombre del mes a partir de un numero entero
     ************************************************************************************/
    public function getMonthName($numero)
    {
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        return $meses[intval($numero) - 1];
    }


   /*************************************************************************************
     * determina el nombre del mes a partir de un numero entero
     ************************************************************************************/
    public function getDay($pdfName)
    {
        // |1993_09_13_A_COMI_JUSTICIA.PDF|
        
        $dia = "$pdfName[8]$pdfName[9]";
        // $dia = intval($dia);
        return $dia;
    }


   /*************************************************************************************
     * determina el nombre del mes a partir de un numero entero
     ************************************************************************************/
    public function getComisionName($numero)
    {
        // dd($numero);
        $comision = array(
            "Credenciales, Justicia Interior, Reglamento y Asuntos Judiciales",
            "Revisión y Corrección de Estilo",
            "Gobierno, Justicia y Asuntos Constitucionales",
            "Presupuesto",
            "Hacienda Pública, Planificación y Política Económica",
            "Comercio, Industrias y Asuntos Económicos",
            "Obras Públicas",
            "Educación, Cultura y Deportes",
            "Asuntos del Canal",
            "Trabajo y Bienestar Social",
            "Comunicación y Transporte",
            "Salud Pública y Seguridad Social",
            "Relaciones Exteriores",
            "Asuntos Agropecuarios",
            "Vivienda",
            "Derechos Humanos",
            "Asuntos IndÌgenas",
            "Población, Ambiente y Desarrollo",
            "Asuntos de la Mujer, Derecho del Niño, la Juventud y la Familia",
            "Prevención, Control y Erradicación de la Droga, el Narcotráfico y el Lavado de Dinero",
            "Etica y Honor Parlamentario",
            "Asuntos Municipales",
            "Economía y Finanzas",
            "Credenciales, Reglamentos, ética Parlamentaria y Asuntos Judiciales",
            "Comercio y Asuntos Económicos",
            "De la Mujer, La Niñez, La Juventud y La Familia",
            "Infraestructura Pública y Asuntos del Canal",
            "Trabajo, Salud y Desarrollo Social"
        );
        return $comision[intval($numero) - 1];
    }

}