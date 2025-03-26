CREATE DATABASE IF NOT EXISTS Ecommerce;

USE Ecommerce; 

CREATE TABLE Users  (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone_number VARCHAR(15),
    address VARCHAR(255)
);

CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    image_url VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES Categories(category_id)
);

CREATE TABLE Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE OrderDetails (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

CREATE TABLE Payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(50) DEFAULT 'Pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id)
);


INSERT INTO Categories (category_name) VALUES
('Camping Gear'),
('Hiking Equipment'),
('Travel Accessories');


INSERT INTO Products (category_id, product_name, description, price, stock, image_url)
VALUES
(1, 'Tent', 'Spacious and durable tent for family camping. Weather-resistant and easy to set up.', 150.00, 20, '../Images/camp1.jpg'),
(1, 'Sleeping Bag', 'Warm and comfortable sleeping bag for outdoor adventures. Lightweight and packable design.', 75.00, 30, '../Images/Sleeping_bag.webp'),
(1, 'Camping Chair', 'Foldable and comfortable chair for relaxing by the fire. Durable materials and lightweight design.', 30.00, 50, '../Images/camping_chair.webp'),
(1, 'Camping Stove', 'Portable gas stove for cooking meals at the campsite. Adjustable flame and easy to clean.', 50.00, 25, '../Images/camping_stove.webp'),
(1, 'Air Mattress', 'Comfortable air mattress for camping and guests. Durable material and easy inflation.', 45.00, 40, '../Images/air_matress.webp'),
(1, 'Hammock', 'Relax and enjoy the outdoors in this comfortable hammock. Easy to set up and take down.', 40.00, 15, '../Images/hammock.webp'),

(2, 'Hiking Backpack', 'Spacious and durable backpack for all your gear. Perfect for day hikes or multi-day adventures.', 80.00, 25, '../Images/bagpack.jpg'),
(2, 'Trekking Poles', 'Lightweight yet strong poles for added stability. Reduce strain on your joints during long hikes.', 60.00, 30, '../Images/trekking_poles.webp'),
(2, 'Water Bottle', 'Stay hydrated on the trails. Durable and leakproof. Available in various sizes and colors.', 20.00, 60, '../Images/water_bottle.webp'),
(2, 'Headlamp', 'Hands-free lighting for early mornings and late nights. Adjustable brightness settings for any situation.', 25.00, 35, '../Images/headlamp.webp'),
(2, 'First Aid Kit', 'Be prepared for emergencies with this compact kit. Essential supplies for minor injuries on the go.', 30.00, 50, '../Images/first_aid_kit.webp'),
(2, 'Compass & Map', 'Navigate with confidence, even off the beaten path. Reliable tools for wilderness exploration.', 15.00, 45, '../Images/compass.webp'),

(3, 'Travel Pillow', 'Soft and comfy travel pillow. Provides neck support for flights.', 15.00, 70, '../Images/travel_pillow.webp'),
(3, 'Water Bottle', 'Durable and leakproof water bottle. Keeps your drinks cold for hours.', 20.00, 60, '../Images/water_bottle.webp'),
(3, 'Luggage Organizer', 'Set of packing cubes for organized travel. Maximize space and minimize wrinkles.', 25.00, 40, '../Images/luggage_organizer.webp'),
(3, 'Portable Charger', 'High-capacity power bank for your devices. Never run out of battery on the go.', 30.00, 35, '../Images/portable_charger.webp'),
(3, 'Eye Mask', 'Soft and comfortable eye mask for sleeping. Blocks out light for restful sleep anywhere.', 10.00, 80, '../Images/eye_mask.webp'),
(3, 'First Aid Kit', 'Compact kit with essential first-aid supplies. Be prepared for minor injuries while traveling.', 12.00, 50, '../Images/first_aid_kit.webp');


ALTER TABLE Products
ADD COLUMN long_description TEXT,
ADD COLUMN brand VARCHAR(100),
ADD COLUMN product_dimension VARCHAR(50),
ADD COLUMN rent DECIMAL(10, 2);

-- Assuming these are the product IDs (1-18) based on the order of your previous insert
-- Please double-check if the order matches your database 

-- Camping Gear (1-6)
UPDATE Products SET long_description = 'This spacious family tent is perfect for your next camping trip! It features a durable, weather-resistant design and is easy to set up.', brand = 'Coleman', product_dimension = '10ft x 8ft x 6ft', rent = 25.00 WHERE product_id = 1;
UPDATE Products SET long_description = 'Stay warm and cozy on chilly nights with this lightweight and packable sleeping bag.', brand = 'Kelty', product_dimension = '7ft x 3ft', rent = 10.00 WHERE product_id = 2;
UPDATE Products SET long_description = 'Relax in comfort around the campfire with this durable and portable camping chair.', brand = 'REI Co-op', product_dimension = '2ft x 2ft x 3ft', rent = 5.00 WHERE product_id = 3;
UPDATE Products SET long_description = 'Cook delicious meals at your campsite with this easy-to-use and clean camping stove.', brand = 'MSR', product_dimension = '1ft x 1ft x 0.5ft', rent = 8.00 WHERE product_id = 4;
UPDATE Products SET long_description = 'Enjoy a comfortable night\'s sleep under the stars with this durable and easy-to-inflate air mattress. ', brand = 'SoundAsleep', product_dimension = '6ft x 4ft x 1ft', rent = 7.00 WHERE product_id = 5;
UPDATE Products SET long_description = 'Find ultimate relaxation in the great outdoors with this easy-to-set-up hammock.', brand = 'ENO', product_dimension = '9ft x 5ft', rent = 6.00 WHERE product_id = 6;

-- Hiking Equipment (7-12)
UPDATE Products SET long_description = 'This high-capacity backpack is perfect for day hikes or longer adventures, with plenty of space for all your gear.', brand = 'Osprey', product_dimension = '2ft x 1.5ft x 1ft', rent = 12.00 WHERE product_id = 7;
UPDATE Products SET long_description = 'These lightweight trekking poles provide extra stability and reduce strain on your joints during challenging hikes.', brand = 'Black Diamond', product_dimension = '4ft (adjustable)', rent = 5.00 WHERE product_id = 8;
UPDATE Products SET long_description = 'Stay hydrated on the go with this durable, leakproof water bottle. Available in a variety of colors!', brand = 'Hydro Flask', product_dimension = '10 inches', rent = 3.00 WHERE product_id = 9;
UPDATE Products SET long_description = 'This bright and adjustable headlamp is essential for early morning or late-night adventures.', brand = 'Petzl', product_dimension = '3 inches', rent = 4.00 WHERE product_id = 10;
UPDATE Products SET long_description = 'Be prepared for anything with this compact first-aid kit, equipped with all the essentials for minor injuries.', brand = 'Adventure Medical Kits', product_dimension = '6in x 4in x 2in', rent = 5.00 WHERE product_id = 11;
UPDATE Products SET long_description = 'Find your way with confidence, even off the beaten path, using this reliable compass and detailed map.', brand = 'Garmin', product_dimension = 'Compass: 3 inches, Map: Varies', rent = 4.00 WHERE product_id = 12;

-- Travel Accessories (13-18)
UPDATE Products SET long_description = 'Enjoy a more comfortable journey with this super-soft and supportive travel pillow.', brand = 'Cabeau', product_dimension = '11 inches', rent = 2.00 WHERE product_id = 13;
UPDATE Products SET long_description = 'This insulated water bottle keeps your drinks cold for hours, so you can stay hydrated all day long.', brand = 'YETI', product_dimension = '12 inches', rent = 4.00 WHERE product_id = 14;
UPDATE Products SET long_description = 'These packing cubes are a game-changer for organized travel, maximizing space and minimizing wrinkles in your suitcase.', brand = 'Eagle Creek', product_dimension = 'Set of 3: Small, Medium, Large', rent = 5.00 WHERE product_id = 15;
UPDATE Products SET long_description = 'Never run out of battery on your adventures! This portable charger provides multiple charges for your phone and other devices.', brand = 'Anker', product_dimension = '6 inches', rent = 6.00 WHERE product_id = 16;
UPDATE Products SET long_description = 'Get a good night\'s sleep anywhere with this soft and comfortable eye mask, designed to block out light.', brand = 'Alaska Bear', product_dimension = 'One size fits most', rent = 1.00 WHERE product_id = 17;
UPDATE Products SET long_description = 'This small but mighty first-aid kit contains everything you need to treat minor injuries while you\'re traveling.', brand = 'Welly', product_dimension = '4in x 3in x 1in', rent = 2.00 WHERE product_id = 18;


ALTER TABLE orders
ADD COLUMN recipient_name VARCHAR(100),
ADD COLUMN recipient_phone VARCHAR(15),
ADD COLUMN recipient_address VARCHAR(255),
ADD COLUMN payment_method VARCHAR(50),
ADD COLUMN payment_status VARCHAR(20),
ADD COLUMN delivery_date DATE,
ADD COLUMN tracking_number VARCHAR(50),
ADD COLUMN notes TEXT;


ALTER TABLE cart
ADD COLUMN order_type ENUM('buy', 'rent') NOT NULL,
ADD COLUMN rent_days INT DEFAULT NULL;
