-- Create products table and sample data
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(64) UNIQUE,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  image VARCHAR(255),
  color VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products (sku, name, description, price, image, color) VALUES
('AIR-001','Air Runner Pro','Premium running shoe',139.99,'/public/assets/images/placeholder1.jpg','Black/Red'),
('COURT-001','Court Classic','Minimalist court sneaker',119.99,'/public/assets/images/placeholder2.jpg','White/Black'),
('STREET-001','Street Glide','Comfort-focused lifestyle shoe',129.99,'/public/assets/images/placeholder3.jpg','Black'),
('SPRINT-001','Sprint Elite','Lightweight performance trainer',149.99,'/public/assets/images/placeholder4.jpg','Red/Black');
