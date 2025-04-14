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

    public function show(User $user): bool {
        try {
            $tag = "[$user->id]";
            $this->logger->debug("$tag: Show Ad Request...");
            if (!$ad = $this->getAd($user)) {
                $this->logger->debug("$tag: Nothing to show");
                return false;
            }

            //if($preText = $ad->ad->data['preText'] ?? false) {
            //    $preShowTime = $ad->ad->data['preShowTime'] ?? 3;
            //    $this->logger->debug("$tag: Sending Pre-text...");
            //    $preMsg = $this->telegram->sendMessage($user->id, $preText, parseMode: 'html');
            //    $this->logger->debug("$tag: Waiting for $preShowTime sec...");
            //    sleep($preShowTime);
            //} else {
            //    $this->logger->debug("$tag: Pre-text not set");
            //}

            if ($text = $ad->ad->text ?? $ad->ad->description ?? $ad->ad->title ?? '') {
                $text = str_replace('{link}', $ad->link, $text);
            }

            $buttonText = $ad->button ?: 'Open';

            //if(($ad->ad->data['buttonType'] ?? false) === 'web-app') {
            $button = new InlineKeyboardButton($buttonText, webApp: new WebAppInfo($ad->link));
            //} else {
            //    $button = new InlineKeyboardButton($buttonText, url: $ad->link);
            //}

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
            } elseif ($text) {
                $this->logger->debug("$tag: Sending text Ad...");
                $msg = $this->telegram->sendMessage(
                    chatId: $user->id,
                    text: $text,
                    parseMode: 'html',
                    linkPreviewOptions: new LinkPreviewOptions(isDisabled: true),
                    replyMarkup: $buttons
                );
            } else {
                return false;
            }

            $showTime = 15;

            $this->logger->debug("$tag: Send impressions...");
            $this->impressions($user, $ad->id);

            $this->logger->debug("$tag: Waiting for $showTime sec...");
            sleep($showTime);

            $this->logger->debug("$tag: Deleting Ad...");
            $this->telegram->deleteMessage($user->id, $msg->getMessageId());

            $this->logger->debug("$tag: Done!");

            return true;
        } catch (Throwable $e) {
            $this->logger->error("$tag:  {$e->getMessage()}");
            return false;
        }
    }

}