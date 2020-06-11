<?php

use SilverStripe\ORM\DataExtension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\LabelField;
use SilverStripe\CMS\Model\SiteTree;
use DNADesign\Elemental\Models\BaseElement;

class BlockLinkExtension extends DataExtension
{
    private static $types = ['block' => 'bestimmt Block von dieser Website']; 

    private static $has_one = [
        'Block' => BaseElement::class
    ];


    function updateFieldLabels(&$labels) {
        $labels['Block'] = _t(__CLASS__.'.Block', 'bestimmt Block von dieser Website');
    }


    public function updateCMSFields(FieldList $fields){
        $fields->removeByName('BlockID');
        $blocks = $this->getBlockTree();
        $fields->addFieldToTab('Root.Main',DropdownField::create('BlockID',_t(__CLASS__.'.Block','Block von dieser Seite'),$blocks)->displayIf('Type')->isEqualTo('Block')->end());
    }

    protected function getBlockTree(){
        $blockstree = array(0 => _t(__CLASS__.'.Label','Bitte Block wählen'));
        $Pages = Page::get()->sort('ParentID ASC, Sort ASC');
        foreach ($Pages as $page) {
            if ($page->ElementalAreaID > 0){
                $blocks = array();
                foreach ($page->ElementalArea()->Elements() as $block) {
                    
                    $blocks[$block->ID] = $block->singleton($block->ClassName)->getType(). " > ".$block->NiceTitle();
                    if ($block->ClassName == "ParentBlock"){
                        foreach ($block->Elements()->Elements() as $underblock) {
                        $blocks[$underblock->ID] = "  ".$block->NiceTitle(). " > ".$underblock->singleton($underblock->ClassName)->getType(). " > ".$underblock->NiceTitle();
                        }
                    }
                }
                //build the page unique sitetree strucuture
                $pageTree = $page->ID.' - '.$page->NestedTitle(4," > ");
            
                $blockstree[$pageTree] = $blocks;
            }
        }
        return $blockstree;
    }
}