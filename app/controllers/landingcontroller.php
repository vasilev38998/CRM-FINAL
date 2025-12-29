<?php
namespace App\Controllers;

class LandingController
{
    public function index(): void
    {
        $plans = db()->query('SELECT * FROM plans ORDER BY price ASC')->fetchAll();
        view('landing', ['plans' => $plans]);
    }
}
