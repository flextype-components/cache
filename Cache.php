<?php

/**
 * @package Flextype Components
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://components.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype\Component\Cache;

class Cache
{
    /**
     * Cache directory
     *
     * @var string
     */
    protected static $cache_dir = '';

    /**
     * Cache file ext
     *
     * @var string
     */
    protected static $cache_file_ext = 'txt';

    /**
     * Cache life time (in seconds)
     *
     * @var int
     */
    public static $cache_time = 31556926;

    /**
     * Configure the settings of Cache
     *
     * Cache::configure('cache_dir', 'path/to/cache/dir');
     *
     * @param string $setting Setting name
     * @param mixed  $value   Setting value
     * @return void
     */
    public static function configure(string $setting, $value) : void
    {
        if (property_exists("cache", $setting)) Cache::$$setting = $value;
    }

    /**
     * Get data from cache
     *
     * $profile = Cache::get('profiles', 'profile');
     *
     * @param  string  $namespace Namespace
     * @param  string  $key       Cache key
     * @return bool|mixed
     */
    public static function get(string $namespace, string $key)
    {
        // Get cache file id
        $cache_file_id = Cache::getCacheFileID($namespace, $key);

        // Is cache file exists ?
        if (file_exists($cache_file_id)) {

            // If cache file has not expired then fetch it
            if ((time() - filemtime($cache_file_id)) < Cache::$cache_time) {

               $handle = fopen($cache_file_id, 'r');

               $cache = '';

               while ( ! feof($handle)) {
                   $cache .= fgets($handle);
               }

               fclose($handle);

               return unserialize($cache);

            } else {
                unlink($cache_file_id);

                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Create new cache file $key in namescapce $namespace with the given data $data
     *
     * $profile = ['login' => 'Awilum',
     *             'url' => 'http://flextype.org'];
     * Cache::put('profiles', 'profile', $profile);
     *
     * @param  string  $namespace Namespace
     * @param  string  $key       Cache key
     * @param  mixed   $data      The variable to store
     * @return bool
     */
    public static function put(string $namespace, string $key, $data) : bool
    {
        // Is Cache::$cache_dir directory writable ?
        if (file_exists(Cache::$cache_dir) === false || is_readable(Cache::$cache_dir) === false || is_writable(Cache::$cache_dir) === false) {
            throw new RuntimeException(vsprintf("%s(): Cache directory ('%s') is not writable.", array(__METHOD__, Cache::$cache_dir)));
        }

        // Create namespace
        if ( ! file_exists(Cache::getNamespaceID($namespace))) {
            mkdir(Cache::getNamespaceID($namespace), 0775, true);
        }

        // Write cache to specific namespace
        return file_put_contents(Cache::getCacheFileID($namespace, $key), serialize($data), LOCK_EX);
    }

    /**
     * Deletes a cache in specific namespace
     *
     * Cache::delete('profiles', 'profile');
     *
     * @param  string  $namespace Namespace
     * @param  string  $key       Cache key
     * @return bool
     */
    public static function delete(string $namespace, string $key)
    {
        if (file_exists(Cache::getCacheFileID($namespace, $key))) unlink(Cache::getCacheFileID($namespace, $key)); else return false;
    }

    /**
     * Clean specific cache namespace.
     *
     * Cache::clean('profiles');
     *
     * @param  string $namespace Namespace
     * @return void
     */
    public static function clean(string $namespace) : void
    {
        array_map("unlink", glob(Cache::$cache_dir . DS . md5($namespace) . DS . "*." . Cache::$cache_file_ext));
    }

    /**
     * Get cache file ID
     *
     * @param  string $namespace Namespace
     * @param  string $key       Cache key
     * @return string
     */
    protected static function getCacheFileID(string $namespace, string $key) : string
    {
        return Cache::$cache_dir . DS . md5($namespace) . DS . md5($key) . '.' . Cache::$cache_file_ext;
    }

    /**
     * Get namespace ID
     *
     * @param  string $namespace Namespace
     * @return string
     */
    protected static function getNamespaceID(string $namespace) : string
    {
        return Cache::$cache_dir . DS . md5($namespace);
    }

}
