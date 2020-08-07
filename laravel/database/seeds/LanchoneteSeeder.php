<?php

use Illuminate\Database\Seeder;
use App\Cliente;
use App\Produto;
use App\Pedido;

class LanchoneteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cliente::create([
            'nome' => 'Jorge',
            'email' => 'jorge@hotmail.com',
            'telefone' => '84988883333',
            'endereco' => 'Rua Jorgiana, 27. Bairro Morro Azul.'
        ]);

        Cliente::create([
            'nome' => 'Fabricia',
            'email' => 'fabricia@hotmail.com',
            'telefone' => '84981883233',
            'endereco' => 'Avenida das Frabricas, 333. Bairro Lagoa Parda.'
        ]);

        #######

        Produto::create([
            'nome' => 'Rosquinha',
            'preco' => '400',
        ]);

        Produto::create([
            'nome' => 'Cachorro Quente',
            'preco' => '350',
        ]);

        Produto::create([
            'nome' => 'Suco de Laranja',
            'preco' => '550',
        ]);


        ########
        
        Pedido::create(['cliente_id' => 1, 'status' => 0])->produtos()->sync(1);

        Pedido::create(['cliente_id' => 2, 'status' => 0])->produtos()->sync(1, 2);
        Pedido::create(['cliente_id' => 2, 'status' => 0])->produtos()->sync(1, 2, 3);
    }
}
