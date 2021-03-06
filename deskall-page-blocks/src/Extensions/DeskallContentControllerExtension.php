<?php



use DNADesign\Elemental\Models\ElementalArea;
use DNADesign\Elemental\Extensions\ElementalAreasExtension;
use SilverStripe\Core\Extension;
use DNADesign\Elemental\Models\BaseElement;
class DeskallContentControllerExtension extends Extension
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'handleChildren'
    );

    public function handleChildren()
    {
        // $parentId = $this->owner->getRequest()->param('OTHERID');
        $id = $this->owner->getRequest()->param('ID');

        // if (!$parentId) {
        //     user_error('No parent ID supplied', E_USER_ERROR);
        //     return false;
        // }
        if (!$id) {
            user_error('No element ID supplied', E_USER_ERROR);
            return false;
        }

        // /** @var SiteTree $elementOwner */
        // $elementOwner = $this->owner->data();

        // $elementalAreaRelations = $this->owner->getElementalRelations();

        // if (!$elementalAreaRelations) {
        //     user_error(get_class($this->owner) . ' has no ElementalArea relationships', E_USER_ERROR);
        //     return false;
        // }
        $element = BaseElement::get()->byId($id);
        if (!$element){
            user_error('No Block found', E_USER_ERROR);
            return false;
        }
        return $element->getController();
        // If parent is children block we loop until we find last parent
        // $parent = BaseElement::get()->byId($parentId);
        // if (!$parent){
        //     user_error('No Block found', E_USER_ERROR);
        //     return false;
        // }
        // while ($parent->hasMethod('isChildren') && $parent->isChildren()){
        //     $parent = $parent->Parent()->getOwnerPage();
        // }

        // foreach ($elementalAreaRelations as $elementalAreaRelation) {
        //     $parent = $elementOwner->$elementalAreaRelation()->Elements()
        //         ->filter('ID', $parent->ID)
        //         ->First();
        //     foreach ($parent->getElementalRelations() as $elementalAreaRelation) {
        //        $element = $parent->$elementalAreaRelation()->Elements()
        //         ->filter('ID', $id)
        //         ->First();
        //         if ($element) {
        //             return $element->getController();
        //         }
        //     }
        // }

        user_error('Parent $parentId not found for this page', E_USER_ERROR);
        return false;
    }
}
