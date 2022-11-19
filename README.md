# Paytop_Test

## Available Commands

```Makefile
make regenerate # Generate/Regenerate dev database and fixtures

make regenerate-test # Generate/Regenerate test database and fixtures

make run # Run dev server with a messenger async worker

make stop # Stop dev server and messenger workers

make qa # Execute phpstan (level 9) then php-cs-fixer (PSR12 + Symfony)

make phpstan: # Execute phpstan (level 9)

make fix # Execute php-cs-fixer (PSR12 + Symfony)
```

## Demo accounts (loaded by fixtures)

- admin@demo.com - demo
- partner1@demo.com - demo
- partner2@demo.com - demo


## Endpoints

GET /api/clients 
  
GET /api/clients/{id}
  
POST /api/clients
```
{
  "firstName": "John",
  "lastName": "Doe",
  "email": "user@example.com",
  "phoneNumber": ""
}
```

POST /authentication_token
```
{
  "email": "admin@demo.com",
  "password": "demo"
}
```


