<?php


namespace App\Models;


class Attributes
{
    private $id;
    private $name;
    private $id_category;
    private $type;
    private $value;
    private $value_min;
    private $value_max;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getIdCategory()
    {
        return $this->id_category;
    }
    
    public function setIdCategory($id_category)
    {
        $this->id_category = $id_category;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValueMin()
    {
        return $this->value_min;
    }

    public function setValueMin($value_min)
    {
        $this->value_min = $value_min;
    }

    public function getValueMax()
    {
        return $this->value_max;
    }

    public function setValueMax($value_max)
    {
        $this->value_max = $value_max;
    }
}
