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
<<<<<<< HEAD
    'coffee/users' => ['App\\Controllers\\ShopUserController', 'index'],
    'coffee/users/add' => ['App\\Controllers\\ShopUserController', 'add'],
=======
>>>>>>> origin/main
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
<<<<<<< HEAD
    'scenarios' => ['App\\Controllers\\ScenarioController', 'index'],
    'scenarios/create' => ['App\\Controllers\\ScenarioController', 'create'],
    'scenarios/report' => ['App\\Controllers\\ScenarioController', 'report'],
    'analytics/pnl' => ['App\\Controllers\\AnalyticsController', 'pnl'],
    'analytics/network' => ['App\\Controllers\\AnalyticsController', 'network'],
    'analytics/abc' => ['App\\Controllers\\AnalyticsController', 'abc'],
    'analytics/seasonality' => ['App\\Controllers\\AnalyticsController', 'seasonality'],
=======
    'analytics/pnl' => ['App\\Controllers\\AnalyticsController', 'pnl'],
    'analytics/network' => ['App\\Controllers\\AnalyticsController', 'network'],
>>>>>>> origin/main
    'subscription/plans' => ['App\\Controllers\\SubscriptionController', 'plans'],
    'subscription/pay' => ['App\\Controllers\\SubscriptionController', 'pay'],
    'subscription/success' => ['App\\Controllers\\SubscriptionController', 'success'],
    'subscription/fail' => ['App\\Controllers\\SubscriptionController', 'fail'],
    'subscription/manage' => ['App\\Controllers\\SubscriptionController', 'manage'],
    'admin/stats' => ['App\\Controllers\\AdminController', 'stats'],
<<<<<<< HEAD
    'admin/tokens' => ['App\\Controllers\\AdminController', 'tokens'],
    'admin/plans' => ['App\\Controllers\\AdminController', 'plans'],
    'admin/plans/edit' => ['App\\Controllers\\AdminController', 'editPlan'],
    'admin/backups' => ['App\\Controllers\\BackupController', 'index'],
    'admin/backups/create' => ['App\\Controllers\\BackupController', 'create'],
    'admin/backups/download' => ['App\\Controllers\\BackupController', 'download'],
    'admin/backups/restore' => ['App\\Controllers\\BackupController', 'restore'],
    'admin/audit' => ['App\\Controllers\\AdminController', 'audit'],
    'admin/system' => ['App\\Controllers\\AdminController', 'system'],
    'import/purchases' => ['App\\Controllers\\ImportController', 'purchases'],
    'import/sales' => ['App\\Controllers\\ImportController', 'sales'],
    'import/expenses' => ['App\\Controllers\\ImportController', 'expenses'],
    'export/purchases' => ['App\\Controllers\\ExportController', 'purchases'],
    'export/sales' => ['App\\Controllers\\ExportController', 'sales'],
    'export/expenses' => ['App\\Controllers\\ExportController', 'expenses'],
=======
    'admin/plans' => ['App\\Controllers\\AdminController', 'plans'],
    'admin/plans/edit' => ['App\\Controllers\\AdminController', 'editPlan'],
    'import/purchases' => ['App\\Controllers\\ImportController', 'purchases'],
    'import/sales' => ['App\\Controllers\\ImportController', 'sales'],
    'import/expenses' => ['App\\Controllers\\ImportController', 'expenses'],
>>>>>>> origin/main
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
