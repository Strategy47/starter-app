# Angular Symfony API Platform with Ionic Starter Project

Welcome to the Angular Symfony API Platform with Ionic Starter Project! This project provides a foundation for building modern web and mobile applications using Angular for the frontend, Symfony API Platform for the backend, Ionic for mobile development, and integrating Ngrx for state management and JWT for authentication.

## Features

- Angular frontend with responsive design.
- Symfony API Platform backend for creating RESTful APIs.
- Ionic for mobile app development.
- Ngrx for state management in Angular.
- JWT (JSON Web Tokens) for authentication and authorization.
- Easy setup and configuration for rapid development.

## Getting Started

Follow these steps to get started with the project:

1. Clone the repository to your local machine:

   ```bash
   git clone https://github.com/Strategy47/starter-app.git
   ```

2. Navigate to the project directory:

   ```bash
   cd starter-app
   ```

3. install dependencies for both Angular frontend and Symfony backend:

   ```bash
    cd frontend
    npm install
    cd ../backend
    composer install
   ```
   
4. Configure the backend:
   * Set up your database connection in the .env file.
   * Run migrations to create the database schema:
   
    ```bash
   docker-compose up -d
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load
   symfony serve
   ```

5. Start the Ionic app:

    ```bash
    cd ../front
    ionic serve
   ```

6. Open your browser and navigate to http://localhost:8100 to see the Ionic app.

## Project Structure

- `frontend/`: Contains the Angular frontend code.
- `backend/`: Contains the Symfony API Platform backend code.

## Contributing

We welcome contributions to improve this starter project. If you have any ideas, suggestions, or bug fixes, please feel free to open an issue or submit a pull request.

---

Happy coding! 🚀 We hope this starter project helps you build amazing web and mobile applications with Angular, Symfony, and Ionic! If you have any questions or need further assistance, please don't hesitate to reach out.**
