<?php

require '../vendor/autoload.php';

(new \Symfony\Component\Dotenv\Dotenv())->load('../.env');

$interval = intval($_ENV['INTERVAL']);
$dateToSearch = new DateTime($_ENV['DATE_TO_SEARCH']);
$days = json_decode($_ENV['DAYS']);
$minimumAvailability = intval($_ENV['MINIMUM_AVAILABILITY']);
$token = $_ENV['TELEGRAM_TOKEN'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];

$telegram = new Telegram(
    $token
);

$service = new \TheLionKing\Service(
    new \TheLionKing\Repository(
        new \GuzzleHttp\Client([
            'base_uri' => 'https://www.elreyleon.es/',
            'defaults' => [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
                    'Referer' => 'https://www.elreyleon.es/entradas/',
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
            ],
        ])
    ),
    $dateToSearch,
    $days,
    $minimumAvailability
);

/**
 * @param \TheLionKing\Entity\Pass[] $passes
 * @return string
 */
function generateMessage(array $passes): string
{
    $message = '';

    /** @var \TheLionKing\Entity\Pass $pass */
    foreach ($passes as $pass) {
        if ($message !== '') {
            $message .= PHP_EOL;
        }

        $message .= '*Pass:* ' . $pass->datetime()->format('Y-m-d H:i:s') . PHP_EOL;
        $message .= '*Available:* ' . $pass->available() . PHP_EOL;
        $message .= '*Web:* ' . $pass->url() . PHP_EOL;
    }

    return $message;
}

while (true) {
    sleep($interval);

    $result = $service->execute();

    if (count($result) === 0) {
        continue;
    }

    $telegram->sendMessage([
        'chat_id' => $chatId,
        'parse_mode' => 'markdown',
        'disable_web_page_preview' => true,
        'text' => generateMessage($result)
    ]);
}
