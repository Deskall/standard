<?php

use SilverStripe\ORM\DataObject;
use DNADesign\Elemental\Models\BaseElement;
use Sheadawson\Linkable\Models\Link;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\SiteConfig\SiteConfig;
class NavigationItem extends DataObject{
	private static $db = [
		'BackgroundColor' => 'Varchar',
		'Title' => 'Varchar',
		'ItemType' => 'Varchar',
	];

	private static $has_one = [
		'Target' => BaseElement::class,
		'Action' => BaseElement::class,
		'Parent' => NavigationBlock::class
	];

	private static $extensions = ['Sortable','Activable','Linkable'];

	public function TargetLink(){
		switch ($this->ItemType) {
			case 'block':
				return $this->Target()->Anchor;
			break;
			case 'target':
				switch ($this->Action()->InteractionType) {
					case 'modal':
						return "#content-container-".$this->ActionID;
						break;
					case 'offcanvas':
						return "#offcanvas-container-".$this->ActionID;
						break;
					case 'toggle':
						return "#toggle-container-".$this->ActionID;
						break;
					default:
						return "#".$this->ActionID;
					break;
				}
			break;
			default:
				return null;
				break;
		}
	}

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->removeByName('ParentID');
		$fields->removeByName('ActionID');
		$fields->removeByName('TargetID');
		$fields->addFieldToTab('Root.Main',TextField::create('Title',_t(__CLASS__."Title",'Titel'))->setAttribute('Placeholder','wird den Block- /Seitetitel benutzen falls leer.'));
		$fields->addFieldToTab('Root.Main',DropdownField::create('ItemType','Menu Typ',['block' => 'Scroll zu Element','target' => 'Aktion', 'link' => 'Link']));
		$fields->addFieldToTab('Root.Main',Wrapper::create(DropdownField::create('ActionID','Aktion',$this->Parent()->Parent()->Elements()->exclude(['ID' => $this->ParentID, 'ClassName' => 'HiddenActionBlock'])->map('ID','Title')))->displayIf('ItemType')->isEqualTo('target')->end());
		$fields->addFieldToTab('Root.Main',Wrapper::create(DropdownField::create('TargetID','Seite Block',$this->Parent()->Parent()->Elements()->filter('ClassName','HiddenActionBlock')->exclude('ID',$this->ParentID)->map('ID','Title')))->displayIf('ItemType')->isEqualTo('block')->end());
		$fields->addFieldToTab('Root.Main',HTMLDropdownField::create('BackgroundColor',_t(__CLASS__.'.BackgroundColor','Hintergrundfarbe'),SiteConfig::current_site_config()->getBackgroundColors())->addExtraClass('colors'));
		return $fields;
	}
}