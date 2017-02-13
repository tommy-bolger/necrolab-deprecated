<?php 
namespace League\OAuth2\Client\Provider;

class YouTubeResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param array  $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

    /**
     * Get channel id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->response['items'][0]['id'] ?: null;
    }

    /**
     * Get channel imageurl
     *
     * @return string|null
     */
    public function getImageurl()
    {
        return $this->response['items'][0]['snippet']['thumbnails']['default']['url'] ?: null;
    }

    /**
     * Get channel name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->response['items'][0]['snippet']['title']  ?: null;
    }

    /**
     * Get channel description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->response['items'][0]['snippet']['description'] ?: null;
    }


    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
