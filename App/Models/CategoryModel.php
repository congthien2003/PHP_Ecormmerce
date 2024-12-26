<?php

class CategoryModel {
    private $id;
    private $name;

    public function __construct($id = null, $name = null) {
        $this->id = $id;
        $this->name = $name;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    // Convert to array for JSON response
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    // Create from database row
    public static function fromDB($row) {
        return new self(
            $row->id ?? null,
            $row->name ?? null
        );
    }
} 