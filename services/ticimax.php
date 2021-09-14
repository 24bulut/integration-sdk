<?php

  namespace App\Services;
  use SoapFault;
  use SoapClient;


  class Ticimax{
    private $hostname, $appSecret, $errorMessage;
    protected $client;
    function __construct($hostName,$appSecret){

      try{
        $this->appSecret = $appSecret;
        $this->hostname = $hostName;
      }
      catch(SoapFault $client){
        $errorMessage = "Bağlantı hatası. Sunucunuzu veya api keylerinizi kontrol ediniz.";
        echo $errorMessage;
      }
      
    }

    public function setUrl($service="UrunServis") {
      $this->client = new SoapClient($this->hostname."/Servis/".$service.".svc?wsdl");
    }
    public function checkErrorMessage(){
      return (is_null($this->errorMessage) || trim($this->errorMessage) == "") ? false : true;
    }
    public function getCategories(){
      if($this->checkErrorMessage()){
        return array();
      }
      else{
        try{
          $categories = $this->client->SelectKategori(array(
            "UyeKodu" => $this->appSecret,
            "kategoriID" => 0
          ));
          $categories = json_encode($categories, JSON_UNESCAPED_UNICODE);
          $categories = json_decode($categories,true);
          $returnedCategories = array();
          foreach ($categories["SelectKategoriResult"]["Kategori"] as $key => $category) {
            $returnedCategories[$category["ID"]] = $category["Tanim"];
          }
          $_SESSION["ticimax"]["categories"] = $returnedCategories;
          return $returnedCategories;
        }
        catch(SoapFault $e){
          //echo $e;
        }
      }
    }
    public function getSuppliers(){
      if($this->checkErrorMessage()){
        return array("error" => $this->errorMessage);
      }
      else{
        try{
          $suppliers = $this->client->SelectTedarikci(array(
            "UyeKodu" => $this->appSecret,
            "tedarikciID" => 0
          ));
          $suppliers = json_encode($suppliers, JSON_UNESCAPED_UNICODE);
          $suppliers = json_decode($suppliers, true);
          $_SESSION["ticimax"]["suppliers"] = $suppliers;
          return $suppliers;
        }
        catch(SoapFault $e){
          //echo $e;
        }
      }
    }
    public function getCurrencyTypes(){
      if($this->checkErrorMessage()){
        return array();
      }
      else{
        try{
          $currencyTypes = $this->client->SelectParaBirimi(array(
            "UyeKodu" => $this->appSecret,
            "ParaBirimiID" => 0
          ));
          $currencyTypes = json_encode($currencyTypes ,JSON_UNESCAPED_UNICODE);
          $currencyTypes = json_decode($currencyTypes, true);
          $_SESSION["ticimax"]["currencyTypes"] = $currencyTypes;
          return $currencyTypes;
        }
        catch(SoapFault $e){
            //echo $e;  
        }
      }
    }


    public function getTecnicalDetailProperty($groupId =0){
      if($this->checkErrorMessage()){
        return array();
      }
      else{
        try{
          $result = $this->client->SelectTeknikDetayOzellik(array(
            "UyeKodu" => $this->appSecret,
            "teknikDetayOzellikId" => 0,
            "dil" => ""
          ));
          $result = json_encode($result, JSON_UNESCAPED_UNICODE);
          $result = json_decode($result,true);
          $returnedresult = array();
          $groupId = (int)$groupId;
          foreach ($result['SelectTeknikDetayOzellikResult']['TeknikDetayOzellik'] as $key => $value) {
            if ($groupId>0) {
              if($groupId == $value['GrupID']){
                $returnedresult[] = array(
                  "propertyid" => $value["ID"],
                  "propertyname" => $value["Tanim"]
                );
              }
            }else{
              $returnedresult[] = array(
                "propertyid" => $value["ID"],
                "propertyname" => $value["Tanim"]
              );
            }
          }
          $TecnicalDetailValue = $this->getTecnicalDetailValue();

          $sonuc = array();
          foreach ($returnedresult as $key => $Property) {
          unset($sondeger);
          foreach ($TecnicalDetailValue as $key => $Value) {

            if ($Property['propertyid']==$Value['valueid']) {
              $sondeger[] = array(
                'id' => $Value['id'],
                'name' => $Value['valuename'],
              );

            }
          }
          $sonuc[] = array(

            'allowCustom' => '',
            'attribute' => array(
              'id' => $Property['propertyid'],
              'name' => $Property['propertyname'],
            ),
            'attributeValues' => $sondeger,
            'categoryId' => '',
            'required' => '',
            'varianter' => '',
            'slicer' => '',
          );


        }

        return $sonuc;


      }
      catch(SoapFault $e){
        //echo $e;
      }
    }
  }

  public function getTecnicalDetailValue(){
    if($this->checkErrorMessage()){
      return array();
    }
    else{
      try{
        $result = $this->client->SelectTeknikDetayDeger(array(
          "UyeKodu" => $this->appSecret,
          "teknikDetayDegerId" => 0,
          "dil" => ""
        ));
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        $result = json_decode($result,true);

          // return $result;
        foreach ($result['SelectTeknikDetayDegerResult']['TeknikDetayDeger'] as $key => $value) {
          $returnedresult[] = array(
            "id" => $value["ID"],
            "valueid" => $value["OzellikID"],
            "valuename" => $value["Tanim"]
          );
        }

        return $returnedresult;
      }
      catch(SoapFault $e){
        //echo $e;
      }
    }
  }

  public function getBrands(){
    if($this->checkErrorMessage()){
      return array();
    }
    else{
      try{
        $brands = $this->client->SelectMarka(array(
          "UyeKodu" => $this->appSecret,
          "markaID" => 0
        ));
        $brands = json_encode($brands, JSON_UNESCAPED_UNICODE);
        $brands = json_decode($brands,true);
        $returnedBrands = array();
        foreach ($brands["SelectMarkaResult"]["Marka"] as $key => $brand) {
          $returnedBrands[] = array(
            "id" => $brand["ID"],
            "name" => $brand["Tanim"]
          );
        }
        $_SESSION["ticimax"]["brands"] = $returnedBrands;
        return $returnedBrands;
      }
      catch(SoapFault $e){
        //echo $e;
      }
    }
  }

  public function insertProduct($productCards, $productCardSettings, $variantSettings){
    if($this->checkErrorMessage()){
      return array();
    }
    else{
      try{
        $result = $this->client->SaveUrun(array(
          "UyeKodu" => $this->appSecret,
          "urunKartlari" => $productCards,
          "ukAyar" => $productCardSettings,
          "vAyar" => $variantSettings
        ));
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        $result = json_decode($result, true);
        return $result;
      }
      catch(SoapFault $e){
        return array(
          "error" => "Ürün Ticimax Platformuna Eklenemedi !",
          "errorMessage" => "SoapFault : $e"
        );
      }
    }
  }
  public function getOrders($webSiparisFiltre, $webSiparisSayfalama){
    
      try{
        $this->setUrl('SiparisServis');
        $result = $this->client->SelectSiparis(array('UyeKodu'=>$this->appSecret,'f'=>$webSiparisFiltre,'s'=>$webSiparisSayfalama));
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        $result = json_decode($result, true);
        return $result;
      }
      catch(SoapFault $e){
        return array(
          "error" => "siparişler çekilemedi !",
          "errorMessage" => "SoapFault : $e"
        );
      }
    }


    public function SetSiparisDurum($SiparisId,$KargoTakipNo){
      $SetSiparisDurumRequest = array(
        'Durum'=>'KargoyaVerildi',
        'KargoTakipNo'=>$KargoTakipNo,
        'MailBilgilendir'=>true,
        'SiparisID'=>$SiparisId
      );
      try{
        $this->setUrl('SiparisServis');
        $result = $this->client->SetSiparisDurum(array('UyeKodu'=>$this->appSecret,'request'=>$SetSiparisDurumRequest));
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        $result = json_decode($result, true);
        return $result;
      }
      catch(SoapFault $e){
        return array(
          "error" => "gönderilemedi !",
          "errorMessage" => "SoapFault : $e"
        );
      }
    }

    
    public function SetSiparisKargoyaVerildi($SiparisId){
    
      try{
        $this->setUrl('SiparisServis');
        $result = $this->client->SetSiparisKargoyaVerildi(array('UyeKodu'=>$this->appSecret,'SiparisId'=>$SiparisId));
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        $result = json_decode($result, true);
        return $result;
      }
      catch(SoapFault $e){
        return array(
          "error" => "gönderilemedi !",
          "errorMessage" => "SoapFault : $e"
        );
      }
    }
  }


?>
