<?php

namespace App\Services;


class Ciceksepeti{

    private $apiKey;
    function __construct($apiKey="") {
        $this->apiKey =$apiKey;
    }

    public function getCategories() {
      $ch = curl_init('https://apis.ciceksepeti.com/api/v1/Categories');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
    }
  
    public function getAttributes($platformCategoryCode=0) {
      $ch = curl_init("https://apis.ciceksepeti.com/api/v1/categories/$platformCategoryCode/attributes");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
    }


    public function getOrders($params) {
      $ch = curl_init('https://apis.ciceksepeti.com/api/v1/Order/GetOrders');
      curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }


    public function statusupdatewithsupplierintegration($params)
    {
      $ch = curl_init('https://apis.ciceksepeti.com/api/v1/Order/statusupdatewithsupplierintegration');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }


    public function readyForCargoWithcsIntegration($params)
    {
      $ch = curl_init('https://apis.ciceksepeti.com/api/v1/Order/readyforcargowithcsintegration');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }

    public function statusUpdateWithcsIntegration($params)
    {
      $ch = curl_init('https://apis.ciceksepeti.com/api/v1/Order/statusupdatewithcsintegration');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }

      public function getCategoryAttributes($platformCategoryCode=0) {
        $attributes = $this->getAttributes($platformCategoryCode);
         $attributes = $attributes["categoryAttributes"];
         $attr = array();
         foreach ($attributes as $key => $attribute) {
           if($attribute["varianter"] == false){
             $attr[] = array(
               'attribute' => array(
                  'id'=> $attribute['attributeId'],
                  'name'=> $attribute['attributeName']
               ),
               'required' =>$attribute['required'],
               'attributeValues' =>$attribute['attributeValues']

             );
           }
         }
         return $attr;
      }

      public function getVariants($platformCategoryCode=0) {
        $attributes = $this->getAttributes($platformCategoryCode);
        $attributes = $attributes["categoryAttributes"];
        $variants = array();
        foreach ($attributes as $key => $attribute) {
            if ($attribute['varianter']==true) {
                $variants[] = array(
                  "allowCustom"=>false,
                  'name'=>$attribute["attributeName"],
                  'id'=>$attribute["attributeId"]
                );
            }
        }
        return ($variants);
      }

      public function getVariantByPlatformCode($platformCategoryCode=0, $platformCode=0){
        $variants = $this->getVariants($platformCategoryCode);
        $returnedVariant = array();
        foreach ($variants as $key => $variant) {
          if($variant["id"] == $platformCode){
            $returnedVariant = $variant;
            break;
          }
        }
        return $returnedVariant;
      }

      public function getVariantsValues($platformCategoryCode=0,$platformVariantCode=0) {
         $attributes = $this->getAttributes($platformCategoryCode);
         //return $attributes;
         $attributes = $attributes["categoryAttributes"];
         $variantValues = array();
         foreach ($attributes as $key => $attribute) {
           if( $attribute['attributeId'] == $platformVariantCode){
              $variantValues = $attribute["attributeValues"];
              break;
            }
         }
         return $variantValues;
    }

    public function addProducts($product) {
      $ch = curl_init('https://apis.ciceksepeti.com/api/v1/Products/');
      curl_setopt($ch,CURLOPT_POSTFIELDS,$product);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }

    public function getLog($batchRequestId) {
      $ch = curl_init("https://apis.ciceksepeti.com/api/v1/Products/batch-status/".$batchRequestId);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'.$this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return json_decode($result,true);
   }

   public function updateProducts($product) {
      $ch = curl_init('https://apis.ciceksepeti.com/api/v1/Products/price-and-stock');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS,$product);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'x-api-key:'. $this->apiKey,
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }
  }
  
?>
