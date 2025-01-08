<?php

namespace Taddy\Sdk;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Taddy\Sdk\Dto\ShowCreativeResponse;
use Taddy\Sdk\Dto\User;
use Throwable;
use Yabx\Telegram\BotApi;
use Yabx\Telegram\Objects\InlineKeyboardButton;
use Yabx\Telegram\Objects\InlineKeyboardMarkup;
use Yabx\Telegram\Objects\WebAppInfo;

class Ads {

    protected Client $client;
    protected string $pubId;
    protected string $token;
    protected BotApi $telegram;
    protected LoggerInterface $logger;

    public function __construct(string $pubId, string $token, string $botApiUrl = 'https://api.telegram.org', Client $client = new Client, LoggerInterface $logger = new NullLogger) {
        $this->client = $client;
        $this->pubId = $pubId;
        $this->token = $token;
        $this->telegram = new BotApi($token, logger: $logger, apiUrl: $botApiUrl);
        $this->logger = $logger;
    }

    public function show(User $user): void {
        try {
            $tag = "[$user->id]";
            $this->logger->debug("$tag: Show Creative Request...");
            if(!$ad = $this->getCreative($user)) {
                $this->logger->debug("$tag: Nothing to show");
                return;
            }

            if($pre = $ad->creative->data['pre'] ?? false) {
                $this->logger->debug("$tag: Sending Pre-text...");
                $msg = $this->telegram->sendMessage($user->id, $pre);
                sleep(3);
                $this->logger->debug("$tag: Deleting Pre-text...");
                $this->telegram->deleteMessage($user->id, $msg->getMessageId());
            } else {
                $this->logger->debug("$tag: Pre-text not set");
            }

            if($text = $ad->creative->data['text'] ?? null) {
                $text = str_replace('{link}', $ad->link, $text);
            }

            $buttonText = $ad->creative->data['buttonText'] ?? 'Open';

            if(($ad->creative->data['buttonType'] ?? false) === 'web-app') {
                $button = new InlineKeyboardButton($buttonText, webApp: new WebAppInfo($ad->link));
            } else {
                $button = new InlineKeyboardButton($buttonText, url: $ad->link);
            }

            $buttons = new InlineKeyboardMarkup([[$button]]);

            if($media = $ad->creative->media) {
                $this->logger->debug("$tag: Sending Creative with media...");
                $msg = $this->telegram->sendPhoto(chatId: $user->id, photo: $media->url, caption: $text, parseMode: 'html', replyMarkup: $buttons);

            } elseif ($text) {
                $this->logger->debug("$tag: Sending Creative with media...");
                $msg = $this->telegram->sendMessage(chatId: $user->id, text: $text, parseMode: 'html', replyMarkup: $buttons);

            } else {
                return;
            }

            $showTime = $ad->creative->data['showTime'] ?? 15;

            $this->logger->debug("$tag: Waiting for $showTime sec...");
            sleep($showTime);

            $this->logger->debug("$tag: Deleting Creative...");
            $this->telegram->deleteMessage($user->id, $msg->getMessageId());
            $this->logger->debug("$tag: Done!");
        } catch (Throwable $e) {
            $this->logger->error("$tag: {$e->getMessage()}");
        }
    }

    public function getCreative(User $user): ?ShowCreativeResponse {
        $res = $this->client->request('GET', '/ads/show', [
            'pubId' => $this->pubId,
            'user' => $this->client->toArray($user)
        ]);
        return $res ? $this->client->toObject($res, ShowCreativeResponse::class) : null;
    }

}