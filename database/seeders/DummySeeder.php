<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{User,Cars,Rides,Bookings,Reviews,Payments,Messages};
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert dummy users with specified IDs
        User::updateOrCreate(['user_id' => 1], [
            'first_name' => 'Admin',
            'last_name' => 'user',
            'email' => 'admin@yopmail.com',
            'password' => Hash::make('Admin@123'),
            'role_id' => '2'
        ]);
        
        // Insert dummy users with specified IDs
        User::updateOrCreate(['user_id' => 2], [
            'email' => 'driver@yopmail.com',
            'email_verified_at' => 	now(),
            'password' => Hash::make('User@123'),
            'role_id' => '1',
            'country_code' => '91',
            'phone_number' => '9867545575',
            'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'join_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'first_name' => 'Driver',
            'last_name' => 'user',
            'dob' => '2000-01-01',
            'id_card'   => 'https://dummyimage.com/600x400/000/fff&text=License',
        ]);

        // Insert dummy users with specified IDs
        User::updateOrCreate(['user_id' => 3], [
            'email' => 'user@yopmail.com',
            'email_verified_at' => 	now(),
            'password' => Hash::make('User@123'),
            'role_id' => '1',
            'country_code' => '91',
            'phone_number' => '7867545574',
            'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'join_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'dob' => '2003-11-01'
        ]);
       
        // Insert dummy users with specified IDs
        Cars::updateOrCreate(['car_id' => 1], [

            'user_id' => 2, // Reference to User factory
            'make' => 'Audi',
            'model' => '500',
            'year' => '2000',
            'license_plate' => 'tdt1082',
            'color' => 'black',
            'seats' => '4',
        ]);    

        // Insert dummy users with specified IDs
        Cars::updateOrCreate(['car_id' => 2], [

            'user_id' => 2, // Reference to User factory
            'make' => 'Toyota',
            'model' => '500',
            'year' => '2000',
            'license_plate' => 'tdt10827',
            'color' => 'black',
            'seats' => '4',
        ]);

        // Insert dummy users with specified IDs
        Cars::updateOrCreate(['car_id' => 3], [

            'user_id' => 2, // Reference to User factory
            'make' => 'BMW',
            'model' => '500',
            'year' => '2000',
            'license_plate' => 'tdt10824',
            'color' => 'black',
            'seats' => '4',
        ]);

        // Insert dummy users with specified IDs
        Cars::updateOrCreate(['car_id' => 4], [

            'user_id' => 2, // Reference to User factory
            'make' => 'Honda',
            'model' => 'Accent',
            'year' => '1995',
            'license_plate' => 'tdt10823',
            'color' => 'green',
            'seats' => '2',
        ]);

        // Insert dummy users with specified IDs
        Cars::updateOrCreate(['car_id' => 5], [

            'user_id' => 2, // Reference to User factory
            'make' => 'Audi',
            'model' => '500',
            'year' => '2000',
            'license_plate' => 'tdt1082',
            'color' => 'black',
            'seats' => '4',
        ]);


        // Insert dummy users with specified IDs
        Cars::updateOrCreate(['car_id' => 6], [

            'user_id' => 2, // Reference to User factory
            'make' => 'Audi',
            'model' => '400',
            'year' => '2000',
            'license_plate' => 'tdt1082',
            'color' => 'black',
            'seats' => '4',
        ]);

        Rides::updateOrCreate(['ride_id' => 1], [
            'driver_id' => 2, // Assuming you have 10 users in your database
            'car_id' => 1, // Assuming you have 10 cars in your database
            'departure_city' => 'Chicago',
            'arrival_city' => 'San Francisco',
            'departure_time' => '2024-07-08 12:01:10',
            'arrival_time' => '2024-07-08 12:01:10',
            'price_per_seat' => '100',
            'available_seats' => '4',
            'luggage_size' => 'small',
            'smoking_allowed' => '1',
            'pets_allowed' => '1',
            'seat_booked' => '4',
            'music_preference' => 'Classical',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        ]);


        Rides::updateOrCreate(['ride_id' => 2], [
            'driver_id' => 2, // Assuming you have 10 users in your database
            'car_id' => 1, // Assuming you have 10 cars in your database
            'departure_city' => 'New York',
            'arrival_city' => 'Austin',
            'departure_time' => '2024-07-09 12:01:10',
            'arrival_time' => '2024-07-10 12:01:10',
            'price_per_seat' => '200',
            'available_seats' => '2',
            'luggage_size' => 'small',
            'smoking_allowed' => '1',
            'pets_allowed' => '1',
            'music_preference' => 'Classical',
            'seat_booked' => '2',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        ]);

        Rides::updateOrCreate(['ride_id' => 3], [
            'driver_id' => 2, // Assuming you have 10 users in your database
            'car_id' => 1, // Assuming you have 10 cars in your database
            'departure_city' => 'Napa',
            'arrival_city' => 'Needles',
            'departure_time' => '2024-07-09 12:01:10',
            'arrival_time' => '2024-07-10 12:01:10',
            'price_per_seat' => '200',
            'available_seats' => '4',
            'luggage_size' => 'small',
            'smoking_allowed' => '1',
            'pets_allowed' => '1',
            'music_preference' => 'Rock',
            'seat_booked' => '4',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        ]);

        Bookings::updateOrCreate(['booking_id' => 1], [
            'ride_id' => 1, // Assuming you have 10 rides in your database
            'passenger_id' => 3, // Assuming you have 10 users in your database
            'booking_date' => '2024-07-11 12:01:10',
            'status' => 'Confirmed',
            'seat_count' => 4,
        ]);

        Bookings::updateOrCreate(['booking_id' => 2], [
            'ride_id' => 2, // Assuming you have 10 rides in your database
            'passenger_id' => 3, // Assuming you have 10 users in your database
            'booking_date' => '2024-07-11 12:01:10',
            'status' => 'Confirmed',
            'seat_count' => 2,
        ]);

        Reviews::updateOrCreate(['review_id' => 1], [
        'ride_id' => 1, // Assumes RideFactory exists
        'reviewer_id' => 3, // Assumes UserFactory exists
        'rating' => 5, // Ratings between 1 and 5
        'comment' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        'review_date' => now(),
        ]);

        Reviews::updateOrCreate(['review_id' => 2], [
        'ride_id' => 2, // Assumes RideFactory exists
        'reviewer_id' => 3, // Assumes UserFactory exists
        'rating' => 4, // Ratings between 1 and 5
        'comment' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        'review_date' => now(),
        ]);

        Reviews::updateOrCreate(['review_id' => 3], [
        'ride_id' => 2, // Assumes RideFactory exists
        'reviewer_id' => 3, // Assumes UserFactory exists
        'rating' => 5, // Ratings between 1 and 5
        'comment' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        'review_date' => now(),
        ]);

        Payments::updateOrCreate(['payment_id' => 1], [
            'booking_id' => 1,
            'amount' => 400, // Generate a random amount between 10 and 1000
            'payment_date' => now(),
            'payment_method' => 'stripe',
            'status' => 'pending',
        ]);

        Payments::updateOrCreate(['payment_id' => 2], [
            'booking_id' => 2,
            'amount' => 100, // Generate a random amount between 10 and 1000
            'payment_date' => now(),
            'payment_method' => 'stripe',
            'status' => 'pending',
        ]);

        Messages::updateOrCreate(['message_id' => 1], [
            'ride_id' => 1,
            'sender_id' => 3,
            'receiver_id' => 2,
            'content' => 'Hello',
            'timestamp' => now(),
        ]);

        Messages::updateOrCreate(['message_id' => 2], [
            'ride_id' => 1,
            'sender_id' => 2,
            'receiver_id' => 3,
            'content' => 'Hi, How May I help you?',
            'timestamp' => now(),
        ]);


        Messages::updateOrCreate(['message_id' => 1], [
            'ride_id' => 2,
            'sender_id' => 3,
            'receiver_id' => 2,
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'timestamp' => now(),
        ]);

        Messages::updateOrCreate(['message_id' => 2], [
            'ride_id' => 2,
            'sender_id' => 2,
            'receiver_id' => 3,
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'timestamp' => now(),
        ]);


    }
}
