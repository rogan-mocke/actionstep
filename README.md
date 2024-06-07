# Actionstep API Tool - PHP

## Overview

This project provides a basic application to interact with the [Actionstep API](https://docs.actionstep.com/).

## Features

- **OAuth2 Authentication & Authorization**: including token refreshing.
- **Matter Documents**: Upload, download & delete documents.
- **GET & POST Requests**: Perform requests against a few popular endpoints.
- **REST Hooks**: Create & delete REST Hooks.

## Requirements

- LAMP Stack
- PHP 7.4 or later

## Installation

1. **Setup LAMP Stack**:
    - Install Apache, MySQL, and PHP on your local machine. You can use packages like XAMPP or WAMP for easy setup.
    - Make sure Apache and MySQL services are running.

<!-- -->
2. **Clone the repository**:
    ```bash
    git clone https://github.com/rogan-mocke/actionstep_api.git
    ```
3. **Create database tables**:
    ```bash
    actionstep_api/schema/tables.sql
    ```
4. **Configure database connection**:

   Edit the `DB.php` file located at `actionstep_api/backend/DB.php`:

    ```php
    $db_config = [
        'host'  => 'localhost',
        'user'  => 'root',
        'pass'  => '',
        'db'    => 'actionstep'
    ];
    ```

5. **Configure API credentials and local file storage**:

   Edit the `ActionStep.php` file located at `actionstep_api/backend/ActionStep.php`:

    ```php
    $this->_defaults = [
        'auth_uri'       => 'https://go.actionstep.com/api/oauth/authorize?',
        'token_uri'      => 'https://api.actionstep.com/api/oauth/token',
        'client_id'      => '',
        'client_secret'  => '',
        'response_type'  => 'code',
        'scope'          => 'all',
        'redirect_uri'   => 'http://localhost/actionstep_api/backend/pages/connect.php',
        'document_path'  => 'C:/Users/../'
    ];
    ```


## Usage

Navigate to `http://localhost/actionstep_api/index.php` in your preferred browser after installation.

## Disclaimer

This project is not an official Actionstep product and is not endorsed by Actionstep. It is intended for educational purposes only.
