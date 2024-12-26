<?php 
 
class ProductModel 
{ 
    // Thuộc tính của lớp ProductModel 
    private $ID; 
    private $Name; 
    private $Description; 
    private $Price; 
    private $ImageUrl;
    private $CategoryId;

    // Constructor để khởi tạo đối tượng ProductModel 
    public function __construct($ID, $Name, $Description, $Price, $ImageUrl, $CategoryId) 
    { 
        $this->ID = $ID; 
        $this->Name = $Name; 
        $this->Description = $Description; 
        $this->Price = $Price; 
        $this->ImageUrl = $ImageUrl; 
        $this->CategoryId = $CategoryId;
    } 
 
    // Getter và Setter cho thuộc tính ID 
    public function getID() 
    { 
        return $this->ID; 
    } 
 
    public function setID($ID) 
    { 
        $this->ID = $ID; 
    }
    public function getCategoryId()
    {
        return $this->CategoryId;
    }
    public function setCategoryId($CategoryId)
    {
        $this->CategoryId = $CategoryId;
    }
     // Getter và Setter cho thuộc tính Name 
    public function getName() 
    { 
        return $this->Name; 
    } 
 
    public function setName($Name) 
    { 
        $this->Name = $Name; 
    } 
 
    // Getter và Setter cho thuộc tính Description 
    public function getDescription() 
    { 
        return $this->Description; 
    } 
 
    public function setDescription($Description) 
    { 
        $this->Description = $Description; 
    } 
 
    // Getter và Setter cho thuộc tính Price 
    public function getPrice() 
    { 
        return $this->Price; 
    } 
 
    public function setPrice($Price) 
    { 
        $this->Price = $Price; 
    } 
    // Getter và Setter cho thuộc tính ImageUrl
    public function getImageUrl()
    {
        return $this->ImageUrl;
    }
    
    public function setImageUrl($ImageUrl)
    {
        $this->ImageUrl = $ImageUrl;
    }
} 
 
?>