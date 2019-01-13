<?php


use SilverStripe\Core\Extension;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\FieldList;


/**
 * Asset Form Factory extension.
 * Extends the CMS detail form to allow Description.
 *
 * @extends Extension
 */
class DeskallImageAssetFormFactoryExtension extends Extension
{

    /**
     * Add FocusPoint field for selecting focus.
     */
    public function updateFormFields(FieldList $fields, $controller, $formName, $context)
    {
        $image = isset($context['Record']) ? $context['Record'] : null;
        if ($image && $image->appCategory() === 'image') {
            $fields->insertAfter(
                'Title',
                TextareaField::create('Description',_t('Image.Description','Beschreibung'))
                    ->setDescription(_t('Image.DescriptionLabel','wird im Front End als alt Tag angezeigt.'))
            );
        }
        
        if ($image->getExtension() == "svg"){
            $fields->removeByName('FocusPoint');
        }
        ob_start();
        print_r($fields);
        $result = ob_get_clean();
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/log.txt", $result);
    }
}
