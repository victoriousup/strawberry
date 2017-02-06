## DSBerry.com 

This is the Laravel 5.2 project for the DSBerry.com app server.

## Getting Started

We recommend using [Laravel Homestead](https://laravel.com/docs/5.2/homestead) for local development.

### Environment Variables

You'll need to set up the ```.env``` file on your local machine. You can use the ```env.example``` file to get started. Most of the application will work without any API keys. Request access to the password manager if you need a testing API key to test specific services such as S3, CDN, Helpscout, etc.

### Database Migration

Run the database migrations to set up the database:

```
$ php artisan migration
```

### Other Services

You also may wish to populate some initial stock photos to be able to build packs, etc. Run the command:

```
$ php artisan stock-photos:import 20
```
That will import 20 stock photos into the app and storage.

If you are testing purchasing APIs you may wish to import the latest exchange rates:

```
$ php artisan update-exchange-rates
```

## Testing with Ngrok

If you need to work with the CDN system you may wish to test using the [Ngrok](https://ngrok.com/) tunnel service. We have set up the CDN at ```https://cdn-local.dsberry.com``` to pull from the Ngrok domain ```https://dsberry.ngrok.com```.

```
$ ngrok http -subdomain=dsberry 8000
```

