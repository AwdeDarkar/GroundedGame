# copyright 2017 AWDE
# date created: 12/26/2017
# date edited: 12/27/2017

drop table if exists Users;
drop table if exists Worlds;
drop table if exists Factions;
drop table if exists Bunkers;
drop table if exists Entities;
drop table if exists Resources;
drop table if exists ResourceCollections;
drop table if exists ResourceDeposits;
drop table if exists Actors;
drop table if exists Equipment;
drop table if exists Rooms;
drop table if exists SurfaceEntities;
drop table if exists News;
drop table if exists ProductionJobs;
drop table if exists ProductionJobComponents;
drop table if exists Orders;
drop table if exists Transactions;
drop table if exists MessageGroups;
drop table if exists MessageGroupParticipants;
drop table if exists Messages;
drop table if exists Processes;
drop table if exists ProcessComponents;


create table Users (
	ID int unsigned primary key auto_increment,
	Name char(30),
	Hash char(128),
	Verification char(128),
	Email char(200),
	DateJoined date,
	Level tinyint,
	NameSafe char(30)
);

create table Worlds (
	ID int unsigned primary key auto_increment,
	Status tinyint,
	Created date,
	Name char(30),
	NameSafe char(30)
);

create table Factions (
	ID int unsigned primary key auto_increment,
	UserID int unsigned,
	WorldID int unsigned,
	Joined date,
	Name char(30), 
	NameSafe char(30)
);

create table Bunkers (
	ID int unsigned primary key auto_increment,
	WorldID int unsigned,
	FactionID int unsigned,
	WorldX int,
	WorldY int
);

create table Entities (
	ID int unsigned primary key auto_increment,
	FactionID int unsigned
);

create table Resources (
	ID int unsigned primary key auto_increment,
	Name char(30),
	NameSafe char(30),
	Type char(30),
	Frequency int,
	Description text
);

create table ResourceCollections (
	ID int unsigned primary key auto_increment,
	EntityID int unsigned,
	BunkerID int unsigned,
	FactionID int unsigned,
	ResourceID int unsigned,
	Amount int unsigned
);

create table ResourceDeposits (
	ID int unsigned primary key auto_increment,
	BunkerID int unsigned,
	ResourceID int unsigned,
	Amount int unsigned,
	ReplenishRate int unsigned,
	Maximum int unsigned
);

create table Actors (
	ID int unsigned primary key auto_increment,
	ResourceID int unsigned,
	RCID int unsigned,
	Hitpoints int unsigned,
	Experience int unsigned,
	Type int unsigned
);

create table Equipment (
	ID int unsigned primary key auto_increment,
	RCID int unsigned,
	ResourceID int unsigned
);

create table Rooms (
	EntityID int unsigned primary key,
	BunkerID int unsigned,
	Name char(30),
	NameSafe char(30),
	GridX int,
	GridY int,
	Width int,
	Height int,
	ConnUp bool,
	ConnDown bool,
	ConnRight bool,
	ConnLeft bool
);

create table SurfaceEntities (
	EntityID int unsigned primary key,
	WorldX int,
	WorldY int,
	WorldXDest int,
	WorldYDest int,
	StartTime datetime
);

create table News (
	ID int unsigned primary key auto_increment,
	FactionID int unsigned,
	Title char(30),
	Content text,
	PostDate date
);

create table ProductionJobs (
	ID int unsigned primary key auto_increment,
	FactionID int unsigned,
	StartDate datetime,
	ProcessID int unsigned
);

create table ProductionJobComponents (
	ID int unsigned,
	PJID int unsigned,
	PCID int unsigned,
	RCID int unsigned,
	AID int unsigned,
	EID int unsigned,
	Amount int unsigned
);

create table Processes (
	ID int unsigned primary key auto_increment,
	BaseTime int unsigned
);

create table ProcessComponents (
	ID int unsigned,
	PID int unsigned,
	RID int unsigned,
	Amount int unsigned,
	Type int unsigned
);

create table Orders (
	ID int unsigned primary key auto_increment,
	RCID int unsigned,
	AmountRemaining int unsigned,
	Cost int unsigned,
	Status tinyint,
	DatePosted datetime
);

create table Transactions (
	ID int unsigned primary key auto_increment,
	RCID int unsigned,
	Amount int unsigned,
	Cost int unsigned,
	Status tinyint,
	SellingFactionID int unsigned,
	BuyingFactionID int unsigned
);

create table MessageGroups (
	ID int unsigned primary key auto_increment,
	WorldID int unsigned
);

create table MessageGroupParticipants (
	MGID int unsigned,
	FactionID int unsigned,
	primary key(MGID, FactionID)
);

create table Messages (
	SrcFactionID int unsigned,
	MGID int unsigned,
	DateSent datetime,
	Content text,
	primary key(SrcFactionID, MGID, DateSent)
);
