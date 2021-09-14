<?php

  namespace App\Services;
  class N11 {
    protected static $_appKey, $_appSecret, $_parameters, $_sclient;
    public $_debug = false;

    public function __construct(array $attributes = array()) {
      self::$_appKey = $attributes['appKey'];
      self::$_appSecret = $attributes['appSecret'];
      self::$_parameters = ['auth' => ['appKey' => self::$_appKey, 'appSecret' => self::$_appSecret]];
    }

    public function setUrl($url) {
      $options = array(
        'cache_wsdl' => 0,
        'trace' => 1,
        'stream_context' => stream_context_create(array(
          'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
          )
        )));
      libxml_disable_entity_loader(false);
      self::$_sclient = new \SoapClient($url,$options);

    }


    public function GetTopLevelCategories() {
      $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
      $categories = self::$_sclient->GetTopLevelCategories(self::$_parameters);
      $sub = array();
      foreach ($categories->categoryList->category as $key => $value) {
        $sub[$value->id] = $value->name;
      }
      return $sub;
    }
    public function GetSubCategories($categoryId){
      $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
      self::$_parameters['categoryId'] = $categoryId;
      $categories = self::$_sclient->GetSubCategories(self::$_parameters);
      if(!isset($categories->category->subCategoryList->subCategory)) return array();
      $sub = array();
      foreach ($categories->category->subCategoryList->subCategory as $key => $value) {
        if(isset($value->id)) $sub[$value->id] = $value->name;
        else {

        }
      }
      return $sub;
    }



    public function GetShipmentCompanies() {
      $this->setUrl('https://api.n11.com/ws/ShipmentCompanyService.wsdl');
      return self::$_sclient->GetShipmentCompanies(self::$_parameters);
    }
    public function GetShipmentTemplateList() {
      $this->setUrl('https://api.n11.com/ws/ShipmentService.wsdl');
      return self::$_sclient->GetShipmentTemplateList(self::$_parameters);
    }
    public function GetShipmentTemplateRequest() {
      $this->setUrl('https://api.n11.com/ws/ShipmentService.wsdl');
      return self::$_sclient->GetShipmentTemplateList(self::$_parameters);
    }
    public function GetCities() {
      $this->setUrl('https://api.n11.com/ws/CityService.wsdl');
      return self::$_sclient->GetCities(self::$_parameters);
    }
    public function GetProductQuestion($pageSize, $currentPage) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters['productQuestionSearch'] = [
        'productId' => '',
        'buyerEmail' => '',
        'subject' => '',
        'status' => '',
        'questionDate' =>'' 
      ];
      self::$_parameters['pagingData'] = ['currentPage' => $currentPage,'pageSize' => $pageSize];
      return self::$_sclient->GetProductQuestionList(self::$_parameters);
    }

    public function GetProductList($itemsPerPage, $currentPage) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters['pagingData'] = ['itemsPerPage' => $itemsPerPage, 'currentPage' => $currentPage];
      return self::$_sclient->GetProductList(self::$_parameters);
    }

    public function GetProductBySellerCode($sellerCode) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters['sellerCode'] = $sellerCode;
      return self::$_sclient->GetProductBySellerCode(self::$_parameters);
    }
    public function GetProductByProductId($sellerCode) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters['productId'] = $sellerCode;
      return self::$_sclient->GetProductByProductId(self::$_parameters);
    }
    public function SaveProduct(array $product = Array()) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters['product'] = $product;
      return self::$_sclient->SaveProduct(self::$_parameters);
    }
    public function UpdateProductBasic(array $product = Array()) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters = $product;
      self::$_parameters['auth'] = ['appKey' => self::$_appKey, 'appSecret' => self::$_appSecret];
      return self::$_sclient->UpdateProductBasic(self::$_parameters);
    }
    public function DeleteProductBySellerCode($sellerCode) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters['productSellerCode'] = $sellerCode;
      return self::$_sclient->DeleteProductBySellerCode(self::$_parameters);
    }
    public function OrderList(array $searchData = Array()) {
      $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
      self::$_parameters['searchData'] = $searchData;
      self::$_parameters['pagingData'] =  ['pageSize' =>20, 'currentPage' => 0];
      return self::$_sclient->OrderList(self::$_parameters);
    }
    public function GetProductQuestionList(array $searchData = Array()) {
      $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
      self::$_parameters['productQuestionSearch'] = array(
        "productId"=>"",
        "buyerEmail"=>"",
        "subject"=>"",
        "status"=>"",
        "questionDate"=>"",
      );

      self::$_parameters['pagingData'] = ['pageSize' =>100, 'currentPage' => 0];

      return self::$_sclient->GetProductQuestionList(self::$_parameters);
    }

    public function ClaimCancelList(array $searchData = array()) {
      $this->setUrl('https://api.n11.com/ws/ClaimCancelService.wsdl');
      self::$_parameters['searchData'] = $searchData;
      self::$_parameters['pagingData'] = ['itemsPerPage' =>20, 'currentPage' => 0];
      return self::$_sclient->ClaimCancelList(self::$_parameters);
    }

    public function OrderItemAccept($id,$numberOfPackages){
      $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
      self::$_parameters['orderItemList']['orderItem']['id'] = $id;
      self::$_parameters['numberOfPackages'] = $numberOfPackages;
      return self::$_sclient->OrderItemAccept(self::$_parameters);
    }



    public function OrderItemReject(array $orderItemIdList=array(), $rejectReason="", $rejectReasonType=""){
      $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
      self::$_parameters['orderItemList'] = $orderItemIdList;
      self::$_parameters['rejectReason'] = $rejectReason;
      self::$_parameters['rejectReasonType'] = $rejectReasonType;
      return self::$_sclient->OrderItemReject(self::$_parameters);
    }

    public function MakeOrderItemShipment(array $orderItemIdList=array()){
      $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
      self::$_parameters['orderItemList'] = $orderItemIdList;
      return self::$_sclient->MakeOrderItemShipment(self::$_parameters);
    }




    public function DetailedOrderList(array $searchData = Array()) {
      $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
      self::$_parameters['searchData'] = $searchData;
      self::$_parameters['pagingData'] = array();
      return self::$_sclient->DetailedOrderList(self::$_parameters);
    }
    public function OrderDetail(array $searchData = Array()) {
      $this->setUrl('https://api.n11.com/ws/OrderService.wsdl?WSDL');
      self::$_parameters['orderRequest'] = $searchData;
      return self::$_sclient->OrderDetail(self::$_parameters);
    }

    public function GetCategoryAttributes($categoryId){
      $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
      self::$_parameters['categoryId'] = $categoryId;
      self::$_parameters['pagingData']['currentPage'] = 0;

      $result = self::$_sclient->GetCategoryAttributes(self::$_parameters);
      $attributes = $result->category->attributeList->attribute;
      $pageCount= (int)$result->category->metadata->pageCount;
      $attr = array();

      foreach ($attributes as  $attribute) {
        $attr[$attribute->id] = array(
          'attribute' => array(
            'id'=> $attribute->id,
            'name'=> $attribute->name
          ),
          'required' =>$attribute->mandatory,
          'attributeValues' =>json_decode(json_encode($attribute->valueList->value),true)
        );
      }

     
      return $attr;
    }


    public function GetCategoryAttributeValue($attributeValue){
      $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
      self::$_parameters['categoryProductAttributeId'] = $attributeValue;
      self::$_parameters['pagingData']['currentPage'] = 2;
      $result = self::$_sclient->GetCategoryAttributeValue(self::$_parameters);
      return $result;
    }


  }
  ?>
