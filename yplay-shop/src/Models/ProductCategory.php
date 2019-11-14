<?php

use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\Image;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\Subsites\Extensions\FileSubsites;
use SilverStripe\Subsites\State\SubsiteState;
use SilverStripe\Assets\Folder;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Control\Controller;

class ProductCategory extends DataObject {

	private static $singular_name = "Kategorie";
	private static $plural_name = "Kategorien";


	private static $db = [
	'Title' => 'Varchar',
	'Subtitle' => 'Text',
	'Description' => 'HTMLText',
	'Code' => 'Varchar'
	];

	private static $has_one = [
		'Icon' => Image::class
	];

	private static $has_many = [
	'Products' => Product::class,
	'Options' => ProductOption::class
	];

	private static $owns = [
		'Icon',
		'Products'
	];

	private static $extensions = [
		'Sortable',
		'Activable'
	];

	private static $summary_fields = [
		'Thumbnail',
		'Title',
		'Subtitle',
		'NiceProducts' => ['title' => 'Produkte']
	];

	private static $searchable_fields = ['Title'];

	public function getFolderName(){
		if ($this->Title){
			return "Uploads/Produkte/".URLSegmentFilter::create()->filter($this->Title);
		}
		
		return "Uploads/Produkte/tmp";
		
	}

	public function Thumbnail(){
		$html = '';
		if ($this->Icon()->exists()){
			$html = '<img src="'.$this->Icon()->URL.'" width="100" >';
		}
		return DBHTMLText::create()->setValue($html);
	}

	public function NiceProducts(){
		$html = '';
		foreach ($this->Products()->filter('ClassName',Product::class) as $p) {
			$html .= $p->Title."<br>";
		}
		return DBHTMLText::create()->setValue($html);
	}


	public function onBeforeWrite(){
		if ($this->ID > 0){
	        $changedFields = $this->getChangedFields();
	        //Update Folder Name
	        if ($this->isChanged('Title') && ($changedFields['Title']['before'] != $changedFields['Title']['after'])){
	            $oldFolderPath = "Uploads/Produkte/".URLSegmentFilter::create()->filter($changedFields['Title']['before']);
	            $newFolder = Folder::find_or_make($oldFolderPath);
	            $newFolder->Name = URLSegmentFilter::create()->filter($changedFields['Title']['after']);
	            $newFolder->Title = $changedFields['Title']['after'];
	            $newFolder->write();
	        }
	    }
	    else{
	    	$oldFolderPath = "Uploads/Produkte/tmp";
	    	$newFolder = Folder::find_or_make($oldFolderPath);
	    	$newFolder->Name = URLSegmentFilter::create()->filter($this->Title);
	    	$newFolder->Title = $this->Title;
	    	$newFolder->write();
	    }
	    if (!$this->Code){
	    	$this->Code = URLSegmentFilter::create()->filter($this->Title);
	    }
		parent::onBeforeWrite();
	}

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->removeByName('Code');
		$fields->dataFieldByName('Icon')->setFolderName($this->getFolderName());
		if ($this->ID > 0){
			$fields->dataFieldByName('Products')->setList($this->Products()->filter('ClassName',Product::class));
			$fields->dataFieldByName('Options')->setList($this->Options()->filter('GroupID',0));
			$fields->dataFieldByName('Products')->getConfig()->removeComponentsByType([GridFieldAddExistingAutocompleter::class,GridFieldDeleteAction::class])->addComponent(new GridFieldOrderableRows('Sort'))->addComponent(new GridFieldShowHideAction())->addComponent(new GridFieldDuplicateAction())->addComponent(new GridFieldDeleteAction());
			$fields->dataFieldByName('Options')->getConfig()->removeComponentsByType([GridFieldAddExistingAutocompleter::class,GridFieldDeleteAction::class])->addComponent(new GridFieldOrderableRows('Sort'))->addComponent(new GridFieldShowHideAction())->addComponent(new GridFieldDuplicateAction())->addComponent(new GridFieldDeleteAction());
		}
	

		return $fields;
	}

	public function fieldLabels($includerelation = true){
		$labels = parent::fieldLabels($includerelation);
		$labels['Title'] = 'Kategoriename';
		$labels['Subtitle'] = 'Untertitel';
		$labels['Description'] = 'Beschreibung';
		$labels['Products'] = 'Produkte';
		$labels['Options'] = 'Optionen';
		$labels['Icon'] = 'Bild';

		return $labels;
	}

	public function filteredProducts(){
		$products = $this->Products()->filter('ClassName',Product::class)->filterByCallback(function($item, $list) {
		    return ($item->shouldDisplay());
		});
		return $products;
	}

	public function getPreselected(){
		return $this->Products()->filter('Preselected',1)->first();
	}

	public function getBestSeller(){
		return $this->Products()->filter('BestSeller',1)->first();
	}


	public function activeIndex(){
		// // $session = Controller::curr()->getRequest()->getSession();
		// // if ($session->get('shopcart_id')){
		// // 	//fet
		// // }
		// $cart =  Controller::curr()->activeCart();
		// if ($cart){
		// 	//if package take products package
		// 	if ($cart->Package()->exists()){
		// 		$p = $cart->Package()->Products()->filter('CategoryID',$this->ID)->first();
		// 		if ($p){
		// 			return $p->Sort;
		// 		}
		// 	}
		// 	//else we look for products
		// 	if ($cart->Products()->exists() && $cart->Products()->filter('CategoryID',$this->ID)->first()){
		// 		$p = $cart->Products()->filter('CategoryID',$this->ID)->first();
		// 		return $p->Sort;
		// 	}
		// }
		return ($this->getPreselected()) ? $this->getPreselected()->Sort : 1;
	}


}