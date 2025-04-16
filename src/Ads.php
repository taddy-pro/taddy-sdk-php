<?php

namespace Taddy\Sdk;

use Psr\Log\LoggerInterface;
use Taddy\Sdk\Dto\Ad;
use Taddy\Sdk\Dto\User;
use Throwable;
use Yabx\Telegram\BotApi;
use Yabx\Telegram\Objects\InlineKeyboardButton;
use Yabx\Telegram\Objects\InlineKeyboardMarkup;
use Yabx\Telegram\Objects\LinkPreviewOptions;
use Yabx\Telegram\Objects\Message;
use Yabx\Telegram\Objects\WebAppInfo;

class Ads {

    protected Taddy $taddy;
    protected BotApi $telegram;
    protected LoggerInterface $logger;

    public function __construct(Taddy $taddy) {
        $this->taddy = $taddy;
        $this->telegram = new BotApi(
            token: $taddy->getOptions()->getToken() ?? '',
            logger: $taddy->getOptions()->getLogger(),
            apiUrl: $taddy->getOptions()->getBotApiUrl()
        );
        $this->logger = $taddy->getOptions()->getLogger();
    }

    public function getAd(User $user): ?Ad {
        $res = $this->taddy->request('POST', '/v1/ads/get', [
            'pubId' => $this->taddy->getPubId(),
            'user' => $this->taddy->toArray($user),
            'origin' => 'server',
            'format' => 'bot-ad',
        ]);
        return $res ? $this->taddy->toObject($res, Ad::class) : null;
    }

    public function impressions(User $user, string|array $id): void {
        $this->taddy->request('POST', '/v1/ads/impressions', [
            'pubId' => $this->taddy->getPubId(),
            'user' => $this->taddy->toArray($user),
            'origin' => 'server',
            'id' => $id,
        ]);
    }

    public function show(Ad $ad, User $user, bool $deleteMessage = true): ?Message {
        try {
            $tag = "[$user->id / $ad->id]";
            $this->logger->debug("$tag: Show Ad...");

            $title = '<b>' . $ad->title . '</b>';
            if ($text = ($ad->ad->text ?? $ad->ad->description ?? null)) {
                $text = str_replace('{link}', $ad->link, $text);
            }
            $text = trim($title . "\n\n" . $text);

            $buttonText = $ad->button ?: 'Go!';
            $button = new InlineKeyboardButton($buttonText, webApp: new WebAppInfo($ad->link));
            $buttons = new InlineKeyboardMarkup([[$button]]);

            if ($image = $ad->image) {
                $this->logger->debug("$tag: Sending Ad with image...");
                $msg = $this->telegram->sendPhoto(
                    chatId: $user->id,
                    photo: $image,
                    caption: $text,
                    parseMode: 'html',
                    replyMarkup: $buttons
                );
            } elseif ($video = $ad->video) {
                $this->logger->debug("$tag: Sending Ad with video...");
                $msg = $this->telegram->sendVideo(
                    chatId: $user->id,
                    video: $video,
                    parseMode: 'html',
                    caption: $text,
                    replyMarkup: $buttons
                );
            } else {
                $this->logger->debug("$tag: Sending text Ad...");
                $msg = $this->telegram->sendMessage(
                    chatId: $user->id,
                    text: $text,
                    parseMode: 'html',
                    linkPreviewOptions: new LinkPreviewOptions(isDisabled: true),
                    replyMarkup: $buttons
                );
            }

            $showTime = 20;

            $this->logger->debug("$tag: Send impressions...");
            $this->impressions($user, $ad->id);

            $this->logger->debug("$tag: Waiting for $showTime sec...");
            sleep($showTime);

            if($deleteMessage) {
                $this->logger->debug("$tag: Deleting Ad...");
                $this->telegram->deleteMessage($user->id, $msg->getMessageId());
                return null;
            }
            return $msg;

        } catch (Throwable $e) {
            $this->logger->error("$tag:  {$e->getMessage()}");
            return null;
        }
    }

}