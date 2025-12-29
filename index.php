<?php
require __DIR__ . '/app/bootstrap.php';

$route = $_GET['route'] ?? 'landing';

$routes = [
    'landing' => ['App\\Controllers\\LandingController', 'index'],
    'auth/login' => ['App\\Controllers\\AuthController', 'login'],
    'auth/register' => ['App\\Controllers\\AuthController', 'register'],
    'auth/logout' => ['App\\Controllers\\AuthController', 'logout'],
    'auth/forgot' => ['App\\Controllers\\AuthController', 'forgot'],
    'auth/reset' => ['App\\Controllers\\AuthController', 'reset'],
    'dashboard' => ['App\\Controllers\\DashboardController', 'index'],
    'coffee/create' => ['App\\Controllers\\CoffeeShopController', 'create'],
    'coffee/select' => ['App\\Controllers\\CoffeeShopController', 'select'],
    'ingredients' => ['App\\Controllers\\IngredientController', 'index'],
    'ingredients/create' => ['App\\Controllers\\IngredientController', 'create'],
    'purchases' => ['App\\Controllers\\PurchaseController', 'index'],
    'purchases/create' => ['App\\Controllers\\PurchaseController', 'create'],
    'products' => ['App\\Controllers\\ProductController', 'index'],
    'products/create' => ['App\\Controllers\\ProductController', 'create'],
    'products/pricing' => ['App\\Controllers\\ProductController', 'pricing'],
    'recipes' => ['App\\Controllers\\RecipeController', 'index'],
    'recipes/create' => ['App\\Controllers\\RecipeController', 'create'],
    'sales' => ['App\\Controllers\\SaleController', 'index'],
    'sales/create' => ['App\\Controllers\\SaleController', 'create'],
    'expenses' => ['App\\Controllers\\ExpenseController', 'index'],
    'expenses/create' => ['App\\Controllers\\ExpenseController', 'create'],
    'cash' => ['App\\Controllers\\CashController', 'index'],
    'cash/create' => ['App\\Controllers\\CashController', 'create'],
    'budgets' => ['App\\Controllers\\BudgetController', 'index'],
    'budgets/create' => ['App\\Controllers\\BudgetController', 'create'],
    'budgets/edit' => ['App\\Controllers\\BudgetController', 'edit'],
    'budgets/report' => ['App\\Controllers\\BudgetController', 'report'],
    'analytics/pnl' => ['App\\Controllers\\AnalyticsController', 'pnl'],
    'analytics/network' => ['App\\Controllers\\AnalyticsController', 'network'],
    'subscription/plans' => ['App\\Controllers\\SubscriptionController', 'plans'],
    'subscription/pay' => ['App\\Controllers\\SubscriptionController', 'pay'],
    'subscription/success' => ['App\\Controllers\\SubscriptionController', 'success'],
    'subscription/fail' => ['App\\Controllers\\SubscriptionController', 'fail'],
    'subscription/manage' => ['App\\Controllers\\SubscriptionController', 'manage'],
    'admin/stats' => ['App\\Controllers\\AdminController', 'stats'],
    'admin/plans' => ['App\\Controllers\\AdminController', 'plans'],
    'admin/plans/edit' => ['App\\Controllers\\AdminController', 'editPlan'],
    'import/purchases' => ['App\\Controllers\\ImportController', 'purchases'],
    'import/sales' => ['App\\Controllers\\ImportController', 'sales'],
    'import/expenses' => ['App\\Controllers\\ImportController', 'expenses'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    view('errors/404');
    exit;
}

[$class, $method] = $routes[$route];

if (!class_exists($class)) {
    http_response_code(500);
    echo 'Контроллер не найден.';
    exit;
}

$controller = new $class();
if (!method_exists($controller, $method)) {
    http_response_code(500);
    echo 'Метод не найден.';
    exit;
}

$controller->$method();
