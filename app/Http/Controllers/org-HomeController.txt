<?php

namespace App\Http\Controllers;
use DB, Storage;
use App\Actaspleno;
use App\Actascomi;
use App\Imports\ActasplenoImport;
use App\Imports\ActascomisImport;
use Maatwebsite\Excel\Facades\Excel;
use Jenssegers\Date\Date;

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
    public function actaspleno()
    {
        // limpia la tabla registros
        DB::table('actasplenos')->truncate();
        
        // importa los datos de excel a la tabla registros
        Excel::import(new ActasplenoImport, 'actas_pleno.xls');
        
        $datos = Actaspleno::where('id', '>', 1)->get();
        
        $datos->map(function ($i) {     
            // determina si existe el pdf en disco local
            // $filepath = env('ROOT_PATH_TO_PDFS').$i->dire_web;
            // $i->pdf_existe = self::existe_pdf($filepath);
            
            // encuentra el nombre del mes
            $i->mes_nombre = Self::getMonthName($i->mes);
        });
        // dd($datos->toArray());

        return view('actaspleno', compact('datos'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function actascomis()
    {
        // limpia la tabla registros
        DB::table('actascomis')->truncate();
        
        // importa los datos de excel a la tabla registros
        Excel::import(new ActascomisImport, 'actas_comi.xls');
        
        $datos = Actascomi::where('id', '>', 1)->get();
        
        // dd($datos->toArray());
        
        $datos->map(function ($i) {      
            // determina si existe el pdf en disco local
            // $filepath = env('ROOT_PATH_TO_PDFS').$i->dire_web;
            // $i->pdf_existe = self::existe_pdf($filepath);
            
            // encuentra el nombre del mes
            $i->mes_nombre = Self::getMonthName($i->mes);
        
            // encuentra el nombre de la comision
            $i->comision = Self::getComisionName($i->comision);

        });
        // dd($datos->toArray());
        
        return view('actascomis', compact('datos'));
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

        if ($comi_pleno == 1) {
            $dato = Actascomi::where('id', $id)->first();   //se trata de un acta de comision
        } else {
            $dato = Actaspleno::where('id', $id)->first();  //se trata de un acta de pleno 
        }
        
        // modifica la direccion contenida en la columna dire_web
        $dire_web = substr($dato->dire_web, 2);     //elimina los dos primeros puntos
        $dire_web = str_replace(".PDF", ".pdf", $dire_web);    //cambia de mayuscula a minuscula la extension del pdf
        
        return redirect()->away("https://asamblea.gob.pa".$dire_web);
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