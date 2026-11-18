<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modelo de productos.
 *
 * Por ahora devuelve datos fijos de ejemplo. Cuando se conecte MySQL, este
 * método pasará a consultar la tabla `products` con PDO y consultas
 * preparadas, sin cambiar controladores ni vistas.
 */
final class Product
{
    /** @return list<array{name: string, summary: string, price: float}> */
    public static function all(): array
    {
        return [
            ['name' => 'Producto uno',    'summary' => 'Descripción breve del primer producto.',  'price' => 19.90],
            ['name' => 'Producto dos',    'summary' => 'Descripción breve del segundo producto.', 'price' => 29.90],
            ['name' => 'Producto tres',   'summary' => 'Descripción breve del tercer producto.',  'price' => 39.90],
            ['name' => 'Producto cuatro', 'summary' => 'Descripción breve del cuarto producto.',  'price' => 49.90],
        ];
    }
}
