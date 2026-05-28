-- ============================================================
-- Carry Over Leave update for ALL units - Year 2082 (BS)
-- Read by app in BS year 2083
-- Total: 139 employees (DSS=74, DDS=30, DWG=7, DPS=12, DWIT=16)
-- Safe to run multiple times (ON DUPLICATE KEY UPDATE)
-- Run AFTER: php artisan migrate
-- ============================================================

INSERT INTO `carry_over_leaves` (`employee_id`, `year`, `days`, `personal_days`, `sick_days`, `created_at`, `updated_at`) VALUES

  -- ======================== DSS (74 employees) ========================
  (9,   2082, 15.5, 12.5, 3,    NOW(), NOW()), -- Usha Adhikari
  (42,  2082, 44,   32,   12,   NOW(), NOW()), -- Ujjwal Poudel
  (57,  2082, 48.5, 36.5, 12,   NOW(), NOW()), -- Madhu Bhusal
  (92,  2082, 21.5, 9.5,  12,   NOW(), NOW()), -- Ruby Labh
  (125, 2082, 34.5, 22.5, 12,   NOW(), NOW()), -- Laxman Jari
  (134, 2082, 14,   9,    5,    NOW(), NOW()), -- Sapana Lama
  (135, 2082, 18,   10,   8,    NOW(), NOW()), -- Youbraj Aryal
  (153, 2082, 35.5, 27,   8.5,  NOW(), NOW()), -- Alisha Shakya
  (198, 2082, 34.5, 23.5, 11,   NOW(), NOW()), -- Sanjay Sunar
  (216, 2082, 44,   34,   10,   NOW(), NOW()), -- Sudip Giri
  (219, 2082, 53,   42,   11,   NOW(), NOW()), -- Man Tamang
  (221, 2082, 37.5, 27,   10.5, NOW(), NOW()), -- Susan Tamrakar
  (222, 2082, 39,   29,   10,   NOW(), NOW()), -- Shanta Tamang
  (226, 2082, 21.5, 15,   6.5,  NOW(), NOW()), -- Binda Karki
  (227, 2082, 19.5, 13.5, 6,    NOW(), NOW()), -- Sitaram Mahat
  (228, 2082, 50,   41,   9,    NOW(), NOW()), -- Sujata Tamang
  (231, 2082, 30.5, 23.5, 7,    NOW(), NOW()), -- Mahendra Karki
  (232, 2082, 31,   20,   11,   NOW(), NOW()), -- Amrita Rai
  (236, 2082, 48.5, 36.5, 12,   NOW(), NOW()), -- Bimala Gautam
  (238, 2082, 32,   20.5, 11.5, NOW(), NOW()), -- Sumitra Bhattarai
  (239, 2082, 32,   20,   12,   NOW(), NOW()), -- Tulasa Karki
  (240, 2082, 45.5, 34,   11.5, NOW(), NOW()), -- Yashoda Sapkota
  (247, 2082, 46.5, 35.5, 11,   NOW(), NOW()), -- Bishnu Lamichhane
  (248, 2082, 54,   43,   11,   NOW(), NOW()), -- Pramod Lamichhane
  (250, 2082, 28.5, 18,   10.5, NOW(), NOW()), -- Ankita Pokharel
  (251, 2082, 40.5, 31.5, 9,    NOW(), NOW()), -- Mazina Rajopadhayaya
  (261, 2082, 24,   12,   12,   NOW(), NOW()), -- Bishnu Pandey
  (266, 2082, 25.5, 16.5, 9,    NOW(), NOW()), -- Urmila Basnet
  (275, 2082, 35.5, 23.5, 12,   NOW(), NOW()), -- Aamir Bakey
  (302, 2082, 22.5, 13,   9.5,  NOW(), NOW()), -- Om Lama
  (303, 2082, 0.5,  0,    0.5,  NOW(), NOW()), -- Subash Sadashankar
  (304, 2082, 23.5, 13,   10.5, NOW(), NOW()), -- Rajan Thapa
  (323, 2082, 19.5, 14.5, 5,    NOW(), NOW()), -- Sushil Sunar
  (327, 2082, 17,   9,    8,    NOW(), NOW()), -- Parbati Acharya
  (97,  2082, 12,   0,    12,   NOW(), NOW()), -- Hama Rajbhandari
  (101, 2082, 5,    0,    5,    NOW(), NOW()), -- Nemjala Bajracharya
  (105, 2082, 6,    0,    6,    NOW(), NOW()), -- Kuldeep Gupta
  (107, 2082, 10.5, 0,    10.5, NOW(), NOW()), -- Hangkeng Rai
  (111, 2082, 15,   4,    11,   NOW(), NOW()), -- Bindu Paudel
  (117, 2082, 0,    0,    0,    NOW(), NOW()), -- Yukta Burma
  (160, 2082, 0,    0,    0,    NOW(), NOW()), -- Hari Subedi
  (175, 2082, 9.5,  0,    9.5,  NOW(), NOW()), -- Chanda Pandey
  (177, 2082, 5.5,  0.5,  5,    NOW(), NOW()), -- Manisha Adhikari
  (188, 2082, 9.5,  0,    9.5,  NOW(), NOW()), -- Saroj Rai
  (211, 2082, 9.5,  0,    9.5,  NOW(), NOW()), -- Dhanshwor Yonghang
  (253, 2082, 20,   8,    12,   NOW(), NOW()), -- Sabita Gyawali
  (254, 2082, 3,    0,    3,    NOW(), NOW()), -- Sushila Kafle
  (255, 2082, 12,   2.5,  9.5,  NOW(), NOW()), -- Sindhu Poudel
  (267, 2082, 12,   1.5,  10.5, NOW(), NOW()), -- Bidhya Karki
  (269, 2082, 6,    0,    6,    NOW(), NOW()), -- Astha Dahal
  (273, 2082, 6,    0,    6,    NOW(), NOW()), -- Ishan Kewrat
  (278, 2082, 7.5,  0.5,  7,    NOW(), NOW()), -- Domankala Limbu
  (281, 2082, 4,    0,    4,    NOW(), NOW()), -- Himal Parajuli
  (295, 2082, 8.5,  1,    7.5,  NOW(), NOW()), -- Durga Puri
  (324, 2082, 3,    0,    3,    NOW(), NOW()), -- Anuja Lamichhane
  (330, 2082, 0,    0,    0,    NOW(), NOW()), -- Sanskriti Rimal
  (332, 2082, 7.5,  0,    7.5,  NOW(), NOW()), -- Benish Javed Sonar
  (350, 2082, 9,    0,    9,    NOW(), NOW()), -- Ankur Upadhyay
  (351, 2082, 0,    0,    0,    NOW(), NOW()), -- Ashika Shakya
  (355, 2082, 0,    0,    0,    NOW(), NOW()), -- Suman Pandey
  (361, 2082, 4.5,  0,    4.5,  NOW(), NOW()), -- Shubham Bhattarai
  (368, 2082, 0.5,  0,    0.5,  NOW(), NOW()), -- Anju Kafle
  (391, 2082, 0,    0,    0,    NOW(), NOW()), -- Rahul Manandhar
  (396, 2082, 0.5,  0,    0.5,  NOW(), NOW()), -- Laxmi Tiwari
  (398, 2082, 0,    0,    0,    NOW(), NOW()), -- Mukunda Maharjan
  (87,  2082, 34,   23,   11,   NOW(), NOW()), -- Sagina Maharjan
  (104, 2082, 5,    2,    3,    NOW(), NOW()), -- Sita Khadka
  (233, 2082, 4.5,  1,    3.5,  NOW(), NOW()), -- Dirga Thapa
  (272, 2082, 13,   13,   0,    NOW(), NOW()), -- Bishnu Thapa magar
  (301, 2082, 12,   10,   2,    NOW(), NOW()), -- Bikram Tamang
  (362, 2082, 10.5, 5,    5.5,  NOW(), NOW()), -- Smarika Bhattarai
  (426, 2082, 8.5,  5,    3.5,  NOW(), NOW()), -- Sudikshya Dahal
  (429, 2082, 10.5, 5.5,  5,    NOW(), NOW()), -- Pratima Koirala
  (442, 2082, 1,    0,    1,    NOW(), NOW()), -- Pin Sapkota

  -- ======================== DDS (30 employees) ========================
  (102, 2082, 0,    0,    0,    NOW(), NOW()), -- Santosh Sahu
  (181, 2082, 0,    0,    0,    NOW(), NOW()), -- Bishnu Parajuli
  (283, 2082, 17.5, 6.5,  11,   NOW(), NOW()), -- Khemraj Muktan
  (284, 2082, 8.5,  0,    8.5,  NOW(), NOW()), -- Pramila Adhikari
  (287, 2082, 4.5,  0,    4.5,  NOW(), NOW()), -- Sachin Yadav
  (289, 2082, 10,   0,    10,   NOW(), NOW()), -- Rupa Adhikari
  (290, 2082, 5.5,  0,    5.5,  NOW(), NOW()), -- Sharada Karki
  (291, 2082, 3.5,  0,    3.5,  NOW(), NOW()), -- Nita Karki
  (292, 2082, 0,    0,    0,    NOW(), NOW()), -- Stuti Pandey
  (293, 2082, 6.5,  0.5,  6,    NOW(), NOW()), -- Juna Karki Pandey
  (296, 2082, 8.5,  5.5,  3,    NOW(), NOW()), -- Dibyadarsi Nepal
  (297, 2082, 0,    0,    0,    NOW(), NOW()), -- Bijaya Bharti
  (300, 2082, 10.5, 1.5,  9,    NOW(), NOW()), -- Prakash Singh
  (322, 2082, 0,    0,    0,    NOW(), NOW()), -- Prakash Adhikari
  (400, 2082, 4.5,  0,    4.5,  NOW(), NOW()), -- Kuresh Pandey
  (30,  2082, 20,   10,   10,   NOW(), NOW()), -- Nirmal Poudel
  (157, 2082, 20,   9.5,  10.5, NOW(), NOW()), -- Reshika Joshi
  (218, 2082, 34,   30,   4,    NOW(), NOW()), -- Durga Karki
  (220, 2082, 24,   24,   0,    NOW(), NOW()), -- Santa Tamang
  (241, 2082, 33,   28,   5,    NOW(), NOW()), -- Maya Tamang
  (260, 2082, 21.5, 11,   10.5, NOW(), NOW()), -- Rohit Ghimire
  (270, 2082, 27,   22.5, 4.5,  NOW(), NOW()), -- Eak Thapa
  (294, 2082, 10,   5,    5,    NOW(), NOW()), -- Sarladh Kunwar
  (299, 2082, 11.5, 11,   0.5,  NOW(), NOW()), -- Sarita Pandey
  (305, 2082, 1,    0.5,  0.5,  NOW(), NOW()), -- Ram Koirala
  (306, 2082, 21.5, 15.5, 6,    NOW(), NOW()), -- Radhika Baruwal
  (307, 2082, 7,    0,    7,    NOW(), NOW()), -- Laxmi Bhandari
  (308, 2082, 9.5,  5,    4.5,  NOW(), NOW()), -- Shanti Bohara
  (318, 2082, 26.5, 14.5, 12,   NOW(), NOW()), -- Rojita Bhandari
  (435, 2082, 6,    1.5,  4.5,  NOW(), NOW()), -- Anisha Sapkota

  -- ======================== DWG / Deerwalk Compware (7 employees) ========================
  (3,   2082, 38,   27.5, 10.5, NOW(), NOW()), -- Hitesh Karki
  (58,  2082, 40,   38,   2,    NOW(), NOW()), -- Hariram Khadka
  (201, 2082, 21,   9,    12,   NOW(), NOW()), -- Alisha Thapa Magar
  (271, 2082, 22,   16.5, 5.5,  NOW(), NOW()), -- Pooja Neupane
  (7,   2082, 43,   32.5, 10.5, NOW(), NOW()), -- Pravin Thapaliya
  (277, 2082, 2,    1,    1,    NOW(), NOW()), -- Preeti Pradhan
  (321, 2082, 9.5,  2.5,  7,    NOW(), NOW()), -- Pralhad Dhungana

  -- ======================== DPS (12 employees) ========================
  (333, 2082, 0,    0,    0,    NOW(), NOW()), -- Asmita Gautam
  (335, 2082, 11,   0,    11,   NOW(), NOW()), -- Kumari Khatri
  (337, 2082, 6,    0,    6,    NOW(), NOW()), -- Aashma Timilsina
  (338, 2082, 9.5,  0,    9.5,  NOW(), NOW()), -- Laxmi Acharya Timalsina
  (339, 2082, 6,    0,    6,    NOW(), NOW()), -- Srijana B. K.
  (340, 2082, 1,    0,    1,    NOW(), NOW()), -- Sunita Maharjan Sanjhya
  (354, 2082, 4,    0,    4,    NOW(), NOW()), -- Kiran B.K
  (356, 2082, 10,   0,    10,   NOW(), NOW()), -- Kalpana Chapai
  (90,  2082, 33.5, 22.5, 11,   NOW(), NOW()), -- Tej Kafle
  (224, 2082, 3.5,  3.5,  0,    NOW(), NOW()), -- Aakash Giri
  (341, 2082, 11,   3,    8,    NOW(), NOW()), -- Srijana Manandhar
  (348, 2082, 13,   5,    8,    NOW(), NOW()), -- Manisha Paudel

  -- ======================== DWIT (16 employees) ========================
  (242, 2082, 16,   11,   5,    NOW(), NOW()), -- Santosh Khadka
  (372, 2082, 0.5,  0,    0.5,  NOW(), NOW()), -- Aakancha Thapa
  (374, 2082, 4,    3.5,  0.5,  NOW(), NOW()), -- Bir Gubaju
  (375, 2082, 7.5,  3.5,  4,    NOW(), NOW()), -- Sofia Tamang
  (376, 2082, 18,   9,    9,    NOW(), NOW()), -- Sarita Tamang
  (379, 2082, 16.5, 8,    8.5,  NOW(), NOW()), -- Shyam Khatiwada
  (380, 2082, 13.5, 4.5,  9,    NOW(), NOW()), -- Shristi Awale
  (381, 2082, 19,   10,   9,    NOW(), NOW()), -- Saurav Gautam
  (382, 2082, 2,    2,    0,    NOW(), NOW()), -- Sarshij Adhikari
  (383, 2082, 9,    0,    9,    NOW(), NOW()), -- Kumar Lamichhane
  (384, 2082, 11.5, 7,    4.5,  NOW(), NOW()), -- Kiran Parajuli
  (386, 2082, 14,   9,    5,    NOW(), NOW()), -- Madan Subedi
  (388, 2082, 6.5,  2,    4.5,  NOW(), NOW()), -- Rojina Dahal
  (389, 2082, 1,    0.5,  0.5,  NOW(), NOW()), -- Upama Pandey
  (390, 2082, 0,    0,    0,    NOW(), NOW()), -- Samjhana Pokhrel
  (433, 2082, 6.5,  3.5,  3,    NOW(), NOW())  -- Milan Lamichhane

ON DUPLICATE KEY UPDATE
  `days`          = VALUES(`days`),
  `personal_days` = VALUES(`personal_days`),
  `sick_days`     = VALUES(`sick_days`),
  `updated_at`    = NOW();

-- ============================================================
-- Summary: 139 employees total
--   DSS  : 74
--   DDS  : 30
--   DWG  :  7
--   DPS  : 12
--   DWIT : 16
-- ============================================================
