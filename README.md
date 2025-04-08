# REST API Documentation

## Project Overview
This Symfony-based REST API provides user management functionality with JWT authentication.

## API Endpoints

### Authentication

#### Login
- **URL**: `/api/login`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "password123"
  }
  ```
- **Success Response**:
  ```json
  {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
  ```
- **Error Response**: `401 Unauthorized`

### User Management

#### Create User (Admin only)
- **URL**: `/api/users`
- **Method**: `POST`
- **Authorization**: Bearer Token (Admin)
- **Request Body**:
  ```json
  {
    "email": "new@example.com",
    "password": "securepassword",
    "name": "New User",
    "roles": ["ROLE_USER"]
  }
  ```
- **Success Response**: `201 Created`
- **Error Response**: `400 Bad Request`

#### Get User Info
- **URL**: `/api/users/{id}`
- **Method**: `GET`
- **Authorization**: Bearer Token
- **Success Response**:
  ```json
  {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe",
    "roles": ["ROLE_USER"]
  }
  ```
- **Error Responses**:
  - `404 Not Found` - User not found
  - `403 Forbidden` - Not authorized

#### Update User
- **URL**: `/api/users/{id}`
- **Method**: `PUT`
- **Authorization**: Bearer Token (Owner or Admin)
- **Request Body**:
  ```json
  {
    "email": "updated@example.com",
    "name": "Updated Name"
  }
  ```
- **Success Response**: `200 OK` with updated user data
- **Error Responses**:
  - `403 Forbidden` - Not authorized
  - `400 Bad Request` - Invalid data

#### Delete User
- **URL**: `/api/users/{id}`
- **Method**: `DELETE`
- **Authorization**: Bearer Token (Owner or Admin)
- **Success Response**: `204 No Content`
- **Error Response**: `403 Forbidden`

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/your-repo.git
   cd your-repo
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Configure environment:
   ```bash
   cp .env .env.local
   ```
   Edit `.env.local` with your database credentials

4. Generate JWT keys:
   ```bash
   mkdir -p config/jwt
   openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
   openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
   ```

5. Set up database:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. Create admin user:
   ```bash
   php bin/console app:create-admin
   ```

## Environment Variables

| Variable | Description |
|----------|-------------|
| `DATABASE_URL` | Database connection string |
| `JWT_SECRET_KEY` | Path to JWT private key |
| `JWT_PUBLIC_KEY` | Path to JWT public key |
| `JWT_PASSPHRASE` | Passphrase for JWT key |

## License

This project is licensed under the MIT License.
