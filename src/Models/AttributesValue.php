<?php


namespace App\Models;


class AttributesValue
{
    private $id;
    private $value;
    private $id_category;
    private $id_attribute;

    public function getId()
    {
        return $this->id;
    }
    
    private function setId($id)
    {
        $this->id = $id;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getIdCategory()
    {
        return $this->id_category;
    }
    
    public function setIdCategory($id_category)
    {
        $this->id_category = $id_category;
    }
    
    public function getIdAttribute()
    {
        return $this->id_attribute;
    }
    
    public function setIdAttribute($id_attribute)
    {
        $this->id_attribute = $id_attribute;
    }
}
