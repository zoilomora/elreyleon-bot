<?php

namespace TheLionKing;

use GuzzleHttp\Client;
use TheLionKing\Entity\Pass;

class Repository
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Pass[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAll(): array
    {
        $response = $this->client->request(
            'GET',
            'wp-admin/admin-ajax.php',
            [
                'query' => [
                    'action' => 'wphack-send-calendar',
                    'musical' => 'ERL.Json',
                ],
            ]
        );

        $raw = $response->getBody()->getContents();
        $data = json_decode($raw, true);

        return $this->buildModel(
            $data['cities']['madrid']['shows']
        );
    }

    /**
     * @param array $data
     * @return Pass[]
     */
    private function buildModel(array $data): array
    {
        $passes = [];

        foreach ($data as $show) {
            foreach ($show['times'] as $pass) {
                $passes[] = Pass::from(
                    \DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        $show['date'] . ' ' . $pass['times'],
                        new \DateTimeZone('Europe/Madrid')
                    ),
                    'https:' . $pass['iframe'],
                    intval($pass['available'])
                );
            }
        }

        return $passes;
    }
}
