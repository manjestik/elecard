<?php


namespace App\Models;


class Product
{
    private $id;
    private $name;
    private $description;
    private $group_id;
    private $atr_name;
    private $atr_value;

    public function getId()
    {
        return $this->id;
    }

    private function setId($id)
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getGroupId()
    {
        return $this->group_id;
    }

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }

    public function getAtrName()
    {
        return $this->atr_name;
    }

    public function setAtrName($atr_name)
    {
        $this->atr_name = $atr_name;
    }

    public function getAtrValue()
    {
        return $this->atr_value;
    }

    public function setAtrValue($atr_value)
    {
        $this->atr_value = $atr_value;
    }

}
