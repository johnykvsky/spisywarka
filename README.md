# Spisywarka

[![Software License][ico-license]](LICENSE.md)

Yet another boilerplate for Symfony (currently: 4.3). It's both, API and admin panel for simple service: manage what do I have, in witch formats I have it, and to whom I have loaned some things. Sometimes it's important to know if book is pdf or epub, was translation made by person A or person B, does this album is first printing from year xxxx or it's a remaster from year yyyy. And to whom I have loaned that book... And did I bought that cd/book or just wanted to, but give it up waiting for discounts... And we can pack things up in collections to see ie. all Fantasy items (books, movies, soundtracks...) or all stuff related to Blade Runner.

Technical info: Symfony 4.3 on PHP 7.2 with use of Message Bus and UUIDs (no auto increment, yay)

What's included, frontend:

 * Full Item, Category, Collection, Loan management via admin panel
 * User registration, login, logout and profile
 * SB Admin 2 template with dark sidebar
 * Modal confirmation for record delete and logout
 * Select2 autocomplete for Item/Category/Collection searching via ajax request

What's included, backend:

 * Full Item, Category, Collection, Loan management via JSON API
 * Frontend CRUD use core API backend parts: Commands and CommandHandlers
 * API documentation (Swagger) via /api/doc
 * Full use of Message Bus, Requests, Commands and Command Handlers
 * Support for Event Bus (working, just not doing anything serious)
 * Forms with use of DTO and DataTransformers, no entities
 * JWT Token Authenticator
 * Tests included!
 * PhpDocs all the way!
 * Type Hinting friendly!
 * Phpstan on board!
 * PhpStorm will love this...

What's planned:
 
 * Guest user pages, for showing single items and items in given category/collection
 * User management
 * Allow for multiple users see only their own data (via Doctrine filters)
 * Translations

Current API flow:
 * Initial validation of incoming request data on Request
 * If validation fail, request is stopped here, errors are returned to client
 * If validation pass, data is transfered to Command and dispatched to CommandHandler
 * CommandHandler double check everything, try to do the job and throw custom Exception if anything goes wrong

Current CRUD flow:
 * Existing data is transfered to DTO or empty DTO is created
 * After submit, form make first step validation of send data
 * If validation fail, request is stopped here, errors are shown to the user
 * If validation pass, data is transfered from DTO to Command and dispatched to CommandHandler
 * CommandHandler double check everything, try to do the job and throw custom Exception if anything goes wrong
 * Any CommandHandler errors are attached to form and shown to the user

## Install

Download/clone repository, go into, update .env with your database settings and:

``` bash
$ composer install
$ bin/console doctrine:migrations:migrate
```

Then fire up your web server - can be `php -S localhost:8000` from ./public/

## Authorization

For CRUD, go to `/register` (to create user) and later to `/login`

For API, go to `/register` (to create user) and later to `/api/login`, sent POST request with email and password. You will get `token` as a response - put it in header Authorization, "Bearer _token_" and start making requests. Token is valid for 30 minutes, to refresh just go (with valid token in Authorization header) to `/api/jwt/validate` to get new token.

For full API documentation go to `/api/doc`

## Testing

``` bash
$ composer test
```

## Phpstan

``` bash
$ composer phpstan
# or for level 7
$ composer phpstan-max
```


## Security

If you discover any security related issues, please email johnykvsky@protonmail.com instead of using the issue tracker.

## Credits

- [johnykvsky][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-author]: https://github.com/johnykvsky

## Preview

![Listing](https://raw.githubusercontent.com/johnykvsky/spisywarka/master/spisywarka-s1.png)

![Editing](https://raw.githubusercontent.com/johnykvsky/spisywarka/master/spisywarka-s2.png)
