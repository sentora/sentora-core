<?php
 
namespace Analogic\ACME;

use \RuntimeException;
use \Psr\Log\LoggerInterface;

class Lescript
{
    public $ca = 'https://acme-v02.api.letsencrypt.org'; // PRODUCTION ONLY
    //public $ca = 'https://acme-staging-v02.api.letsencrypt.org'; // TESTING ONLY!!!!!
    public $countryCode;
    public $state;
    public $challenge = 'http-01'; # http-01 challange only
    public $contact = array(); # optional

	public $clientUserAgent = "analogic-lescript/0.3.0";
	
    protected $certificatesDir;
    protected $webRootDir;

    /** @var LoggerInterface */
    protected $logger;
    /** @var ClientInterface */
    protected $client;
    protected $accountKeyPath;

    protected $accountId = '';
    protected $urlNewAccount = '';
    protected $urlNewNonce = '';
    protected $urlNewOrder = '';
	
	# NEW CODE - tg - Added counntry code and state
    public function __construct($accountDir, $certificatesDir, $webRootDir, $logger = NULL, $countryCode, $state, ClientInterface $client = NULL)
    {
		$this->accountDir = $accountDir;
        $this->certificatesDir = $certificatesDir;
        $this->webRootDir = $webRootDir;
        $this->logger = $logger;
		$this->countryCode = $countryCode;
		$this->state = $state;
        $this->client = $client ? $client : new Client($this->ca, $this->clientUserAgent);
        $this->accountKeyPath = $accountDir . '_account/private.pem';
    }

    public function initAccount()
    {
        $this->initCommunication();

        if (!is_file($this->accountKeyPath)) {

            # generate and save new private key for account
            # ---------------------------------------------
            $this->log('Starting new account registration');
            $this->generateKey(dirname($this->accountKeyPath));
            $this->postNewReg();
            $this->log('New account certificate registered');
			
        } else {
            $this->log('Account already registered. Continuing.');
            $this->getAccountId();
        }

        if (empty($this->accountId)) {
            throw new RuntimeException("We don't have account ID");
        }

        $this->log("Account: " . $this->accountId);
    }

    public function initCommunication()
    {
		$this->log('ACME Client: '.$this->clientUserAgent);
        $this->log('Getting list of URLs for API');

        $directory = $this->client->get('/directory');
        if (!isset($directory['newNonce']) || !isset($directory['newAccount']) || !isset($directory['newOrder']) || !isset($directory['revokeCert']) ) {
            throw new RuntimeException("Missing setup urls");
        }

        $this->urlNewNonce = $directory['newNonce'];
        $this->urlNewAccount = $directory['newAccount'];
        $this->urlNewOrder = $directory['newOrder'];
		$this->urlRevokeCert = $directory['revokeCert'];

        $this->log('Requesting new nonce for client communication');
        $this->client->get($this->urlNewNonce);
    }

    public function signDomains(array $domains, $reuseCsr = false)
    {
        $this->log('Starting certificate generation process for domains');

        $privateAccountKey = $this->readPrivateKey($this->accountKeyPath);
        $accountKeyDetails = openssl_pkey_get_details($privateAccountKey);

        # start domains authentication
        # ----------------------------
        $this->log("Requesting challenge for ".join(', ', $domains));
        $response = $this->signedRequest(
            $this->urlNewOrder,
            array("identifiers" => array_map(
                function ($domain) { 
					return array("type" => "dns", "value" => $domain);
				}, 
                $domains
                ))
        );

        $finalizeUrl = $response['finalize'];

        foreach ($response['authorizations'] as $authz) {
            # 1. getting authentication requirements
            # --------------------------------------
            $response = $this->signedRequest($authz, "");
            $domain = $response['identifier']['value'];
            if (empty($response['challenges'])) {
                throw new RuntimeException("HTTP Challenge for $domain is not available. Whole response: " . json_encode($response));
            }

            $self = $this;
            $challenge = array_reduce($response['challenges'], function ($v, $w) use (&$self) {
                return $v ? $v : ($w['type'] == $self->challenge ? $w : false);
            });
            if (!$challenge) throw new RuntimeException("HTTP Challenge for $domain is not available. Whole response: " . json_encode($response));

            $this->log("Got challenge token for $domain");

            # 2. saving authentication token for web verification
            # ---------------------------------------------------
            $directory = $this->webRootDir . '/.well-known/acme-challenge';
            $tokenPath = $directory . '/' . $challenge['token'];

            if (!file_exists($directory) && !@mkdir($directory, 0755, true)) {
                throw new RuntimeException("Couldn't create directory to expose challenge: ${tokenPath}");
            }

            $header = array(
                # need to be in precise order!
                "e" => Base64UrlSafeEncoder::encode($accountKeyDetails["rsa"]["e"]),
                "kty" => "RSA",
                "n" => Base64UrlSafeEncoder::encode($accountKeyDetails["rsa"]["n"])

            );
            $payload = $challenge['token'] . '.' . Base64UrlSafeEncoder::encode(hash('sha256', json_encode($header), true));

            file_put_contents($tokenPath, $payload);
            chmod($tokenPath, 0644);

            # 3. verification process itself
            # -------------------------------
            $uri = "http://${domain}/.well-known/acme-challenge/${challenge['token']}";

            $this->log("Token for $domain saved at $tokenPath and should be available at $uri");

            $this->log("Sending request to challenge");
                
            # send request to challenge
            $maxAllowedLoops = 6;
            $loopCount = 1;
            $result = null;
            while ($loopCount < $maxAllowedLoops) {
                $result = $this->signedRequest(
                    $challenge['url'],
                    array("keyAuthorization" => $payload)
                );

                if (empty($result['status']) || $result['status'] == "invalid") {
                    throw new RuntimeException("Verification ended with error: " . json_encode($result));
                }

                if ($result['status'] != "pending") {
                    break;
                }

                $sleepTime = $loopCount * $loopCount; // 1 4 9 16 25 36
                $loopCount++;

                $this->log("Verification pending, sleeping " . $sleepTime . "s");
                sleep($sleepTime);
            }

            if ($result['status'] === "pending") {
                throw new RuntimeException("Verification timed out");
            }

            $this->log("Verification ended with status: ${result['status']}");

            @unlink($tokenPath);
        } 

        # requesting certificate
        # ----------------------
        $domainPath = $this->getDomainPath(reset($domains));

        # generate private key for domain if not exist
        if (!is_dir($domainPath) || !is_file($domainPath . '/private.pem')) {
            $this->generateKey($domainPath);
        }

        # load domain key
        $privateDomainKey = $this->readPrivateKey($domainPath . '/private.pem');

        $this->client->getLastLinks();

        $csr = $reuseCsr && is_file($domainPath . "/last.csr") ?
            $this->getCsrContent($domainPath . "/last.csr") :
            $this->generateCSR($privateDomainKey, $domains);

        $finalizeResponse = $this->signedRequest($finalizeUrl, array('csr' => $csr));

        if ($this->client->getLastCode() > 299 || $this->client->getLastCode() < 200) {
            throw new RuntimeException("Invalid response code: " . $this->client->getLastCode() . ", " . json_encode($finalizeResponse));
        }
        
        $maxAllowedLoops = 6;
        $loopCount = 1;
		
		$lastLocationUrl = $this->client->getLastLocation();
		
		
        while ($loopCount < $maxAllowedLoops) {
            $this->log("Firing Order Status Request Nr. " . $loopCount . " to: " . $lastLocationUrl);
            $OrderStatusResponse = $this->signedRequest($lastLocationUrl, "");

            if (($this->client->getLastCode() > 299 || $this->client->getLastCode() < 200)) {
                throw new RuntimeException("Invalid response code: " . $this->client->getLastCode() . ", " . json_encode($OrderStatusResponse));
            }

            if (($OrderStatusResponse['status'] == "valid" && !empty($OrderStatusResponse['certificate']))) {
                $this->log("Order Status: " . $OrderStatusResponse['status']);
                $location = $OrderStatusResponse['certificate'];
                break;
            }

            $sleepTime = $loopCount * $loopCount; // 1 4 9 16 25 36
            $loopCount++;

            $this->log("Order Status not 'valid' yet but '" . $OrderStatusResponse['status'] . "', sleeping " . $sleepTime . "s");
            sleep($sleepTime);
        }

        if (empty($location)) {
            throw new RuntimeException("Certificate generation processing timed out (Status not 'valid')");
        }

        # waiting loop
        $certificates = array();
        while (1) {
            $this->client->getLastLinks();

            $result = $this->signedRequest($location, "");

            if ($this->client->getLastCode() == 202) {

                $this->log("Certificate generation pending, sleeping 1s");
                sleep(1);

            } else if ($this->client->getLastCode() == 200) {

                $this->log("Got certificate! YAY!");
                $serverCert = $this->parseFirstPemFromBody($result);
                $certificates[] = $serverCert;
                $certificates[] = substr($result, strlen($serverCert)); # rest of ca certs

                break;
            } else {

                throw new RuntimeException("Can't get certificate: HTTP code " . $this->client->getLastCode());

            }
        }

        if (empty($certificates)) throw new RuntimeException('No certificates generated');

        $this->log("Saving fullchain.pem");
        file_put_contents($domainPath . '/fullchain.pem', implode("\n", $certificates));

        $this->log("Saving cert.pem");
        file_put_contents($domainPath . '/cert.pem', array_shift($certificates));

        $this->log("Saving chain.pem");
        file_put_contents($domainPath . "/chain.pem", implode("\n", $certificates));

        $this->log("Done !!§§!");
    }

    protected function readPrivateKey($path)
    {
        if (($key = openssl_pkey_get_private('file://' . $path)) === FALSE) {
            throw new RuntimeException(openssl_error_string());
        }

        return $key;
    }

    protected function parseFirstPemFromBody($body)
    {
        preg_match('~(-----BEGIN.*?END CERTIFICATE-----)~s', $body, $matches);

        return $matches[1];
    }

    protected function getDomainPath($domain)
    {
        //tg return $this->certificatesDir . '/' . $domain . '/';
		return $this->certificatesDir;
    }

    protected function getAccountId()
    {
        return $this->postNewReg();
    }

    protected function postNewReg()
    {
        $data = array(
            'termsOfServiceAgreed' => true
        );

        $this->log('Sending registration to letsencrypt server');

        if ($this->contact) {
            $data['contact'] = $this->contact;
        }

        $response = $this->signedRequest(
            $this->urlNewAccount,
            $data
        );
        $lastLocation = $this->client->getLastLocation();
        if (!empty($lastLocation)) {
            $this->accountId = $lastLocation;
        }
        return $response;
    }

    protected function generateCSR($privateKey, array $domains)
    {
        $domain = reset($domains);
        $san = implode(",", array_map(function ($dns) {
            return "DNS:" . $dns;
        }, $domains));
        $tmpConf = tmpfile();
        $tmpConfMeta = stream_get_meta_data($tmpConf);
        $tmpConfPath = $tmpConfMeta["uri"];

        # workaround to get SAN working
        fwrite($tmpConf,
            'HOME = .
RANDFILE = $ENV::HOME/.rnd
[ req ]
default_bits = 2048
default_keyfile = privkey.pem
distinguished_name = req_distinguished_name
req_extensions = v3_req
[ req_distinguished_name ]
countryName = Country Name (2 letter code)
[ v3_req ]
basicConstraints = CA:FALSE
subjectAltName = ' . $san . '
keyUsage = nonRepudiation, digitalSignature, keyEncipherment');

        $csr = openssl_csr_new(
            array(
                "CN" => $domain,
                "ST" => $this->state,
                "C" => $this->countryCode,
                "O" => "Unknown",
            ),
            $privateKey,
            array(
                "config" => $tmpConfPath,
                "digest_alg" => "sha256"
            )
        );

        if (!$csr) throw new RuntimeException("CSR couldn't be generated! " . openssl_error_string());

        openssl_csr_export($csr, $csr);
        fclose($tmpConf);

        $csrPath = $this->getDomainPath($domain) . "/last.csr";
        file_put_contents($csrPath, $csr);

        return $this->getCsrContent($csrPath);
    }

    protected function getCsrContent($csrPath) {
        $csr = file_get_contents($csrPath);

        preg_match('~REQUEST-----(.*)-----END~s', $csr, $matches);

        return trim(Base64UrlSafeEncoder::encode(base64_decode($matches[1])));
    }

    protected function generateKey($outputDirectory)
    {
        $res = openssl_pkey_new(array(
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            "private_key_bits" => 4096,
        ));

        if (!openssl_pkey_export($res, $privateKey)) {
            throw new RuntimeException("Key export failed!");
        }

        $details = openssl_pkey_get_details($res);

        if (!is_dir($outputDirectory)) @mkdir($outputDirectory, 0700, true);
        if (!is_dir($outputDirectory)) throw new RuntimeException("Cant't create directory $outputDirectory");

        file_put_contents($outputDirectory . '/private.pem', $privateKey);
        file_put_contents($outputDirectory . '/public.pem', $details['key']);
    }

    protected function signedRequest($uri, $payload, $nonce = null)
    {
        $privateKey = $this->readPrivateKey($this->accountKeyPath);
        $details = openssl_pkey_get_details($privateKey);

        $protected = array(
            "alg" => "RS256",
            "nonce" => $nonce ? $nonce : $this->client->getLastNonce(),
            "url" => $uri
        );

        if ($this->accountId) {
            $protected["kid"] = $this->accountId;
        } else {
            $protected["jwk"] = array(
                "kty" => "RSA",
                "n" => Base64UrlSafeEncoder::encode($details["rsa"]["n"]),
                "e" => Base64UrlSafeEncoder::encode($details["rsa"]["e"]),
            );
        }

        $payload64 = Base64UrlSafeEncoder::encode(empty($payload) ? "" : str_replace('\\/', '/', json_encode($payload)));
        $protected64 = Base64UrlSafeEncoder::encode(json_encode($protected));

        openssl_sign($protected64 . '.' . $payload64, $signed, $privateKey, "SHA256");

        $signed64 = Base64UrlSafeEncoder::encode($signed);

        $data = array(
            'protected' => $protected64,
            'payload' => $payload64,
            'signature' => $signed64
        );

        $this->log("Sending signed request to $uri");

        return $this->client->post($uri, json_encode($data));
    }

    protected function log($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        } else {
            echo $message . "\n";
        }
    }
	
	################################################################### 
	
	
	public function postUpdateRegEmail()
    {

        $data = array(
			'contact' => $this->contact
		);

        $this->log('Requesting to update Email on account...');

        $response = $this->updateRequest(
			$this->accountId,
            $data
        );
        $lastLocation = $this->client->getLastLocation();
        if (!empty($lastLocation)) {
            $this->accountId = $lastLocation;
        }
        return $response;
    }

    public function updateRequest($uri, $payload, $nonce = null)
    {
        $privateKey = $this->readPrivateKey($this->accountKeyPath);
        $details = openssl_pkey_get_details($privateKey);

        $protected = array(
            "alg" => "RS256",
            "nonce" => $nonce ? $nonce : $this->client->getLastNonce(),
            "url" => $uri
        );

        if ($this->accountId) {
            $protected["kid"] = $uri;
        } else {
            $protected["jwk"] = array(
                "kty" => "RSA",
                "n" => Base64UrlSafeEncoder::encode($details["rsa"]["n"]),
                "e" => Base64UrlSafeEncoder::encode($details["rsa"]["e"]),
            );
        }

        $payload64 = Base64UrlSafeEncoder::encode(empty($payload) ? "" : str_replace('\\/', '/', json_encode($payload)));
        $protected64 = Base64UrlSafeEncoder::encode(json_encode($protected));

        openssl_sign($protected64.'.'.$payload64, $signed, $privateKey, "SHA256");

        $signed64 = Base64UrlSafeEncoder::encode($signed);

        $data = array(
            'protected' => $protected64,
            'payload' => $payload64,
            'signature' => $signed64
        );

		#FOR TESTING
		//$sendDATA = array(
			//"URL" => $uri,
			//"payload" => $payload,
			//"Protected" =>	$protected
		//);

		$this->log("Sending request to update account email...");
		# TESTING ONLY
		//echo print_r($sendDATA);
		
        return $this->client->post($uri, json_encode($data));
		
		//$this->log("Request accepted. Email Updated.");
		
    }
	
	
	function postRevoke($certData) {
		
		$reason = "1";
		
        $data = array(
            'certificate' => $certData
			//'reason' => $reason
        );

        $this->log('Sending request to revoke certificate');

        $response = $this->revokeRequest(
            $this->urlRevokeCert,
            $data
        );
        //$lastLocation = $this->client->getLastLocation();
        //if (!empty($lastLocation)) {
           // $this->accountId = $lastLocation;
        //}
        return $response;
    }
	
	
	function revokeRequest($uri, $payload, $nonce = null) {
        $privateKey = $this->readPrivateKey($this->accountKeyPath);
        $details = openssl_pkey_get_details($privateKey);

        $protected = array(
            "alg" => "RS256",
            "nonce" => $nonce ? $nonce : $this->client->getLastNonce(),
            "url" => $uri
        );

        if ($this->accountId) {
            $protected["kid"] = $this->accountId;
        } else {
            $protected["jwk"] = array(
                "kty" => "RSA",
                "n" => Base64UrlSafeEncoder::encode($details["rsa"]["n"]),
                "e" => Base64UrlSafeEncoder::encode($details["rsa"]["e"]),
            );
        }

        $payload64 = Base64UrlSafeEncoder::encode(empty($payload) ? "" :  json_encode($payload));
        $protected64 = Base64UrlSafeEncoder::encode(json_encode($protected));

        openssl_sign($protected64.'.'.$payload64, $signed, $privateKey, "SHA256");

        $signed64 = Base64UrlSafeEncoder::encode($signed);

        $data = array(
            'protected' => $protected64,
            'payload' => $payload64,
            'signature' => $signed64
        );

        $this->log("Sending revoke cert request to $uri");

        return $this->client->post($uri, json_encode($data));
    }
		
}

interface ClientInterface
{
    /**
     * Constructor
     *
     * @param string $base the ACME API base all relative requests are sent to
	 * @param string $userAgent ACME Client User-Agent
     */
    public function __construct($base, $userAgent);
    /**
     * Send a POST request
     *
     * @param string $url URL to post to
     * @param array $data fields to sent via post
     * @return array|string the parsed JSON response, raw response on error
     */
    public function post($url, $data);
    /**
     * @param string $url URL to request via get
     * @return array|string the parsed JSON response, raw response on error
     */
    public function get($url);
    /**
     * Returns the Replay-Nonce header of the last request
     *
     * if no request has been made, yet. A GET on $base/directory is done and the
     * resulting nonce returned
     *
     * @return mixed
     */
    public function getLastNonce();
    /**
     * Return the Location header of the last request
     *
     * returns null if last request had no location header
     *
     * @return string|null
     */
    public function getLastLocation();
    /**
     * Return the HTTP status code of the last request
     *
     * @return int
     */
    public function getLastCode();
    /**
     * Get all Link headers of the last request
     *
     * @return string[]
     */
    public function getLastLinks();
}

class Client implements ClientInterface
{
    protected $lastCode;
    protected $lastHeader;

    protected $base;
	protected $userAgent;

    public function __construct($base, $userAgent)
    {
        $this->base = $base;
		$this->userAgent = $userAgent;
    }

    protected function curl($method, $url, $data = null)
    {
        $headers = array('Accept: application/json', 'Content-Type: application/jose+json');
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, preg_match('~^http~', $url) ? $url : $this->base . $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_USERAGENT, $this->userAgent);

        # DO NOT DO THAT!
        // curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
        }
        $response = curl_exec($handle);

        if (curl_errno($handle)) {
            throw new RuntimeException('Curl: ' . curl_error($handle));
        }

        $header_size = curl_getinfo($handle, CURLINFO_HEADER_SIZE);

        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $this->lastHeader = $header;
        $this->lastCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($this->lastCode >= 400 && $this->lastCode < 600) {
            throw new RuntimeException($this->lastCode . "\n".$body);
        }

        $data = json_decode($body, true);
        return $data === null ? $body : $data;
    }

    public function post($url, $data)
    {
        return $this->curl('POST', $url, $data);
    }

    public function get($url)
    {
        return $this->curl('GET', $url);
    }

    public function getLastNonce()
    {
        if (preg_match('~Replay-Nonce: (.+)~i', $this->lastHeader, $matches)) {
            return trim($matches[1]);
        }
        
        throw new RuntimeException("We don't have nonce");
    }

    public function getLastLocation()
    {
        if (preg_match('~Location: (.+)~i', $this->lastHeader, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    public function getLastCode()
    {
        return $this->lastCode;
    }

    public function getLastLinks()
    {
        preg_match_all('~Link: <(.+)>;rel="up"~', $this->lastHeader, $matches);
        return $matches[1];
    }
}

class Base64UrlSafeEncoder
{
    public static function encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    public static function decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
