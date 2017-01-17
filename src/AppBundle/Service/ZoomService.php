<?php
namespace AppBundle\Service;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
* 
* 
 * ZoomService
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
class ZoomService extends BaseService
{

    protected $redis;
    protected $container;
    private $CI;
    private $_code;
    private $_password;
    private $_person_search_params = array(
        'pc' => false,
        'outputType' => false,
        'outputFieldOptions' => false,
        'key' => false,
        'rpp' => false,
        'page' => false,
        'SortBy' => false,
        'SortOrder' => false,
        'firstName' => true,
        'middleInitial' => true,
        'lastName' => true,
        'personTitle' => true,
        'TitleSeniority' => true,
        'TitleClassification' => true,
        'companyId' => true,
        'companyName' => true,
        'companyDesc' => true,
        'IndustryClassification' => true,
        'IndustryKeywords' => true,
        'State' => true,
        'MetroRegion' => true,
        'Country' => true,
        'ZipCode' => true,
        'RadiusMiles' => true,
        'location' => true,
        'locationSearchType' => true,
        'RevenueClassificationMin' => true,
        'RevenueClassificationMax' => true,
        'RevenueClassification' => true,
        'EmployeeSizeClassificationMin' => true,
        'EmployeeSizeClassificationMax' => true,
        'EmployeeSizeClassification' => true,
        'IsPublic' => true,
        'CompanyRanking' => true,
        'school' => true,
        'degree' => true,
        'gender' => true,
        'companyDomainName' => true,
        'titleCertification' => true,
        'companyPastOrPresent' => true,
        'ValidDateMonthDist' => true,
        'ContactRequirements' => true,
        'EmailAddress' => true,
    );
    private $_person_detail_params = array(
        'pc' => false,
        'outputType' => false,
        'outputFieldOptions' => false,
        'key' => false,
        'PersonID' => true,
        'EmailAddress' => true,
    );
    private $_company_detail_params = array(
        'pc' => false,
        'key' => false,
        'outputType' => false,
        'includeKeyPerson' => false,
        'outputFieldOptions' => false,
        'CompanyID' => true,
        'CompanyDomain' => true,
    );

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
        /* $this->CI =& get_instance();

          $this->CI->config->load('zoominfo');

          $this->_code     = $this->CI->config->item('zoominfo_code');
          $this->_password = $this->CI->config->item('zoominfo_password');

          $this->CI->load->model('Source_model'); */
    }

    /**
* 
* 
     * _send
     * Insert description here
     *
     * @param $endpoint
     * @param $params
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    private function _send($endpoint, $params)
    {
        $url = 'http://partnerapi.zoominfo.com/partnerapi/' . $endpoint;
        $sorted_pairs = array();
        $key_source = '';

        if ($endpoint === 'person/search') {
            $_params = $this->_person_search_params;
        } elseif ($endpoint === 'person/detail') {
            $_params = $this->_person_detail_params;
        } elseif ($endpoint === 'company/detail') {
            $_params = $this->_company_detail_params;
        }

        foreach ($_params as $key => $val) {
            if (isset($params[$key]) && $params[$key] !== '') {
                $sorted_pairs[] = $key . '=' . urlencode($params[$key]);
                if ($val) {
                    $key_source .= substr($params[$key], 0, 2);
                }
            }
        }
        $url .= '?' . implode('&', $sorted_pairs);
        $key_source .= $this->_password . date_format(date_create('now', timezone_open('EST')), 'jnY');
        $url .= '&pc=' . $this->_code . '&key=' . md5($key_source);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

        // curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($output, true);

        if (!$result) {
            $this->CI->app_core->log(App_core::CODE_INTERNAL_ERROR, $output);
            return false;
        } else {
            return $result;
        }
    }

    /**
* 
* 
     * person_search
     * Insert description here
     *
     * @param $params
     * @param $count
     * @param $page
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function person_search($params, $count = 5, $page = 1)
    {
        $zoominfo_params = array();

        foreach ($params as $key => $param) {
            if ($key === 'countries' && is_array($param)) {
                $zoominfo_params['Country'] = implode(',', $param);
            } elseif ($key === 'states' && is_array($param)) {
                $zoominfo_params['State'] = implode(',', $param);
            } elseif ($key === 'metro_regions' && is_array($param)) {
                $zoominfo_params['MetroRegion'] = implode(',', $param);
            } elseif ($key === 'location') {
                $zoominfo_params['location'] = $param;
            } elseif ($key === 'location_match_type') {
                $zoominfo_params['locationSearchType'] = $param;
            } elseif ($key === 'company_names' && is_array($param)) {
                $zoominfo_params['companyName'] = implode(',', $param);
            } elseif ($key === 'revenues' && is_array($param)) {
                $zoominfo_params['RevenueClassification'] = implode(',', $param);
            } elseif ($key === 'employee_sizes' && is_array($param)) {
                $zoominfo_params['EmployeeSizeClassification'] = implode(',', $param);
            } elseif ($key === 'person_title') {
                $zoominfo_params['personTitle'] = $param;
            } elseif ($key === 'title_roles' && is_array($param)) {
                $zoominfo_params['TitleClassification'] = implode(',', $param);
            } elseif ($key === 'title_seniorities' && is_array($param)) {
                $zoominfo_params['TitleSeniority'] = implode(',', $param);
            } elseif ($key === 'industries' && is_array($param)) {
                $zoominfo_params['IndustryClassification'] = implode(',', $param);
            } elseif ($key === 'person_name') {
                $name_parts = explode(' ', trim($param));
                $name_parts_size = count($name_parts);
                if ($name_parts_size === 1) {
                    $zoominfo_params['lastName'] = $name_parts[0];
                } elseif ($name_parts_size === 2) {
                    $zoominfo_params['firstName'] = $name_parts[0];
                    $zoominfo_params['lastName'] = $name_parts[1];
                } elseif ($name_parts_size === 3) {
                    $zoominfo_params['firstName'] = $name_parts[0];
                    $zoominfo_params['middleInitial'] = $name_parts[1];
                    $zoominfo_params['lastName'] = $name_parts[2];
                }
            }
        }

        $zoominfo_params['companyPastOrPresent'] = '1'; // Present
        $zoominfo_params['ContactRequirements'] = '1'; // Email
        $zoominfo_params['outputFieldOptions'] = 'localAddress,companyAddress,companyWebsite,companyLogo,companyRevenue,companyEmployeeCount';
        $zoominfo_params['rpp'] = min(25, $count);
        $zoominfo_params['page'] = $page;
        $zoominfo_params['ValidDateMonthDist'] = 12;
        // $zoominfo_params['SortBy']               = 'LastMentioned';
        // $zoominfo_params['SortOrder']            = 'asc';

        if ($zoominfo_params) {
            $result = $this->_send('person/search', $zoominfo_params);
            $objects = array();
            $total_count = 0;

            if ($result && isset($result['PeopleSearchRequest']['PeopleSearchResults']['PersonRecord'])) {
                $total_count = $result['PeopleSearchRequest']['TotalResults'];
                foreach ($result['PeopleSearchRequest']['PeopleSearchResults']['PersonRecord'] as $person) {
                    if (!isset($person['PersonID'])) {
                        continue;
                    } else {
                        $obj = array();

                        $obj['deal'] = array(
                            'action_values' => $params,
                        );

                        $obj['client_source'] = array(
                            'code' => (string) $person['PersonID'],
                            'type' => Source_model::TYPE_ZOOMINFO_PERSON,
                            'extra' => array(),
                        );

                        if (isset($person['LocalAddress'])) {
                            if (isset($person['LocalAddress']['Street'])) {
                                $obj['client_source']['extra']['location']['street'] = $person['LocalAddress']['Street'];
                            }
                            if (isset($person['LocalAddress']['City'])) {
                                $obj['client_source']['extra']['location']['city'] = $person['LocalAddress']['City'];
                            }
                            if (isset($person['LocalAddress']['State'])) {
                                $obj['client_source']['extra']['location']['state'] = $person['LocalAddress']['State'];
                            }
                            if (isset($person['LocalAddress']['Zip'])) {
                                $obj['client_source']['extra']['location']['zip'] = $person['LocalAddress']['Zip'];
                            }
                            if (isset($person['LocalAddress']['CountryCode'])) {
                                $obj['client_source']['extra']['location']['country'] = $person['LocalAddress']['CountryCode'];
                            }
                        }

                        if (isset($person['ImageUrl'])) {
                            $obj['client_source']['extra']['avatar'] = $person['ImageUrl'];
                        }
                        if (isset($person['Email'])) {
                            $obj['client_source']['extra']['email'] = $person['Email'];
                        }
                        if (isset($person['Phone'])) {
                            $obj['client_source']['extra']['phone'] = $person['Phone'];
                        }

                        $obj['client'] = array();

                        if (isset($person['LocalAddress'])) {
                            if (isset($person['LocalAddress']['Street'])) {
                                $obj['client']['address_line1'] = $person['LocalAddress']['Street'];
                            }
                            if (isset($person['LocalAddress']['City'])) {
                                $obj['client']['city'] = $person['LocalAddress']['City'];
                            }
                            if (isset($person['LocalAddress']['State'])) {
                                $obj['client']['state'] = $person['LocalAddress']['State'];
                            }
                            if (isset($person['LocalAddress']['Zip'])) {
                                $obj['client']['zip'] = $person['LocalAddress']['Zip'];
                            }
                            if (isset($person['LocalAddress']['CountryCode'])) {
                                $obj['client']['country'] = $person['LocalAddress']['CountryCode'];
                            }
                        }

                        if (isset($person['Email'])) {
                            $obj['client']['email'] = $person['Email'];
                        }
                        if (isset($person['Phone'])) {
                            $obj['client']['phone'] = $person['Phone'];
                        }

                        if (isset($person['CurrentEmployment'])) {
                            if ($person['IsPast'] === 'False' && isset($person['CurrentEmployment']['JobTitle'])) {
                                $obj['client_source']['extra']['occupation'] = $person['CurrentEmployment']['JobTitle'];
                                $obj['client']['occupation'] = $person['CurrentEmployment']['JobTitle'];
                            }

                            if (isset($person['CurrentEmployment']['Company'])) {
                                $obj['company'] = array();

                                if (isset($person['CurrentEmployment']['Company']['CompanyAddress'])) {
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['Street'])) {
                                        $obj['company']['address_line1'] = $person['CurrentEmployment']['Company']['CompanyAddress']['Street'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['City'])) {
                                        $obj['company']['city'] = $person['CurrentEmployment']['Company']['CompanyAddress']['City'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['State'])) {
                                        $obj['company']['state'] = $person['CurrentEmployment']['Company']['CompanyAddress']['State'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['Zip'])) {
                                        $obj['company']['zip'] = $person['CurrentEmployment']['Company']['CompanyAddress']['Zip'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['CountryCode'])) {
                                        $obj['company']['country'] = $person['CurrentEmployment']['Company']['CompanyAddress']['CountryCode'];
                                    }
                                }

                                if (isset($person['CurrentEmployment']['Company']['CompanyName'])) {
                                    $obj['company']['name'] = $person['CurrentEmployment']['Company']['CompanyName'];
                                }
                                if (isset($person['CurrentEmployment']['Company']['CompanyPhone'])) {
                                    $obj['company']['phone'] = $person['CurrentEmployment']['Company']['CompanyPhone'];
                                }
                                if (isset($person['CurrentEmployment']['Company']['CompanyWebsite'])) {
                                    $obj['company']['website'] = $person['CurrentEmployment']['Company']['CompanyWebsite'];
                                }

                                if (isset($person['CurrentEmployment']['Company']['CompanyID'])) {
                                    $obj['company_source']['code'] = $person['CurrentEmployment']['Company']['CompanyID'];
                                    $obj['company_source']['type'] = Source_model::TYPE_ZOOMINFO_COMPANY;
                                } else {
                                    $obj['company_source']['code'] = 'noid' . $person['PersonID'];
                                    $obj['company_source']['type'] = Source_model::TYPE_ZOOMINFO_COMPANY_NOID;
                                }

                                if (isset($person['CurrentEmployment']['Company']['CompanyAddress'])) {
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['Street'])) {
                                        $obj['company_source']['extra']['location']['street'] = $person['CurrentEmployment']['Company']['CompanyAddress']['Street'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['City'])) {
                                        $obj['company_source']['extra']['location']['city'] = $person['CurrentEmployment']['Company']['CompanyAddress']['City'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['State'])) {
                                        $obj['company_source']['extra']['location']['state'] = $person['CurrentEmployment']['Company']['CompanyAddress']['State'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['Zip'])) {
                                        $obj['company_source']['extra']['location']['zip'] = $person['CurrentEmployment']['Company']['CompanyAddress']['Zip'];
                                    }
                                    if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['CountryCode'])) {
                                        $obj['company_source']['extra']['location']['country'] = $person['CurrentEmployment']['Company']['CompanyAddress']['CountryCode'];
                                    }
                                }

                                if (isset($person['CurrentEmployment']['Company']['CompanyLogo'])) {
                                    $obj['company_source']['extra']['logo'] = $person['CurrentEmployment']['Company']['CompanyLogo'];
                                }
                                if (isset($person['CurrentEmployment']['Company']['CompanyName'])) {
                                    $obj['company_source']['extra']['name'] = $person['CurrentEmployment']['Company']['CompanyName'];
                                }
                                if (isset($person['CurrentEmployment']['Company']['CompanyPhone'])) {
                                    $obj['company_source']['extra']['phone'] = $person['CurrentEmployment']['Company']['CompanyPhone'];
                                }
                                if (isset($person['CurrentEmployment']['Company']['CompanyWebsite'])) {
                                    $obj['company_source']['extra']['website'] = $person['CurrentEmployment']['Company']['CompanyWebsite'];
                                }
                                if (isset($person['CurrentEmployment']['Company']['CompanyRevenue'])) {
                                    $obj['company_source']['extra']['revenue'] = $person['CurrentEmployment']['Company']['CompanyRevenue'];
                                }
                                if (isset($person['CurrentEmployment']['Company']['CompanyEmployeeCount'])) {
                                    $obj['company_source']['extra']['employee_count'] = $person['CurrentEmployment']['Company']['CompanyEmployeeCount'];
                                }

                                if (isset($person['Industry'])) {
                                    if (is_string($person['Industry'])) {
                                        $obj['company_source']['extra']['industries'] = array($person['Industry']);
                                    } else {
                                        $obj['company_source']['extra']['industries'] = $person['Industry'];
                                    }
                                }

                                if (!$obj['company_source']['extra']) {
                                    $obj['company_source']['extra'] = (object) array();
                                }
                            }
                        }

                        if (isset($obj['company_source']['extra'])) {
                            $obj['client_source']['extra']['company'] = $obj['company_source']['extra'];
                        }

                        if (!$obj['client_source']['extra']) {
                            $obj['client_source']['extra'] = (object) array();
                        }

                        $obj['source'] = $obj['client_source'];

                        $objects[] = $obj;
                    }
                }
            }
            $scan = array('deals' => $objects, 'total_count' => $total_count);
            if ($result && isset($result['PeopleSearchRequest']['ErrorMessage'])) {
                $scan['error'] = $result['PeopleSearchRequest']['ErrorMessage'];
            }
            return $scan;
        } else {
            return false;
        }
    }

    /**
* 
* 
     * person_detail
     * Insert description here
     *
     * @param $id
     * @param $enrich_only
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function person_detail($id, $enrich_only = false)
    {
        $zoominfo_params['PersonID'] = $id;

        $zoominfo_params['outputFieldOptions'] = 'localAddress,jobFunction,managementLevel,companyTopLevelIndustry,companyLogo';

        if ($zoominfo_params) {
            $result = $this->_send('person/detail', $zoominfo_params);
            $person = $result['PersonDetailRequest'];

            $obj = array();

            $obj['client']['name'] = '';

            if (isset($person['FirstName'])) {
                $obj['client']['name'] .= $person['FirstName'];
            }
            if (isset($person['MiddleInitial'])) {
                $obj['client']['name'] .= ' ' . $person['MiddleInitial'];
            }
            if (isset($person['LastName'])) {
                $obj['client']['name'] .= ' ' . $person['LastName'];
            }

            $obj['client']['name'] = trim($obj['client']['name']);

            if (isset($person['Email'])) {
                $obj['client']['email'] = $person['Email'];
            }
            if (isset($person['DirectPhone'])) {
                $obj['client']['phone'] = $person['DirectPhone'];
            } elseif (isset($person['Phone'])) {
                $obj['client']['phone'] = $person['Phone'];
            } elseif (isset($person['CompanyPhone'])) {
                $obj['client']['phone'] = $person['CompanyPhone'];
            }

            if (!$enrich_only) {
                if (isset($person['LocalAddress'])) {
                    if (isset($person['LocalAddress']['Street'])) {
                        $obj['client']['address_line1'] = $person['LocalAddress']['Street'];
                    }
                    if (isset($person['LocalAddress']['City'])) {
                        $obj['client']['city'] = $person['LocalAddress']['City'];
                    }
                    if (isset($person['LocalAddress']['State'])) {
                        $obj['client']['state'] = $person['LocalAddress']['State'];
                    }
                    if (isset($person['LocalAddress']['Zip'])) {
                        $obj['client']['zip'] = $person['LocalAddress']['Zip'];
                    }
                    if (isset($person['LocalAddress']['CountryCode'])) {
                        $obj['client']['country'] = $person['LocalAddress']['CountryCode'];
                    }
                }

                $obj['client_source'] = array(
                    'code' => $person['PersonID'],
                    'type' => Source_model::TYPE_ZOOMINFO_PERSON,
                    'extra' => array(
                        'avatar' => isset($person['ImageUrl']) ? $person['ImageUrl'] : null,
                    ),
                );

                $obj['source'] = $obj['client_source'];

                if (isset($person['CurrentEmployment'])) {
                    if (isset($person['CurrentEmployment']['JobTitle'])) {
                        $obj['client']['occupation'] = $person['CurrentEmployment']['JobTitle'];
                    }

                    if (isset($person['CurrentEmployment']['Company'])) {
                        $obj['company'] = array();

                        if (isset($person['CurrentEmployment']['Company']['CompanyAddress'])) {
                            if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['Street'])) {
                                $obj['company']['address_line1'] = $person['CurrentEmployment']['Company']['CompanyAddress']['Street'];
                            }
                            if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['City'])) {
                                $obj['company']['city'] = $person['CurrentEmployment']['Company']['CompanyAddress']['City'];
                            }
                            if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['State'])) {
                                $obj['company']['state'] = $person['CurrentEmployment']['Company']['CompanyAddress']['State'];
                            }
                            if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['Zip'])) {
                                $obj['company']['zip'] = $person['CurrentEmployment']['Company']['CompanyAddress']['Zip'];
                            }
                            if (isset($person['CurrentEmployment']['Company']['CompanyAddress']['CountryCode'])) {
                                $obj['company']['country'] = $person['CurrentEmployment']['Company']['CompanyAddress']['CountryCode'];
                            }
                        }

                        if (isset($person['CurrentEmployment']['Company']['CompanyName'])) {
                            $obj['company']['name'] = $person['CurrentEmployment']['Company']['CompanyName'];
                        }
                        if (isset($person['CurrentEmployment']['Company']['Phone'])) {
                            $obj['company']['phone'] = $person['CurrentEmployment']['Company']['Phone'];
                        }
                        if (isset($person['CurrentEmployment']['Company']['Website'])) {
                            $obj['company']['website'] = $person['CurrentEmployment']['Company']['Website'];
                        }

                        if (isset($person['CurrentEmployment']['Company']['CompanyID'])) {
                            $obj['company_source']['code'] = $person['CurrentEmployment']['Company']['CompanyID'];
                            $obj['company_source']['type'] = Source_model::TYPE_ZOOMINFO_COMPANY;
                        } else {
                            $obj['company_source']['code'] = 'noid' . $person['PersonID'];
                            $obj['company_source']['type'] = Source_model::TYPE_ZOOMINFO_COMPANY_NOID;
                        }

                        $obj['company_source']['extra'] = array(
                            'logo' => isset($person['CurrentEmployment']['Company']['CompanyLogo']) ? $person['CurrentEmployment']['Company']['CompanyLogo'] : null,
                        );

                        if (!(isset($obj['client']['address_line1']) && $obj['client']['address_line1']) && isset($obj['company']['address_line1']) && $obj['company']['address_line1']) {
                            $obj['client']['address_line1'] = $obj['company']['address_line1'];

                            unset($obj['client']['city']);
                            unset($obj['client']['state']);
                            unset($obj['client']['zip']);
                            unset($obj['client']['country']);

                            if (isset($obj['company']['city'])) {
                                $obj['client']['city'] = $obj['company']['city'];
                            }
                            if (isset($obj['company']['state'])) {
                                $obj['client']['state'] = $obj['company']['state'];
                            }
                            if (isset($obj['company']['zip'])) {
                                $obj['client']['zip'] = $obj['company']['zip'];
                            }
                            if (isset($obj['company']['country'])) {
                                $obj['client']['country'] = $obj['company']['country'];
                            }
                        }
                    }
                }
            }

            return $obj;
        } else {
            return false;
        }
    }

    /**
* 
* 
     * company_detail
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
    public function company_detail($id)
    {
        $zoominfo_params['CompanyID'] = $id;

        if ($zoominfo_params) {
            $result = $this->_send('company/detail', $zoominfo_params);
            $company = $result['CompanyDetailRequest'];

            $obj = array();

            $obj['company'] = array();

            if (isset($company['CompanyDescription'])) {
                $obj['company']['description'] = $company['CompanyDescription'];
            }

            return $obj;
        } else {
            return false;
        }
    }

    /**
* 
* 
     * do_zoominfo
     * Insert description here
     *
     * @param $workspace_id
     * @param $client_id
     * @param $company_id
     * @param $user_id
     * @param $company_source_code
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function do_zoominfo($workspace_id, $client_id, $company_id, $user_id, $company_source_code)
    {
        $this->CI = & get_instance();

        $this->CI->load->model(array('Company_model', 'Client_model'));

        $this->CI->load->library('zoominfo');
        $this->CI->load->library('xverify');

        $pipl = $this->CI->zoominfo->person_detail($user_id, true);

        if ($pipl) {
            $is_valid_email = $this->CI->xverify->verify_email($pipl['client']['email']);
            $pipl['is_valid_email'] = $is_valid_email;

            if ($is_valid_email) {
                if ($company_source_code !== null && strpos($company_source_code, 'noid') !== 0) {
                    $company = $this->CI->Company_model->create($_SESSION['user_id'], $workspace_id, $pipl['company']);
                    if (!$company) {
                        if (isset($pipl['company'])) {
                            $pipl['company'] = array_merge($pipl['company'], $company['company']);
                        }
                    }
                }

                $this->CI->Client_model->update($client_id, $pipl['client'], false);
                if (isset($pipl['company'])) {
                    if ($company_id === '0') {
                        $company = $this->CI->Company_model->create($_SESSION['account_id'], $group_id, $pipl['company']);
                        if (!$company) {
                            $this->CI->app_core->log(App_core::CODE_DB_ERROR, 'Company_model->create');
                            return false;
                        } else {
                            $client_updated = $this->CI->Client_model->update($client_id, array('company_id' => $company['id']), true);
                            if ($client_updated === null) {
                                $this->CI->app_core->log(App_core::CODE_DB_ERROR, 'Client_model->update_client');
                                return false;
                            }
                            $company_id = $company['id'];
                        }
                    } else {
                        $this->CI->Company_model->update($company_id, $pipl['company'], false);
                    }
                }
            }
        }
        return $pipl;
    }
}
