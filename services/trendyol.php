<?php

namespace App\Services;


class Trendyol{
  private $username,$password,$supplierId,$guzzle,$options;
  function __construct($username="",$password="",$supplierId="") {
    $this->supplierId = $supplierId;
    $this->username =$username;
    $this->password = $password;
  }

  public function getOrders(){
    $orderDetails = array(
        // Belirli bir tarihten sonraki siparişleri getirir. Timestamp olarak gönderilmelidir.
      'startDate'          => time() - (86400 * 10),
        // Belirtilen tarihe kadar olan siparişleri getirir. Timestamp olarak gönderilmelidir ve startDate ve endDate aralığı en fazla 2 hafta olmalıdır
      'endDate'            => time(),
        // Sadece belirtilen sayfadaki bilgileri döndürür
      'page'               => 0,
        // Bir sayfada listelenecek maksimum adeti belirtir. (Max 200)
      'size'               => 200,
        // Sadece belirli bir sipariş numarası verilerek o siparişin bilgilerini getirir
      'orderNumber'        => '',
        // Siparişlerin statülerine göre bilgileri getirir.	(Created, Picking, Invoiced, Shipped, Cancelled, Delivered, UnDelivered, Returned, Repack, UnSupplied)
      'status'             => '',
        // Siparişler neye göre sıralanacak? (PackageLastModifiedDate, CreatedDate)
      'orderByField'       => 'CreatedDate',
        // Siparişleri sıralama türü? (ASC, DESC)
      'orderByDirection'   => 'DESC',
        // Paket numarasıyla sorgu atılır.
      'shipmentPackagesId' => '',
    );
    $url = "https://api.trendyol.com/sapigw/suppliers/".$this->supplierId."/orders?page=0&status=".$orderDetails['status']."&size=".$orderDetails['size']."&startDate".$orderDetails['startDate']."&endDate=".$orderDetails['endDate']."&orderByField=".$orderDetails['orderByField']."&orderByDirection=".$orderDetails['orderByDirection'];
    $data = $this->guzzle->get($url);
    $orders = json_decode($data->getBody(),true)['content'];
    return $orders;
  }
  public function getCanceled(){
    $orderDetails = array(
        // Belirli bir tarihten sonraki siparişleri getirir. Timestamp olarak gönderilmelidir.
      'startDate'          => time() - (86400 * 10),
        // Belirtilen tarihe kadar olan siparişleri getirir. Timestamp olarak gönderilmelidir ve startDate ve endDate aralığı en fazla 2 hafta olmalıdır
      'endDate'            => time(),
        // Sadece belirtilen sayfadaki bilgileri döndürür
      'page'               => 0,
        // Bir sayfada listelenecek maksimum adeti belirtir. (Max 200)
      'size'               => 150,
        // Sadece belirli bir sipariş numarası verilerek o siparişin bilgilerini getirir
      'orderNumber'        => '',
        // Siparişlerin statülerine göre bilgileri getirir.	(Created, Picking, Invoiced, Shipped, Cancelled, Delivered, UnDelivered, Returned, Repack, UnSupplied)
      'status'             => 'Cancelled',
        // Siparişler neye göre sıralanacak? (PackageLastModifiedDate, CreatedDate)
      'orderByField'       => 'CreatedDate',
        // Siparişleri sıralama türü? (ASC, DESC)
      'orderByDirection'   => 'DESC',
        // Paket numarasıyla sorgu atılır.
      'shipmentPackagesId' => '',
    );
    $url = "https://api.trendyol.com/sapigw/suppliers/".$this->supplierId."/orders?status=".$orderDetails['status']."&size=".$orderDetails['size']."&startDate".$orderDetails['startDate']."&endDate=".$orderDetails['endDate']."&orderByField=".$orderDetails['orderByField']."&orderByDirection=".$orderDetails['orderByDirection'];
    $data = $this->guzzle->get($url);
    return json_decode($data->getBody(),true);
  }

  public function getReturned(){
    $orderDetails = array(
        // Belirli bir tarihten sonraki siparişleri getirir. Timestamp olarak gönderilmelidir.
      'startDate'          => time() - (86400 * 10),
        // Belirtilen tarihe kadar olan siparişleri getirir. Timestamp olarak gönderilmelidir ve startDate ve endDate aralığı en fazla 2 hafta olmalıdır
      'endDate'            => time(),
        // Sadece belirtilen sayfadaki bilgileri döndürür
      'page'               => 0,
        // Bir sayfada listelenecek maksimum adeti belirtir. (Max 200)
      'size'               => 1
    );
    $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$this->supplierId.'/claims?claimItemStatus=Accepted&size='.$orderDetails['size'].'&page=0');
    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
      'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
      'Content-Type:application/json',
      'User-Agent:'. $this->supplierId.' - SelfIntegration'
    ));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    return json_decode(json_encode($result),true);
  }

  public function getQuestions()
  {
    $end = time()*1000;
    $start= (time()*1000)-864000000;
    
    $ch = curl_init("https://api.trendyol.com/sapigw/suppliers/".$this->supplierId."/questions/filter?startDate=".$start."&endDate=".$end."&size=50&orderByDirection=DESC");
    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
      'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
      'Content-Type:application/json',
      'User-Agent:'. $this->supplierId.' - SelfIntegration'
    ));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    return json_decode(json_encode($result),true);
  }



  public function getOrder($orderNumber){

    $ch = curl_init("https://api.trendyol.com/sapigw/suppliers/".$this->supplierId."/orders?orderNumber=".$orderNumber);
    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
      'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
      'Content-Type:application/json',
      'User-Agent:'. $this->supplierId.' - SelfIntegration'
    ));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    return json_decode(json_encode($result),true);
  }

  public function getCategories() {
    $ch = curl_init("https://api.trendyol.com/sapigw/product-categories");
    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
      'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
      'Content-Type:application/json',
      'User-Agent:'. $this->supplierId.' - SelfIntegration'
    ));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    return json_decode(json_encode($result),true);
  }
  public function getAttributes($platformCategoryCode=0) {
    $ch = curl_init("https://api.trendyol.com/sapigw/product-categories/$platformCategoryCode/attributes");
    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
      'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
      'Content-Type:application/json',
      'User-Agent:'. $this->supplierId.' - SelfIntegration'
    ));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    return json_decode(json_encode($result),true);
  }
    /**
     * Kargo takip numarası bildirmeye yarar.
     */
    public function updateTrackingNumber($supplierId,$shipmentPackageId,$trackingNumber){
      $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$supplierId.'/'.$shipmentPackageId.'/update-tracking-number');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode(array(
        "trackingNumber"=>$trackingNumber
      )));
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }

    public function processAlternativeDelivery($supplierId,$shipmentPackageId,$productKey,$phone){
      $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$supplierId.'/shipment-packages/'.$shipmentPackageId.'/alternative-delivery');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS, '{ 
        "isPhoneNumber": true, 
        "trackingInfo": "'.$phone.'",
        "params": 
        {"digitalCode": "'.$productKey.'"}
        }');
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }
    /**
     * Paket Statü Bildirimi
     *
     * NOT: Statü beslemelerini yaparken önce "Picking" sonra "Invoiced" statü beslemesi yapmanız gerekmektedir.
     * "status": "Picking" (Toplanmaya Başlandı Bildirimi)                - params {}
     * "status": "Invoiced" (Fatura Kesme Bildirimi)                      - params {"invoiceNumber": "EME2018000025208"}
     * "status": "UnSupplied" İptal Bildirimi (Tedarik Edememe Bildirimi) - params {}
     *
     */
    public function updatePackage($supplierId, $request, $id){
      $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$supplierId.'/shipment-packages/'.$id);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS, $request);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }

    public function addProducts($supplierid,$product) {
      $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$supplierid.'/v2/products');
      curl_setopt($ch,CURLOPT_POSTFIELDS,$product);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json',
        'User-Agent:'.$supplierid.' - SelfIntegration'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }


    public function updateProducts($supplierId,$product) {
      $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$supplierId.'/v2/products');
      //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch,CURLOPT_POSTFIELDS,$product);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json',
        'User-Agent:'.$supplierId.' - SelfIntegration'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }
    public function updatePriceAndInventory($supplierid,$product) {
      $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$supplierid.'/products/price-and-inventory');
      curl_setopt($ch,CURLOPT_POSTFIELDS,$product);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json',
        'User-Agent:'.$supplierid.' - SelfIntegration'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      return $result;
    }

      public function createAnswer($supplierid,$id,$text) {
        $ch = curl_init('https://api.trendyol.com/sapigw/suppliers/'.$supplierid.'/questions/'.$id.'/answers');
        $params = '{
          "text": "'.$text.'"
        }';
        curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
          'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
          'Content-Type:application/json',
          'User-Agent:'.$supplierid.' - SelfIntegration'
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
          //if($attribute["varianter"] == false && $attribute["slicer"] == false){
        $attr[] = $attribute;
          //}
      }
      return $attr;
    }
      public function getVariants($platformCategoryCode=0) {
        $attributes = $this->getAttributes($platformCategoryCode);
        $attributes = $attributes["categoryAttributes"];
        $variants = array();
        foreach ($attributes as $key => $attribute) {
          $attribute["attribute"]["allowCustom"] = $attribute["allowCustom"];
          $variants[] = $attribute["attribute"];
        }
        return $variants;
      }
      public function getVariantByPlatformCode($platformCategoryCode=0, $platformCode=0){
        $variants = $this->getVariants($platformCategoryCode);
        $returnedVariant=array();
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
      $attributes = $attributes["categoryAttributes"];
      $variantValues = array();
      foreach ($attributes as $key => $attribute) {
        if($attribute['attribute']['id'] == $platformVariantCode){
          $variantValues = $attribute["attributeValues"];
          break;
        }
      }
      return $variantValues;
    }
    public function getLog($batchRequestId) {
      $ch = curl_init("https://api.trendyol.com/sapigw/suppliers/".$this->supplierId."/products/batch-requests/".$batchRequestId);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json',
        'User-Agent:'. $this->supplierId.' - SelfIntegration'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      return json_decode(json_encode($result),true);
    }
    public function getBrands($size=15,$page=1) {

      $ch = curl_init("https://api.trendyol.com/sapigw/brands?size=".$size."&page=".$page);
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json',
        'User-Agent:'. $this->supplierId.' - SelfIntegration'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      return json_decode(json_encode($result),true);
    }
    public function getSearchBrands($name="") {
      $ch = curl_init("https://api.trendyol.com/sapigw/brands/by-name?name=$name");
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json',
        'User-Agent:'. $this->supplierId.' - SelfIntegration'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      return json_decode(json_encode($result),true);
    }
    public function getCargoProviders() {
      $ch = curl_init("https://api.trendyol.com/sapigw/shipment-providers");
      curl_setopt($ch,CURLOPT_HTTPHEADER,array(
        'Authorization:Basic ' . base64_encode($this->username . ':' . $this->password),
        'Content-Type:application/json',
        'User-Agent:'. $this->supplierId.' - SelfIntegration'
      ));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      return json_decode(json_encode($result),true);
    }
  }
  ?>
