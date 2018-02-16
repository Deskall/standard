<?php

use SilverStripe\Forms\HTMLEditor\HtmlEditorField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GroupedDropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\FieldType\DBField;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Assets\Image;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

class SliderBlock extends BaseElement
{
    private static $icon = 'font-icon-block-banner';
    
    private static $controller_template = 'DefaultHolder';

    private static $controller_class = BlockController::class;

    private static $db = [
        'Animation' => 'Varchar(255)',
        'Autoplay' => 'Boolean(1)',
        'Nav' => 'Varchar(255)',
        'Height' => 'Varchar(255)',
        'MinHeight' => 'Int',
        'MaxHeight' => 'Int'
    ];

    private static $has_one = [
        'Referent' => SliderBlock::class
    ];

    private static $has_many = [
        'Slides' => Slide::class
    ];

    private static $owns = [
        'Slides',
    ];

    private static $cascade_delete = [
        'Slides',
    ];


    private static $defaults = [
        'Layout' => 'slideshow',
        'FullWidth' => 1
    ];

    private static $block_layouts = [
        'slideshow' => 'Slideshow'
    ];


    private static $block_heights = [
        'uk-height-small' => 'klein',
        'uk-height-medium' => 'medium',
        'uk-height-large' => 'gross',
        'viewport' => 'ganz Bildschirm'
    ];

    private static $animations = [
        'slide' => 'slide',
        'fade' => 'fade',
        'scale' => 'scale',
        'pull' => 'pull',
        'push' => 'push'
    ];
   
    private static $controls = [
        'none' => 'kein',
        'dots' => 'dots',
        'controls' => 'arrows'
    ];

    private static $table_name = 'SliderBlock';

    private static $singular_name = 'Slideshow';

    private static $plural_name = 'Slideshow';

    private static $description = 'Bilder als Slide angezeigen.';

    public function getCMSFields()
    {

        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->removeByName('TitleAndDisplayed');
            $fields->removeByName('RelatedPageID');
            $fields->removeByName('Slides');

            $referent = new GroupedDropdownField("ReferentID", "Slides kopieren nach", $source = $this->getReferentSource());
            $fields->addFieldToTab('Root.Main',$referent);


            if ($this->ID > 0){
                $config = GridFieldConfig_RecordEditor::create();
                $config->addComponent(new GridFieldOrderableRows('Sort'));
                if (singleton('Slide')->hasExtension('Activable')){
                     $config->addComponent(new GridFieldShowHideAction());
                }
                $slidesField = new GridField('Slides','Slides',$this->Slides(),$config);
                $fields->addFieldToTab('Root.Main',$slidesField);
            }

            $fields->addFieldToTab('Root.Settings',CheckboxField::create('Autoplay','Autoplay'));
            $fields->addFieldToTab('Root.Settings',DropdownField::create('Animation','Animation', self::$animations));
            $fields->addFieldToTab('Root.Settings',DropdownField::create('Nav','Kontrols', self::$controls));
            $fields->addFieldToTab('Root.Settings',LayoutField::create('Layout','Format', self::$block_layouts));
            $fields->addFieldToTab('Root.Settings',LayoutField::create('Height','Höhe',self::$block_heights));
            $fields->addFieldToTab('Root.Settings',NumericField::create('MinHeight','min. Höhe'));
            $fields->addFieldToTab('Root.Settings',NumericField::create('MaxHeight','max. Höhe'));
        });
        $fields = parent::getCMSFields();
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
        return _t(__CLASS__ . '.BlockType', 'Slideshow');
    }

    public function activeSlides(){
        if (singleton('Slide')->hasExtension('Activable')){
            return $this->Slides()->filter('isVisible',1);
        }
        return $this->Slides();
    }

    public function getReferentSource(){
        $source = [];
        foreach (Page::get() as $page) {
            if ($page->ElementalArea()->Elements()->filter('ClassName','SliderBlock')->exclude('ID',$this->ID)->count() > 0){
                $slides = $$page->ElementalArea()->Elements()->filter('ClassName','SliderBlock')->exclude('ID',$this->ID)->map('ID','Title');
                $source[$page->MenuTitle] = $slides;
            }
        } 
        return $source;
    }

}
