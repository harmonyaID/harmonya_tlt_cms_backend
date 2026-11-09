<?php

namespace App\ThirdParty\Telegram;

use App\Jobs\SendTelegramMessageJob;
use App\Services\Constant\Global\QueueType;
use function config;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Telegram
{
    const BASE_URL = 'https://api.telegram.org/';

    const BOOT_TOKENS = [
        'DEVELOPMENT' => ''
    ];

    const ROOM_CHAT_IDS = [
        'DEVELOPMENT' => ''
    ];

    public function __construct(public string|null $bootToken = null, public string|null $roomChatId = null)
    {
        if (!$this->bootToken || !$this->roomChatId) {
            $this->fromDevelopment();
        }
    }

    public function fromDevelopment()
    {
        $this->bootToken = self::BOOT_TOKENS['DEVELOPMENT'];
        $this->roomChatId = self::ROOM_CHAT_IDS['DEVELOPMENT'];

        return $this;
    }

    public function send(string $messages)
    {
        if (strlen($messages) <= 0) {
            return false;
        }

        $limit = 3000;
        $messageStrReplace = str_replace("\r\n", "<>", $messages);
        $messageParser = explode("<=>", wordwrap($messageStrReplace, $limit, "<=>"));
        $messageContents = str_replace("<>", "\n", $messageParser);

        if (count($messageContents) == 1) {
            $this->sendContentTelegram($messageContents[0]);
        } else {
            foreach ($messageContents as $key => $messageContent) {
                $this->sendContentTelegram($messageContents[$key]);
            }
        }
    }

    public function queue(string $message, bool $now = true)
    {
        if ($now) {
            SendTelegramMessageJob::dispatchAfterResponse($this->bootToken, $this->roomChatId, $message);
        } else {
            SendTelegramMessageJob::dispatch($this->bootToken, $this->roomChatId, $message)
                ->onConnection(config('queue.default'))
                ->onQueue(QueueType::NOTIFICATION);
        }
    }

    private function sendContentTelegram(string $message)
    {
        try {
            $url = self::BASE_URL . "bot$this->bootToken/sendMessage";

            $client = new Client();
            $response = $client->request("POST", $url, [
                "form_params" => [
                    'chat_id' => $this->roomChatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown'
                ],
                "timeout" => 10
            ]);

            if ($response->getStatusCode() == Response::HTTP_OK) {
                return json_decode($response->getBody(), true);
            } else {
                Log::debug("Third: Telegram {$response->getStatusCode()}");
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception);
            return false;
        }
    }
}
