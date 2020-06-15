<?php
namespace AmphiBee\Entities;

class Loader
{
    public function __construct()
    {
        require __DIR__ . '/../../vendor/models/models.php';
        $this->setPath();
    }

    public function setPath() {
        add_filter('sober/models/path', function($path) {
            return ABSPATH . '../../config/entities';
        });
    }
}
