<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class OrderController extends Controller
{
    public function checkout(): void
    {
        $this->view('orders/checkout', [
            'pageCss'    => 'checkout', //load css
        ]);
    }
}
