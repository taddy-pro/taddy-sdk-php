## Install SDK
```shell
composer require taddy/taddy-sdk
```

## Initialize Taddy
```php
use Taddy\Sdk\Taddy;

$taddy = new Taddy(pubId: 'bot-xxxxxxxxxxxxxx', new TaddyOptions(
    token: '123456780:yyyyyyyyyyyyyyyyyy', // Your bot's token
));
```

## Define User with all data you have
```php
use Taddy\Sdk\Dto\User as TaddyUser;

$user = TaddyUser::factory($tgUserId)
    ->withUsername($tgUsername)
    ->withFirstName($tgFirstName)
    // other stuff
;
```

## Start event
#### Handle `/start` command to detect incoming users (basic integration)
```php
$taddy->start(
    user: $user, // User DTO
    start: $startMessage // text from update with /start command
);
```

## Ads Service
#### Initialize Ads Service
```php
$ads = $taddy->ads();
```

#### Automatically show ads
```php
// Retrieve ad for $user
$ad = $ads->getAd($user);

// Ad exists, showtime!
if ($ad) {
    $ads->show($ad, $user);
}
```

#### Manual show ads
```php
// Retrieve ad for $user
$ad = $ads->getAd($user);

// Ad exists, showtime!
if ($ad) {
    // show $ad
    myShowAdFuncation($ad); // your custom method
    
    // send imoressions event
    $ads->impressions($user, $ad->id);
    
    // show ad delay 15 sec.
    sleep(15); 
    
    // hide $ad (delete message)
    myHideAdFuncation($ad); // your custom method
}
```

## Exchange Service
#### Initialize Exchange Service
```php
$exchange = $taddy->exchange();
```
#### Get tasks list (feed) to display
This method returns array of `ExchangeFeedItem` DTO with `id`, `title`, `description`, `image`, `type` and `link` fields.
You need to display it how you want.
```php
$feed = $exchange->getFeed(
    user: $user, // User DTO
    limit: 4, // limit tasks count
    imageFormat: 'png', // png, webp, jpg
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
    user: $user, // User DTO
    item: $item, // ExchangeFeedItem DTO or item id   
);
```

## Custom events (optional)
```php
// send custom event
$taddy->customEvent(
    user: $user, // User DTO
    event: 'custom1', // custom event: custom1 ... custom4 
    value: 1.23, // Value (optional)
    currency: Currency::USD, // value currency (optional)
    once: false, // one-time event registration (optional)
);
```

### See also
- [Taddy Docs](https://taddy.gitbook.io/docs)
- [Taddy Rest API](https://taddy.gitbook.io/docs/api)
- [Taddy PHP SDK](https://taddy.gitbook.io/docs/sdk/php)