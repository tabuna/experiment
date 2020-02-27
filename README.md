# Experiment


<a href="https://travis-ci.org/tabuna/experiment/"><img src="https://travis-ci.org/tabuna/experiment.svg?branch=master"></a>
<a href="https://styleci.io/repos/159730043"><img src="https://styleci.io/repos/159730043/shield?branch=master"/></a>
<a href="https://codecov.io/gh/orchidsoftware/platform"><img src="https://codecov.io/gh/orchidsoftware/experiment/branch/master/graph/badge.svg" /></a>
<a href="https://packagist.org/packages/orchid/experiment"><img src="https://poser.pugx.org/orchid/experiment/v/stable"/></a>
<a href="https://packagist.org/packages/orchid/experiment"><img src="https://poser.pugx.org/orchid/experiment/downloads"/></a>
<a href="https://packagist.org/packages/orchid/experiment"><img src="https://poser.pugx.org/orchid/experiment/license"/></a>


An A/B Testing suite for Laravel which allows multiple experiments.


## Installation

Download using Composer:
```php
$ composer require orchid/experiment
```

### Base Usage

Your cache driver will be used by default.

```php
$experiment = new Experiment();

// Distribution
$ab = $experiment->start([
    'A' => 1,
    'B' => 1,
]); // A or B

```

The experiment is transmitted in the form of an array, where the keys are the names, and the values are the required ratios.
For example, if you specify two values containing A -> 50 and B -> 100, then first there will be 50 users with the value A, then there will be 100 users with the value B. 
This allows us to clearly define how the testing will be distributed.


```php
$storage = Cache::store('redis');
$experiment = new Experiment('my-key',$storage);

$ab = $experiment->start([
    'A' => 50,
    'B' => 100,
]); // A or B
```

You can also install via your request:

```bash
http:://example.com?my-key=A
```

### Cookie

I recommend putting this on an middleware and immediately install a cookie using

```php
namespace App\Http\Middleware;

use Closure;
use Orchid\Experiment\Experiment;

class Experiments
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        $experiment = new Experiment('AB');

        $experiment->startAndSaveCookie([
            'A' => 50,
            'B' => 50,
        ]);
        
        return $next($request);
    }
}
```

This allows you to transfer data to Google analytics and similar services using javascript

```javascript
alert( document.cookie );
```

Laravel encrypts all cookies by default, so do not forget to specify your key in the exceptions `app/Http/Middleware/EncryptCookies.php`:

```php
namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'AB'
    ];
}
```

### Blade

If you want to use the blade, you still must install the middleware after this call is as example:

```php
@experiment('my-key', 'A')
    <button>Click me</button>
@else
    <button>Push me</button>
@endexperiment
```


#### Tests

```bash
php vendor/bin/phpunit --coverage-html ./logs/coverage ./tests
```


## Donate & Support

If you would like to support development by making a donation you can do so [here](https://www.paypal.me/tabuna/10usd). &#x1F60A;


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

