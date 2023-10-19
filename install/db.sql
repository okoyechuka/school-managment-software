--
-- Drop Tables
--
DROP TABLE IF EXISTS `chats`, `cbt_choices`, `cbt_answers`, `cbt_questions`, `cbt`, `students_course`, `class_course`, `e_courses_contents`, `e_courses`, `album`, `applicants`, `assignments`, `backups`, `books`, `book_categories`, `book_issues`, `classes`, `countries`, `currency`, `custom_fields`, `custom_values`, `emails`, `exams`, `exam_note`, `exam_student_score`, `fees`, `fee_paid`, `gallery`, `gateways`, `grades`, `guardians`, `hostels`, `invoices`, `library`, `messagedetails`, `messages`, `notice`, `notice_read`, `parents`, `paymentalerts`, `paymentgateways`, `paymentgateway_templates`, `pin`, `purchase`, `salary_pay`, `sales`, `schedules`, `schools`, `sentmessages`, `sessions`, `settings`, `sms_gateways`, `staffs`, `stock`, `stock_category`, `students`, `student_assignments`, `student_attendance`, `student_class`, `student_guardian`, `student_hostel`, `student_parent`, `student_status`, `subject`, `teachers`, `teacher_class`, `teacher_subject`, `terms`, `themes`, `timetable`, `transactions`, `transaction_status`, `users`, `user_roles`,`vehicles`,`student_bus`;

--
-- Table structure for table `album`
--

CREATE TABLE `album` (
  `id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `room` varchar(300) NOT NULL,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `message` varchar(1000) NOT NULL,
  `datetime` varchar(44) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `chats`  ADD PRIMARY KEY (`id`);
ALTER TABLE `chats`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `other_name` varchar(100) NOT NULL,
  `sex` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `local_council` varchar(150) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(110) NOT NULL,
  `nationality` varchar(110) NOT NULL,
  `state_origin` varchar(200) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `session_id` int(20) NOT NULL,
  `status` varchar(101) NOT NULL DEFAULT 'Pending',
  `application_date` date NOT NULL,
  `application_type` varchar(200) NOT NULL,
  `school_name` varchar(200) NOT NULL,
  `school_address` varchar(200) NOT NULL,
  `current_class` varchar(200) NOT NULL,
  `serial` varchar(20) NOT NULL,
  `application_number` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `close_date` date NOT NULL,
  `title` varchar(300) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `type` varchar(200) NOT NULL,
  `file` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `serial` varchar(200) NOT NULL,
  `author` varchar(200) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(400) NOT NULL,
  `location` varchar(200) NOT NULL,
  `catalog` int(11) NOT NULL,
  `isbn` varchar(100) NOT NULL,
  `publisher` varchar(200) NOT NULL,
  `sub_title` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `book_issues`
--

CREATE TABLE `book_issues` (
  `id` int(11) NOT NULL,
  `date_issued` date NOT NULL,
  `date_returned` date NOT NULL,
  `book_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `school_id` int(11) NOT NULL,
  `date_due` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table stores information about all classes';

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `flag` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `title`, `code`, `flag`) VALUES
(1, 'Bulgaria', 'BG', ''),
(2, 'Europe', 'EU', ''),
(3, 'Andorra', 'AD', ''),
(4, 'United Arab Emirates', 'AE', ''),
(5, 'Afghanistan', 'AF', ''),
(6, 'Antigua and Barbuda', 'AG', ''),
(7, 'Anguilla', 'AI', ''),
(8, 'Albania', 'AL', ''),
(9, 'Armenia', 'AM', ''),
(10, 'Netherlands Antilles', 'AN', ''),
(11, 'Angola', 'AO', ''),
(12, 'Asia/Pacific Region', 'AP', ''),
(13, 'Antarctica', 'AQ', ''),
(14, 'Argentina', 'AR', ''),
(15, 'American Samoa', 'AS', ''),
(16, 'Austria', 'AT', ''),
(17, 'Australia', 'AU', ''),
(18, 'Aruba', 'AW', ''),
(19, 'Aland Islands', 'AX', ''),
(20, 'Azerbaijan', 'AZ', ''),
(21, 'Bosnia and Herzegovina', 'BA', ''),
(22, 'Barbados', 'BB', ''),
(23, 'Bangladesh', 'BD', ''),
(24, 'Belgium', 'BE', ''),
(25, 'Burkina Faso', 'BF', ''),
(26, 'Bahrain', 'BH', ''),
(27, 'Burundi', 'BI', ''),
(28, 'Benin', 'BJ', ''),
(29, 'Bermuda', 'BM', ''),
(30, 'Brunei Darussalam', 'BN', ''),
(31, 'Bolivia', 'BO', ''),
(32, 'Brazil', 'BR', ''),
(33, 'Bahamas', 'BS', ''),
(34, 'Bhutan', 'BT', ''),
(35, 'Bouvet Island', 'BV', ''),
(36, 'Botswana', 'BW', ''),
(37, 'Belarus', 'BY', ''),
(38, 'Belize', 'BZ', ''),
(39, 'Canada', 'CA', ''),
(40, 'Cocos (Keeling) Islands', 'CC', ''),
(41, 'Congo, The Democratic Republic of the', 'CD', ''),
(42, 'Central African Republic', 'CF', ''),
(43, 'Congo', 'CG', ''),
(44, 'Switzerland', 'CH', ''),
(45, 'Cote d&quot;Ivoire', 'CI', ''),
(46, 'Cook Islands', 'CK', ''),
(47, 'Chile', 'CL', ''),
(48, 'Cameroon', 'CM', ''),
(49, 'China', 'CN', ''),
(50, 'Colombia', 'CO', ''),
(51, 'Costa Rica', 'CR', ''),
(52, 'Cuba', 'CU', ''),
(53, 'Cape Verde', 'CV', ''),
(54, 'Christmas Island', 'CX', ''),
(55, 'Cyprus', 'CY', ''),
(56, 'Czech Republic', 'CZ', ''),
(57, 'Germany', 'DE', ''),
(58, 'Djibouti', 'DJ', ''),
(59, 'Denmark', 'DK', ''),
(60, 'Dominica', 'DM', ''),
(61, 'Dominican Republic', 'DO', ''),
(62, 'Algeria', 'DZ', ''),
(63, 'Ecuador', 'EC', ''),
(64, 'Estonia', 'EE', ''),
(65, 'Egypt', 'EG', ''),
(66, 'Western Sahara', 'EH', ''),
(67, 'Eritrea', 'ER', ''),
(68, 'Spain', 'ES', ''),
(69, 'Ethiopia', 'ET', ''),
(70, 'Finland', 'FI', ''),
(71, 'Fiji', 'FJ', ''),
(72, 'Falkland Islands (Malvinas)', 'FK', ''),
(73, 'Micronesia, Federated States of', 'FM', ''),
(74, 'Faroe Islands', 'FO', ''),
(75, 'France', 'FR', ''),
(76, 'Gabon', 'GA', ''),
(77, 'United Kingdom', 'GB', ''),
(78, 'Grenada', 'GD', ''),
(79, 'Georgia', 'GE', ''),
(80, 'French Guiana', 'GF', ''),
(81, 'Guernsey', 'GG', ''),
(82, 'Ghana', 'GH', ''),
(83, 'Gibraltar', 'GI', ''),
(84, 'Greenland', 'GL', ''),
(85, 'Gambia', 'GM', ''),
(86, 'Guinea', 'GN', ''),
(87, 'Guadeloupe', 'GP', ''),
(88, 'Equatorial Guinea', 'GQ', ''),
(89, 'Greece', 'GR', ''),
(90, 'South Georgia and the South Sandwich Islands', 'GS', ''),
(91, 'Guatemala', 'GT', ''),
(92, 'Guam', 'GU', ''),
(93, 'Guinea-Bissau', 'GW', ''),
(94, 'Guyana', 'GY', ''),
(95, 'Hong Kong', 'HK', ''),
(96, 'Heard Island and McDonald Islands', 'HM', ''),
(97, 'Honduras', 'HN', ''),
(98, 'Croatia', 'HR', ''),
(99, 'Haiti', 'HT', ''),
(100, 'Hungary', 'HU', ''),
(101, 'Indonesia', 'ID', ''),
(102, 'Ireland', 'IE', ''),
(103, 'Israel', 'IL', ''),
(104, 'Isle of Man', 'IM', ''),
(105, 'India', 'IN', ''),
(106, 'British Indian Ocean Territory', 'IO', ''),
(107, 'Iraq', 'IQ', ''),
(108, 'Iran, Islamic Republic of', 'IR', ''),
(109, 'Iceland', 'IS', ''),
(110, 'Italy', 'IT', ''),
(111, 'Jersey', 'JE', ''),
(112, 'Jamaica', 'JM', ''),
(113, 'Jordan', 'JO', ''),
(114, 'Japan', 'JP', ''),
(115, 'Kenya', 'KE', ''),
(116, 'Kyrgyzstan', 'KG', ''),
(117, 'Cambodia', 'KH', ''),
(118, 'Kiribati', 'KI', ''),
(119, 'Comoros', 'KM', ''),
(120, 'Saint Kitts and Nevis', 'KN', ''),
(121, 'Korea, Democratic People&quot;s Republic of', 'KP', ''),
(122, 'Korea, Republic of', 'KR', ''),
(123, 'Kuwait', 'KW', ''),
(124, 'Cayman Islands', 'KY', ''),
(125, 'Kazakhstan', 'KZ', ''),
(126, 'Lao People&quot;s Democratic Republic', 'LA', ''),
(127, 'Lebanon', 'LB', ''),
(128, 'Saint Lucia', 'LC', ''),
(129, 'Liechtenstein', 'LI', ''),
(130, 'Sri Lanka', 'LK', ''),
(131, 'Liberia', 'LR', ''),
(132, 'Lesotho', 'LS', ''),
(133, 'Lithuania', 'LT', ''),
(134, 'Luxembourg', 'LU', ''),
(135, 'Latvia', 'LV', ''),
(136, 'Libyan Arab Jamahiriya', 'LY', ''),
(137, 'Morocco', 'MA', ''),
(138, 'Monaco', 'MC', ''),
(139, 'Moldova, Republic of', 'MD', ''),
(140, 'Montenegro', 'ME', ''),
(141, 'Madagascar', 'MG', ''),
(142, 'Marshall Islands', 'MH', ''),
(143, 'Macedonia', 'MK', ''),
(144, 'Mali', 'ML', ''),
(145, 'Myanmar', 'MM', ''),
(146, 'Mongolia', 'MN', ''),
(147, 'Macao', 'MO', ''),
(148, 'Northern Mariana Islands', 'MP', ''),
(149, 'Martinique', 'MQ', ''),
(150, 'Mauritania', 'MR', ''),
(151, 'Montserrat', 'MS', ''),
(152, 'Malta', 'MT', ''),
(153, 'Mauritius', 'MU', ''),
(154, 'Maldives', 'MV', ''),
(155, 'Malawi', 'MW', ''),
(156, 'Mexico', 'MX', ''),
(157, 'Malaysia', 'MY', ''),
(158, 'Mozambique', 'MZ', ''),
(159, 'Namibia', 'NA', ''),
(160, 'New Caledonia', 'NC', ''),
(161, 'Niger', 'NE', ''),
(162, 'Norfolk Island', 'NF', ''),
(163, 'Nigeria', 'NG', ''),
(164, 'Nicaragua', 'NI', ''),
(165, 'Netherlands', 'NL', ''),
(166, 'Norway', 'NO', ''),
(167, 'Nepal', 'NP', ''),
(168, 'Nauru', 'NR', ''),
(169, 'Niue', 'NU', ''),
(170, 'New Zealand', 'NZ', ''),
(171, 'Oman', 'OM', ''),
(172, 'Panama', 'PA', ''),
(173, 'Peru', 'PE', ''),
(174, 'French Polynesia', 'PF', ''),
(175, 'Papua New Guinea', 'PG', ''),
(176, 'Philippines', 'PH', ''),
(177, 'Pakistan', 'PK', ''),
(178, 'Poland', 'PL', ''),
(179, 'Saint Pierre and Miquelon', 'PM', ''),
(180, 'Pitcairn', 'PN', ''),
(181, 'Puerto Rico', 'PR', ''),
(182, 'Palestinian Territory', 'PS', ''),
(183, 'Portugal', 'PT', ''),
(184, 'Palau', 'PW', ''),
(185, 'Paraguay', 'PY', ''),
(186, 'Qatar', 'QA', ''),
(187, 'Reunion', 'RE', ''),
(188, 'Romania', 'RO', ''),
(189, 'Serbia', 'RS', ''),
(190, 'Russian Federation', 'RU', ''),
(191, 'Rwanda', 'RW', ''),
(192, 'Saudi Arabia', 'SA', ''),
(193, 'Solomon Islands', 'SB', ''),
(194, 'Seychelles', 'SC', ''),
(195, 'Sudan', 'SD', ''),
(196, 'Sweden', 'SE', ''),
(197, 'Singapore', 'SG', ''),
(198, 'Saint Helena', 'SH', ''),
(199, 'Slovenia', 'SI', ''),
(200, 'Svalbard and Jan Mayen', 'SJ', ''),
(201, 'Slovakia', 'SK', ''),
(202, 'Sierra Leone', 'SL', ''),
(203, 'San Marino', 'SM', ''),
(204, 'Senegal', 'SN', ''),
(205, 'Somalia', 'SO', ''),
(206, 'Suriname', 'SR', ''),
(207, 'Sao Tome and Principe', 'ST', ''),
(208, 'El Salvador', 'SV', ''),
(209, 'Syrian Arab Republic', 'SY', ''),
(210, 'Swaziland', 'SZ', ''),
(211, 'Turks and Caicos Islands', 'TC', ''),
(212, 'Chad', 'TD', ''),
(213, 'French Southern Territories', 'TF', ''),
(214, 'Togo', 'TG', ''),
(215, 'Thailand', 'TH', ''),
(216, 'Tajikistan', 'TJ', ''),
(217, 'Tokelau', 'TK', ''),
(218, 'Timor-Leste', 'TL', ''),
(219, 'Turkmenistan', 'TM', ''),
(220, 'Tunisia', 'TN', ''),
(221, 'Tonga', 'TO', ''),
(222, 'Turkey', 'TR', ''),
(223, 'Trinidad and Tobago', 'TT', ''),
(224, 'Tuvalu', 'TV', ''),
(225, 'Taiwan', 'TW', ''),
(226, 'Tanzania, United Republic of', 'TZ', ''),
(227, 'Ukraine', 'UA', ''),
(228, 'Uganda', 'UG', ''),
(229, 'United States Minor Outlying Islands', 'UM', ''),
(230, 'United States', 'US', ''),
(231, 'Uruguay', 'UY', ''),
(232, 'Uzbekistan', 'UZ', ''),
(233, 'Holy See (Vatican City State)', 'VA', ''),
(234, 'Saint Vincent and the Grenadines', 'VC', ''),
(235, 'Venezuela', 'VE', ''),
(236, 'Virgin Islands, British', 'VG', ''),
(237, 'Virgin Islands, U.S.', 'VI', ''),
(238, 'Vietnam', 'VN', ''),
(239, 'Vanuatu', 'VU', ''),
(240, 'Wallis and Futuna', 'WF', ''),
(241, 'Samoa', 'WS', ''),
(242, 'Yemen', 'YE', ''),
(243, 'Mayotte', 'YT', ''),
(244, 'South Africa', 'ZA', ''),
(245, 'Zambia', 'ZM', ''),
(246, 'Zimbabwe', 'ZW', '');

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `rate` decimal(11,6) NOT NULL,
  `symbul` varchar(10) NOT NULL,
  `school_id` int(11) NOT NULL,
  `code` varchar(42) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`id`, `title`, `rate`, `symbul`, `school_id`, `code`) VALUES
(1, 'Naira', '1.000000', '&#8358;', 1, 'NGN'),
(2, 'US Dollars', '0.005100', '$', 1, 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

CREATE TABLE `custom_fields` (
  `id` int(11) NOT NULL,
  `label` varchar(555) NOT NULL,
  `type` varchar(55) NOT NULL DEFAULT 'text',
  `form` varchar(55) NOT NULL DEFAULT 'student',
  `school_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `custom_values`
--

CREATE TABLE `custom_values` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(4444) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `id` int(11) NOT NULL,
  `subject` varchar(777) NOT NULL,
  `recipient` longtext NOT NULL,
  `message` text NOT NULL,
  `status` varchar(44) NOT NULL DEFAULT 'queued',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(444) NOT NULL,
  `sender` varchar(444) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `is_cumulative` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `exam_note`
--

CREATE TABLE `exam_note` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `seasion_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `notes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `exam_student_score`
--

CREATE TABLE `exam_student_score` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assessment_score` decimal(11,2) NOT NULL,
  `exam_score` decimal(11,2) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `assessment_1` varchar(33) NOT NULL DEFAULT '0',
  `assessment_2` varchar(33) NOT NULL DEFAULT '0',
  `assessment_3` varchar(33) NOT NULL DEFAULT '0',
  `assessment_4` varchar(33) NOT NULL DEFAULT '0',
  `assessment_5` varchar(33) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `session_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL DEFAULT '0',
  `student_id` int(11) NOT NULL DEFAULT '0',
  `bus_id` int(11) NOT NULL DEFAULT '0',
  `hostel_id` int(11) NOT NULL DEFAULT '0',
  `amount` decimal(11,2) NOT NULL,
  `extras` VARCHAR(3000) NOT NULL DEFAULT '',
  `term_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table stores information on all fees applicable to the school';

-- --------------------------------------------------------

--
-- Table structure for table `fee_paid`
--

CREATE TABLE `fee_paid` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `approved_by` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `gateway` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='this table holds the records of students that have paid a particular fee';

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `school_id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'Image'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gateways`
--

CREATE TABLE `gateways` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sendAPI` varchar(500) NOT NULL,
  `balanceAPI` varchar(500) NOT NULL,
  `deliveryAPI` varchar(500) NOT NULL,
  `successWord` varchar(300) NOT NULL,
  `batchSize` int(11) NOT NULL DEFAULT '200'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `class_id` INT(11) NOT NULL DEFAULT '0',
  `start_mark` decimal(11,2) NOT NULL,
  `end_mark` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `school_id`, `title`, `code`, `start_mark`, `end_mark`) VALUES
(1, 1, 'Distinction', 'A', '70.00', '100.00'),
(2, 1, 'Good', 'B', '60.00', '69.99'),
(3, 1, 'Credit', 'C', '45.00', '59.99'),
(4, 1, 'Fail', 'F', '0.00', '35.00'),
(5, 2, 'Distinction', 'A', '70.00', '100.00'),
(6, 2, 'Good', 'B', '60.00', '69.99'),
(7, 2, 'Credit', 'C', '45.00', '59.99');

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

CREATE TABLE `guardians` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(110) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `phone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Un-Paid',
  `parent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `library`
--

CREATE TABLE `library` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(400) NOT NULL,
  `privilege` varchar(110) NOT NULL,
  `date_created` date NOT NULL,
  `school_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table stores books and other library articles';

-- --------------------------------------------------------

--
-- Table structure for table `messagedetails`
--

CREATE TABLE `messagedetails` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `recipient` varchar(110) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'queued',
  `date` varchar(20) NOT NULL,
  `sender_id` varchar(200) NOT NULL,
  `notice` text NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `role` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notice`
--

CREATE TABLE `notice` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `text` text NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `class_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notice_read`
--

CREATE TABLE `notice_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `mother_name` varchar(100) NOT NULL,
  `father_occupation` varchar(100) NOT NULL,
  `mother_occupation` varchar(100) NOT NULL,
  `father_photo` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `portal_access` int(11) NOT NULL DEFAULT '1',
  `country` varchar(110) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `phone2` varchar(50) NOT NULL,
  `authorization_code` varchar(100) NOT NULL,
  `mother_photo` varchar(200) NOT NULL,
  `pln_pa` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='stores parents information';

-- --------------------------------------------------------

--
-- Table structure for table `paymentalerts`
--

CREATE TABLE `paymentalerts` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `depositor` varchar(200) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `gateway` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reference` varchar(30) NOT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `paymentgateways`
--

CREATE TABLE `paymentgateways` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `type` varchar(300) NOT NULL DEFAULT 'custom',
  `enabled` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `image` varchar(444) NOT NULL,
  `param1` varchar(444) NOT NULL,
  `param2` mediumtext NOT NULL,
  `param3` varchar(300) NOT NULL,
  `param4` varchar(300) NOT NULL,
  `currency_id` varchar(10) NOT NULL DEFAULT '0',
  `minimum_order` decimal(11,6) NOT NULL DEFAULT '0.000000',
  `maximum_order` decimal(11,6) NOT NULL DEFAULT '0.000000',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `charges_perc` varchar(33) NOT NULL DEFAULT '0.00',
  `charges_fix` varchar(55) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `paymentgateway_templates`
--

CREATE TABLE `paymentgateway_templates` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `alias` varchar(300) NOT NULL DEFAULT 'custom',
  `image` varchar(444) NOT NULL,
  `param3_label` varchar(500) NOT NULL,
  `param4_label` varchar(300) NOT NULL,
  `currency_id` varchar(5) NOT NULL DEFAULT '0',
  `param1_label` varchar(113) NOT NULL,
  `param2_label` varchar(113) NOT NULL,
  `hide_pay_button` int(11) NOT NULL DEFAULT '0',
  `module_id` varchar(999) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `paymentgateway_templates`
--

INSERT INTO `paymentgateway_templates` (`id`, `title`, `alias`, `image`, `param3_label`, `param4_label`, `currency_id`, `param1_label`, `param2_label`, `hide_pay_button`, `module_id`, `status`) VALUES
(1, 'InterSwitch WebPay', 'webpay', '', '', '', 'NGN', 'Client ID', 'MAC Key', 0, '0', 1),
(2, 'GTPay', 'gtpay', '', '', '', 'NGN', 'Merchant ID', 'Hash Key', 0, '0', 1),
(3, 'PayPal', 'paypal', '', '', '', 'USD', 'PayPal Email', '', 0, '0', 1),
(4, 'Custom Gateway', 'custom', '', '', '', '0', '', '', 0, '0', 1),
(5, '2Checkout', '2checkout', '', '', '', 'USD', 'Account ID', 'API Secret', 0, '0', 1),
(6, 'Quickteller', 'quickteller', '', '', '', 'NGN', 'Payment Code', '', 0, '0', 1),
(7, 'Stripe', 'stripe', '', '', '', 'USD', 'API Key', '', 0, '0', 1),
(8, 'PayStack', 'paystack', '', '', '', 'NGN', 'Public Key', 'Private Key', 0, '0', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pin`
--

CREATE TABLE `pin` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `pin` varchar(100) NOT NULL,
  `serial` varchar(12) NOT NULL,
  `validity_type` varchar(20) NOT NULL DEFAULT 'none',
  `term_id` int(11) NOT NULL DEFAULT '0',
  `session_id` int(11) NOT NULL DEFAULT '0',
  `student_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `applicant` int(11) NOT NULL,
  `date_generated` date NOT NULL,
  `date_activated` date NOT NULL,
  `valid` varchar(300) NOT NULL,
  `uses` int(11) NOT NULL,
  `used` int(11) NOT NULL,
  `description` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `id` int(11) NOT NULL,
  `added_by` varchar(500) NOT NULL,
  `school_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `cost` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `salary_pay`
--

CREATE TABLE `salary_pay` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `date_pay` date NOT NULL,
  `date_due` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `approved_by` int(11) NOT NULL,
  `generated_by` varchar(200) NOT NULL DEFAULT 'System',
  `month` varchar(7) NOT NULL,
  `allowance` decimal(11,2) NOT NULL,
  `deduction` varchar(44) NOT NULL,
  `paye` varchar(55) NOT NULL,
  `school_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='stores information on salary payments';

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `buyer` varchar(500) NOT NULL,
  `school_id` int(11) NOT NULL,
  `added_by` varchar(500) NOT NULL,
  `date` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `price` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `schedule` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `username` varchar(200) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country_id` varchar(100) NOT NULL,
  `phone1` varchar(20) NOT NULL,
  `phone2` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `logo` varchar(300) NOT NULL,
  `background` varchar(500) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `local_council` varchar(200) NOT NULL,
  `portal_welcome_message` text NOT NULL,
  `domain` varchar(100) NOT NULL,
  `current_session` int(11) NOT NULL,
  `current_term` int(11) NOT NULL,
  `graduate_class_id` int(11) NOT NULL,
  `pin_enabled` int(2) NOT NULL,
  `register_pin_enabled` int(2) NOT NULL,
  `register_portal_enabled` int(2) NOT NULL,
  `register_close_date` date NOT NULL,
  `SMS_username` varchar(200) NOT NULL,
  `SMS_password` varchar(200) NOT NULL,
  `defaultTimeZone` varchar(200) NOT NULL,
  `SMS_sender` varchar(11) NOT NULL,
  `nextscheduled` int(11) NOT NULL DEFAULT '0',
  `nextupdatecheck` int(11) NOT NULL DEFAULT '0',
  `simplepay_id` varchar(200) NOT NULL,
  `paypal_id` varchar(200) NOT NULL,
  `gtpay_id` varchar(200) NOT NULL,
  `webpay_id` varchar(200) NOT NULL,
  `quickteller_id` varchar(200) NOT NULL,
  `voguepay_id` varchar(200) NOT NULL,
  `2checkout_id` varchar(200) NOT NULL,
  `2checkout_secret` varchar(300) NOT NULL,
  `quickteller_secret` varchar(300) NOT NULL,
  `quickteller_domain` varchar(300) NOT NULL,
  `interswitch_mac_key` varchar(500) NOT NULL,
  `theme` varchar(100) NOT NULL,
  `gtpay_hash_key` varchar(300) NOT NULL,
  `paystack_pub` varchar(400) NOT NULL DEFAULT '',
  `paystack_pri` varchar(400) NOT NULL DEFAULT '',
  `webpay_enabled` int(11) NOT NULL DEFAULT '0',
  `gtpay_enabled` int(11) NOT NULL DEFAULT '0',
  `paystack_enabled` int(11) NOT NULL DEFAULT '0',
  `quickteller_enabled` int(11) NOT NULL DEFAULT '0',
  `paypal_enabled` int(11) NOT NULL DEFAULT '0',
  `2checkout_enabled` int(11) NOT NULL DEFAULT '0',
  `voguepay_enabled` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sentmessages`
--

CREATE TABLE `sentmessages` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sender_id` varchar(29) NOT NULL,
  `recipients` mediumtext NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'queued',
  `sent_on` datetime NOT NULL,
  `to_count` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table holds the various sessions of the school';

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `field` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `school_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `field`, `value`, `school_id`) VALUES
(1, 'lastMinuteCron', '15', 0),
(2, 'defaultLanguage', 'en', 0),
(3, 'theme', 'default_blue.css', 0),
(4, 'defaultTimeZone', 'Africa/Lagos', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sms_gateways`
--

CREATE TABLE `sms_gateways` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `base_url` text NOT NULL,
  `success_word` varchar(100) NOT NULL,
  `json_encode` int(11) NOT NULL,
  `request_type` varchar(20) NOT NULL DEFAULT 'GET',
  `sender_field` text NOT NULL,
  `recipient_field` text NOT NULL,
  `message_field` text NOT NULL,
  `param1_field` text NOT NULL,
  `param2_field` text NOT NULL,
  `param3_field` text NOT NULL,
  `param4_field` text NOT NULL,
  `param1_value` text NOT NULL,
  `param2_value` text NOT NULL,
  `param3_value` text NOT NULL,
  `param4_value` text NOT NULL,
  `type` varchar(11) NOT NULL DEFAULT 'sms',
  `language_field` text NOT NULL,
  `audio_field` text NOT NULL,
  `timeout_field` text NOT NULL,
  `speed_field` text NOT NULL,
  `authentication` text NOT NULL,
  `success_logic` varchar(333) NOT NULL DEFAULT 'contain',
  `dlr_enabled` int(11) NOT NULL DEFAULT '0',
  `dlr_callback` varchar(555) NOT NULL,
  `param5_field` varchar(555) NOT NULL,
  `param5_value` varchar(555) NOT NULL,
  `tts_enabled` int(11) NOT NULL DEFAULT '0',
  `file_enabled` int(11) NOT NULL DEFAULT '0',
  `cutting_limit` int(11) NOT NULL DEFAULT '0',
  `hour_limit` int(11) NOT NULL DEFAULT '0',
  `cutting_percent` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(333) NOT NULL,
  `username` varchar(666) NOT NULL,
  `password` varchar(666) NOT NULL,
  `balance_url` varchar(555) NOT NULL,
  `nextStart` varchar(33) NOT NULL DEFAULT '0',
  `jobs` int(11) NOT NULL DEFAULT '0',
  `base64_encode` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `sex` varchar(20) NOT NULL,
  `address` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(110) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `marital_status` varchar(100) NOT NULL,
  `pln_pa` varchar(199) NOT NULL,
  `payroll` decimal(11,2) NOT NULL,
  `acc_name` varchar(200) NOT NULL,
  `acc_num` varchar(200) NOT NULL,
  `banker` varchar(200) NOT NULL,
  `allowance` decimal(11,2) NOT NULL,
  `deduction` decimal(11,2) NOT NULL,
  `paye` decimal(11,2) NOT NULL,
  `employment_date` date NOT NULL,
  `qualification` varchar(300) NOT NULL,
  `designation` varchar(300) NOT NULL,
  `note` text NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` varchar(500) NOT NULL,
  `quantity` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `cost` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_category`
--

CREATE TABLE `stock_category` (
  `id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `other_name` varchar(100) NOT NULL,
  `sex` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `local_council` varchar(150) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(110) NOT NULL,
  `portal_access` int(11) NOT NULL DEFAULT '1',
  `nationality` varchar(110) NOT NULL,
  `state_origin` varchar(200) NOT NULL,
  `admission_number` varchar(100) NOT NULL,
  `bload_group` varchar(10) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `hostel_id` int(11) NOT NULL DEFAULT '0',
  `year` year(4) NOT NULL,
  `pln_pa` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `email` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student_assignments`
--

CREATE TABLE `student_assignments` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `student_id` int(11) NOT NULL,
  `file` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `seen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `attendance` varchar(50) NOT NULL DEFAULT 'Present'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='keeps attendance records of each students for each day';

-- --------------------------------------------------------

--
-- Table structure for table `student_class`
--

CREATE TABLE `student_class` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='maps each student to a class for a given session';

--
-- Dumping data for table `student_class`
--

--
-- Table structure for table `student_guardian`
--

CREATE TABLE `student_guardian` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table maps each guardians to a student';

-- --------------------------------------------------------

--
-- Table structure for table `student_hostel`
--

CREATE TABLE `student_hostel` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `hostel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student_parent`
--

CREATE TABLE `student_parent` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='maps each student to a parent';

-- --------------------------------------------------------

--
-- Table structure for table `student_status`
--

CREATE TABLE `student_status` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` varchar(500) NOT NULL,
  `class_id` int(11) NOT NULL,
  `assessment_max` decimal(11,2) NOT NULL DEFAULT '30.00',
  `exam_max` decimal(11,2) NOT NULL DEFAULT '70.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `sex` varchar(20) NOT NULL,
  `address` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `acc_name` varchar(200) NOT NULL,
  `acc_num` varchar(200) NOT NULL,
  `banker` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(110) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `marital_status` varchar(100) NOT NULL,
  `pln_pa` varchar(199) NOT NULL,
  `payroll` decimal(11,2) NOT NULL,
  `allowance` decimal(11,2) NOT NULL,
  `deduction` decimal(11,2) NOT NULL,
  `paye` decimal(11,2) NOT NULL,
  `employment_date` date NOT NULL,
  `qualification` varchar(300) NOT NULL,
  `designation` varchar(300) NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class`
--

CREATE TABLE `teacher_class` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='this table maps each teacher to designated class';

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subject`
--

CREATE TABLE `teacher_subject` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='this table maps each teacher to desicnated subject';

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `url` text NOT NULL,
  `is_system` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`id`, `title`, `url`, `is_system`) VALUES
(1, 'Default Blue', 'default_blue.css', 1),
(2, 'Midnight Blue', 'MidnightBlue.css', 1),
(3, 'Dark Red', 'DarkRed.css', 1),
(4, 'Snow White', 'SnowWhite.css', 1),
(5, 'Olive Green', 'OliveGreen.css', 1),
(6, 'Sea Green', 'SeaGreen.css', 1),
(7, 'Dark Green', 'DarkGreen.css', 1),
(8, 'Dark Cyan', 'DarkCyan.css', 1),
(9, 'Rebecca Purple', 'RebeccaPurple.css', 1),
(10, 'Orchid Pink', 'Orchid.css', 1),
(11, 'Black', 'Black.css', 1),
(12, 'Saddle Brown', 'SaddleBrown.css', 1);

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `start_time` varchar(10) NOT NULL,
  `end_time` varchar(10) NOT NULL,
  `activity` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(110) NOT NULL,
  `direction` varchar(11) NOT NULL,
  `approvedBy` varchar(200) NOT NULL,
  `invoice_id` int(11) NOT NULL DEFAULT '0',
  `gateway` int(11) NOT NULL DEFAULT '0',
  `gateway_comment` varchar(500) NOT NULL,
  `transaction_reference` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Holds details of all purchases on the panel';

-- --------------------------------------------------------

--
-- Table structure for table `transaction_status`
--

CREATE TABLE `transaction_status` (
  `id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Definition of the various transaction status';

--
-- Dumping data for table `transaction_status`
--

INSERT INTO `transaction_status` (`id`, `value`) VALUES
(1, 'Pending'),
(2, 'Pending'),
(3, 'Completed'),
(4, 'Failed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `localsession_id` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `is_supper` int(11) NOT NULL DEFAULT '0',
  `last_login` datetime NOT NULL,
  `profile_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `title`) VALUES
(1, 'Administrator'),
(2, 'Manager'),
(3, 'Account'),
(4, 'Teacher'),
(5, 'Parent'),
(6, 'Student'),
(7, 'Front-Desk'),
(8, 'Librarian'),
(9, 'Others'),
(10, 'Store Keeper');

CREATE TABLE `student_bus` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `student_id` int(11) NOT NULL,
 `bus_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `route` varchar(300) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT '1',
  `driver` varchar(300) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `license_no` varchar(20) NOT NULL,
  `school_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
CREATE TABLE `e_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `teacher_id` int(11) NOT NULL DEFAULT '0',
  `expire_date` varchar(30) NOT NULL,
  `fee_id` int(11) NOT NULL DEFAULT '0',
  `start_date` varchar(30) NOT NULL,
  `school_id` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `e_courses_contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `datetime` varchar(30) NOT NULL,
  `text` text NOT NULL DEFAULT '',
  `video` varchar(3000) NOT NULL DEFAULT '',
  `youtube` varchar(1033) NOT NULL DEFAULT '',
  `document` varchar(3000) NOT NULL DEFAULT '',
  `audio` varchar(3000) NOT NULL DEFAULT '',
  `course_id` varchar(30) NOT NULL,
  `school_id` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `class_course` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `class_id` int(11) NOT NULL,
 `course_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `students_course` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `student_id` int(11) NOT NULL,
 `course_id` int(11) NOT NULL,
 `start_date` varchar(30) NOT NULL,
 `end_date` varchar(30) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cbt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `datetime` varchar(30) NOT NULL,
  `mark_type` VARCHAR(44) NOT NULL DEFAULT 'assessment',
  `accessment_id` int(11) NOT NULL DEFAULT '0',
  `description` varchar(5000) NOT NULL,
  `question_limit` int(11) NOT NULL DEFAULT '10',
  `pass_mark` int(11) NOT NULL DEFAULT '50',
  `time_duration` int(11) NOT NULL DEFAULT '0',
  `allow_repeat` int(11) NOT NULL DEFAULT '0',
  `teacher_id` int(11) NOT NULL DEFAULT '0',
  `exam_id` int(11) NOT NULL DEFAULT '0',
  `subject_id` int(11) NOT NULL DEFAULT '0',
  `course_id` int(11) NOT NULL DEFAULT '0',
  `fee_id` int(11) NOT NULL DEFAULT '0',
  `start_date` varchar(30) NOT NULL,
  `expire_date` varchar(30) NOT NULL,
  `school_id` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cbt_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(3000) NOT NULL,
  `cbt_id` int(11) NOT NULL DEFAULT '0',
  `correct_answer` varchar(3000) NOT NULL,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `school_id` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cbt_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `answer_date` varchar(300) NOT NULL,
  `question_id` varchar(300) NOT NULL,
  `cbt_id` int(11) NOT NULL DEFAULT '0',
  `answer` varchar(3000) NOT NULL,
  `competion_time` varchar(300) NOT NULL DEFAULT '0',
  `is_finished` varchar(300) NOT NULL DEFAULT '0',
  `student_id` int(11) NOT NULL DEFAULT '0',
  `school_id` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cbt_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT '0',
  `answer` varchar(3000) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;  

CREATE TABLE `reportcard_extras` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(333) NOT NULL,
  `parent_only` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reportcard_extras_values` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `reportcard_extras_id` int(11) NOT NULL,
  `value` varchar(555) NOT NULL,
  `exam_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `reportcard_extras`  ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `reportcard_extras_values`  ADD PRIMARY KEY (`id`);

ALTER TABLE `reportcard_extras`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reportcard_extras_values`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_fields`
--
ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_values`
--
ALTER TABLE `custom_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_note`
--
ALTER TABLE `exam_note`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_student_score`
--
ALTER TABLE `exam_student_score`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_paid`
--
ALTER TABLE `fee_paid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateways`
--
ALTER TABLE `gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guardians`
--
ALTER TABLE `guardians`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `library`
--
ALTER TABLE `library`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messagedetails`
--
ALTER TABLE `messagedetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notice_read`
--
ALTER TABLE `notice_read`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paymentalerts`
--
ALTER TABLE `paymentalerts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paymentgateways`
--
ALTER TABLE `paymentgateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paymentgateway_templates`
--
ALTER TABLE `paymentgateway_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pin`
--
ALTER TABLE `pin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salary_pay`
--
ALTER TABLE `salary_pay`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sentmessages`
--
ALTER TABLE `sentmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `settings` ADD FULLTEXT KEY `field` (`field`);

--
-- Indexes for table `sms_gateways`
--
ALTER TABLE `sms_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_category`
--
ALTER TABLE `stock_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_class`
--
ALTER TABLE `student_class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_guardian`
--
ALTER TABLE `student_guardian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_hostel`
--
ALTER TABLE `student_hostel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_parent`
--
ALTER TABLE `student_parent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_status`
--
ALTER TABLE `student_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_class`
--
ALTER TABLE `teacher_class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_status`
--
ALTER TABLE `transaction_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `album`
--
ALTER TABLE `album`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `book_categories`
--
ALTER TABLE `book_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `book_issues`
--
ALTER TABLE `book_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;
--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `custom_fields`
--
ALTER TABLE `custom_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `custom_values`
--
ALTER TABLE `custom_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exam_note`
--
ALTER TABLE `exam_note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `exam_student_score`
--
ALTER TABLE `exam_student_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `fee_paid`
--
ALTER TABLE `fee_paid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `gateways`
--
ALTER TABLE `gateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `guardians`
--
ALTER TABLE `guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `library`
--
ALTER TABLE `library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `messagedetails`
--
ALTER TABLE `messagedetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notice`
--
ALTER TABLE `notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notice_read`
--
ALTER TABLE `notice_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `paymentalerts`
--
ALTER TABLE `paymentalerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `paymentgateways`
--
ALTER TABLE `paymentgateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `paymentgateway_templates`
--
ALTER TABLE `paymentgateway_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `pin`
--
ALTER TABLE `pin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `salary_pay`
--
ALTER TABLE `salary_pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `sentmessages`
--
ALTER TABLE `sentmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `sms_gateways`
--
ALTER TABLE `sms_gateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `stock_category`
--
ALTER TABLE `stock_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `student_assignments`
--
ALTER TABLE `student_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `student_attendance`
--
ALTER TABLE `student_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `student_class`
--
ALTER TABLE `student_class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `student_guardian`
--
ALTER TABLE `student_guardian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `student_hostel`
--
ALTER TABLE `student_hostel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `student_parent`
--
ALTER TABLE `student_parent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `student_status`
--
ALTER TABLE `student_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `teacher_class`
--
ALTER TABLE `teacher_class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `transaction_status`
--
ALTER TABLE `transaction_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;  

-- Indexes for perforance
ALTER TABLE `cbt` ADD INDEX( `teacher_id`, `exam_id`, `subject_id`, `course_id`, `fee_id`, `school_id`);
ALTER TABLE `cbt_answers` ADD INDEX( `question_id`, `cbt_id`, `answer`, `student_id`);
ALTER TABLE `cbt_choices` ADD INDEX( `question_id`);
ALTER TABLE `class_course` ADD INDEX( `class_id`, `course_id`);
ALTER TABLE `cbt_questions` ADD INDEX( `cbt_id`, `course_id`, `school_id`);
ALTER TABLE `classes` ADD INDEX( `school_id`);
ALTER TABLE `assignments` ADD INDEX( `school_id`, `subject_id`, `session_id`, `term_id`, `class_id`);
ALTER TABLE `custom_fields` ADD INDEX( `school_id`);
ALTER TABLE `exams` ADD INDEX( `school_id`, `session_id`, `term_id`);
ALTER TABLE `e_courses` ADD INDEX( `teacher_id`, `school_id`);
ALTER TABLE `exam_student_score` ADD INDEX( `exam_id`, `student_id`, `subject_id`, `school_id`, `class_id`, `session_id`);
ALTER TABLE `e_courses_contents` ADD INDEX( `course_id`, `school_id`);
ALTER TABLE `fees` ADD INDEX( `school_id`, `session_id`, `class_id`, `bus_id`, `hostel_id`, `term_id`);
ALTER TABLE `fee_paid` ADD INDEX( `school_id`, `fee_id`, `student_id`, `session_id`, `term_id`, `amount`);
ALTER TABLE `grades` ADD INDEX( `school_id`, `code`);
ALTER TABLE `messagedetails` ADD INDEX( `message_id`, `recipient`, `status`, `customer_id`);
ALTER TABLE `guardians` ADD INDEX( `school_id`);
ALTER TABLE `parents` ADD INDEX( `school_id`);
ALTER TABLE `paymentgateways` ADD INDEX( `enabled`, `customer_id`);
ALTER TABLE `sessions` ADD INDEX( `school_id`);
ALTER TABLE `pin` ADD INDEX( `school_id`, `pin`, `serial`, `validity_type`, `term_id`, `session_id`, `student_id`, `parent_id`, `applicant`, `uses`, `used`);
ALTER TABLE `settings` ADD INDEX( `school_id`);
ALTER TABLE `students` ADD INDEX( `school_id`, `status`);
ALTER TABLE `students_course` ADD INDEX( `student_id`, `course_id`);
ALTER TABLE `student_assignments` ADD INDEX( `assignment_id`, `student_id`, `seen`);
ALTER TABLE `student_attendance` ADD INDEX( `school_id`, `session_id`, `class_id`, `term_id`, `student_id`, `date`, `attendance`);
ALTER TABLE `student_class` ADD INDEX( `class_id`, `student_id`, `session_id`);
ALTER TABLE `student_guardian` ADD INDEX( `student_id`, `guardian_id`);
ALTER TABLE `student_hostel` ADD INDEX( `student_id`, `hostel_id`);
ALTER TABLE `student_parent` ADD INDEX( `parent_id`, `student_id`);
ALTER TABLE `subject` ADD INDEX( `school_id`, `class_id`);
ALTER TABLE `teacher_subject` ADD INDEX( `teacher_id`, `subject_id`);
ALTER TABLE `teacher_class` ADD INDEX( `teacher_id`, `class_id`);
ALTER TABLE `users` ADD INDEX( `username`, `password`, `localsession_id`, `email`, `school_id`);
ALTER TABLE `timetable` ADD INDEX( `school_id`, `term_id`, `class_id`, `session_id`, `day`);
ALTER TABLE `vehicles` ADD INDEX( `license_no`, `school_id`);
