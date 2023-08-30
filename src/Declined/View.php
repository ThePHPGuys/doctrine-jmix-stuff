<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined;

use Generator;

final class View
{
    /**
     * @var ViewAttribute[]
     */
    private array $attributes=[];
    private ?ViewAttribute $parent = null;

    public function __construct()
    {
    }

    public function setParent(ViewAttribute $attribute)
    {
        $this->parent = $attribute;
    }

    public function addAttribute(string $string, View $group = null):ViewAttribute
    {
        //$attribute = new FetchAttribute($this, $string);
        $attribute = $this->getByPath($string,true);
        if($group !==null){
            $attribute->setView($group);
        }
        return $attribute;
    }

    /**
     * @return ViewAttribute[]
     */
    public function getAttributes():array
    {
        return array_values($this->attributes);
    }

    public function getPath():string
    {
        return  $this->parent!==null?$this->parent->getPath():'';
    }

    public function getAttribute(string $name):ViewAttribute
    {
        $attribute = $this->getByPath($name);
        if($attribute===null){
            throw new \InvalidArgumentException('Unknown attribute:'. $name);
        }
        return $attribute;
    }

    public function hasAttribute(string $name):bool
    {
        return array_key_exists($name,$this->attributes);
    }

    private function getByPath(string $path, bool $create = false):?ViewAttribute
    {
        $parts = explode('.',$path);
        $partsCount = count($parts);
        $currentAttribute = null;
        $currentGroup = $this;
        $isLastIteration = fn(int $i)=>$i===($partsCount-1);

        foreach ($parts as $i=>$part){
            if(!isset($currentGroup->attributes[$part])){
                if($create) {
                    $currentAttribute = new ViewAttribute($currentGroup, $part);
                    $currentGroup->attributes[$part] = $currentAttribute;
                }else{
                    return null;
                }
            }else{
                $currentAttribute = $currentGroup->attributes[$part];
            }

            if($isLastIteration($i)){
                continue;
            }
            if($currentAttribute->getView()===null){
                if($create){
                    $currentGroup = new View();
                    $currentAttribute->setView($currentGroup);
                }else{
                    return null;
                }
            }else{
                $currentGroup = $currentAttribute->getView();
            }

        }
        return $currentAttribute;
    }

    /**
     * @psalm-return Generator<ViewAttribute>
     */
    public function getAllAttributes(): Generator
    {
        foreach ($this->attributes as $attribute){
            yield $attribute;
            $attributeGroup = $attribute->getView();
            if($attributeGroup!==null){
                yield from $attributeGroup->getAllAttributes();
            }
        }
    }

    public function clone():self
    {
        $cloned = new self();
        foreach ($this->attributes as $attribute){
            $cloned->addAttribute($attribute->getName(),$attribute->getView()?->clone());
        }
        return $cloned;
    }
}