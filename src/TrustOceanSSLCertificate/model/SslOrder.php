<?php
namespace Londry\TrustOceanSSLCertificate\model;

use blobfolio\domain\domain;
use Londry\TrustOceanSSLCertificate\TrustoceanException;

class SslOrder{

    /**
     * @var string
     * The Account Email Address of the Reseller,
     * to register an account please access https://www.trustocean.com ,
     * and submit an ticket or contact online-staff to upgrade your account to
     * a Reseller Account, then you can access this API.
     */
    protected $username;

    public function setUserName($username){
        $this->username = $username;
    }

    /**
     * @var string
     * The API Token of your Reseller Account,
     * you can generate one new API Token from
     * https://console.trustocean.com/partner/api-setting
     */
    protected $password;
    public function setPassword($apiToken){
        $this->password = $apiToken;
    }

    /**
     * @var int
     * The Product ID of products provide by TrustOcean.
     */
    protected $pid;

    /**
     * @var Product
     * The instanct of product.
     */
    private $product;

    /**
     * @param $CertificateType
     * Set the product pid for Api Order.
     */
    public function setCertificateType($CertificateType){
        $this->product = new Product($CertificateType);
        $this->pid = $this->product->getPid();
    }

    protected $period;

    /**
     * @param $period
     * @throws TrustoceanException
     */
    public function setCertificatePeriod($period){
        if(!in_array($period, $this->product->getAvaliablePeriod())){
            throw new TrustoceanException('It\'s not a valid Period('.$period.') option for Product '.$this->product->getName(), 25001);
        }
        $this->period = $period;
    }

    protected $csr_code;

    /**
     * @param Csr $csrCode
     * @throws TrustoceanException
     */
    public function setCsrCode(Csr $csrCode){
        if($csrCode->isWildcardCommonName() && !$this->product->isWildcardProduct()){
            throw new TrustoceanException('Invalid CommonName of your CSR, this product not allowed protect wildcard domain.', 25008);
        }
        if(!$csrCode->isEmptyEmailAddress()){
            $this->contact_email = $csrCode->getEmailAddress();
        }
        $this->csr_code = $csrCode->getValidaCsrContent();
    }

    protected $contact_email;

    protected $dcv_method;

    protected $unique_id;
    public function setUniqueId($uniqueId){
        $this->unique_id = $uniqueId;
    }

    protected $domains;

    /**
     * @param array $domainArray
     * @throws TrustoceanException
     */
    public function setDomains($domainArray){
        foreach ($domainArray as $key => $domainName){
            $theDomain = new domain($domainName);
            if(!$theDomain->is_ascii()){
                throw new TrustoceanException('Invalid DomainName('.$domainName.'), please convert it to ASCII format', 25009);
            }
            if($theDomain->is_ip() && $this->product->isSupportIpAddress() === FALSE){
                throw new TrustoceanException('Invalid DomainName('.$domainName.'), the product you choose does not support an IP address.', 25010);
            }
            if(strpos($domainName, '*.') !== FALSE && $this->product->isWildcardProduct() === FALSE){
                throw new TrustoceanException('Invalid DomainName('.$domainName.'), the product you choose does not support wildcard domain name.', 250014);
            }
            if(trim($domainName) !== $domainName){
                throw new TrustoceanException('Invalid DomainName('.$domainName.'), please remove any spaces.', 25011);
            }
            if(trim($theDomain) == ""){
                throw new TrustoceanException('Invalid DomainName('.$domainName.').', 25012);
            }
        }

        if(count($domainArray) != count(array_unique($domainArray))){
            throw new TrustoceanException('Duplicate domain name found, please check your domain names.', 25013);
        }

        $this->domains = implode(',', $domainArray);
    }

    /**
     * @return array
     */
    public function getDomains(){
        return explode(',', $this->domains);
    }

    protected $renew = 'no';

    /**
     * @param bool $isRenew
     */
    public function setRenew($isRenew = false){
        if($isRenew === false){
            $this->renew = 'no';
        }else{
            $this->renew = 'yes';
        }
    }

    protected $organization_name = NULL;

    /**
     * @param string $organizationName
     */
    public function setOrganizationName($organizationName){
        $this->organization_name = $organizationName;
    }

    /**
     * @return string|NULL
     */
    public function getOrganizationName(){
        return $this->organization_name;
    }

    protected $organizationalUnitName = NULL;

    /**
     * @param string $organizationalUnitName
     */
    public function setOrganizationalUnitName($organizationalUnitName){
        $this->organizationalUnitName = $organizationalUnitName;
    }

    /**
     * @return string|null
     */
    public function getOrganizationalUnitName(){
        return $this->organizationalUnitName;
    }

    protected $registered_address_line1 = NULL;

    /**
     * @param string $registered_address_line1
     */
    public function setRegisteredAddressLine1($registered_address_line1){
        $this->registered_address_line1 = $registered_address_line1;
    }

    /**
     * @return string|null
     */
    public function getRegisteredAddressLine1(){
        return $this->registered_address_line1;
    }

    protected $registered_no;

    protected $country;

    protected $state;

    protected $city;

    protected $postal_code;

    protected $organization_phone;

    protected $date_of_incorporation;

    protected $contact_name;

    protected $contact_title;

    protected $contact_phone;
}

