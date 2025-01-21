Step 1: Install SDK
===================
`composer require taddy/taddy-sdk`

Step 2: Configure Ads
=====================
```php
use Taddy\Sdk\Ads as TaddyAds;
use Taddy\Sdk\Dto\User as TelegramUser;

$ads = new TaddyAds(
    pubId: 'bot-xxxxxxxxxxxxxx', // Get from Taddy.pro
    token: '123456780:yyyyyyyyyyyyyyyyyy', // Your bot's token
);
```

Step 3: Define User
===================
```php
$user = TelegramUser::factory($tgUserId)
    ->withUsername($tgUsername)
    ->withFirstName($tgFirstName)
    // other stuff
;
```

Step 4: Show Ads
===================
```php
// somewere in your code
$ads->show($user);
// continue
```