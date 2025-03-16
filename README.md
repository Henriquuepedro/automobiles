
# Automobiles System

This is a PHP-based system designed for managing automobiles. It provides features for managing car listings, customer interactions, and financial services, including integration with Mercado Pago for payment plans. The system is designed for both administrators and business owners, with automated daily updates for FIPE table (vehicle price data).

## Features

### 1. **Registration**
- **Automobiles**: Add and manage automobiles available for rent.
- **Characteristics**: Register various details about each automobile (e.g., model, brand, year).
- **Colors**: Specify available colors for each automobile.
- **Testimonials**: Add customer reviews and testimonials for automobiles.
- **Financial Status**: Track the financial status of each automobile (e.g., rental payments, outstanding amounts).
- **Automobile Options**: List additional options available for each vehicle (e.g., GPS, air conditioning).
- **Complementary Data**: Add any additional information about the automobile that might be relevant for customers.

### 2. **Configurations**
- **Home Page**: Set up the page that customers will first see when they visit the website.
- **Blog Page**: Create and manage a blog with posts about automobiles, services, or company updates.
- **Banner Images**: Add images for the banner on the home page to highlight promotions or featured cars.
- **Store History**: Provide a page with information about the company’s history, mission, and values.

### 3. **Customer Messages**
- **Customer Inquiries**: Receive and manage messages sent by customers through the website for inquiries about automobiles or services.

### 4. **Reports**
- **FIPE Table Report**: Generate reports based on the FIPE table (Fundação Instituto de Pesquisas Econômicas), which provides average prices for new and used vehicles. The system can automatically update the FIPE table on a daily basis through scheduled routines.

### 5. **Service Plans**
- **Subscription Plans**: Offer service plans for customers, utilizing the financial system of Mercado Pago for secure payments and plan management.

### 6. **Automobile Rental Service**
- **Vehicle Rental**: Manage automobile rentals, including booking, rental dates, and payment tracking.

### 7. **User Roles**
- **Administrator Role**: Allows for full access to the system, including managing automobiles, users, financial transactions, and more.
- **Owner Role**: The owner profile has the highest level of access, allowing full control over all companies, users, and system records.

### 8. **Email Notifications**
- **SMTP Email Integration**: Send automated emails to customers for booking confirmations, rental updates, and other notifications.

### 9. **Automated Daily Updates**
- **FIPE Table Updates**: The system automatically updates the FIPE table daily to ensure the most accurate pricing information for automobiles.

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```
   git clone https://github.com/Henriquuepedro/automobiles.git
   ```

2. Navigate to the project directory:
   ```
   cd automobiles
   ```

3. Install dependencies using Composer:
   ```
   composer install
   ```

4. Set up the environment variables. Copy the `.env.example` file to `.env` and configure the necessary details (e.g., database, API credentials, SMTP settings):
   ```
   cp .env.example .env
   ```

5. Generate the application key:
   ```
   php artisan key:generate
   ```

6. Run migrations to set up the database:
   ```
   php artisan migrate
   ```

7. Run the application:
   ```
   php artisan serve
   ```

The application should now be running at `http://localhost:8000`.

## Contributing

Feel free to fork this project and submit pull requests. If you encounter any bugs or have suggestions for improvements, please open an issue on GitHub.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
