<?php

use SilverStripe\ORM\DataExtension;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\File;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\View\ThemeResourceLoader;
use SilverStripe\View\SSViewer;
use SilverStripe\Control\Director;


class DeskallPageExtension extends DataExtension
{
	 private static $db = [
        'ShowInMainMenu' => 'Boolean(1)'
    ];

    private static $has_one = [];

    private static $menu_level = [
        '1' => 'Hauptnavigation',
        '0' => 'Untennavigation'
    ];

    public function ThemeDir(){
    	return ThemeResourceLoader::inst()->getThemePaths(SSViewer::get_themes())[0];
    }

    public static function page_type_classes(){
        $types = self::page_type_classes();
        print_r($types);
        unset($types['SilverStripe\UserForms\Model\UserDefinedForm']);
        return $types;
    }

    public function updateCMSFields(FieldList $fields){
        if ($this->owner->ShowInMenus){
            $field = OptionsetField::create('ShowInMainMenu',_t(__CLASS__.'.ShowInMainMenuLabel','In welchem Menu sollt diese Seite anzeigen ?'), $this->owner->getTranslatedSourceFor(__CLASS__,'menu_level'));
            $fields->insertAfter($field,'MenuTitle');

        }
    }


    public function generateFolderName(){
    	if ($this->owner->ID > 0){
    		if ($this->owner->ParentID > 0){
	    		return $this->owner->Parent()->generateFolderName()."/".$this->owner->URLSegment;
	    	}
	    	else{
	    		return "Uploads/".$this->owner->URLSegment;
	    	}
    	}
    	else{
    		return "Uploads/tmp";
    	}
    	
    }

    public function onBeforeWrite(){
    	if ($this->owner->ID > 0){
            $changedFields = $this->owner->getChangedFields();
            //Update Folder Name
            if ($this->owner->isChanged('URLSegment') && ($changedFields['URLSegment']['before'] != $changedFields['URLSegment']['after'])){
                $oldFolderPath = ($this->owner->ParentID > 0 ) ? $this->owner->Parent()->generateFolderName()."/".$changedFields['URLSegment']['before'] : "Uploads/".$changedFields['URLSegment']['before'];
                $newFolder = Folder::find_or_make($oldFolderPath);
                $newFolder->Name = $changedFields['URLSegment']['after'];
                $newFolder->Title = $changedFields['URLSegment']['after'];
                $newFolder->write();
            }
            //Update Folder Structure
            if($this->owner->isChanged('ParentID')){
                $oldParent = ($changedFields['ParentID']['before'] == 0) ? null : DataObject::get_by_id(SiteTree::class,$changedFields['ParentID']['before']);
                $oldFolderPath = ($oldParent) ? $oldParent->generateFolderName()."/".$this->owner->URLSegment : "Uploads/".$this->owner->URLSegment;
                $oldFolder = Folder::find_or_make($oldFolderPath);

                $newParent = ($changedFields['ParentID']['after'] == 0) ? null : DataObject::get_by_id(SiteTree::class,$changedFields['ParentID']['after']);
                $newParentFolderPath = ($newParent) ? $newParent->generateFolderName() : "Uploads";
                $newParentFolder = Folder::find_or_make($newParentFolderPath);
                
                $oldFolder->ParentID = $newParentFolder->ID;
                $oldFolder->write();
            }
        }
    	
    	parent::onBeforeWrite();
    }

      /************* TRANLSATIONS *******************/
    public function provideI18nEntities(){
        $entities = [];
        foreach($this->stat('menu_level') as $key => $value) {
          $entities[__CLASS__.".menu_level_{$key}"] = $value;
        }

        return $entities;
    }

    /************* END TRANLSATIONS *******************/
}