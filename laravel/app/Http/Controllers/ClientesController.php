<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cliente;


class ClientesController extends Controller
{
    private function getValidatedData($fields='all'){
        $validationRules = [
            'nome' => 'required',
            'email' => 'unique:clientes|required|email',
            'telefone' => 'unique:clientes|required|digits_between:10,11',
            'endereco' => 'required',
        ];

        if ($fields == 'all'){
            return request()->validate($validationRules);
        } elseif ($fields == 'requested' || $fields == 'request' || $fields == 'inRequest'){
            return $this->getValidatedData($this->getRequestFieldsNames());
        } else{
            $data = [];
            foreach ($validationRules as $key => $value){
                if (in_array($key, $fields)){
                    array_push([ $key => $value]);
                    'make sure'
                }
            }
        }
        
    }
    
    private function getRequestFieldsNames(){
        $fields = [];
        foreach(request()->all() as $fieldName => $value){
            array_push($fields, $fieldName);
        }
        return $fields;
    }
    
    public function store(){
       $newObj = Cliente::create($this->getValidatedData()); 
       return $newObj;
    }

    public function show(Cliente $cliente){
        return $cliente;
    }

    public function update(Cliente $cliente){
        $cliente->update($this->getValidatedData('requested'));
        return $cliente;
    }

    public function destroy(Cliente $cliente){
        $cliente->delete();
    }
}
