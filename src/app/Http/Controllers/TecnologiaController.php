<?php

namespace App\Http\Controllers;

use App\Models\Tecnologia;
use Exception;
use Illuminate\Http\Request;

class TecnologiaController extends Controller
{
    public function index(){
        $tecnologias = Tecnologia::select([
                'id',
                'nombre',
                'descripcion'
            ])
            ->orderBy('id', 'desc')
            ->get();
        return view('tecnologias.index', compact('tecnologias'));
    }

    public function create(){
        return view('tecnologias.show');
    }

    public function store(Request $request){
        try {
            Tecnologia::create($request->all());
            return redirect()->route('tecnologias.index')->with('success', 'Se creo con exito');
        } catch (Exception $e) {
            return view('tecnologias.show', ['error' => 'Hubo un error al crear.']);
        }
    }

    public function show($id){
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            return view('tecnologias.show', compact('tecnologia'));
        } catch (Exception $e) {
            return redirect()->route('tecnologias.index', $id)->with('error', 'Hubo un error al actualizar la tecnología');
        }
    }

    public function update(Request $request, $id){
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            $tecnologia->update($request->all());
            return redirect()->route('tecnologias.show', $id)->with('success', 'La tecnología ha sido actualizada correctamente');
        } catch (Exception $e) {
            return redirect()->route('tecnologias.show', $id)->with('error', 'Hubo un error al actualizar la tecnología');
        }
    }

    public function destroy($id){
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            $tecnologia->delete();
            return redirect()->route('tecnologias.index')->with('success', 'La tecnología ha sido eliminada correctamente');
        } catch (Exception $e) {
            return redirect()->route('tecnologias.index')->with('error', 'Hubo un error al eliminar la tecnología');
        }
    }
}
