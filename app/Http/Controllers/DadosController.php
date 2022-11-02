<?php

namespace App\Http\Controllers;

use App\Models\Dados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DadosController extends Controller
{   
    protected Dados $dados;

    public function __construct(Dados $dados)
    {
        $this->dados = $dados;
    }
    public function index()
    {
        return response()->json(['data' =>  $this->dados->all(['id', 'name', 'password', 'file_path'])], 200);
    }

    public function store(Request $request)
    {   
        $request->validate($this->dados->rules());
        if ($request->has('file')) {
            $path = $request->file('file')->store('dados', 'local');
            $request->merge(['file_path' => $path]);
        } 
        $this->dados->create($request->all('name', 'password', 'file_path'));
        return response()->json(['message' => 'Data successfully stored.'], 200);
    }

    public function show(Int $id)
    {
        if ($dado = $this->dados->find($id)) {
            return response()->json(['data' => $dado], 200);
        }
        return response()->json(['message' => 'Data not found.'], 404);
    }

    public function update(Request $request, Int $id)
    {   
        if ($dado = $this->dados->find($id)) {
            $new_validation_rule = [];
            foreach ($request->except(['_method']) as $key => $value) {
                $new_validation_rule[$key] = $this->dados->rules()[$key];
            }
            $request->validate($new_validation_rule);
            if ($request->has('file')) {
                if ($dado->file_path) {
                    Storage::disk('local')->delete($dado->file_path);
                }
                $path = $request->file('file')->store('dados', 'local');
                $request->merge(['file_path' => $path]);
            } 
            $dado->update($request->all());
            return response()->json(['message' => 'Data successfully updated.'], 200);
        }
        return response()->json(['message' => 'Data not found.'], 404);
    }

    public function destroy(Int $id)
    {
        if ($dado = $this->dados->find($id)) {
            if ($dado->file_path) {
                Storage::disk('local')->delete($dado->file_path);
            }
            $dado->delete();
            return response()->json(['message' => 'Data successfully removed.'], 200);
        }
        return response()->json(['message' => 'Data not found.'], 404);
    }

    public function download_file(Int $id)
    {
        if ($dado = $this->dados->find($id)) {
            if ($dado->file_path) {
                return Storage::download($dado->file_path);
            }
        }
        return response()->json(['message' => 'File not found.'], 404);
    }
}
