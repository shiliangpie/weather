<?php

namespace Shiliangpie\Weather;

use GuzzleHttp\Client;
use Shiliangpie\Weather\Exceptions\HttpException;
use Shiliangpie\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    protected $key;

    protected $guzzleOptions = [];

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param $city
     * @param string $type
     * @param string $format
     *
     * @return mixed|string
     *
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getWeather($city, $type = 'live', $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        $types = [
            'live' => 'base',
            'forecast' => 'all',
        ];

        if (!\in_array(\strtolower($format), ['json', 'xml'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        if (!\in_array(\strtolower($type), array_keys($types))) {
            throw new InvalidArgumentException('Invalid type value(live/forecast): '.$type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $types[$type],
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $city
     * @param string $format
     *
     * @return mixed|string
     *
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getLiveWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'live', $format);
    }

    /**
     * @param $city
     * @param string $format
     *
     * @return mixed|string
     *
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getForecastsWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'forecast', $format);
    }
}
