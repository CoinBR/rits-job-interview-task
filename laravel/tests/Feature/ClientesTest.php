<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Cliente;


class ClientesTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_cliente(){
        $this->withoutExceptionHandling();

        $clienteData = [
            'nome' => 'Fulano',
            'email' => 'fulano@cicrano.com',
            'telefone' => '84988887777',
            'endereco' => 'Avenida das Ruas, 85. Bairro: Barro Azul'
        ];
        $this->post('/api/clientes', $clienteData);
        $cliente = Cliente::first();

        foreach($clienteData as $key => $value){
            $this->assertEquals($clienteData[$key], $cliente[$key]);
        }
    }

    public function test_require_nome(){
        $clienteData = [
            'nome' => 'Fulano',
            'email' => 'fulano@cicrano.com',
            'telefone' => '84988887777',
            'endereco' => 'Avenida das Ruas, 85. Bairro: Barro Azul'
        ];

        foreach ($clienteData as $k => $v){
            $incompleteData = $clienteData;
            unset($incompleteData[$k]);
            $this->post('/api/clientes', $incompleteData)->assertSessionHasErrors($k);
        }

        $this->assertCount(0, Cliente::all());
    }
}