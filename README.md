# Project Setup Guide

## Prerequisites
Before you begin, ensure you have the following installed on your local machine:
- [**XAMPP**](https://www.apachefriends.org/index.html) (or any other local server like WAMP, MAMP, etc.)
- XAMPP is an open-source web server solution package. It is mainly used for web application testing on a local host webserver.
XAMPP stands for:
X = Cross-platform
A = Apache Server
M = MariaDB
P = PHP
P = Perl
On completing the download of the setup file, begin the installation process and, in the “Select Components” section, select all the required components.

![image](https://github.com/user-attachments/assets/87332a04-5ae4-41b4-8cd8-62186ae80484)

Next, select the directory where you want the software to be installed. It is recommended that you keep the default directory “C:\xampp” and click on “next” to complete the installation

![image](https://github.com/user-attachments/assets/65e66106-5c0b-40d5-816d-aace1fd0e099)



## Step-by-Step Instructions

### 1. Clone the Repository OR download the source code
First, download the source code or clone the repository using Git.

**To clone the repository:**
```bash
https://github.com/ABCText-IndianLanguages/test2.git
```

### 2. Download the Source Code

- If you don not want to clone from github , you can download the source code as a ZIP file.
- Extract the ZIP file to your desired location.

### 3. Directory Structure
![image](https://github.com/user-attachments/assets/2f778b1f-cdc2-40d0-9832-3ce68e71ceea)

  In the XAMPP directory, there exists a folder called “htdocs”. This is where all the programs for the web pages will be stored.
   Now, to run a PHP script:
   
   Go to “C:\xampp\htdocs” and inside it, and move the source code downloaded here.

### Setup Instructions

### 4. Set Up the Database
![image](https://github.com/user-attachments/assets/f4b66c0f-c490-4867-bfe9-bc95acbd32d7)

Before you run or start writing any program in PHP, you should start Apache and MYSQL in xampp as shown above.

1. Open phpMyAdmin by navigating to `http://localhost/phpmyadmin` in your web browser.
     - Create a new database named **'textidc'** .
    - Import the SQL files provided in the databases folder into the textidc database:
      Click on the textidc database, go to the Import tab, and select the **'nusers.sql'** file from the databases folder. Click Go to import the file.
    - Repeat the process by importing the **'studies.sql'** file into the same textABC database.


**Open your web browser and navigate to http://localhost/IDCText**.
