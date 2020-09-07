<?php

use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\SiteConfig\SiteConfig;

class Overlay extends DataObject{

	private static $singular_name = 'Overlay';
	private static $plural_name = 'Overlay';

	private static $db = [
		'Type' => 'Varchar(255)',
		'Title' => 'Varchar(255)',
		'Subtitle' => 'Varchar(255)',
		'Content' => 'HTMLText',
		'CountDown' => 'Boolean(0)',
		'CountDownDate' => 'Datetime',
		'CloseButtonText' => 'Varchar(255)',
		'ValidButtonText' => 'Varchar(255)',
		'TriggerType' => 'Varchar(255)',
		'TriggerFrequency' => 'Varchar(255)',
		'TriggerTime' => 'Int',
		'BackgroundColor' => 'Varchar(255)'
	];

	private static $has_many = [
		'Pages' => Page::class
	];

	private static $has_one = [
		'BackgroundImage' => Image::class
	];

	private static $extensions = [
		Versioned::class
	];

    public function fieldLabels($includerelations = true) {
	    $labels = parent::fieldLabels($includerelations);
	    $labels['Title'] = 'Titel';
	    $labels['Type'] = 'Art';
	    $labels['Subtitle'] = 'Untertitel';
	    $labels['Content'] = 'Inhalt';
	    $labels['CountDown'] = 'mit Rückwärts Zähler?';
	 	$labels['CountDownDate'] = 'Rückwärts bis';
	 	$labels['CloseButtonText'] = 'Titel des Buttons zum Schließen';
	 	$labels['CountDownDate'] = 'Titel des Buttons zum Senden';
	 	$labels['TriggerType'] = 'Auslösungsart';
	    $labels['TriggerFrequency'] = 'Auslösung';
	 	$labels['TriggerTime'] = 'Zeit zum Auslösen (Sekunden)';
	 	$labels['BackgroundColor'] = 'Hintergrundfarbe';
	 	$labels['BackgroundImage'] = 'Hintergrundbild';
	    return $labels;
	}

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->removeByName('Type');
		$fields->removeByName('BackgroundColor');
		$fields->removeByName('BackgroundImage');

		$fields->insertBefore('Title',DropdownField::create('Type', $this->fieldLabels()['Type'],['Newsletter' => 'Newsletter Anmeldung', 'Form' => 'Formular (Umfrage, Rezension)', 'Bewertung' => 'Bewertung', 'Text' => 'Inhalt (mit CountDown Möglichkeit)']));
		$fields->addFieldsToTab('Root.Layout', [
			HTMLDropdownField::create('BackgroundColor',_t(__CLASS__.'.BackgroundColor','Hintergrundfarbe'),SiteConfig::current_site_config()->getBackgroundColors())->setDescription(_t(__CLASS__.'.BackgroundColorHelpText','wird als overlay anzeigen falls es ein Hintergrundbild gibt.'))->addExtraClass('colors'),
			UploadField::create('BackgroundImage',_t(__CLASS__.'.BackgroundImage','Hintergrundbild'))->setFolderName('Uploads/Overlays')
		]);

		return $fields;
	}
}