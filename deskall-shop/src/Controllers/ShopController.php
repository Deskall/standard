<?php
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\DateField;
use SilverStripe\i18n\i18n;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\View\Requirements;
use SilverStripe\ORM\ValidationException;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Security\Security;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Control\Director;
use SilverStripe\Security\MemberAuthenticator\MemberLoginForm;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use UndefinedOffset\NoCaptcha\Forms\NocaptchaField;
use SilverStripe\ORM\FieldType\DBHTMLText;

class ShopController extends PageController{
	private static $allowed_actions = ['Category','Product','getActiveCart','updateCart'];

	private static $url_handlers = [
		'kategorie//$URLSegment' => 'Category',
		'produkt//$URLSegment' => 'Product',
	];

	public function init(){
		parent::init();
		
	}

	public function Category(HTTPRequest $request){
		$URLSegment = $request->param('URLSegment');
		if ($URLSegment){
			$category = ProductCategory::get()->filter('URLSegment',$URLSegment)->first();
			if ($category){
				return ['Title' => $category->Title, 'Category' => $category ];
			}
		}
		return $this->httpError(404);
	}

	public function Product(HTTPRequest $request){
		$URLSegment = $request->param('URLSegment');
		if ($URLSegment){
			$product = Product::get()->filter('URLSegment',$URLSegment)->first();
			if ($product){
				return ['Title' => $product->Title, 'Product' => $product ];
			}
		}
		return $this->httpError(404);
	}

	public function getActiveCart(){
	   $id = $this->getRequest()->getSession()->get('shopcart_id');
	   $cart = null;
	   if ($id){
	      $cart = ShopCart::get()->byId($id);
	   }
	   $cart = ($cart) ? $cart : new ShopCart();
	   
	   return $cart->renderWith('Includes/ShopCart');
	}

	public function updateCart(HTTPRequest $request){
	   $id = $this->getRequest()->getSession()->get('shopcart_id');
	   $cart = null;
	   if ($id){
	      $cart = ShopCart::get()->byId($id);
	   }
	   $cart = ($cart) ? $cart : new ShopCart();
	   $productID = $request->getVar('productID');
	   if ($productID){
	   	$product = Product::get()->byId($productID);
	   	if ($product){
	   		$quantity = ($request->getVar('Quantity')) ? $request->getVar('Quantity') : 1;
	   		$sort = $cart->Products()->count() + 1;
	   		$cart->Products()->add($product,['Quantity' => $quantity, 'Sort' => $sort]);
	   	}
	   }
	   $cart->write();

	   return $cart->renderWith('Includes/ShopCart');
	}
}