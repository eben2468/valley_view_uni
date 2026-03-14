-- Radio Page Content
CREATE TABLE IF NOT EXISTS `radio_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) DEFAULT 'Valley View Radio',
  `hero_subtitle` text,
  `hero_image` varchar(255) DEFAULT 'images/vvu_radio_hero_bg.png',
  `live_on_air_text` varchar(100) DEFAULT 'Live On Air',
  
  -- Live Player
  `now_playing_heading` varchar(255) DEFAULT 'Now Playing',
  `current_show` varchar(255) DEFAULT 'The Morning Rise',
  `current_host` varchar(255) DEFAULT 'DJ Grace & Bro. Samuel',
  `current_show_image` varchar(255) DEFAULT 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
  `next_show_time` varchar(100) DEFAULT 'Campus Pulse @ 10:00 AM',
  `frequency` varchar(50) DEFAULT '97.7 FM',
  
  -- About Section
  `about_heading` varchar(255) DEFAULT 'About Valley View Radio',
  `about_text` text,
  `about_image_1` varchar(255),
  `about_image_2` varchar(255),
  `about_image_3` varchar(255),
  `about_image_4` varchar(255),
  
  -- Program Highlights
  `programs_heading` varchar(255) DEFAULT 'Program Highlights',
  `programs_text` text,
  
  -- CTA
  `cta_heading` varchar(255) DEFAULT 'Join the Conversation',
  `cta_text` text,
  `cta_phone` varchar(50) DEFAULT '+233 307 011 832',
  `cta_email` varchar(255) DEFAULT 'radio@vvu.edu.gh',
  
  -- Contact Info
  `location_text` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Radio Programs
CREATE TABLE IF NOT EXISTS `radio_programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `schedule` varchar(255),
  `description` text,
  `icon` varchar(100) DEFAULT 'radio',
  `border_color` varchar(50) DEFAULT 'purple-600',
  `icon_bg_color` varchar(50) DEFAULT 'purple-600',
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Radio Features (About section)
CREATE TABLE IF NOT EXISTS `radio_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon` varchar(100) DEFAULT 'school',
  `color_class` varchar(100) DEFAULT 'purple',
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Data
INSERT INTO `radio_content` (
  `hero_title`, `hero_subtitle`, `hero_image`, 
  `about_text`,
  `programs_text`,
  `cta_text`,
  `location_text`
) VALUES (
  'Valley View Radio 97.7 FM', 
  '"Voice of the Valley — Your #1 Campus Station for Music, News, and Spiritual Inspiration."', 
  'images/vvu_radio_hero_bg.png',
  'Valley View Radio is the heartbeat of our campus, broadcasting 24/7 to provide a unique blend of educational content, spiritual nourishment, and the best in contemporary and traditional music.',
  'Tune in to our most popular shows throughout the week.',
  'Want to request a song, share a shoutout, or join our team of presenters? We\'d love to hear from you!',
  'Mile 19 Off the Adenta-Dodowa Road, Oyibi, Accra'
);

INSERT INTO `radio_features` (`title`, `icon`, `color_class`, `display_order`) VALUES
('Student-Led Broadcasting', 'school', 'purple', 1),
('Spiritual & Moral Values', 'volunteer_activism', 'pink', 2),
('Global Online Reach', 'public', 'orange', 3);

INSERT INTO `radio_programs` (`title`, `schedule`, `description`, `icon`, `border_color`, `icon_bg_color`, `display_order`) VALUES
('The Morning Rise', 'Mon - Fri | 6:00 AM', 'A perfect blend of morning devotion, news updates, and uplifting melodies.', 'wb_sunny', 'purple-600', 'purple-600', 1),
('Campus Pulse', 'Mon - Wed | 10:00 AM', 'Engaging talk shows focusing on student life, academics, and campus events.', 'podcasts', 'pink-600', 'pink-600', 2),
('Evening Chill', 'Daily | 8:00 PM', 'Wind down with smooth jazz, soul, and relaxing conversations under the stars.', 'nightlight', 'orange-600', 'orange-600', 3),
('Gospel Hour', 'Sundays | 7:00 AM', 'A dedicated time for spiritual growth through powerful sermons and gospel music.', 'menu_book', 'blue-600', 'blue-600', 4);
