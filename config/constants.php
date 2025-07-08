<?php

return [

    "ERROR" => [
        "AUTHORIZATION"     => "Opps! You do not have permission to access.",
        "ACCOUNT_ISSUE"     => "Opps! Your account is not verified.Please check your email.",
        "INVALID_CREDENTIAL"=> "Please provide valid credential",
        "NOT_FOUND"         => "not found!",
        "SOMETHING_WRONG"   => "Opps! Something went wrong.",
        
    ],
    "SUCCESS"   => [
        "UPDATE_DONE"       => "has been updated successfully.",
        "ADD_DONE"          => "has been added successfully.",
        "CHANGED_DONE"      => "has been changed successfully.",
        "DELETE_DONE"       => "has been deleted successfully.",
        "FETCH_DONE"        => "fetched successfully.",
        "VERIFY_SEND"       => "has been created successfully. Please check your email and verify email address",
        "VERIFY_DONE"       => "has been verified successfully.",
        "LOGIN"             => "Login successfully.",
        "SENT_DONE"         => "has been sent successfully.",
        "LOGOUT_DONE"       => "Logged out successfully.",
        "DONE"              => "has been done successfully.",
    ],
    "ROLES"     => [
        "ADMIN"     => "admin",
        "USER"      => "user",
    ],
    "APP_NAME"          => "CARPOOL",

    "COMPANYNAME"       => env('APP_NAME','CARPOOL'),
    "encryptionMethod"  => env('ENC_DEC_METHOD',''),
    "secrect"           => env('ENC_DEC_SECRET',''),
    "STRIPE_KEY"        => env('STRIPE_KEY',''),
    "STRIPE_SECRET"     => env('STRIPE_SECRET',''),
];
