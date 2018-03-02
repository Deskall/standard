<?php

use DNADesign\ElementalUserForms\Control\ElementFormController;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\CMS\Model\SiteTree;

class ElementFormControllerExtension extends ElementFormController
{

	 private static $allowed_actions = [
        'finished'
    ];

	public function finished()
    {
    	if ($this->element->RedirectPageID > 0){
    		$redirectPage = DataObject::get_by_id(SiteTree::class,$this->element->RedirectPageID);
    		if ($redirectPage){
    			return $this->redirect($redirectPage->Link());
    		}
    	}
    	
    	parent::finished();
        
    }

}