

## Discounts microservice

This is a Laravel based microservice that calculates the discounts to apply to a given order 

## Instalation

1. Clone the repository.

2. Inside the project directory run `composer install`. If composer is not installed you can get information about how to install it [here](https://getcomposer.org/download/).

3. Run `npm install`.

4. Set permissions

`chgrp -R _www project-directory`

`chmod -R 755 project-directory`

`chmod -R 775 project-directory/storage`

`chmod -R 775 project-directory/bootstrap`

5. Add a `.env` file to the root of the file with the DB credentials (a `.env.example` file is provided as model)

6. Generate the app key with the command `php artisan key:generate`. This key will be automatically added to your `.env`.

## Running the application

Run the migrations with `php artisan migrate`. This will create the database structure.

Seed the database with the initial data by running `php artisan db:seed`. 

You can perform the two previous actions at once by running `php artisan migrate --seed`.

Either create a `virtualhosts` in your apache config or use the command `php artisan serve`.

The app is up and running.

## Testing

The app's main functionalities (that being the calculating the discounts to given orders) via phpunit.

To use a test environment that will not interfere with the app's main configurations, create a `.env.testing` file where define the app's db for tests. If you fail to provide a testing DB, the app will try to run the tests using your main DB which can cause if the DB is already seeded.

With all set, just run `vendor/bin/phpunit`.

If you prefer a more visual test interface, at the root url of the app, there's a simple webpage that also allows to test the api with the previous defined orders or even customize new orders.

## How it works

This discounts endpoint of the app expects and `order` in json format with the following structure:

```
{
  "id": "1",
  "customer-id": "1",
  "items": [
    {
      "product-id": "B102",
      "quantity": "10",
      "unit-price": "4.99",
      "total": "49.90"
    }
  ],
  "total": "49.90"
}
```

After receiving the order, the app will proceed to load all the active discounts and pass the order through them. If any discount applies to that order the value of the discount will be appended to the order, and also the description of the discount. Discounts can accumulate and have different targets. All the discounts are configurable and the are represented by entries in the database. Here's the structure of a discount:

- `name` - Name of the discount
- `description` - Description of the discount
- `value_in_percent` - Value in percentage that will be used to calculate the discount value
- `type` - This is the type of discount and it will determine how the discount is applied to an order. There are 3 implemented types of discount, `customer_revenue`, that will apply the discount based on the customer total revenue, `product_type`, that will apply the discount based on the product category and `total_value`, that will apply the discount based on the order total value.
- `trigger_value_in_cents` - value that will trigger the discount. This value is in cents so it can avoid errors due to floats inaccuracies. If the discount type if of  `product_type`, this value will be converted to an absolute value.
- `threshold` - defines how the trigger value should evaluate. Possible values are `>`, `<`, `>=`, `<=`, `==`.
- `target` - represents the target of the discount within a given order. This can be `total` or `item`. For `item` futher options can be specified with `|`. In the this case only `item|min` is implemented. This option will target the cheapest item.
- `product_category_id` - specifies the category of products to which a discount should apply. If not a product discount, should be null.
- `product_id` - specifies a specific product to apply the discount.
- `priority` - defines the priority of the discount.
- `repeat` - specifies if the discount logic should be repeated. Useful in 6 for the price of 5 type discounts.
- `cumulative` - specifies if more discounts can apply in top of the current.
- `active` - defines the discount state

After applying the discounts the processed order will be returned in `json` format.




