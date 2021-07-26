<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Address;
use App\Models\Rol;
use Illuminate\Http\Request;
use DB;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $personas = DB::table('person as p')
        ->select('p.*',DB::raw("concat(add_street,' ',add_city,' ',add_state,' ',add_postal_code,' ',add_country) as address"),'rol_nombre')
        ->leftjoin('address as a','a.per_id','p.per_id')
        ->join('roles as r','r.rol_id','p.per_rol')
        ->get();
        //return Persona::All();
        return response()->json($personas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tbl_person = new Persona();
        $tbl_person->per_name = $request->per_name;
        $tbl_person->per_phone = $request->per_phone;
        $tbl_person->per_email = $request->per_email;
        $tbl_person->per_rol = $request->per_rol;
        $tbl_person->save();

        $tbl_address = new Address;
        $tbl_address->add_street = $request->add_street;
        $tbl_address->add_city = $request->add_city;
        $tbl_address->add_state = $request->add_state;
        $tbl_address->add_postal_code = $request->add_postal_code;
        $tbl_address->add_country = $request->add_country;
        $tbl_address->per_id = $tbl_person->per_id;
        $tbl_address->save();
        return response()->json($tbl_person,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function show(string $persona)
    {
        //$resultado = Persona::find($persona->per_id);
        //return response()->json($resultado,200);
        $resultado = DB::table('person as p')
        ->select(
            'p.*',
            'a.*',
            DB::raw("concat(add_street,' ',add_city,' ',add_state,' ',add_postal_code,' ',add_country) as address"),
            'r.*'
        )
        ->join('address as a','a.per_id','p.per_id')
        ->join('roles as r','r.rol_id','p.per_rol')
        ->where('p.per_id', $persona)
        ->get();
        return response()->json($resultado,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Persona $persona)
    {   
        //$persona->update($request->all());
        $persona->per_name = $request->per_name;
        $persona->per_phone = $request->per_phone;
        $persona->per_email = $request->per_email;
        $persona->per_rol = $request->per_rol;
        $persona->save();

        $tbl_address = Address::where('per_id', $persona->per_id)->first();
        $tbl_address->add_street = $request->add_street;
        $tbl_address->add_city = $request->add_city;
        $tbl_address->add_state = $request->add_state;
        $tbl_address->add_postal_code = $request->add_postal_code;
        $tbl_address->add_country = $request->add_country;
        $tbl_address->per_id = $persona->per_id;
        $tbl_address->save();
        
        return response()->json($persona, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $persona)
    {
        $adress = Address::where('per_id',$persona)->delete();
        $persona = Persona::where('per_id',$persona)->delete();
        return response()->json(null, 204);
    }

    public function getRol(){
        return Rol::all();
    }
}
