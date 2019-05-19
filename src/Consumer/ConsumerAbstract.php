<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 13.05.19
 * Time: 22:14
 */

namespace App\Consumer;

use Psr\Log\LoggerInterface;

abstract class ConsumerAbstract
{
    /**
     * @var \DateTime
     */
    private $endDate;


    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ConsumerAbstract constructor.
     * @param LoggerInterface $logger
     * @throws \Exception
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->info('CONSUMER_DEVICE rozpoczynam prace');
        $ttl = isset($_ENV['CONSUMER_TTL']) ? $_ENV['CONSUMER_TTL'] : '5minutes';
        $this->endDate = (new \DateTime('now'))->modify('+' . $ttl);
        $this->logger->info('ustawiłem date końca', ['data_konca' => $this->endDate->format('d-m-Y H:i:s')]);
    }


    /**
     * @throws \Exception
     */
    public function check(){
        $now = new \DateTime('now');
        $wynik = $now->getTimestamp() >= $this->endDate->getTimestamp();
        $this->logger->info('czy mozna kontynuowac', [
            'data_konca' => $this->endDate->format('d-m-Y H:i:s'),
            'data_teraz' => $now->format('d-m-Y H:i:s'),
            'wynik' => $wynik
        ]);
        if($wynik){
            $this->logger->info('Zabijam consumera!', [$now->format('d-m-Y H:i:s')]);
            exit(1);
        }
    }
}