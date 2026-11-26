-- Realistic awkward data on top of the app's own seeders: employees with
-- Nepali text and emoji, the DMT account (the only backup admin), another
-- HR account, binary data, a trigger, a procedure and a function.
SET NAMES utf8mb4;

INSERT INTO units (id, unit_name, organization_id) VALUES (1, 'DWIT', 1);
INSERT INTO departments (id, name, unit_id) VALUES (1, 'R&D', 1);
INSERT INTO designations (id, job_title_name, job_description) VALUES (1, 'Developer', 'Writes code');
INSERT INTO service_types (id, service_type_name) VALUES (1, 'Permanent');
INSERT INTO shifts (id, name) VALUES (1, 'Day');

SET SESSION cte_max_recursion_depth = 10000;
INSERT INTO employees (first_name, last_name, employee_id, date_of_birth, marital_status, gender, mobile,
    alter_email, country, permanent_address, permanent_district, permanent_municipality, permanent_ward_no,
    permanent_tole, temp_add_same_as_per_add, join_date, service_type, designation_id, organization_id,
    unit_id, email, shift_id, department_id)
  WITH RECURSIVE s(n) AS (SELECT 1 UNION ALL SELECT n + 1 FROM s WHERE n < 300)
  SELECT CASE WHEN n = 1 THEN 'DMT' WHEN n % 3 = 0 THEN CONCAT('राम बहादुर थापा 🎉 ', n)
              ELSE CONCAT('सीता कुमारी श्रेष्ठ — "quoted" \\ back\\slash ', n) END,
         'Developer', CONCAT('E', n), '1990-01-01', 'single', 'male', '9841000000',
         CONCAT('alt', n, '@example.com'), 'Nepal', 3, 28, 'Sifal', '12', 'Sifal', '1', '2011-11-18',
         1, 1, 1, 1, IF(n = 1, 'dmt@deerwalk.edu.np', CONCAT('e', n, '@example.com')), 1, 1
  FROM s;

-- password for all three: "password"
INSERT INTO users (employee_id, role_id, username, password) VALUES
  (1, 1, 'dmt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  (2, 1, 'hr2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
  (3, 3, 'emp3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

CREATE TABLE backup_test_blobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL,
  data LONGBLOB,
  touched_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE backup_test_audit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  blob_id INT,
  note VARCHAR(200) CHARACTER SET utf8mb4
) ENGINE=InnoDB;

DELIMITER //
CREATE TRIGGER backup_test_blobs_ai AFTER INSERT ON backup_test_blobs FOR EACH ROW
BEGIN
  INSERT INTO backup_test_audit (blob_id, note) VALUES (NEW.id, CONCAT('थपियो ✅ ', NEW.label));
END//
CREATE PROCEDURE backup_test_count_employees(OUT n INT)
BEGIN
  SELECT COUNT(*) INTO n FROM employees;
END//
CREATE FUNCTION backup_test_greeting(who VARCHAR(50)) RETURNS VARCHAR(100) CHARSET utf8mb4 DETERMINISTIC
BEGIN
  RETURN CONCAT('नमस्ते, ', who, ' 🙏');
END//
DELIMITER ;

INSERT INTO backup_test_blobs (label, data) VALUES
  ('all bytes', (WITH RECURSIVE s(n) AS (SELECT 0 UNION ALL SELECT n + 1 FROM s WHERE n < 255)
                 SELECT GROUP_CONCAT(CHAR(n USING binary) ORDER BY n SEPARATOR '') FROM s)),
  ('nul and quotes', CONCAT(X'00', '''', '"', X'0A0D1A5C', X'00')),
  ('random 1MiB', RANDOM_BYTES(1024)),
  ('empty', ''),
  ('null', NULL);
