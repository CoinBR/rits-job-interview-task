<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Pedido;
use App\Cliente;


class PedidosController extends Controller
{

    private function basicValidation($fields='all'){
        $validationRules = [
            'cliente_id' => 'required|integer',
            'status' => 'string',
            'produtos' => 'array',
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
    
    private function getValidatedData($fields='all'){
        $data = $this->basicValidation($fields);
        if (!array_key_exists('status', $data) || !$data['status']){
            $data['status'] = 0;
        }
        if (!array_key_exists('produtos', $data)){
            $data['produtos'] = [];
        }

        if (!Cliente::find($data['cliente_id'])){
            throw ValidationException::withMessages(
                ['cliente_id' => 'Cliente não encontrado na base de dados']);
        }
        return $data;
    }



    private function getRequestFieldsNames(){
        $fields = [];
        foreach(request()->all() as $fieldName => $value){
            array_push($fields, $fieldName);
        }
        return $fields;
    }
    
    public function index(){
        return Pedido::with('produtos')->get();
    }

    public function store(){
       $data = $this->getValidatedData();
       $pedidoData = $data;
       unset($pedidoData['produtos']);

       $newObj = Pedido::create($pedidoData); 

       $objsIds = [];
       foreach ($data['produtos'] as $key => $produto){
           $objsIds[$key] = $produto['id'];
       }
       $newObj->produtos()->sync($objsIds);

       return $this->show($newObj);
    }

    public function show(Pedido $pedido){
        return Pedido::where('id', '=', $pedido['id'])
            ->with('produtos')
            ->first();
    }


    public function destroy(Pedido $pedido){
        $pedido->delete();
    }
}
