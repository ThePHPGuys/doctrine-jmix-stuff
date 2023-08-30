<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined;

final class ViewAttribute
{
    private ?View $view=null;

    public function __construct(private readonly View $parent, private readonly string $attributeName)
    {

    }

    public function getName():string
    {
        return $this->attributeName;
    }

    public function setView(View $fetchGroup):void
    {
        $fetchGroup->setParent($this);
        $this->view = $fetchGroup;
    }

    public function getView(): ?View
    {
        return $this->view;
    }

    public function getPath():string
    {
        $parentPath = $this->parent->getPath();
        if($parentPath){
            $parentPath =$parentPath.'.';
        }
        return $parentPath.$this->attributeName;
    }

    public function clone(View $newParent){
        $cloned = new self($newParent, $this->attributeName);
        if($this->view!==null){
            $cloned->setView($this->getView()->clone());
        }
        return $cloned;
    }

}