-- ~1.6M rows (~170 MB of SQL): bigger than the 64M memory_limit used in the test.
SET SESSION cte_max_recursion_depth = 2000000;
CREATE TABLE backup_test_bulk (
  id INT AUTO_INCREMENT PRIMARY KEY,
  a CHAR(32) NOT NULL,
  b VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB;
INSERT INTO backup_test_bulk (a, b)
  WITH RECURSIVE s(n) AS (SELECT 1 UNION ALL SELECT n + 1 FROM s WHERE n < 1600000)
  SELECT MD5(RAND()), CONCAT('पङ्क्ति ', n, ' ', LEFT(SHA1(RAND()), 20)) FROM s;
