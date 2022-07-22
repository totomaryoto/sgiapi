-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 17 Nov 2020 pada 16.46
-- Versi server: 10.2.34-MariaDB-cll-lve
-- Versi PHP: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `helk6427_pos`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(64) DEFAULT NULL,
  `store_id` varchar(64) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(64) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(64) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `category_id` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `category`
--

INSERT INTO `category` (`id`, `category_name`, `store_id`, `created_date`, `created_by`, `modified_date`, `modified_by`, `parent_id`, `category_id`) VALUES
(1, 'Air mineral', 'STR-20-00002', '2020-09-23 09:35:20', 'USR-0920-0002', NULL, NULL, NULL, 'CAT-0920-0001'),
(2, 'Makanan', 'STR-20-00001', '2020-09-26 14:25:51', 'USR-0920-0001', NULL, NULL, NULL, 'CAT-0920-0001'),
(3, 'Lain-lain', 'STR-20-00002', '2020-10-05 18:49:01', 'USR-0920-0002', NULL, NULL, NULL, 'CAT-1020-0001'),
(4, 'Frozen', '', '2020-11-03 23:40:50', 'USR-1120-0001', NULL, NULL, NULL, 'CAT-1120-0001'),
(5, 'kopi', 'STR-20-00003', '2020-11-16 15:32:36', 'USR-1120-0003', NULL, NULL, NULL, 'CAT-1120-0001'),
(6, 'makanan', 'STR-20-00003', '2020-11-16 17:02:07', 'USR-1120-0003', NULL, NULL, NULL, 'CAT-1120-0002'),
(9, 'Minuman', 'STR-20-00004', '2020-11-17 13:43:53', 'USR-1120-0004', NULL, NULL, NULL, 'CAT-1120-0001'),
(10, 'Makanan', 'STR-20-00004', '2020-11-17 13:44:03', 'USR-1120-0004', NULL, NULL, NULL, 'CAT-1120-0002');

-- --------------------------------------------------------

--
-- Struktur dari tabel `items`
--

CREATE TABLE `items` (
  `item_code` varchar(64) NOT NULL,
  `item_name` varchar(64) DEFAULT NULL,
  `remark` varchar(64) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(64) NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(64) DEFAULT NULL,
  `store_id` varchar(64) NOT NULL,
  `item_price` decimal(18,2) DEFAULT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `item_type` varchar(18) DEFAULT NULL,
  `item_category_id` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `items`
--

INSERT INTO `items` (`item_code`, `item_name`, `remark`, `created_date`, `created_by`, `modified_date`, `modified_by`, `store_id`, `item_price`, `item_image`, `item_type`, `item_category_id`) VALUES
('ITM-STR-20-00003-1120-1', 'americano', 'sma', '2020-11-16 15:33:52', 'USR-1120-0003', NULL, NULL, 'STR-20-00003', 15000.00, '8d091bd98cc5dc9096a7e2f1e2e6445d.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00003-1120-2', 'Latte', 'j', '2020-11-16 15:43:46', 'USR-1120-0003', NULL, NULL, 'STR-20-00003', 10000.00, '31839387abe72979008d903b4c0cfa00.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-1', 'Kapal Api', 'Gelas', '2020-11-17 13:46:38', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, 'b48919abbd5dfcf5e229f180624a4ff3.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-2', 'Torabika Duo', 'Gelas', '2020-11-17 13:48:28', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, '402f80c530b7989ee3d40e00a9f7a5c3.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-3', 'White Koffie', 'Gelas', '2020-11-17 13:51:36', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, 'ddb40e637f47eaa992f85268340a944e.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-4', 'ABC Susu ', 'Gelas', '2020-11-17 13:52:56', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, '402a2974e4efb59c7f15c6de2587067a.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-5', 'ABC Mocca', 'Gelas', '2020-11-17 13:54:33', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, '6c275a6e2799506ea97a220117cc6440.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-6', 'Indocafe', 'Gelas', '2020-11-17 13:55:52', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, '7add0202a0143973382a39730b08a1ad.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-7', 'TaroBika Susu', 'Gelas', '2020-11-17 13:57:18', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, '103f40398f3d88f4682e3d6e28308477.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-8', 'ToraBika Mocca', 'Gelas', '2020-11-17 14:00:46', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, 'b0894f0fc81cb59d726065a474d153b3.jpg', 'Produk', 'CAT-1120-0001'),
('ITM-STR-20-00004-1120-9', 'ToraBika Jahe Susu', 'Gelas', '2020-11-17 14:08:23', 'USR-1120-0004', NULL, NULL, 'STR-20-00004', 3000.00, '908376235cc82cc85733df3e5a760120.jpg', 'Produk', 'CAT-1120-0001');

-- --------------------------------------------------------

--
-- Struktur dari tabel `item_price`
--

CREATE TABLE `item_price` (
  `id` int(11) NOT NULL,
  `item_code` varchar(32) DEFAULT NULL,
  `store_id` varchar(32) DEFAULT NULL,
  `item_price` decimal(18,2) DEFAULT NULL,
  `partner_type` varchar(32) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `item_price`
--

INSERT INTO `item_price` (`id`, `item_code`, `store_id`, `item_price`, `partner_type`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'ITM-STR-20-00003-1120-1', 'STR-20-00003', 15000.00, '0', '2020-11-16 15:33:52', 'USR-1120-0003', NULL, NULL),
(2, 'ITM-STR-20-00003-1120-2', 'STR-20-00003', 10000.00, '0', '2020-11-16 15:43:46', 'USR-1120-0003', NULL, NULL),
(3, 'ITM-STR-20-00004-1120-1', 'STR-20-00004', 3000.00, '0', '2020-11-17 13:46:38', 'USR-1120-0004', NULL, NULL),
(4, 'ITM-STR-20-00004-1120-2', 'STR-20-00004', 3000.00, '0', '2020-11-17 13:48:28', 'USR-1120-0004', NULL, NULL),
(5, 'ITM-STR-20-00004-1120-3', 'STR-20-00004', 3000.00, '0', '2020-11-17 13:51:36', 'USR-1120-0004', NULL, NULL),
(6, 'ITM-STR-20-00004-1120-4', 'STR-20-00004', 3000.00, '0', '2020-11-17 13:52:56', 'USR-1120-0004', NULL, NULL),
(7, 'ITM-STR-20-00004-1120-5', 'STR-20-00004', 3000.00, '0', '2020-11-17 13:54:33', 'USR-1120-0004', NULL, NULL),
(8, 'ITM-STR-20-00004-1120-6', 'STR-20-00004', 3000.00, '0', '2020-11-17 13:55:52', 'USR-1120-0004', NULL, NULL),
(9, 'ITM-STR-20-00004-1120-7', 'STR-20-00004', 3000.00, '0', '2020-11-17 13:57:18', 'USR-1120-0004', NULL, NULL),
(10, 'ITM-STR-20-00004-1120-8', 'STR-20-00004', 3000.00, '0', '2020-11-17 14:00:46', 'USR-1120-0004', NULL, NULL),
(11, 'ITM-STR-20-00004-1120-9', 'STR-20-00004', 3000.00, '0', '2020-11-17 14:08:23', 'USR-1120-0004', NULL, NULL),
(12, 'ITM-STR-20-00004-1120-2', 'STR-20-00004', 3500.00, '1', '2020-11-17 14:49:21', 'USR-1120-0004', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `item_stock`
--

CREATE TABLE `item_stock` (
  `id` int(11) NOT NULL,
  `item_code` varchar(32) DEFAULT NULL,
  `store_id` varchar(32) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `document_id` varchar(32) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(32) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `item_stock`
--

INSERT INTO `item_stock` (`id`, `item_code`, `store_id`, `quantity`, `document_id`, `created_date`, `created_by`, `remark`, `modified_date`, `modified_by`) VALUES
(1, 'ITM-STR-20-00003-1120-1', 'STR-20-00003', 9, NULL, '2020-11-16 18:11:14', 'USR-1120-0003', NULL, '2020-11-16 18:11:15', 'USR-1120-0003'),
(2, 'ITM-STR-20-00004-1120-1', 'STR-20-00004', 10, NULL, '2020-11-17 14:16:04', 'USR-1120-0004', NULL, NULL, NULL),
(3, 'ITM-STR-20-00004-1120-2', 'STR-20-00004', 10, NULL, '2020-11-17 14:16:14', 'USR-1120-0004', NULL, NULL, NULL),
(4, 'ITM-STR-20-00004-1120-3', 'STR-20-00004', 10, NULL, '2020-11-17 14:16:20', 'USR-1120-0004', NULL, NULL, NULL),
(5, 'ITM-STR-20-00004-1120-4', 'STR-20-00004', 6, NULL, '2020-11-17 14:16:30', 'USR-1120-0004', NULL, NULL, NULL),
(6, 'ITM-STR-20-00004-1120-5', 'STR-20-00004', 10, NULL, '2020-11-17 14:16:36', 'USR-1120-0004', NULL, NULL, NULL),
(7, 'ITM-STR-20-00004-1120-6', 'STR-20-00004', 10, NULL, '2020-11-17 14:16:46', 'USR-1120-0004', NULL, NULL, NULL),
(8, 'ITM-STR-20-00004-1120-7', 'STR-20-00004', 10, NULL, '2020-11-17 14:16:53', 'USR-1120-0004', NULL, NULL, NULL),
(9, 'ITM-STR-20-00004-1120-8', 'STR-20-00004', 10, NULL, '2020-11-17 14:17:01', 'USR-1120-0004', NULL, NULL, NULL),
(10, 'ITM-STR-20-00004-1120-9', 'STR-20-00004', 10, NULL, '2020-11-17 14:17:07', 'USR-1120-0004', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `midtrans_status_code`
--

CREATE TABLE `midtrans_status_code` (
  `id` int(11) NOT NULL,
  `description` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `midtrans_status_code`
--

INSERT INTO `midtrans_status_code` (`id`, `description`) VALUES
(200, 'Berhasil'),
(201, 'Menunggu Proses Pembayaran'),
(202, 'Pembayaran Gagal');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notification`
--

CREATE TABLE `notification` (
  `id` int(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` varchar(250) NOT NULL,
  `notification_date` datetime NOT NULL,
  `parameter` text NOT NULL,
  `notification_to` varchar(64) NOT NULL,
  `read_date` datetime NOT NULL,
  `read_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `partners`
--

CREATE TABLE `partners` (
  `partner_code` varchar(25) NOT NULL,
  `partner_name` varchar(64) DEFAULT NULL,
  `partner_address` varchar(180) DEFAULT NULL,
  `partner_phone` varchar(20) DEFAULT NULL,
  `partner_email` varchar(100) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `store_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `partners`
--

INSERT INTO `partners` (`partner_code`, `partner_name`, `partner_address`, `partner_phone`, `partner_email`, `join_date`, `type`, `created_date`, `created_by`, `modified_date`, `modified_by`, `store_id`) VALUES
('PRT-1120-0001', 'test 1', 'jakarta', '+620484545', 'test@gmail.com', '2020-11-16', '0', '2020-11-16 17:03:05', 'USR-1120-0003', '2020-11-17 13:10:06', 'USR-1120-0003', 'STR-20-00003');

-- --------------------------------------------------------

--
-- Struktur dari tabel `partner_type`
--

CREATE TABLE `partner_type` (
  `id` int(11) NOT NULL,
  `partner_type_name` varchar(100) DEFAULT NULL,
  `flag` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `partner_type`
--

INSERT INTO `partner_type` (`id`, `partner_type_name`, `flag`) VALUES
(0, 'Retail', 1),
(1, 'Supplier', 1),
(2, 'Agent', 1),
(3, 'Distributor', 1),
(4, 'Reseller', 1),
(5, 'Member', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stores`
--

CREATE TABLE `stores` (
  `store_id` varchar(100) NOT NULL,
  `store_name` varchar(100) DEFAULT NULL,
  `store_address` varchar(100) DEFAULT NULL,
  `store_phone` varchar(20) DEFAULT NULL,
  `store_owner` varchar(100) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `stores`
--

INSERT INTO `stores` (`store_id`, `store_name`, `store_address`, `store_phone`, `store_owner`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
('STR-20-00001', 'toko kuh', 'jalan jalan', '+6289688965164', 'USR-1120-0001', 'USR-1120-0001', '2020-11-05 13:11:58', NULL, NULL),
('STR-20-00002', 'Lapak Jam HDT', 'Mampang Prapatan', '+6281219936210', 'USR-1120-0002', 'USR-1120-0002', '2020-11-05 13:45:43', NULL, NULL),
('STR-20-00003', 'toko kopiku', 'jalan merdeka', '+6202199231', 'USR-1120-0003', 'USR-1120-0003', '2020-11-16 15:31:52', NULL, NULL),
('STR-20-00004', 'Warkop Atas ', 'Jakarta timur', '+6281770800286', 'USR-1120-0004', 'USR-1120-0004', '2020-11-17 13:20:02', NULL, '2020-11-17 13:20:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `subscription_name` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `month_total` int(11) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `flag` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `subscription_name`, `created_date`, `created_by`, `modified_date`, `modified_by`, `month_total`, `price`, `flag`) VALUES
(1, 'Trial', NULL, NULL, NULL, NULL, 1, 0, 1),
(2, 'Premium', NULL, NULL, '2020-10-16 10:33:24', 0, 1, 100000, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ticket`
--

CREATE TABLE `ticket` (
  `id` varchar(64) NOT NULL,
  `ticket_category` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `is_close` tinyint(1) NOT NULL,
  `close_date` datetime NOT NULL,
  `attachment` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ticket_category`
--

CREATE TABLE `ticket_category` (
  `ticket_category_id` int(11) NOT NULL,
  `ticket_category_name` varchar(100) NOT NULL,
  `ticket_category_order` int(11) NOT NULL,
  `flag` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ticket_detail`
--

CREATE TABLE `ticket_detail` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(64) NOT NULL,
  `message_from` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `message_date` datetime NOT NULL,
  `attachment` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_detail`
--

CREATE TABLE `transaction_detail` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `item_code` varchar(64) DEFAULT NULL,
  `item_price` decimal(18,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `grand_total` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `transaction_detail`
--

INSERT INTO `transaction_detail` (`id`, `transaction_id`, `item_code`, `item_price`, `qty`, `grand_total`) VALUES
(1, 'TR-1120-0001', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(2, 'TR-1120-0002', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(3, 'TR-1120-0003', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(4, 'TR-1120-0004', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(5, 'TR-1120-0005', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(6, 'TR-1120-0006', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(7, 'TR-1120-0007', 'ITM-STR-20-00003-1120-2', 10000.00, 1, 10000.00),
(8, 'TR-1120-0007', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(9, 'TR-1120-0008', 'ITM-STR-20-00003-1120-2', 10000.00, 1, 10000.00),
(10, 'TR-1120-0008', 'ITM-STR-20-00003-1120-1', 15000.00, 1, 15000.00),
(11, 'TR-1120-0001', 'ITM-STR-20-00004-1120-4', 3000.00, 1, 3000.00),
(12, 'TR-1120-0001', 'ITM-STR-20-00004-1120-2', 3000.00, 1, 3000.00),
(13, 'TR-1120-0002', 'ITM-STR-20-00004-1120-4', 3000.00, 2, 6000.00),
(14, 'TR-1120-0003', 'ITM-STR-20-00004-1120-4', 3000.00, 2, 6000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_header`
--

CREATE TABLE `transaction_header` (
  `transaction_id` varchar(100) NOT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `store_id` varchar(64) NOT NULL,
  `grand_total` decimal(18,2) DEFAULT NULL,
  `promo_code` varchar(64) DEFAULT NULL,
  `promo_discount` decimal(18,2) DEFAULT NULL,
  `promo_type` varchar(64) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(64) DEFAULT NULL,
  `partner_type` int(11) DEFAULT NULL,
  `partner_code` varchar(64) DEFAULT NULL,
  `midtrans_transaction_id` varchar(250) NOT NULL,
  `midtrans_order_id` varchar(250) NOT NULL,
  `status_code` int(11) NOT NULL,
  `va_number` varchar(100) NOT NULL,
  `va_bank` varchar(64) NOT NULL,
  `biller_code` varchar(32) NOT NULL,
  `bill_key` varchar(64) NOT NULL,
  `payment_type` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `transaction_header`
--

INSERT INTO `transaction_header` (`transaction_id`, `transaction_date`, `store_id`, `grand_total`, `promo_code`, `promo_discount`, `promo_type`, `created_date`, `created_by`, `partner_type`, `partner_code`, `midtrans_transaction_id`, `midtrans_order_id`, `status_code`, `va_number`, `va_bank`, `biller_code`, `bill_key`, `payment_type`) VALUES
('TR-1120-0001', '2020-11-16 15:39:12', 'STR-20-00003', 15000.00, NULL, NULL, NULL, '2020-11-16 15:39:12', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0001', '2020-11-17 14:14:02', 'STR-20-00004', 6000.00, NULL, NULL, NULL, '2020-11-17 14:14:02', 'USR-1120-0004', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0002', '2020-11-16 15:39:23', 'STR-20-00003', 15000.00, NULL, NULL, NULL, '2020-11-16 15:39:23', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0002', '2020-11-17 14:58:32', 'STR-20-00004', 6000.00, NULL, NULL, NULL, '2020-11-17 14:58:32', 'USR-1120-0004', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0003', '2020-11-16 15:39:28', 'STR-20-00003', 15000.00, NULL, NULL, NULL, '2020-11-16 15:39:28', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0003', '2020-11-17 14:59:02', 'STR-20-00004', 6000.00, NULL, NULL, NULL, '2020-11-17 14:59:02', 'USR-1120-0004', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0004', '2020-11-16 15:39:31', 'STR-20-00003', 15000.00, NULL, NULL, NULL, '2020-11-16 15:39:31', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0005', '2020-11-16 15:39:40', 'STR-20-00003', 15000.00, NULL, NULL, NULL, '2020-11-16 15:39:40', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0006', '2020-11-16 15:39:43', 'STR-20-00003', 15000.00, NULL, NULL, NULL, '2020-11-16 15:39:43', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0007', '2020-11-16 15:52:43', 'STR-20-00003', 25000.00, NULL, NULL, NULL, '2020-11-16 15:52:43', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash'),
('TR-1120-0008', '2020-11-16 15:53:01', 'STR-20-00003', 25000.00, NULL, NULL, NULL, '2020-11-16 15:53:01', 'USR-1120-0003', 0, '-', '', '', 0, '', '', '', '', 'cash');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` varchar(64) NOT NULL,
  `fullname` varchar(240) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(256) DEFAULT NULL,
  `referal_code` varchar(100) DEFAULT NULL,
  `device_id` varchar(250) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `user_type` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_image` varchar(100) DEFAULT NULL,
  `rekening_no` varchar(100) NOT NULL,
  `rekening_name` varchar(100) NOT NULL,
  `rekening_bank` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `referal_code`, `device_id`, `created_date`, `modified_date`, `user_type`, `phone`, `user_image`, `rekening_no`, `rekening_name`, `rekening_bank`) VALUES
('USR-1120-0004', '', 'agungpranaputra19@gmail.com', '46d918a8db884bfd7d7d58587799a61b08b9f4d7c322aa1f0bf1526344173abbe56edd0b8048941c03d7bd5d42ad78c2ef06c6d22aaec49c237ebd19d06fa98dbhoqGiWuhRCtN0Sy1VeWbIXO4TVame53oEmjx0r7/cs=', '', 'dTMQCgPPSZ2lzj1Es_bTRJ:APA91bERNZ3HUqKIUtwVPOPCFiIFcpgiegQ0BPfZaVl6le9ZIvZNSG3ImzsccAvKRKfZbIiZ_HTSAtiVwo_elt-6qmG6o4bZMjC23u2GW7qBpWddclDjVSsqL_FHpBr4kCPCNXtEG4N8', '2020-11-17 13:16:06', '2020-11-17 13:39:42', 2, '+6281770800286', '8b2a40283798c054c611dd3f122f83f2.jpg', '', '', ''),
('USR-1120-0001', '', 'boby12kurniawan@gmail.com', '503c23d18581b00e29dfd7ca5eba9ef81279c0441f73f294245ad7a9e67d9e03eace82e1c28df41d759c0512b3aa27244a7568b3252dd1c901ca2e0839013f293p6DhmEUh5wcyR2ImuEsxQcMNEtfL/bu4QNsNSklWJk=', '', 'fPYR05qlRp-XdvSx7jBBbM:APA91bH6zuMPkNROEcQpJSXyqD9GZBjhqdSueTODRSGQSbiG52oCuqbBr3PvDBfFqms_XlddLxnOVfUmU0nnMcZH0hTz_wGzljpiswSkQNpBlzMVafQuQQKWDR5qRCSa5g7Ab5pxy-H9', '2020-11-05 13:11:02', '2020-11-05 13:11:12', 2, '+6289688965164', NULL, '', '', ''),
('USR-1120-0002', '', 'riaoktaviaindahp@gmail.com', '3afd725f2b43efb29b26f9b6f5662a015231c58e6af843204d882eaf6d8e12bbf33ce60d7cf78131a55a8100ecff7cf55668ee91fc0f36995dc89bd959f8b362mBSldZhWPMVfZsauUFd7Ok3YgSxMHnZJWL5393ezuqs=', '', 'dlLTVsgqR5OWk0myf13q4c:APA91bH3qPkpVJ8dSMdrfhJ9rub3ovYyiVe2pRl1xPgjwOXbD6Pdzmjr-hhG0pVunAc7BWcSHyvsASKiGCIVKZ5xAPNqWNy7S9UYjhJ6TuO3rSAPO7Nrt2q1pIjPxvLtsm7Fj-C5oIML', '2020-11-05 13:34:43', '2020-11-05 13:42:16', 2, '+6281219936210', NULL, '', '', ''),
('USR-1120-0003', '', 'rpgsourcecode@gmail.com', 'c9efb59413916bcec44de205984c86b26ef0d4412e65bef7456ce3c756827b4051df86007fe03f7761901f0215301339c443f61c2ab11f46eaf9710e5e161f5dCNySCg4GndiIGxxvVByjE8xHpkvFf6mNu+MUMnGLGHI=', '', 'dTMQCgPPSZ2lzj1Es_bTRJ:APA91bERNZ3HUqKIUtwVPOPCFiIFcpgiegQ0BPfZaVl6le9ZIvZNSG3ImzsccAvKRKfZbIiZ_HTSAtiVwo_elt-6qmG6o4bZMjC23u2GW7qBpWddclDjVSsqL_FHpBr4kCPCNXtEG4N8', '2020-11-16 15:31:03', '2020-11-16 17:23:28', 2, '+6289685058781', NULL, '', '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_stores`
--

CREATE TABLE `user_stores` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` varchar(64) NOT NULL,
  `user_id` varchar(64) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `is_active` int(11) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `user_id`, `subscription_id`, `valid_from`, `valid_to`, `is_active`, `created_date`, `created_by`) VALUES
('SUBS-1120-0001', 'USR-1120-0001', 1, '2020-11-05', '2020-12-05', 1, '2020-11-05 13:11:02', NULL),
('SUBS-1120-0002', 'USR-1120-0002', 1, '2020-11-05', '2020-12-05', 1, '2020-11-05 13:34:43', NULL),
('SUBS-1120-0003', 'USR-1120-0003', 1, '2020-11-16', '2020-12-16', 1, '2020-11-16 15:31:03', NULL),
('SUBS-1120-0004', 'USR-1120-0004', 1, '2020-11-17', '2020-12-17', 1, '2020-11-17 13:16:06', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_type`
--

CREATE TABLE `user_type` (
  `id` int(11) NOT NULL,
  `type_code` varchar(10) DEFAULT NULL,
  `type_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_code`,`store_id`) USING BTREE;

--
-- Indeks untuk tabel `item_price`
--
ALTER TABLE `item_price`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `item_stock`
--
ALTER TABLE `item_stock`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `midtrans_status_code`
--
ALTER TABLE `midtrans_status_code`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`store_id`,`partner_code`) USING BTREE;

--
-- Indeks untuk tabel `partner_type`
--
ALTER TABLE `partner_type`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`store_id`) USING BTREE;

--
-- Indeks untuk tabel `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ticket_category`
--
ALTER TABLE `ticket_category`
  ADD PRIMARY KEY (`ticket_category_id`);

--
-- Indeks untuk tabel `ticket_detail`
--
ALTER TABLE `ticket_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaction_detail`
--
ALTER TABLE `transaction_detail`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `transaction_header`
--
ALTER TABLE `transaction_header`
  ADD PRIMARY KEY (`transaction_id`,`store_id`) USING BTREE;

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`) USING BTREE;

--
-- Indeks untuk tabel `user_stores`
--
ALTER TABLE `user_stores`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `item_price`
--
ALTER TABLE `item_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `item_stock`
--
ALTER TABLE `item_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `partner_type`
--
ALTER TABLE `partner_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `ticket_category`
--
ALTER TABLE `ticket_category`
  MODIFY `ticket_category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `ticket_detail`
--
ALTER TABLE `ticket_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transaction_detail`
--
ALTER TABLE `transaction_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `user_stores`
--
ALTER TABLE `user_stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
