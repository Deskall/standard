<?php



use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Security\Group;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Control\Controller;

class ShopConfig extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar'
       'OrderNumberOffset' => 'Varchar'
       
    );

    private static $singular_name = "Einstellungen";
    private static $plural_name = "Einstellungen";

    private static $has_one = [
      
       'AGBFile' => File::class
       
    ];

    private static $owns = [
       
        'AGBFile'
       
    ];

    private static $summary_fields = [
        'Title' => ['title' => 'Titel']
    ];


    public function fieldLabels($includerelation = true){
    $labels = parent::fieldLabels($includerelation);
    $labels['Title'] = _t(__CLASS__.'.Title','Titel');
   
    $labels['AGBFile'] = _t(__CLASS__.'.AGBFile','AGB Datei');
   
    return $labels;
    }

   

    public function onBeforeWrite(){
        parent::onBeforeWrite();
       
    }

    public function onAfterWrite()
    {
       
        parent::onAfterWrite();
       
    }



    public function getCMSFields()
    {
       $fields = parent::getCMSFields();
       $fields->addFieldToTab('Root.Main',UploadField::create('AGBFile',$this->fieldLabels()['AGBFile'])->setIsMultiUpload(false)->setFolderName('Uploads/Shop'));
      
      

       return $fields;
    }


    public function parseString($string,$order)
    {


        $variables = array(
            '$SiteName'       => SiteConfig::current_site_config()->Title,
            '$Datum'          => date('d.m.Y')
            // '$LoginLink'      => Controller::join_links(
            //     $absoluteBaseURL,
            //     singleton(Security::class)->Link('login')
            // ),
           
        );
        if ($order){
           foreach (array('Name', 'Vorname', 'Email','Gender','Price') as $field) {
              $variables["\$Order.$field"] = $order->$field;
           }
        }


        if ($order->Customer()){
           foreach (array('Gender','Name','FirstName','Email', 'Birthday','Address','City','PostalCode') as $field) {
              $variables["\$Customer.$field"] = $order->Customer()->$field;
           }
        

           foreach (array('printAddress','printTitle') as $method) {
              $variables["\$Customer.$method"] = $order->Customer()->{$method}();
           }
        }
        
        

        return str_replace(array_keys($variables), array_values($variables), $string);
    }
  


}