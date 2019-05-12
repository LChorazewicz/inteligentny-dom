<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 07.05.19
 * Time: 19:44
 */

namespace App\Service\Producers\Util;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class ProducerAbstract
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @param string $virtualHost
     * @return $this
     * @throws InvalidConfigurationException
     */
    protected function connect(string $virtualHost = '/')
    {
        if (is_null($this->connection) || is_null($this->channel)) {
            $config = $this->loadConfig();
            $this->connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $virtualHost);
        }
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @throws \Exception
     */
    public function disconnect()
    {
        if (!is_null($this->channel)) {
            $this->channel->close();
        }

        if (!is_null($this->connection)) {
            $this->connection->close();
        }
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function declareQueue(string $name)
    {
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($name, false, true, false, false);

        return $this;
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function loadConfig()
    {
        $inlineConfig = $_ENV['RABBITMQ_URL'];

        if (is_null($inlineConfig)) {
            throw new InvalidConfigurationException();
        }

        $data = explode('://', $inlineConfig);
        $data1 = explode(':', $data[1]);
        $data2 = explode('@', $data1[1]);

        return [
            'user' => $data1[0], 'password' => $data2[0], 'host' => $data2[1], 'port' => $data1[2], 'protocol' => $data[0]
        ];
    }
}