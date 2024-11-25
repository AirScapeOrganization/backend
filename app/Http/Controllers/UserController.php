<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::all();
        $data = [
            'users'=>$usuarios,
            'status'=> 200,
        ];

        return response()->json($data, 200);
   
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'remember_token' => 'required'
           
        ]);

        if ($validacion->fails()) {
            return response()->json([
                'mensaje'=>'Error en la validacion de datos',
                'error'=> $validacion->errors(),
                'status'=> 400
            ]);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'remember_token' => $request->remember_token
        ]);
        

        if (!$user) {
            return response()->json([
                'mensaje'=> 'No se pudo crear el usuario',
                'error'=>500
            ]);
        }

        return response()->json([
            'message'=> 'Se pudo crear correctamente',
            'status'=>200
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        
        $validacion = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'remember_token' => 'required'
        ]);
    
        if ($validacion->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $validacion->errors(),
                'status' => 400
            ]);
        }
    
      
        $user = User::find($id);
    
        if (!$user) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => 404
            ]);
        }
    
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->remember_token = $request->remember_token;
    
        if (!$user->save()) {
            return response()->json([
                'mensaje' => 'No se pudo actualizar el usuario',
                'status' => 500
            ]);
        }
    
        return response()->json([
            'mensaje' => 'Usuario actualizado correctamente',
            'status' => 200
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       
        $user = User::find($id);
    
        if (!$user) {
            return response()->json([
                'mensaje' => 'Usuario no encontrado',
                'status' => 404
            ]);
        }
    
        if (!$user->delete()) {
            return response()->json([
                'mensaje' => 'No se pudo eliminar el usuario',
                'status' => 500
            ]);
        }
    
        return response()->json([
            'mensaje' => 'Usuario eliminado correctamente',
            'status' => 200
        ]);
    }
    
}
