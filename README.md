# Scarpinocc

Scarpinocc is a simple PHP Library for managing recurring time periods.

The library takes its name from a type of filled pasta (called "[Scarpinocc](https://it.wikipedia.org/wiki/Scarpinocc)") typical of Parre town in northern Italy. As the filling can be inside or outside a single Scarpinocc piece, the library can be used to check if a timestamp is contained in or not in a single Period.

Its main objective is to manage automatically scheduled maintenance periods, when the application must be unavailable or a certain operation should not be triggered.

## Functionalities

Scarpinocc can currently manage five different types of periods:

- **Weekly Periods**: Periods that repeat themselves every 7 days
- **Daily Periods**: Periods that repeat themselves every 24 hours
- **Once Periods**: Single events that happen only once and never again
- **Always Periods**: Periods that are perpetually active
- **Never Periods**: Periods that are never active

Each period is defined by its **Edges**, which declare when a period starts or finishes.

The library makes it possible to combine multiple periods; a timestamp will be considered contained if at least one of the periods contains it.

The periods can be declared directly from the code or by a JSON string; this makes it possible to store the configuration somewhere and load it dynamically when needed.

### Weekly Periods

A Weekly Period is defined by day/hour edges, for example:

```json
{
  "name": "scheduled maintenance",
  "description": "update indexes",
  "type": "weekly",
  "from": {
    "day": "saturday",
    "hour": "23:00"
  },
  "to": {
    "day": "sunday",
    "hour": "07:00"
  }
}
```

In this case, the period starts every Saturday at 23:00 and ends the next Sunday at 07:00 local time.

### Daily Periods

A Daily Period is defined by hour edges and repeats every day, for example:

```json
{
  "name": "business hours",
  "description": "office working hours",
  "type": "daily",
  "from": {
    "hour": "09:00"
  },
  "to": {
    "hour": "17:30"
  }
}
```

In this case, the period is active every day from 09:00 to 17:30 local time.

Daily periods can also cross midnight:

```json
{
  "name": "night shift",
  "description": "overnight maintenance window",
  "type": "daily",
  "from": {
    "hour": "22:00"
  },
  "to": {
    "hour": "06:00"
  }
}
```

This period is active every day from 22:00 to 06:00 the next day.

### Once Periods

A Once period is defined by timestamp edges, for example:

```json
{
  "name": "service interruption",
  "description": "as defined in mail 18/02/2025",
  "type": "once",
  "from": {
    "timestamp": "2025-02-20 12:30:00"
  },
  "to": {
    "timestamp": "2025-02-20 14:30:00"
  }
}
```

In this case, the period starts at 12:30 on 20 February 2025 and ends at 14:30 on the same day.

### Always Periods

An Always Period is perpetually active and has no defined start or end:

```json
{
  "name": "maintenance mode",
  "description": "system permanently under maintenance",
  "type": "always"
}
```

This period will always return true for any timestamp check. It's useful for permanently enabling/disabling features when needed.

### Never Periods

A Never Period is never active:

```json
{
  "name": "disabled feature",
  "description": "feature permanently disabled",
  "type": "never"
}
```

This period will always return false for any timestamp check. It's useful for representing enabled/disabled features or as placeholders in configurations.

## Installation

_Installation via Packagist is not yet available. For now, you can clone the repository and use it locally._

## Usage

### Base example

```php
<?php

require_once 'vendor/autoload.php';

use Scarpinocc\Scarpinocc;
use Carbon\Carbon;

// Period definition
$jsonConfig = '{
    "periods":[
        {
            "name":"scheduled maintenance",
            "description":"update indexes",
            "type":"weekly",
            "from":{
                "day":"saturday",
                "hour":"23:00"
            },
            "to":{
                "day":"sunday",
                "hour":"07:00"
            }
        },
        {
            "name":"business hours",
            "description":"daily working hours",
            "type":"daily",
            "from":{
                "hour":"09:00"
            },
            "to":{
                "hour":"17:30"
            }
        },
        {
            "name":"service interruption",
            "description":"as defined in mail 18/02/2025",
            "type":"once",
            "from":{
                "timestamp":"2025-02-20 12:30:00"
            },
            "to":{
                "timestamp":"2025-02-20 14:30:00"
            }
        },
        {
            "name":"emergency maintenance",
            "description":"system under emergency maintenance",
            "type":"always"
        },
        {
            "name":"placeholder",
            "description":"placeholder",
            "type":"never"
        }
    ]
}';

try {
    $dish = Scarpinocc::fromJson($jsonConfig);

    // Checking if the system is currently offline
    if ($dish->containsNow()) {
        echo "The service is currently offline\n";
    } else {
        echo "The service is currently online\n";
    }

    // Checking a specific time
    $specificTime = Carbon::parse('2025-02-20 13:00:00');
    if ($dish->contains($specificTime)) {
        echo "The specified time is contained in the period\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## API

### `Scarpinocc` methods

- `contains(Carbon $t): bool`: Returns true if `$t` is included in at least one of the periods
- `containsNow(): bool`: Returns true if the current moment is included in the periods
- `fromJson(string $json): Scarpinocc`: Creates a Scarpinocc instance from a JSON string
- `fromArray(array $data): Scarpinocc`: Creates a Scarpinocc instance from an array

### `Period` methods

- `contains(Carbon $t): bool`: Returns true if the moment `$t` is in the period
- `containsNow(): bool`: Returns true if the current moment is in the period

**Note**: Unlike the Go version, the PHP implementation currently focuses on the core functionality of checking if times are contained within periods. Additional temporal methods may be added in future versions.

## Test

The tests can be executed with:

```bash
composer install
vendor/bin/phpunit tests
```

## License

This library is distributed under the MIT license found in the [LICENSE](./LICENSE)
file.

## Related Projects

This library is the PHP port of a similar Go project: [Casoncelli](https://github.com/valmoz/casoncelli)
