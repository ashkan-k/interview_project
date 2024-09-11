# پروژه Laravel با MySQL و Docker

این پروژه یک برنامه وب مبتنی بر لاراول است که از MySQL به عنوان پایگاه داده استفاده می‌کند و با استفاده از Docker کانتینرهای مورد نیاز برای اجرای پروژه را راه‌اندازی می‌کند.

## پیش‌نیازها

برای اجرای این پروژه مطمئن شوید که موارد زیر روی سیستم شما نصب شده است:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)
- [Composer](https://getcomposer.org/)

## راه‌اندازی پروژه

1. کلون کردن مخزن:

    ```bash
    git clone https://github.com/your-repo/your-project.git
    cd interview_project
    ```

2. فایل .env-example را کپی کرده و پیکربندی کنید:

    ```bash
    cp .env.example .env
    ```

   مطمئن شوید که اطلاعات پایگاه داده و سایر جزئیات مورد نیاز را در فایل `.env` تنظیم کرده‌اید.

3. تولید کلید برنامه:

    ```bash
    docker-compose exec app php artisan key:generate
    ```

4. ساخت و اجرای کانتینرهای Docker:

    ```bash
    docker-compose up -d
    ```

   این دستور سرویس‌های زیر را راه‌اندازی می‌کند:

    - **PHP-FPM**: برای اجرای برنامه لاراول.
    - **MySQL**: به عنوان سرویس پایگاه داده.
    - **Nginx**: به عنوان وب سرور.
    - Migrate کردن دیتابیس.

[comment]: <> (5. اجرای مهاجرت‌های پایگاه داده:)

[comment]: <> (    ```bash)

[comment]: <> (    docker-compose exec app php artisan migrate)

[comment]: <> (    ```)

8. دسترسی به برنامه:

   پس از راه‌اندازی کانتینرها، برنامه لاراول شما در آدرس زیر در دسترس خواهد بود:

    ```
    http://localhost:8000
    ```

## ساختار پروژه

- **app/**: شامل کد اصلی برنامه.
- **config/**: فایل‌های پیکربندی لاراول و سرویس‌های شخص ثالث.
- **database/**: مهاجرت‌ها و seeders برای راه‌اندازی پایگاه داده.
- **docker/**: فایل‌های پیکربندی Docker برای پروژه.
- **routes/**: مسیرهای وب و API.
- **resources/**: ویوهای Blade و منابع فرانت‌اند.
