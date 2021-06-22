# Rafflebird

Rafflebird is a highly experimental CLI application for giveaways / raffles on Twitter, built in PHP with [Minicli](https://github.com/minicli/minicli).

![Rafflebird giveaway for most liked reply on the given tweet](https://user-images.githubusercontent.com/293241/122918814-cf683900-d35f-11eb-974b-126566c3c9e8.png)

_**Disclaimer:** The recent search endpoint available via the Twitter API v2 will not return results older than **1 week**. This application is intended for quick raffles that don't require much setup or data persistence. Just pick a random reply, or the most liked reply, and that's it._

## Installation

You can run Rafflebird using Docker Compose, or in a local PHP environment.

### Requirements

- **If you're running Rafflebird with the included Docker Compose environment**, you'll need:
  - Docker and Docker Compose installed on your local system. On Linux, depending on which version of Docker you're using, you may need to install both as separate packages. Windows and macOS users need to install Docker Desktop instead. 
- **If you're running Rafflebird on an existing PHP environment**, you'll need:
  - PHP 7.4+ (CLI only)
  - Composer
  - ext-json
    
### Obtaining the Code
Start by cloning this repository and accessing the project folder with:

```shell
git clone https://github.com/erikaheidi/rafflebird.git
cd rafflebird
```

### Running the Docker Environment
Bring the Docker Compose environment up with:

```shell
docker-compose up -d
```
### Installing Dependencies Via Composer

```shell
docker-compose exec app composer install
```

Or, if you are running this on a local PHP environment:

```shell
composer install
```

### Running Rafflebird

```shell
docker-compose exec app php rafflebird
```

Or, if you are running this on a local PHP environment:

```shell
php rafflebird
```

## Configuring Rafflebird

When you executed `composer install`, a new config file was generated based on a sample config included in the project. You'll need to edit this file to set you your Twitter API credentials.
You'll need valid Twitter credentials from a registered application in order to use this application.

### V2 API
We use the new Twitter V2 API for pulling in the content via the search endpoints, obtaining all replies and their authors as well as information about likes and retweets. That requires the `App Bearer` token.

### V1 API
The traditional Twitter V1.1 API is required for validating participation, such as to verify if a user follows another, or other criteria that might be developed in the future. For these we use the [TwitterOAuth Library](https://twitteroauth.com/). It requires the 4 traditional OAuth keys:

  - API Token / Consumer Token: The application consumer key or APP Key.
  - API Secret / Consummer Secret: The application consumer key secret or App Secret.
  - User Access Token: The user access token.
  - User Access Token Secret: The user access token secret.

All these tokens are available in the application settings page on your [Twitter Developer Portal](https://developer.twitter.com/en/portal/dashboard).

### Setting Up `config.php` File

Once you have your credentials, you can set them up in your `config.php` file:

```php
<?php
#########################
## Configuration File
#########################

return [
    'app_path' => __DIR__ . '/app/Command',
    'debug' => true,

    # App Settings
    'ignore_usernames' => ['twitter', 'your_username'],
    'raffle_admin' => 'your_username', //the MustFollow criteria checks for this info to validate participation
    'raffle_rules' => [ 'App\\Rules\\DefaultRule' ],

    # Twitter API v2 Bearer Token
    'twitter_bearer_token' => 'TWITTER_BEARER_TOKEN',

    # Twitter API v1 Tokens
    'twitter_api_key' => 'TWITTER_API_KEY',
    'twitter_api_secret' => 'TWITTER_API_SECRET',
    'twitter_access_token' => 'USER_ACCESS_TOKEN',
    'twitter_token_secret' => 'USER_ACCESS_TOKEN_SECRET',
];

```

You should also configure the `ignore_usernames` and `raffle_admin` settings to include your own Twitter username. 

The `raffle_rules` array defines which rules will be applied to validate a user's participation. The `DefaultRule` is a dummy rule and will allow anyone to participate. To enforce only followers, you should change this to `MustFollow`.

Don't forget to save the file when you're done.

## Running Rafflebird

At the moment, there are two main commands: `fetch` and `pick`. Both will require you to provide a valid tweet ID as parameter.

To get a Tweet's ID, open the tweet in a dedicated browser window and check the URL, you'll find the tweet ID as the last numeric portion of it right after `/status/`:

```
https://twitter.com/erikaheidi/status/1405904174634651649
```

You can fetch the replies for a tweet with:

```shell
docker-compose exec app php rafflebird fetch replies id=1405904174634651649
```

![Fetching replies](https://user-images.githubusercontent.com/293241/122918835-d68f4700-d35f-11eb-9207-b8fa099815ef.png)

To pick a random winner from the participants, run:

```shell
docker-compose exec app php rafflebird pick random id=1405904174634651649
```

To pick the reply with the most likes, run:

```shell
docker-compose exec app php rafflebird pick best id=1405904174634651649
```

![Rafflebird giveaway for most liked reply on the given tweet](https://user-images.githubusercontent.com/293241/122918814-cf683900-d35f-11eb-974b-126566c3c9e8.png)
