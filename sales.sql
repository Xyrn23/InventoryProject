-- -------------------------------------------------------------------------------------------------
-- SQLITE SCRIPT: HIGH-VOLUME SALES DATA (SCHEMA COMPLIANT)
--
-- This script is fully compliant with the user-provided schemas for 'sales' and 'sales_items',
-- using AUTOINCREMENT for the 'id' columns and 'transaction_id' for linking.
-- Cashier Names: Jersey, Janvyn, Aaron, Winalyn. Tax Rate: 12% VAT.
-- -------------------------------------------------------------------------------------------------

-- 1. SALES TRANSACTIONS (22 Consecutive Days)
-- NOTE: We omit the 'id' column as it is set to INTEGER PRIMARY KEY AUTOINCREMENT.
-- Financials are calculated based on the item list defined in section 2.

-- Sale 1: T-20250925-001 | Subtotal: 17,496.00 | Total: 19,595.52 (Cashier: Jersey)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20250925-001', '2025-09-25 15:30:00', 17496.00, 2099.52, 19595.52, 20000.00, 404.48, 'Jersey');

-- Sale 2: T-20250926-002 | Subtotal: 28,999.00 | Total: 32,478.88 (Cashier: Janvyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20250926-002', '2025-09-26 12:45:00', 28999.00, 3479.88, 32478.88, 33000.00, 521.12, 'Janvyn');

-- Sale 3: T-20250927-003 | Subtotal: 54,999.00 | Total: 61,598.88 (Cashier: Aaron)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20250927-003', '2025-09-27 18:15:00', 54999.00, 6599.88, 61598.88, 62000.00, 401.12, 'Aaron');

-- Sale 4: T-20250928-004 | Subtotal: 25,998.00 | Total: 29,117.76 (Cashier: Winalyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20250928-004', '2025-09-28 10:00:00', 25998.00, 3119.76, 29117.76, 30000.00, 882.24, 'Winalyn');

-- Sale 5: T-20250929-005 | Subtotal: 68,999.00 | Total: 77,278.88 (Cashier: Jersey)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20250929-005', '2025-09-29 09:30:00', 68999.00, 8279.88, 77278.88, 78000.00, 721.12, 'Jersey');

-- Sale 6: T-20250930-006 | Subtotal: 20,497.00 | Total: 22,956.64 (Cashier: Janvyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20250930-006', '2025-09-30 16:20:00', 20497.00, 2459.64, 22956.64, 23000.00, 43.36, 'Janvyn');

-- Sale 7: T-20251001-007 | Subtotal: 24,999.00 | Total: 27,998.88 (Cashier: Aaron)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251001-007', '2025-10-01 11:10:00', 24999.00, 2999.88, 27998.88, 28000.00, 1.12, 'Aaron');

-- Sale 8: T-20251002-008 | Subtotal: 45,999.00 | Total: 51,518.88 (Cashier: Winalyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251002-008', '2025-10-02 14:05:00', 45999.00, 5519.88, 51518.88, 52000.00, 481.12, 'Winalyn');

-- Sale 9: T-20251003-009 | Subtotal: 25,998.00 | Total: 29,117.76 (Cashier: Jersey)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251003-009', '2025-10-03 17:50:00', 25998.00, 3119.76, 29117.76, 30000.00, 882.24, 'Jersey');

-- Sale 10: T-20251004-010 | Subtotal: 20,998.00 | Total: 23,517.76 (Cashier: Janvyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251004-010', '2025-10-04 13:40:00', 20998.00, 2519.76, 23517.76, 24000.00, 482.24, 'Janvyn');

-- Sale 11: T-20251005-011 | Subtotal: 17,998.00 | Total: 20,157.76 (Cashier: Aaron)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251005-011', '2025-10-05 19:25:00', 17998.00, 2159.76, 20157.76, 21000.00, 842.24, 'Aaron');

-- Sale 12: T-20251006-012 | Subtotal: 13,998.00 | Total: 15,677.76 (Cashier: Winalyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251006-012', '2025-10-06 08:55:00', 13998.00, 1679.76, 15677.76, 16000.00, 322.24, 'Winalyn');

-- Sale 13: T-20251007-013 | Subtotal: 24,999.00 | Total: 27,998.88 (Cashier: Jersey)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251007-013', '2025-10-07 15:00:00', 24999.00, 2999.88, 27998.88, 28000.00, 1.12, 'Jersey');

-- Sale 14: T-20251008-014 | Subtotal: 18,998.00 | Total: 21,277.76 (Cashier: Janvyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251008-014', '2025-10-08 11:35:00', 18998.00, 2279.76, 21277.76, 22000.00, 722.24, 'Janvyn');

-- Sale 15: T-20251009-015 | Subtotal: 22,997.00 | Total: 25,756.64 (Cashier: Aaron)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251009-015', '2025-10-09 16:45:00', 22997.00, 2759.64, 25756.64, 26000.00, 243.36, 'Aaron');

-- Sale 16: T-20251010-016 | Subtotal: 29,998.00 | Total: 33,597.76 (Cashier: Winalyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251010-016', '2025-10-10 12:00:00', 29998.00, 3599.76, 33597.76, 34000.00, 402.24, 'Winalyn');

-- Sale 17: T-20251011-017 | Subtotal: 13,598.00 | Total: 15,229.76 (Cashier: Jersey)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251011-017', '2025-10-11 17:15:00', 13598.00, 1631.76, 15229.76, 16000.00, 770.24, 'Jersey');

-- Sale 18: T-20251012-018 | Subtotal: 11,998.00 | Total: 13,437.76 (Cashier: Janvyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251012-018', '2025-10-12 14:20:00', 11998.00, 1439.76, 13437.76, 14000.00, 562.24, 'Janvyn');

-- Sale 19: T-20251013-019 | Subtotal: 11,998.00 | Total: 13,437.76 (Cashier: Aaron)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251013-019', '2025-10-13 10:30:00', 11998.00, 1439.76, 13437.76, 14000.00, 562.24, 'Aaron');

-- Sale 20: T-20251014-020 | Subtotal: 12,998.00 | Total: 14,557.76 (Cashier: Winalyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251014-020', '2025-10-14 15:40:00', 12998.00, 1559.76, 14557.76, 15000.00, 442.24, 'Winalyn');

-- Sale 21: T-20251015-021 | Subtotal: 12,297.50 | Total: 13,773.20 (Cashier: Jersey)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251015-021', '2025-10-15 13:10:00', 12297.50, 1475.70, 13773.20, 14000.00, 226.80, 'Jersey');

-- Sale 22: T-20251016-022 | Subtotal: 13,998.00 | Total: 15,677.76 (Cashier: Janvyn)
INSERT INTO sales (transaction_id, sale_date, subtotal, tax_amount, total_amount, payment_received, change_given, cashier_name) VALUES
('T-20251016-022', '2025-10-16 18:05:00', 13998.00, 1679.76, 15677.76, 16000.00, 322.24, 'Janvyn');


---

-- 2. SALES ITEMS DATA (47 line items)
-- The 'sale_id' (Foreign Key) is retrieved via subquery using the 'transaction_id'.
-- NOTE: We omit the 'id' column as it is set to INTEGER PRIMARY KEY AUTOINCREMENT.

-- Sale 1: T-20250925-001 (17,496.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20250925-001'), 'T-20250925-001', 'DEL-MON003', 'Dell S2721QS 27-inch 4K Monitor', 11299.00, 1, 11299.00, '2025-09-25 15:30:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20250925-001'), 'T-20250925-001', 'SAMS-SSD012', 'Samsung 970 EVO Plus 1TB NVMe SSD', 4999.00, 1, 4999.00, '2025-09-25 15:30:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20250925-001'), 'T-20250925-001', 'ANE-CAB017', 'Anker PowerLine+ USB-C Cable', 599.00, 2, 1198.00, '2025-09-25 15:30:00');

-- Sale 2: T-20250926-002 (28,999.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20250926-002'), 'T-20250926-002', 'HP-LAP004', 'HP Pavilion 15 Laptop (i7/16GB)', 28999.00, 1, 28999.00, '2025-09-26 12:45:00');

-- Sale 3: T-20250927-003 (54,999.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20250927-003'), 'T-20250927-003', 'LEN-LAP024', 'Lenovo Legion 5 Gaming Laptop (RTX 3060)', 54999.00, 1, 54999.00, '2025-09-27 18:15:00');

-- Sale 4: T-20250928-004 (25,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20250928-004'), 'T-20250928-004', 'INT-CPU014', 'Intel Core i7-13700K Processor', 18999.00, 1, 18999.00, '2025-09-28 10:00:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20250928-004'), 'T-20250928-004', 'GIG-RAM036', 'G.Skill Trident Z5 32GB DDR5 RAM', 6999.00, 1, 6999.00, '2025-09-28 10:00:00');

-- Sale 5: T-20250929-005 (68,999.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20250929-005'), 'T-20250929-005', 'NVI-GPU035', 'NVIDIA GeForce RTX 4070 Ti Graphics Card', 68999.00, 1, 68999.00, '2025-09-29 09:30:00');

-- Sale 6: T-20250930-006 (20,497.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20250930-006'), 'T-20250930-006', 'SAM-MON023', 'Samsung G5 27-inch Curved Monitor', 11999.00, 1, 11999.00, '2025-09-30 16:20:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20250930-006'), 'T-20250930-006', 'RAZ-KEY042', 'Razer Huntsman Mini 60% Keyboard', 5999.00, 1, 5999.00, '2025-09-30 16:20:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20250930-006'), 'T-20250930-006', 'LOG-MOU021', 'Logitech G502 Hero Gaming Mouse', 2499.00, 1, 2499.00, '2025-09-30 16:20:00');

-- Sale 7: T-20251001-007 (24,999.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251001-007'), 'T-20251001-007', 'FOC-SPE047', 'Focal Alpha 80 Studio Monitors', 24999.00, 1, 24999.00, '2025-10-01 11:10:00');

-- Sale 8: T-20251002-008 (45,999.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251002-008'), 'T-20251002-008', 'AMD-CPU034', 'AMD Ryzen 9 7950X Processor', 45999.00, 1, 45999.00, '2025-10-02 14:05:00');

-- Sale 9: T-20251003-009 (25,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251003-009'), 'T-20251003-009', 'CAL-DOC020', 'CalDigit TS4 Thunderbolt Dock', 12999.00, 1, 12999.00, '2025-10-03 17:50:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251003-009'), 'T-20251003-009', 'JAB-HEA045', 'Jabra Evolve2 85 Headset', 12999.00, 1, 12999.00, '2025-10-03 17:50:00');

-- Sale 10: T-20251004-010 (20,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251004-010'), 'T-20251004-010', 'KEL-DOC040', 'Kensington SD5700T Thunderbolt Dock', 14999.00, 1, 14999.00, '2025-10-04 13:40:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251004-010'), 'T-20251004-010', 'AUD-MIC011', 'Audio-Technica AT2020 Condenser Mic', 5999.00, 1, 5999.00, '2025-10-04 13:40:00');

-- Sale 11: T-20251005-011 (17,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251005-011'), 'T-20251005-011', 'EPS-PRI010', 'Epson EcoTank L3250 Printer', 8999.00, 1, 8999.00, '2025-10-05 19:25:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251005-011'), 'T-20251005-011', 'LOG-CAM046', 'Logitech Brio 4K Webcam', 8999.00, 1, 8999.00, '2025-10-05 19:25:00');

-- Sale 12: T-20251006-012 (13,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251006-012'), 'T-20251006-012', 'DXR-CHA048', 'DXRacer Formula Gaming Chair', 8999.00, 1, 8999.00, '2025-10-06 08:55:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251006-012'), 'T-20251006-012', 'LOG-KEY022', 'Logitech MX Keys Wireless Keyboard', 4999.00, 1, 4999.00, '2025-10-06 08:55:00');

-- Sale 13: T-20251007-013 (24,999.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251007-013'), 'T-20251007-013', 'HER-CHA028', 'Herman Miller Sayl Office Chair', 24999.00, 1, 24999.00, '2025-10-07 15:00:00');

-- Sale 14: T-20251008-014 (18,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251008-014'), 'T-20251008-014', 'SAM-TAB009', 'Samsung Galaxy Tab A9 (10.5-inch)', 12999.00, 1, 12999.00, '2025-10-08 11:35:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251008-014'), 'T-20251008-014', 'APP-STY019', 'Apple Pencil 2nd Gen Stylus', 5999.00, 1, 5999.00, '2025-10-08 11:35:00');

-- Sale 15: T-20251009-015 (22,997.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251009-015'), 'T-20251009-015', 'LG-MON043', 'LG UltraGear 32-inch Curved Monitor', 15999.00, 1, 15999.00, '2025-10-09 16:45:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251009-015'), 'T-20251009-015', 'COR-RAM016', 'Corsair Vengeance 32GB DDR4 RAM Kit', 3499.00, 2, 6998.00, '2025-10-09 16:45:00');

-- Sale 16: T-20251010-016 (29,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251010-016'), 'T-20251010-016', 'ASU-LAP044', 'ASUS VivoBook Flip 14 2-in-1 Laptop', 27999.00, 1, 27999.00, '2025-10-10 12:00:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251010-016'), 'T-20251010-016', 'TAR-BAG018', 'Targus 15-inch Laptop Backpack', 1999.00, 1, 1999.00, '2025-10-10 12:00:00');

-- Sale 17: T-20251011-017 (13,598.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251011-017'), 'T-20251011-017', 'RAZ-HEA005', 'Razer BlackShark V2 Pro Headset', 8999.00, 1, 8999.00, '2025-10-11 17:15:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251011-017'), 'T-20251011-017', 'COR-KEY002', 'Corsair K70 RGB Pro Mechanical Keyboard', 4599.00, 1, 4599.00, '2025-10-11 17:15:00');

-- Sale 18: T-20251012-018 (11,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251012-018'), 'T-20251012-018', 'JBL-SPE007', 'JBL Charge 5 Bluetooth Speakers', 5999.00, 1, 5999.00, '2025-10-12 14:20:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251012-018'), 'T-20251012-018', 'WD-SSD032', 'Western Digital My Passport 2TB SSD', 5999.00, 1, 5999.00, '2025-10-12 14:20:00');

-- Sale 19: T-20251013-019 (11,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251013-019'), 'T-20251013-019', 'LOG-CAM026', 'Logitech StreamCam 4K Webcam', 6999.00, 1, 6999.00, '2025-10-13 10:30:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251013-019'), 'T-20251013-019', 'YET-MIC031', 'Yeti Nano USB Microphone', 4999.00, 1, 4999.00, '2025-10-13 10:30:00');

-- Sale 20: T-20251014-020 (12,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251014-020'), 'T-20251014-020', 'BRO-PRI030', 'Brother HL-L2350DW Laser Printer', 7999.00, 1, 7999.00, '2025-10-14 15:40:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251014-020'), 'T-20251014-020', 'IKE-CHA008', 'IKEA MARKUS Ergonomic Office Chair', 4999.00, 1, 4999.00, '2025-10-14 15:40:00');

-- Sale 21: T-20251015-021 (12,297.50 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251015-021'), 'T-20251015-021', 'BOS-SPE027', 'Bose SoundLink Flex Speakers', 4999.00, 1, 4999.00, '2025-10-15 13:10:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251015-021'), 'T-20251015-021', 'SEN-HEA025', 'Sennheiser HD 450BT Headset', 5999.00, 1, 5999.00, '2025-10-15 13:10:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251015-021'), 'T-20251015-021', 'LOG-MOU001', 'Logitech MX Master 3S Wireless Mouse', 1299.50, 1, 1299.50, '2025-10-15 13:10:00');

-- Sale 22: T-20251016-022 (13,998.00 subtotal)
INSERT INTO sales_items (sale_id, transaction_id, product_code, product_name, unit_price, quantity, subtotal, sold_at) VALUES
((SELECT id FROM sales WHERE transaction_id = 'T-20251016-022'), 'T-20251016-022', 'MIC-TAB029', 'Micromax Canvas Tab (10-inch)', 8999.00, 1, 8999.00, '2025-10-16 18:05:00'),
((SELECT id FROM sales WHERE transaction_id = 'T-20251016-022'), 'T-20251016-022', 'SEA-HDD033', 'Seagate Expansion 4TB Portable HDD', 4999.00, 1, 4999.00, '2025-10-16 18:05:00');
