<?php

namespace AppBundle\Service;

use Predis\Connection\ConnectionException;
use Predis\ServerException;
use Symfony\Component\Debug\Exception\ContextErrorException;
use EmpireBundle\Service\RedisService as CS;

/**
 * Description of EmpireService
 *
 * @author Josh Murphy
 */
class RedisService
{
    protected $_c;
    protected $logger;
    protected $expire;
    protected $verbose = true;

    const PREFIX        = 'guff';
    const SP            = ':';
    const INFANALYTICS  = 'infanyl';
    const INFTOPCONTENT = 'inftpcnt';
    const CONTENT       = 'cnt';
    const PRODUCTIVITY  = 'prod';
    const ANALYTICS     = 'anyl';
    const SITEDATA      = 'site';
    const POST          = 'post';
    const MISSINGPOST   = 'missing';
    const COLLECTION    = 'collection';
    const PLAYLIST      = 'playlist';
    const SECTIONS      = 'sections';
    const SECTION       = 'section';
    const TILES         = 'tiles';
    const FRONTPAGE     = 'frontpage';
    const REVENUE       = 'revenue';
    const TIMESTAMP     = 'timestamp';
    const USER          = 'user';
    const PROFILE       = 'profile';
    const GACMP   = 'gacmp';
    const GACMPPVPSPD   = 'gacpvpspd';

    /**
     *
     * @param unknown $cache
     */
    public function __construct($container)
    {
        $this->_c     = $container->get('sncredis.default');
        $this->expire = 3600;
        $this->logger = $container->get('logger');
    }

    /**
     * expireKeys
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function expireKeys()
    {
        $members = $this->_c->zRangeByScore(CS::PREFIX . CS::SP . $this->satellite_id . CS::SP . CS::TIMESTAMP, "-inf", time() - (24 * 60 * 60));
        foreach ($members as $member) {
            $this->_c->del($member[0]);
        }
        $this->_c->zRemRangeByScore(CS::PREFIX . CS::SP . $this->satellite_id . CS::SP . CS::TIMESTAMP, "-inf", time() - (24 * 60 * 60));
        $this->listKeys();
    }

    /**
     * listKeys
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function listKeys()
    {
        $members = $this->_c->zRange(CS::PREFIX . CS::SP . $this->satellite_id . CS::SP . CS::TIMESTAMP, 0, -1, ['withscores' => true]);
        foreach ($members as $member) {
            echo $member[0] . ' ' . date('l jS \of F Y h:i:s A', $member[1]) . PHP_EOL;
        }
    }

    /**
     * refreshKey
     * Insert description here
     *
     * @param $key
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function refreshKey($key)
    {
        //validate key
        $valid = $this->_c->get($key);
        if ($valid == "") {
            return ["Failed key " . $key];
        }
        $data = explode(":", $key);
        if (isset($data[2])) {
            $this->_c->zRem(CS::PREFIX . CS::SP . $this->satellite_id . CS::SP . CS::TIMESTAMP, $key);
            switch ($data[2]) {
            case CS::FRONTPAGE:
                $ret = $this->getFrontPagePosts();
                break;
            case CS::SITEDATA:
                $ret = $this->getSiteData();
                break;
            case CS::POST:
                $ret = $this->getPost($data[3], $data[4]);
                break;
            case CS::SECTION:
                if (isset($data[3]) && $data[3] == "tiles") {
                    $ret = $this->getSectionTiles($data[4], $data[5]);
                } else {
                    $ret = $this->getSection($data[3]);
                }
                break;
            case CS::SECTIONS:
                $ret = $this->getSections();
                break;
            default:
                $ret = ["Failed key " . $key];
            }
            print_r($ret);
        }
    }

    /**
     *
     */
    public function get($name)
    {
        try {
            return $this->_c->get($name);
        } catch (ConnectionException $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }

    /**
     *
     */
    public function set($name, $data)
    {
        try {
            $this->_c->set($name, $data);
            return true;
        } catch (ContextErrorException $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (ConnectionException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     *
     * @param string $name
     * @param int    $score
     * @param string $data
     * @return boolean
     */
    public function getScore($key)
    {
        try {
            $this->logger->debug($key);
            return $data = $this->_c->zScore(CS::PREFIX . CS::SP . CS::TIMESTAMP, $key);
        } catch (ContextErrorException $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (ConnectionException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     *
     * @param string $name
     * @param int    $score
     * @param string $data
     * @return boolean
     */
    public function sortedSetAdd($name, $score, $data)
    {
        try {
            $this->_c->zAdd($name, $score, $data);
        } catch (ContextErrorException $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (ConnectionException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $name
     * @param string $data
     * @return bool
     */
    public function sortedSetRemove($name, $data)
    {
        try {
            $this->_c->zRem($name, $data);
        } catch (ContextErrorException $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (ConnectionException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $name
     * @param int    $start
     * @param int    $end
     * @param bool   $withScores
     * @return array
     */
    public function sortedSetRange($name, $start = 0, $end = -1, $withScores = false)
    {
        try {
            if ($withScores == true) {
                $result = $this->_c->zRange($name, $start, $end, ['withscores' => $withScores]);
            } else {
                $result = $this->_c->zRange($name, $start, $end);
            }
        } catch (ContextErrorException $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (ConnectionException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return $result;
    }

    /**
     * getDataByCURL
     * Insert description here
     *
     * @param $url
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function getDataByCURL($url)
    {
        $ch      = curl_init();
        $url     = $this->api_host . '/satellite/api/v' . $this->api_version . '/' . $url;
        //echo $url;
        $this->logger->info($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->verbose);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "satellite_id=" . $this->satellite_id . "&data=" . strtr($this->encrypt(serialize(['sig' => time()])), '+/=', '-_,'));
        //make the request
        $this->logger->debug("satellite_id=" . $this->satellite_id . "&data=" . strtr($this->encrypt(serialize(['sig' => time()])), '+/=', '-_,'));
        if (!$content = curl_exec($ch)) {
            $this->logger->error(curl_error($ch));
            return false;
        }
        $this->logger->debug($content);
        /* echo $url .PHP_EOL;
          print_r(json_decode($content));exit; */
        return $json = json_decode($content);
    }

    /**
     * encrypt
     * Insert description here
     *
     * @param $string
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function encrypt($string)
    {
        // --- ENCRYPTION ---
        // the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        // convert a string into a key
        // key is specified using hexadecimal
        $key = pack('H*', bin2hex($this->partner_key));

        // show key size use either 16, 24 or 32 byte keys for AES-128, 192
        // and 256 respectively
        $key_size = strlen($key);

        // create a random IV to use with CBC encoding
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        // creates a cipher text compatible with AES (Rijndael block size = 128)
        // to keep the text confidential
        // only suitable for encoded input that never ends with value 00h
        // (because of default zero padding)
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $iv);

        // prepend the IV for it to be available for decryption
        $ciphertext = $iv . $ciphertext;

        // encode the resulting cipher text so it can be represented by a string
        $ciphertext_base64 = base64_encode($ciphertext);

        //echo $ciphertext_base64 . "\n";
        return $ciphertext_base64;
    }

    /**
     * decrypt
     * Insert description here
     *
     * @param $data
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function decrypt($data)
    {
        // --- DECRYPTION ---
        // the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        // convert a string into a key
        // key is specified using hexadecimal
        $key = pack('H*', $this->partner_key);

        // show key size use either 16, 24 or 32 byte keys for AES-128, 192
        // and 256 respectively
        $key_size       = strlen($key);
        $ciphertext_dec = base64_decode($data);

        // retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        // retrieves the cipher text (everything except the $iv_size in the front)
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);

        // may remove 00h valued characters from end of plain text
        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        echo $plaintext_dec . "\n";
        exit;
    }
}
