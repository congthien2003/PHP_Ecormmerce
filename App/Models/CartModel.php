<?php 

class CartModel 
{ 
    // Thuộc tính của lớp CartModel 
    private $IdUser; 
    private $IdProduct; 
    private $Quantity; 
    private $UpdatedAt;

    // Constructor để khởi tạo đối tượng CartModel
    public function __construct($IdUser, $IdProduct, $Quantity, $UpdatedAt) 
    { 
        $this->IdUser = $IdUser; 
        $this->IdProduct = $IdProduct; 
        $this->Quantity = $Quantity; 
        $this->UpdatedAt = $UpdatedAt;
    } 
    
    // Getter và Setter cho thuộc tính IdUser
    public function getIdUser() 
    { 
        return $this->IdUser; 
    } 
    
    public function setIdUser($IdUser) 
    { 
        $this->IdUser = $IdUser; 
    }

    // Getter và Setter cho thuộc tính IdProduct
    public function getIdProduct() 
    { 
        return $this->IdProduct; 
    } 
    
    public function setIdProduct($IdProduct) 
    { 
        $this->IdProduct = $IdProduct; 
    }

    // Getter và Setter cho thuộc tính Quantity
    public function getQuantity() 
    { 
        return $this->Quantity; 
    } 
    
    public function setQuantity($Quantity) 
    { 
        $this->Quantity = $Quantity; 
    }

    // Getter và Setter cho thuộc tính UpdatedAt
    public function getUpdatedAt() 
    { 
        return $this->UpdatedAt; 
    } 
    
    public function setUpdatedAt($UpdatedAt) 
    { 
        $this->UpdatedAt = $UpdatedAt; 
    }
} 

?>
