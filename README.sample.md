# Byte_Learn: Educational Short-Form Video Content
> Learning bit-by-bit

Byte_Learn is a website focused on serving educational short-form video content and connecting everyone with a desire to learn!

## Installing / Getting started

A quick introduction of the minimal setup you need to get everything up an running.

1. Clone the repository
2. Make sure Docker is running
3. Go to the backend folder, run:
```shell
composer install
./vendor/bin/sail up
```
4. Go to the frontend folder, run:
```shell
npm install
npx ng serve
```
5. Everything should be running and served at http://localhost:80

These steps will get all necessary packages for Laravel and Angular, respectively, then run the Docker container via Sail to make sure everything is running.

## Developing

### Built With
- Frontend
  - Angular (TypeScript)
  - Tailwind
  - Angular Material CDK
  - Cypress (testing)
  - npm (package management)
- Backend
  - Laravel
  - PHPUnit (feature testing)
  - Postman (API testing)
  - Composer (package management)
- Full-text Search
  - Meilisearch
  - Laravel Scout
- Database
  - PostgreSQL
- Containerization
  - Docker
  - Docker Compose
  - Laravel Sail
- Hosting and Cloud Storage
  - Linode
  - AWS S3
- DevOps
  - GitHub
  - GitHub Projects
  - Discord 

### Prerequisites
- [Docker Desktop](https://www.docker.com/)
- [PHP](https://www.php.net/)
- [Node](https://nodejs.org/en)
