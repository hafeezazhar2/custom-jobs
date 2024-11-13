# Laravel Background Job Processor

A Laravel-based background job processing system with custom queue management. This application allows you to add, process, and retry background jobs, with easy monitoring and logging.

## System Requirements

- **PHP**: v8.2.24
- **MySQL**: v8
- **Laravel**: v11.30.0
- **GIT**
- **COMPOSER**
- **Bootstrap**

## Project Setup

### Environment Configuration

Update the `.env` file with your database credentials:

```plaintext
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=*****
DB_USERNAME=*****
DB_PASSWORD=*****

## To specify the path for backend workers:

    Linux: APP_PATH=/path/to/job-runner
    Windows: APP_PATH=D:/home/hafeez/job-runner
