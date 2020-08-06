<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Produto;


class ProdutosController extends Controller
{
    private function getValidatedData($fields='all'){

        $validationRules = [
            'nome' => 'unique:produtos|required',
            'preco' => 'required|integer|gt:0',
        ];

        if ($fields == 'all'){
            return request()->validate($validationRules);
        } elseif ($fields == 'requested' || $fields == 'request' || $fields == 'inRequest'){
            return $this->getValidatedData($this->getRequestFieldsNames());
        } else{
            $data = [];
            foreach ($validationRules as $key => $value){
                if (in_array($key, $fields)){
                    $data[$key] = $value;
                }
            }
            return request()->validate($data);
        }
        
    }
    
    private function getRequestFieldsNames(){
        $fields = [];
        foreach(request()->all() as $fieldName => $value){
            array_push($fields, $fieldName);
        }
        return $fields;
    }
    
    public function index(){
        return Produto::all();
    }

    public function store(){
       $newObj = Produto::create($this->getValidatedData()); 
       return $newObj;
    }

    public function show(Produto $produto){
        return $produto;
    }

    public function update(Produto $produto){
        $produto->update($this->getValidatedData('requested'));
        return $produto;
    }

    public function destroy(Produto $produto){
        $produto->delete();
    }
}
