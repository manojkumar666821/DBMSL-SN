-- ============================================================================
-- PROJECT: Smart Neighborhood & Flat Society Portal (Complete 30 Tables)
-- ============================================================================

-- 1. Identity, Roles & Security
CREATE TABLE user_roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_verified TINYINT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES user_roles(role_id)
);

CREATE TABLE password_resets (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    otp_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    is_used TINYINT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_name VARCHAR(100) NOT NULL,
    target_table VARCHAR(50) NOT NULL,
    record_id INT NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NOT NULL,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE system_backups (
    backup_id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    backup_size_bytes BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Marketplace, Discovery & Ingress
CREATE TABLE flats (
    flat_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    building_block VARCHAR(10) NOT NULL,
    flat_number VARCHAR(20) NOT NULL,
    square_feet INT NOT NULL,
    listing_type VARCHAR(10) NOT NULL,
    rent_amount DECIMAL(12,2) NOT NULL,
    service_charge DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'AVAILABLE',
    version_id INT NOT NULL DEFAULT 1,
    FOREIGN KEY (owner_id) REFERENCES users(user_id)
);

CREATE TABLE flat_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    guest_id INT NOT NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING_OWNER',
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (guest_id) REFERENCES users(user_id)
);

CREATE TABLE flat_bids (
    bid_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    guest_id INT NOT NULL,
    bid_amount DECIMAL(12,2) NOT NULL,
    proposed_move_in DATE NOT NULL,
    bid_status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (guest_id) REFERENCES users(user_id)
);

CREATE TABLE viewing_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    guest_id INT NOT NULL,
    visit_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    party_size INT NOT NULL DEFAULT 1,
    gate_pass_otp VARCHAR(6) NOT NULL,
    booking_status VARCHAR(20) NOT NULL DEFAULT 'CONFIRMED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (guest_id) REFERENCES users(user_id)
);

CREATE TABLE inquiries (
    inquiry_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    guest_id INT NOT NULL,
    message_payload TEXT NOT NULL,
    owner_reply TEXT,
    is_read TINYINT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'OPEN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (guest_id) REFERENCES users(user_id)
);

CREATE TABLE resident_verifications (
    verification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    flat_id INT NOT NULL,
    document_path VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    reviewed_by INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (reviewed_by) REFERENCES users(user_id)
);

CREATE TABLE owner_verifications (
    owner_verification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    deed_document_path VARCHAR(255) NOT NULL,
    holding_tax_number VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    approved_by_committee INT NOT NULL,
    verified_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (approved_by_committee) REFERENCES users(user_id)
);

-- 3. Tenancy Lifecycle & Property Inspections
CREATE TABLE tenancy_agreements (
    agreement_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    tenant_id INT NOT NULL,
    owner_id INT NOT NULL,
    monthly_rent DECIMAL(12,2) NOT NULL,
    security_deposit DECIMAL(12,2) NOT NULL,
    lease_start DATE NOT NULL,
    lease_end DATE NOT NULL,
    agreement_status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (tenant_id) REFERENCES users(user_id),
    FOREIGN KEY (owner_id) REFERENCES users(user_id)
);

CREATE TABLE move_in_out_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    agreement_id INT NOT NULL,
    inspection_type VARCHAR(10) NOT NULL,
    inspection_date DATE NOT NULL,
    room_item_name VARCHAR(100) NOT NULL,
    condition_state VARCHAR(50) NOT NULL,
    evidence_photo_path VARCHAR(255),
    notes TEXT,
    FOREIGN KEY (agreement_id) REFERENCES tenancy_agreements(agreement_id)
);

CREATE TABLE deposit_refunds (
    refund_id INT AUTO_INCREMENT PRIMARY KEY,
    agreement_id INT NOT NULL,
    original_deposit DECIMAL(12,2) NOT NULL,
    damage_deductions DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    unpaid_bills_deductions DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    net_refund_amount DECIMAL(12,2) NOT NULL,
    refund_status VARCHAR(20) NOT NULL DEFAULT 'PENDING_AUDIT',
    processed_date DATETIME NOT NULL,
    FOREIGN KEY (agreement_id) REFERENCES tenancy_agreements(agreement_id)
);

-- 4. Financial Split-Ledger & Utility Tracking
CREATE TABLE utility_tariffs (
    tariff_id INT AUTO_INCREMENT PRIMARY KEY,
    utility_type VARCHAR(30) NOT NULL,
    rate_per_unit DECIMAL(10,2) NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NOT NULL
);

CREATE TABLE utility_readings (
    reading_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    utility_type VARCHAR(30) NOT NULL,
    previous_reading DECIMAL(10,2) NOT NULL,
    current_reading DECIMAL(10,2) NOT NULL,
    consumption DECIMAL(10,2) NOT NULL,
    meter_photo_path VARCHAR(255),
    recorded_by_staff INT NOT NULL,
    reading_date DATE NOT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (recorded_by_staff) REFERENCES users(user_id)
);

CREATE TABLE invoices (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    tenant_id INT NOT NULL,
    billing_month VARCHAR(20) NOT NULL,
    rent_portion DECIMAL(10,2) NOT NULL,
    society_portion DECIMAL(10,2) NOT NULL,
    utility_portion DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    due_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'UNPAID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (tenant_id) REFERENCES users(user_id)
);

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    transaction_reference VARCHAR(100) NOT NULL UNIQUE,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id)
);

-- 5. Treasury Disbursements & Committee Governance
CREATE TABLE society_funds (
    fund_id INT AUTO_INCREMENT PRIMARY KEY,
    total_balance DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    reserve_sinking_balance DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE expense_ledgers (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(150) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    invoice_receipt_path VARCHAR(255) NOT NULL,
    created_by_treasurer INT NOT NULL,
    approved_by_president INT NOT NULL,
    status VARCHAR(25) NOT NULL DEFAULT 'PENDING_APPROVAL',
    approval_timestamp DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_treasurer) REFERENCES users(user_id),
    FOREIGN KEY (approved_by_president) REFERENCES users(user_id)
);

CREATE TABLE vendor_quotes (
    quote_id INT AUTO_INCREMENT PRIMARY KEY,
    expense_id INT NOT NULL,
    vendor_name VARCHAR(100) NOT NULL,
    itemized_proposal TEXT NOT NULL,
    estimated_cost DECIMAL(12,2) NOT NULL,
    quote_file_path VARCHAR(255),
    is_selected TINYINT NOT NULL DEFAULT 0,
    FOREIGN KEY (expense_id) REFERENCES expense_ledgers(expense_id)
);

CREATE TABLE committee_resolutions (
    resolution_id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_title VARCHAR(150) NOT NULL,
    meeting_date DATE NOT NULL,
    agenda_summary TEXT NOT NULL,
    decision_log TEXT NOT NULL,
    recorded_by_secretary INT NOT NULL,
    is_locked TINYINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recorded_by_secretary) REFERENCES users(user_id)
);

-- 6. Amenities, Parking & Security Gate Logistics
CREATE TABLE society_amenities (
    amenity_id INT AUTO_INCREMENT PRIMARY KEY,
    amenity_name VARCHAR(100) NOT NULL,
    hourly_rate DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    opening_time TIME NOT NULL,
    closing_time TIME NOT NULL,
    is_active TINYINT NOT NULL DEFAULT 1
);

CREATE TABLE amenity_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    amenity_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    lock_expires_at DATETIME NOT NULL,
    booking_status VARCHAR(20) NOT NULL DEFAULT 'LOCKED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (amenity_id) REFERENCES society_amenities(amenity_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE gate_visitors (
    visitor_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    visitor_category VARCHAR(30) NOT NULL,
    pass_otp VARCHAR(6) NOT NULL,
    valid_until DATETIME NOT NULL,
    is_used TINYINT NOT NULL DEFAULT 0,
    entry_time DATETIME NOT NULL,
    exit_time DATETIME NOT NULL,
    verified_by_guard INT NOT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (verified_by_guard) REFERENCES users(user_id)
);

CREATE TABLE vehicle_registry (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    owner_id INT NOT NULL,
    license_plate VARCHAR(30) NOT NULL UNIQUE,
    vehicle_type VARCHAR(20) NOT NULL,
    qr_sticker_token VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (owner_id) REFERENCES users(user_id)
);

CREATE TABLE parking_assignments (
    slot_id INT AUTO_INCREMENT PRIMARY KEY,
    slot_code VARCHAR(20) NOT NULL UNIQUE,
    assigned_flat_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    FOREIGN KEY (assigned_flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicle_registry(vehicle_id)
);

CREATE TABLE package_deliveries (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    courier_company VARCHAR(50) NOT NULL,
    tracking_or_pin VARCHAR(50),
    arrival_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    collected_time DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'WAITING_PICKUP',
    logged_by_guard INT NOT NULL,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (logged_by_guard) REFERENCES users(user_id)
);

-- 7. Maintenance Desk, Work Orders & Circulars
CREATE TABLE maintenance_complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    flat_id INT NOT NULL,
    submitted_by INT NOT NULL,
    scope VARCHAR(20) NOT NULL,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    photo_path VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (submitted_by) REFERENCES users(user_id)
);

CREATE TABLE assigned_tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT NOT NULL,
    assigned_staff_id INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ASSIGNED',
    start_time DATETIME NOT NULL,
    resolved_at DATETIME NOT NULL,
    FOREIGN KEY (complaint_id) REFERENCES maintenance_complaints(complaint_id),
    FOREIGN KEY (assigned_staff_id) REFERENCES users(user_id)
);

CREATE TABLE task_photos (
    photo_id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT NOT NULL,
    stage VARCHAR(10) NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES assigned_tasks(task_id)
);

CREATE TABLE staff_attendance (
    attendance_id INT PRIMARY KEY AUTO_INCREMENT,
    staff_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    clock_in DATETIME NOT NULL,
    clock_out DATETIME NOT NULL,
    shift_name VARCHAR(20) NOT NULL,
    FOREIGN KEY (staff_id) REFERENCES users(user_id)
);

CREATE TABLE emergency_alerts (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    flat_id INT NOT NULL,
    triggered_by INT NOT NULL,
    alert_type VARCHAR(30) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    cleared_by_guard INT NOT NULL,
    triggered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flat_id) REFERENCES flats(flat_id),
    FOREIGN KEY (triggered_by) REFERENCES users(user_id),
    FOREIGN KEY (cleared_by_guard) REFERENCES users(user_id)
);

CREATE TABLE notices (
    notice_id INT PRIMARY KEY AUTO_INCREMENT,
    target_role VARCHAR(30) NOT NULL,
    building_block VARCHAR(10) NOT NULL DEFAULT 'ALL',
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    published_by INT NOT NULL,
    expiry_date DATE NOT NULL,
    is_public TINYINT NOT NULL DEFAULT 0,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (published_by) REFERENCES users(user_id)
);