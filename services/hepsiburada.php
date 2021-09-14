<?php
  namespace App\Services;
  use CURLFile;
    
  class Hepsiburada
  {
    protected $username,$password,$merchantId,$guzzle,$authToken;
    protected $categories;
    function __construct($username,$password,$merchantId) {
      $this->merchantId = $merchantId;
      $this->username = $username;
      $this->password = $password;
      $this->getAuthToken($username,$password);

    }


    public  function getAuthToken($username,$password)
    {
      $postData = array(
        'username' => $username,
        'password' => $password,
        'authenticationType' => 'INTEGRATOR'
      );
      $json = json_encode($postData);
      $ch = curl_init('https://mpop.hepsiburada.com/api/authenticate');
      curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);

      $arr = json_decode($result,true);
      $this->authToken= $arr['id_token'];

    }


    public function getBarer()
    {
      return $this->authToken;
    }

    public function getAttributes($categoryCode){

      $ch = curl_init('https://mpop.hepsiburada.com/product/api/categories/'.$categoryCode.'/attributes');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        'Content-Type:multipart/form-data',
        'Authorization:Bearer '.$this->authToken
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
      
    }
    public function getVariants($categoryCode){

      $ch = curl_init('https://mpop.hepsiburada.com/product/api/categories/'.$categoryCode.'/attributes');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        'Content-Type:multipart/form-data',
        'Authorization:Bearer '.$this->authToken
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
    }

    public function getVariantValues($categoryCode,$variantCode){

      $ch = curl_init('https://mpop.hepsiburada.com/product/api/categories/'.$categoryCode.'/attribute/'.$variantCode);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        'Content-Type:multipart/form-data',
        'Authorization:Bearer '.$this->authToken
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
    }


    public function getCategoryAttributeValues($categoryCode,$attributeId){
      $ch = curl_init('https://mpop.hepsiburada.com/product/api/categories/'.$categoryCode.'/attribute/'.$attributeId);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        'Content-Type:multipart/form-data',
        'Authorization:Bearer '.$this->authToken
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
    }

    public function getCategoryAttributes($categoryCode){

      $ch = curl_init('https://mpop.hepsiburada.com/product/api/categories/'.$categoryCode.'/attributes/');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        'Content-Type:multipart/form-data',
        'Authorization:Bearer '.$this->authToken
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
    
    }


    public function getCategories() {

      $ch = curl_init('https://mpop.hepsiburada.com/product/api/categories/get-all-categories?leaf=true&page=0&size=2000');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        'Content-Type:multipart/form-data',
        'Authorization:Bearer '.$this->authToken
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
    }


    public function addProducts($jsonPath="") {

      $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://mpop.hepsiburada.com/product/api/products/import');    // Define target site
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array (
          'Content-Type:multipart/form-data',
          'Authorization:Bearer '.$this->authToken
        )); // No http head
        //curl_setopt($ch, CURLOPT_REFERER, $ref);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);      // Return page in string

        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array (
          'file' => new CURLFile ($jsonPath)));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);      // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);

      # Execute the PHP/CURL session and echo the downloaded page
        $page = curl_exec($ch);


        $err = curl_error($ch);
        $info =curl_getinfo($ch);

      # Close the cURL session
        curl_close($ch);


        return $page;
      }

      public function getLog($trackingId)
      {

        $ch = curl_init('https://mpop.hepsiburada.com/product/api/products/status/'.$trackingId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array (
          'Content-Type:multipart/form-data',
          'Authorization:Bearer '.$this->authToken
        ));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,true);
      }

      public function getOrders()
      {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://oms-external.hepsiburada.com/orders/merchantid/".$this->merchantId."?offset=0&limit=100");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
          'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
          'Content-Type:application/json'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return json_decode($server_output,true);
    }
    public function updateProducts($xml_data)
    {
        $ch = curl_init(); 
        curl_setopt($ch,CURLOPT_URL,"https://listing-external.hepsiburada.com/listings/merchantid/".$this->merchantId."/inventory-uploads"); 
        $headers = array(
              'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
              'Content-Type:application/xml'
            );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
        //curl_setopt($ch,CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,"$xml_data");
        $output=curl_exec($ch); 
        curl_close($ch); 
        return $output; 
  }



  }







?>
