<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

use App\Produto;


class ProdutosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void{
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // Entity data and configs. Must be changed for each Entity

    public static function data(){
        return [
            'nome' => 'Pastel',
            'preco' => 599,
        ];
    }

    public static function data2(){
        return [
            'nome' => 'Espetinho',
            'preco' => 350,
        ];
    }

    public static function data3(){
        return [
            'nome' => 'Suco',
            'preco' => 700,
        ];
    }

    private function getBaseEndpoint(){
        return '/api/produtos';
    }

    private function getUniqueFields(){
        return ['nome',];
    }


    // Tests focused on this specific Entity
    


    // Tests common to most CRUD entities
    public function test_create_produto(){
        $this->withoutExceptionHandling();

        $this->post($this->getBaseEndpoint(), $this->data());
        $produto = Produto::first();

        foreach($this->data() as $key => $value){
            $this->assertEquals($value, $produto[$key]);
        }
    }

    public function test_get_produto(){
        $objsData = [$this->data(), $this->data2()];
    
        foreach($objsData as $k => $objData){
            $this->post($this->getBaseEndpoint(), $objData);
        }
        foreach($objsData as $k => $objData){
            $response = $this->get($this->getBaseEndpoint() . '/' . ($k + 1));
            $response->assertJson($objData);
        }
    }

    public function test_patch_produto(){
        $this->withoutExceptionHandling();

        $this->post($this->getBaseEndpoint(), $this->data());

        $tmp = $this->data2();
        array_pop($tmp);
        $responsePatch = $this->patch($this->getBaseEndpoint() . '/1', $tmp);
        $responseGet = $this->get($this->getBaseEndpoint() . '/1');
        $expected = array_merge(['id' => 1], $this->data(), $tmp);

        $responsePatch->assertJson($expected);
        $responseGet->assertJson($expected);
        $this->assertCount(1, Produto::all());
    }
    
    public function test_delete_produto(){
        $this->post($this->getBaseEndpoint(), $this->data());
        $this->assertCount(1, Produto::all());

        $this->delete($this->getBaseEndpoint() . '/1');
        $this->assertCount(0, Produto::all());
       
        $this->post($this->getBaseEndpoint(), $this->data());
        $this->post($this->getBaseEndpoint(), $this->data2());
        $this->assertCount(2, Produto::all());

        $this->delete($this->getBaseEndpoint() . '/2');
        $this->assertCount(1, Produto::all());

        $this->delete($this->getBaseEndpoint() . '/3');
        $this->assertCount(0, Produto::all());
    }

    public function test_unique_fields(){
        $legit = $this->data();
        $this->post($this->getBaseEndpoint(), $legit);

        foreach ($this->getUniqueFields() as $key => $field){
            $clone = array_merge($this->data2(), [ $field => $legit[$field] ]);
            $this->post($this->getBaseEndpoint(), $clone)->assertSessionHasErrors($field);
        }
        $this->assertCount(1, Produto::all());
    }

    public function test_all_fields_are_required(){
        $produtoData = $this->data();

        foreach ($produtoData as $k => $v){
            $incompleteData = $produtoData;
            unset($incompleteData[$k]);
            $this->post($this->getBaseEndpoint(), $incompleteData)->assertSessionHasErrors($k);
        }
        $this->assertCount(0, Produto::all());
    }

    public function test_list_produtos(){
       $this->post($this->getBaseEndpoint(), $this->data());
       $this->post($this->getBaseEndpoint(), $this->data2());
    
       $expected = [
           array_merge($this->data(), ['id' => 1]),
           array_merge($this->data2(), ['id' => 2]),
       ];
       $this->get($this->getBaseEndpoint())->assertJson($expected);
    }
}