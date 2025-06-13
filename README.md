# Smart Building API

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Redis](https://img.shields.io/badge/redis-%23DD0031.svg?style=for-the-badge&logo=redis&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

---

## 📝 Project Description

This project provides a robust API for managing sensors and visitor data within a smart building environment. It's built with **Laravel** and utilizes **Docker** for easy setup and deployment, along with **Redis** for efficient caching of API responses and aggregated data. The API is designed to deliver fast and reliable data access for various smart building applications.

---

## ✨ Features

* **Location Management:** Integration with physical locations, allowing sensors and visitor records to be associated with specific areas within the building.
* **Sensor Management:** Complete CRUD operations for sensor devices (e.g., camera, wifi, beacons).
* **Visitor Management:** Complete CRUD operations for daily visitor counts (in, out, pass-by).
* **Application Summary:** An aggregated endpoint providing real-time summaries of sensor statuses and recent visitor activity.
* **API Caching:** Leverages **Redis** to cache frequently accessed data, significantly improving response times and reducing database load.
    * **Tagged Caching:** Sensor and Visitor lists are cached using tags for efficient mass invalidation.
    * **Specific Key Caching:** The application summary is cached with a dedicated key for precise control.
* **Cache Invalidation:** Automated cache invalidation via **Laravel Observers** ensures data consistency across the API.
* **Standardized API Responses:** Utilizes a custom **API Response trait** for consistent JSON formatting across all endpoints.
* **Dockerized Environment:** Ensures easy setup and a consistent development/production environment using **Docker Compose**.

---

## 🛠️ Technologies Used

* **Laravel Framework**: PHP framework for web artisans.
* **PHP**: Backend programming language.
* **MySQL**: Relational database for persistent data storage.
* **Redis**: In-memory data store used for caching.
* **Docker & Docker Compose**: For containerization and environment orchestration.

---

## 🚀 Getting Started

Follow these steps to get the project up and running on your local machine.

### Prerequisites

* **Docker**: [Install Docker Desktop](https://www.docker.com/products/docker-desktop) (includes Docker Compose).

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/ismailkm/visitor-analytics-api.git
    cd visitor-analytics-api
    ```

2.  **Create `.env` file:**
    Copy the example environment file.
    ```bash
    cp .env.example .env
    ```
    **Note:** Ensure your `.env` file's `DB_HOST` is set to `mysql` and `REDIS_HOST` is set to `redis` to match the service names defined in `docker-compose.yml`.

3.  **Build and Start Docker Containers:**
    This command will build the Docker images (if not already built) and start your application, database, and Redis containers in the background.
    ```bash
    docker-compose up -d --build
    ```

4.  **Install Composer Dependencies:**
    Once the `app` container is running, execute Composer commands inside it.
    ```bash
    docker-compose exec app composer install
    ```

5.  **Generate Application Key:**
    ```bash
    docker-compose exec app php artisan key:generate
    ```

6.  **Run Database Migrations:**
    This will create the necessary tables in your MySQL database.
    ```bash
    docker-compose exec app php artisan migrate
    ```

7.  **Seed the Database (Optional):**
    If you have seeders for dummy data, run them:
    ```bash
    docker-compose exec app php artisan db:seed
    ```

Your application should now be accessible.

---

## 🌐 API Documentation

All API endpoints are documented and available via a Postman Collection.

### Accessing the API

Once your Docker containers are up, the API should be available via your web browser or API client (like Postman/Insomnia) at the following base URL:

`http://localhost:8000/api/v1/`

### Importing Postman Collection

To explore and test the API endpoints:

1.  **Download Postman** (if you haven't already): [Postman Official Website](https://www.postman.com/downloads/)
2.  **Download the Postman Collection JSON file:**
    You will find the collection file named `Smart_Building_API_Collection.json` in the root of this repository.
3.  **Import into Postman:**
    * Open Postman.
    * Click on the **"Import"** button in the top left corner.
    * Select the downloaded `Smart_Building_API_Collection.json` file.
    * The collection will appear in your sidebar.

---

## 🛑 Stopping the Application

To stop and remove the Docker containers, networks, and volumes (for a clean slate):

```bash
docker-compose down -v
