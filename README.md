Cloakings CloakIT
=================

Detect if user is bot or real user using mr-clo.com

## Install

```bash
composer require cloakings/cloakings-mrclo
```

## Usage

### Basic Usage

Register at https://www.mr-clo.com:
- Look for token in dashboard page
- Create domain

```php
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$cloaker = \Cloakings\CloakingsMrClo\MrCloCloaker(
    token: $token
);
$cloakerResult = $cloaker->handle($request);
```

Check if result mode is `CloakModeEnum::Fake` or `CloakModeEnum::Real` and do something with it.

If you want to render result like the original MrClo library
```php
$baseIncludeDir = __DIR__; // change to your dir with real.php and fake.php
$renderer = \Cloakings\CloakingsMrClo\MrCloRenderer();
$response = $renderer->render($cloakerResult);
```

You can change params creating your own `MrCloParams`

```php
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$cloaker = \Cloakings\CloakingsMrClo\MrCloCloaker(
    token: $token,
    params: \Cloakings\CloakingsMrClo\MrCloParams(
        source: \Cloakings\CloakingsMrClo\MrCloSourceEnum::GoogleSearch,
        blockIos: true,
    )
);
$cloakerResult = $cloaker->handle($request);
```

## Original Logic

Original library is located at `doc/original`.

License for this repository doesn't cover that code.
