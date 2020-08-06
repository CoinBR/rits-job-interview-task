<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

use App\Cliente;


class ClientesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void{
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // Entity data and configs. Must be changed for each Entity

    public static function data(){
        return [
            'nome' => 'Fulano',
            'email' => 'fulano@cicrano.com',
            'telefone' => '84988887777',
            'endereco' => 'Avenida das Ruas, 85. Bairro: Barro Azul'
        ];
    }

    public static function data2(){
        return [
            'nome' => 'Jesse Pinkman',
            'email' => 'pinkman@waner.tv',
            'telefone' => '84988003311',
            'endereco' => 'Rua das Estrelas, 77. Bairro: Ponta Parda'
        ];
    }

    private function getBaseEndpoint(){
        return '/api/clientes';
    }

    private function getUniqueFields(){
        return ['email', 'telefone'];
    }


    // Tests focused on this specific Entity
    

    public function test_valid_email(){
        $wrongData = array_merge($this->data(), ['email' => 'I dont have an email']);
        $this->post($this->getBaseEndpoint(), $wrongData)->assertSessionHasErrors('email');
    }
    public function test_valid_telefone(){
        $field = 'telefone';
        $wrongs = ['988884444', '(84) 9 8888 4444', '84x88884444', '084988885555', '33334444'];
        $corrects = ['84955554444', 84988884444, '8433334444', 8533334444];

        $this->post($this->getBaseEndpoint(), $this->data());

        foreach($wrongs as $value){
            $this->patch($this->getBaseEndpoint() . '/1', [$field => $value])->assertSessionHasErrors($field);
        }

        foreach($corrects as $value){
            $res = $this->patch($this->getBaseEndpoint() . '/1', [$field => $value]);
            $response = $this->get($this->getBaseEndpoint() . '/1');
            $this->assertEquals($response[$field], $value);
        }
    }

    // Tests common to most CRUD entities

    public function test_unique_fields(){
        $legit = $this->data();
        $this->post($this->getBaseEndpoint(), $legit);

        foreach ($this->getUniqueFields() as $key => $field){
            $clone = array_merge($this->data2(), [ $field => $legit[$field] ]);
            $this->post($this->getBaseEndpoint(), $clone)->assertSessionHasErrors($field);
        }
        $this->assertCount(1, Cliente::all());
    }

    public function test_create_cliente(){
        $this->withoutExceptionHandling();

        $this->post($this->getBaseEndpoint(), $this->data());
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
            $this->post($this->getBaseEndpoint(), $incompleteData)->assertSessionHasErrors($k);
        }
        $this->assertCount(0, Cliente::all());
    }

    public function test_get_cliente(){
        $objsData = [$this->data(), $this->data2()];
    
        foreach($objsData as $k => $objData){
            $this->post($this->getBaseEndpoint(), $objData);
        }
        foreach($objsData as $k => $objData){
            $response = $this->get($this->getBaseEndpoint() . '/' . ($k + 1));
            $response->assertJson($objData);
        }
    }

    public function test_patch_cliente(){
        $this->withoutExceptionHandling();

        $this->post($this->getBaseEndpoint(), $this->data());

        $tmp = $this->data2();
        array_pop($tmp);
        $responsePatch = $this->patch($this->getBaseEndpoint() . '/1', $tmp);
        $responseGet = $this->get($this->getBaseEndpoint() . '/1');
        $expected = array_merge(['id' => 1], $this->data2(), ['endereco' => $this->data()['endereco']]);

        $responsePatch->assertJson($expected);
        $responseGet->assertJson($expected);
        $this->assertCount(1, Cliente::all());
    }
    
    public function test_delete_cliente(){
        $this->post($this->getBaseEndpoint(), $this->data());
        $this->assertCount(1, Cliente::all());

        $this->delete($this->getBaseEndpoint() . '/1');
        $this->assertCount(0, Cliente::all());
       
        $this->post($this->getBaseEndpoint(), $this->data());
        $this->post($this->getBaseEndpoint(), $this->data2());
        $this->assertCount(2, Cliente::all());

        $this->delete($this->getBaseEndpoint() . '/2');
        $this->assertCount(1, Cliente::all());

        $this->delete($this->getBaseEndpoint() . '/3');
        $this->assertCount(0, Cliente::all());
    }

    public function test_list_clientes(){
       $this->post($this->getBaseEndpoint(), $this->data());
       $this->post($this->getBaseEndpoint(), $this->data2());
    
       $expected = [
           array_merge($this->data(), ['id' => 1]),
           array_merge($this->data2(), ['id' => 2]),
       ];
       $this->get($this->getBaseEndpoint())->assertJson($expected);
    }
}