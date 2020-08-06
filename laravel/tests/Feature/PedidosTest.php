<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

use App\Pedido;
use App\Produto;

use Tests\Feature\ProdutosTest;
use Tests\Feature\ClientesTest;



class PedidosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void{
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);

        $this->post('/api/produtos', ProdutosTest::data());
        $this->post('/api/produtos', ProdutosTest::data2());
        $this->post('/api/produtos', ProdutosTest::data3());

        $this->post('/api/clientes', ClientesTest::data());
        $this->post('/api/clientes', ClientesTest::data2());
   }

    // Entity data and configs. Must be changed for each Entity

    private function data(){
        $produtos = $this->getRelated('/api/produtos', 3);
        return [
            'cliente_id' => '1',
            'produtos' => [
                $produtos[0],
                $produtos[2],
            ]
        ];
    }

    private function data2(){
        $produtos = $this->getRelated('/api/produtos', 3);
        return [
            'cliente_id' => '2',
            'produtos' => [
                $produtos[1],
            ]
        ];
    }

    private function data3(){
        $produtos = $this->getRelated('/api/produtos', 3);
        return [
            'cliente_id' => '2',
            'produtos' => [
                $produtos[0],
                $produtos[1],
                $produtos[2],
            ]
        ];
    }

    private function getRelated($endpoint, $qty=2){
        $arrs = [];
        for ($i=0; $i < $qty; $i++){
            $response = $this->get($endpoint . '/' . ($i + 1))->getContent();
            $arrs[$i] = json_decode($response, True);
        }
        return $arrs;
    }

    private function clientes(){
        $arrs = [];
        for ($i=0; $i < 2; $i++){
            $response = $this->get($linksAendpoint, $payload)->getContent();
            $arrs[$i] = json_decode($response, True);
        }
        return $arrs;
    }

    private function getBaseEndpoint(){
        return '/api/pedidos';
    }

    private function getUniqueFields(){
        return [];
    }


    // Tests focused on this specific Entity
    


    // Tests common to most CRUD entities
    public function test_create_pedido(){
        $this->withoutExceptionHandling();

        $this->post($this->getBaseEndpoint(), $this->data());
        $pedido = Pedido::first();

        $this->assertEquals($pedido['id'], 1);
        $this->assertEquals($pedido['cliente_id'], 1);
        $this->assertEquals($pedido['status'], 0);

        $asserts = [
            [$pedido->produtos()->getResults()->skip(0)->first()->toArray()  ,  Produto::first()->toArray()], 
            [$pedido->produtos()->getResults()->skip(1)->first()->toArray()  ,  Produto::find(3)->toArray()], 
        ];
        foreach ($asserts as $pair){
            unset($pair[0]['pivot']);
            $this->assertEquals($pair[0], $pair[1]);
        }
    }

    public function test_get_pedido(){
        $objsData = [$this->data(), $this->data2()];
    
        foreach($objsData as $k => $objData){
            $this->post($this->getBaseEndpoint(), $objData);
        }
        foreach($objsData as $k => $objData){
            $response = $this->get($this->getBaseEndpoint() . '/' . ($k + 1));
            $response->assertJson($objData);
        }
    }
/*
    public function test_patch_pedido(){
        $this->withoutExceptionHandling();

        $this->post($this->getBaseEndpoint(), $this->data());

        $tmp = $this->data2();
        array_pop($tmp);
        $responsePatch = $this->patch($this->getBaseEndpoint() . '/1', $tmp);
        $responseGet = $this->get($this->getBaseEndpoint() . '/1');
        $expected = array_merge(['id' => 1], $this->data(), $tmp);
        unset($expected['produtos']);

        $responsePatch->assertJson($expected);
        $responseGet->assertJson($expected);
        $this->assertCount(1, Pedido::all());
    }
    */
    
    public function test_delete_pedido(){

        $this->post($this->getBaseEndpoint(), $this->data());
        $this->post($this->getBaseEndpoint(), $this->data2());
        $this->post($this->getBaseEndpoint(), $this->data3());

        $this->get('/api/clientes/2/pedidos')->assertJsonCount(2);
        $this->delete('/api/clientes/2/pedidos/2');
        $this->get('/api/clientes/2/pedidos')->assertJsonCount(1);
        $this->delete('/api/clientes/1/pedidos/3')->assertSessionHasErrors();
    }

    public function test_unique_fields(){
        $legit = $this->data();
        $this->post($this->getBaseEndpoint(), $legit);

        foreach ($this->getUniqueFields() as $key => $field){
            $clone = array_merge($this->data2(), [ $field => $legit[$field] ]);
            $this->post($this->getBaseEndpoint(), $clone)->assertSessionHasErrors($field);
        }
        $this->assertCount(1, Pedido::all());
    }

    public function test_list_pedidos(){
       $this->post($this->getBaseEndpoint(), $this->data());
       $this->post($this->getBaseEndpoint(), $this->data2());
    
       $expected = [
           array_merge($this->data(), ['id' => 1]),
           array_merge($this->data2(), ['id' => 2]),
       ];
        
       $this->get($this->getBaseEndpoint())->assertJson($expected);
    }

    public function test_pedido_index_cliente(){
        $this->post($this->getBaseEndpoint(), $this->data());
        $this->post($this->getBaseEndpoint(), $this->data2());
        $this->post($this->getBaseEndpoint(), $this->data3());

        $response = $this->get('/api/clientes/2/pedidos');
        $response->assertJsonCount(2);
        $response->assertJson([
            array_merge($this->data2(), ['id' => 2]),
            array_merge($this->data3(), ['id' => 3])
        ]);
    }
}