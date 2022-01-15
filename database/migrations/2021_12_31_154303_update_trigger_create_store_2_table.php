<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateTriggerCreateStore2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP TRIGGER tr_optionals_financial_state_complementary_stores;
            CREATE TRIGGER tr_optionals_financial_state_complementary_stores AFTER INSERT ON `stores` FOR EACH ROW
            BEGIN
                INSERT INTO optionals (`nome`, `tipo_auto`, `company_id`, `store_id`, `user_insert`, `created_at`) VALUES
                ("Airbag", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Alarme", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Alerta de Colisão", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Apoio de Braço", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Ar condicionado", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Ar quente", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Banco com regulagem de altura", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Banco de couro", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Bancos dianteiros com aquecimento", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Chave reserva", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Computador de Bordo", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Conexão Bluetooth", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Controle de Som no Volante", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Controle de Velocidade", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Desembaçador traseiro", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Encosto de cabeça traseiro", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Faróis de LED", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Faróis de Neblina", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Farol de xenônio", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Freio ABS", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Limpador traseiro", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Manual do proprietário", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Para-choques na Cor do Veículo", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Piloto Automático", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Retrovisores Elétricos", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Rodas de Liga Leve", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Sensor de estacionamento", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Sensor de Ré", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Teto Panorâmico", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Teto solar", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Tração 4x4", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Travas elétricas", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Vidros elétricos", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Volante com regulagem de altura", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Volante Escamoteável", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("7 lugares", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Abertura Interno Porta Malas", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Air Bag Duplo", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Air Bag Lateral", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Airbag lateral em modo cortina", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Alerta de colisão frontal", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Alerta de ponto cego", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Ar Condicionado Digital", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Ar-condicionado Dual Zone", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Banco Bi-Partido", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Banco do Motorista com Ajuste de Altura", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Banco do Motorista com Ajuste Elétrico", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Bancos Elétricos Couro", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Câmbio Tiptronic", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Câmera 360°", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Câmera de ré", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("CD Player", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Central multimídia", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Controle de Estabilidade", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Controle de Tração", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("GPS", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Indicador de temperatura externa", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Interface", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("ISOFIX", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Luz Diurna de LED", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("MP3 Player", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Paddle Shift", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Rebatimento elétrico dos retrovisores", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Revisões em dia", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Saída de ar-condicionado para os bancos traseiros", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Sistema start-stop", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Som", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Som USB", "carros", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE);

                INSERT INTO financial_states (`nome`, `company_id`, `store_id`, `user_insert`, `created_at`) VALUES
                ("Garantia de Fábrica", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("IPVA Pago", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Licenciado", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Todas as Revisões", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Único Dono", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Aceita Financiamento", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Aceita Troca", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Pagamento à vista", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Troca com Troco", NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE);

                INSERT INTO complementaries (`nome`, `tipo_auto`, `tipo_campo`, `valores_padrao`, `company_id`, `store_id`, `user_insert`, `created_at`) VALUES
                ("Direção", "carros", "select", \'["Direção Elétrica","Direção Hidráulica","Direção Manual"]\', NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Câmbio", "carros", "select", \'["Automático","Automatizado","CVT", "Manual"]\', NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Carroceria", "carros", "select", \'["Buggy","Cupê","Hatchback","Minivan","Perua / SW","Picape","Sedã","SUV","Van / Utilitário"]\', NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE),
                ("Estado", "all", "select", \'["Novo","Seminovo","Usado"]\', NEW.company_id, NEW.id, NEW.user_created, CURRENT_DATE);

                INSERT INTO users_to_stores (user_id, `company_id`, `store_id`) SELECT u.id, NEW.company_id, NEW.id FROM users AS u WHERE u.permission = "master";
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
