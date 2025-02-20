## Install SDK
```shell
composer require taddy/taddy-sdk
```

## Configure Ads or Exchange
```php
use Taddy\Sdk\Ads as TaddyAds;
use Taddy\Sdk\Exchange as TaddyExchange;
use Taddy\Sdk\Dto\User as TelegramUser;
use Taddy\Sdk\Dto\Currency;

$ads = new TaddyAds(
    pubId: 'bot-xxxxxxxxxxxxxx', // Get from Taddy.pro
    token: '123456780:yyyyyyyyyyyyyyyyyy', // Your bot's token
);

$exchange = new TaddyExchange(
    pubId: 'bot-xxxxxxxxxxxxxx', // Get from Taddy.pro
);
```

## Define User with all data you have 
```php
$user = TelegramUser::factory($tgUserId)
    ->withUsername($tgUsername)
    ->withFirstName($tgFirstName)
    // other stuff
;
```
## Show Ads
```php
// somewere in your code
$ads->show($user);
// continue
```

## Exchange
#### Handle `/start` command to detect incoming users
```php
$exchange->startEvent(
    user: $user, // User DTO
    start: $startMessage // text from update with /start command
);
```
#### Get tasks list (feed) to display
This method returns array of `ExchangeFeedItem` DTO with `id`, `title`, `description`, `image` and `link` fields.
You need to display it how you want.
```php
$feed = $exchange->getFeed(
    user: $user, // User DTO
    limit: 4, // limit tasks count
    autoImpressions: false, // automatically send impressions event    
);
```
#### Send impressions event
If you are not used `autoImpressions` in previous step, you should use this call to send impressions event manually
```php
// send impressions event manually (after successful show)
$exchange->impressionsEvent(
    user: $user, // User DTO
    items: $feed, // showed items array    
);
```
#### Check exchange
You track if exchange completed using webhooks or calling this method
```php
// send impressions event manually (after successful show)
$exchange->check(
    user: $user, // User DTO or user id
    item: $item, // ExchangeFeedItem DTO or item id   
);
```
### Send custom events (optional)
```php
// send custom event
$exchange->customEvent(
    event: 'custom1', // custom event: custom1 ... custom4 
    user: $user, // User DTO or user id
    value: 1.23, // Value (optional)
    currency: Currency::USD, // value currency (optional)
    once: false, // one-time event registration
);
```

### See also
- [Taddy Exchange Docs](https://dent-cacao-26b.notion.site/SDK-1a02599ec91e800ba6e5d4ebd027b620)
- [Taddy WEB SDK](https://www.npmjs.com/package/taddy-sdk)