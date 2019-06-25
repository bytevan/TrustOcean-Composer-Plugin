<?php
namespace Londry\TrustOceanSSLCertificate\api;

use Londry\TrustOceanSSLCertificate\model\Order;
use Londry\TrustOceanSSLCertificate\TrustoceanException;

class sslOrder extends Order{

    public function __construct($username, $token)
    {
        $this->username = $username;
        $this->password = $token;
    }

    public function callInit($orderNumber = NULL){
        // check login credential and unique_id by callTrustOceanAPI
        // check permission of product by callTrustOceanAPI

    }

    /**
     * @return $this
     * @throws TrustoceanException
     * Create new ssl order by API, before call this function,
     * you also need set required params whit `set function` provide by this class own.
     */
    public function callCreate(){
        // check required params for new dv singleDomain/multiDomain ssl order
        $requiredParamKeys = [
            'pid',
            'period',
            'csr_code',
            'contact_email',
            'dcv_method',
        ];
        if($this->product->isMultiDomainProduct() === TRUE){
            $requiredParamKeys[] = 'domains';
        }
        // check required params for new ov/ev singleDomain/multiDomain ssl order
        if($this->product->isOrganizationProduct() === TRUE){
            array_push($requiredParamKeys, [
                'organization_name',
                'organizationalUnitName',
                'registered_address_line1',
                'registered_no',
                'country',
                'state',
                'city',
                'postal_code',
                'organization_phone',
                'date_of_incorporation',
                'contact_name',
                'contact_title',
                'contact_phone'
            ]);
        }
        // Is this order will be a renew order?
        if($this->renew === TRUE){
            $requiredParamKeys[] = 'renew';
        }
        // check required param keys
        foreach ($requiredParamKeys as $keyName){
            if($this->$keyName === NULL){
                throw new TrustoceanException('Required param('.$keyName.') cannot be empty.', 25015);
            }
        }
        // build request data array
        $requestParams = [];
        foreach ($requiredParamKeys as $keyName){
            $requestParams[$keyName] = $this->$keyName;
        }
        $callResult = $this->callTrustOceanAPI('addSSLOrder', $requestParams);
        $this->dcv_info = $callResult['dcv_info'];
        $this->order_status = $callResult['cert_status'];
        $this->order_id = $callResult['trustocean_id'];
        $this->created_at = $callResult['created_at'];

        // return created and updated order object
        return $this;

    }

    public function callReissue(){
        // check required params for reissue singleDomain/multiDomain ssl order
    }

    public function callChangeDcvMethod($domainName, $newMethod){

    }

    public function callGetStatus(){

    }

    public function callRemoveDomainName($domainName){

    }

    public function callRetryDcvProcess(){

    }

    public function callResendDcvEmails(){

    }

    public function callGetDcvDetails(){

    }

    public function callRevokeCertificate(){

    }

    public function callCancelAndRevokeCertificate(){

    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws TrustoceanException
     */
    protected function callTrustOceanAPI($method,$params){
        # Partner Login Details
        $params['username'] = $this->username;
        $params['password'] =  $this->password;
        $postVars = http_build_query($params);

        $apiURL = "https://api.trustocean.com/ssl/v2/$method"; // API Endpoint located in Beijing CN
        // $apiURL = "https://sapi.trustocean.com/ssl/v2/$method"; // API Endpoint located in London UK

        $curlHandle = curl_init ();
        curl_setopt ($curlHandle, CURLOPT_URL, $apiURL);
        curl_setopt ($curlHandle, CURLOPT_POST, 1);
        curl_setopt ($curlHandle, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt ($curlHandle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt ($curlHandle, CURLOPT_POSTFIELDS, $postVars);
        $callResult = curl_exec ($curlHandle);
        if (!curl_error ($curlHandle)) {
            curl_close ($curlHandle);
            $result = json_decode($callResult, 1);
            if($result['status'] === 'error'){
                throw new TrustoceanException($result['message'], 25000);
            }else{
                return $result;
            }
        }else{
            throw new TrustoceanException('CURL error found, please check your network and api params, try it again or contact us for help.', 25014);
        }
    }

}

