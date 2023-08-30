<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\FetchGroup;

final class FetchAttribute
{
    private ?FetchGroup $group=null;

    public function __construct(private readonly FetchGroup $parent, private readonly string $attributeName)
    {

    }

    public function getParent():FetchGroup
    {
        return $this->parent;
    }

    public function getName():string
    {
        return $this->attributeName;
    }

    public function setGroup(FetchGroup $fetchGroup):void
    {
        $fetchGroup->setParent($this);
        $this->group = $fetchGroup;
    }

    public function getGroup(): ?FetchGroup
    {
        return $this->group;
    }

    public function getPath():string
    {
        $parentPath = $this->parent->getPath();
        if($parentPath){
            $parentPath =$parentPath.'.';
        }
        return $parentPath.$this->attributeName;
    }

    public function clone(FetchGroup $newParent):self{
        $cloned = new self($newParent, $this->attributeName);
        if($this->group!==null){
            $cloned->setGroup($this->getGroup()->clone());
        }
        return $cloned;
    }

    public function &getValueReference(array &$data)
    {
        $parentRef = &$this->parent->getValueReference($data);
        if(!is_array($parentRef)){
            $parentRef = [];
        }
        if(!array_key_exists($this->attributeName,$parentRef)){
            $parentRef[$this->attributeName] = null;
        }
        return $parentRef[$this->attributeName];
    }

    public function getValue(array $data):mixed
    {
        $parentData = $this->parent->getValue($data);
        if(!is_array($parentData) || !array_key_exists($this->attributeName,$parentData)){
            throw new \RuntimeException('Undefined variable:'. $this->attributeName. ' by path '.$this->getPath());
        }
        return $parentData[$this->attributeName];
    }

    public function setValue(array $data, mixed $value):void
    {
        $reference = &$this->getValueReference($data);
        $reference = $value;
    }

}