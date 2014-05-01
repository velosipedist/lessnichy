<?php
namespace Lessnichy;

/**
 * Listens to lessnichy AJAX queries and also casts js & css resources
 * @package Lessnichy
 */
class Server
{
    public function __construct()
    {
        require_once __DIR__ . '/app.php';
    }
}
 