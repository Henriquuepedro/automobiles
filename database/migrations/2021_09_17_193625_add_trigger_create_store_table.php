<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTriggerCreateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER tr_optionals_financial_state_complementary_stores AFTER INSERT ON `stores` FOR EACH ROW
            BEGIN
                INSERT INTO opcionais (`nome`, `tipo_auto`, `company_id`, `store_id`, `user_insert`, `created_at`) VALUES
                ("Airbag", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Alarme", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Alerta de Colisão", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Apoio de Braço", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Ar condicionado", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Ar quente", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Banco com regulagem de altura", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Banco de couro", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Bancos dianteiros com aquecimento", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Chave reserva", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Computador de Bordo", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Conexão Bluetooth", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Controle de Som no Volante", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Controle de Velocidade", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Desembaçador traseiro", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Encosto de cabeça traseiro", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Faróis de LED", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Faróis de Neblina", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Farol de xenônio", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Freio ABS", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Limpador traseiro", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Manual do proprietário", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Para-choques na Cor do Veículo", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Piloto Automático", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Retrovisores Elétricos", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Rodas de liga leve", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Rodas de Liga Leve", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Sensor de estacionamento", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Sensor de Ré", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Teto Panorâmico", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Teto solar", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Tração 4x4", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Travas elétricas", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Vidros elétricos", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Volante com regulagem de altura", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Volante Escamoteável", "carros", NEW.company_id, NEW.id, NEW.user_created, NOW());

                INSERT INTO estados_financeiro (`nome`, `company_id`, `store_id`, `user_insert`, `created_at`) VALUES
                ("Garantia de Fábrica", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("IPVA Pago", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Licenciado", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Todas as Revisões", NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Único Dono", NEW.company_id, NEW.id, NEW.user_created, NOW());

                INSERT INTO complementar_autos (`nome`, `tipo_auto`, `tipo_campo`, `valores_padrao`, `company_id`, `store_id`, `user_insert`, `created_at`) VALUES
                ("Direção", "carros", "select", \'["Direção Elétrica","Direção Hidráulica","Direção Manual"]\', NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Câmbio", "carros", "select", \'["Automático","Automatizado","CVT", "Manual"]\', NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Carroceria", "carros", "select", \'["Buggy","Cupê","Hatchback","Minivan","Perua / SW","Picape","Sedã","SUV","Van / Utilitário"]\', NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Estado", "all", "select", \'["Novo","Seminovo","Usado"]\', NEW.company_id, NEW.id, NEW.user_created, NOW()),
                ("Tipo de Negociação", "all", "select", \'["Aceita Financiamento","Aceita Troca","Pagamento à vista","Troca com Troco"]\', NEW.company_id, NEW.id, NEW.user_created, NOW());
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_optionals_financial_state_complementary_stores`');
    }
}
