
CREATE DATABASE IF NOT EXISTS vk_tool;

CREATE TABLE IF NOT EXISTS youtube_videos (id VARCHAR(32) NOT NULL PRIMARY KEY, playlist_title VARCHAR(128), video_title VARCHAR(128), description TEXT, sitename ENUM ('russianpod101', 'arabicpod101', 'thaipod101'));

ALTER TABLE youtube_videos DROP PRIMARY KEY, ADD PRIMARY KEY USING HASH (id);

ALTER TABLE youtube_videos ADD COLUMN playlist_id VARCHAR(32);
