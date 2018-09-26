# Prion Error
Return API Errors consistently throughout an application. Allow users to customize their response messages.

## Setup

### Lumen

In order to use this plugin you must include the following in your app file:
`$app->register(Error\ErrorServiceProvider::class);`

Also, replace the default error handler with:

`$app->singleton(
     Illuminate\Contracts\Debug\ExceptionHandler::class,
     Error\Exceptions\Handler::class
 );`