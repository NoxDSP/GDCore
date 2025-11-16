-- GDCore Database Schema
-- Based on Cvolton's GMDprivateServer structure
-- Compatible with Geometry Dash 1.0 - 2.2

CREATE DATABASE IF NOT EXISTS gdcore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gdcore;

-- Users/Accounts Table
CREATE TABLE IF NOT EXISTS accounts (
    accountID INT AUTO_INCREMENT PRIMARY KEY,
    userName VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    secret VARCHAR(10) DEFAULT '',
    saveData TEXT,
    registerDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isActive TINYINT(1) DEFAULT 1,
    mS INT DEFAULT 0,
    frS INT DEFAULT 0,
    cS INT DEFAULT 0,
    youtubeurl VARCHAR(255) DEFAULT '',
    twitter VARCHAR(255) DEFAULT '',
    twitch VARCHAR(255) DEFAULT '',
    INDEX idx_username (userName),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Stats Table
CREATE TABLE IF NOT EXISTS users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    userName VARCHAR(255) NOT NULL,
    IP VARCHAR(45) DEFAULT '',
    stars INT DEFAULT 0,
    demons INT DEFAULT 0,
    coins INT DEFAULT 0,
    userCoins INT DEFAULT 0,
    icon INT DEFAULT 1,
    color1 INT DEFAULT 0,
    color2 INT DEFAULT 3,
    iconType INT DEFAULT 0,
    special INT DEFAULT 0,
    accIcon INT DEFAULT 1,
    accShip INT DEFAULT 1,
    accBall INT DEFAULT 1,
    accBird INT DEFAULT 1,
    accDart INT DEFAULT 1,
    accRobot INT DEFAULT 1,
    accSpider INT DEFAULT 1,
    accSwing INT DEFAULT 1,
    accGlow INT DEFAULT 0,
    accExplosion INT DEFAULT 1,
    creatorPoints INT DEFAULT 0,
    isBanned TINYINT(1) DEFAULT 0,
    isRegistered TINYINT(1) DEFAULT 0,
    registerDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    INDEX idx_account (accountID),
    INDEX idx_username (userName),
    INDEX idx_stars (stars),
    INDEX idx_cp (creatorPoints)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Levels Table
CREATE TABLE IF NOT EXISTS levels (
    levelID INT AUTO_INCREMENT PRIMARY KEY,
    levelName VARCHAR(255) NOT NULL,
    levelDesc TEXT,
    levelVersion INT DEFAULT 1,
    userID INT NOT NULL,
    userName VARCHAR(255) NOT NULL,
    accountID INT DEFAULT 0,
    difficulty INT DEFAULT 0,
    starDifficulty INT DEFAULT 0,
    downloads INT DEFAULT 0,
    likes INT DEFAULT 0,
    length INT DEFAULT 0,
    gameVersion INT DEFAULT 1,
    twoPlayer TINYINT(1) DEFAULT 0,
    songID INT DEFAULT 0,
    coins INT DEFAULT 0,
    starCoins TINYINT(1) DEFAULT 0,
    requestedStars INT DEFAULT 0,
    starDemon TINYINT(1) DEFAULT 0,
    starAuto TINYINT(1) DEFAULT 0,
    levelString LONGTEXT,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updateDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    originalLevel INT DEFAULT 0,
    objects INT DEFAULT 0,
    password INT DEFAULT 0,
    isLDM TINYINT(1) DEFAULT 0,
    rateDate TIMESTAMP NULL,
    featured TINYINT(1) DEFAULT 0,
    epic TINYINT(1) DEFAULT 0,
    starFeatured INT DEFAULT 0,
    unlisted TINYINT(1) DEFAULT 0,
    INDEX idx_user (userID),
    INDEX idx_featured (featured),
    INDEX idx_epic (epic),
    INDEX idx_difficulty (starDifficulty),
    INDEX idx_downloads (downloads),
    INDEX idx_likes (likes),
    INDEX idx_upload (uploadDate),
    INDEX idx_account (accountID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments Table
CREATE TABLE IF NOT EXISTS comments (
    commentID INT AUTO_INCREMENT PRIMARY KEY,
    levelID INT NOT NULL,
    userID INT NOT NULL,
    userName VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    likes INT DEFAULT 0,
    isSpam TINYINT(1) DEFAULT 0,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    percent INT DEFAULT 0,
    INDEX idx_level (levelID),
    INDEX idx_user (userID),
    INDEX idx_date (uploadDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Account Comments Table
CREATE TABLE IF NOT EXISTS acccomments (
    commentID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    userName VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    likes INT DEFAULT 0,
    isSpam TINYINT(1) DEFAULT 0,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    INDEX idx_account (accountID),
    INDEX idx_date (uploadDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Level Ratings Table
CREATE TABLE IF NOT EXISTS levelscores (
    scoreID INT AUTO_INCREMENT PRIMARY KEY,
    levelID INT NOT NULL,
    userID INT NOT NULL,
    accountID INT NOT NULL,
    stars INT DEFAULT 0,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_level_user (levelID, accountID),
    INDEX idx_level (levelID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Friend Requests Table
CREATE TABLE IF NOT EXISTS friendreqs (
    requestID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    toAccountID INT NOT NULL,
    comment TEXT,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isNew TINYINT(1) DEFAULT 1,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    FOREIGN KEY (toAccountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    UNIQUE KEY unique_friend_request (accountID, toAccountID),
    INDEX idx_to_account (toAccountID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Friends Table
CREATE TABLE IF NOT EXISTS friendships (
    friendshipID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    friendAccountID INT NOT NULL,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isNew TINYINT(1) DEFAULT 1,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    FOREIGN KEY (friendAccountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    UNIQUE KEY unique_friendship (accountID, friendAccountID),
    INDEX idx_account (accountID),
    INDEX idx_friend (friendAccountID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages Table
CREATE TABLE IF NOT EXISTS messages (
    messageID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    toAccountID INT NOT NULL,
    userName VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isNew TINYINT(1) DEFAULT 1,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    FOREIGN KEY (toAccountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    INDEX idx_to_account (toAccountID),
    INDEX idx_from_account (accountID),
    INDEX idx_new (isNew)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Level Lists Table
CREATE TABLE IF NOT EXISTS levelLists (
    listID INT AUTO_INCREMENT PRIMARY KEY,
    listName VARCHAR(255) NOT NULL,
    listDesc TEXT,
    accountID INT NOT NULL,
    userName VARCHAR(255) NOT NULL,
    difficulty INT DEFAULT 0,
    likes INT DEFAULT 0,
    downloads INT DEFAULT 0,
    levels TEXT NOT NULL,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updateDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    unlisted TINYINT(1) DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    INDEX idx_account (accountID),
    INDEX idx_featured (featured),
    INDEX idx_likes (likes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Songs Table (Custom songs)
CREATE TABLE IF NOT EXISTS songs (
    songID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    authorID INT DEFAULT 0,
    authorName VARCHAR(255) NOT NULL,
    size VARCHAR(50) DEFAULT '0',
    download VARCHAR(512) NOT NULL,
    uploadDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isDisabled TINYINT(1) DEFAULT 0,
    INDEX idx_author (authorID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily Levels Table
CREATE TABLE IF NOT EXISTS dailylevels (
    dailyID INT AUTO_INCREMENT PRIMARY KEY,
    levelID INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    type INT DEFAULT 0,
    INDEX idx_level (levelID),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gauntlets Table
CREATE TABLE IF NOT EXISTS gauntlets (
    gauntletID INT AUTO_INCREMENT PRIMARY KEY,
    level1 INT DEFAULT 0,
    level2 INT DEFAULT 0,
    level3 INT DEFAULT 0,
    level4 INT DEFAULT 0,
    level5 INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Map Packs Table
CREATE TABLE IF NOT EXISTS mappacks (
    packID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    levels VARCHAR(255) NOT NULL,
    stars INT DEFAULT 0,
    coins INT DEFAULT 0,
    difficulty INT DEFAULT 0,
    rgbcolors VARCHAR(20) DEFAULT '255,255,255'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bans Table
CREATE TABLE IF NOT EXISTS bans (
    banID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    reason TEXT,
    expireDate TIMESTAMP NULL,
    banDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    INDEX idx_account (accountID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Actions Log Table
CREATE TABLE IF NOT EXISTS actions (
    actionID INT AUTO_INCREMENT PRIMARY KEY,
    type INT NOT NULL,
    accountID INT NOT NULL,
    value VARCHAR(255) DEFAULT '',
    value2 VARCHAR(255) DEFAULT '',
    value3 VARCHAR(255) DEFAULT '',
    value4 VARCHAR(255) DEFAULT '',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_account (accountID),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mod Actions Table  
CREATE TABLE IF NOT EXISTS modactions (
    actionID INT AUTO_INCREMENT PRIMARY KEY,
    type INT NOT NULL,
    accountID INT NOT NULL,
    value VARCHAR(255) DEFAULT '',
    value2 VARCHAR(255) DEFAULT '',
    value3 VARCHAR(255) DEFAULT '',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_account (accountID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles Table (for permissions)
CREATE TABLE IF NOT EXISTS roles (
    roleID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    roleType INT NOT NULL,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    UNIQUE KEY unique_account_role (accountID, roleType)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quests Table
CREATE TABLE IF NOT EXISTS quests (
    questID INT AUTO_INCREMENT PRIMARY KEY,
    type INT NOT NULL,
    amount INT NOT NULL,
    reward INT NOT NULL,
    name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Quests Progress
CREATE TABLE IF NOT EXISTS userquests (
    userQuestID INT AUTO_INCREMENT PRIMARY KEY,
    accountID INT NOT NULL,
    questID INT NOT NULL,
    progress INT DEFAULT 0,
    completed TINYINT(1) DEFAULT 0,
    FOREIGN KEY (accountID) REFERENCES accounts(accountID) ON DELETE CASCADE,
    UNIQUE KEY unique_user_quest (accountID, questID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin Account (password: admin123 - CHANGE THIS!)
INSERT INTO accounts (userName, password, email, isActive) 
VALUES ('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMye2QZLGy8UR5PmkE3RQJ8t9PZVlqZ8oPO', 'admin@gdcore.local', 1);

INSERT INTO users (accountID, userName, stars, demons, creatorPoints, isRegistered)
VALUES (1, 'admin', 0, 0, 0, 1);

INSERT INTO roles (accountID, roleType) VALUES (1, 2); -- 2 = Admin
