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
        return ['nome',];
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
            #dd(get_class_methods($response));
            #dd($response->json());
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

        $responsePatch->assertJson($expected);
        $responseGet->assertJson($expected);
        $this->assertCount(1, Pedido::all());
    }
    
    public function test_delete_pedido(){
        $this->post($this->getBaseEndpoint(), $this->data());
        $this->assertCount(1, Pedido::all());

        $this->delete($this->getBaseEndpoint() . '/1');
        $this->assertCount(0, Pedido::all());
       
        $this->post($this->getBaseEndpoint(), $this->data());
        $this->post($this->getBaseEndpoint(), $this->data2());
        $this->assertCount(2, Pedido::all());

        $this->delete($this->getBaseEndpoint() . '/2');
        $this->assertCount(1, Pedido::all());

        $this->delete($this->getBaseEndpoint() . '/3');
        $this->assertCount(0, Pedido::all());
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

    public function test_all_fields_are_required(){
        $pedidoData = $this->data();

        foreach ($pedidoData as $k => $v){
            $incompleteData = $pedidoData;
            unset($incompleteData[$k]);
            $this->post($this->getBaseEndpoint(), $incompleteData)->assertSessionHasErrors($k);
        }
        $this->assertCount(0, Pedido::all());
    }
    */
}