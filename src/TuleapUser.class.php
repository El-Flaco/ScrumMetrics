<?php
namespace Flacox;

class TuleapUser
{
    private $token = NULL;
    private $id = NULL;

    public function __construct($name, $password)
    {
        if ($this->authenticate($name, $password)) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * This method authenticates the user.
    * If $name and $password are correct, the method sets user's token and id
    *
    * @param string $name The Tuleap username
    * @param string $password The password
    */
    public function authenticate($name, $password)
    {
        if ($this->token === NULL || $this->id === NULL)
        {
            $curlHandle = curl_init();
            $url = "https://tuleap-web.tuleap-aio-dev.docker/";
            $jsonFields = "{\"username\": \"" . $name ."\", \"password\":\"" . $password . "\"}";

            curl_setopt($curlHandle, CURLOPT_URL, $url."api/tokens");
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $jsonFields);
            curl_setopt($curlHandle, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);

            $query = curl_exec($curlHandle);

            if (curl_errno($curlHandle)) {
                echo 'Error:' . curl_error($curlHandle);
                echo "\n";
            } elseif (strpos($query, 'Invalid Password Or User Name') !== false) {
                echo "Invalid Password Or User Name \n";
            } else {
                $jsonObject = json_decode($query);

                $this->token = $jsonObject->token;
                $this->id = $jsonObject->user_id;
                return true;   
            }

            return false;
        }
    }

    /**
    * @return string
    */
    public function getToken()
    {
        return $this->token;
    }

    /**
    * @return string
    */
    public function getId()
    {
        return $this->id;
    }
}
?>
