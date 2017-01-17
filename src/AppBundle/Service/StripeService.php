<?php
namespace AppBundle\Service;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
* 
* 
 * StripeService
 * Insert description here
 *
 * @category
 * @package
 * @author
 * @copyright
 * @license
 * @version
 * @link
 * @see
 * @since
 
*/
class StripeService extends BaseService
{

    protected $redis;
    protected $container;

    /**
     * Constructor
     *
     * @param mixed $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
        $this->redis = []; //$container->get('snc_redis.default');
        $this->output = new ConsoleOutput();
        /* $this->CI->config->load('stripe');

          $this->private_key = $this->CI->config->item('stripe_sk');

          \Stripe\Stripe::setApiKey($this->private_key); */
    }

    /**
* 
* 
     * retrieve_charge
     * Insert description here
     *
     * @param $id
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function retrieve_charge($id)
    {
        try {
            $charge = \Stripe\Charge::retrieve($id);
        } catch (Exception $e) {
            $charge = false;
        }
        return $charge;
    }

    /**
* 
* 
     * create_charge
     * Insert description here
     *
     * @param $id
     * @param $amount
     * @param $desc
     * @param $meta
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function create_charge($id, $amount, $desc, $meta)
    {
        try {
            $row = array(
                'amount' => $amount * 100,
                'currency' => 'usd',
                'source' => $id,
                'description' => $desc
            );
            if ($meta) {
                $row['metadata'] = $meta;
            }
            $charge = \Stripe\Charge::create($row);
        } catch (Exception $e) {
            $charge = false;
        }
        return $charge;
    }

    /**
* 
* 
     * create_customer_charge
     * Insert description here
     *
     * @param $customer_id
     * @param $amount
     * @param $desc
     * @param $meta
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function create_customer_charge($customer_id, $amount, $desc, $meta)
    {
        try {
            $row = array(
                'amount' => $amount * 100,
                'currency' => 'usd',
                'customer' => $customer_id,
                'description' => $desc
            );
            if ($meta) {
                $row['metadata'] = $meta;
            }
            $charge = \Stripe\Charge::create($row);
        } catch (Exception $e) {
            $charge = false;
        }
        return $charge;
    }

    /**
* 
* 
     * create_customer
     * Insert description here
     *
     * @param $token
     * @param $desc
     * @param $email
     * @param $meta
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function create_customer($token, $desc, $email, $meta)
    {
        try {
            $row = array(
                'source' => $token,
                'description' => $desc,
                'email' => $email
            );
            if ($meta) {
                $row['metadata'] = $meta;
            }
            $customer = \Stripe\Customer::create($row);
        } catch (Exception $e) {
            $customer = false;
        }
        return $customer;
    }
    // Create a Customer
}
