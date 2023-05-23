<p align="center">
    <h1 align="center">LMS-ST</h1>
</p>

LMS-ST is Learning Management System for school management, student, and teacher that build with Laravel, VueJS, Tailwind, MySQL, HTML, and CSS.

FEATURE
-------------------
1. Class group
2. Course
3. Attendance
4. Forum
5. Report

DEPLOYMENT
-------------------
1. Clone this repository ```git clone git@github.com:syauqifut/lms-st.git```
2. Change directory to installation folder ```cd lms-st```
3. Import ```lms.sql``` in database (e.g. phpmyadmin)
4. create env file ```cp .env.example .env```
5. Install composer ```composer install```
6. Install NPM ```npm install```
7. Run NPM ```npm run dev```
8. Genereate App Key ```php artisan key:generate```
9. Run the application with (choose one):
    - Artisan ```php artisan serve```
    - Web service application (e.g. XAMPP, Laragon)
    - Web service configuration (e.g. Nginx, Apache)
