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
}