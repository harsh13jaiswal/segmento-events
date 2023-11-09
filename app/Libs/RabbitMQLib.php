<?php

namespace App\Libs;

use App\Services\EventService;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQLib {
    protected $channel;
    protected $connection;
    public function __construct($host, $port, $user, $password, $vhost){
        //define('AMQP_DEBUG', true);
        if(env('APP_ENV') == 'local') {
            $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        } else {
            $this->connection = new AMQPSSLConnection($host, $port, $user, $password, $vhost,
                ['verify_peer_name' => false],
                'ssl'
            );
        }
        $this->channel = $this->connection->channel();
    }
    public function enqueue($queue, $data) {
        $this->channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage(json_encode($data));
        $this->channel->basic_publish($msg, '', $queue);
    }
    public function dequeue($queue, $batchSize) {
        try {
            $eventLogs = [];
            $deliveryTags = [];
            $queueProperties = $this->channel->queue_declare($queue, false, true, false, false);
            $queueSize = $queueProperties[1];
            $this->channel->basic_qos(null, $batchSize, null);
            $this->channel->basic_consume(
                $queue,                    #queue 
                '',                             #consumer tag - Identifier for the consumer, valid within the current channel. just string
                false,                          #no local - TRUE: the server will not send messages to the connection that published them
                false,                           #no ack - send a proper acknowledgment from the worker, once we're done with a task
                false,                          #exclusive - queues may only be accessed by the current connection
                false,                          #no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
                function($message) use (&$eventLogs, &$deliveryTags, $batchSize) {
                    $decodedMsg = json_decode($message->body, true);
                    $eventLogs[] = $decodedMsg;
                    $deliveryTags[] = $message->delivery_info['delivery_tag'];

                    // If we have processed all messages in the prefetch, acknowledge them
                    if (count($eventLogs) == $batchSize) {
                        $service = new EventService();
                        $service->executeEventLogJob($eventLogs);

                        foreach ($deliveryTags as $tag) {
                            $message->delivery_info['channel']->basic_ack($tag);
                        }
                        
                        $deliveryTags = [];
                        $eventLogs = [];
                    }
                }   #callback - method that will receive the message
            );

            while($queueSize--) {
                $this->channel->wait();
            }

        } catch (\Exception $ex) {
            echo $ex->getMessage();
            return [];
        }
    }

    public function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }
}