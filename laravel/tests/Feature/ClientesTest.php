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

        $this->post('/api/clientes', ['nome' => 'Fulano']);
        $this->assertCount(1, Cliente::all());
    }
}
