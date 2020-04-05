<?php
namespace App\Services\Api;

use App\Exceptions\ApiException;

abstract class ApiService
{
    /**
     * @var string OAuth token
     */
    protected $token;

    /**
     * @var string OAuth token secret
     */
    protected $tokenSecret;

    /**
     * @var string OAuth key
     */
    protected $consumerKey;

    /**
     * @var string OAuth secret key
     */
    protected $consumerSecret;

    /**
     * @var string api host
     */
    protected $host;

    /**
     * @var string auth header
     */
    protected $auth;

    /**
     * @var string
     */
    protected $query;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    public function __construct()
    {
        $this->getConfig();
    }

    protected abstract function getConfig(): void;

    /**
     * @param array $query
     * @param string $method
     * @param string $path
     */
    protected function buildAuth(array $query, string $method, string $path): void
    {
        $oauth = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_token' => $this->token,
            'oauth_nonce' => (string)mt_rand(),
            'oauth_timestamp' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0'
        ];

        $oauth = array_map("rawurlencode", $oauth);
        $query = array_map("rawurlencode", $query);

        $arr = array_merge($oauth, $query);

        asort($arr);
        ksort($arr);

        $queryString = urldecode(http_build_query($arr, '', '&'));

        $url = "https://{$this->host}{$path}";

        $baseString = $method."&".rawurlencode($url)."&".rawurlencode($queryString);

        $key = rawurlencode($this->consumerSecret)."&".rawurlencode($this->tokenSecret);

        $signature = rawurlencode(base64_encode(hash_hmac('sha1', $baseString, $key, true)));

        $oauth['oauth_signature'] = $signature;
        ksort($oauth);

        $addQuotes = function ($str) { return '"'.$str.'"'; };
        $oauth = array_map($addQuotes, $oauth);

        $this->auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $query
     * @return array|null
     * @throws ApiException
     */
    public function request(string $method, string $path, array $query = []): ?array
    {
        $this->buildAuth($query, $method, $path);

        $url = "https://{$this->host}{$path}";
        $url .= "?".http_build_query($query);
        $url = str_replace("&amp;","&",$url);

        $options = [
            CURLOPT_HTTPHEADER => ["Authorization: $this->auth"],
            //CURLOPT_POSTFIELDS => $postfields,                    // TODO: Add POST functionality
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);

        if (($error = curl_error($feed)) !== '')
        {
            curl_close($feed);

            throw new ApiException($error);
        }

        curl_close($feed);

        $twitterData = json_decode($json);

        return $twitterData;
    }
}
