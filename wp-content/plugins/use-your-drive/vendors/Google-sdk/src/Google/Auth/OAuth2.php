<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!class_exists('UYDGoogle_Client')) {
    require_once dirname(__FILE__).'/../autoload.php';
}

/**
 * Authentication class that deals with the OAuth 2 web-server authentication flow.
 */
class UYDGoogle_Auth_OAuth2 extends UYDGoogle_Auth_Abstract
{
    public const OAUTH2_REVOKE_URI = 'https://accounts.google.com/o/oauth2/revoke';
    public const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';
    public const OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
    public const CLOCK_SKEW_SECS = 300; // five minutes in seconds
    public const AUTH_TOKEN_LIFETIME_SECS = 300; // five minutes in seconds
    public const MAX_TOKEN_LIFETIME_SECS = 86400; // one day in seconds
    public const OAUTH2_ISSUER = 'accounts.google.com';
    public const OAUTH2_ISSUER_HTTPS = 'https://accounts.google.com';

    /** @var UYDGoogle_Auth_AssertionCredentials */
    private $assertionCredentials;

    /**
     * @var string the state parameters for CSRF and other forgery protection
     */
    private $state;

    /**
     * @var array the token bundle
     */
    private $token = [];

    /**
     * @var UYDGoogle_Client the base client
     */
    private $client;

    /**
     * Instantiates the class, but does not initiate the login flow, leaving it
     * to the discretion of the caller.
     */
    public function __construct(UYDGoogle_Client $client)
    {
        $this->client = $client;
    }

    /**
     * Perform an authenticated / signed apiHttpRequest.
     * This function takes the apiHttpRequest, calls apiAuth->sign on it
     * (which can modify the request in what ever way fits the auth mechanism)
     * and then calls apiCurlIO::makeRequest on the signed request.
     *
     * @return UYDGoogle_Http_Request the resulting HTTP response including the
     *                                responseHttpCode, responseHeaders and responseBody
     */
    public function authenticatedRequest(UYDGoogle_Http_Request $request)
    {
        $request = $this->sign($request);

        return $this->client->getIo()->makeRequest($request);
    }

    /**
     * @param string $code
     * @param bool   $crossClient
     *
     * @return string
     *
     * @throws UYDGoogle_Auth_Exception
     */
    public function authenticate($code, $crossClient = false)
    {
        if (0 == strlen($code)) {
            throw new UYDGoogle_Auth_Exception('Invalid code');
        }

        $arguments = [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'client_id' => $this->client->getClassConfig($this, 'client_id'),
            'client_secret' => $this->client->getClassConfig($this, 'client_secret'),
        ];

        if (true !== $crossClient) {
            $arguments['redirect_uri'] = $this->client->getClassConfig($this, 'redirect_uri');
        }

        // We got here from the redirect from a successful authorization grant,
        // fetch the access token
        $request = new UYDGoogle_Http_Request(
            self::OAUTH2_TOKEN_URI,
            'POST',
            [],
            $arguments
        );
        $request->disableGzip();
        $response = $this->client->getIo()->makeRequest($request);

        if (200 == $response->getResponseHttpCode()) {
            $this->setAccessToken($response->getResponseBody());
            $this->token['created'] = time();

            return $this->getAccessToken();
        }
        $decodedResponse = json_decode($response->getResponseBody(), true);
        if (null != $decodedResponse && $decodedResponse['error']) {
            $errorText = $decodedResponse['error'];
            if (isset($decodedResponse['error_description'])) {
                $errorText .= ': '.$decodedResponse['error_description'];
            }
        }

        throw new UYDGoogle_Auth_Exception(
            sprintf(
                "Error fetching OAuth2 access token, message: '%s'",
                $errorText
            ),
            $response->getResponseHttpCode()
        );
    }

    /**
     * Create a URL to obtain user authorization.
     * The authorization endpoint allows the user to first
     * authenticate, and then grant/deny the access request.
     *
     * @param string $scope the scope is expressed as a list of space-delimited strings
     *
     * @return string
     */
    public function createAuthUrl($scope)
    {
        $params = [
            'response_type' => 'code',
            'redirect_uri' => $this->client->getClassConfig($this, 'redirect_uri'),
            'client_id' => $this->client->getClassConfig($this, 'client_id'),
            'scope' => $scope,
            'access_type' => $this->client->getClassConfig($this, 'access_type'),
        ];

        // Prefer prompt to approval prompt.
        if ($this->client->getClassConfig($this, 'prompt')) {
            $params = $this->maybeAddParam($params, 'prompt');
        } else {
            $params = $this->maybeAddParam($params, 'approval_prompt');
        }
        $params = $this->maybeAddParam($params, 'login_hint');
        $params = $this->maybeAddParam($params, 'hd');
        $params = $this->maybeAddParam($params, 'openid.realm');
        $params = $this->maybeAddParam($params, 'include_granted_scopes');

        // If the list of scopes contains plus.login, add request_visible_actions
        // to auth URL.
        $rva = $this->client->getClassConfig($this, 'request_visible_actions');
        if (strpos($scope, 'plus.login') && strlen($rva) > 0) {
            $params['request_visible_actions'] = $rva;
        }

        if (isset($this->state)) {
            $params['state'] = $this->state;
        }

        return self::OAUTH2_AUTH_URL.'?'.http_build_query($params, '', '&');
    }

    /**
     * @param string $token
     *
     * @throws UYDGoogle_Auth_Exception
     */
    public function setAccessToken($token)
    {
        $token = json_decode($token, true);
        if (null == $token) {
            throw new UYDGoogle_Auth_Exception('Could not json decode the token');
        }
        if (!isset($token['access_token'])) {
            throw new UYDGoogle_Auth_Exception('Invalid token format');
        }
        $this->token = $token;
    }

    public function getAccessToken()
    {
        return json_encode($this->token);
    }

    public function getRefreshToken()
    {
        if (array_key_exists('refresh_token', $this->token)) {
            return $this->token['refresh_token'];
        }

        return null;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function setAssertionCredentials(UYDGoogle_Auth_AssertionCredentials $creds)
    {
        $this->assertionCredentials = $creds;
    }

    /**
     * Include an accessToken in a given apiHttpRequest.
     *
     * @return UYDGoogle_Http_Request
     *
     * @throws UYDGoogle_Auth_Exception
     */
    public function sign(UYDGoogle_Http_Request $request)
    {
        // add the developer key to the request before signing it
        if ($this->client->getClassConfig($this, 'developer_key')) {
            $request->setQueryParam('key', $this->client->getClassConfig($this, 'developer_key'));
        }

        // Cannot sign the request without an OAuth access token.
        if (null == $this->token && null == $this->assertionCredentials) {
            return $request;
        }

        // Check if the token is set to expire in the next 30 seconds
        // (or has already expired).
        if ($this->isAccessTokenExpired()) {
            if ($this->assertionCredentials) {
                $this->refreshTokenWithAssertion();
            } else {
                $this->client->getLogger()->debug('OAuth2 access token expired');
                if (!array_key_exists('refresh_token', $this->token)) {
                    $error = 'The OAuth 2.0 access token has expired,'
                            .' and a refresh token is not available. Refresh tokens'
                            .' are not returned for responses that were auto-approved.';

                    $this->client->getLogger()->error($error);

                    throw new UYDGoogle_Auth_Exception($error);
                }

                // Call Update function of plugin itself
                do_action('use-your-drive-refresh-token', \TheLion\UseyourDrive\App::instance()->get_current_account());

                $this->refreshToken($this->getRefreshToken());
            }
        }

        $this->client->getLogger()->debug('OAuth2 authentication');

        // Add the OAuth2 header to the request
        $request->setRequestHeaders(
            ['Authorization' => 'Bearer '.$this->token['access_token']]
        );

        return $request;
    }

    /**
     * Fetches a fresh access token with the given refresh token.
     *
     * @param string $refreshToken
     */
    public function refreshToken($refreshToken)
    {
        $this->refreshTokenRequest(
            [
                'client_id' => \TheLion\UseyourDrive\App::instance()->get_app_key(),
                'client_secret' => \TheLion\UseyourDrive\App::instance()->get_app_secret(),
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]
        );
    }

    /**
     * Fetches a fresh access token with a given assertion token.
     *
     * @param UYDGoogle_Auth_AssertionCredentials $assertionCredentials optional
     */
    public function refreshTokenWithAssertion($assertionCredentials = null)
    {
        if (!$assertionCredentials) {
            $assertionCredentials = $this->assertionCredentials;
        }

        $cacheKey = $assertionCredentials->getCacheKey();

        if ($cacheKey) {
            // We can check whether we have a token available in the
            // cache. If it is expired, we can retrieve a new one from
            // the assertion.
            $token = $this->client->get_cache()->get($cacheKey);
            if ($token) {
                $this->setAccessToken($token);
            }
            if (!$this->isAccessTokenExpired()) {
                return;
            }
        }

        $this->client->getLogger()->debug('OAuth2 access token expired');
        $this->refreshTokenRequest(
            [
                'grant_type' => 'assertion',
                'assertion_type' => $assertionCredentials->assertionType,
                'assertion' => $assertionCredentials->generateAssertion(),
            ]
        );

        if ($cacheKey) {
            // Attempt to cache the token.
            $this->client->get_cache()->set(
                $cacheKey,
                $this->getAccessToken()
            );
        }
    }

    /**
     * Revoke an OAuth2 access token or refresh token. This method will revoke the current access
     * token, if a token isn't provided.
     *
     * @param null|string $token the token (access token or a refresh token) that should be revoked
     *
     * @return bool returns True if the revocation was successful, otherwise False
     *
     * @throws UYDGoogle_Auth_Exception
     */
    public function revokeToken($token = null)
    {
        if (!$token) {
            if (!$this->token) {
                // Not initialized, no token to actually revoke
                return false;
            }
            if (array_key_exists('refresh_token', $this->token)) {
                $token = $this->token['refresh_token'];
            } else {
                $token = $this->token['access_token'];
            }
        }
        $request = new UYDGoogle_Http_Request(
            self::OAUTH2_REVOKE_URI,
            'POST',
            [],
            "token={$token}"
        );
        $request->disableGzip();
        $response = $this->client->getIo()->makeRequest($request);
        $code = $response->getResponseHttpCode();
        if (200 == $code) {
            $this->token = null;

            return true;
        }

        return false;
    }

    /**
     * Returns if the access_token is expired.
     *
     * @return bool returns True if the access_token is expired
     */
    public function isAccessTokenExpired()
    {
        if (!$this->token || !isset($this->token['created'])) {
            return true;
        }

        // If the token is set to expire in the next 120 seconds.
        return ($this->token['created']
            + ($this->token['expires_in'] - 120)) < time();
    }

    /**
     * Retrieve and cache a certificates file.
     *
     * @param $url string location
     *
     * @return array certificates
     *
     * @throws UYDGoogle_Auth_Exception
     */
    public function retrieveCertsFromLocation($url)
    {
        // If we're retrieving a local file, just grab it.
        if ('http' != substr($url, 0, 4)) {
            $file = file_get_contents($url);
            if ($file) {
                return json_decode($file, true);
            }

            throw new UYDGoogle_Auth_Exception(
                "Failed to retrieve verification certificates: '".
                $url."'."
            );
        }

        // This relies on makeRequest caching certificate responses.
        $request = $this->client->getIo()->makeRequest(
            new UYDGoogle_Http_Request(
                $url
            )
        );
        if (200 == $request->getResponseHttpCode()) {
            $certs = json_decode($request->getResponseBody(), true);
            if ($certs) {
                return $certs;
            }
        }

        throw new UYDGoogle_Auth_Exception(
            "Failed to retrieve verification certificates: '".
            $request->getResponseBody()."'.",
            $request->getResponseHttpCode()
        );
    }

    /**
     * Verifies an id token and returns the authenticated apiLoginTicket.
     * Throws an exception if the id token is not valid.
     * The audience parameter can be used to control which id tokens are
     * accepted.  By default, the id token must have been issued to this OAuth2 client.
     *
     * @param $id_token
     * @param $audience
     *
     * @return UYDGoogle_Auth_LoginTicket
     */
    public function verifyIdToken($id_token = null, $audience = null)
    {
        if (!$id_token) {
            $id_token = $this->token['id_token'];
        }
        $certs = $this->getFederatedSignonCerts();
        if (!$audience) {
            $audience = $this->client->getClassConfig($this, 'client_id');
        }

        return $this->verifySignedJwtWithCerts(
            $id_token,
            $certs,
            $audience,
            [self::OAUTH2_ISSUER, self::OAUTH2_ISSUER_HTTPS]
        );
    }

    /**
     * Verifies the id token, returns the verified token contents.
     *
     * @param $jwt string the token
     * @param $certs array of certificates
     * @param $required_audience string the expected consumer of the token
     * @param [$issuer] the expected issues, defaults to Google
     * @param [$max_expiry] the max lifetime of a token, defaults to MAX_TOKEN_LIFETIME_SECS
     *
     * @return mixed token information if valid, false if not
     *
     * @throws UYDGoogle_Auth_Exception
     */
    public function verifySignedJwtWithCerts(
        $jwt,
        $certs,
        $required_audience,
        $issuer = null,
        $max_expiry = null
    ) {
        if (!$max_expiry) {
            // Set the maximum time we will accept a token for.
            $max_expiry = self::MAX_TOKEN_LIFETIME_SECS;
        }

        $segments = explode('.', $jwt);
        if (3 != count($segments)) {
            throw new UYDGoogle_Auth_Exception("Wrong number of segments in token: {$jwt}");
        }
        $signed = $segments[0].'.'.$segments[1];
        $signature = UYDGoogle_Utils::urlSafeB64Decode($segments[2]);

        // Parse envelope.
        $envelope = json_decode(UYDGoogle_Utils::urlSafeB64Decode($segments[0]), true);
        if (!$envelope) {
            throw new UYDGoogle_Auth_Exception("Can't parse token envelope: ".$segments[0]);
        }

        // Parse token
        $json_body = UYDGoogle_Utils::urlSafeB64Decode($segments[1]);
        $payload = json_decode($json_body, true);
        if (!$payload) {
            throw new UYDGoogle_Auth_Exception("Can't parse token payload: ".$segments[1]);
        }

        // Check signature
        $verified = false;
        foreach ($certs as $keyName => $pem) {
            $public_key = new UYDGoogle_Verifier_Pem($pem);
            if ($public_key->verify($signed, $signature)) {
                $verified = true;

                break;
            }
        }

        if (!$verified) {
            throw new UYDGoogle_Auth_Exception("Invalid token signature: {$jwt}");
        }

        // Check issued-at timestamp
        $iat = 0;
        if (array_key_exists('iat', $payload)) {
            $iat = $payload['iat'];
        }
        if (!$iat) {
            throw new UYDGoogle_Auth_Exception("No issue time in token: {$json_body}");
        }
        $earliest = $iat - self::CLOCK_SKEW_SECS;

        // Check expiration timestamp
        $now = time();
        $exp = 0;
        if (array_key_exists('exp', $payload)) {
            $exp = $payload['exp'];
        }
        if (!$exp) {
            throw new UYDGoogle_Auth_Exception("No expiration time in token: {$json_body}");
        }
        if ($exp >= $now + $max_expiry) {
            throw new UYDGoogle_Auth_Exception(
                sprintf('Expiration time too far in future: %s', $json_body)
            );
        }

        $latest = $exp + self::CLOCK_SKEW_SECS;
        if ($now < $earliest) {
            throw new UYDGoogle_Auth_Exception(
                sprintf(
                    'Token used too early, %s < %s: %s',
                    $now,
                    $earliest,
                    $json_body
                )
            );
        }
        if ($now > $latest) {
            throw new UYDGoogle_Auth_Exception(
                sprintf(
                    'Token used too late, %s > %s: %s',
                    $now,
                    $latest,
                    $json_body
                )
            );
        }

        // support HTTP and HTTPS issuers
        // @see https://developers.google.com/identity/sign-in/web/backend-auth
        $iss = $payload['iss'];
        if ($issuer && !in_array($iss, (array) $issuer)) {
            throw new UYDGoogle_Auth_Exception(
                sprintf(
                    'Invalid issuer, %s not in %s: %s',
                    $iss,
                    '['.implode(',', (array) $issuer).']',
                    $json_body
                )
            );
        }

        // Check audience
        $aud = $payload['aud'];
        if ($aud != $required_audience) {
            throw new UYDGoogle_Auth_Exception(
                sprintf(
                    'Wrong recipient, %s != %s:',
                    $aud,
                    $required_audience,
                    $json_body
                )
            );
        }

        // All good.
        return new UYDGoogle_Auth_LoginTicket($envelope, $payload);
    }

    private function refreshTokenRequest($params)
    {
        if (isset($params['assertion'])) {
            $this->client->getLogger()->info(
                'OAuth2 access token refresh with Signed JWT assertion grants.'
            );
        } else {
            $this->client->getLogger()->info('OAuth2 access token refresh');
        }

        $http = new UYDGoogle_Http_Request(
            self::OAUTH2_TOKEN_URI,
            'POST',
            [],
            $params
        );
        $http->disableGzip();

        // FIX: In case token needs to be refreshed during a streamed download
        // Store curl options for current call,
        // and reset to default curl options for auth request
        $current_options = $this->client->getIo()->getOptions();
        $this->client->getIo()->setDefaultOptions();
        $old_handler = $this->client->getIo()->getHandler();
        if (!empty($old_handler)) {
            curl_close($old_handler);
        }
        // END FIX

        $request = $this->client->getIo()->makeRequest($http);

        // FIX: In case token needs to be refreshed during a streamed download
        // Restore original curl request options
        $this->client->getIo()->setOptions($current_options);
        // END FIX

        $code = $request->getResponseHttpCode();
        $body = $request->getResponseBody();
        if (200 == $code) {
            $token = json_decode($body, true);
            if (null == $token) {
                throw new UYDGoogle_Auth_Exception('Could not json decode the access token');
            }

            if (!isset($token['access_token']) || !isset($token['expires_in'])) {
                throw new UYDGoogle_Auth_Exception('Invalid token format');
            }

            if (isset($token['id_token'])) {
                $this->token['id_token'] = $token['id_token'];
            }
            $this->token['access_token'] = $token['access_token'];
            $this->token['expires_in'] = $token['expires_in'];
            $this->token['created'] = time();
        } else {
            throw new UYDGoogle_Auth_Exception("Error refreshing the OAuth2 token, message: '{$body}'", $code);
        }
    }

    // Gets federated sign-on certificates to use for verifying identity tokens.
    // Returns certs as array structure, where keys are key ids, and values
    // are PEM encoded certificates.
    private function getFederatedSignOnCerts()
    {
        return $this->retrieveCertsFromLocation(
            $this->client->getClassConfig($this, 'federated_signon_certs_url')
        );
    }

    /**
     * Add a parameter to the auth params if not empty string.
     *
     * @param mixed $params
     * @param mixed $name
     */
    private function maybeAddParam($params, $name)
    {
        $param = $this->client->getClassConfig($this, $name);
        if ('' != $param) {
            $params[$name] = $param;
        }

        return $params;
    }
}
