# Restaurant Onboarding System Guide

## Overview

Fastify is a comprehensive QR code menu service that allows restaurant owners to create digital menus and generate QR codes for their tables. Customers can scan QR codes to view menus, place orders, and pay seamlessly.

## Features

### 🏪 Restaurant Management
- **Multi-tenant System**: Each restaurant has its own dedicated URL and dashboard
- **Restaurant Onboarding**: Beautiful onboarding process for new restaurant owners
- **Branding Customization**: Custom colors, logos, and banner images
- **QR Code Generation**: Generate unique QR codes for each table

### 📱 Customer Experience
- **Digital Menus**: Beautiful, mobile-friendly menu display
- **Order Placement**: Customers can place orders directly from their phones
- **Dine-in & Delivery**: Support for both restaurant and delivery orders
- **Real-time Updates**: Live order tracking and status updates

### 🛠️ Management Tools
- **Restaurant Dashboard**: Comprehensive dashboard for restaurant owners
- **Order Management**: Track and manage incoming orders
- **Menu Management**: Add, edit, and organize menu items
- **Analytics**: View order statistics and customer insights

## System Architecture

### Database Structure

#### Restaurants Table
```sql
- id (Primary Key)
- name (Restaurant name)
- slug (Unique URL identifier)
- description (Restaurant description)
- logo (Restaurant logo path)
- banner_image (Banner image path)
- whatsapp_number (Contact number)
- phone_number (Additional phone)
- email (Contact email)
- address, city, state, postal_code, country (Location)
- currency (Currency symbol)
- business_hours (JSON - Operating hours)
- is_active (Boolean - Restaurant status)
- is_verified (Boolean - Admin verification)
- theme_color (Primary brand color)
- secondary_color (Secondary brand color)
- settings (JSON - Custom settings)
```

#### Users Table (Extended)
```sql
- restaurant_id (Foreign Key to restaurants)
- is_admin (Boolean - Admin privileges)
```

### URL Structure

- **Restaurant Menu**: `/menu/{restaurant-slug}`
- **Restaurant Dashboard**: `/restaurant/{restaurant-slug}/dashboard`
- **QR Code Access**: `/qr/{qr-code}`

## Setup Instructions

### 1. Database Setup

Run the migrations to create the necessary tables:

```bash
php artisan migrate
```

### 2. File Storage

Ensure your storage is properly configured for file uploads:

```bash
php artisan storage:link
```

### 3. Admin User Creation

Create an admin user to manage the system:

```php
// In tinker or a seeder
User::create([
    'name' => 'Admin User',
    'email' => 'admin@abujaeat.com',
    'password' => Hash::make('password'),
    'is_admin' => true,
]);
```

## Restaurant Onboarding Process

### Step 1: Restaurant Registration

1. Restaurant owner visits `/restaurant/onboarding`
2. Fills out restaurant information form
3. Uploads logo and banner images
4. Sets custom branding colors
5. System generates unique slug for restaurant

### Step 2: Menu Setup

1. Restaurant owner accesses dashboard at `/restaurant/{slug}/dashboard`
2. Adds menu categories and items
3. Uploads food images
4. Sets prices and descriptions

### Step 3: QR Code Generation

1. Restaurant owner goes to QR Codes section
2. Generates QR codes for each table
3. Prints and displays QR codes on tables

### Step 4: Customer Experience

1. Customer scans QR code
2. Views restaurant-specific digital menu
3. Places order through mobile interface
4. Restaurant receives order notification

## API Endpoints

### Restaurant Management

```php
// Restaurant Onboarding
GET  /restaurant/onboarding
POST /restaurant/store

// Restaurant Dashboard
GET  /restaurant/{slug}/dashboard
GET  /restaurant/{slug}/edit
PUT  /restaurant/{slug}/update
GET  /restaurant/{slug}/qr-codes
POST /restaurant/{slug}/generate-qr
```

### Menu System

```php
// Menu Display
GET  /menu                    // Default menu
GET  /menu/{slug}            // Restaurant-specific menu
GET  /menu/search            // Search menu items
GET  /menu/category/{id}     // Category-specific items
```

### Order Management

```php
// Order Processing
GET  /cart                    // View cart
POST /cart/add               // Add to cart
GET  /checkout               // Checkout page
POST /orders                 // Place order
```

## Customization Options

### Restaurant Branding

Each restaurant can customize:
- **Colors**: Primary and secondary brand colors
- **Logo**: Restaurant logo displayed on menu
- **Banner**: Hero image for menu page
- **Currency**: Local currency symbol

### Menu Features

- **Categories**: Organize items by category
- **Images**: High-quality food photos
- **Descriptions**: Detailed item descriptions
- **Pricing**: Flexible pricing options
- **Availability**: Mark items as available/unavailable

### Order Settings

- **Delivery**: Enable/disable delivery orders
- **Dine-in**: Enable/disable restaurant orders
- **Payment Methods**: Configure accepted payment methods
- **Minimum Orders**: Set minimum order amounts
- **Delivery Fees**: Configure delivery charges

## Security Features

### Access Control

- **Restaurant Owners**: Can only access their own restaurant dashboard
- **Admin Users**: Can access all restaurants and system settings
- **Customers**: Can only view public menus and place orders

### Data Protection

- **Input Validation**: All form inputs are validated
- **File Upload Security**: Image uploads are secured
- **SQL Injection Protection**: Laravel's built-in protection
- **CSRF Protection**: Cross-site request forgery protection

## Deployment Considerations

### Environment Variables

```env
APP_NAME="Fastify"
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abujaeat
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### File Storage

Configure your file storage for production:

```env
FILESYSTEM_DISK=public
```

### SSL Certificate

Ensure HTTPS is enabled for secure QR code scanning and payments.

## Monitoring & Analytics

### Restaurant Dashboard Metrics

- **Total Orders**: Number of orders received
- **Pending Orders**: Orders awaiting processing
- **Menu Items**: Number of active menu items
- **Categories**: Number of menu categories

### Customer Analytics

- **Popular Items**: Most ordered menu items
- **Peak Hours**: Busiest ordering times
- **Order Values**: Average order amounts
- **Customer Retention**: Repeat customer metrics

## Support & Maintenance

### Regular Tasks

1. **Database Backups**: Daily automated backups
2. **Log Monitoring**: Monitor application logs
3. **Performance Monitoring**: Track system performance
4. **Security Updates**: Regular security patches

### Troubleshooting

#### Common Issues

1. **QR Codes Not Working**
   - Check URL generation
   - Verify restaurant is active
   - Ensure proper routing

2. **Image Upload Failures**
   - Check storage permissions
   - Verify file size limits
   - Ensure proper file types

3. **Order Notifications**
   - Check WhatsApp integration
   - Verify phone number format
   - Test notification system

## Future Enhancements

### Planned Features

- **Multi-language Support**: Multiple language menus
- **Advanced Analytics**: Detailed business insights
- **Inventory Management**: Stock tracking system
- **Loyalty Program**: Customer rewards system
- **Mobile App**: Native mobile applications
- **Payment Integration**: Direct payment processing
- **Kitchen Display**: Real-time order display
- **Customer Reviews**: Rating and review system

### Technical Improvements

- **API Rate Limiting**: Prevent abuse
- **Caching**: Improve performance
- **CDN Integration**: Faster image loading
- **Real-time Updates**: WebSocket integration
- **Progressive Web App**: Enhanced mobile experience

## Conclusion

The Fastify restaurant onboarding system provides a comprehensive solution for restaurants to digitize their menus and streamline their operations. With its multi-tenant architecture, customizable branding, and user-friendly interface, it offers everything needed to modernize restaurant ordering and improve customer experience.

For technical support or feature requests, please contact the development team. 