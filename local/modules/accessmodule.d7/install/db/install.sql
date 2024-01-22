/*
 * Таблица со свсеми типами смарт-процессов
 * (обновляются автоматически)
 *
 * И стандартными сущностями Лид, Компания, Контакт, Сделка
 * (добавлены вручную заранее)
 */
CREATE TABLE `accessmodule_entity_type` (
                                            `ID` INT(11) NOT NULL,
                                            `NAME` VARCHAR(50) NOT NULL,
                                            `STANDARD_TYPE` BOOL DEFAULT FALSE,
                                            PRIMARY KEY(ID)
);

/*
 * Типы доступов к полям/областям
 */
CREATE TABLE `accessmodule_access_type` (
                                            `ID` INT(11) NOT NULL,
                                            `NAME` VARCHAR(50) NOT NULL,
                                            PRIMARY KEY(ID)
);

/* Таблица для хранения всех полей (стандартные/пользовательские)
 * и доступа ролей к ним
 */
CREATE TABLE `accessmodule_field` (
                                            `ID` INT(11) NOT NULL,
                                            `NAME` VARCHAR(50) NOT NULL,
                                            `LANG_NAME` VARCHAR(255) NOT NULL,
                                            `ENTITY_TYPE_ID` INT(11) NOT NULL,
                                            `STANDARD_TYPE` BOOL DEFAULT FALSE,
                                            `ROLE_ALL_USERS` INT(11) DEFAULT 2,
                                            PRIMARY KEY(ID),
                                            FOREIGN KEY (ENTITY_TYPE_ID) REFERENCES accessmodule_entity_type (ID)
                                                ON UPDATE CASCADE
                                                ON DELETE CASCADE,
                                            FOREIGN KEY (ROLE_ALL_USERS) REFERENCES accessmodule_access_type (ID)
                                                ON DELETE CASCADE
);


/*
 * Роли
 */
CREATE TABLE `accessmodule_role` (
                                            `ID` INT(11) AUTO_INCREMENT,
                                            `NAME` VARCHAR(50) NOT NULL UNIQUE,
                                            `LANG_NAME` VARCHAR(255) NOT NULL UNIQUE,
                                            PRIMARY KEY(ID)
);

/*
 * Соответствия Роль - Группа пользователей
 */
CREATE TABLE `accessmodule_role_group` (
                                            `ID` INT(11) NOT NULL AUTO_INCREMENT,
                                            `ROLE_ID` INT(11) NOT NULL,
                                            `GROUP_ID` INT(11) NOT NULL,
                                            PRIMARY KEY(ID),
                                            UNIQUE (ROLE_ID, GROUP_ID),
                                            FOREIGN KEY (ROLE_ID) REFERENCES accessmodule_role (ID)
                                                ON DELETE CASCADE
);

/*
 * Соответствия Роль - Пользователь
 */
CREATE TABLE `accessmodule_role_user` (
                                            `ID` INT(11) NOT NULL AUTO_INCREMENT,
                                            `ROLE_ID` INT(11) NOT NULL,
                                            `USER_ID` INT(11) NOT NULL,
                                            PRIMARY KEY(ID),
                                            UNIQUE (ROLE_ID, USER_ID),
                                            FOREIGN KEY (ROLE_ID) REFERENCES accessmodule_role (ID)
                                                ON DELETE CASCADE
);

/*
 * Области сайта
 */
CREATE TABLE `accessmodule_area` (
                                            `ID` INT(11) NOT NULL AUTO_INCREMENT,
                                            `NAME` VARCHAR(50) NOT NULL UNIQUE,
                                            `LANG_NAME` VARCHAR(255) NOT NULL UNIQUE,
                                            `COMMENT` VARCHAR(65536),
                                            `ROLE_ALL_USERS` INT(11) DEFAULT 2,
                                            PRIMARY KEY(ID),
                                            FOREIGN KEY (ROLE_ALL_USERS) REFERENCES accessmodule_access_type (ID)
                                                ON DELETE CASCADE
);


# Добавляем все стандартные типы доступа
insert  into `accessmodule_access_type` (`ID`, `NAME`) values (1, 'Наследовать'),
                                                              (2, 'Все'),
                                                              (3, 'Свои + Отделов + Подотделов'),
                                                              (4, 'Свои + Отделов'),
                                                              (5, 'Свои'),
                                                              (6, 'Нет доступа');

# Добавляем первую дефолтную роль Все пользователи
insert  into `accessmodule_role` (`ID`, `NAME`, `LANG_NAME`) values (1, 'ROLE_ALL_USERS', 'Все пользователи');

# Добавляем первую связку Роль - Группа
insert  into `accessmodule_role_group` (`ROLE_ID`, `GROUP_ID`) values (1, 2);

# Добавляем все стандартные типы доступа
insert  into `accessmodule_entity_type` (`ID`, `NAME`, `STANDARD_TYPE`) values
                                                                (1, 'Лид', true),
                                                                (2, 'Сделка', true),
                                                                (3, 'Контакт', true),
                                                                (4, 'Компания', true);

# Добавляем все стандартные типы доступа
insert  into `accessmodule_field` (`ID`, `NAME`, `LANG_NAME`, `ENTITY_TYPE_ID`, `STANDARD_TYPE`) values
                                                                (99001, 'ASSIGNED_BY_ID', 'Ответственный', 1, true),
                                                                (99002, 'ASSIGNED_BY_ID', 'Ответственный', 2, true),
                                                                (99003, 'ASSIGNED_BY_ID', 'Ответственный', 3, true),
                                                                (99004, 'ASSIGNED_BY_ID', 'Ответственный', 4, true);