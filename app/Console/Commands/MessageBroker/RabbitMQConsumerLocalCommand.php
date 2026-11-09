<?php

namespace App\Console\Commands\MessageBroker;

use GlobalXtreme\RabbitMQ\Constant\GXRabbitConnectionType;
use GlobalXtreme\RabbitMQ\Queue\GXRabbitMQConsumer;
use Illuminate\Console\Command;

class RabbitMQConsumerLocalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consumer-local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Default command for consume local connection';

    public function handle()
    {
        $consumer = new GXRabbitMQConsumer();

        $consumer->setExchanges([
        ]);

        $consumer->setQueues([
        ]);

        $connection = GXRabbitConnectionType::LOCAL;
        $this->line("\n<bg=blue>[GX-Info]</> Processing consumer for the <options=bold>[$connection]</> connection.\n");

        $consumer->consume($connection);
    }
}
