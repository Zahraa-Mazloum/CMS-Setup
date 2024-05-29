<?php

namespace app\lib;

use Carbon\Carbon;
use yii\base\BaseObject;
use yii\helpers\Json;

/**
 * @property string[] $errors
 * @property boolean $hasErrors
 * @property array $data
 */
class Jwt extends BaseObject
{
    protected $_errors = [];
    protected $_data = [];

    public function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    public function generateKey()
    {
        return bin2hex(random_bytes(32));
    }

    public function create(array $data)
    {
        $this->_data = $data;

        $secret = param('jwt.key.secret');
        $days = param('jwt.expiration.days', '30');
        $expiration = Carbon::now()->addDays(intval($days))->timestamp;

        addLog("jwt_set_exp", ['seconds' => $expiration, 'expiration', Carbon::createFromTimestamp($expiration),]);

        // Create the token header
        $header = Json::encode([
            'typ' => 'JWT',
            'alg' => 'HS256',
        ]);

        // Create the token payload
        $payload = Json::encode([
            ...$data,
            'exp' => $expiration,
        ]);

        // Encode Header
        $base64UrlHeader = $this->base64UrlEncode($header);

        // Encode Payload
        $base64UrlPayload = $this->base64UrlEncode($payload);

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = $this->base64UrlEncode($signature);

        // Create JWT
        return [
            'code' => 200,
            'expire' => date('c', $expiration),
            'token' => $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature,
        ];
    }

    public function validate(string $jwt)
    {
        $this->_errors = [];

        $secret = param('jwt.key.secret');

        // split the token
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        $this->_data = Json::decode($payload);

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
        $expiration = Carbon::createFromTimestamp($this->_data['exp']);
        $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);

        addLog("jwt_get_exp", ['expiration' => $expiration, 'diff' => Carbon::now()->diffInSeconds($expiration, false) , 'diffAbs' => Carbon::now()->diffInSeconds($expiration, true), 'expired' => $tokenExpired]);

        if ($tokenExpired) {
            file_put_contents(alias('@runtime/jwt_expired.txt'), $jwt);
            $this->_errors[] = "Token has expired.";
            addLog("jwt_token_expired", ["errors" => $this->_errors]);
            return false;
        }

        // build a signature based on the header and payload using the secret
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        // verify it matches the signature provided in the token
        addLog("jwt_signature_validation",['base64UrlSignature' => $base64UrlSignature, 'signatureProvided ' => $signatureProvided, '==' => $base64UrlSignature == $signatureProvided, '===' => $base64UrlSignature === $signatureProvided]);
        $signatureValid = ($base64UrlSignature === $signatureProvided);

        if (!$signatureValid) {
            file_put_contents(alias('@runtime/jwt_signature_invalid.txt'), $jwt);
            $this->_errors[] = "The signature is not valid.";
            addLog("jwt_signature_invalid", ["errors" => $this->_errors]);
            return false;
        }

        return true;
    }

    public function load(string $jwt)
    {
        if ($this->validate($jwt)) {
            return $this;
        }
        return null;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getHasErrors()
    {
        return count($this->_errors) > 0;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function has($name)
    {
        return \array_key_exists($name, $this->_data);
    }

    public function get($name, $defaultValue = null)
    {
        return $this->has($name) ? $this->_data[$name] : $defaultValue;
    }
}
