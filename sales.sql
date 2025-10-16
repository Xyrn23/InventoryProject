-- -------------------------------------------------------------------------------------------------
-- SQLITE SCRIPT: HIGH-VOLUME SALES DATA (PHP)
--
-- This script generates data for a 'sales' and 'sales_items' table, covering 22 consecutive days
-- from September 25, 2025, to October 16, 2025.
--
-- All monetary values are in PHP (Philippine Pesos).
-- Daily sales totals consistently exceed PHP 5,000.
-- Product data reflects the user's 'products' inventory table.
-- -------------------------------------------------------------------------------------------------

-- 1. CASHIER DATA
-- Add 'Jersey' to the cashier list if not already present.
INSERT OR IGNORE INTO cashier (cashier_id, name) VALUES
(1, 'Janvyn'),
(2, 'Winalyn'),
(3, 'Aaron'),
(4, 'Jersey');

-- 2. SALES TRANSACTIONS (22 Consecutive Days)
-- The sale_id is set using RANDOM() to simulate production ID generation.

-- September 2025
INSERT INTO sales (sale_id, sale_date, cashier_id, amount) VALUES
(ABS(RANDOM()), '2025-09-25 15:30:00', 2, 17496.00), -- Items: DEL-MON003 (1), SAMS-SSD012 (1), ANE-CAB017 (2)
(ABS(RANDOM()), '2025-09-26 12:45:00', 4, 28999.00), -- Items: HP-LAP004 (1)
(ABS(RANDOM()), '2025-09-27 18:15:00', 3, 54999.00), -- Items: LEN-LAP024 (1)
(ABS(RANDOM()), '2025-09-28 10:00:00', 1, 25998.00), -- Items: INT-CPU014 (1), GIG-RAM036 (1)
(ABS(RANDOM()), '2025-09-29 09:30:00', 2, 68999.00), -- Items: NVI-GPU035 (1)
(ABS(RANDOM()), '2025-09-30 16:20:00', 4, 20497.00); -- Items: SAM-MON023 (1), RAZ-KEY042 (1), LOG-MOU021 (1)

-- October 2025
INSERT INTO sales (sale_id, sale_date, cashier_id, amount) VALUES
(ABS(RANDOM()), '2025-10-01 11:10:00', 3, 24999.00), -- Items: FOC-SPE047 (1)
(ABS(RANDOM()), '2025-10-02 14:05:00', 1, 45999.00), -- Items: AMD-CPU034 (1)
(ABS(RANDOM()), '2025-10-03 17:50:00', 2, 25998.00), -- Items: CAL-DOC020 (1), JAB-HEA045 (1)
(ABS(RANDOM()), '2025-10-04 13:40:00', 4, 20998.00), -- Items: KEL-DOC040 (1), AUD-MIC011 (1)
(ABS(RANDOM()), '2025-10-05 19:25:00', 3, 17998.00), -- Items: EPS-PRI010 (1), LOG-CAM046 (1)
(ABS(RANDOM()), '2025-10-06 08:55:00', 1, 13998.00), -- Items: DXR-CHA048 (1), LOG-KEY022 (1)
(ABS(RANDOM()), '2025-10-07 15:00:00', 2, 24999.00), -- Items: HER-CHA028 (1)
(ABS(RANDOM()), '2025-10-08 11:35:00', 4, 18998.00), -- Items: SAM-TAB009 (1), APP-STY019 (1)
(ABS(RANDOM()), '2025-10-09 16:45:00', 3, 22997.00), -- Items: LG-MON043 (1), COR-RAM016 (2)
(ABS(RANDOM()), '2025-10-10 12:00:00', 1, 29998.00), -- Items: ASU-LAP044 (1), TAR-BAG018 (1)
(ABS(RANDOM()), '2025-10-11 17:15:00', 2, 13598.00), -- Items: RAZ-HEA005 (1), COR-KEY002 (1)
(ABS(RANDOM()), '2025-10-12 14:20:00', 4, 11998.00), -- Items: JBL-SPE007 (1), WD-SSD032 (1)
(ABS(RANDOM()), '2025-10-13 10:30:00', 3, 11998.00), -- Items: LOG-CAM026 (1), YET-MIC031 (1)
(ABS(RANDOM()), '2025-10-14 15:40:00', 1, 12998.00), -- Items: BRO-PRI030 (1), IKE-CHA008 (1)
(ABS(RANDOM()), '2025-10-15 13:10:00', 2, 12297.50), -- Items: BOS-SPE027 (1), SEN-HEA025 (1), LOG-MOU001 (1)
(ABS(RANDOM()), '2025-10-16 18:05:00', 4, 13998.00); -- Items: MIC-TAB029 (1), SEA-HDD033 (1)


-- 3. SALES ITEMS DATA (linking products to sales)
-- Note: sale_id values will match the ABS(RANDOM()) values generated in the sales table.
-- For a real database, these should be generated in sequence using a transaction,
-- but for this script, we'll map them based on insertion order for demonstration.

-- Clear existing data and re-insert 47 lines
DELETE FROM sales_items;

-- Sale 1: 2025-09-25 (PHP 17,496.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-25%'), 'DEL-MON003', 1, 11299.00), -- Dell S2721QS 27-inch 4K Monitor
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-25%'), 'SAMS-SSD012', 1, 4999.00),  -- Samsung 970 EVO Plus 1TB NVMe SSD
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-25%'), 'ANE-CAB017', 2, 599.00);   -- Anker PowerLine+ USB-C Cable

-- Sale 2: 2025-09-26 (PHP 28,999.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-26%'), 'HP-LAP004', 1, 28999.00);   -- HP Pavilion 15 Laptop (i7/16GB)

-- Sale 3: 2025-09-27 (PHP 54,999.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-27%'), 'LEN-LAP024', 1, 54999.00);  -- Lenovo Legion 5 Gaming Laptop (RTX 3060)

-- Sale 4: 2025-09-28 (PHP 25,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-28%'), 'INT-CPU014', 1, 18999.00), -- Intel Core i7-13700K Processor
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-28%'), 'GIG-RAM036', 1, 6999.00);  -- G.Skill Trident Z5 32GB DDR5 RAM

-- Sale 5: 2025-09-29 (PHP 68,999.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-29%'), 'NVI-GPU035', 1, 68999.00); -- NVIDIA GeForce RTX 4070 Ti Graphics Card

-- Sale 6: 2025-09-30 (PHP 20,497.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-30%'), 'SAM-MON023', 1, 11999.00), -- Samsung G5 27-inch Curved Monitor
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-30%'), 'RAZ-KEY042', 1, 5999.00),  -- Razer Huntsman Mini 60% Keyboard
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-09-30%'), 'LOG-MOU021', 1, 2499.00);  -- Logitech G502 Hero Gaming Mouse

-- Sale 7: 2025-10-01 (PHP 24,999.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-01%'), 'FOC-SPE047', 1, 24999.00); -- Focal Alpha 80 Studio Monitors

-- Sale 8: 2025-10-02 (PHP 45,999.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-02%'), 'AMD-CPU034', 1, 45999.00); -- AMD Ryzen 9 7950X Processor

-- Sale 9: 2025-10-03 (PHP 25,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-03%'), 'CAL-DOC020', 1, 12999.00), -- CalDigit TS4 Thunderbolt Dock
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-03%'), 'JAB-HEA045', 1, 12999.00); -- Jabra Evolve2 85 Headset

-- Sale 10: 2025-10-04 (PHP 20,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-04%'), 'KEL-DOC040', 1, 14999.00), -- Kensington SD5700T Thunderbolt Dock
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-04%'), 'AUD-MIC011', 1, 5999.00);  -- Audio-Technica AT2020 Condenser Mic

-- Sale 11: 2025-10-05 (PHP 17,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-05%'), 'EPS-PRI010', 1, 8999.00),  -- Epson EcoTank L3250 Printer
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-05%'), 'LOG-CAM046', 1, 8999.00);  -- Logitech Brio 4K Webcam

-- Sale 12: 2025-10-06 (PHP 13,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-06%'), 'DXR-CHA048', 1, 8999.00),  -- DXRacer Formula Gaming Chair
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-06%'), 'LOG-KEY022', 1, 4999.00);  -- Logitech MX Keys Wireless Keyboard

-- Sale 13: 2025-10-07 (PHP 24,999.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-07%'), 'HER-CHA028', 1, 24999.00); -- Herman Miller Sayl Office Chair

-- Sale 14: 2025-10-08 (PHP 18,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-08%'), 'SAM-TAB009', 1, 12999.00), -- Samsung Galaxy Tab A9 (10.5-inch)
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-08%'), 'APP-STY019', 1, 5999.00);  -- Apple Pencil 2nd Gen Stylus

-- Sale 15: 2025-10-09 (PHP 22,997.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-09%'), 'LG-MON043', 1, 15999.00),  -- LG UltraGear 32-inch Curved Monitor
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-09%'), 'COR-RAM016', 2, 3499.00);  -- Corsair Vengeance 32GB DDR4 RAM Kit

-- Sale 16: 2025-10-10 (PHP 29,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-10%'), 'ASU-LAP044', 1, 27999.00), -- ASUS VivoBook Flip 14 2-in-1 Laptop
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-10%'), 'TAR-BAG018', 1, 1999.00);  -- Targus 15-inch Laptop Backpack

-- Sale 17: 2025-10-11 (PHP 13,598.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-11%'), 'RAZ-HEA005', 1, 8999.00),  -- Razer BlackShark V2 Pro Headset
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-11%'), 'COR-KEY002', 1, 4599.00);  -- Corsair K70 RGB Pro Mechanical Keyboard

-- Sale 18: 2025-10-12 (PHP 11,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-12%'), 'JBL-SPE007', 1, 5999.00),  -- JBL Charge 5 Bluetooth Speakers
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-12%'), 'WD-SSD032', 1, 5999.00);  -- Western Digital My Passport 2TB SSD

-- Sale 19: 2025-10-13 (PHP 11,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-13%'), 'LOG-CAM026', 1, 6999.00),  -- Logitech StreamCam 4K Webcam
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-13%'), 'YET-MIC031', 1, 4999.00);  -- Yeti Nano USB Microphone

-- Sale 20: 2025-10-14 (PHP 12,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-14%'), 'BRO-PRI030', 1, 7999.00),  -- Brother HL-L2350DW Laser Printer
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-14%'), 'IKE-CHA008', 1, 4999.00);  -- IKEA MARKUS Ergonomic Office Chair

-- Sale 21: 2025-10-15 (PHP 12,297.50)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-15%'), 'BOS-SPE027', 1, 4999.00),  -- Bose SoundLink Flex Speakers
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-15%'), 'SEN-HEA025', 1, 5999.00),  -- Sennheiser HD 450BT Headset
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-15%'), 'LOG-MOU001', 1, 1299.50);  -- Logitech MX Master 3S Wireless Mouse

-- Sale 22: 2025-10-16 (PHP 13,998.00)
INSERT INTO sales_items (sale_id, product_code, quantity, unit_price) VALUES
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-16%'), 'MIC-TAB029', 1, 8999.00),  -- Micromax Canvas Tab (10-inch)
((SELECT sale_id FROM sales WHERE sale_date LIKE '2025-10-16%'), 'SEA-HDD033', 1, 4999.00);  -- Seagate Expansion 4TB Portable HDD
