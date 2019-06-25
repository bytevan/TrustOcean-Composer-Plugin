<?php
namespace Londry\TrustOceanSSLCertificate\comInterface;

use Londry\TrustOceanSSLCertificate\definition\CertificateType;

class Product{

    public function sdget(){
        $product = new \Londry\TrustOceanSSLCertificate\model\Product(CertificateType::ComodoDvPositiveMultiDomainSsl);


    }
}