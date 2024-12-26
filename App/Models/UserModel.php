

<?php 

class UserModel 
{ 
    // Thuộc tính của lớp UserModel 
    private $Id; 
    private $Username; 
    private $Password; 

    // Constructor để khởi tạo đối tượng UserModel
    public function __construct($Id, $Username, $Password) 
    { 
        $this->Id = $Id; 
        $this->Username = $Username; 
        $this->Password = $Password; 
    } 
    
    // Getter và Setter cho thuộc tính Id
    public function getId() 
    { 
        return $this->Id; 
    } 
    
    public function setId($Id) 
    { 
        $this->Id = $Id; 
    }

    // Getter và Setter cho thuộc tính Username
    public function getUsername() 
    { 
        return $this->Username; 
    } 
    
    public function setUsername($Username) 
    { 
        $this->Username = $Username; 
    }

    // Getter và Setter cho thuộc tính Password
    public function getPassword() 
    { 
        return $this->Password; 
    } 
    
    public function setPassword($Password) 
    { 
        $this->Password = $Password; 
    }
} 

?>
