<?php

use SilverStripe\Forms\HTMLEditor\HtmlEditorField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\FieldType\DBField;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\ArrayList;

class MapBlock extends BaseElement
{
    private static $icon = 'font-icon-globe-1';
    
    private static $controller_template = 'BlockHolder';

    private static $controller_class = BlockController::class;

    private static $db = [
        'HTML' => 'HTMLText',
        'Adresse' => 'Varchar(255)',
        'Height' => 'Varchar(255)',
        'Styles' => 'Text',
        'disableDefaultUI' => 'Boolean(0)',
        'mapTypeControl' => 'Boolean(1)',
        'mapTypeControlOptions' => 'Text',
        'ZoomControl' => 'Boolean(1)',
        'ZoomControlOptions' => 'Text',
        'streetViewControl' => 'Boolean(1)',
        'streetViewControlOptions' => 'Text',
        'scaleControl' => 'Boolean(1)',
        'fullscreenControl' => 'Boolean(1)',

    ];

    private static $block_heights = [
        'uk-height-small' => 'klein',
        'uk-height-medium' => 'medium',
        'uk-height-large' => 'gross'
    ];


   
    private static $table_name = 'MapBlock';

    private static $singular_name = 'Google Map block';

    private static $plural_name = 'Google Map blocks';

    private static $description = 'Google Map mit Adresse hinzufügen';



    public function getCMSFields()
    {

        $this->beforeUpdateCMSFields(function ($fields) {
            
            $fields
                ->fieldByName('Root.Main.HTML')
                ->setTitle(_t(__CLASS__ . '.ContentLabel', 'Content'));
                
            $fields->addFieldToTab('Root.Main',TextField::create('Adresse',_t(__CLASS__.'.Adresse','Adresse')),'HTML');
            $fields->addFieldToTab('Root.Settings',OptionsetField::create('Height',_t(__CLASS__.'.Height','Height'), $this->getTranslatedSourceFor(__CLASS__,'block_heights')));

            $fields->addFieldToTab('Root.MapOptions', CheckboxField::create('disableDefaultUI',_t(__CLASS__.'.disableControls','Kontrol deaktivieren')));
            $fields->addFieldToTab('Root.MapOptions', CheckboxField::create('mapTypeControl',_t(__CLASS__.'.mapControls','Map Kontrol'))->displayIf('disableDefaultUI')->isNotChecked()->end());
            $fields->addFieldToTab('Root.MapOptions', TextareaField::create('mapTypeControlOptions',_t(__CLASS__.'.mapControlOptions','Map Kontrol Optionen'))->displayIf('mapTypeControl')->isChecked()->andIf('disableDefaultUI')->isNotChecked()->end());
            $fields->addFieldToTab('Root.MapOptions', CheckboxField::create('ZoomControl',_t(__CLASS__.'.zoomControls','Zoom Kontrol'))->displayIf('disableDefaultUI')->isNotChecked()->end());
            $fields->addFieldToTab('Root.MapOptions', TextareaField::create('ZoomControlOptions',_t(__CLASS__.'.zoomControlOptions','Zoom Kontrol Optionen'))->displayIf('ZoomControl')->isChecked()->andIf('disableDefaultUI')->isNotChecked()->end());
            $fields->addFieldToTab('Root.MapOptions', CheckboxField::create('streetViewControl',_t(__CLASS__.'.streetViewControls','StreetView Kontrol'))->displayIf('disableDefaultUI')->isNotChecked()->end());
            $fields->addFieldToTab('Root.MapOptions', TextareaField::create('streetViewControlOptions',_t(__CLASS__.'.streetViewControlOptions','StreetView Kontrol Optionen'))->displayIf('streetViewControl')->isChecked()->andIf('disableDefaultUI')->isNotChecked()->end());
            $fields->addFieldToTab('Root.MapOptions', CheckboxField::create('scaleControl',_t(__CLASS__.'.scaleControls','Scale Kontrol'))->displayIf('disableDefaultUI')->isNotChecked()->end());
            $fields->addFieldToTab('Root.MapOptions', CheckboxField::create('fullscreenControl',_t(__CLASS__.'.fullscreenControls','Fullscreen Kontrol'))->displayIf('disableDefaultUI')->isNotChecked()->end());
            

            $fields->addFieldToTab('Root.MapOptions',LiteralField::create('Styles generieren','<a href="https://mapstyle.withgoogle.com/" target="_blank">'._t(__CLASS__.'.LinkToStyle','Styles generieren').'</a>'));
            $fields->addFieldToTab('Root.MapOptions',TextareaField::create('Styles',_t(__CLASS__.'.Styles','Google Map Styles')));
        });
        $fields = parent::getCMSFields();
        $fields->removeByName('Layout');
        $fields->removeByName('Background');
        $fields->removeByName('BackgroundImage');
        return $fields;
    }

    public function getSummary()
    {
        return DBField::create_field('HTMLText', $this->HTML)->Summary(20);
    }

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Map');
    }

    public function NiceStyles(){
        return preg_replace( "/\r|\n/", "", $this->Styles );
    }

    public function getMapOptions(){
        $options = [];
        $options['zoom'] = 15;
        $options['disableDefaultUI'] = ($this->disableDefaultUI) ? true : false;
        if(!$this->disableDefaultUI){
            $options['mapTypeControl'] = ($this->mapTypeControl) ? true : false;
            if ($this->mapTypeControlOptions){
                $options['mapTypeControlOptions'] = json_decode(preg_replace( "/\r|\n/", "", $this->mapTypeControlOptions ));
            }
            
            $options['zoomControl'] = ($this->ZoomControl) ? true : false;
            if ($this->ZoomControlOptions){
                $options['zoomControlOptions'] = json_decode(preg_replace( "/\r|\n/", "", $this->zoomControlOptions ));
            }
            
            $options['streetViewControl'] = ($this->streetViewControl) ? true : false;
            if ($this->streetViewControlOptions){
                $options['streetViewControlOptions'] = json_decode(preg_replace( "/\r|\n/", "", $this->streetViewControlOptions ));
            }

            $options['scaleControl'] = ($this->scaleControl) ? true : false;
            $options['fullscreenControl'] = ($this->fullscreenControl) ? true : false;
        }

        if($this->Styles){
            $options['styles'] = json_decode($this->NiceStyles());
        }
        return json_encode($options);
    }

}
