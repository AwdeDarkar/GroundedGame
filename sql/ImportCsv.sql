TRUNCATE TABLE Resources;
TRUNCATE TABLE Processes;
TRUNCATE TABLE ProcessComponents;

LOAD DATA INFILE 'resources.csv'
	INTO TABLE Resources
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
	LINES TERMINATED BY '\n'
	(ID,Name,NameSafe,Type,Frequency,Description);

LOAD DATA INFILE 'process_components.csv'
	INTO TABLE ProcessComponents
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
	LINES TERMINATED BY '\n'
	(PID,RID,Amount,Type);

LOAD DATA INFILE 'processes.csv'
	INTO TABLE Processes
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
	LINES TERMINATED BY '\n'
	(ID,Name,NameSafe,BaseTime);
