<?php

require_once __DIR__ . '/../config/db.php';

// ===========================
// PRODUCTS MODULE FUNCTIONS
// ===========================

/**
 * Fetch all products
 */
function get_all_products(PDO $pdo): array {
    $stmt = $pdo->prepare("
        SELECT p.*, s.name AS supplier_name
        FROM products p
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch a single product by ID
 */
function get_product_by_id(PDO $pdo, int $product_id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->execute(['product_id' => $product_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Add a new product
 */
function add_product(PDO $pdo, string $name, ?string $brand, ?string $category, ?float $size_ml,
                     float $cost_price, float $retail_price, ?int $supplier_id, string $status, string $description): void {
    $stmt = $pdo->prepare("
        INSERT INTO products 
        (name, brand, category, size_ml, cost_price, retail_price, supplier_id, status, description, created_at)
        VALUES 
        (:name, :brand, :category, :size_ml, :cost_price, :retail_price, :supplier_id, :status, :description, NOW())
    ");
    $stmt->execute([
        'name' => $name,
        'brand' => $brand,
        'category' => $category,
        'size_ml' => $size_ml,
        'cost_price' => $cost_price,
        'retail_price' => $retail_price,
        'supplier_id' => $supplier_id,
        'status' => $status,
        'description' => $description
    ]);
}

/**
 * Edit an existing product
 */
function edit_product(PDO $pdo, int $product_id, string $name, ?string $brand, ?string $category, ?float $size_ml,
                      float $cost_price, float $retail_price, ?int $supplier_id, string $status, string $description): void {
    $stmt = $pdo->prepare("
        UPDATE products SET
            name = :name,
            brand = :brand,
            category = :category,
            size_ml = :size_ml,
            cost_price = :cost_price,
            retail_price = :retail_price,
            supplier_id = :supplier_id,
            status = :status,
            description = :description
        WHERE product_id = :product_id
    ");
    $stmt->execute([
        'name' => $name,
        'brand' => $brand,
        'category' => $category,
        'size_ml' => $size_ml,
        'cost_price' => $cost_price,
        'retail_price' => $retail_price,
        'supplier_id' => $supplier_id,
        'status' => $status,
        'description' => $description,
        'product_id' => $product_id
    ]);
}

/**
 * Delete a product
 */
function delete_product(PDO $pdo, int $product_id): void {
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = :product_id");
    $stmt->execute(['product_id' => $product_id]);
}

// ======= SUPPLIERS =======

// Fetch all suppliers
function get_all_suppliers(PDO $pdo) {
    $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch a single supplier by ID
function get_supplier_by_id(PDO $pdo, int $supplier_id) {
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Add a new supplier
function add_supplier(PDO $pdo, string $name, ?string $contact_person, ?string $phone, ?string $email, ?string $address, float $reliability_score = 0.0) {
    $stmt = $pdo->prepare("
        INSERT INTO suppliers (name, contact_person, phone, email, address, reliability_score)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $contact_person, $phone, $email, $address, $reliability_score]);
}

// Update supplier
function update_supplier(PDO $pdo, int $supplier_id, string $name, ?string $contact_person, ?string $phone, ?string $email, ?string $address, float $reliability_score = 0.0) {
    $stmt = $pdo->prepare("
        UPDATE suppliers
        SET name = ?, contact_person = ?, phone = ?, email = ?, address = ?, reliability_score = ?
        WHERE supplier_id = ?
    ");
    $stmt->execute([$name, $contact_person, $phone, $email, $address, $reliability_score, $supplier_id]);
}

// Delete supplier
function delete_supplier(PDO $pdo, int $supplier_id) {
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);
}

// Get all purchases with optional filter
function get_all_purchases($pdo, $filters = []) {
    $sql = "SELECT p.*, s.name AS supplier_name
            FROM purchases p
            JOIN suppliers s ON p.supplier_id = s.supplier_id
            WHERE 1";
    
    $params = [];
    if (!empty($filters['supplier_id'])) {
        $sql .= " AND p.supplier_id = :supplier_id";
        $params[':supplier_id'] = $filters['supplier_id'];
    }
    if (!empty($filters['date_from'])) {
        $sql .= " AND p.purchase_date >= :date_from";
        $params[':date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= " AND p.purchase_date <= :date_to";
        $params[':date_to'] = $filters['date_to'];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get single purchase by ID
function get_purchase_by_id($pdo, $purchase_id) {
    $stmt = $pdo->prepare("SELECT p.*, s.name AS supplier_name
                           FROM purchases p
                           JOIN suppliers s ON p.supplier_id = s.supplier_id
                           WHERE p.purchase_id = :id");
    $stmt->execute([':id' => $purchase_id]);
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($purchase) {
        $stmt = $pdo->prepare("SELECT pi.*, pr.name AS product_name
                               FROM purchase_items pi
                               JOIN products pr ON pi.product_id = pr.product_id
                               WHERE pi.purchase_id = :id");
        $stmt->execute([':id' => $purchase_id]);
        $purchase['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $purchase;
}

// Create a purchase
function create_purchase($pdo, $supplier_id, $items, $payment_method, $invoice_number = null, $purchase_date = null) {
    if (!$purchase_date) $purchase_date = date('Y-m-d');

    $pdo->beginTransaction();
    try {
        // Calculate total cost
        $total_cost = 0;
        foreach ($items as $item) {
            $total_cost += $item['quantity'] * $item['cost_per_unit'];
        }

        // Insert purchase
        $stmt = $pdo->prepare("
            INSERT INTO purchases (supplier_id, invoice_number, purchase_date, total_cost, payment_method)
            VALUES (:supplier_id, :invoice_number, :purchase_date, :total_cost, :payment_method)
        ");
        $stmt->execute([
            ':supplier_id' => $supplier_id,
            ':invoice_number' => $invoice_number,
            ':purchase_date' => $purchase_date,
            ':total_cost' => $total_cost,
            ':payment_method' => $payment_method
        ]);
        $purchase_id = $pdo->lastInsertId();

        // Insert purchase items and update inventory
        foreach ($items as $item) {
            // Insert into purchase_items
            $stmtItem = $pdo->prepare("
                INSERT INTO purchase_items (purchase_id, product_id, quantity, cost_per_unit)
                VALUES (:purchase_id, :product_id, :quantity, :cost_per_unit)
            ");
            $stmtItem->execute([
                ':purchase_id' => $purchase_id,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':cost_per_unit' => $item['cost_per_unit']
            ]);

            // Update inventory
            $stmtInv = $pdo->prepare("SELECT * FROM inventory WHERE product_id = :product_id");
            $stmtInv->execute([':product_id' => $item['product_id']]);
            $inventory = $stmtInv->fetch(PDO::FETCH_ASSOC);

            if ($inventory) {
                $new_qty = $inventory['stock_in'] + $item['quantity'];
                $stmtUpdate = $pdo->prepare("
                    UPDATE inventory
                    SET stock_in = :stock_in, last_updated = NOW()
                    WHERE product_id = :product_id
                ");
                $stmtUpdate->execute([
                    ':stock_in' => $new_qty,
                    ':product_id' => $item['product_id']
                ]);
            } else {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO inventory (product_id, stock_in, stock_out)
                    VALUES (:product_id, :stock_in, 0)
                ");
                $stmtInsert->execute([
                    ':product_id' => $item['product_id'],
                    ':stock_in' => $item['quantity']
                ]);
            }
        }

        $pdo->commit();
        return $purchase_id;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Delete a purchase (and reverse inventory)
function delete_purchase($pdo, $purchase_id) {
    $pdo->beginTransaction();
    try {
        // Fetch items to reverse inventory
        $stmt = $pdo->prepare("SELECT * FROM purchase_items WHERE purchase_id = :id");
        $stmt->execute([':id' => $purchase_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $stmtInv = $pdo->prepare("SELECT * FROM inventory WHERE product_id = :product_id");
            $stmtInv->execute([':product_id' => $item['product_id']]);
            $inventory = $stmtInv->fetch(PDO::FETCH_ASSOC);
            if ($inventory) {
                $new_qty = $inventory['quantity'] - $item['quantity'];
                if ($new_qty < 0) $new_qty = 0;
                $stmtUpdate = $pdo->prepare("UPDATE inventory SET quantity = :qty WHERE product_id = :product_id");
                $stmtUpdate->execute([
                    ':qty' => $new_qty,
                    ':product_id' => $item['product_id']
                ]);
            }
        }

        // Delete purchase items
        $stmt = $pdo->prepare("DELETE FROM purchase_items WHERE purchase_id = :id");
        $stmt->execute([':id' => $purchase_id]);

        // Delete purchase
        $stmt = $pdo->prepare("DELETE FROM purchases WHERE purchase_id = :id");
        $stmt->execute([':id' => $purchase_id]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// SALES

function get_all_sales(PDO $pdo, array $filters = []) {
    $sql = "SELECT s.*, c.name AS customer_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.customer_id
            WHERE 1";

    $params = [];
    if (!empty($filters['customer_id'])) {
        $sql .= " AND s.customer_id = :customer_id";
        $params[':customer_id'] = $filters['customer_id'];
    }
    if (!empty($filters['date_from'])) {
        $sql .= " AND s.sale_date >= :date_from";
        $params[':date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= " AND s.sale_date <= :date_to";
        $params[':date_to'] = $filters['date_to'];
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_sale_by_id(PDO $pdo, $sale_id) {
    $stmt = $pdo->prepare("SELECT * FROM sales WHERE sale_id = ?");
    $stmt->execute([$sale_id]);
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sale) {
        $stmt2 = $pdo->prepare("SELECT si.*, p.name AS product_name
                                FROM sale_items si
                                JOIN products p ON si.product_id = p.product_id
                                WHERE si.sale_id = ?");
        $stmt2->execute([$sale_id]);
        $sale['items'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
    return $sale;
}

function create_sale(PDO $pdo, $customer_id, $items, $payment_method, $sales_channel, $discount = 0) {
    try {
        $pdo->beginTransaction();

        $total_amount = 0;

        // Calculate total
        foreach ($items as $item) {
            $total_amount += $item['unit_price'] * $item['quantity'];
        }
        $total_amount -= $discount;

        // Insert into sales
        $stmt = $pdo->prepare("INSERT INTO sales 
            (customer_id, sale_date, total_amount, payment_method, sales_channel, discount) 
            VALUES (?, NOW(), ?, ?, ?, ?)");
        $stmt->execute([$customer_id, $total_amount, $payment_method, $sales_channel, $discount]);
        $sale_id = $pdo->lastInsertId();

        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $unit_price = $item['unit_price'];

            // Check inventory
            $stmt = $pdo->prepare("SELECT current_stock FROM inventory WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $stock = $stmt->fetchColumn();
            if ($stock < $quantity) throw new Exception("Not enough stock for product ID $product_id");

            // Fetch cost_price (average cost from purchases)
            $stmt = $pdo->prepare("SELECT cost_price FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $cost_price = $stmt->fetchColumn();

            // Insert sale item
            $stmt = $pdo->prepare("INSERT INTO sale_items
                (sale_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$sale_id, $product_id, $quantity, $unit_price]);

            // Update inventory
            $stmt = $pdo->prepare("UPDATE inventory SET stock_out = stock_out + ? WHERE product_id = ?");
            $stmt->execute([$quantity, $product_id]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function update_sale(PDO $pdo, $sale_id, $customer_id, $items, $payment_method, $sales_channel, $discount = 0) {
    try {
        $pdo->beginTransaction();

        // Fetch old items
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM sale_items WHERE sale_id = ?");
        $stmt->execute([$sale_id]);
        $old_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Reverse inventory
        foreach ($old_items as $item) {
            $stmt = $pdo->prepare("UPDATE inventory SET stock_out = stock_out - ? WHERE product_id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        // Delete old sale_items
        $stmt = $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?");
        $stmt->execute([$sale_id]);

        // Recalculate total
        $total_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['unit_price'] * $item['quantity'];
        }
        $total_amount -= $discount;

        // Update sale
        $stmt = $pdo->prepare("UPDATE sales SET 
            customer_id = ?, total_amount = ?, payment_method = ?, sales_channel = ?, discount = ?
            WHERE sale_id = ?");
        $stmt->execute([$customer_id, $total_amount, $payment_method, $sales_channel, $discount, $sale_id]);

        // Insert new items and adjust inventory
        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $unit_price = $item['unit_price'];

            // Check inventory
            $stmt = $pdo->prepare("SELECT current_stock FROM inventory WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $stock = $stmt->fetchColumn();
            if ($stock < $quantity) throw new Exception("Not enough stock for product ID $product_id");

            $stmt = $pdo->prepare("INSERT INTO sale_items
                (sale_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$sale_id, $product_id, $quantity, $unit_price]);

            // Update inventory
            $stmt = $pdo->prepare("UPDATE inventory SET stock_out = stock_out + ? WHERE product_id = ?");
            $stmt->execute([$quantity, $product_id]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function delete_sale(PDO $pdo, $sale_id) {
    try {
        $pdo->beginTransaction();

        // Fetch sale items
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM sale_items WHERE sale_id = ?");
        $stmt->execute([$sale_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Reverse inventory
        foreach ($items as $item) {
            $stmt = $pdo->prepare("UPDATE inventory SET stock_out = stock_out - ? WHERE product_id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        // Delete sale items
        $stmt = $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?");
        $stmt->execute([$sale_id]);

        // Delete sale
        $stmt = $pdo->prepare("DELETE FROM sales WHERE sale_id = ?");
        $stmt->execute([$sale_id]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// ===========================
// RETURNS MODULE FUNCTIONS
// ===========================

/**
 * Fetch all product returns with related product name.
 */
function get_all_returns(PDO $pdo): array {
    $stmt = $pdo->prepare("
        SELECT r.*, p.name AS product_name
        FROM returns r
        JOIN products p ON r.product_id = p.product_id
        ORDER BY r.return_date DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch a single return record by ID.
 */
function get_return_by_id(PDO $pdo, int $return_id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM returns WHERE return_id = :return_id");
    $stmt->execute(['return_id' => $return_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Add a new product return and update related tables accordingly.
 */
function add_return(PDO $pdo, int $sale_id, int $product_id, int $quantity, string $reason): void {
    $pdo->beginTransaction();
    try {
        // Validate sale item
        $stmt = $pdo->prepare("
            SELECT quantity, unit_price 
            FROM sale_items 
            WHERE sale_id = :sale_id AND product_id = :product_id
        ");
        $stmt->execute(['sale_id' => $sale_id, 'product_id' => $product_id]);
        $sale_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sale_item) {
            throw new Exception("Product not found in sale.");
        }

        if ($quantity > $sale_item['quantity']) {
            throw new Exception("Return quantity exceeds sold quantity.");
        }

        // Adjust inventory (reduce stock_out)
        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET stock_out = stock_out - :qty 
            WHERE product_id = :product_id
        ");
        $stmt->execute(['qty' => $quantity, 'product_id' => $product_id]);

        // Insert return record
        $stmt = $pdo->prepare("
            INSERT INTO returns (sale_id, product_id, quantity, reason, return_date)
            VALUES (:sale_id, :product_id, :quantity, :reason, NOW())
        ");
        $stmt->execute([
            'sale_id' => $sale_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'reason' => $reason
        ]);

        // Adjust sale total
        $stmt = $pdo->prepare("
            UPDATE sales s
            JOIN sale_items si ON s.sale_id = si.sale_id
            SET s.total_amount = s.total_amount - (:qty * si.unit_price)
            WHERE s.sale_id = :sale_id AND si.product_id = :product_id
        ");
        $stmt->execute([
            'qty' => $quantity,
            'sale_id' => $sale_id,
            'product_id' => $product_id
        ]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Edit an existing product return safely.
 * Reverses previous effects, then applies new values in a single transaction.
 */
function edit_return(PDO $pdo, int $return_id, int $sale_id, int $product_id, int $quantity, string $reason): void {
    $pdo->beginTransaction();
    try {
        $old = get_return_by_id($pdo, $return_id);
        if (!$old) {
            throw new Exception("Return not found.");
        }

        // Validate sale item for new data
        $stmt = $pdo->prepare("
            SELECT quantity, unit_price 
            FROM sale_items 
            WHERE sale_id = :sale_id AND product_id = :product_id
        ");
        $stmt->execute(['sale_id' => $sale_id, 'product_id' => $product_id]);
        $sale_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sale_item) {
            throw new Exception("Product not found in sale.");
        }

        if ($quantity > $sale_item['quantity']) {
            throw new Exception("Return quantity exceeds sold quantity.");
        }

        // STEP 1: Reverse previous return’s impact
        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET stock_out = stock_out + :old_qty 
            WHERE product_id = :old_product
        ");
        $stmt->execute(['old_qty' => $old['quantity'], 'old_product' => $old['product_id']]);

        $stmt = $pdo->prepare("
            UPDATE sales s
            JOIN sale_items si ON s.sale_id = si.sale_id
            SET s.total_amount = s.total_amount + (:old_qty * si.unit_price)
            WHERE s.sale_id = :old_sale AND si.product_id = :old_product
        ");
        $stmt->execute([
            'old_qty' => $old['quantity'],
            'old_sale' => $old['sale_id'],
            'old_product' => $old['product_id']
        ]);

        // STEP 2: Apply new return logic (same as add_return but inline)
        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET stock_out = stock_out - :qty 
            WHERE product_id = :product_id
        ");
        $stmt->execute(['qty' => $quantity, 'product_id' => $product_id]);

        $stmt = $pdo->prepare("
            UPDATE sales s
            JOIN sale_items si ON s.sale_id = si.sale_id
            SET s.total_amount = s.total_amount - (:qty * si.unit_price)
            WHERE s.sale_id = :sale_id AND si.product_id = :product_id
        ");
        $stmt->execute([
            'qty' => $quantity,
            'sale_id' => $sale_id,
            'product_id' => $product_id
        ]);

        // STEP 3: Update return record itself
        $stmt = $pdo->prepare("
            UPDATE returns
            SET sale_id = :sale_id,
                product_id = :product_id,
                quantity = :quantity,
                reason = :reason,
                return_date = NOW()
            WHERE return_id = :return_id
        ");
        $stmt->execute([
            'sale_id' => $sale_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'reason' => $reason,
            'return_id' => $return_id
        ]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Delete a product return and revert all effects safely.
 */
function delete_return(PDO $pdo, int $return_id): void {
    $pdo->beginTransaction();
    try {
        $r = get_return_by_id($pdo, $return_id);
        if (!$r) {
            throw new Exception("Return not found.");
        }

        // Reverse inventory adjustment
        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET stock_out = stock_out + :qty 
            WHERE product_id = :product_id
        ");
        $stmt->execute([
            'qty' => $r['quantity'],
            'product_id' => $r['product_id']
        ]);

        // Reverse sales total
        $stmt = $pdo->prepare("
            UPDATE sales s
            JOIN sale_items si ON s.sale_id = si.sale_id
            SET s.total_amount = s.total_amount + (:qty * si.unit_price)
            WHERE s.sale_id = :sale_id AND si.product_id = :product_id
        ");
        $stmt->execute([
            'qty' => $r['quantity'],
            'sale_id' => $r['sale_id'],
            'product_id' => $r['product_id']
        ]);

        // Remove the return record
        $stmt = $pdo->prepare("DELETE FROM returns WHERE return_id = :return_id");
        $stmt->execute(['return_id' => $return_id]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/* ============================================
   EXPENSES FUNCTIONS
   ============================================ */

// Add a new expense
function addExpense($conn, $category, $description, $amount, $expense_date, $payment_method) {
    $stmt = $conn->prepare("INSERT INTO expenses (category, description, amount, expense_date, payment_method, created_at)
                            VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssdss", $category, $description, $amount, $expense_date, $payment_method);
    return $stmt->execute();
}

// Fetch all expenses
function getAllExpenses($conn) {
    $sql = "SELECT * FROM expenses ORDER BY expense_date DESC";
    $result = $conn->query($sql);
    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
    return $expenses;
}

// Fetch single expense by ID
function getExpenseById($conn, $expense_id) {
    $stmt = $conn->prepare("SELECT * FROM expenses WHERE expense_id = ?");
    $stmt->bind_param("i", $expense_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Update an expense
function updateExpense($conn, $expense_id, $category, $description, $amount, $expense_date, $payment_method) {
    $stmt = $conn->prepare("UPDATE expenses 
                            SET category=?, description=?, amount=?, expense_date=?, payment_method=? 
                            WHERE expense_id=?");
    $stmt->bind_param("ssdssi", $category, $description, $amount, $expense_date, $payment_method, $expense_id);
    return $stmt->execute();
}

// Delete an expense
function deleteExpense($conn, $expense_id) {
    $stmt = $conn->prepare("DELETE FROM expenses WHERE expense_id = ?");
    $stmt->bind_param("i", $expense_id);
    return $stmt->execute();
}

// MARKETING MODULE FUNCTIONS
function get_all_campaigns($pdo) {
    $stmt = $pdo->query("SELECT * FROM marketing ORDER BY start_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_campaign_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM marketing WHERE campaign_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function add_campaign($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO marketing (platform, start_date, end_date, budget, sales_generated, remarks)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['platform'], $data['start_date'], $data['end_date'],
        $data['budget'], $data['sales_generated'], $data['remarks']
    ]);
}

function update_campaign($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE marketing 
        SET platform=?, start_date=?, end_date=?, budget=?, sales_generated=?, remarks=? 
        WHERE campaign_id=?
    ");
    return $stmt->execute([
        $data['platform'], $data['start_date'], $data['end_date'],
        $data['budget'], $data['sales_generated'], $data['remarks'], $id
    ]);
}

function delete_campaign($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM marketing WHERE campaign_id=?");
    return $stmt->execute([$id]);
}

// INVENTORY MODULE FUNCTIONS
function get_all_inventory($pdo) {
    $sql = "
        SELECT i.*, p.name AS product_name
        FROM inventory i
        JOIN products p ON i.product_id = p.product_id
        ORDER BY p.name ASC
    ";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_inventory_by_id($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT i.*, p.name AS product_name
        FROM inventory i
        JOIN products p ON i.product_id = p.product_id
        WHERE i.inventory_id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function add_inventory_entry($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO inventory (product_id, stock_in, stock_out, current_stock, last_updated)
        VALUES (?, ?, ?, ?, NOW())
    ");
    return $stmt->execute([
        $data['product_id'],
        $data['stock_in'],
        $data['stock_out'],
        $data['current_stock']
    ]);
}

function update_inventory($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE inventory
        SET product_id=?, stock_in=?, stock_out=?, current_stock=?, last_updated=NOW()
        WHERE inventory_id=?
    ");
    return $stmt->execute([
        $data['product_id'],
        $data['stock_in'],
        $data['stock_out'],
        $data['current_stock'],
        $id
    ]);
}

function delete_inventory_entry($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE inventory_id=?");
    return $stmt->execute([$id]);
}

// Utility: get stock by product
function get_current_stock($pdo, $product_id) {
    $stmt = $pdo->prepare("SELECT current_stock FROM inventory WHERE product_id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetchColumn();
}

// Customers functions

// Get all customers
function get_all_customers(PDO $pdo) {
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get single customer by ID
function get_customer_by_id(PDO $pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

// Add customer
function add_customer(PDO $pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO customers (name, phone, email, region, source) 
        VALUES (:name, :phone, :email, :region, :source)
    ");
    return $stmt->execute([
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'region' => $data['region'],
        'source' => $data['source']
    ]);
}

// Update customer
function update_customer(PDO $pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE customers 
        SET name = :name, phone = :phone, email = :email, region = :region, source = :source
        WHERE customer_id = :id
    ");
    return $stmt->execute([
        'id' => $id,
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'region' => $data['region'],
        'source' => $data['source']
    ]);
}

// Delete customer
function delete_customer(PDO $pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = :id");
    return $stmt->execute(['id' => $id]);
}

// AUDITING
/**
 * Add a log entry
 */
function add_log(PDO $pdo, int $user_id, string $action, string $module, ?int $record_id = null, ?string $details = null) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, module, record_id, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $module, $record_id, $details]);
}

/**
 * Fetch logs (optionally filtered)
 */
function get_logs(PDO $pdo, array $filters = []) {
    $sql = "SELECT l.*, u.username 
            FROM logs l
            JOIN users u ON l.user_id = u.user_id
            WHERE 1";
    $params = [];

    if (!empty($filters['user_id'])) {
        $sql .= " AND l.user_id = :user_id";
        $params[':user_id'] = $filters['user_id'];
    }

    if (!empty($filters['module'])) {
        $sql .= " AND l.module = :module";
        $params[':module'] = $filters['module'];
    }

    if (!empty($filters['action'])) {
        $sql .= " AND l.action = :action";
        $params[':action'] = $filters['action'];
    }

    if (!empty($filters['date_from'])) {
        $sql .= " AND l.created_at >= :date_from";
        $params[':date_from'] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $sql .= " AND l.created_at <= :date_to";
        $params[':date_to'] = $filters['date_to'];
    }

    $sql .= " ORDER BY l.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function log_action(PDO $pdo, $user_id, $action, $module, $record_id = null, $details = null) {
    $stmt = $pdo->prepare("
        INSERT INTO logs (user_id, action, module, record_id, details)
        VALUES (:user_id, :action, :module, :record_id, :details)
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':action' => $action,
        ':module' => $module,
        ':record_id' => $record_id,
        ':details' => $details
    ]);
}   

// SETTINGS
function get_all_settings(PDO $pdo) {
    $stmt = $pdo->query("SELECT * FROM settings ORDER BY name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_setting_by_id(PDO $pdo, int $id) {
    $stmt = $pdo->prepare("SELECT * FROM settings WHERE setting_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function update_setting(PDO $pdo, int $id, string $value) {
    $stmt = $pdo->prepare("UPDATE settings SET value = ?, updated_at = NOW() WHERE setting_id = ?");
    return $stmt->execute([$value, $id]);
}
