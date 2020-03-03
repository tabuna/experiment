<?php

declare(strict_types=1);

namespace Orchid\Experiment;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class Experiment
{
    /**
     * History Store.
     *
     * @var Repository
     */
    private $store;

    /**
     * The key of the experiment for cookie.
     *
     * @var string
     */
    private $key;

    /**
     * Experiment constructor.
     *
     * @param string     $key
     * @param Repository $store
     */
    public function __construct(string $key = 'AB', Repository $store = null)
    {
        $this->store = $store ?? Cache::store();
        $this->key = $key;
    }

    /**
     * @param array $experiments
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return string|null
     */
    public function startAndSaveCookie(array $experiments): ?string
    {
        $ab = $this->start($experiments);

        Cookie::queue(cookie($this->key, $ab));
        $_COOKIE[$this->key] = $ab;

        return $ab;
    }

    /**
     * Begins the experiment, returning its value.
     *
     * @param array $experiments
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return string|null
     */
    public function start(array $experiments): ?string
    {
        if (empty($experiments)) {
            return null;
        }

        $select = self::getCookieValue($this->key);

        if ($select !== null) {
            return $select;
        }

        $prepareExperiments = $this->prepareExperiments($experiments);

        foreach ($experiments as $key => $maxValue) {
            if ($prepareExperiments[$key] >= $maxValue) {
                continue;
            }

            $this->store->increment($key);

            return $key;
        }

        $this->resetHistory($prepareExperiments);

        return $this->start($experiments);
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function getCookieValue(string $key, $default = null)
    {
        return
            request()->get($key)
            ?? request()->cookie($key)
            ?? $_GET[$key]
            ?? $_COOKIE[$key]
            ?? $default;
    }

    /**
     * @param array $experiments
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array
     */
    private function prepareExperiments(array $experiments = []): array
    {
        foreach ($experiments as $key => $experiment) {
            $experiments[$key] = $this->store->get($key);
        }

        return $experiments;
    }

    /**
     * @param array $keys
     */
    private function resetHistory(array $keys): void
    {
        foreach ($keys as $key => $value) {
            $this->store->decrement($key, $value);
        }
    }
}
