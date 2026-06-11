<?php

namespace App\Console\Commands\MessageBroker;

use App\Services\Constant\Global\RabbitMQConstant;
use App\Services\MessageBroker\TestingConsumer;
use GlobalXtreme\RabbitMQ\Constant\GXRabbitConnectionType;
use GlobalXtreme\RabbitMQ\Queue\GXRabbitMQConsumer;
use Illuminate\Console\Command;

class RabbitMQConsumerGlobalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consumer-global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Default command for consume global connection';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $consumer = new GXRabbitMQConsumer();

        $consumer->setExchanges([
            RabbitMQConstant::SERVICE_DOMAIN_FEATURE_ACTION_EXCHANGE => TestingConsumer::class,
        ]);

        $consumer->setQueues([
            RabbitMQConstant::SERVICE_DOMAIN_FEATURE_ACTION_QUEUE => TestingConsumer::class,
        ]);

        $connection = GXRabbitConnectionType::GLOBAL;
        $this->line("\n<bg=blue>[GX-Info]</> Processing consumer for the <options=bold>[$connection]</> connection.\n");

        $consumer->consume($connection);
    }
}
