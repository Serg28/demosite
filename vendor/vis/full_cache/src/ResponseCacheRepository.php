<?php

namespace Vis\FullCache;

use Illuminate\Support\Facades\Cache;

class ResponseCacheRepository
{
    /**
     * @var \Illuminate\Cache\Repository
     */
    protected $cache;

    /**
     * @var \Vis\FullCache\ResponseSerializer
     */
    protected $responseSerializer;

    /**
     * @var string
     */
    protected $cacheTags = ['settings', 'translations'];

    /**
     *
     * @param \Vis\FullCache\ResponseSerializer     $responseSerializer
     */
    public function __construct(ResponseSerializer $responseSerializer)
    {
        $this->cacheTags = array_merge($this->cacheTags, config('full_cache.tagsCache'));
        $this->responseSerializer = $responseSerializer;
    }

    /**
     * @param string                                     $key
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     */
    public function put($key, $response)
    {
         Cache::tags($this->cacheTags)->forever($key, $this->responseSerializer->serialize($response));
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return Cache::tags($this->cacheTags)->has($key);
    }

    /**
     * @param string $key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get($key)
    {
        return $this->responseSerializer->unserialize(Cache::tags($this->cacheTags)->get($key));
    }

}
