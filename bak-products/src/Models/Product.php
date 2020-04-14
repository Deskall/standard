<?php

namespace Bak\Products\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Director;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Folder;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use Bummzack\SortableFile\Forms\SortableUploadField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\Parsers\URLSegmentFilter;
use Bak\Products\Models\ProductCategory;
use Bak\Products\Models\ProductUseArea;

class Product extends DataObject {

    private static $singular_name = 'Produkt';
    private static $plural_name = 'Produkte';
    private static $table_name = 'BAK_Product';

    private static $db = array(
        'Name' => 'Text',
        'HeaderText' => 'Text',
        'Lead' => 'Text',
        'Features' => 'HTMLText',
        'Description' => 'Text',
        'Table' => 'HTMLText',
        'URLSegment' => 'Varchar(250)',
        'Videos' => 'Text',
        'ShowDetail' => 'Boolean(true)',
        'Number' => 'Varchar(250)',

        'MetaDescription' => 'Text',
        'MetaTitle' => 'Varchar(255)',

    );


    private static $defaults = array(
      'Name' => 'Neuer Eintrag',
      'URLSegment' => 'neuer-eintrag',
      'ShowDetail' => 'Boolean(true)',      
    );

    private static $indexes = array(
      'URLSegment' => true
    );

    private static $extensions = ['Sortable'];

    private static $has_one = array(
      'MainImage' => Image::class,
      'HeaderImage' =>  Image::class
    );

    private static $many_many = array(
      'Images' => Image::class,
      'Downloads' => File::class,
      'Downloads__en_US' => File::class,
      'Downloads__es_ES' => File::class,
      'Categories' => ProductCategory::class,
      'Usages' => ProductUsage::class
    );

    private static $many_many_extraFields = array(
      'Downloads' => array('SortOrder' => 'Int'),
      'Downloads__en_US' => array('SortOrder' => 'Int'),
      'Downloads__es_ES' => array('SortOrder' => 'Int'),
      'Images' => array('SortOrder' => 'Int'),
    );


    private static $searchable_fields = array (
      'Name' => array('title' => 'Produkt'),
      'Categories.Title' => array('title' => 'Kategorie'),
      'Usages.Title' => array('title' => 'Kategorie')
    );

    private static $summary_fields = array (
      'Name' => array('title' => 'Produkt'),
      'showCategories' => 'Kategorien',
      'showUsages' => 'Anwendungen'
    );

    protected function showCategories() {

      $categories = $this->Categories();
      $str = '';
      foreach ($categories as $category )
        $str = $str.$category->Title.',';

      return $str;
    }

    protected function showUsages() {

      $usages = $this->Usages();
      $str = '';
      foreach ($usages as $usage )
        $str = $str.$usage->Title.',';

      return $str;
    }


    public function getCMSFields() {

      $fields = parent::getCMSFields();
     



      
      return $fields;

  }

  public function onBeforeWrite(){


      $oldFolderName =  "Uploads/product-detail/".$this->URLSegment;

      if($this->isChanged('Title')){
        $newFolderName = "Uploads/product-detail/". URLSegmentFilter::create()->filter($this->Title); 

        if ( strcmp($oldFolderName, $newFolderName) != 0)   {
            $imageFolder = Folder::find($oldFolderName);

            if($imageFolder){
              $imageFolder->setName(basename($newFolderName));
              $imageFolder->write();
            }
        }
      }

      $this->URLSegment =  URLSegmentFilter::create()->filter($this->Name);
      $this->URLSegment__en_US =  URLSegmentFilter::create()->filter($this->Name__en_US);
      $this->URLSegment__es_ES =  URLSegmentFilter::create()->filter($this->Name__es_ES);
      
      parent::onBeforeWrite();
  }

  public function hasCategory($ID){
   //  $query = new SQLQuery();
   //  $query->setFrom('Product_Categories')->setSelect('COUNT(*)')->addWhere('ProductID = '.$this->ID)->addWhere('ProductCategoryID = '.$ID);
   // $count = $query->execute()->value();
   // return ($count > 0) ? true : false;
  }
    
  /**
     * Link to this DO
     * @return string
     */
    public function Link($Locale = null) {
      switch ($Locale) {
        case 'de_DE':
          $URLSegment = '/produkte/detail/'.$this->URLSegment;
          break;
        case 'en_US':
          $URLSegment = '/products/detail/'.$this->URLSegment__en_US;
          break;
        case 'es_ES':
          $URLSegment = '/productos/detalle/'.$this->URLSegment__es_ES;
          break;
        default:
          $URLSegment = '/produkte/detail/'.$this->URLSegment;
          break;
      }
      return $URLSegment;
    }

    /**
       * Aboslute Link to this DO
       * @return string
       */
      public function AbsoluteLink($Locale = null) {
        return Director::absoluteURL($this->Link($Locale));
      }


    /**
     * Filter array
     * eg. array('Disabled' => 0);
     * @return array
     */
    public static function getSearchFilter() {
        return array();
    }

    /**
     * Fields that compose the Title
     * eg. array('Title', 'Subtitle');
     * @return array
     */
    public function getTitleFields() {
        return array('Name');
    }

    /**
     * Fields that compose the Content
     * eg. array('Teaser', 'Content');
     * @return array
     */
    public function getContentFields() {
        return array('Lead');
    }

  /**
   * Get the videos
   * @return false|ArrayList
   */
  public function Urls()
  {
      if ($this->Videos){ 
        $urls = array();
        foreach (explode("\n",$this->Videos) as $url){
          $urls[] = $url;
        }
        return $urls;
      }

      return false;
  }

  /**
   * Get the embedded media
   * @return false|Oembed_Result
   */
  public function Media($url)
  {
      // return Oembed::get_oembed_from_url($url, false);

  }

  public function ProductMetaTags($locale) {
      $tags = '<meta name="generator" content="SilverStripe - http://silverstripe.org"><meta http-equiv="Content-type" content="text/html; charset=utf-8">';
      $tags .= '<meta name="description" content="'.$this->getProductMetaDescription( $locale ).'">';
      $tags .= '<link rel="alternate" type="text/html" title="'.Convert::raw2xml($this->Name).'" hreflang="de" href="'.Director::AbsoluteURL($this->Link('de_DE')).'" />' . "\n";
      $tags .= '<link rel="alternate" type="text/html" title="'.Convert::raw2xml($this->Name__en_US).'" hreflang="en" href="'.Director::AbsoluteURL($this->Link('en_US')).'" />' . "\n";
      $tags .= '<link rel="alternate" type="text/html" title="'.Convert::raw2xml($this->Name__es_ES).'" hreflang="es" href="'.Director::AbsoluteURL($this->Link('es_ES')).'" />' . "\n";
    

      return $tags;
      
  }



  public function getProductMetaDescription( $Locale ){
    switch($Locale){
      case "de_DE":
      $description = ( $this->MetaDescription ) ? $this->MetaDescription : $this->Description;
      break;
      case "en_US":
      $description = ( $this->MetaDescription__en_US ) ? $this->MetaDescription__en_US : $this->Description__en_US;
      break;
      case "es_ES":
      $description = ( $this->MetaDescription__es_ES ) ? $this->MetaDescription__es_ES : $this->Description__es_ES;
      break;
      default:
      $description = ( $this->MetaDescription ) ? $this->MetaDescription : $this->Description;
      break;
    }

    return $description;
  }


  public function getProductMetaTitle( $Locale ){
     switch($Locale){
      case "de_DE":
      $title = ( $this->MetaTitle ) ? $this->MetaTitle : $this->Categories()->First()->NameSingular.' '.$this->Name;
      break;
      case "en_US":
       $title = ( $this->MetaTitle__en_US ) ? $this->MetaTitle__en_US : $this->Categories()->First()->NameSingular__en_US.' '.$this->Name__en_US;
      break;
      case "es_ES":
       $title = ( $this->MetaTitle__es_ES ) ? $this->MetaTitle__es_ES : $this->Categories()->First()->NameSingular__es_ES.' '.$this->Name__es_ES;
      break;
      default:
       $title = ( $this->MetaTitle ) ? $this->MetaTitle : $this->Categories()->First()->NameSingular.' '.$this->Name;
      break;
    }
    return $title;
  }

  public function getAllProducts( $Locale ){

    $products = Product::get()->sort(array('SortOrder' => 'DESC'));
    $str = "";
    switch($Locale){
      case "de_DE":
      foreach( $products as $product ){
        if( $this->ID == $product->ID ){
         $str .= '<label><input name="products[]" type="checkbox" checked="checked" value="'.$product->Name.'">'.$product->Name.'</label>';
        }else{
          $str .= '<label><input name="products[]" type="checkbox" value="'.$product->Name.'">'.$product->Name.'</label>';
        }
      }
      break;
      case "en_US":
      foreach( $products as $product ){
        if( $this->ID == $product->ID ){
         $str .= '<label><input name="products[]" type="checkbox" checked="checked" value="'.$product->Name__en_US.'">'.$product->Name__en_US.'</label>';
        }else{
          $str .= '<label><input name="products[]" type="checkbox" value="'.$product->Name__en_US.'">'.$product->Name__en_US.'</label>';
        }
      }
      break;
      case "es_ES":
      foreach( $products as $product ){
        if( $this->ID == $product->ID ){
         $str .= '<label><input name="products[]" type="checkbox" checked="checked" value="'.$product->Name__es_ES.'">'.$product->Name__es_ES.'</label>';
        }else{
          $str .= '<label><input name="products[]" type="checkbox" value="'.$product->Name__es_ES.'">'.$product->Name__es_ES.'</label>';
        }
      }
      break;
      default:
      foreach( $products as $product ){
        if( $this->ID == $product->ID ){
         $str .= '<label><input name="products[]" type="checkbox" checked="checked" value="'.$product->Name.'">'.$product->Name.'</label>';
        }else{
          $str .= '<label><input name="products[]" type="checkbox" value="'.$product->Name.'">'.$product->Name.'</label>';
        }
      }
      break;
    }

    return $str;
  }

  public function StructuredData($Locale){
      $html = '<script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "'.$this->getLocalizedValue('Name').', '.str_replace("\n"," ",strip_tags($this->getLocalizedValue('Lead'))).'",
      "image": "'.Director::AbsoluteURL($this->MainImage()->getURL()).'",
      "description": "'.$this->getProductMetaDescription($Locale).'",
      "brand": {
        "@type": "Thing",
        "name": "BAK AG"
      },
      "sku": "'.$this->ID.'"
    }
    </script>';

    return DBField::create_field('HTMLText',$html);

  }
}