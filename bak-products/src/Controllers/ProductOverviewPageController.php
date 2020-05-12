<?php

namespace Bak\Products\Controllers;

use PageController;
use HeaderSlide;
use SilverStripe\Core\Convert;
use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Director;
use SilverStripe\CMS\Model\SiteTree;
use Bak\Products\Models\Product;
use Bak\Products\Models\ProductCategory;
use Bak\Products\Models\ProductUseArea;
use Bak\Products\Models\ProductUsage;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Requirements;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;

class ProductOverviewPageController extends PageController 
{

    public function init(){
        parent::init();
        Requirements::javascript('bak-products/javascript/handlebars.min.js');
        Requirements::javascript('bak-products/javascript/bak-products.js');
    }

    private static $allowed_actions = array(
        'category',
        'application',
        'detail',
        'videos',
        'SendProductForm',
        'all'
    );

    private static $url_handlers = array (
        'detalle/$Product!' => 'detail',
        'categoria/$Category' => 'category',
        'kategorie/$Category' => 'category',
        'uso/$UsageArea/$Usage' => 'application',
        'anwendung/$UsageArea/$Usage' => 'application'
    );

    public function getCategories(){
        return ProductCategory::get();
    }

    public function getUseArea(){
        return ProductUseArea::get();
    }

    public function all(){
        $products = Product::get();
        $productList = array();


        $locale = $this->ContentLocale;

        foreach ($products as $product) {

            $tmp =  array();

            if( $product->MainImage()->Link() != "/assets/" ){
                $tmp['image'] = $product->MainImage()->Pad(210,150)->Link();
            }
            $cat = '';
            foreach( $product->Categories() as $category ){
                $cat .= $category->Link($locale)." ";
            }
            $tmp['categories'] = $cat;

            $use = '';
            foreach( $product->Usages() as $usage ){
               $use .= $usage->Link($locale)." ";
            }
            $tmp['usages'] = $use;
            $tmp['link'] = $product->Link($locale);

            $tmp['number'] = $product->Number;
            $tmp['name'] = $product->Name;
            $tmp['lead'] = $product->Lead;
            $tmp['description'] = DBHTMLText::create()->setValue($product->Description)->limitWordCount(30);
            $tmp['linkText'] = _t('BAK.ToProduct','Zum Produkt');
            $tmp['numberText'] = _t('BAK.InvoiceNumber','Best.-Nr.');

        
            array_push($productList, $tmp);
        }

        ob_start();
                    print_r($products->count());
                    $result = ob_get_clean();
                    file_put_contents($_SERVER['DOCUMENT_ROOT']."/log.txt", $result);

        $categories = ProductCategory::get();
        $categoriesList = array();
       
        foreach ($categories as $category) {

            $tmp =  array();
            $tmp['title'] = $category->Title;
            $tmp['description'] = $category->Description;

            $tmp['metatitle'] = $category->printMetaTitle($locale);
            $tmp['metadescription'] = $category->printMetaDescription($locale);

            array_push($categoriesList, $tmp);
        }

        $usages = ProductUsage::get();
        $usagesList = array();
        
        foreach ($usages as $usage) {

            $tmp =  array();
            $tmp['title'] = $usage->Title;
            $tmp['description'] = $usage->Description;
            $tmp['metatitle'] = $usage->printMetaTitle($locale);
            $tmp['metadescription'] = $usage->printMetaDescription($locale);

            array_push($usagesList, $tmp);
        }
        return json_encode(array('products' => $productList, 'categories' => $categoriesList, 'usages' => $usagesList));
    }

    public function index(){
        return ['ShowCategories' => true];
    }


    public function category(HTTPRequest $request){

        switch($this->Locale){
            case "de-DE":
            $urlsegment = 'URLSegment';
            break;
            case "en-US":
            $urlsegment = 'URLSegment__en-US';
            break;
            case "es-ES":
            $urlsegment = 'URLSegment__es-ES';
            break;
            default:
            $urlsegment = 'URLSegment';
            break;
        }
        if ($request->param('Category')){
            $category = ProductCategory::get()->filter(array($urlsegment => $request->param('Category')))->First();
            if(!$category) {
                return $this->httpError(404,'Kategorie nicht gefunden.');
            }
            else{
                return array(
                    'SelectedCategory' => $category,
                    'showProducts' => true,
                    'CustomMetaTitle' => $category->printMetaTitle($this->Locale),
                    'CustomMetaTags' => $category->getMetaTags($this->Locale)
                );
            }
        }
     

            $tags = '<meta name="generator" content="SilverStripe - http://silverstripe.org"><meta http-equiv="Content-type" content="text/html; charset=utf-8">';
            $tags .= '<meta name="description" content="'._t('ProductPage.CATEGORIES','Produkt Kategorien').' - '.$this->MetaDescription.'">';
            // $tags .= '<link rel="alternate" type="text/html" title="Kategorien - '.Convert::raw2xml($this->getTranslation('de-DE')->Title).'" hreflang="de" href="'.Director::AbsoluteURL($this->getTranslation('de-DE')->Link().'kategorie').'" />' . "\n";
            // $tags .= '<link rel="alternate" type="text/html" title="Categories - '.Convert::raw2xml($this->getTranslation('en-US')->Title).'" hreflang="en" href="'.Director::AbsoluteURL($this->getTranslation('en-US')->Link().'category').'" />' . "\n";
            // $tags .= '<link rel="alternate" type="text/html" title="Categoria - '.Convert::raw2xml($this->getTranslation('es-ES')->Title).'" hreflang="es" href="'.Director::AbsoluteURL($this->getTranslation('es-ES')->Link().'categoria').'" />' . "\n";

            return array(
                'showCategories' => true,
                'Locale' => $this->Locale,
                'Title' => $this->Title.': '._t('ProductPage.CATEGORIES','Kategorien'),
                'MetaTitle' => _t('ProductPage.CATEGORIES','Produkt Kategorien'),
                'CustomMetaTags' =>  $tags,
                'isCategoryOverview' => true
            );

        
    }


    public function application(HTTPRequest $request ){
         switch($this->Locale){
            case "de-DE":
            $urlsegment = 'URLSegment';
            break;
            case "en-US":
            $urlsegment = 'URLSegment__en-US';
            break;
            case "es-ES":
            $urlsegment = 'URLSegment__es-ES';
            break;
            default:
            $urlsegment = 'URLSegment';
            break;
        }
        $usage = ProductUsage::get()->filter(array($urlsegment => $request->param('ID').'/'.$request->param('OtherID')))->First();
        if(!$usage) {

          $tags = '<meta name="generator" content="SilverStripe - http://silverstripe.org"><meta http-equiv="Content-type" content="text/html; charset=utf-8">';
          $tags .= '<meta name="description" content="'._t('ProductPage.ANWENDUNG','Produkt Anwendungen').' - '.$this->MetaDescription.'">';
            // $tags .= '<link rel="alternate" type="text/html" title="Anwendungen - '.Convert::raw2xml($this->getTranslation('de-DE')->Title).'" hreflang="de" href="'.Director::AbsoluteURL($this->getTranslation('de-DE')->Link().'anwendung').'" />' . "\n";
            // $tags .= '<link rel="alternate" type="text/html" title="Usages - '.Convert::raw2xml($this->getTranslation('en-US')->Title).'" hreflang="en" href="'.Director::AbsoluteURL($this->getTranslation('en-US')->Link().'application').'" />' . "\n";
            // $tags .= '<link rel="alternate" type="text/html" title="Usos - '.Convert::raw2xml($this->getTranslation('es-ES')->Title).'" hreflang="es" href="'.Director::AbsoluteURL($this->getTranslation('es-ES')->Link().'uso').'" />' . "\n";

      
            return array(
                'showUsages' => true,
                'Locale' => $this->Locale,
                'Title' => $this->Title.': '._t('ProductPage.ANWENDUNG','Anwendungen'),
                'MetaTitle' => _t('ProductPage.ANWENDUNG','Produkt Anwendungen'),
                'CustomMetaTags' =>  $tags,
                'isUsageOverview' => true
            );
        }else{
            return array(
                'SelectedUsage' => $usage,
                'showProducts' => true,
                'Locale' => $this->Locale,
                'CustomMetaTitle' => $usage->printMetaTitle($this->Locale),
                'CustomMetaTags' => $usage->getMetaTags($this->Locale)
            );
        }

    }



    public function detail(HTTPRequest $request ){
        
        $product = Product::get()->filter('URLSegment',$request->param('ID'))->first();

        if(!$product) {
            return $this->httpError(404,_t('BAK.ProductNotFound','Produkt nicht gefunden.'));
        }
        return array (
            'Product' => $product,
            'Title' => $product->Name,
            'MetaTitle' => $product->getProductMetaTitle(),
            'MetaTags' => DBHTMLText::create()->setValue($product->ProductMetaTags()),
            'StructuredData' => $product->StructuredData(),
            'HeaderSlide' => HeaderSlide::get()->first(),
            'Locales' => $product->Locales()
        );

    }


    function SendProductForm($data) {
        ob_start();
                    print_r($_POST);
                    $result = ob_get_clean();
                    file_put_contents($_SERVER['DOCUMENT_ROOT']."/log.txt", $result);
        //Recaptcha validation
        // if (!array_key_exists('g-recaptcha-response', $_POST) || empty($_POST['g-recaptcha-response'])) {
        //   return $this->redirectBack();
        // }

        // $response = $this->recaptchaHTTPPost($_POST['g-recaptcha-response']);
        // $response = json_decode($response, true);


        // if ($response['success'] != 'true') {
        //   return $this->redirectBack();
        // }
        
        $Product = Product::get()->byId(intval($_POST['ID']));
        // Read Data

        $contents = array(
            'de-DE' => array(
                'address' => 'Personalien',
                'name' => 'Name',
                'firma' => 'Firma',
                'str' => 'Adresse',
                'ort' => 'PLZ / Ort',
                'land' => 'Land',
                'email' => 'E-Mail',
                'phone' => 'Telefon',
                'products' => 'Produkte',
                'sendme' => 'Senden Sie mir',
                'message' => 'Nachricht'

            ),
            'en-US' => array(
                'address' => 'Personal data',
                'name' => 'Name',
                'firma' => 'Company',
                'str' => 'Address',
                'ort' => 'ZIP Code / City',
                'land' => 'Country',
                'email' => 'E-Mail',
                'phone' => 'Telephone',
                'products' => 'Products',
                'sendme' => 'Please send me',
                'message' => 'Message',
            ),
           'es-ES' => array(
              'address' => 'Información personal',
              'name' => 'Nombre',
              'firma' => 'Empresa',
              'str' => 'Calle',
              'ort' => 'Código postal / Ciudad',
              'land' => 'país',
              'email' => 'Correo electrónico',
              'phone' => 'Teléfono',
              'products' => 'Productos',
              'sendme' => 'Por favor envíame',
              'message' => 'Mensaje',
          )
        );

        // address
        $address = '<p><strong>'.$contents[$_POST['locale']]['address'].': </strong>'."<br/>";
        $address .= $contents[$_POST['locale']]['name'].': '.$_POST['name']."<br/>";
        if (isset($_POST['firma'])){
             $address .= $contents[$_POST['locale']]['firma'].': '.$_POST['firma']."<br/>";
        }
       
       // $address .= $contents[$_POST['locale']]['str'].': '.$_POST['address']."<br/>";
       // $address .= $contents[$_POST['locale']]['ort'].': '.$_POST['ort']."<br/>";
        $address .= $contents[$_POST['locale']]['land'].': '.$_POST['land']."<br/>";
        $address .= $contents[$_POST['locale']]['email'].': '.$_POST['email']."<br/>";
        if (isset($_POST['telephone'])){
            $address .= $contents[$_POST['locale']]['phone'].': '.$_POST['telephone']."</p>";
        }
        // products
        if( $_POST['products'] ){
            $products = '<p><strong>'.$contents[$_POST['locale']]['products'].': </strong>'."<br/>";
            $products .= implode ( "<br/>" , $_POST['products'] )."</p>";
        }

        // data to send
        if( $_POST['send'] ){
            $send = '<p><strong>'.$contents[$_POST['locale']]['sendme'].': </strong>'."<br/>";
            $send .= implode ( "<br/>" , $_POST['send'] )."</p>";
        }      

        // data to send
        if( $_POST['message'] ){
            $message = '<p><strong>'.$contents[$_POST['locale']]['message'].': </strong>'."<br/>";
            $message .= nl2br($_POST['message'])."</p>";
        }

        $config = SiteConfig::current_site_config(); 
        

        // send email
        $body = '<html>
            <body>
                <h2>'.$config->SubjectEmail.'</h2>
                <p>'.$config->ContentEmail.'</p>

                '.$address.'
                '.$products.'
                '.$send.'
                '.$message.'

            </body>
        </html>';



        // send confirmation

        $bodyConfirmation = '<html>
            <body>
                <h2>'.$config->SubjectEmailConfirmation.'</h2>
                <p>'.$config->ContentEmailConfirmation.'</p>

                '.$address.'
                '.$products.'
                '.$send.'
                '.$message.'

            </body>
        </html>';

        if( $_POST['email'] ){
            $confirmation = new Email( $config->EmailSentFrom , $_POST['email'] , $config->SubjectEmailConfirmation, $bodyConfirmation);
            $email = new Email( $_POST['email'] ,  $config->ReceiverEmail, $config->SubjectEmail, $body);

            $confirmation->send();
            $email->send();
        }

        // do redirect
        ($config->ConfirmationPage()) ? $this->redirect($config->ConfirmationPage()->Link()) : $this->redirectBack() ;

    }



}