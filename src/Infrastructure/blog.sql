
create database blog;

use blog;

create table user (
    id int primary key auto_increment, 
    name varchar(50) not null, 
    surname varchar(150) not null, 
    email varchar(150) not null
);

/** 1 x N */
create table category (
    id int primary key auto_increment, 
    title varchar(150) not null, 
    description varchar(255) not null, 
    fk_category int
);

/** 1 x N */
create table post (
    id int primary key auto_increment, 
    title varchar(255) not null, 
    description varchar(255) not null, 
    content text not null, 
    fk_author int not null, 
    fk_category int not null
);

/** 0 x 1 */
create table comment (
    id int primary key auto_increment, 
    comment varchar(255) not null, 
    fk_post int not null, 
    fk_user int not null
);

alter table category add constraint fk_category 
foreign key(fk_category) references category(id);

alter table post add constraint fk_category_post
foreign key(fk_category) references category(id);

alter table comment add constraint fk_post_comment
foreign key(fk_post) references post(id);

alter table comment add constraint fk_user_comment 
foreign key(fk_user) references user(id);
