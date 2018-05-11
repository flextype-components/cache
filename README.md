# Cache Component
![version](https://img.shields.io/badge/version-1.0.0-brightgreen.svg?style=flat-square "Version")
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/flextype-components/cache/blob/master/LICENSE)

Form component allows you to easily create HTML forms.

### Installation

```
composer require flextype-components/cache
```

### Usage

```php
use Flextype\Component\Cache\Cache;
```

Configure the settings of Cache
```php
Cache::configure('cache_dir', 'path/to/cache/dir');
```

Get data from cache
```php
$profile = Cache::get('profiles', 'profile');
```

Create new cache file $key in namescapce $namespace with the given data $data
```php
$profile = ['login' => 'Awilum',
             'url' => 'http://flextype.org'];
Cache::put('profiles', 'profile', $profile);
```

Deletes a cache in specific namespace
```php
Cache::delete('profiles', 'profile');
```

Clean specific cache namespace
```php
Cache::clean('profiles');
```

## License
See [LICENSE](https://github.com/flextype-components/cache/blob/master/LICENSE)
