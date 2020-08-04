<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Cliente;


class ClientesTest extends TestCase
{
    use RefreshDatabase;


    function data(){
        return [
            'nome' => 'Fulano',
            'email' => 'fulano@cicrano.com',
            'telefone' => '84988887777',
            'endereco' => 'Avenida das Ruas, 85. Bairro: Barro Azul'
        ];
    }

    function data2(){
        return [
            'nome' => 'Jesse Pinkman',
            'email' => 'pinkman@waner.tv',
            'telefone' => '84988003311',
            'endereco' => 'Rua das Estrelas, 77. Bairro: Ponta Parda'
        ];
    }

    public function test_create_cliente(){
        $this->withoutExceptionHandling();

        $this->post('/api/clientes', $this->data());
        $cliente = Cliente::first();

        foreach($this->data() as $key => $value){
            $this->assertEquals($value, $cliente[$key]);
        }
    }

    public function test_all_fields_are_required(){
        $clienteData = $this->data();

        foreach ($clienteData as $k => $v){
            $incompleteData = $clienteData;
            unset($incompleteData[$k]);
            $this->post('/api/clientes', $incompleteData)->assertSessionHasErrors($k);
        }
        $this->assertCount(0, Cliente::all());
    }
    
    public function test_valid_email(){
        $wrongData = array_merge($this->data(), ['email' => 'I dont have an email']);
        $this->post('/api/clientes', $wrongData)->assertSessionHasErrors('email');
    }

    public function test_unique_fields(){
        $uniqueFields = ['email', 'telefone'];

        $legit = $this->data();
        $this->post('api/clientes', $legit);

        foreach ($uniqueFields as $key => $field){
            $clone = array_merge($this->data2(), [ $field => $legit[$field] ]);
            $this->post('api/clientes', $clone)->assertSessionHasErrors($field);
        }

        $this->assertCount(1, Cliente::all());
    }

    public function test_get_cliente(){
        $objsData = [$this->data(), $this->data2()];
    
        foreach($objsData as $k => $objData){
            $this->post('api/clientes', $objData);
        }
        foreach($objsData as $k => $objData){
            $response = $this->get('api/clientes/' . ($k + 1));
            $response->assertJson($objData);
        }
    }
}