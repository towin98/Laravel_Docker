<?php

namespace App\Http\Controllers;

use App\Models\Tecnologia;
use Exception;
use App\Models\User;
use Freshbitsweb\Laratables\Laratables;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.list');
    }

    /**
     * Método que consulta usuario por parametros de la paginación.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Si ocurre un error durante la consulta.
     */
    public function usersList()
    {
        return Laratables::recordsOf(User::class);
    }

    /**
     * Asigna tecnologias a un usuario.
     *
     * @param Request $request
     * @param [type] $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function asignarTecnologia(Request $request, $user){
        try {
            $user = User::find($user);
            if($user){

                if($request->filled('tecnologias')){
                    $user->tecnologias()->sync($request->tecnologias);
                }
                return response()->json([
                        'message' => 'Tecnologías asociadas correctamente.'
                    ], 200);
            }else{
                return response()->json([
                    'error' => 'No se encontró el usuario.'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Hubo un error al asociar las tecnologías.' . $e
            ], 500);
        }
    }

    /**
     * Obtengo las tecnologias asignadas del usuario
     *
     * @param [type] $user id usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function tecnologiasUser($user)
    {
        $tecnologiasUser = User::with('tecnologias:id,nombre')->select('users.id', 'users.name')->findOrFail($user);
        return response()->json([
            "data" => $tecnologiasUser
        ]);
    }

    public function tusTecnologias(){
        $tusTecnologias = auth()->user()->tecnologias;
        return view('users.tusTecnologias', compact('tusTecnologias'));
    }
}

