-- app db schema

create table if not exists user (
	id integer not null auto_increment,
	username varchar(30) not null,
	password varchar(30) not null,
	name varchar(30) not null,
	level varchar(30) not null default 'user',
	avatar varchar(30) null default null,
	primary key (id)
) engine = myisam;