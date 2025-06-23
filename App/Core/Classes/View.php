<?php
namespace App\Core;

class View {
    public static function render($view, $data = []) {
        extract($data);
        ob_start();
        include "../app/Views/$view.php";
        $content = ob_get_clean();
        include "../app/Views/layout.php";  // master template
    }
}
