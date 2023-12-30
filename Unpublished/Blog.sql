USE u403841495_blog;

CREATE TABLE categories (
    category_id int auto_increment PRIMARY KEY,
    parent_category int,
    category_name varchar(20) NOT NULL,
    FOREIGN KEY (parent_category) REFERENCES categories(category_id)
);

CREATE TABLE roles (
    role_id int auto_increment PRIMARY KEY,
    role_name varchar(15)
) AUTO_INCREMENT=1;

INSERT INTO roles(role_name) VALUES ('User');
INSERT INTO roles(role_name) VALUES ('Author');
INSERT INTO roles(role_name) VALUES ('Admin');

CREATE TABLE themes (
    theme_id int auto_increment PRIMARY KEY,
    name varchar(20),
    url varchar(50)
) AUTO_INCREMENT=1;

INSERT INTO themes(name, url) VALUES ('Default', './css/default.css');

CREATE TABLE users (
    user_id int auto_increment PRIMARY KEY,
    username varchar(25) NOT NULL,
    display_name varchar(25),
    full_name varchar(30),
    email varchar(30) NOT NULL,
    role_id int DEFAULT 1,
    email_validated boolean,
    theme_id int DEFAULT 1,
    password varchar(255),
    FOREIGN KEY (role_id) REFERENCES roles(role_id),
    FOREIGN KEY (theme_id) REFERENCES themes(theme_id)
) AUTO_INCREMENT=1;

CREATE UNIQUE INDEX idx_username ON users(username);

CREATE TABLE posts (
    post_id int auto_increment PRIMARY KEY,
    user_id int,
    publish_time timestamp NOT NULL,
    unpublish_time timestamp,
    title varchar(40) NOT NULL,
    content longtext NOT NULL,
    closed_for_comments boolean,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) AUTO_INCREMENT=1;

CREATE TABLE posts_categories (
    post_id int,
    category_id int,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE comments (
    comment_id int auto_increment PRIMARY KEY,
    post_id int NOT NULL,
    reply_to int,
    title varchar(40) NOT NULL,
    content text NOT NULL,
    user_id int NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (reply_to) REFERENCES comments(comment_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE tokens (
    token_id int auto_increment PRIMARY KEY,
    user_id int NOT NULL,
    value varchar(50),
    valid_until datetime,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) AUTO_INCREMENT=1;