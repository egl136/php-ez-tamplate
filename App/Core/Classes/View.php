<?php
namespace App\Core\Classes;

class View {
    
    public static function render($view, $data = []) {
        extract($data);
        ob_start();
        include __DIR__."/../../Views/$view";
        $content = ob_get_clean();
        include "../App/Views/layout.php";  
    }
}

?>