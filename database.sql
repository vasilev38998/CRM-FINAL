CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL
);

CREATE TABLE coffee_shops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(120),
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT NOT NULL,
    description TEXT
);

CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    status VARCHAR(30) NOT NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES plans(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    provider VARCHAR(50) NOT NULL,
    order_id VARCHAR(120) NOT NULL,
    payment_id VARCHAR(120),
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES plans(id)
);

CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coffee_shop_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    stock_qty DECIMAL(12,4) NOT NULL DEFAULT 0,
    avg_price DECIMAL(12,4) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (coffee_shop_id) REFERENCES coffee_shops(id)
);

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coffee_shop_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    qty DECIMAL(12,4) NOT NULL,
    price DECIMAL(12,4) NOT NULL,
    total DECIMAL(12,4) NOT NULL,
    purchased_at DATE NOT NULL,
    FOREIGN KEY (coffee_shop_id) REFERENCES coffee_shops(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coffee_shop_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    price_sell DECIMAL(12,4) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (coffee_shop_id) REFERENCES coffee_shops(id)
);

CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coffee_shop_id INT NOT NULL,
    product_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    qty DECIMAL(12,4) NOT NULL,
    FOREIGN KEY (coffee_shop_id) REFERENCES coffee_shops(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coffee_shop_id INT NOT NULL,
    product_id INT NOT NULL,
    qty DECIMAL(12,4) NOT NULL,
    price DECIMAL(12,4) NOT NULL,
    total DECIMAL(12,4) NOT NULL,
    cogs DECIMAL(12,4) NOT NULL,
    sold_at DATE NOT NULL,
    FOREIGN KEY (coffee_shop_id) REFERENCES coffee_shops(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coffee_shop_id INT NOT NULL,
    category VARCHAR(120) NOT NULL,
    amount DECIMAL(12,4) NOT NULL,
    note TEXT,
    spent_at DATE NOT NULL,
    FOREIGN KEY (coffee_shop_id) REFERENCES coffee_shops(id)
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO plans (name, price, duration_days, description) VALUES
('Trial 7 дней', 299.00, 7, '7 дней доступа для теста сервиса.'),
('Pro 30 дней', 1299.00, 30, 'Полный доступ на 30 дней.'),
('Maxi 30 дней', 2499.00, 30, 'Расширенный доступ и приоритетная поддержка.');
