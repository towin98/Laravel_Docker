<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
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
    public function usersList(Request $request)
    {
        try {
            $draw = $request->query('draw', 1); // Es un contador propio de datatable
            $recordsTotal = User::count();

            $skip = 0;
            $take = 10;

            $orderColumn = 'id';
            $order = 'asc';

            if ($request->filled('skip')) {
                $skip = $request->skip;
            }
            if ($request->filled('take')) {
                $take = $request->take;
            }
            if ($request->filled('orderColumn')) {
                $orderColumn = $request->orderColumn;
            }
            if ($request->filled('order')) {
                $order = $request->order;
            }

            $totalRecords = User::select(['id', 'name', 'email'])
                ->where(function ($query) use ($request) {
                    if ($request->has('search') && $request->search!= '') {
                        $query->where('name', 'LIKE', '%'. $request->search. '%')
                            ->orWhere('email', 'LIKE', '%'. $request->search. '%');
                    }
                });

            $totalRecordsPage = $totalRecords->count();

            $users = $totalRecords
                ->skip($skip)
                ->take($take)
                ->orderBy($orderColumn, $order)
                ->get()
                ->map(function ($user) {
                    return [
                        'id'            => $user->id,
                        'name'          => strtoupper($user->name),
                        'email'         => $user->email,
                    ];
                });

            return response()->json([
                "draw" => intval($draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $totalRecordsPage,
                "data" => $users
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Hubo un error al listar los tecnologías.' . $e
            ], 500);
        }
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

