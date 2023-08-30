<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\FetchGroup;

use Generator;

final class FetchGroup
{
    /**
     * @var FetchAttribute[]
     */
    private array $attributes=[];
    private ?FetchAttribute $parent = null;

    public function __construct()
    {
    }

    public function setParent(FetchAttribute $attribute)
    {
        $this->parent = $attribute;
    }

    public function addAttribute(string $nameOrPath, ?FetchGroup $group = null):FetchAttribute
    {
        //$attribute = new FetchAttribute($this, $string);
        $attribute = $this->getByPath($nameOrPath,true);
        if($group !==null){
            $attribute->setGroup($group);
        }
        return $attribute;
    }

    /**
     * @return list<FetchAttribute>
     */
    public function getAttributes():array
    {
        return array_values($this->attributes);
    }

    /**
     * @return list<string>
     */
    public function getAttributeNames():array
    {
        return array_keys($this->attributes);
    }

    public function getPath():string
    {
        return  $this->parent!==null?$this->parent->getPath():'';
    }

    public function getAttribute(string $name):FetchAttribute
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

    private function getByPath(string $path, bool $create = false):?FetchAttribute
    {
        $parts = explode('.',$path);
        $partsCount = count($parts);
        $currentAttribute = null;
        $currentGroup = $this;
        $isLastIteration = fn(int $i)=>$i===($partsCount-1);

        foreach ($parts as $i=>$part){
            if(!isset($currentGroup->attributes[$part])){
                if($create) {
                    $currentAttribute = new FetchAttribute($currentGroup, $part);
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
            if($currentAttribute->getGroup()===null){
                if($create){
                    $currentGroup = new FetchGroup();
                    $currentAttribute->setGroup($currentGroup);
                }else{
                    return null;
                }
            }else{
                $currentGroup = $currentAttribute->getGroup();
            }

        }
        return $currentAttribute;
    }

    /**
     * @psalm-return Generator<FetchAttribute>
     */
    public function getAllAttributes(): Generator
    {
        foreach ($this->attributes as $attribute){
            yield $attribute;
            $attributeGroup = $attribute->getGroup();
            if($attributeGroup!==null){
                yield from $attributeGroup->getAllAttributes();
            }
        }
    }

    public function clone():self
    {
        $cloned = new self();
        foreach ($this->attributes as $attribute){
            $cloned->addAttribute($attribute->getName(),$attribute->getGroup()?->clone());
        }
        return $cloned;
    }

    public function &getValueReference(array &$data)
    {
        if($this->parent){
            return $this->parent->getValueReference($data);
        }
        return $data;
    }

    public function getValue(array $data)
    {
        if($this->parent){
            return $this->parent->getValue($data);
        }
        return $data;
    }

}