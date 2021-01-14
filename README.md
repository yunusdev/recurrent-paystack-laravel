**To get the project up and running, Pls Do the following After Cloning:**

- RUN `composer install`

- RUN `cp .env.example .env` _(Then setup ur .env file)_ 
or duplicate the `.env.example` file and name the duplicated one `.env`

    - Set up DB connection in .env
    - Provide your paystack public and secret key in the env (PAYSTACK_PUBLIC_KEY, PAYSTACK_SECRET_KEY)

- RUN `php artisan key:generate`

- RUN `php artisan migrate`

- RUN `php artisan serve` _(To start the application)_

