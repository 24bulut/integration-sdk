<?php
  namespace App\Services;
  use SoapClient;
  class Gittigidiyor{
    protected $apiKey,$secretKey,$login,$password,$sign;
    function __construct($apiKey,$secretKey,$login,$password){
      $this->apiKey    = $apiKey;
      $this->secretKey = $secretKey;
      $this->login     = $login;
      $this->password  = $password;
    }
    
    
    public function client($action,array $params,$soap_url){
        $client = new SoapClient($soap_url, array('login' => $this->login, 'password' => $this->password , 'authentication' => SOAP_AUTHENTICATION_BASIC));
        $response = json_decode(json_encode($client->__soapCall($action,$params)),true);
        return $response;
    }

    public function getProduct($productId=0,$itemId=0){
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array(
        "apiKey" => $this->apiKey,
        "sign" => $sign ,
        "time"=>$time,
        "productId"=>$productId,
        "itemId"=>$itemId,
        "lang"=> "tr"
      );
      $response = $this->client('getProduct',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualProductService?wsdl");
      return $response;
    }
    public function getOrders(){
      $status="A";
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("apiKey" => $this->apiKey,
                      "sign" => $sign,
                      "time" => $time,
                      "withData" => true,
                      "byStatus" => $status,
                      "byUser" => "",
                      "orderBy" => "A",
                      "orderType" => "A",
                      "pageNumber" => 1,
                      "pageSize" => 50,
                      "lang" => 'tr');
      $response = $this->client('getPagedSales',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualSaleService?wsdl");
      return $response;
    }
    public function getCategoryVariantSpecs($categoryCode){
      $status="A";
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("categoryCode" => $categoryCode,
                      "lang" => 'tr');
      $response = $this->client('getCategoryVariantSpecs',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/CategoryService?wsdl");

      if (isset($response['specs']['spec'][0])) {
        $attributes = $response['specs']['spec'];
      }else{
        $attributes[0] = $response['specs']['spec'];

      }

      $variants = array();
      $i=0;
      foreach ($attributes as $key => $attribute) {
          $variants[$i]['name'] = $attribute["name"];
          $variants[$i]['id'] = $attribute["nameId"];
          $i++;
      }
      return $variants;
    }
    public function getCategoryVariantSpecValues($categoryCode,$variantCode){
      $status="A";
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("categoryCode" => $categoryCode,
                      "lang" => 'tr');
      $response = $this->client('getCategoryVariantSpecs',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/CategoryService?wsdl");

      $values = array();
      if (isset($response["specs"]["spec"][0])) {
        $temp = $response["specs"]["spec"];
      }else{
        $temp[0] = $response["specs"]["spec"];

      }

      foreach ($temp as $key => $spec) {
        if($spec["nameId"] == $variantCode){
            foreach ($spec["specValues"]["specValue"] as $value) {
              $values[] = array(
                "id" => $value["valueId"],
                "name" => $value["value"]
              );
            }
          break;
        }
      }
      return $values;
    }
    public function insertProductWithNewCargoDetail($productId,$product){
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("apiKey" => $this->apiKey,
                      "sign" => $sign ,
                      "time"=>$time,
                      "itemId"=>$productId,
                      "product"=>$product,
                      "forceToSpecEntry"=>false,
                      "nextDateOption"=> false,
                      "lang"=> "tr");
      $response = $this->client('insertProductWithNewCargoDetail',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualProductService?wsdl");
      return $response;
    }
    public function insertAndActivateProduct($productId,$product){
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("apiKey" => $this->apiKey,
                      "sign" => $sign ,
                      "time"=>$time,
                      "itemId"=>$productId,
                      "product"=>$product,
                      "forceToSpecEntry"=>false,
                      "nextDateOption"=> false,
                      "lang"=> "tr");
      
      $response = $this->client('insertAndActivateProduct',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualProductService?wsdl");
      return $response;
    }
    public function updateProductVariants($productId,$productVariant){
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("apiKey" => $this->apiKey,
                      "sign" => $sign ,
                      "time"=>$time,
                      "productId"=>$productId,
                      "itemId"=>"",
                      "productVariant"=>$productVariant,
                      "lang"=> "tr");
      
      $response = $this->client('updateProductVariants',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualProductService?wsdl");
      return $response;
    }
    public function updateProductWithNewCargoDetail($productId,$itemId,$product){
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("apiKey" => $this->apiKey,
                      "sign" => $sign ,
                      "time"=>$time,
                      "itemId"=>$itemId,
                      "productId"=>$productId,
                      "product"=>$product,
                      "onSale"=>false,
                      "forceToSpecEntry"=>false,
                      "nextDateOption"=> false,
                      "lang"=> "tr");
      $response = $this->client('updateProductWithNewCargoDetail',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualProductService?wsdl");
      return $response;
    }
    public function getCategorySpecsWithDetail($categoryCode){
        $time = round(microtime(1) * 1000);
        $sign = md5($this->apiKey.$this->secretKey.$time);
        $params = array("categoryCode" => $categoryCode,
                        "lang" => 'tr');
        $response = $this->client('getCategorySpecsWithDetail',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/CategoryService?wsdl");
        $values = array();
        $i=0;
        foreach ($response["specs"]["spec"] as $key => $spec) {
              $values[$i]['attribute'] = array(
                'id' =>$spec['specId'],
                'name'=>$spec['name']
              );
              $values[$i]['required']=$spec["required"];
              $values[$i]['allowCustom']=false;
              $values[$i]['type']=$spec["type"];
              foreach ($spec["values"]["value"] as $value) {
                $values[$i]["attributeValues"][] = array(
                  "id" => $value["specId"],
                  "name" => $value["name"]
                );
              }
              $i++;
        }
        return $values;
      }
    public function getCities(){
      $params = array(
        "startOffSet" => 0,
        "rowCount" => 82,
        "lang" => 'tr'
      );
      $response = $this->client('getCities',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/CityService?wsdl");
      return $response;
    }
    public function getCargoCompany(){
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array(
        "apiKey" => $this->apiKey,
        "sign" => $sign ,
        "time"=>$time,
        "lang" => 'tr'
      );
      $response = $this->client('getCargoCompany',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualCargoService?wsdl");
      return $response;
    }
    public function getCategories(){
      $ctgArr = array();
      for ($i=0; $i < 80 ; $i++) {
        $params = array(
                "startOffSet" => $i*100,
                "rowCount" => 100,
                "withSpecs" => false,
                "withDeepest" => true,
                "withCatalog" => false,
                "lang" => 'tr'
          );
        $response = $this->client('getCategories',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/CategoryService?wsdl");

        $categories = array();
        if(!isset($response['categories']['category'])) break;
        else{
          foreach ($response['categories']['category'] as $key => $value) {
            if ($value['deepest']==1) {
              
              $categories[$value['categoryCode']] = $value['categoryName'];
            }
          }
          $ctgArr =$ctgArr + $categories;
        }
      }
      return $ctgArr;
    }
    /**
     * Kargolama aşamasındaki siparişi iptal eder.
     *
     * @access public
     * @param string $saleCode (Sipariş kodu)
     * @param int    $reasonId (İptal sebebi ID'si)
     * @return object
     */
    public function cancelSale($saleCode="", $reasonId=0){
      $time = round(microtime(1) * 1000);
      $sign = md5($this->apiKey.$this->secretKey.$time);
      $params = array("apiKey" => $this->apiKey,
                      "sign" => $sign ,
                      "time"=>$time,
                      "saleCode"=>$saleCode,
                      "reasonId"=>$reasonId,
                      "lang"=> "tr");
      $response = $this->client('cancelSale',$params,"https://dev.gittigidiyor.com:8443/listingapi/ws/IndividualSaleService?wsdl");
      return $response;
    }
  }
?>
